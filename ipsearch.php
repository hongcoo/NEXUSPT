<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn();

if (get_user_class() < $userprofile_class)
	permissiondenied();


	$ip = trim($_GET['ip']);
	if ($ip)
	{	$ip=str_replace(array('[',']'),"",$ip);
		$regex = "/[^\[\]0-9a-fA-F\:\.]/i";
		if (preg_match($regex, $ip)||strlen($ip)<7)
		{stderr($lang_ipsearch['std_error'], $lang_ipsearch['std_invalid_ip']);	}
	}


		$where1 = "u.ip like '$ip%'";
		$where2 = "iplog.ip like '$ip%'";


	stdhead($lang_ipsearch['head_search_ip_history']);
	begin_main_frame();

	print("<h1 align=\"center\">".$lang_ipsearch['text_search_ip_history']."</h1>\n");
	print("<form method=\"get\" action=\"".$_SERVER[PHP_SELF]."\">");
	print("<table align=center border=1 cellspacing=0 width=115 cellpadding=5>\n");
	tr($lang_ipsearch['row_ip']."<font color=red>*</font>", "<input type=\"text\" name=\"ip\" size=\"40\" value=\"".htmlspecialchars($ip)."\" />", 1);
	print("<tr><td align=\"right\" colspan=\"2\"><input type=\"submit\" value=\"".$lang_ipsearch['submit_search']."\"/></td></tr>");
	print("</table></form>\n");
	if ($ip)
	{

$queryc = "SELECT COUNT(*) FROM
(
SELECT u.id FROM users AS u WHERE $where1
UNION SELECT u.id FROM users AS u RIGHT JOIN iplog ON u.id = iplog.userid WHERE $where2
GROUP BY u.id
) AS ipsearch";

	$res = sql_query($queryc) or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_array($res);
	$count = $row[0];

	if ($count == 0)
	{
		print("<h1 align=\"center\">".$count.$lang_ipsearch['text_users_used_the_ip'].$ip." ( ".convertipv6($ip)." ) </h1>");
		end_main_frame();
		stdfoot();
		die;
	}

	$order = $_GET['order'];
	$page = 0 + $_GET["page"];
	$perpage = 20;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "$_SERVER[PHP_SELF]?ip=$ip&mask=$mask&order=$order&");

	if ($order == "added")
		$orderby = "added DESC";
	elseif ($order == "username")
		$orderby = "UPPER(username) ASC";
	elseif ($order == "email")
		$orderby = "email ASC";
	elseif ($order == "last_ip")
		$orderby = "last_ip ASC";
	elseif ($order == "last_access")
		$orderby = "last_ip ASC";
	else
		$orderby = "access DESC";

	$query = "SELECT * FROM (
SELECT u.id, u.username, u.ip AS ip, u.ip AS last_ip, u.last_access, u.last_access AS access, u.email, u.invited_by, u.added, u.class, u.uploaded, u.downloaded, u.donor, u.enabled, u.warned
FROM users AS u
WHERE $where1
UNION SELECT u.id, u.username, iplog.ip AS ip, u.ip as last_ip, u.last_access, max(iplog.access) AS access, u.email, u.invited_by, u.added, u.class, u.uploaded, u.downloaded, u.donor, u.enabled, u.warned
FROM users AS u
RIGHT JOIN iplog ON u.id = iplog.userid
WHERE $where2
GROUP BY u.id ) as ipsearch
GROUP BY id
ORDER BY $orderby
$limit";

	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

		print("<h1 align=\"center\">".$count.$lang_ipsearch['text_users_used_the_ip'].$ip." ( ".convertipv6($ip,false)." ) </h1>");

	print("<table width=940 border=1 cellspacing=0 cellpadding=5 align=center>\n");
	print("<tr><td class=colhead align=center><a class=colhead href=\"?ip=$ip&mask=$mask&order=username\">".$lang_ipsearch['col_username']."</a></td>".
"<td class=colhead align=center><a class=colhead href=\"?ip=$ip&mask=$mask&order=last_ip\">".$lang_ipsearch['col_last_ip']."</a></td>".
"<td class=colhead align=center>位置</td>".
"<td class=colhead align=center><a class=colhead href=\"?ip=$ip&mask=$mask&order=last_access\">".$lang_ipsearch['col_last_access']."</a></td>".
"<td class=colhead align=center>".$lang_ipsearch['col_ip_num']."</td>".
"<td class=colhead align=center><a class=colhead href=\"?ip=$ip&mask=$mask\">".$lang_ipsearch['col_last_access_on']."</a></td>".
"<td class=colhead align=center><a class=colhead href=\"?ip=$ip&mask=$mask&order=added\">".$lang_ipsearch['col_added']."</a></td>".
"<td class=colhead align=center>".$lang_ipsearch['col_invited_by']."</td>");

	while ($user = mysql_fetch_array($res))
	{
		if ($user['added'] == '0000-00-00 00:00:00')
			$added = $lang_ipsearch['text_not_available'];
		else $added = gettime($user['added']);
		if ($user['last_access'] == '0000-00-00 00:00:00')
			$lastaccess = $lang_ipsearch['text_not_available'];
		else $lastaccess = gettime($user['last_access']);

		if ($user['last_ip'])
			$ipstr = $user['last_ip'];
		else
			$ipstr = $lang_ipsearch['text_not_available'];
			
			list($loc_pub, $loc_mod) = get_ip_location($user['last_ip']);
			$addr =  $loc_pub;

		//$resip = sql_query("SELECT ip FROM iplog WHERE userid=" . sqlesc($user['id']) . " GROUP BY iplog.ip") or sqlerr(__FILE__, __LINE__);
//$iphistory = mysql_num_rows($resip);
$iphistory = get_row_count("iplog","WHERE userid=" . sqlesc($user['id']) );

		if ($user["invited_by"] > 0)
		{
			$invited_by = get_username($user['invited_by']);
		}
		else
			$invited_by = $lang_ipsearch['text_not_available'];

		echo "<tr><td align=\"center\">" .
get_username($user['id'])."</td>".
"<td align=\"center\">" . $ipstr . "</td>
<td align=\"center\">$addr</td>
<td align=\"center\">" . $lastaccess . "</td>
<td align=\"center\"><a href=\"iphistory.php?id=" . $user['id'] . "\">" . $iphistory. "</a></td>
<td align=\"center\">" . gettime($user['access']) . "</td>
<td align=\"center\">" . gettime($user['added']) . "</td>
<td align=\"center\">" . $invited_by . "</td>
</tr>\n";
	}
	echo "</table>";

	echo $pagerbottom;
	}
	end_main_frame();
	stdfoot();

?>
