<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");


if ($_GET['id'])
	die("Party is over!". "This trick doesn't work anymore. You need to click the button!");
$userid = $CURUSER["id"];
//$torrentid =0+ $_POST["id"];
$ratednum=0+ $_POST["ratednum"];
$torrenthash=sqlesc(pack("H*",$_POST["id"]));
$tsql = sql_query("SELECT owner,id FROM torrents where info_hash=$torrenthash");


$arr = mysql_fetch_array($tsql);
if (!$arr)
	die("Error". "Invalid torrent id!");
	
	
$torrentowner = $arr['owner'];
$torrentid=$arr['id'];
$tsql = sql_query("SELECT COUNT(*) FROM thanks where torrentid=$torrentid and userid=$userid");
$trows = mysql_fetch_array($tsql);
$t_ab = $trows[0];
//if ($t_ab != 0)
/*	stderr("Error", "You already said thanks!");
*/
	
if (isset($userid) && isset($torrentid)&&$torrentowner!=$CURUSER['id'])
{

if($CURUSER["class"]>=$userbar_class)$betmb_options = array(50 => 1, 100 => 1, 200 => 1, 500 => 1,1000 => 1);
else
$betmb_options = array(50 => 1, 100 => 1, 200 => 1, 500 => 1);




if (!isset($betmb_options[$ratednum])||$CURUSER['seedbonus']<10000||$CURUSER["class"]<=UC_USER||$ratednum<0)$ratednum=0; 


$res = sql_query("INSERT INTO thanks (torrentid, userid, rated) VALUES ($torrentid, $userid,$ratednum)  ON DUPLICATE KEY update rated=(rated+".$ratednum.")");

if ($t_ab != 0){
$saythanks_bonus=0;
$receivethanks_bonus=0;
}
if($ratednum!=1000)KPS("+",$saythanks_bonus-$ratednum,$CURUSER['id']);//User gets bonus for saying thanks
KPS("+",$receivethanks_bonus+$ratednum*0.8,$torrentowner);//Thanks receiver get bonus
echo ok;
//echo $receivethanks_bonus+$saythanks_bonus;
}
