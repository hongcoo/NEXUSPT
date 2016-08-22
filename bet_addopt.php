<?php
require "include/bittorrent.php";
 
 
dbconn(false);
loggedinorreturn();

 

if (empty($_POST['opt'])){
stderr('Silly Rabbit', 'Twix are for kids.. Enter at least one option ya nugget !');
}

$id = isset($_POST['id']) && is_valid_id($_POST['id']) ? $_POST['id'] : 0;

$text = htmlspecialchars($_POST['opt'], ENT_QUOTES);

if ($CURUSER['class'] < UC_MODERATOR&&!get_bet_moderators_is($id))
      stderr("Error","Permission denied.");

sql_query("INSERT INTO betoptions(gameid,text) VALUES(".sqlesc($id).",".sqlesc($text).")") or sqlerr(__FILE__, __LINE__);
header("location: bet_opt.php?id=".$id."");
?>