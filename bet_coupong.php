<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

if ($CURUSER["class"] < UC_POWER_USER)
stderr("Error", "You must be at least Power Class to use the Betting System.");

$HTMLOUT ="";

$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>博彩频道</h1>
<table class='main' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>当前竞猜</a></td>
<td align='center' class='navigation'><a href='/bet_history.php'>历史竞猜</a></td>
<td align='center' class='navigation'><a href='./bet_coupon.php'>我的押注</a></td>
<td align='center' class='navigation'><a href='./bet_bonustop.php'>用户排名</font></a></td>
<td align='center' class='navigation'><a href='./bet_info.php'>系统帮助</a></td>";
if( $CURUSER['class'] >= UC_POWER_USER)//if( $CURUSER['class'] >= UC_MODERATOR ||get_bet_moderators_is() )
{
$HTMLOUT .= "<td align='center' class='navigation'><a href='/bet_admin.php'>管理竞猜</a></td>";
}
$HTMLOUT .="</tr></table><br />";

list($pagertop, $pagerbottom, $limit) = pager(50, get_row_count('bets','where gameid='.sqlesc(0+$_GET['id'])), "?id={$_GET['id']}&");

$main = sql_query("SELECT userid, optionid, heading, text, odds, bonus FROM bets LEFT JOIN betgames ON bets.gameid = betgames.id LEFT JOIN betoptions ON betoptions.gameid = betgames.id AND betoptions.id = bets.optionid WHERE betoptions.gameid = ".sqlesc(0+$_GET['id'])." $limit") or sqlerr(__FILE__, __LINE__);

$HTMLOUT .="<table cellpadding='5'>
<tr>
<td colspan='1' class='colhead'>用户名</td>
<td colspan='1' class='colhead'>押注选项</td>
<td colspan='1' class='colhead'>倍率</td>
<td colspan='1' class='colhead'>押注金额</td>
<td colspan='1' class='colhead'>可获金额</td>
</tr>";


if(mysql_num_rows($main) == 0)
{
$HTMLOUT .="<tr><td  colspan='5'><i>你还没有参加</i></tr>";
}

while($more = mysql_fetch_assoc($main))
{
$odds = $more['odds'];

switch(strlen($odds))
{
case 1:
$odds = $odds.".00";
break;
case 3:
$odds = $odds."0";
break;
}

$HTMLOUT .="<tr>
<td>".get_username($more['userid'])."</td>
<td>{$more['text']}</td>
<td>{$odds}</td>
<td class='noflagclear' align='right'>{$more['bonus']} Points</td>
<td class='noflagclear' align='right'><b>".($more['bonus']+round(($more['bonus']*($more['odds']-1))*0.90))." Points</b></td></tr>
";
}
$HTMLOUT .="</table>".$pagerbottom;
stdhead('Betting');print  $HTMLOUT ; stdfoot();
?>