<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
loggedinorreturn();

function bark($msg) {
	global $lang_takeedit;
	genbark($msg, $lang_takeedit['std_edit_failed']);
}

if (!mkglobal("id")){
	global $lang_takeedit;
	bark($lang_takeedit['std_missing_form_data']);
}

$id = 0 + $id;
if (!$id)
	die();


$res = sql_query("SELECT category, owner, filename, save_as, anonymous, picktype, picktime, added, url FROM torrents WHERE id = ".mysql_real_escape_string($id));
$row = mysql_fetch_array($res);
$torrentAddedTimeString = $row['added'];
if (!$row)
	die();

if ($CURUSER["id"] != $row["owner"] && get_user_class() < $torrentmanage_class&&$CURUSER["picker"] != 'yes')
	bark($lang_takeedit['std_not_owner']);
$oldcatmode = get_single_value("categories","mode","WHERE id=".sqlesc($row['category']));

$updateset = array();

//$fname = $row["filename"];
//preg_match('/^(.+)\.torrent$/si', $fname, $matches);
//$shortfname = $matches[1];
//$dname = $row["save_as"];

$url = parse_imdb_id($_POST['url']);

$imdbnum = 0+$_POST['imdbnum'];

/*if($imdbnum==2){
$url2=get_single_value('imdbdoubanurl','imdb','where douban='.sqlesc($url));
if($url2){$url=$url2;$imdbnum=1;}
}*/

if($CURUSER["id"] == $row["owner"] || get_user_class()>=$torrentmanage_class){

if (!mkglobal("name:descr:type")){
	global $lang_takeedit;
	bark($lang_takeedit['std_missing_form_data']);
}

$descr = str_replace("http://6movie.org/topics/", "http://www.imdb.com/title/", $descr);

if ($enablenfo_main=='yes'){
$nfoaction = $_POST['nfoaction'];
if ($nfoaction == "update")
{
	$nfofile = $_FILES['nfo'];
	if (!$nfofile) die("No data " . var_dump($_FILES));
	if ($nfofile['size'] > 65535)
		bark($lang_takeedit['std_nfo_too_big']);
	$nfofilename = $nfofile['tmp_name'];
	if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0)
		$updateset[] = "nfo = " . sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", file_get_contents($nfofilename)));
	$Cache->delete_value('nfo_block_torrent_id_'.$id);
}
elseif ($nfoaction == "remove"){
	$updateset[] = "nfo = ''";
	$Cache->delete_value('nfo_block_torrent_id_'.$id);
}
}

$catid = (0 + $type);
//if (!is_valid_id($catid)||$_POST["audiocodec_sel"]==0&&isset($_POST["audiocodec_sel"]))

$secondtype = searchbox_item_list("audiocodecs",$catid);
$secondsize = count($secondtype,0);
for($i=0; $i<$secondsize; $i++)	$cachearray[] = $secondtype[$i]['id'];
if (!is_valid_id($catid)||$_POST["audiocodec_sel"]==0&&isset($_POST["audiocodec_sel"])||$secondsize>0&&!in_array(($_POST["audiocodec_sel"]), $cachearray))
bark($lang_takeedit['std_missing_form_data']);


if (!$name || !$descr)
bark($lang_takeedit['std_missing_form_data']);
$newcatmode = get_single_value("categories","mode","WHERE id=".sqlesc($catid));
if ($enablespecial == 'yes' && (get_user_class() >= $movetorrent_class||$CURUSER["picker"] == 'yes'))
	$allowmove = true; //enable moving torrent to other section
else $allowmove = false;
if ($oldcatmode != $newcatmode && !$allowmove)
	bark($lang_takeedit['std_cannot_move_torrent']);
	
$small_descr=$_POST["small_descr"];


$small_descr = form_second_name($small_descr);
$name = form_second_name($name);
	
$updateset[] = "anonymous = '" . ($_POST["anonymous"] ? "yes" : "no") . "'";
$updateset[] = "name = " . sqlesc($name);
$updateset[] = "descr = " . sqlesc($descr);
//$updateset[] = "url = " . sqlesc($url);
$updateset[] = "small_descr = " . sqlesc($small_descr);
//$updateset[] = "ori_descr = " . sqlesc($descr);
$updateset[] = "category = " . sqlesc($catid);
$updateset[] = "source = " . sqlesc(0 + $_POST["source_sel"]);
$updateset[] = "medium = " . sqlesc(0 + $_POST["medium_sel"]);
$updateset[] = "codec = " . sqlesc(0 + $_POST["codec_sel"]);
$updateset[] = "standard = " . sqlesc(0 + $_POST["standard_sel"]);
$updateset[] = "processing = " . sqlesc(0 + $_POST["processing_sel"]);
$updateset[] = "team = " . sqlesc(0 + $_POST["team_sel"]);
$updateset[] = "audiocodec = " . sqlesc(0 + $_POST["audiocodec_sel"]);
}

if (get_user_class() >= $torrentmanage_class||$CURUSER["picker"] == 'yes') {
	if ($_POST["banned"]) {
		$updateset[] = "banned = 'yes'";
		$_POST["visible"] = 0;
	}
	else
		$updateset[] = "banned = 'no'";
}
$updateset[] = "visible = '" . ($_POST["visible"] ? "yes" : "no") . "'";
if(get_user_class()>=$torrentonpromotion_class)
{
	if(!isset($_POST["sel_spstate"]) || $_POST["sel_spstate"] == 1)
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

	//promotion expiration type
	if(!isset($_POST["promotion_time_type"]) || $_POST["promotion_time_type"] == 0) {
		$updateset[] = "promotion_time_type = 0";
		$updateset[] = "promotion_until = '0000-00-00 00:00:00'";
	} elseif ($_POST["promotion_time_type"] == 1) {
		$updateset[] = "promotion_time_type = 1";
		$updateset[] = "promotion_until = '0000-00-00 00:00:00'";
	} elseif ($_POST["promotion_time_type"] == 2) {
		if ($_POST["promotionuntil"] && strtotime($torrentAddedTimeString) <= strtotime($_POST["promotionuntil"])) {
			$updateset[] = "promotion_time_type = 2";
			$updateset[] = "promotion_until = ".sqlesc($_POST["promotionuntil"]);
		} else {
			$updateset[] = "promotion_time_type = 0";
			$updateset[] = "promotion_until = '0000-00-00 00:00:00'";
		}
	}
}
if(get_user_class()>=$torrentsticky_class)
{
	if((0 + $_POST["sel_posstate"]) == 0)
		$updateset[] = "pos_state = 'normal'";
	elseif((0 + $_POST["sel_posstate"]) == 1)
		$updateset[] = "pos_state = 'sticky'";

}

$pick_info = "";
if (get_user_class()>= $torrentsticky_class||$CURUSER["picker"] == 'yes')
//if(get_user_class()>=$torrentmanage_class && $CURUSER['picker'] == 'yes')
{
	if((0 + $_POST["sel_recmovie"]) == 0)
	{
		if($row["picktype"] != 'normal')
			$pick_info = ", recomendation canceled!";
		$updateset[] = "picktype = 'normal'";
		$updateset[] = "picktime = '0000-00-00 00:00:00'";
	}
	elseif((0 + $_POST["sel_recmovie"]) == 1)
	{
		if($row["picktype"] != 'hot')
			$pick_info = ", recommend as hot movie";
		$updateset[] = "picktype = 'hot'";
		$updateset[] = "picktime = ". sqlesc(date("Y-m-d H:i:s"));
	}
	elseif((0 + $_POST["sel_recmovie"]) == 2)
	{
		if($row["picktype"] != 'classic')
			$pick_info = ", recommend as classic movie";
		$updateset[] = "picktype = 'classic'";
		$updateset[] = "picktime = ". sqlesc(date("Y-m-d H:i:s"));
	}
	elseif((0 + $_POST["sel_recmovie"]) == 3)
	{
		if($row["picktype"] != 'recommended')
			$pick_info = ", recommend as recommended movie";
		$updateset[] = "picktype = 'recommended'";
		$updateset[] = "picktime = ". sqlesc(date("Y-m-d H:i:s"));
	}
	
	IF(!$catid){
	$catid = (0 + $_POST['type']);
	if (!is_valid_id($catid))bark($lang_takeedit['std_missing_form_data']);
	$updateset[] = "category = " . sqlesc($catid);
}	
}
$updateset[] = "url = " . sqlesc($url);
$updateset[] = "urltype = " . sqlesc($imdbnum);
$updateset[] = "editdate = ". sqlesc(date("Y-m-d H:i:s"));
$updateset[] = "quality = 'pend'";


sql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id") or sqlerr(__FILE__, __LINE__);


if($url&&get_row_count("torrents", "WHERE url = ".sqlesc($url)." and urltype != ".sqlesc($imdbnum) ))
write_log('ERROR_UPLOAD_IMDBURL:'.sqlesc($url),"mod");


if($CURUSER["id"] == $row["owner"])
{
	if ($row["anonymous"]=='yes')
	{
		write_log("Torrent $id ($name) was edited by Anonymous" . $pick_info . $place_info);
	}
	else
	{
		write_log("Torrent $id ($name) was edited by $CURUSER[username]" . $pick_info . $place_info);
	}
}
else
{
	write_log("Torrent $id ($name) was edited by $CURUSER[username], Mod Edit" . $pick_info . $place_info);
	$dt = sqlesc(date("Y-m-d H:i:s"));
	$subject = sqlesc('您发布的资源被修改');
	$msg = sqlesc("您发布的种子 [url=details.php?id=".$id."]".$name."[/url] 被 [uid".$CURUSER[id]."] (应该是管理员)修改");
	sql_query("INSERT INTO messages (sender, receiver, subject, added, msg) VALUES(0, $row[owner], $subject, $dt, $msg)");
	
}
$returl = "details.php?id=$id&edited=1";
if (isset($_POST["returnto"]))
	$returl = $_POST["returnto"];
	

$Cache->delete_value((0+$row['owner']).'_torrentsothercount');

reset_cachetimestamp($id);
if((get_user_class() > $updateextinfo_class||$row['owner']==$CURUSER['id'])&&$url!=$row["url"])
header("Location: details.php?id=$id&edited=1");	
else
header("Refresh: 0; url=$returl");

