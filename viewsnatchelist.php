<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path('viewsnatches.php'));

//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

$id = 0 + $_GET['id'];
if(!isset($CURUSER))die();

$id = 0+$_GET["id"];
	
	
	$s = "<a href=\"javascript: hidepeerlist();\"><b>" . $arr=get_row_count("snatched", "WHERE  finished = 'yes' AND  torrentid = ".sqlesc($id)) . " 个完成者</b></a>\n";
	if (!count($arr)){print($s);die;}
	$s .= "\n";
	$s .= "<table  id='viewsnatcheslist' class=main border=1 cellspacing=0 cellpadding=3 width=100%>\n";
	$s .= "<thead>";
	print $s; 
	
	print("<tr><td class=colhead align=center>".$lang_viewsnatches['col_username']."</td>".(get_user_class() >= $userprofile_class ? "<td class=colhead align=center>".$lang_viewsnatches['col_ip']."</td>" : "")."<td class=colhead align=center>".$lang_viewsnatches['col_uploaded']."</td><td class=colhead align=center>".$lang_viewsnatches['col_downloaded']."</td><td class=colhead align=center>".$lang_viewsnatches['col_ratio']."</td><td class=colhead align=center>".$lang_viewsnatches['col_se_time']."</td><td class=colhead align=center>".$lang_viewsnatches['col_le_time']."</td><td class=colhead align=center>".$lang_viewsnatches['col_when_completed']."</td><td class=colhead align=center>".$lang_viewsnatches['col_last_action']."</td></tr>");
	
	print "</thead><tbody>";
	
	$res = sql_query("SELECT * FROM snatched WHERE finished = 'yes' AND torrentid =" . sqlesc($id).  " ORDER BY last_action DESC");

	while ($arr = mysql_fetch_assoc($res))
	{

		if ($arr["downloaded"] > 0)
		{
			$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
			$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
		}
		elseif ($arr["uploaded"] > 0)
			$ratio = $lang_viewsnatches['text_inf'];
		else
			$ratio = "---";
			
		$uploaded =mksize($arr["uploaded"]);
		$downloaded = mksize($arr["downloaded"]);
		$seedtime = mkprettytime($arr["seedtime"]);
		$leechtime = mkprettytime($arr["leechtime"]);

		$uprate = $arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0);
		$downrate = $arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0);
		//end

		$highlight = ($CURUSER["id"] == $arr["userid"]) ? " class=halfdown_bg" : "";
		$userrow = get_user_row($arr['userid']);
		if ($userrow['privacy'] == 'strong'){
			$username = $lang_viewsnatches['text_anonymous'];
			if (get_user_class() >= $viewanonymous_class || $arr["userid"] == $CURUSER['id'])
				$username .= "<br />(".get_username($arr[userid]).")";
		}
		else $username = get_username($arr[userid]);
		
		
		list($loc_pub, $loc_mod) = get_ip_location($arr[ip],true);
		print("<tr$highlight><td class=rowfollow align=center>" . $username ."</td>".(get_user_class() >= $userprofile_class ? "<td class=rowfollow align=center>".$loc_pub."</td>" : "")."<td class=rowfollow align=center>".$uploaded."@".$uprate.$lang_viewsnatches['text_per_second']."</td><td class=rowfollow align=center>".$downloaded."@".$downrate.$lang_viewsnatches['text_per_second']."</td><td class=rowfollow align=center>$ratio</td><td class=rowfollow align=center>$seedtime</td><td class=rowfollow align=center>$leechtime</td><td class=rowfollow align=center>".($arr[finished]==yes?gettime($arr[completedat],true,false):"-")."</td><td class=rowfollow align=center>".gettime($arr[last_action],true,false)."</td></tr>\n");
	}
		print("</tbody></table>\n");

?>
