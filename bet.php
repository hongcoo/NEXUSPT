<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

$tid = time();
sql_query("UPDATE betgames set active = 0 WHERE endtime < $tid") or sqlerr(__FILE__, __LINE__);

$HTMLOUT ="";

$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>博彩频道</h1>
<table class='main' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='/bet.php' class='faqlink'>当前竞猜</a></td>
<td align='center' class='navigation'><a href='/bet_history.php'>历史竞猜</a></td>
<td align='center' class='navigation'><a href='/bet_coupon.php'>我的押注</a></td>
<td align='center' class='navigation'><a href='/bet_bonustop.php'>用户排名</a></td>
<td align='center' class='navigation'><a href='/bet_info.php'>系统帮助</a></td>";
if( $CURUSER['class'] >= UC_POWER_USER)//if( $CURUSER['class'] >= UC_MODERATOR || get_bet_moderators_is() )
{
$HTMLOUT .= "<td align='center' class='navigation'><a href='/bet_admin.php'>管理竞猜</a></td>";
}
$HTMLOUT .="</tr></table><br />";


$aa = sql_query("SELECT bets.gameid,bonus,thisright,text FROM bets LEFT JOIN betoptions ON bets.optionid=betoptions.id  WHERE userid = ".sqlesc($CURUSER["id"])."") ;
while($bb = mysql_fetch_array($aa)){
$mydata[$bb['gameid']]['bonus'] = $bb['bonus'];
$mydata[$bb['gameid']]['thisright'] = $bb['thisright'];
$mydata[$bb['gameid']]['text'] = $bb['text'];
}

 


list($pagertop, $pagerbottom, $limit) = pager(50, get_row_count('betgames','where fix = 0 '), "?");


$res = sql_query("SELECT  betgames.*,count(*) AS tnum,SUM(bonus) AS bsum FROM  betgames LEFT JOIN bets ON gameid = betgames.id where fix = 0 GROUP BY betgames.id ORDER BY endtime ASC $limit") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) == 0)
{
$HTMLOUT .= "<i>暂无项目</i>";
}else{
$HTMLOUT .= "<table width='40%' cellpadding='5'><tr><td class='colhead'>竞猜主题</td>
<td class='colhead'>结束时间</td>
<td class='colhead'>庄家</td>
<td class='colhead'>下注总数</td>
<td class='colhead'>投注总额</td>
<td class='colhead'>我的下注</td>
<td class='colhead'>状态</td><tr>";
while($a = mysql_fetch_assoc($res))
{
if($a["fix"]){
if($mydata[$a['id']]['thisright'])$thistype='博彩赢盘';
elseif($mydata[$a['id']]['bonus'])$thistype='博彩输盘';
else $thistype='已结盘';
}
elseif($a["endtime"]<time())$thistype='<i>等待结算</i>';
elseif(!$a["active"])$thistype='<i>等待激活</i>';
elseif($mydata[$a['id']]['bonus'])$thistype="<a href='/bet_odds.php?id=".$a["id"]."'>继续<b>押注</b></a>";
else $thistype="<a href='/bet_odds.php?id=".$a["id"]."'><b>点击押注</b></a>";

//$c=@mysql_fetch_assoc(sql_query("SELECT count(*) as num,SUM(bonus) as sum from bets where gameid =".sqlesc($a["id"]).""));
$HTMLOUT .= "<tr>
<td class='nowrap'><a href='./bet_gameinfo.php?showgames={$a["id"]}'><b>".htmlspecialchars($a["heading"])."</b></a></td>
<td class='nowrap'><b>". date('Y-m-d H:i:s', $a['endtime'])."</b>. 剩余: <b>".mkprettytime(($a['endtime']) - time())."</b></td>
<td class='nowrap' align='center'>".get_username($a["creator"])."</td>
<td class='nowrap' align='center'>".$a["tnum"]."</td>
<td class='nowrap' align='center'>".(0+$a["bsum"])."</td>
<td class='nowrap' align='center'>".($mydata[$a['id']]['bonus']?("<span  title='".$mydata[$a['id']]['text']."'>".(0+$mydata[$a['id']]['bonus']))."</span>":"-")."</td>
<td class='nowrap' align='center'>".$thistype."</td></tr>";

}
$HTMLOUT .="</table></br>".$pagerbottom;
$HTMLOUT .= "<h1><a href='/bet_gameinfo_all.php'>".htmlspecialchars(" < 全局统计情况 > ")."</a></h1>";
}

stdhead('Betting');print  $HTMLOUT ; stdfoot();
?>