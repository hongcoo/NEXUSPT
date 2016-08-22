<?php
require "include/bittorrent.php";
 
 
dbconn(false);
loggedinorreturn();

 

$HTMLOUT ="下注金额返还用户:<br/>";




$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

if ($CURUSER["class"] < UC_MODERATOR&&!get_bet_moderators_is())
stderr('ERROR',"Bet Stuff - Nosey Cunt !");

$res = sql_query("SELECT * FROM betgames where id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) < 1)
stderr("Error", "No game with that ID. Contact the coder.");
$res = mysql_fetch_array($res);
$message = $res["heading"];


$res1 = sql_query("SELECT * FROM bets where gameid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res1) < 1)
stderr("Error", "No game with that ID. Contact the coder or delete it by first type");
$bets = mysql_num_rows($res1);

$a = sql_query("SELECT * FROM `betlog` WHERE betid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($a) < 1 || mysql_num_rows($a) > 1000)
stderr(	"Error", "No bonus log with similar message. Contact the coder.");

$whoopsie = 0;

$log = mysql_num_rows($a);

if(isset($_GET["shite"]))

$shite = 1;
else
$shite = 0;

$res3 = sql_query("SELECT * FROM bets where gameid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$bets = mysql_num_rows($res3);
if($log != $bets && $shite == 0)
{
stderr("Error", "Number of operations and bonus logs entered did not match. ".htmlspecialchars($log). " vs ".htmlspecialchars($bets)." Contact the coder...<br />Fuck it... <a href='bet_nullbet.php?id=".$id."&amp;shite=1'><u>Do it anyway</u></a>",false);
}
else
{
$added = sqlesc(date("Y-m-d H:i:s"));
while($res3 = mysql_fetch_array($a))
	{
	$uid =  $res3['userid'];
	$points = $res3['bonus'];	
	$HTMLOUT .="".$points." -> ";
	$HTMLOUT .="".get_username($res3['userid'])."<br />";	
	sql_query("UPDATE users SET seedbonus = seedbonus-".sqlesc($points)." WHERE id =".sqlesc($uid)." LIMIT 1") or sqlerr(__FILE__, __LINE__);	
	
	if($points < 0){
	$points=0-$points;
	$subject = sqlesc("下注奖金返还 $points");
	$msg = sqlesc("由于错误或者竞猜提前结束,你获得了押注在 ".$message." 上的 ".$points." 魔力值. ( ".$res3['msg']." )");	
	}else{
	$subject = sqlesc("获奖奖金回收 $points");
	$msg = sqlesc("由于错误或者竞猜提前结束,你在 ".$message." 上获益的 ".$points." 魔力值被回收. ( ".$res3['msg']." )");
	}
	
	
	sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, $uid, $added, $msg, $subject)") or sqlerr(__FILE__, __LINE__);
	$msg2 = sqlesc("Bet-bonus at stake: ".$message." <b>".$points." Points</b>");
	sql_query("INSERT INTO betlog(userid,msg,date,bonus) VALUES($uid, $msg2, $added, $points)") or sqlerr(__FILE__, __LINE__);
  $whoopsie -= $points;
	}
sql_query("DELETE FROM betgames WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM bets WHERE gameid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM bets WHERE id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM betoptions WHERE gameid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM betlog WHERE betid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
stdhead("Pay back credits", false) ;print $HTMLOUT ;stdfoot();
}

?>