<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

$HTMLOUT ="";


$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

if ($CURUSER["class"] < UC_MODERATOR&&!get_bet_moderators_is($id))
    stderr("ERROR","Bet Stuff - Nosey Cunt !");

$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>Admin</h1>
<table class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet_admin.php'>添加竞猜</a></td>
<td align='center' class='navigation'><a href='./bet_gamefinish.php'>结束竞猜</a></td>
</tr>
</table>
<br />";

$HTMLOUT .="<h2>选项管理</h2><table cellpadding='5'>";

$a = sql_query("SELECT * FROM betgames WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
while($b = mysql_fetch_array($a))
{

$HTMLOUT .="<tr><td>".htmlspecialchars($b['1'])."</td>";
$HTMLOUT .="<td><i>".htmlspecialchars($b['undertext'])."</i></td>";
$HTMLOUT .="</tr>";

}
$HTMLOUT .="</table><br />";
  $res = sql_query("SELECT id, gameid, text FROM betoptions WHERE gameid =".sqlesc($id)." ORDER BY id asc") or sqlerr(__FILE__, __LINE__);
    $HTMLOUT .="<table border='1' cellspacing='0' cellpadding='5'>\n";
    $HTMLOUT .="<tr>
    <td colspan='2' class='colhead' align='left'>已有选项</td></tr>\n";
    while ($arr = mysql_fetch_array($res))
    {
     $HTMLOUT .="<tr><td>".htmlspecialchars($arr['text'])."</td><td><a href='./bet_delopt.php?id=$arr[id]&amp;b=$id'>删除</a></td></tr>\n";
    }
    $HTMLOUT .="</table><br /><br/>";


$HTMLOUT .="<form action='bet_addopt.php' method='post'>
选项: <input type='text' size='10' name='opt' />
<input type='hidden' name='id' value='".htmlspecialchars($id)."' />
<input type='submit' value='添加' />
</form>
<br /><br />";

stdhead('Betting');print  $HTMLOUT ; stdfoot();
?>