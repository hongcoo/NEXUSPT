<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
parked();

if (get_user_class() < $staffmem_class)
	permissiondenied();
$torrentid=0+$_GET['torrentid'];
$userid=0+$_GET['userid'];

if ($_POST['setdealt']){
	$res = sql_query ("SELECT id FROM cheaters WHERE dealtwith=0 AND id IN (0," . implode(", ", $_POST[delcheater]) . ")");
	while ($arr = mysql_fetch_assoc($res))
		sql_query ("UPDATE cheaters SET dealtwith=1, dealtby = $CURUSER[id] WHERE id = $arr[id]") or sqlerr();
	$Cache->delete_value('staff_new_cheater_count');
}
elseif ($_POST['delete']){
	$res = sql_query ("SELECT id FROM cheaters WHERE id IN (" . implode(", ", $_POST[delcheater]) . ")");
	while ($arr = mysql_fetch_assoc($res))
		sql_query ("DELETE from cheaters WHERE id = $arr[id]") or sqlerr();
	$Cache->delete_value('staff_new_cheater_count');
}


if($torrentid&&$userid){$countwhere="torrentid = ".$torrentid." and userid = ".$userid;$link="torrentid=".$torrentid."&userid=".$userid;}
elseif($userid){$countwhere="userid = ".$userid;$link="userid=".$userid;}
elseif($torrentid){$countwhere="torrentid = ".$torrentid;$link="torrentid=".$torrentid;}
else $countwhere="1";


$count = get_row_count("cheaters","where ".$countwhere);
if (!$count){
	stderr($lang_cheaterbox['std_oho'], $lang_cheaterbox['std_no_suspect_detected']);
}
$perpage = 50;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "cheaterbox.php?".$link);
stdhead($lang_cheaterbox['head_cheaterbox']);
?>
<style type="text/css">
table.cheaterbox td
{
	text-align: center;
}
</style>
<?php
begin_main_frame();
print("<h1 align=center>".$lang_cheaterbox['text_cheaterbox']."</h1>");
print("<table class=cheaterbox border=1 cellspacing=0 cellpadding=5 align=center>\n");
print("<tr><td class=colhead><nobr>".$lang_cheaterbox['col_added']."</nobr></td><td class=colhead>".$lang_cheaterbox['col_suspect']."</td><td class=colhead>".$lang_cheaterbox['col_torrent']."</td><td class=colhead>".$lang_cheaterbox['col_ul']."</td><td class=colhead>".$lang_cheaterbox['col_dl']."</td><td class=colhead><nobr>".$lang_cheaterbox['col_ann_time']."</nobr></td><td class=colhead><nobr>".$lang_cheaterbox['col_seeders']."</nobr></td><td class=colhead><nobr>".$lang_cheaterbox['col_leechers']."</nobr></td><td class=colhead>".$lang_cheaterbox['col_comment']."</td><td class=colhead><nobr>".$lang_cheaterbox['col_dealt_with']."</nobr></td><td class=colhead><nobr>总上传/总下载</nobr></td><td class=colhead><nobr>".$lang_cheaterbox['col_action']."</nobr></td></tr>");

print("<form method=post action=cheaterbox.php>");
$cheatersres = sql_query("SELECT * FROM cheaters where ".$countwhere." ORDER BY dealtwith ASC, id DESC $limit");

while ($row = mysql_fetch_array($cheatersres))
{
	$upspeed = ($row['uploaded'] > 0 ? $row['uploaded'] / $row['anctime'] : 0);
	$lespeed = ($row['downloaded'] > 0 ? $row['downloaded'] / $row['anctime'] : 0);
	$torrentres = sql_query("SELECT (SELECT sum(uploaded) FROM snatched  where torrentid=torrents.id ) as progressTotaluploaded, (SELECT sum(downloaded) FROM snatched  where torrentid=torrents.id ) as progressTotaldownloaded, name,owner FROM torrents WHERE id=".sqlesc($row['torrentid']));
	$torrentrow = mysql_fetch_array($torrentres);
	if ($torrentrow)
		$torrent = "<a href=cheaterbox.php?".$link."&torrentid=".$row['torrentid'].">".htmlspecialchars($torrentrow['name'])."</a>".get_username($torrentrow['owner']);
	else $torrent = $lang_cheaterbox['text_torrent_does_not_exist'];
	if ($row['dealtwith'])
		$dealtwith = "<font color=green>".$lang_cheaterbox['text_yes']."</font> - " . get_username($row['dealtby']);
	else
		$dealtwith = "<font color=red>".$lang_cheaterbox['text_no']."</font>";
		

	print("<tr><td class=rowfollow>".gettime($row['added'])."</td><td class=rowfollow><a href=cheaterbox.php?".$link."&userid=".$row['userid'].">" . get_username($row['userid'],false,false) . "</a></td><td class=rowfollow>" . $torrent . "</td><td class=rowfollow>".mksize($row['uploaded']).($upspeed ? " @ ".mksize($upspeed)."/s" : "")."</td><td class=rowfollow>".mksize($row['downloaded']).($lespeed ? " @ ".mksize($lespeed)."/s" : "")."</td><td class=rowfollow>".$row['anctime']." sec"."</td><td class=rowfollow>".$row['seeders']."</td><td class=rowfollow>".$row['leechers']."</td><td class=rowfollow>".htmlspecialchars($row['comment'])."</td><td class=rowfollow>".$dealtwith."</td><td class=rowfollow>".mksize($torrentrow[progressTotaluploaded])."<br>".mksize($torrentrow[progressTotaldownloaded])."</td><td class=rowfollow><input type=\"checkbox\" name=\"delcheater[]\" value=\"" . $row[id] . "\" /></td></tr>\n");
}
?>
<tr><td class="colhead" colspan="12" style="text-align: right"><input type="button" value="全选" onClick="this.value=check(form,'全选','全不选')"><input type="submit" name="setdealt" value="<?php echo $lang_cheaterbox['submit_set_dealt']?>" /><input type="submit" name="delete" value="<?php echo $lang_cheaterbox['submit_delete']?>" /></td></tr> 
</form>
<?php
print("</table>");
print($pagerbottom);
end_main_frame();
stdfoot();
?>
