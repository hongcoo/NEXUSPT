<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

if ($CURUSER < UC_MODERATOR&&!get_bet_moderators_is())
      stderr("Error","Permission denied.");

$res = sql_query("SELECT * FROM bets where gameid =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) > 0&&!get_bet_moderators_is())
{
stderr("错误", "已有押注,若一定要删除,请联系管理员");
}
else
{
if(!$creator=get_single_value('betgames','creator',"where id=".sqlesc($id)))stderr("ERROR", "盘面不存在");

if($creator!=$CURUSER['id']){
if(!$_POST['reason'])stderr("请输入删除理由(会短信发给庄家)", "<form method='post' action='bet_delgame.php?id=$id'>
<input type='text' name='reason' size='60'/>
<input type='submit' value='删除' />
</form>
",false);

sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0,$creator, ".sqlesc("十分抱歉,您所提交的竞猜 [b]".get_single_value('betgames','heading',"where id=".sqlesc($id))."[/b] 未能通过审核,已被管理员 [uid{$CURUSER['id']}] 删除并返还开盘费.\n被删除原因: ".$_POST['reason']).",".sqlesc(date("Y-m-d H:i:s")).", ".sqlesc('你的盘面被拒绝').")") or sqlerr(__FILE__, __LINE__);
}

KPS("+",1000,$creator);
sql_query("DELETE FROM betgames WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM betoptions WHERE gameid =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM bets WHERE gameid =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
header("location: bet_admin.php");

}
?>