<?php 
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
if (get_user_class() < UC_Warehouse) stderr("Error", "Permission denied.");
$timeout=24*4;
preg_match_all( "/([\[\]A-Za-z0-9:.]+)/sim", nl2br($_POST['body']), $bookblocks );
$id=0+$_POST['pid'];
$totelnum=0;
if($id){
sql_query("delete from peers where torrent = $id and userid = 0 ");
sql_query("UPDATE torrents SET seeders = seeders + 1,last_action = ".sqlesc(date("Y-m-d H:i:s"))." WHERE id = $id");
}

foreach($bookblocks[0] as $block )
{
preg_match_all( "/\[([A-Fa-f0-9:.]+)]:([0-9]+)/sim",$block, $temp );

if($temp[1][0]&&$temp[2][0])
{
//PRINT "[".$temp[1][0]."]".":".$temp[2][0]."<BR />";
$totelnum++;
sql_query("INSERT INTO peers (torrent,ip,port,connectable,seeder,next_action,iptype) VALUES ($id,".sqlesc($temp[1][0]).", ".sqlesc($temp[2][0])." , 'yes', 'yes',".sqlesc(strtotime(date("Y-m-d H:i:s")) + $timeout*3600)." , 6  )");
}

preg_match_all( "/([0-9.]+):([0-9]+)/sim",$block, $temp );

if($temp[1][0]&&$temp[2][0])
{
//PRINT "[".$temp[1][0]."]".":".$temp[2][0]."<BR />";
$totelnum++;
sql_query("INSERT INTO peers (torrent,ip,port,connectable,seeder,next_action,iptype) VALUES ($id,".sqlesc($temp[1][0]).", ".sqlesc($temp[2][0])." , 'yes', 'yes',".sqlesc(strtotime(date("Y-m-d H:i:s")) + $timeout*3600)." , 4)");
}


}

stdhead("测试");

 ?>
 <h1>种子手动添加用户,<?php echo $timeout; ?>小时有效(请确保正确性)</h1>
 <?php
begin_main_frame("",false);
?>
<table width="100%"><tr><td class="text" align="center"> 

<form method="post">

UT用户列表<?php
if($totelnum)print("<br />已成功添加".$totelnum."个做种用户");
 ?><br /><textarea name='body' cols="100" rows="8" style="width: 450px"></textarea><br />
种子ID<input type="text" name="pid" /><input type="submit"  value="添加" /></form>
</td> </tr></table>
<?php
end_main_frame();
stdfoot();
?>

 
  
 