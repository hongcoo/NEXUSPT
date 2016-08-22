<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

$bid = isset($_GET['b']) && is_valid_id($_GET['b']) ? $_GET['b'] : 0;

if ($CURUSER['class'] < UC_MODERATOR&&!get_bet_moderators_is($bid))
      stderr("Error","Permission denied.");

$res= sql_query("SELECT * FROM bets where optionid =".sqlesc($id)."")or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) > 0)
{
stderr("ERROR", "You are trying to delete an option that people bet on. Contact the coders.");
}
else
{
sql_query("DELETE FROM betoptions WHERE gameid=".sqlesc($bid)."  and id =".sqlesc($id)."")or sqlerr(__FILE__, __LINE__);

header("location: bet_opt.php?id=".$bid."");
}

?>