<?php
require_once("include/bittorrent.php");
require ("imdb/imdb.class.php");
dbconn();
loggedinorreturn();

$id = 0 + $_GET["id"];
$type = 0 + $_GET["type"];
$siteid = 0 + $_GET["siteid"]; // 1 for IMDb

if (!isset($id) || !$id || !is_numeric($id) || !isset($type) || !$type || !is_numeric($type)){
die('丢失表单');
}

$r = sql_query("SELECT * from torrents WHERE id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($r) != 1){
die('该种子不存在');
}



$row = mysql_fetch_assoc($r);

if (get_user_class() < $updateextinfo_class&&$row['owner']!=$CURUSER['id']) {
die('等级不够');
}




if(!ipv6statue('NETWORK')){
$type =1;
}


function renewimdb($url,$site,$type){
global $Cache;
	$id=parse_imdb_id($url);
	if(!$id)return false;
	$movie = new imdb ($id);
	$movie->setid ($id);
	$movie->settypt($site);
	($type == 2 ? $movie->purge_single(true) : "");
	$movie->preparecache(array('Title'),true);
	
	if($movie->photo_localurl()&&$movie->doubantureid()){
	sql_query("Delete from imdbinfo where imdb =". sqlesc($id));
	sql_query("INSERT INTO imdbinfo (imdb,name,info,rating,time) VALUES  (". sqlesc($id) .",". sqlesc($movie->alsoknowcnname ()) ."," .sqlesc($movie->movieallinfo()) . "," .sqlesc($movie->rating()) . ",".TIMENOW.") ON DUPLICATE KEY update time=values(time)");
	sql_query("UPDATE torrents SET rating =" . sqlesc($movie->rating()) . " WHERE urltype = " . sqlesc($site) . " and url = " . sqlesc($id));
			$Cache->delete_value('imdb_id_'.$id.'_movie_name');
			$Cache->delete_value('imdb_id_'.$id.'_large', true);
			$Cache->delete_value('imdb_id_'.$id.'_median', true);
			$Cache->delete_value('imdb_id_'.$id.'_minor', true);
			$Cache->delete_value('ranting_'.$id);
	return array('DOUBAN'=>$movie->doubantureid(),'IMDB'=>$movie->imdbtureid());
	}
	return false;
}


		if ($imdb_id= parse_imdb_id($row["url"])){	

			if($row['cache_stamp'] != 0 &&((time()-$row['cache_stamp']) < 30))die('已有用户申请更新');
			set_cachetimestamp($id,"cache_stamp");
			if(!@imagecreatefromjpeg('./imdb/images/'.$imdb_id.'.jpg'))@unlink('./imdb/images/'.$imdb_id.'.jpg');
			$thenumbers = renewimdb($imdb_id,$row['urltype'],$type);
			if($thenumbers['DOUBAN']&&$thenumbers['IMDB']){
				sql_query("UPDATE torrents SET urltype = 1, url=" . sqlesc($thenumbers['IMDB']) . " WHERE urltype = 2 and url = " . sqlesc($thenumbers['DOUBAN']));			
				sql_query("INSERT INTO imdbdoubanurl (imdb, douban, time) VALUES (" . sqlesc($thenumbers['IMDB'])."," . sqlesc($thenumbers['DOUBAN']).",".TIMENOW.")  ON DUPLICATE KEY update time=values(time)");
			}elseif($thenumbers['DOUBAN']&&$row['urltype']==1&&$imdb_id){
			
				$thenumberschange = renewimdb($thenumbers['DOUBAN'],2,$type);
				if($thenumberschange['DOUBAN'])
				sql_query("UPDATE torrents SET urltype = 2, url=" . sqlesc($thenumberschange['DOUBAN']) . " WHERE urltype = 1 and url = " . sqlesc($imdb_id));
				
			}
		}			
if($_GET["isjq"])die('ok');	
	


header("Location: " . get_protocol_prefix() . "$BASEURL/details.php?id=".htmlspecialchars($id));
?>
