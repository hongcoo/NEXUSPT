<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

$tid = time();
sql_query("UPDATE betgames set active = 0 WHERE endtime < $tid") or sqlerr(__FILE__, __LINE__);

$HTMLOUT ="";

if ($CURUSER['class'] < UC_MODERATOR&&!get_bet_moderators_is())
    $where=' where creator='.sqlesc($CURUSER['id']);//stderr("竞猜管理 - Nosey Cunt !");

$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>Admin</h1>
<table align='center' class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet_admin.php' class='faqlink'>添加竞猜</a></td>
<td align='center' class='navigation'><a href='./bet_gamefinish.php'>结算竞猜</a></td>
</tr></table><br />";


$HTMLOUT .="<script type=\"text/javascript\" src=\"javascript/DatePicker/WdatePicker.js\"></script><form method='post' action='bet_takenew.php'>
<table align='center' cellpadding='5'>
<tr><td>标题</td><td><input type='text' name='heading' size='50' /></td></tr>
<tr><td>条件</td><td><input type='text' name='undertext' size='50' value='如题' /></td></tr>
<tr>
<tr><td>选项(,)</td><td><input type='text' name='text' size='50' value='中国,平局' /></td></tr>
<tr>
<td>结束时间</td><td><input type='text' name='endtime' size='50' class=\"Wdate\" value='".date('Y-m-d H:i:s')."' />
     </td>
     </tr>

<tr>
<td>选项排序:</td><td>
<input type='radio' name='sort' value='1' />
添加顺序<input type='radio' name='sort' value='0' checked='checked' />
倍率大小</td></tr>
<tr><td colspan='2' align='center'>
<input  type='submit' value='添加' />
</td></tr></table></form>";

$HTMLOUT .="<br /><br />
<table align='center' cellpadding='5'>
<tr>
<td><b>庄家</b></td>
<!--<td><b>开始时间</b></td>-->
<td><b>结束时间</b></td>
<td><b>标题</b></td>
<td><b>条件</b></td>
<td><b>状态选项</b></td>
<td><b>添加选项</b></td>
<td><b>编辑选项</b></td>
</tr>";
 
list($pagertop, $pagerbottom, $limit) = pager(50, get_row_count('betgames',$where), "?");
$a = sql_query("SELECT *, endtime as end FROM betgames $where order by fix,active,endtime desc $limit") or sqlerr(__FILE__, __LINE__);
while($b = mysql_fetch_array($a))
{
$HTMLOUT .="<tr><td align='left'>".get_username($b['6'])."</td>";
//$HTMLOUT .="<td align='left'><i>".htmlspecialchars(date('Y-m-d H:i:s',$b['9']))."</i></td>";
if (time() > $b["end"])
$HTMLOUT .="<td align='left'><i>".htmlspecialchars(date('Y-m-d H:i:s',$b['3']))."</i></td>";
else
$HTMLOUT .="<td align='left'>".htmlspecialchars(date('Y-m-d H:i:s',$b['3']))."</td>";
$HTMLOUT .="<td align='left'>".htmlspecialchars($b['1'])."</td>";
$HTMLOUT .="<td align='left'><i>".htmlspecialchars($b['undertext'])."</i></td>";
if ($b["fix"])
$HTMLOUT.="<td align='center'><i>已结束</i></td>";
elseif (time() > $b["end"])
$HTMLOUT.="<td align='center'>待结算</td>";
elseif(!$where)
$HTMLOUT .="<td align='center'><a href='./bet_active.php?id=".$b['0']."'><u>".($b['active']?'进行中</u></a>':'<b>激活</b></u></a>/<a href=\'./bet_delgame.php?id='.$b['0'].'\'>拒绝</a>')."</td>";
else
$HTMLOUT .="<td align='center'>".($b['active']?'进行中':'<b>待审核</b>')."</td>";


$HTMLOUT .="<td align='center'><a href='./bet_opt.php?id=".$b['0']."'>添加</a></td>";
$HTMLOUT .="<td align='center'><a href='./bet_opt2.php?id=".$b['0']."'>编辑</a></td></tr>";

}
$HTMLOUT .="</table>$pagerbottom<br /><br />\n";


stdhead('Betting');print  $HTMLOUT ; stdfoot();
?>