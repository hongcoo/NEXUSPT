<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();


$betonwer=$bet_robot=$ROBOTUSERID;
$forumid = 10;


$HTMLOUT ="";
$subject ="";

 


//==Autopost By Retro
function auto_bet($subject = "Error - Subject Missing", $body = "Error - No Body",$gameid=0)
{
    global $CURUSER, $betonwer,$bet_robot,$forumid;
    
   
    $res = sql_query("SELECT id FROM topics WHERE forumid=$forumid AND subject=$subject");

    if (mysql_num_rows($res) == 1) {
        $arr = mysql_fetch_array($res);
        $topicid = $arr['id'];
    } else {
        //$subject = sqlesc($subject."结果");
		//sql_query("UPDATE forums SET topiccount=topiccount+1, postcount=postcount+1 WHERE id='$forumid'");
        sql_query("INSERT INTO topics (userid, forumid, subject) VALUES(".$bet_robot.", $forumid, $subject)") or sqlerr(__FILE__, __LINE__);
        $topicid = @mysql_insert_id();
    }
	sql_query("UPDATE betgames SET topicid = $topicid WHERE id= $gameid");
    $added = "'".date("Y-m-d H:i:s") . "'";
    sql_query("INSERT INTO posts (topicid, userid, added, body) " . "VALUES($topicid, ".$bet_robot.", $added, $body)") or sqlerr(__FILE__, __LINE__);
	$lastpostid = $firstpostid = @mysql_insert_id();
	sql_query("INSERT INTO posts (topicid, userid, added, body) " . "VALUES($topicid, ".$betonwer.", $added,'其实我才是楼主')") or sqlerr(__FILE__, __LINE__);
    $lastpostid = @mysql_insert_id();
    sql_query("UPDATE topics SET lastpost=$firstpostid,firstpost=$lastpostid WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
}


$forumlink = "[url=forums.php?action=viewforum&forumid=".$forumid."]博彩专区[/url]";
//==End

$date = date("Y-m-d H:i:s");
$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;
$a = sql_query("SELECT * FROM betoptions WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$b = mysql_fetch_array($a);
$gameid = $b['gameid'];

if ($CURUSER["class"] < UC_MODERATOR&&!get_bet_moderators_is($gameid))
 stderr("ERROR","Bet Stuff - Nosey Cunt !");
 
if($gameid < 1){
header("location: bet_gamefinish.php");
exit;
}


$res3 = sql_query("SELECT * FROM betgames WHERE id =".sqlesc($gameid)." AND fix = 0") or sqlerr(__FILE__, __LINE__);
$o = @mysql_fetch_array($res3);
$c = sql_query("SELECT * FROM bets WHERE optionid =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$creatbouns=$totalstats = 0;

 if(!$_GET['sure'])stderr("确认结盘结果", "认为 <b>{$o['heading']}</b> 盘面正确选项为 <b>{$b['text']}</b><br /><br /><a href='bet_gamefinish2.php?id=".$id."&amp;sure=1'><u>点击确认并结算盘面</u></a>",false);

if(@mysql_num_rows($res3) == 1)
{
sql_query("UPDATE betgames SET fix = 1 WHERE id =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE betoptions SET thisright = 1 WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
}
else
stderr('ERROR','已经结算过?');

while($d = mysql_fetch_array($c))
{
$dividend = $d['bonus']+round(($d['bonus']*($b['odds']-1))*0.90,0);
if(mysql_num_rows(sql_query("SELECT * FROM bettop WHERE userid =".sqlesc($d["userid"])."")) == 0){
sql_query("INSERT INTO bettop(userid, bonus,winbonus,winnum,allbouns) VALUES(".sqlesc($d["userid"]).", ".sqlesc($dividend-$d["bonus"]).", ".sqlesc($dividend-$d["bonus"]).",1, ".sqlesc($d["bonus"]).")") or sqlerr(__FILE__, __LINE__);
}
else{
sql_query("UPDATE bettop SET winnum = winnum + 1 ,bonus = bonus + ".sqlesc($dividend -$d["bonus"])." ,winbonus = winbonus + ".sqlesc($dividend -$d["bonus"]).",allbouns = allbouns + ".sqlesc($d["bonus"])." WHERE userid =".sqlesc($d["userid"])."") or sqlerr(__FILE__, __LINE__);
}

$totalstats += $d['bonus']*$b['odds'];
$dividend = $d['bonus']+round(($d['bonus']*($b['odds']-1))*0.90,0);
$subjectwin = "博彩赢盘! (+".$dividend.")";
$msg = "魔力值奖励 +".$dividend;
$msg2 = <<<EOD
本次盘面你获得了{$dividend}魔力值!
你在 [i]{$o['heading']}[/i] 盘面上将 {$d['bonus']} 魔力值押注在 [i]{$b['text']}[/i] 选项上, 赔率为 {$b['odds']} !

论坛相关帖子:{$forumlink}

EOD;
$bonuscomment = sqlesc(date("Y-m-d") . " - BET SYSTEM - 押注 ".($d['bonus']). " 魔力值并获得了 " .($dividend)." 魔力值". " \n" .get_single_value('users', 'bonuscomment', "where id = ".sqlesc($d["userid"])));
sql_query("UPDATE users set bonuscomment=$bonuscomment , seedbonus = seedbonus + ".sqlesc($dividend)." WHERE id = ".sqlesc($d["userid"])."") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO betlog(userid,msg,date,bonus,betid) VALUES(".sqlesc($d["userid"]).", ".sqlesc($msg).", '$date', ".sqlesc($dividend).", ".sqlesc($o['id']).")") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0,$d[userid], ".sqlesc($msg2).",'$date', ".sqlesc($subjectwin).")") or sqlerr(__FILE__, __LINE__);
//$totalstats += $dividend;
}

$betonwer=$o["creator"];

$s = mysql_fetch_array(sql_query("SELECT COUNT(*) from bets where gameid =".sqlesc($gameid)."")) or sqlerr(__FILE__, __LINE__);
$body = "[b]".($o['heading'])."[/b] - [i]".($o['undertext'])."[/i]\n\n";
$body.= "下注总数 : [b] ".htmlspecialchars($s[0])." [/b]\n";
$body.= "投注总额 : [b] ".htmlspecialchars($totalstats)." points[/b]\n";
$body.= "获胜选项 : [b] ".($b['text'])." [/b]\n";
$body.= "开盘黑手 : [b] [uid".($o['creator'])."] [/b]\n";
$body.= "开盘时间 : [b] ".htmlspecialchars(date("Y-m-d H:i",$o['startime']))." [/b]\n";
$body.= "结盘黑手 : [b] [uid".($CURUSER['id'])."] [/b]\n";
$body.= "封盘时间 : [b] ".htmlspecialchars(date("Y-m-d H:i",$o['endtime']))." [/b]\n";
$body.= "结盘时间 : [b] ".htmlspecialchars(date("Y-m-d H:i"))." [/b]\n";





if($s[0]){
$creatbouns=min(50000,(round($totalstats*0.08,0)+50*$s[0]));
$bonuscomment = sqlesc(date("Y-m-d") . " - BET SYSTEM - 开盘 ".($o['heading']). " 并获得了 " .($creatbouns)." 魔力值". " \n" .get_single_value('users', 'bonuscomment', "where id = ".sqlesc($o["creator"])));
sql_query("UPDATE users set bonuscomment=$bonuscomment , seedbonus = seedbonus + ".sqlesc($creatbouns)." WHERE id = ".sqlesc($o["creator"])."") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0,".sqlesc($o["creator"]).", ".sqlesc("开盘 ".($o['heading']). " 成功,获得了 " .($creatbouns)." 魔力值").",'$date', ".sqlesc("博彩结盘! (+".$creatbouns.")").")") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO betlog(userid,msg,date,bonus,betid) VALUES(".sqlesc($o["creator"]).", ".sqlesc('庄家收入*2').", '$date', ".sqlesc($creatbouns*2).", ".sqlesc($o['id']).")") or sqlerr(__FILE__, __LINE__);
$body.= "庄家收入 : [b] $creatbouns [/b]\n\n";
}


$body.= "[b]选项与倍率 :[/b]\n";

$res = sql_query("SELECT * FROM betgames WHERE id =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_array($res);

if($a['sort']==0)
$sort = "odds DESC";
elseif($a['sort']==1)
$sort = "id ASC";
$res2 = sql_query("SELECT * from betoptions where gameid =".sqlesc($a["id"])." ORDER BY $sort") or sqlerr(__FILE__, __LINE__);
while($b = mysql_fetch_array($res2)){
$body.= " ".($b['text'])." X[b]".($b['odds'])."[/b]\n";
}

$m = sql_query("SELECT users.username, users.id, bets.userid, bets.bonus FROM bets INNER JOIN users on bets.userid = users.id WHERE optionid =".sqlesc($id)." and gameid =".sqlesc($gameid)." order by bonus DESC LIMIT 20") or sqlerr(__FILE__, __LINE__);
$body.= "\n[b]Top 20 赌神:[/b]\n";
$odds = mysql_fetch_array(sql_query("SELECT * FROM betoptions WHERE id =".sqlesc($id)."")) or sqlerr(__FILE__, __LINE__);

while($k = mysql_fetch_array($m)){
$body .= "[b]+".($k['bonus']+round($k['bonus']*($odds['odds']-1)*0.90,0))." 魔力值[/b],用户 [uid".$k['id']."] 押注 ".htmlspecialchars($k['bonus'])." 魔力值 \n";
}

$m = sql_query("SELECT users.username, users.id, bets.userid, bets.bonus FROM bets INNER JOIN users on bets.userid = users.id WHERE optionid <> $id and gameid = $gameid order by bonus DESC LIMIT 20") or sqlerr(__FILE__, __LINE__);
$body.= "\n[b]Top 20 好人:[/b]\n";
while($k = mysql_fetch_array($m)){
$body .= "[b]-".htmlspecialchars($k['bonus'])." 魔力值[/b],用户 [uid".$k['id']."] \n";
}

$body = sqlesc($body);

//if($totalstats)
auto_bet(sqlesc('竞猜结果公示 - '.date("Y-m-d H:i",$o['endtime']).' : \''.($o['heading']).'\''), $body,$gameid);
$c = sql_query("SELECT * FROM bets WHERE optionid <> $id AND gameid =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
while($a = mysql_fetch_array($c))
{
if(mysql_num_rows(sql_query("SELECT * FROM bettop WHERE userid =".sqlesc($a["userid"])."")) == 0){
sql_query("INSERT INTO bettop(userid, bonus ,lossbonus,lossnum,allbouns) VALUES(".sqlesc($a["userid"]).", -".sqlesc($a["bonus"]).",".sqlesc($a["bonus"]).",1,".sqlesc($a["bonus"]).")") or sqlerr(__FILE__, __LINE__);
}
else{
sql_query("UPDATE bettop SET lossnum=lossnum+1,bonus = bonus - ".sqlesc($a["bonus"]).",lossbonus = lossbonus + ".sqlesc($a["bonus"]).",allbouns = allbouns + ".sqlesc($a["bonus"])." WHERE userid =".sqlesc($a["userid"])."") or sqlerr(__FILE__, __LINE__);
}
$k = mysql_fetch_array(sql_query("SELECT * from betgames where id =".sqlesc($gameid)."")) or sqlerr(__FILE__, __LINE__);
$msg2 = <<<EOD
很不幸的是,你押注在[i]{$k['heading']}[/i]上面的{$a["bonus"]}魔力值被系统收回!您的宝贵积分,我们一定好好保管
更多竞猜,等你来战
论坛相关帖子 : {$forumlink}
EOD;
$subjectloss = "博彩输盘! (-".$a["bonus"].")";

$bonuscomment = sqlesc(date("Y-m-d") . " - BET SYSTEM - 失去了押注的 ".($a["bonus"]). " 魔力值". " \n" .get_single_value('users', 'bonuscomment', "where id = ".sqlesc($a["userid"])));
sql_query("UPDATE users set bonuscomment=$bonuscomment WHERE id = ".sqlesc($a["userid"])."") or sqlerr(__FILE__, __LINE__);

sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0, ".sqlesc($a["userid"]).", ".sqlesc($msg2).", '$date', ".sqlesc($subjectloss).")") or sqlerr(__FILE__, __LINE__);
}

//sql_query("DELETE FROM betgames WHERE id =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
//sql_query("DELETE FROM betoptions WHERE gameid =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
///sql_query("DELETE FROM bets WHERE gameid =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
header("location: bet_gamefinish.php");
?>