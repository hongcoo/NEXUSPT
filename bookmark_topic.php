<?php
require "include/bittorrent.php";
dbconn();

//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

$topicid = 0 + $_POST['topicid'];
if(isset($CURUSER))
{
	$res_bookmark = sql_query("SELECT * FROM bookmarks_topic WHERE topicid=" . sqlesc($topicid) . " AND userid=" . sqlesc($CURUSER[id]));
	if (mysql_num_rows($res_bookmark) >0){
		sql_query("DELETE FROM bookmarks_topic WHERE topicid=" . sqlesc($topicid) . " AND userid=" . sqlesc($CURUSER['id'])) or sqlerr(__FILE__,__LINE__);
		$Cache->delete_value('user_'.$CURUSER['id'].'_bookmark_array_topic');
		echo "deleted";
		}
	else{
		sql_query("INSERT INTO bookmarks_topic (topicid, userid,time_book) VALUES (" . sqlesc($topicid) . "," . sqlesc($CURUSER['id']) . "," . TIMENOW . ")") or sqlerr(__FILE__,__LINE__);
		$Cache->delete_value('user_'.$CURUSER['id'].'_bookmark_array_topic');
		echo "added";
	}
}
else echo "failed";
?>
