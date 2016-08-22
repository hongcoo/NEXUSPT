<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path("viewpeerlist.php"));

//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

$id = 0 + $_GET['id'];
if(isset($CURUSER))
{

function mksizepeer($bytes)
{
	if ($bytes < 10)
	return "0";
	elseif ($bytes < 1000 * 1024)
	return number_format($bytes / 1024, 2) . "KB";
	elseif ($bytes < 1000 * 1048576)
	return number_format($bytes / 1048576, 2) . "MB";
	elseif ($bytes < 1000 * 1073741824)
	return number_format($bytes / 1073741824, 2) . "GB";
	elseif ($bytes < 1000 * 1099511627776)
	return number_format($bytes / 1099511627776, 3) . "TB";
	else
	return number_format($bytes / 1125899906842624, 3) . "PB";
}

function dltable($name, $arr, $torrent,$count,$type)
{
	global $lang_viewpeerlist,$viewanonymous_class,$userprofile_class,$enablelocation_tweak;
	global $CURUSER;
	//$s = "<b>" . count($arr) . " $name</b>\n";
	$s = "<a href=\"javascript: hidepeerlist();\"><b>" . $count . " $name</b></a>\n";
	if (!count($arr))
		return $s;
	$s .= "\n";
	$s .= "<table  id='{$type}s' class=main border=1 cellspacing=0 cellpadding=3 width=100%>\n";
	$s .= "<thead><tr><td class=colhead align=center width=1%>".$lang_viewpeerlist['col_user_ip']."</td>" .
	($enablelocation_tweak == 'yes' || get_user_class() >= $userprofile_class ? "<td class=colhead align=center width=1% >".$lang_viewpeerlist['col_location']."</td><td class=colhead align=center width=1% >".$lang_viewpeerlist['col_location_type']."</td>" : "").

	"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_uploaded']."</td>".
	($type=="seeder"?"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_rate']."</td>":"" ) .
	"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_av_rate']."</td>" .
	"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_downloaded']."</td>" .
	($type=="leecher"?"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_rate']."</td>":"" ) .
	"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_av_rate']."</td>".
	/*"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_ratio']."</td>" .*/
	($type=="seeder"?"":"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_complete']."</td>" ) .
	"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_connected']."</td>" .
	"<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_idle']."</td>" 
	."<td class=colhead align=center width=1%>".$lang_viewpeerlist['col_client']."</td></tr></thead><tbody>\n";
	$now = time();
	foreach ($arr as $e) {
		$privacy = get_single_value("users", "privacy","WHERE id=".sqlesc($e['userid']));
		++$num;

		$highlight = $CURUSER["id"] == $e['userid'] ? " bgcolor=#BBAF9B" : "";
		$s .= "<tr$highlight>\n";
		if($privacy == "strong" || ($torrent['anonymous'] == 'yes' && $e['userid'] == $torrent['owner']))
		{
			if(get_user_class() >= $viewanonymous_class || $e['userid'] == $CURUSER['id'])
				$s .= "<td class=rowfollow align=left width=1%>(" . get_username($e['userid']) . ")";
			else
				$s .= "<td class=rowfollow align=left width=1%>".$lang_viewpeerlist['text_anonymous']."</a></td>\n";
		}
		else
			$s .= "<td class=rowfollow align=left width=1%>" . get_username($e['userid']);

		$secs = max(300, ($e["la"] - $e["st"]));
		
		
		if ($enablelocation_tweak == 'yes'){

		
					list($loc_pub, $loc_mod) = get_ip_location($e["ip"]);
					
					
			//$location = ((ip2long($e["ip"]) == 0) ? "<b>IPV6</b>" : $loc_pub).(($e["connectable"] == 'yes')?"":"<b>*</b>" );
			$xinhao=(($e["connectable"] == 'yes')?"":"<b>*</b>" );
			$location = $loc_pub;
			$location = get_user_class() >= $userprofile_class ? "<div title='" . $e["ip"] . "'>" . $location  . "</div>" : $location ;
			
			
			if($e["iptype"]==6)
			$iptype="<b>IPV6</b>";
			else
			if($e["iptype"]==5)
			$iptype="<b>6TO4</b>";
			else
			$iptype="IPV4";
			
			if($e["iptypesecond"]==6)
			$iptypesecond=" / <b>IPV6*</b>";
			else
			if($e["iptypesecond"]==5)
			$iptypesecond=" / <b>6TO4*</b>";
			elseif($e["iptypesecond"]==4)
			$iptypesecond=" / IPV4<b>*</b>";
			else
			$iptypesecond="";
			
			
			$s .= "<td class=rowfollow align=left width=1%><nobr>" . $location . "</nobr></td>\n<td class=rowfollow align=left width=1%><nobr>" . $iptype.$xinhao.$iptypesecond. "</nobr></td>\n";
			
			
		}
		elseif (get_user_class() >= $userprofile_class){
			$location = $e["ip"];
			$s .= "<td class=rowfollow align=center width=1%><nobr>" . $location . "</nobr></td>\n";
		}
		else $location = "";
		

		
		if($e["connectable"] == 'yes'||1){
		$s .= "<td class=rowfollow align=center width=1%><nobr>" . mksizepeer($e["uploaded"]) . "</nobr></td>\n";

		/*$s .= "<td class=rowfollow align=center width=1%><nobr>" . mksizepeer( max(1,($e["uploaded"] - $e["uploadoffset"]) )/ $secs) . "/s</nobr></td>\n";*/
		if($type=="seeder")$s .= "<td class=rowfollow align=center width=1%><nobr>" . mksizepeer( max(1,($e["upthis"]) )/ max(1,$e["announcetime"])) . "/s</nobr></td>\n";
		$s .= "<td class=rowfollow align=center width=1%><nobr>" . mksizepeer( max(1,($e["uploaded"] - $e["uploadoffset"]) )/ max(1,$e["seedtime"])) . "/s</nobr></td>\n";
		$s .= "<td class=rowfollow align=center width=1%><nobr>" . mksizepeer($e["downloaded"]) . "</nobr></td>\n";
		
		/*if ($e["seeder"] == "no")
		$s .= "<td class=rowfollow align=center width=1%><nobr>" . mksizepeer( max(1,($e["downloaded"] - $e["downloadoffset"])) / $secs) . "/s</nobr></td>\n";
		else
		$s .= "<td class=rowfollow align=center width=1%><nobr>" . mksizepeer( max(1,($e["downloaded"] - $e["downloadoffset"]) )/ max(300, $e["finishedat"] - $e[st])) .	"/s</nobr></td>\n";*/
		if($type=="leecher")$s .= "<td class=rowfollow align=center width=1%><nobr>" . mksizepeer( max(1,($e["downthis"]) )/ max(1,$e["announcetime"])) . "/s</nobr></td>\n";
		$s .= "<td class=rowfollow align=center width=1%><nobr>" . mksizepeer( max(1,($e["downloaded"] - $e["downloadoffset"])) / max(1,$e["leechtime"])) . "/s</nobr></td>\n";
		/*if ($e["downloaded"])
		{
			$ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
			$s .= "<td class=rowfollow align=\"center\" width=1%><font color=" . get_ratio_color($ratio) . "><nobr>" . number_format($ratio, 3) . "</nobr></font></td>\n";
		}
		elseif ($e["uploaded"])
		$s .= "<td class=rowfollow align=center width=1%>".$lang_viewpeerlist['text_inf']."</td>\n";
		else
		$s .= "<td class=rowfollow align=center width=1%>---</td>\n";*/
		
		}else{
		$s .= "<td class=rowfollow align=center width=1%>---</td>\n";
		$s .= "<td class=rowfollow align=center width=1%>---</td>\n";
		/*$s .= "<td class=rowfollow align=center width=1%>---</td>\n";*/
		$s .= "<td class=rowfollow align=center width=1%>---</td>\n";
		$s .= "<td class=rowfollow align=center width=1%>---</td>\n";
		$s .= "<td class=rowfollow align=center width=1%>---</td>\n";
		
		
		
		}
		
		
		
		
		
		
		
		$s .= ($type=="seeder"?"":"<td class=rowfollow align=center width=1%><nobr>" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</nobr></td>\n");
		$s .= "<td class=rowfollow align=center width=1%><nobr>" . mkprettytime($now - $e["st"]) . "</nobr></td>\n";
		$s .= "<td class=rowfollow align=center width=1%><nobr>" . mkprettytime($now - $e["la"]) . "</nobr></td>\n";
		$s .= "<td class=rowfollow align=center width=1%><nobr>" . htmlspecialchars(get_agent($e["peer_id"],$e["agent"])) . "</nobr></td>\n";
		$s .= "</tr>\n";
	}
	$s .= "</tbody></table>\n";
	return $s;
}



	
	
	$downloaders = array();
	$seeders = array();
	$noconnectable = array();
	$subres = sql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, ip, port, uploaded, downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, peer_id, UNIX_TIMESTAMP(last_action) AS la, userid ,iptype ,seedtime , leechtime ,upthis ,downthis,announcetime FROM peers WHERE torrent = $id and userid > 0  ORDER BY userid desc") or sqlerr();
	while ($subrow = mysql_fetch_array($subres)) {
	if($subrow["connectable"] == "no")
		$noconnectable[]=$subrow;
	elseif ($subrow["seeder"] == "yes")
		$seeders[$subrow[userid]] = $subrow;
	else
		$downloaders[$subrow[userid]] = $subrow;
	}
	
	foreach($noconnectable as $noconnectablerow)
	if($seeders[$noconnectablerow[userid]])
		$seeders[$noconnectablerow[userid]]['iptypesecond']=$noconnectablerow['iptype'];
	elseif($downloaders[$noconnectablerow[userid]])
		$downloaders[$noconnectablerow[userid]]['iptypesecond']=$noconnectablerow['iptype'];
	elseif ($noconnectablerow["seeder"] == "yes")
		$seeders[$noconnectablerow[userid]] = $noconnectablerow;
	else
		$downloaders[$noconnectablerow[userid]] = $noconnectablerow;
	
	$seedercount = get_single_value("peers","COUNT( DISTINCT (userid))","WHERE torrent = $id and seeder ='yes'");
	$leechercount = get_single_value("peers","COUNT( DISTINCT (userid))","WHERE torrent = $id and seeder ='no'");
	
	

	function leech_sort($a,$b) {
		$x = $a["to_go"];
		$y = $b["to_go"];
		if ($x == $y)
			return 0;
		if ($x < $y)
			return -1;
		return 1;
	}
	function seed_sort($a,$b) {
		$x = $a["uploaded"];
		$y = $b["uploaded"];
		if ($x == $y)
			return 0;
		if ($x < $y)
			return 1;
		return -1;
	}
	$res = sql_query("SELECT torrents.id, torrents.owner, torrents.size, torrents.anonymous FROM torrents WHERE torrents.id = $id LIMIT 1") or sqlerr();
	$row = mysql_fetch_array($res);
	//usort($seeders, "seed_sort");
	//usort($downloaders, "leech_sort");

	print(dltable($lang_viewpeerlist['text_seeders'], $seeders, $row,$seedercount,"seeder"));
	print(dltable($lang_viewpeerlist['text_leechers'], $downloaders, $row,$leechercount,"leecher"));


}
print '<!--'.(microtime(1)-TIMENOWSTART).'-->';
?>
