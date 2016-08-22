<?php
require "include/bittorrent.php";
 
 
dbconn(false);
loggedinorreturn();
 



$id = isset($_POST['id']) && is_valid_id($_POST['id']) ? $_POST['id'] : 0;

if ($CURUSER["class"] < UC_MODERATOR&&!get_bet_moderators_is($id))
    stderr("Error", "Bet Stuff - Nosey Cunt !");
	
$heading = htmlspecialchars($_POST['heading']);
$heading = str_replace("'","",$heading);
$undertext = htmlspecialchars($_POST['undertext']);
$undertext = str_replace("'","",$undertext);
//$endtime = (int) $_POST['endtime'] + time();
$endtime=strtotime($_POST['endtime'])?strtotime($_POST['endtime']):time();
$sort = (int) $_POST['sort'];
$res = "UPDATE betgames SET heading =".sqlesc($heading).", undertext=".sqlesc($undertext).", endtime=".sqlesc($endtime).", sort=".sqlesc($sort)." WHERE fix=0 and active=0 and id =".sqlesc($id)."";
sql_query($res) or sqlerr(__FILE__, __LINE__);
header("location: bet_admin.php");

?>