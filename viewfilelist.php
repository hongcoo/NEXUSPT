<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());

//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

$id = 0 + $_GET['id'];
$maxnum=50;
if(isset($CURUSER))
{

	$Cache->new_page('viewfilelist_'.$id, 600, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
	
	
	$s = "<table class=\"main\" border=\"1\" cellspacing=0 cellpadding=\"5\">\n";
	$totalnum=0;
	$subres = sql_query("SELECT * FROM files WHERE torrent = ".sqlesc($id)." ORDER BY RAND() limit ".($maxnum+5));
	$s.="<thead><tr><td class=colhead>".$lang_viewfilelist['col_path']."</td><td class=colhead align=center><img class=\"size\" src=\"pic/trans.gif\" alt=\"size\" /></td></tr></thead><tbody>\n";
	while ($subrow = mysql_fetch_array($subres)) {
	if($totalnum>$maxnum){
	$endbreak=true;
	break;}
		$s .= "<tr><td class=rowfollow>".$subrow["filename"]."</td><td class=rowfollow align=\"right\">" . mksize($subrow["size"]) . "</td></tr>\n";
		$totalnum++;
	}
	$s .= "</tbody>";
	if($endbreak)$s .= "<tr><td class=colhead colspan=2>仅随机显示 $maxnum 个文件...想了解更多内容?请下载!</td></tr>\n";
	$s .= "</table>\n";
	echo $s;
	
	$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();
	
}
?>
