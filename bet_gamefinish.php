<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 


if ($CURUSER["class"] < UC_MODERATOR&&!get_bet_moderators_is())
    $where=' creator='.sqlesc($CURUSER['id']).' and ';//stderr("Bet Stuff - Nosey Cunt !");

$HTMLOUT ="";

$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>Admin</h1>
<table class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet_admin.php'>添加竞猜</a></td>
<td align='center' class='navigation'><a href='./bet_gamefinish.php' class='faqlink'>结束竞猜</a></td>
</tr>
</table>
<br />";

$HTMLOUT .="<h1>! Warning !<br /> 点击获胜选项将发放奖金 !<br />! Warning !</h1>";
$HTMLOUT .="<table><tr><td class='colhead'>标题</td><td class='colhead'>正确结果</td></tr>";
$end = time();
$active = sql_query("SELECT * FROM betgames where $where active = 0 and fix=0 AND endtime <".sqlesc($end)." order by endtime") or sqlerr(__FILE__, __LINE__);
while($active1 = mysql_fetch_assoc($active))
{
$HTMLOUT .="<tr><td><b>".htmlspecialchars($active1['heading'])."</b>的结果是</td><td align='center'>";

$a = sql_query("SELECT * FROM betoptions where gameid =".sqlesc($active1["id"])." ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);
while($b = mysql_fetch_assoc($a))
{
$HTMLOUT .="<a href='bet_gamefinish2.php?id=".$b['id']."'><b>".htmlspecialchars($b['text'])."</b></a>  | ";
}
$HTMLOUT .='<a href="bet_nullbet.php?id='.$active1["id"].'"><b>无结果</b></a></td></tr><tr><td colspan="2"></td></tr>';
}
$HTMLOUT .='</table>';
stdhead('Betting');print  $HTMLOUT ; stdfoot();
?>