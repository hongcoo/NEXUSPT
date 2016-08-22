<?php
require "include/bittorrent.php";
 
 
dbconn(false);
loggedinorreturn();

 

if ($CURUSER['class']< UC_MODERATOR&&!get_bet_moderators_is())
      stderr("Error","Permission denied.");

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

$res = sql_query("SELECT active FROM betgames WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$res = mysql_fetch_assoc($res);

if($res['active'] == 0)

$status = 1;
else
$status = 0;

if(isset($res['finished']) == 1)
$status = 0;
sql_query("UPDATE betgames SET startime = ".TIMENOW." ,active =".sqlesc($status)." WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
shoutbox_into('[bid'.$id.']');
header("location: bet_admin.php");
?>