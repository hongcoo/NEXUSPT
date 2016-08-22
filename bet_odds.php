<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

$HTMLOUT ="";

if ($CURUSER["class"] < UC_POWER_USER)
{
stderr("Sorry", "You have to Be A Power User to use Bet.");
}

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;


if(get_bet_moderators_is($id))stderr('错误','庄家禁止押注');

$b = @mysql_fetch_assoc(sql_query("SELECT * from betgames where id =".sqlesc($id).""));

if($b['active'] == 0){
header("location: bet.php");
exit;
}

if($b['sort']==0)
$sort = " order by odds desc";
elseif($b['sort']==1)
$sort = " order by id ASC";
$res = sql_query("SELECT * FROM betoptions WHERE gameid =".sqlesc($id)."$sort") or sqlerr(__FILE__, __LINE__);

$res22 = sql_query("SELECT * FROM bets WHERE gameid =".sqlesc($id)."  and userid =".$CURUSER['id']);
while($aa = mysql_fetch_assoc($res22))$havebet[$aa['optionid']]='已有押金:'.$aa['bonus'].'';

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
$HTMLOUT .="
</tr>
</table>
<h1>{$b['heading']}</h1>
<p>{$b['undertext']}
<table cellpadding='5'>
<tr>
<td class='colhead'>选项</td>
<td class='colhead'>倍率</td>
<td class='colhead' align='center'>押注</td>
<td class='colhead' align='center'>已押注</td>
</tr>";

while($a = mysql_fetch_assoc($res)){
$HTMLOUT .="<tr>
<td>{$a['text']}</td>
<td>{$a['odds']}</td>";

if(mysql_num_rows($res22)){
if($havebet[$a['id']])
$HTMLOUT .="<td><form method='post' action='bet_odds2.php'>
<input type='text' name='bonus' size='6' maxlength='6' />  <b> 魔力值</b>
<input type='hidden' name='id' value='".htmlspecialchars($a['id'])."' /><input type='submit' value='追加' /></form></td>";
else $HTMLOUT .="<td align='center'>----</td>";
}
else
$HTMLOUT .="<td><form method='post' action='bet_odds2.php'>
<input type='text' name='bonus' size='6' maxlength='6' />  <b> 魔力值</b>
<input type='hidden' name='id' value='".htmlspecialchars($a['id'])."' /><input type='submit' value='押注' /></form></td>";

 

$HTMLOUT .="<td align='center'>--{$havebet[$a['id']]}--</td></tr>";
}

$HTMLOUT .="</table>";
if(isset($_GET['err']))
{
if($_GET['err'] == "b")
$HTMLOUT .="<font color='#ff0000'>魔力值不足</font>";
elseif($_GET['err'] == "c")
$HTMLOUT .="<font color='#ff0000'>下注数出错</font>";
elseif($_GET['err'] == "d")
$HTMLOUT .="<font color='#ff0000'>最高下注金额1W</font>.";
}
stdhead('Betting');print  $HTMLOUT ; stdfoot(); 
?>