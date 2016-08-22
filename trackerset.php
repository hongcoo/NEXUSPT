<?php
require_once("include/bittorrent.php");
dbconn();
//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

if(isset($CURUSER))
{
	$Cache->delete_value('user_passkey_'.$CURUSER['passkey'].'_content');
	sql_query("UPDATE useriptype SET reuseriptype= ".sqlesc($CURUSER["passkey"])." where userid=".sqlesc($CURUSER["id"]));	
	sql_query("UPDATE users SET  MODEMAX = 0   WHERE id=" . sqlesc($CURUSER["id"]))  ;
	echo "ok";
}
else echo "failed";
?>
