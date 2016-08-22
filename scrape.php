<?php
require_once('include/bittorrent_announce.php');
require_once('include/benc.php');
dbconn_announce();

// BLOCK ACCESS WITH WEB BROWSERS AND CHEATS!
block_browser();


preg_match_all('/info_hash=([^&]*)/i', $_SERVER["QUERY_STRING"], $info_hash_array);
$fields = "id ,info_hash, times_completed, seeders, leechers";


if (!$az = $Cache->get_value('user_passkey_scrape_'.$passkey.'_content')){
	$res = sql_query("SELECT  MODEMAX FROM users WHERE passkey=". sqlesc($passkey)." LIMIT 1");
	$az = mysql_fetch_array($res);
	$Cache->cache_value('user_passkey_scrape_'.$passkey.'_content', $az, 600);
}



$enable=(($az["MODEMAX"]==5||$az["MODEMAX"]==6)&&!ip2long(getip())||($az["MODEMAX"]==4&&ip2long(getip()))?true:false);

if (count($info_hash_array[1]) < 1) {
	$query = "SELECT $fields FROM torrents where  0  ORDER BY id";
	//$query = "SELECT $fields FROM torrents WHERE " . hash_where('info_hash', $info_hash_array[1])
}
else {
	$query = "SELECT $fields FROM torrents WHERE " . hash_where_arr('info_hash', $info_hash_array[1]);
}
$r = "d" . benc_str("files") . "d";

$res = sql_query($query);

if (mysql_num_rows($res) < 1){
	err("该种子还未上传到服务器.");
}

while ($row = mysql_fetch_assoc($res)) {
	if($enable)
	$r .= "20:" . hash_pad($row["info_hash"]) . "d" .benc_str("complete") . "i" . $row["seeders"] . "e" .benc_str("downloaded") . "i" . ($row["times_completed"]+1) . "e" .benc_str("incomplete") . "i" . $row["leechers"] . "e" ."e";
else{
	$r .= "20:" . hash_pad($row["info_hash"]) . "d" .benc_str("complete") . "i0e" .benc_str("downloaded") . "i0e" .benc_str("incomplete") . "i0e" ."e";
}
	}
$r .= "ee";

benc_resp_raw($r);
