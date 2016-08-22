<?php
//请自行添加HASH修改的SQL

if (php_sapi_name() != 'cli') die('This program can be run only in php_cli mode.');
require_once("include/bittorrent.php");
require_once("include/benc.php");
@set_time_limit(600);
dbconn();
print ("START \n");
function dict_check($d, $s) {
	global $lang_takeupload;
	if ($d["type"] != "dictionary")
	return false;
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if(isset($dd[$k.".utf-8"]))$k=$k.".utf-8";
		if (!isset($dd[$k]))
		return false;
		if (isset($t)) {
			if ($dd[$k]["type"] != $t)
			return false;
			$ret[] = $dd[$k]["value"];
		}
		else
		$ret[] = $dd[$k];
	}
	return $ret;
}


function hashtorrent($id) {
global $torrent_dir,$max_torrent_size;
$dict = bdec_file("$torrent_dir/$id.torrent", $max_torrent_size);
if(!list($info) = dict_check($dict, "info"))return  "HASH_ERROR".$id;
return pack("H*", sha1($info["string"]));				
}


$res = sql_query("SELECT id,name, info_hash FROM torrents  ORDER BY id ");
		while ($row = mysql_fetch_array($res)){
		$newhash=hashtorrent($row[id]);
			if($row[info_hash]!=$newhash){
			sql_query("update torrents set info_hash=".sqlesc($newhash)."  where id =".sqlesc($row[id]));
			print bin2hex($newhash)."@".$row[id]."<br>\n" ;}
			}
			
			

print ('done@min:'.(0+((getmicrotime()-TIMENOWSTART))/60));