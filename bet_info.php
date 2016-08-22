<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

$HTMLOUT ="";

$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>博彩频道</h1>
<table class='main' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>当前竞猜</a></td>
<td align='center' class='navigation'><a href='/bet_history.php'>历史竞猜</a></td>
<td align='center' class='navigation'><a href='./bet_coupon.php'>我的押注</a></td>
<td align='center' class='navigation'><a href='./bet_bonustop.php'>用户排名</font></a></td>
<td align='center' class='navigation'><a href='./bet_info.php' class='faqlink'>系统帮助</a></td>";
if( $CURUSER['class'] >= UC_POWER_USER)//if( $CURUSER['class'] >= UC_MODERATOR ||get_bet_moderators_is() )
{
$HTMLOUT .= "<td align='center' class='navigation'><a href='/bet_admin.php'>管理竞猜</a></td>";
}
$HTMLOUT .="
</tr>
</table>
<br />
<table class='main' width='500' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
<table width='100%' border='1' cellspacing='0' cellpadding='10'>
<tr><td class='text'>
<b>博彩说明</b><br />
<br />
博彩系统是一个通过使用魔力值竞猜比赛结果的娱乐平台。<br />
即使你之前没有接触过类似游戏，也很容易上手。<br />
<br />
这个系统仅使用魔力值进行竞猜。<br />
<br />
当你的下注正确时，你会得到你下注的魔力值乘上该选择的赔率后的魔力值。并且下注后不可以更改。<br />
赔率是动态变化的(本盘总额/本项总额)，会根据所我的下注进行变化。<br />
当然你所可能获得的魔力值也会随之变化。<br />
比赛开始，当前盘面关闭，比赛结束后有关部门会进行奖励,赶紧来到博彩系统试试你得手气吧~!!.<br />
<br />
为了防止魔力值的膨胀，系统会扣除你赢取魔力值利润的10%作为所得税。<br />
</td></tr></table>
<table width='100%' border='1' cellspacing='0' cellpadding='10'>
<tr><td class='text'>

一年生上学期以上用户可以花费1000魔力值开盘<br />竞猜至少在比赛开始前15分钟结束,竞猜时间至少持续1天<br>开盘需经过审核,删除请<a class='faqlink' href='contactstaff.php'>PM管理组</a>或志愿者(未开盘的可返还魔力值)<br />结盘后,庄家获得盘面上7%的利润(最高50000),并且每注还额外获得100魔力值<br />庄家不可押注自己的盘面<br />其他问题或建议意见请到相应板块发帖<br /><br />
</td></tr></table>
<table width='100%' border='1' cellspacing='0' cellpadding='10'>
<tr><td class='text'>
<b>当前管理志愿者:</b><br /><br />
".get_bet_moderators(true)."
</td></tr></table>
</td></tr></table>";
//
stdhead('Betting');print  $HTMLOUT ; stdfoot();
?>