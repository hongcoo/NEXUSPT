<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

$HTMLOUT ="";

$ordernum = isset($_GET['a']) && is_valid_id($_GET['a']) ? $_GET['a'] : 0;

switch ($ordernum){
case 1: $order = 'order by bonus desc';break;
case 2: $order = 'order by bonus asc';;break;
case 3: $order = 'order by winbonus desc';break;
case 4: $order = 'order by winbonus asc';;break;
case 5: $order = 'order by lossbonus desc';break;
case 6: $order = 'order by lossbonus asc';;break;
case 7: $order = 'order by winnum desc';break;
case 8: $order = 'order by winnum asc';;break;
case 9: $order = 'order by lossnum desc';break;
case 10: $order = 'order by lossnum asc';;break;
case 11: $order = 'order by per desc';break;
case 12: $order = 'order by per asc';;break;
case 13: $order = 'order by allbouns desc';break;
case 14: $order = 'order by allbouns asc';;break;
default:$order = 'order by allbouns desc';;break;
}


$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>博彩频道</h1>
<table class='main' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>当前竞猜</a></td>
<td align='center' class='navigation'><a href='/bet_history.php'>历史竞猜</a></td>
<td align='center' class='navigation'><a href='./bet_coupon.php'>我的押注</a></td>
<td align='center' class='navigation'><a href='./bet_bonustop.php' class='faqlink'>用户排名</a></td>
<td align='center' class='navigation'><a href='./bet_info.php'>系统帮助</a></td>";
if( $CURUSER['class'] >= UC_POWER_USER)//if( $CURUSER['class'] >= UC_MODERATOR ||get_bet_moderators_is())
{
$HTMLOUT .= "<td align='center' class='navigation'><a href='/bet_admin.php'>管理竞猜</a></td>";
}
$HTMLOUT .="</tr></table><br />";


$res = sql_query("SELECT *,(winnum/lossnum) as per FROM bettop WHERE userid = ".sqlesc($CURUSER['id'])."") or sqlerr(__FILE__, __LINE__);

while($arr = mysql_fetch_assoc($res))
{
$HTMLOUT .="<table border='1' cellspacing='0' cellpadding='5'>\n";
    $HTMLOUT .="<tr><td class='colhead' align='left'>用户名</td>
	<td class='colhead' align='left'>积分变化</td>
	<td class='colhead' align='left'>赢盘积分</td>
	<td class='colhead' align='left'>输盘积分</td>
	<td class='colhead' align='left'>赢盘次数</td>
	<td class='colhead' align='left'>输盘次数</td>
	<td class='colhead' align='left'>获胜比例</td>
	<td class='colhead' align='left'>总投入</td>
	</tr>\n";
$HTMLOUT .="<tr><td>".get_username($arr["userid"])."</td>
	<td align='right'><b>".htmlspecialchars($arr["bonus"])." Points</b></td>
	<td align='right'><b>".htmlspecialchars($arr["winbonus"])." Points</b></td>
	<td align='right'><b>".htmlspecialchars($arr["lossbonus"])." Points</b></td>
	<td align='right'><b>".htmlspecialchars($arr["winnum"])." </b></td>
	<td align='right'><b>".htmlspecialchars($arr["lossnum"])." </b></td>
	<td align='right'><b>".htmlspecialchars($arr["lossnum"]>0?$arr["per"]:"Inf.")." </b></td>
	<td align='right'><b>".htmlspecialchars($arr["allbouns"])." </b></td>
	</tr></table>\n";
}

  $number = 0;
  $res = sql_query("SELECT bettop.*,((winnum+0.000001)/(lossnum+0.000001))  as per FROM bettop where allbouns >1000 $order limit 50") or sqlerr(__FILE__, __LINE__);
  $HTMLOUT.="<h1>用户排名(总投入大于1000魔力才会被计入排行榜)</h1>\n";


    $HTMLOUT .="<table border='1' cellspacing='0' cellpadding='5'>\n";
    $HTMLOUT .="<tr><td class='colhead' align='left'>排名</td><td class='colhead' align='left'>用户名</td>
	<td class='colhead' align='left'><a href='bet_bonustop.php?a=".($ordernum==1?'2':'1')."' class='faqlink'>积分变化</a></td>
	<td class='colhead' align='left'><a href='bet_bonustop.php?a=".($ordernum==3?'4':'3')."' class='faqlink'>赢盘积分</a></td>
	<td class='colhead' align='left'><a href='bet_bonustop.php?a=".($ordernum==5?'6':'5')."' class='faqlink'>输盘积分</a></td>
	<td class='colhead' align='left'><a href='bet_bonustop.php?a=".($ordernum==7?'8':'7')."' class='faqlink'>赢盘次数</a></td>
	<td class='colhead' align='left'><a href='bet_bonustop.php?a=".($ordernum==9?'10':'9')."' class='faqlink'>输盘次数</a></td>
	<td class='colhead' align='left'><a href='bet_bonustop.php?a=".($ordernum==11?'12':'11')."' class='faqlink'>获胜比例</a></td>
	<td class='colhead' align='left'><a href='bet_bonustop.php?a=".($ordernum==13?'14':'13')."' class='faqlink'>总投入</a></td>
	</tr>\n";
    while ($arr = mysql_fetch_assoc($res))
    {
    $number++;
    $HTMLOUT .="<tr><td>#".htmlspecialchars($number)."</td><td>".get_username($arr["userid"])."</td>
	<td align='right'><b>".htmlspecialchars($arr["bonus"])." Points</b></td>
	<td align='right'><b>".htmlspecialchars($arr["winbonus"])." Points</b></td>
	<td align='right'><b>".htmlspecialchars($arr["lossbonus"])." Points</b></td>
	<td align='center'><b>".htmlspecialchars($arr["winnum"])." </b></td>
	<td align='center'><b>".htmlspecialchars($arr["lossnum"])." </b></td>
	<td align='center'><b>".htmlspecialchars($arr["lossnum"]>0?number_format($arr["per"],2,'.',''):"Inf.")." </b></td>
	<td align='center'><b>".htmlspecialchars($arr["allbouns"])." </b></td>
	</tr>\n";
    }
    $HTMLOUT .="</table>";
  
stdhead('Betting');print  $HTMLOUT ; stdfoot();
?>