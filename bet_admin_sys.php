<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

$HTMLOUT ="";

if ($CURUSER['class'] < UC_SYSOP)
    stderr("管理员管理 - Nosey Cunt !");
	
$Cache->delete_value('moderators_content');

if ($_POST['beterator'])set_bet_moderators($_POST['beterator']);

$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>管理员管理</h1>
<br />";


$HTMLOUT .="<form method='post' action='?'>
<table align='center' cellpadding='5'>
<tr><td>管理员</td><td>
<input name='beterator' type='text' style='width: 400px'  value='".get_bet_moderators()."'></td></tr>
<tr><td align='center' colspan='2'>
<input type='submit' value='确认'></td></tr>
</table></form>";

stdhead('Betting');print  $HTMLOUT ; stdfoot();
?>