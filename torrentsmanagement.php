<?php
require_once("include/bittorrent.php");
dbconn();


loggedinorreturn();
if (get_user_class() < UC_UPLOADER)
    stderr('错误','权限不足');
	
	
$torrent_id = $_POST['torrentsmanagementid'];
$torrent_id[]=0;
$where=" WHERE id IN (" . implode(", ", array_map("sqlesc",$torrent_id)) . ") ";

if(get_user_class() >=$torrentmanage_class){
if($_POST['bannedtorrent'])$action='ban';
elseif($_POST['posstatetorrent'])$action='pos';
elseif($_POST['picktorrent'])$action='pick';
elseif($_POST['promotiontorrent'])$action='promotion';
elseif($_POST['deletetorrent'])$action='delete';
elseif($_POST['torrentlowquality'])$action='lowquality';
elseif($_POST['torrentqualityyes'])$action='qualityyes';
elseif($_POST['torrentqualitypend'])$action='qualitypend';
elseif($_POST['torrentqualityno'])$action='qualityno';
else stderr('错误','操作不明');
}else{
if($_POST['torrentqualityyes'])$action='qualityyes';
elseif($_POST['torrentqualitypend'])$action='qualitypend';
elseif($_POST['torrentqualityno'])$action='qualityno';
else stderr('错误','操作不明');
}

switch ($action){
	case "qualityyes": 
			$updateset[] = "quality = 'yes'";
	break;
	case "qualitypend": 
			$updateset[] = "quality = 'pend'";
	break;
	case "qualityno": 
			$updateset[] = "quality = 'no'";
	break;
	case "lowquality": 
			$updateset[] = "category = 415";
			$updateset[] = "editdate = ".sqlesc(date("Y-m-d H:i:s"));
	break;
	case "ban": 
		if (0+$_POST["sel_banstate"])
			$updateset[] = "banned = 'yes' , visible = 'no' , category = 415";
		else
			$updateset[] = "banned = 'no'";
	
	break;
	case "pos": 
	
		if(0 + $_POST["sel_posstate"])
			$updateset[] = "pos_state = 'sticky'";		
		else
			$updateset[] = "pos_state = 'normal'";
		

	break;
	case "pick":
		if((0 + $_POST["sel_recmovie"]) == 0)
	{
		$updateset[] = "picktype = 'normal'";
		$updateset[] = "picktime = '0000-00-00 00:00:00'";
	}
	elseif((0 + $_POST["sel_recmovie"]) == 1)
	{
		$updateset[] = "picktype = 'hot'";
		$updateset[] = "picktime = ". sqlesc(date("Y-m-d H:i:s"));
	}
	elseif((0 + $_POST["sel_recmovie"]) == 2)
	{
		$updateset[] = "picktype = 'classic'";
		$updateset[] = "picktime = ". sqlesc(date("Y-m-d H:i:s"));
	}
	elseif((0 + $_POST["sel_recmovie"]) == 3)
	{
		$updateset[] = "picktype = 'recommended'";
		$updateset[] = "picktime = ". sqlesc(date("Y-m-d H:i:s"));
	}
		
	break;
	case "promotion": 
	if(0+ $_POST["sel_spstate"] == 1)
		$updateset[] = "sp_state = 1";
	elseif((0 + $_POST["sel_spstate"]) == 2)
		$updateset[] = "sp_state = 2";
	elseif((0 + $_POST["sel_spstate"]) == 3)
		$updateset[] = "sp_state = 3";
	elseif((0 + $_POST["sel_spstate"]) == 4)
		$updateset[] = "sp_state = 4";
	elseif((0 + $_POST["sel_spstate"]) == 5)
		$updateset[] = "sp_state = 5";
	elseif((0 + $_POST["sel_spstate"]) == 6)
		$updateset[] = "sp_state = 6";
	elseif((0 + $_POST["sel_spstate"]) == 7)
		$updateset[] = "sp_state = 7";
		
		if($_POST["promotion_time_type"] == 0) {
		$updateset[] = "promotion_time_type = 0";
		$updateset[] = "promotion_until = '0000-00-00 00:00:00'";
	} elseif ($_POST["promotion_time_type"] == 1) {
		$updateset[] = "promotion_time_type = 1";
		$updateset[] = "promotion_until = '0000-00-00 00:00:00'";
	} elseif ($_POST["promotion_time_type"] == 2) {
		if ($_POST["promotionuntil"]){
			$updateset[] = "promotion_time_type = 2";
			$updateset[] = "promotion_until = ".sqlesc($_POST["promotionuntil"]);
		} else {
			$updateset[] = "promotion_time_type = 0";
			$updateset[] = "promotion_until = '0000-00-00 00:00:00'";
		}
	}

		break;
	case "delete": 
	require_once(get_langfile_path('fastdelete.php'));
	require_once(get_langfile_path('fastdelete.php',true)); 
	
	foreach($torrent_id as $id){
	$row = @mysql_fetch_array(sql_query("SELECT name,owner,seeders,anonymous FROM torrents WHERE id = $id"));
	 if(!$row) continue;
		deletetorrent($id);
		KPS("-",$uploadtorrent_bonus,$row["owner"]);
		if ($row['anonymous'] == 'yes' && $CURUSER["id"] == $row["owner"]) {
			write_log("Torrent $id ($row[name]) was deleted by its anonymous uploader",'normal');
		} else {
			write_log("Torrent $id ($row[name]) was deleted by $CURUSER[username]",'normal');
		}

	if ($CURUSER["id"] != $row["owner"])
	{
	$dt = sqlesc(date("Y-m-d H:i:s"));
	$subject = sqlesc($lang_fastdelete_target[get_user_lang($row["owner"])]['msg_torrent_deleted']."(".$row['name'].")");
	$msg = sqlesc($lang_fastdelete_target[get_user_lang($row["owner"])]['msg_the_torrent_you_uploaded'].$row['name'].$lang_fastdelete_target[get_user_lang($row["owner"])]['msg_was_deleted_by']."[url=userdetails.php?id=".$CURUSER['id']."]".$CURUSER['username']."[/url]".$lang_fastdelete_target[get_user_lang($row["owner"])]['msg_blank']);
	sql_query("INSERT INTO messages (sender, receiver, subject, added, msg) VALUES(0, $row[owner], $subject, $dt, $msg)");
	}

	
	$res = sql_query("SELECT DISTINCT(userid) FROM peers WHERE torrent = ".sqlesc($id)." AND userid <> ".sqlesc($row["owner"]));
	while ($rowdel = mysql_fetch_array($res)){
	$dt = sqlesc(date("Y-m-d H:i:s"));
	$subject = sqlesc("您正在下载或做种的种子被删除(".$row['name'].")");
	$msg = sqlesc("您正在下载或做种的种子 ".$row['name']." 被用户 [url=userdetails.php?id=".$CURUSER['id']."]".$CURUSER['username']."[/url]  删除，感谢您的贡献。");	
	sql_query("INSERT INTO messages (sender, receiver, subject, added, msg) VALUES(0, $rowdel[userid], $subject, $dt, $msg)");
			}
	}
	break;
	}
	
if($updateset)sql_query("UPDATE torrents SET " . join(",", $updateset) . $where);
if ($_POST['returnto'] != "")
	header("Location: {$_POST['returnto']}");
	else
header("Refresh: 0; url=torrents.php");
?>