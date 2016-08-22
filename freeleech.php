<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
if (get_user_class() < UC_ADMINISTRATOR)
	stderr("Error", "Permission denied.");

if ($_SERVER["REQUEST_METHOD"] == "POST"){
sql_query('DELETE  FROM `torrents_state` where secondtype NOT IN (SELECT id FROM audiocodecs) and secondtype!=9999');
$checksecondtypeid = $_POST['checksecondtypeid'];
$spstate=0+$_POST['spstate'];
if($_POST['globalpromotiontorrent']==1&&!$checksecondtypeid){
sql_query('INSERT INTO torrents_state(secondtype,global_sp_state) VALUES (9999,'.$spstate.') ON DUPLICATE KEY update global_sp_state=values(global_sp_state)');
}elseif($_POST['globalpromotiontorrent']==2&&$checksecondtypeid){
foreach($checksecondtypeid as $secondid)
$update[]="( $secondid , $spstate )";
sql_query('INSERT INTO torrents_state(secondtype,global_sp_state) VALUES '.join(',',$update).' ON DUPLICATE KEY update global_sp_state=values(global_sp_state)');
}
$Cache->delete_value('global_promotion_state');	
}

$get_second_name=get_second_name();
$res = sql_query("SELECT * FROM audiocodecs ORDER BY lid asc ,sort_index ASC");
stdhead("促销");
?>
<h1>种子促销设置</h1>
<table border="1" cellspacing="0" cellpadding="5" width="940">
<tr>
<td class="colhead">分类</td>
<td class="colhead">当前状态</td>
<td class="colhead">操作<form action="freeleech.php" method="post"></td>
</tr>
<?php
		while ($row = mysql_fetch_array($res))
		{
?>
<tr>
<td class="rowfollow" align='left'><?php echo htmlspecialchars($get_second_name['categories']['name'][$row['lid']].$row['name'])?></td>
<td class="colfollow"><?php echo get_torrent_promotion_append(1,$row['id'])?></td>
<td class="colfollow"><input class=checkbox type="checkbox" name="checksecondtypeid[]" value="<? echo $row['id']?>"></td>
</tr>
<?php
		}
?>
<tr>
	
	<td class='colhead' style='padding: 0px' align='right' colspan='3'>
<?if(get_global_sp_state(0,0)!=1){print "当前全局促销:".get_torrent_promotion_append(0,0);}?>
	<input type="button" value="全选" onClick="this.value=check(form,'全选','全不')">
	<input type="button" value="反选" onClick="checktocheck(form)">
	<select name="spstate" ><option value="1">普通</option><option value="2">免费</option><option value="3">2X</option><option value="4">2X免费</option><option value="5">50%</option><option value="6">2X 50%</option><option value="7">30%</option></select>
<select name="globalpromotiontorrent"><option value="1">全局</option><option value="2">分条</option></select><input type="submit" name="promotiontorrent" value="确定">
</td></form></tr>
</table>
<?
stdfoot();