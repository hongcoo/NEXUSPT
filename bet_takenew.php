<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

if ($CURUSER["class"] < UC_POWER_USER)//if ($CURUSER["class"] < UC_MODERATOR&&!get_bet_moderators_is())
   stderr("Betting" ,"Nosey Cunt !");

if (empty($_POST['heading']) || empty($_POST['undertext']) || empty($_POST['endtime'])){
stderr('Silly Rabbit', 'Twix are for kids.. Enter a Bet title or name or endtime ya nugget !');
}

$heading = htmlspecialchars($_POST['heading']);
$heading = str_replace("'","",$heading);
$undertext = htmlspecialchars($_POST['undertext']);
$undertext = str_replace("'","",$undertext);
//$endtime = (int) $_POST['endtime'] + time();
$endtime=strtotime($_POST['endtime'])?strtotime($_POST['endtime']):time();
$sort = (int) $_POST['sort'];
sql_query("INSERT INTO betgames(heading, undertext, endtime, sort, creator) VALUES(".sqlesc($heading).", ".sqlesc($undertext).", ".sqlesc($endtime).", ".sqlesc($sort).", '$CURUSER[id]')") or sqlerr(__FILE__, __LINE__);

$gameid = @mysql_insert_id();
$textarr=explode(',', $_POST['text']);
foreach($textarr as $text)if($text)sql_query("INSERT INTO betoptions(gameid,text) VALUES(".sqlesc($gameid).",".sqlesc($text).")");

KPS("-",1000,$CURUSER['id']);

header("location: bet_admin.php");
?>