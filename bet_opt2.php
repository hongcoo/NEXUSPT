<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

$HTMLOUT ="";

$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>Admin</h1>
<table class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet_admin.php'>添加竞猜</a></td>
<td align='center' class='navigation'><a href='./bet_gamefinish.php'>结束竞猜</a></td>
</tr>
</table>
<br />";

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

if ($CURUSER["class"] < UC_MODERATOR&&!get_bet_moderators_is($id))
stderr("ERROR","Db Stuff - Nosey Cunt !");

$a = sql_query("SELECT * FROM betgames where id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$b = mysql_fetch_array($a);

$HTMLOUT .="<script type=\"text/javascript\" src=\"javascript/DatePicker/WdatePicker.js\"></script><form method='post' action='bet_takeedit.php'>
<table cellpadding='5'>
<tr>
<td><input name='id' type='hidden' value='".htmlspecialchars($id)."' />
标题</td><td><input type='text' name='heading' size='50' value='".htmlspecialchars($b['1'])."' />
</td>
</tr>
<tr>
<td>条件</td><td><input type='text' name='undertext' size='50' value='".htmlspecialchars($b['2'])."' /></td>
</tr>
<tr>
<td>结束时间</td><td><input type='text' name='endtime' size='50' class=\"Wdate\" value='".date("Y-m-d H:i:s",($b['3']))."' /></td>
     </tr><tr>
<td>选项排序:</td> <td><input type='radio' name='sort' value='1' />
添加顺序<input type='radio' name='sort' value='0' checked='checked' /> 
倍率大小</td></tr>
</table><br />
<input type='submit' ".($b['fix']?'disabled':'')." value='Save Changes' />
</form>
<br /><br />".(($CURUSER['class'] > UC_MODERATOR||get_bet_moderators_is())?"
<a href='bet_delgame.php?id=".$b['0']."'><u>点击</u></a> 删除这个盘面.
<br /><br />
<a href='bet_nullbet.php?id=".$b['0']."'><u>点击</u></a> 删除这个盘面并返还魔力值":"");
stdhead('Betting');print  $HTMLOUT ; stdfoot();
?>