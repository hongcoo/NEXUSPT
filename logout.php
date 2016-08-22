<?php
require_once("include/bittorrent.php");
dbconn();
//sql_query("INSERT INTO chenzhuyudubug (page) VALUES (".sqlesc($_SERVER['HTTP_ACCEPT']).")");
loggedinorreturn(true);

//if($_GET)
sql_query("UPDATE users SET logouttime = ".sqlesc(TIMENOW)." WHERE id =".sqlesc(0+$CURUSER["id"]));


//if($_SERVER['REQUEST_URI']=='/logout.php')
logoutcookie();
//logoutsession();
//header("Refresh: 0; url=./");
Header("Location: " . get_protocol_prefix() . "$BASEURL/");
?>