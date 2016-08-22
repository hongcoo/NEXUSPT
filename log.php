<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
$thispagewidthscreen=false;
loggedinorreturn();
/*if (get_user_class() < $log_class)
{
stderr($lang_log['std_sorry'],$lang_log['std_permission_denied_only'].get_user_class_name($log_class,false,true,true).$lang_log['std_or_above_can_view'],false);
}
*/
function permissiondeny(){
	global $lang_log;
	stderr($lang_log['std_sorry'],$lang_log['std_permission_denied'],false);
}

function logmenu($selected = "showstats"){
		global $lang_log;
		//global $lang_index;
		global $showfunbox_main;
		begin_main_frame();
		print ("<div id=\"lognav\"><ul id=\"logmenu\" class=\"menu\">");
		
		print ("<li" . ($selected == "showstats" ? " class=selected" : "") . "><a href=\"?action=showstats\">".$lang_log['text_showstats']."</a></li>");
		print ("<li" . ($selected == "highcharts" ? " class=selected" : "") . "><a href=\"?action=highcharts\">&nbsp;站&nbsp;点&nbsp;日&nbsp;记&nbsp;</a></li>");
		print ("<li" . ($selected == "dailylog" ? " class=selected" : "") . "><a href=\"?action=dailylog\">".$lang_log['text_daily_log']."</a></li>");
		print ("<li" . ($selected == "chronicle" ? " class=selected" : "") . "><a href=\"?action=chronicle\">".$lang_log['text_chronicle']."</a></li>");
		if ($showfunbox_main == 'yes')print ("<li" . ($selected == "funbox" ? " class=selected" : "") . "><a href=\"?action=funbox\">".$lang_log['text_funbox']."</a></li>");
		print ("<li" . ($selected == "news" ? " class=selected" : "") . "><a href=\"?action=news\">".$lang_log['text_news']."</a></li>");
		print ("<li" . ($selected == "poll" ? " class=selected" : "") . "><a href=\"?action=poll\">".$lang_log['text_poll']."</a></li>");
		print ("</ul></div>");
		end_main_frame();
}

function searchtable($title, $action, $opts = array()){
		global $lang_log;
		print("<table border=1 cellspacing=0 width=940 cellpadding=5>\n");
		print("<tr><td class=colhead align=left>".$title."</td></tr>\n");
		print("<tr><td class=toolbox align=left><form method=\"get\" action='" . $_SERVER['PHP_SELF'] . "'>\n");
		print("<input type=\"text\" name=\"query\" style=\"width:500px\" value=\"".$_GET['query']."\">\n");
		if ($opts) {
			print($lang_log['text_in']."<select name=search>");
			foreach($opts as $value => $text)
				print("<option value='".$value."'". ($value == $_GET['search'] ? " selected" : "").">".$text."</option>");
			print("</select>");
			}
		print("<input type=\"hidden\" name=\"action\" value='".$action."'>&nbsp;&nbsp;");
		print("<input type=submit value=" . $lang_log['submit_search'] . "></form>\n");
		print("</td></tr></table><br />\n");
}

function additem($title, $action){
		global $lang_log;
		print("<table border=1 cellspacing=0 width=940 cellpadding=5>\n");
		print("<tr><td class=colhead align=left>".$title."</td></tr>\n");
		print("<tr><td class=toolbox align=left><form method=\"post\" action='" . $_SERVER['PHP_SELF'] . "'>\n");
		print("<textarea name=\"txt\" style=\"width:500px\" rows=\"3\" >".$row["txt"]."</textarea>\n");
		print("<input type=\"hidden\" name=\"action\" value=".$action.">");
		print("<input type=\"hidden\" name=\"do\" value=\"add\">");
		print("<input type=submit value=" . $lang_log['submit_add'] . "></form>\n");
		print("</td></tr></table><br />\n");
}

function edititem($title, $action, $id){
		global $lang_log;
		$result = sql_query ("SELECT * FROM ".$action." where id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
		if ($row = mysql_fetch_array($result)) {
		print("<table border=1 cellspacing=0 width=940 cellpadding=5>\n");
		print("<tr><td class=colhead align=left>".$title."</td></tr>\n");
		print("<tr><td class=toolbox align=left><form method=\"post\" action='" . $_SERVER['PHP_SELF'] . "'>\n");
		print("<textarea name=\"txt\" style=\"width:500px\" rows=\"3\" >".$row["txt"]."</textarea>\n");
		print("<input type=\"hidden\" name=\"action\" value=".$action.">");
		print("<input type=\"hidden\" name=\"do\" value=\"update\">");
		print("<input type=\"hidden\" name=\"id\" value=".$id.">");
		print("<input type=submit value=" . $lang_log['submit_okay'] . " style='height: 20px' /></form>\n");
		print("</td></tr></table><br />\n");
		}
}

$action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : (isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '');
$allowed_actions = array("dailylog","chronicle","funbox","news","poll","showstats","highcharts");
if (!$action)
	$action='showstats';
	
if (get_user_class() < $log_class&& $action!='showstats'&&$action!='highcharts')
{
stderr($lang_log['std_sorry'],$lang_log['std_permission_denied_only'].get_user_class_name($log_class,false,true,true).$lang_log['std_or_above_can_view'],false);
}



if (!in_array($action, $allowed_actions))
stderr($lang_log['std_error'], $lang_log['std_invalid_action']);
else {
	switch ($action){
	case "showstats":
stdhead($lang_log['head_site_log']);

logmenu("showstats");
// ------------- start: stats ------------------//
//if ($showstats_main == "yes")
{
?>
<h2> <?php echo $lang_index['text_tracker_statistics'] ?></h2>
<table width="100%"><tr><td class="text" align="center">
<table width="90%" class="main" border="1" cellspacing="0" cellpadding="10">
<?php
	$Cache->new_page('stats_users', 3560, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
	$registered = number_format(get_row_count("users"));
	$registerednow = get_row_count("users","WHERE added >= ".sqlesc(date("Y-m-d H:i:s",(TIMENOW - 86400))));
	$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
	$totalonlinetoday = number_format(get_row_count("users","WHERE last_access >= ". sqlesc(date("Y-m-d H:i:s",(TIMENOW - 86400)))));
	$totalonlineweek = number_format(get_row_count("users","WHERE last_access >= ". sqlesc(date("Y-m-d H:i:s",(TIMENOW - 604800)))));
	$totalonlinemonth = number_format(get_row_count("users","WHERE last_access >= ". sqlesc(date("Y-m-d H:i:s",(TIMENOW - 604800*4)))));
	$registered_male = number_format(get_row_count("users", "WHERE gender='Male'"));
	$registered_female = number_format(get_row_count("users", "WHERE gender='Female'"));
	$registered_female_male = number_format(get_row_count("users", "WHERE gender='N/A'"));
		
	$ipv6now = get_row_count("users","WHERE MODEMAX = 6 ");
	$ipv64now = get_row_count("users","WHERE MODEMAX = 5");
	$ipv4now = get_row_count("users","WHERE  MODEMAX = 4");

	$activewebusernow = get_row_count("users","WHERE last_access >= ".sqlesc(date("Y-m-d H:i:s",(TIMENOW - 900))));
	$activewebusernow=number_format($activewebusernow);

	
?>
<tr>
<?php
	twotd($lang_index['row_active_browsing_users']." / ".$lang_index['row_users_active_today'],$activewebusernow." / ".$totalonlinetoday);
	twotd("本周 / 本月 访问",$totalonlineweek." / ".$totalonlinemonth);
?>
</tr>
<tr>
<?php
	twotd($lang_index['row_registered_users'],$registered." / ".number_format($maxusers));
	twotd($lang_index['row_unconfirmed_users']." / 今日注册用户",$unverified." / ".$registerednow);
?>
</tr>
<tr>
<?php
	//twotd("IPV6 / IPV4",$ipv6now."/".$ipv4now);
	twotd("IPV6 / 6TO4 / IPV4 ",$ipv6now." / ".$ipv64now." / ".$ipv4now);
	twotd($lang_index['row_female_users']." / 秀吉 /".$lang_index['row_male_users'],$registered_female." / ".$registered_female_male." / ".$registered_male);
?>
</tr>

<?php
	$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();
?>
<tr><td colspan="4" class="rowhead">&nbsp;</td></tr>
<?php
	$Cache->new_page('stats_torrents',1800, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
	$torrents = number_format(get_row_count("torrents"));
	$torrentstoday = number_format(get_row_count('torrents', 'WHERE added >  '.sqlesc(date("Y-m-d")))); 
	$dead = number_format(get_row_count("torrents", "WHERE visible='no'"));
	$noseeddead = number_format(get_row_count("torrents", "WHERE visible='yes' and havenoseed = 'yes'"));
	$seeders = get_row_count("peers", "WHERE seeder='yes'");
	$leechers = get_row_count("peers", "WHERE seeder='no'");
	
	
	
	$ipv6da8peer= get_row_count("peers","WHERE ip LIKE '%2001:da8%' and ip  NOT LIKE '%5efe%' ");
	$ipv6250peer= get_row_count("peers","WHERE ip LIKE '%2001:250%' and ip  NOT LIKE '%5efe%' ");
	$IPV6to4peer= get_row_count("peers","WHERE  ip LIKE '%5efe%' ");
	$ipv6schoolpeer= get_row_count("peers","WHERE ip LIKE '%".$schoolipv6."%' and ip NOT LIKE '%5efe%' ");
	$ipv4peer = get_row_count("peers","WHERE ip  NOT LIKE '%2001%' ");
	

	
		$IPV6to4peer = number_format($IPV6to4peer);
		$ipv4peer = number_format($ipv4peer);
		$ipv6school = number_format($ipv6schoolpeer);
		$ipv6outschool = number_format($ipv6da8peer+$ipv6250peer-$ipv6schoolpeer);
		

		
		$ipv6peeruser = get_single_value("peers","COUNT(DISTINCT(userid)) ","WHERE (ip LIKE '%2001:da8%' or ip LIKE '%2001:250%') and ip  NOT LIKE '%5efe%' ");
		$ipv4peeruser = get_single_value("peers","COUNT(DISTINCT(userid))"," WHERE ip  NOT LIKE '%2001%'");
		$ipv6to4peeruser = get_single_value("peers","COUNT(DISTINCT(userid)) ","WHERE  ip LIKE '%5efe%'");
		$ipv6schoolpeeruser = get_single_value("peers","COUNT(DISTINCT(userid)) ","WHERE ip LIKE '%".$schoolipv6."%' and ip  NOT LIKE '%5efe%'");
		
		

		
		
		
		$ipv6outschoolpeeruser = number_format($ipv6peeruser-$ipv6schoolpeeruser);
		$ipv4peeruser = number_format($ipv4peeruser);
		$ipv6to4peeruser = number_format($ipv6to4peeruser);
		$ipv6schoolpeeruser = number_format($ipv6schoolpeeruser);
		

		
		
		
		
	
	
	if ($leechers == 0)
		$ratio = 0;
	else
		$ratio = round($seeders / $leechers * 100);
	$activewebusernow = get_row_count("users","WHERE last_access >= ".sqlesc(date("Y-m-d H:i:s",(TIMENOW - 10))));
	$activewebusernow=number_format($activewebusernow);
	$activetrackerusernow = number_format(get_single_value("peers","COUNT(DISTINCT(userid))"));
	
	
	$activetrackeruseruseridnum = get_single_value("peers","COUNT(DISTINCT(userid))");
	$activetrackeruseripnum = get_single_value("peers","COUNT(DISTINCT(ip))");
	

	
	$peers = number_format($seeders + $leechers);
	$seeders = number_format($seeders);
	$leechers = number_format($leechers);
	$totaltorrentssize = mksize(get_row_sum("torrents", "size",'',3600*24));
	$totaluploadednow = get_row_sum("peers","upthis");
	$totaldownloadednow = get_row_sum("peers","downthis");

	$totaluploadedtotal = get_row_sum("peers","uploaded","WHERE uploadoffset<uploaded")-get_row_sum("peers","uploadoffset","WHERE uploadoffset<uploaded");
	$totaldownloadedtotal = get_row_sum("peers","downloaded","WHERE downloadoffset<downloaded")-get_row_sum("peers","downloadoffset","WHERE downloadoffset<downloaded");
	
	$totaluploaded = get_row_sum("users","uploaded",'',3600*24);
	$totaldownloaded = get_row_sum("users","downloaded",'',3600*24);
	
	
	if (!$totalsumsnatched = $Cache->get_value('totalsumsnatched_content')){
		$res = sql_query("SELECT SUM(uploaded) as uploaded,SUM(downloaded) as downloaded,SUM(imdownloaded) as imdownloaded,SUM(imuploaded) as imuploaded FROM snatched;");
		$totalsumsnatched = mysql_fetch_array($res);
		$Cache->cache_value('totalsumsnatched_content', $totalsumsnatched, 3600*24);
	}

	
	$totalimuploaded = $totalsumsnatched['imuploaded'];
	$totaltrueuploaded = $totalsumsnatched['uploaded'];
	
	$totalimdownloaded = $totalsumsnatched['imdownloaded'];
	$totaltruedownloaded = $totalsumsnatched['downloaded'];
	
?>
<tr>
<?php
	twotd("今日 / 种子总数",$torrentstoday." / ".$torrents);
	twotd($lang_index['row_noseed_torrents']." / ".$lang_index['row_dead_torrents'],$noseeddead." / ".$dead);
?>
</tr>
<tr>
<?php
	twotd($lang_index['row_seeders'],$seeders);
	twotd($lang_index['row_leechers'],$leechers);
?>
</tr>
<tr>
<?php
	twotd($lang_index['row_peers'],$peers);
	//twotd($lang_index['row_seeder_leecher_ratio'],$ratio."%");
	twotd($lang_index['row_tracker_active_users'], $activetrackerusernow);
?>
</tr>
<tr>
<?php
	twotd($lang_index['row_total_size_of_torrents'],$totaltorrentssize);
	twotd(/*$lang_index['row_total_data']*/"总上传量(显) / 总下载量(显)",mksize($totaluploaded)." / ". mksize($totaldownloaded));
	
?>
</tr>
<tr>
<?php
twotd($lang_index['row_total_uploaded']."(虚/实)",mksize($totalimuploaded)." / ". mksize($totaltrueuploaded));
	twotd($lang_index['row_total_downloaded']."(虚/实)",mksize($totalimdownloaded)." / ". mksize($totaltruedownloaded));

?>
</tr>
<tr>
<?php
	twotd("活跃".$lang_index['row_total_uploaded']."(流通 / 累计)",mksize($totaluploadednow)." / ".mksize($totaluploadedtotal));
	twotd("活跃".$lang_index['row_total_downloaded']."(流通 / 累计)",mksize($totaldownloadednow)." / ".mksize($totaldownloadedtotal));

?>
</tr>
<tr>
<?php

	twotd("6TO4 / IPV4 同伴",$IPV6to4peer." / ".$ipv4peer);
	twotd("校外 / 校内 IPV6同伴",$ipv6outschool ." / ".$ipv6school);
?>
</tr>
<tr>
<?php

	twotd("6TO4 / IPV4 用户",$ipv6to4peeruser." / ".$ipv4peeruser);
	twotd("校外 / 校内 IPV6用户",$ipv6outschoolpeeruser ." / ".$ipv6schoolpeeruser);
?>
</tr>



<?php
	$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();
?>

<tr><td colspan="4" class="rowhead">&nbsp;</td></tr>

	
<?php
	$Cache->new_page('stats_classes',3630, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
	$peasants =  number_format(get_row_count("users", "WHERE class=".UC_PEASANT));
	$users = number_format(get_row_count("users", "WHERE class=".UC_USER));
	$usershr = number_format(get_row_count("users", "WHERE hrwarned >= 1"));
	$powerusers = number_format(get_row_count("users", "WHERE class=".UC_POWER_USER));
	$eliteusers = number_format(get_row_count("users", "WHERE class=".UC_ELITE_USER));
	$crazyusers = number_format(get_row_count("users", "WHERE class=".UC_CRAZY_USER));
	$insaneusers = number_format(get_row_count("users", "WHERE class=".UC_INSANE_USER));
	$veteranusers = number_format(get_row_count("users", "WHERE class=".UC_VETERAN_USER));
	$extremeusers = number_format(get_row_count("users", "WHERE class=".UC_EXTREME_USER));
	$ultimateusers = number_format(get_row_count("users", "WHERE class=".UC_ULTIMATE_USER));
	$nexusmasters = number_format(get_row_count("users", "WHERE class=".UC_NEXUS_MASTER));
		$VIP = number_format(get_row_count("users", "WHERE class=".UC_VIP));
	$donated = number_format(get_row_count("users", "WHERE donor = 'yes'"));
	$warned = number_format(get_row_count("users", "WHERE warned='yes'"));
	$disabled = number_format(get_row_count("users", "WHERE enabled='no'"));
?>
<tr>
<?php
	twotd(get_user_class_name(UC_PEASANT,false,false,true)." <img class=\"leechwarned\" src=\"pic/trans.gif\" alt=\"leechwarned\" />",$peasants);
	twotd(get_user_class_name(UC_USER,false,false,true)." / HR <img class=\"hrwarned\" src=\"pic/trans.gif\" alt=\"Hrwarned\" />",$users." / ".$usershr);
?>
</tr>
<tr>
<?php
	twotd(get_user_class_name(UC_POWER_USER,false,false,true),$powerusers);
	twotd(get_user_class_name(UC_ELITE_USER,false,false,true),$eliteusers);
?>
</tr>
<tr>
<?php
	twotd(get_user_class_name(UC_CRAZY_USER,false,false,true),$crazyusers);
	twotd(get_user_class_name(UC_INSANE_USER,false,false,true),$insaneusers);
?>
</tr>
<tr>
<?php
	twotd(get_user_class_name(UC_VETERAN_USER,false,false,true),$veteranusers);
	twotd(get_user_class_name(UC_EXTREME_USER,false,false,true),$extremeusers);
?>
</tr>
<tr>
<?php
	twotd(get_user_class_name(UC_ULTIMATE_USER,false,false,true),$ultimateusers);
	twotd(get_user_class_name(UC_NEXUS_MASTER,false,false,true),$nexusmasters);
?>
</tr>

<tr>
<?php
	twotd(get_user_class_name(UC_VIP,false,false,true),$VIP);
	twotd($lang_index['row_donors']." <img class=\"star\" src=\"pic/trans.gif\" alt=\"Donor\" />",$donated);
?>
</tr>
<tr>
<?php
	twotd($lang_index['row_warned_users']." <img class=\"warned\" src=\"pic/trans.gif\" alt=\"warned\" />",$warned);
	twotd($lang_index['row_banned_users']." <img class=\"disabled\" src=\"pic/trans.gif\" alt=\"disabled\" />",$disabled);
?>
</tr>
<?php
	$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();
?>
</table>
</td></tr></table>
<?php
}

	$Cache->new_page('Category_Activity',3230, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
	
	
$res = sql_query("SELECT COUNT(*),sum(size) FROM torrents") or sqlerr(__FILE__, __LINE__);
$n = mysql_fetch_row($res);
$n_tor = $n[0];
$n_tor_size = $n[1];

$res = sql_query("SELECT COUNT(*) FROM peers") or sqlerr(__FILE__, __LINE__);
$n = mysql_fetch_row($res);
$n_peers = $n[0];



if ($n_tor == 0)
	stdmsg("Sorry...", "No categories defined!");
else
{

		$orderby = "c.id";

  $res = sql_query("SELECT c.name, MAX(t.added) AS last, COUNT(DISTINCT t.id) AS n_t, COUNT(p.id) AS n_p,sum(t.size) AS c_s
	FROM categories as c LEFT JOIN torrents as t ON t.category = c.id LEFT JOIN peers as p
	ON t.id = p.torrent GROUP BY c.id ORDER BY c.id") or sqlerr(__FILE__, __LINE__);

	begin_frame("Category Activity", True);
	begin_table(false,5,'categoryactivity');
	print("<thead><tr><td class=colhead>Category</td>
	<td class=colhead>Last Upload</td>
	<td class=colhead>Torrents</td>
	<td class=colhead>Perc.</td>
	<td class=colhead>Peers</td>
	<td class=colhead>Perc.</td>
	<td class=colhead>Size</td>
	<td class=colhead>Perc.</td></tr></thead><tbody>\n");
	while ($cat = mysql_fetch_array($res))
	{
		print("<tr><td class=rowhead>" . $cat['name'] . "</b></a></td>");
		print("<td " . ($cat['last']?(">".$cat['last']." (".get_elapsed_time(strtotime($cat['last']))." ago)"):"align = center>---") ."</td>");
		print("<td align=right>" . $cat['n_t'] . "</td>");
		print("<td align=right>" . number_format(100 * $cat['n_t']/$n_tor,1) . "%</td>");
		print("<td align=right>" . $cat['n_p'] . "</td>");
		print("<td align=right>" . ($n_peers > 0?number_format(100 * $cat['n_p']/$n_peers,1)."%":"---") . "</td>");
		print("<td align=right>" . mksize($cat['c_s']) . "</td>");
		print("<td align=right>" . number_format(100 * $cat['c_s']/$n_tor_size,1) . "%</td></tr>\n");
	}
	print("</tbody>");
	end_table();
	
print('<script type="text/javascript">$(function(){$("table#categoryactivity").tablesorter();})</script>');
	
	end_frame();
	
}
	$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();

// ------------- end: stats ------------------//
		//print($lang_log['time_zone_note']);
		stdfoot();
		die;

		break;
	
	
	
	
	
	
	
	
	
	
	
	
	case "dailylog":
		stdhead($lang_log['head_site_log']);


		$query = mysql_real_escape_string(trim($_GET["query"]));
		$search = $_GET["search"];

		$addparam = "";
		$wherea = "";
		if (get_user_class() >= $confilog_class){
			switch ($search)
			{
				case "mod": $wherea=" WHERE security_level = 'mod'"; break;
				case "normal": $wherea=" WHERE security_level = 'normal'"; break;
				case "all": break;
			}
			$addparam = ($wherea ? "search=".rawurlencode($search)."&" : "");
		}
		else{
			$wherea=" WHERE security_level = 'normal'";
		}

		if($query){
				$wherea .= ($wherea ? " AND " : " WHERE ")." txt LIKE '%$query%' ";
				$addparam .= "query=".rawurlencode($query)."&";
		}

		logmenu('dailylog');
				
		$opt = array (all => $lang_log['text_all'], normal => $lang_log['text_normal'], mod => $lang_log['text_mod']);
		searchtable($lang_log['text_search_log'], 'dailylog',$opt);

		$res = sql_query("SELECT COUNT(*) FROM sitelog".$wherea);
		$row = mysql_fetch_array($res);
		$count = $row[0];

		$perpage = 50;

		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "log.php?action=dailylog&".$addparam);

		$res = sql_query("SELECT added, txt FROM sitelog $wherea ORDER BY added DESC $limit") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) == 0)
		print($lang_log['text_log_empty']);
		else
		{

		//echo $pagertop;

			print("<table width=940 border=1 cellspacing=0 cellpadding=5>\n");
			print("<tr><td class=colhead align=center><img class=\"time\" src=\"pic/trans.gif\" alt=\"time\" title=\"".$lang_log['title_time_added']."\" /></td><td class=colhead align=left>".$lang_log['col_event']."</td></tr>\n");
			while ($arr = mysql_fetch_assoc($res))
			{
				$color = "";
				if (strpos($arr['txt'],'was uploaded by')) $color = "green";
				if (strpos($arr['txt'],'was deleted by')) $color = "red";
				if (strpos($arr['txt'],'was added to the Request section')) $color = "purple";
				if (strpos($arr['txt'],'was edited by')) $color = "blue";
				if (strpos($arr['txt'],'settings updated by')) $color = "darkred";
				print("<tr><td class=\"rowfollow nowrap\" align=center>".gettime($arr['added'],true,false)."</td><td class=rowfollow align=left><font color='".$color."'>".htmlspecialchars($arr['txt'])."</font></td></tr>\n");
			}
			print("</table>");
	
			echo $pagerbottom;
		}

		print($lang_log['time_zone_note']);

		stdfoot();
		die;
		break;
	case "chronicle":
		stdhead($lang_log['head_chronicle']);
		$query = mysql_real_escape_string(trim($_GET["query"]));
		if($query){
		$wherea=" WHERE txt LIKE '%$query%' ";
		$addparam = "query=".rawurlencode($query)."&";
		}
		else{
		$wherea="";
		$addparam = "";
		}
		logmenu("chronicle");
		searchtable($lang_log['text_search_chronicle'], 'chronicle');
		if (get_user_class() >= $chrmanage_class)
			additem($lang_log['text_add_chronicle'], 'chronicle');
		if ($_GET['do'] == "del" || $_GET['do'] == 'edit' || $_POST['do'] == "add" || $_POST['do'] == "update") {
			$txt = $_POST['txt'];
			if (get_user_class() < $chrmanage_class)
				permissiondeny();
			elseif ($_POST['do'] == "add")
					sql_query ("INSERT INTO chronicle (userid,added, txt) VALUES ('".$CURUSER["id"]."', now(), ".sqlesc($txt).")") or sqlerr(__FILE__, __LINE__);
			elseif ($_POST['do'] == "update"){
				$id = 0 + $_POST['id'];
				if (!$id) { header("Location: log.php?action=chronicle"); die();}
				else sql_query ("UPDATE chronicle SET txt=".sqlesc($txt)." WHERE id=".$id) or sqlerr(__FILE__, __LINE__);}
			else {$id = 0 + $_GET['id'];
				if (!$id) { header("Location: log.php?action=chronicle"); die();}
				elseif ($_GET['do'] == "del")
					sql_query ("DELETE FROM chronicle where id = '".$id."'") or sqlerr(__FILE__, __LINE__);
				elseif ($_GET['do'] == "edit")
					edititem($lang_log['text_edit_chronicle'],'chronicle', $id);
				}
		}

		$res = sql_query("SELECT COUNT(*) FROM chronicle".$wherea);
		$row = mysql_fetch_array($res);
		$count = $row[0];

		$perpage = 50;

		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "log.php?action=chronicle&".$addparam);
		$res = sql_query("SELECT id, added, txt FROM chronicle $wherea ORDER BY added DESC $limit") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) == 0)
		print($lang_log['text_chronicle_empty']);
		else
		{

		//echo $pagertop;

			print("<table width=940 border=1 cellspacing=0 cellpadding=5>\n");
			print("<tr><td class=colhead align=center>".$lang_log['col_date']."</td><td class=colhead align=left>".$lang_log['col_event']."</td>".(get_user_class() >= $chrmanage_class ? "<td class=colhead align=center>".$lang_log['col_modify']."</td>" : "")."</tr>\n");
			while ($arr = mysql_fetch_assoc($res))
			{
				$date = gettime($arr['added'],true,false);
				print("<tr><td class=rowfollow align=center><nobr>$date</nobr></td><td class=rowfollow align=left>".format_comment($arr["txt"],true,false,true)."</td>".(get_user_class() >= $chrmanage_class ? "<td align=center nowrap><b><a href=\"".$PHP_SELF."?action=chronicle&do=edit&id=".$arr["id"]."\">".$lang_log['text_edit']."</a>&nbsp;|&nbsp;<a href=\"".$PHP_SELF."?action=chronicle&do=del&id=".$arr["id"]."\"><font color=red>".$lang_log['text_delete']."</font></a></b></td>" : "")."</tr>\n");
			}
			print("</table>");
			echo $pagerbottom;
		}

		print($lang_log['time_zone_note']);

		stdfoot();
		die;
		break;
	case "funbox":
		stdhead($lang_log['head_funbox']);
		$query = mysql_real_escape_string(trim($_GET["query"]));
		$search = $_GET["search"];
		if($query){
			switch ($search){
				case "title": $wherea=" WHERE title LIKE '%$query%' AND status != 'banned'"; break;
				case "body": $wherea=" WHERE body LIKE '%$query%' AND status != 'banned'"; break;
				case "both": $wherea=" WHERE (body LIKE '%$query%' or title LIKE '%$query%') AND status != 'banned'" ; break;
				}
			$addparam = "search=".rawurlencode($search)."&query=".rawurlencode($query)."&";
			}
		else{
		//$wherea=" WHERE status != 'banned'";
		$addparam = "";
		}
		logmenu("funbox");
		$opt = array (title => $lang_log['text_title'], body => $lang_log['text_body'], both => $lang_log['text_both']);
		searchtable($lang_log['text_search_funbox'], 'funbox', $opt);
		$res = sql_query("SELECT COUNT(*) FROM fun ".$wherea);
		$row = mysql_fetch_array($res);
		$count = $row[0];

		$perpage = 10;
		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "log.php?action=funbox&".$addparam);
		$res = sql_query("SELECT id,added, body, title, status FROM fun $wherea ORDER BY added DESC $limit") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) == 0)
			print($lang_log['text_funbox_empty']);
		else
		{

		//echo $pagertop;
			while ($arr = mysql_fetch_assoc($res)){
				$date = gettime($arr['added'],true,false);
			print("<table  width=940 border=1 cellspacing=0 cellpadding=5>\n");
			print("<tr><td class=rowhead width='10%'>".$lang_log['col_title']."</td><td class=rowfollow align=left>".$arr["title"]." (ID:".$arr['id'].") - <b>".$arr["status"]."</b></td></tr><tr><td class=rowhead width='10%'>".$lang_log['col_date']."</td><td class=rowfollow align=left>".$date."</td></tr><tr><td class=rowhead width='10%'>".$lang_log['col_body']."</td><td class=rowfollow align=left>".format_comment($arr["body"],false,false,true)."</td></tr>\n");
			
					$subres = sql_query("SELECT * FROM funcomment WHERE funid=".$arr['id']."  ORDER BY id ");
		
		while ($subrow = mysql_fetch_array($subres)) {
		
		if($subrow["text"]!=$temp){
		$temp=$subrow["text"];
		
		
		
			print("<tr id=fun".$arr['id']."><td width='15%'>");
			
		if (get_user_class() >= $sbmanage_class) {
			$del="[<a href=\"fun.php?del=".$subrow[id]."\">DEL</a>]";
		}
			print($del.get_username($subrow['userid'],false,true,true,true,false,false,"",false)."</td><td>".format_comment($subrow['text'],true,false,true,true,600,false,false));
			
			
			print("</td></tr>");
			}
			
			
		}
		
		print("<tr><td width='15%'>");
		
		print "<form action='fun.php#bottom' method='POST' name='funboxcomment'  ><input type='submit'  value=\"点击这个按钮进行评论\"  name='tofunboxcomment'  /></td><td>
	<input type='text' name='fun_text' id='fun_text' size='100' style='width: 750px; border: 1px solid gray;' /> 
	<input type=hidden name=funid value=".$arr['id'].">
	<input type=hidden name=ruturnfunid value=".$arr['id'].">
	</form>";
		
		
		print("</td></tr>");
			print("</table><br />");
			}




			echo $pagerbottom;
		}

		print($lang_log['time_zone_note']);
		stdfoot();
		die;

		break;






	case "news":
		stdhead($lang_log['head_news']);
		$query = mysql_real_escape_string(trim($_GET["query"]));
		$search = $_GET["search"];
		if($query){
			switch ($search){
				case "title": $wherea=" WHERE title LIKE '%$query%' "; break;
				case "body": $wherea=" WHERE body LIKE '%$query%' "; break;
				case "both": $wherea=" WHERE body LIKE '%$query%' or title LIKE '%$query%'" ; break;
				}
			$addparam = "search=".rawurlencode($search)."&query=".rawurlencode($query)."&";
		}
		else{
		$wherea= "";
		$addparam = "";
		}
		logmenu("news");
		$opt = array (title => $lang_log['text_title'], body => $lang_log['text_body'], both => $lang_log['text_both']);
		searchtable($lang_log['text_search_news'], 'news', $opt);

		$res = sql_query("SELECT COUNT(*) FROM news".$wherea);
		$row = mysql_fetch_array($res);
		$count = $row[0];

		$perpage = 20;

		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "log.php?action=news&".$addparam);
		$res = sql_query("SELECT id, added, body, title FROM news $wherea ORDER BY added DESC $limit") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) == 0)
		print($lang_log['text_news_empty']);
		else
		{

		//echo $pagertop;
			while ($arr = mysql_fetch_assoc($res)){
				$date = gettime($arr['added'],true,false);
			print("<table width=940 border=1 cellspacing=0 cellpadding=5>\n");
			print("<tr><td class=rowhead width='10%'>".$lang_log['col_title']."</td><td class=rowfollow align=left>".$arr["title"]."</td></tr><tr><td class=rowhead width='10%'>".$lang_log['col_date']."</td><td class=rowfollow align=left>".$date."</td></tr><tr><td class=rowhead width='10%'>".$lang_log['col_body']."</td><td class=rowfollow align=left>".format_comment($arr["body"],false,false,true)."</td></tr>\n");
			print("</table><br />");
			}
			echo $pagerbottom;
		}

		print($lang_log['time_zone_note']);

		stdfoot();
		die;
		break;
	case "poll":
		$do = $_GET["do"];
  		$pollid = $_GET["pollid"];
  		$returnto = htmlspecialchars($_GET["returnto"]);
  		if ($do == "delete")
  		{
  		if (get_user_class() < $chrmanage_class)
  		stderr($lang_log['std_error'], $lang_log['std_permission_denied']);

  		int_check($pollid,true);

   		$sure = $_GET["sure"];
   		if (!$sure)
    		stderr($lang_log['std_delete_poll'],$lang_log['std_delete_poll_confirmation'] .
    		"<a href=?action=poll&do=delete&pollid=$pollid&returnto=$returnto&sure=1>".$lang_log['std_here_if_sure'],false);

		sql_query("DELETE FROM pollanswers WHERE pollid = $pollid") or sqlerr();
		sql_query("DELETE FROM polls WHERE id = $pollid") or sqlerr();
		$Cache->delete_value('current_poll_content');
		$Cache->delete_value('current_poll_result', true);
		if ($returnto == "main")
			header("Location: " . get_protocol_prefix() . "$BASEURL");
		else
			header("Location: " . get_protocol_prefix() . "$BASEURL/log.php?action=poll&deleted=1");
		die;
  }

  $rows = sql_query("SELECT COUNT(*) FROM polls") or sqlerr();
  $row = mysql_fetch_row($rows);
  $pollcount = $row[0];
  if ($pollcount == 0)
  	stderr($lang_log['std_sorry'], $lang_log['std_no_polls']);
  $polls = sql_query("SELECT * FROM polls ORDER BY id DESC LIMIT 1," . ($pollcount - 1 )) or sqlerr();
  stdhead($lang_log['head_previous_polls']);
  		logmenu("poll");
  		print("<table border=1 cellspacing=0 width=940 cellpadding=5>\n");
		//print("<tr><td class=colhead align=center>".$lang_log['text_previous_polls']."</td></tr>\n");

    function srt($a,$b)
    {
      if ($a[0] > $b[0]) return -1;
      if ($a[0] < $b[0]) return 1;
      return 0;
    }

  while ($poll = mysql_fetch_assoc($polls))
  {
    $o = array($poll["option0"], $poll["option1"], $poll["option2"], $poll["option3"], $poll["option4"],
    $poll["option5"], $poll["option6"], $poll["option7"], $poll["option8"], $poll["option9"],
    $poll["option10"], $poll["option11"], $poll["option12"], $poll["option13"], $poll["option14"],
    $poll["option15"], $poll["option16"], $poll["option17"], $poll["option18"], $poll["option19"]);

    print("<tr><td align=center>\n");

    print("<p class=sub>");
    $added = gettime($poll['added'], true, false);

    print($added);

    if (get_user_class() >= $pollmanage_class)
    {
    	print(" - [<a href=makepoll.php?action=edit&pollid=$poll[id]><b>".$lang_log['text_edit']."</b></a>]\n");
			print(" - [<a href=?action=poll&do=delete&pollid=$poll[id]><b>".$lang_log['text_delete']."</b></a>]\n");
		}

		print("<a name=$poll[id]>");

		print("</p>\n");

    print("<table class=main border=1 cellspacing=0 cellpadding=5><tr><td class=text>\n");

    print("<p align=center><b>" . $poll["question"] . "</b></p>");

    $pollanswers = sql_query("SELECT selection FROM pollanswers WHERE pollid=" . $poll["id"] . " AND  selection < 20") or sqlerr();

    $tvotes = mysql_num_rows($pollanswers);

    $vs = array(); // count for each option ([0]..[19])
    $os = array(); // votes and options: array(array(123, "Option 1"), array(45, "Option 2"))

    // Count votes
    while ($pollanswer = mysql_fetch_row($pollanswers))
      $vs[$pollanswer[0]] += 1;

    reset($o);
    for ($i = 0; $i < count($o); ++$i)
      if ($o[$i])
        $os[$i] = array($vs[$i], $o[$i]);

    print("<table width=100% class=main border=0 cellspacing=0 cellpadding=0>\n");
    $i = 0;
    while ($a = $os[$i])
    {
	  	if ($tvotes > 0)
	  		$p = round($a[0] / $tvotes * 100);
	  	else
				$p = 0;
      print("<tr><td class=embedded>" . $a[1] . "&nbsp;&nbsp;</td><td class=\"embedded nowrap\">" .
        "<img class=\"bar_end\" src=\"pic/trans.gif\" alt=\"\" /><img class=\"unsltbar\" src=\"pic/trans.gif\" style=\"width: " . ($p * 3) . "px\" /><img class=\"bar_end\" src=\"pic/trans.gif\" alt=\"\" /> $p%</td></tr>\n");
      ++$i;
    }
    print("</table>\n");
	$tvotes = number_format($tvotes);
    print("<p align=center>".$lang_log['text_votes']."$tvotes</p>\n");

    print("</td></tr></table><br /><br />\n");

    print("</p></td></tr>\n");
}
	print("</table>");
		print($lang_log['time_zone_note']);
		stdfoot();
		die;
		break;
	
	
	case "highcharts":
		stdhead('站点日记');
		logmenu('highcharts');
		?>
		<script src="javascript/highcharts.js"></script>
		<?
		if($_GET['type']=='torrent')
		$usephp='torrents.php';
		elseif($_GET['type']=='signup')
		$usephp='signup.php';
		elseif($_GET['type']=='bets')
		$usephp='bets.php';
		elseif($_GET['type']=='baka')
		$usephp='bakaperday.php';
		elseif($_GET['type']=='com')
		$usephp='comment.php';
		elseif($_GET['type']=='peer')
		$usephp='peers.php';
		elseif($_GET['type']=='mypeer')
		$usephp='peersmy.php';
		else
		$usephp='bakaperday.php';
		
		$wheretype=0+$_GET['wheretype'];
		
		$TIMENOW=strtotime(date("Y-m-d"));
		if($wheretype==1){$searchwheretime=$TIMENOW-3600*24*30;$searchwheredate=sqlesc(date('Y-m-d',$searchwheretime));}
		elseif($wheretype==2){$searchwheretime=$TIMENOW-3600*24*365;$searchwheredate=sqlesc(date('Y-m-d',$searchwheretime));}
		elseif($wheretype==3){$searchwheretime='0';$searchwheredate=sqlesc('0000-00-00');}
		else{$wheretype=0;$searchwheretime=$TIMENOW-3600*24*7;$searchwheredate=sqlesc(date('Y-m-d',$searchwheretime));}
		
			define('IN_highcharts','yeah');
			require_once("highcharts/".$usephp);



		?>
		<p><a href='?action=highcharts&type=torrent'><b>种子发布</b></a> | <a href='?action=highcharts&type=signup'><b>用户注册</b></a> | <a href='?action=highcharts&type=peer'><b>流量统计</b></a><a href='?action=highcharts&type=mypeer&userid=<?echo $CURUSER['id']?>'><b>(我的)</b></a> | <a href='?action=highcharts&type=com'><b>帖子回复</b></a> | <a href='?action=highcharts&type=bets'><b>竞猜大厅</b></a> | <a href='?action=highcharts&type=baka'><b>每日签到</b></a><p>
		<a href='?action=highcharts&type=<?echo $_GET['type']?>&wheretype=0' <?echo ($wheretype==0?'class="faqlink"':"")?>><b>一周</b></a> | <a href='?action=highcharts&type=<?echo $_GET['type']?>&wheretype=1' <?echo ($wheretype==1?'class="faqlink"':"")?>><b>一月</b></a> | <a href='?action=highcharts&type=<?echo $_GET['type']?>&wheretype=2' <?echo ($wheretype==2?'class="faqlink"':"")?>><b>一年</b></a> | <a href='?action=highcharts&type=<?echo $_GET['type']?>&wheretype=3' <?echo ($wheretype==3?'class="faqlink"':"")?>><b>全部</b></a><p>
		<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
		<?		
		stdfoot();
		die;
		break;}
}

?>
