<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

if ($CURUSER["class"] < UC_POWER_USER)
stderr("Sorry", "You have to Be A Power User to use SoftBet.");

$id = isset($_POST['id']) && is_valid_id($_POST['id']) ? $_POST['id'] : 0;
if(get_bet_moderators_is($id))stderr('错误','庄家禁止押注');
$bonus = (int) $_POST['bonus'];

if($CURUSER['seedbonus'] < $bonus){
header("location: bet_odds.php?err=b&amp;id=".$id."");
exit;
}

if($CURUSER['seedbonus'] < $bonus || $bonus < 1){
header("Location: bet_odds.php?err=c&amp;id=".$id."");
exit;
}

$res = sql_query("SELECT * FROM betoptions WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_assoc($res);
$gameid = (int) $a['gameid'];

if($gameid== 0){
header("location: bet.php");
exit;
}


$res2 = sql_query("SELECT * from betgames where id =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
$s = mysql_fetch_assoc($res2);

if($s['active'] == 0||$s['fix'] == 1){
header("location: bet.php");
exit;
}

$k = sql_query("SELECT * FROM bets WHERE gameid = ".sqlesc($gameid)." and optionid != ".sqlesc($a["id"])." AND userid =".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($k) > 0)
{
stderr(	"Sorry", "你已经下注过该盘其他选项了");
}
/*
$k = sql_query("SELECT * FROM bets WHERE gameid = ".sqlesc($gameid)." AND userid =".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($k) > 0)
{
stderr(	"Sorry", "你已经下注过该盘了");
}
*/
$tid = time();

sql_query("INSERT INTO bets(gameid,bonus,optionid,userid,date) VALUES(".sqlesc($gameid).", ".sqlesc($bonus).", ".sqlesc($id).", ".sqlesc($CURUSER["id"]).", '$tid') ON DUPLICATE KEY update bonus=bonus+values(bonus)") or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE users SET seedbonus = seedbonus -".sqlesc($bonus)." WHERE id =".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO betlog(userid,date,msg,bonus,betid) VALUES($CURUSER[id], ".sqlesc(date("Y-m-d H:i:s")).", 'Bet. ".$s['heading']." -> ".$a['text']."-".$bonus." Points.',-$bonus,$gameid)") or sqlerr(__FILE__, __LINE__);

$e = sql_query("SELECT * FROM betoptions WHERE gameid =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
while($f = mysql_fetch_assoc($e))
{

$optionid = $f['id'];
$total = 0;
$optiontotal = 0;

$b = sql_query("SELECT * FROM bets WHERE gameid = ".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
while($c = mysql_fetch_assoc($b))
{
$total += $c['bonus'];
if($c['optionid'] == $optionid)
$optiontotal += $c['bonus'];
}
if($optiontotal == 0)
$odds = 0.00;
else
$odds = number_format($total/$optiontotal, 2, '.', '');
sql_query("UPDATE betoptions SET odds = ".sqlesc($odds)." WHERE id = ".sqlesc($optionid)."") or sqlerr(__FILE__, __LINE__);
}

header("location: bet_coupon.php");
?>