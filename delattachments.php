<?php
if (php_sapi_name() != 'cli') die('This program can be run only in php_cli mode.');
echo "\nSTART\n";
require "include/bittorrent.php";
dbconn();
@set_time_limit(1200);
//ini_set("memory_limit","5G");
$deleteattachments=in_array($argv[1], array('-delall'));
//print ($deleteattachments?"1":"2");
sql_query("UPDATE attachments SET inuse = 0 ") or sqlerr(__FILE__, __LINE__);

  $tables = array('torrents'=> 'descr',
	     'posts'=> 'body',
	     'offers'=> 'descr',
	     'comments'=> 'text',
	     'fun'=> 'body',
	     'messages'=> 'msg',
	     'staffmessages'=> 'msg',
	     'users'=> 'signature',
	     'requests'=> 'descr');

  foreach ($tables as $table => $col) {
      echo $table, '.', $col , "\n";
    $atts=array();
    dbconn();
    $res = sql_query("SELECT `$col` FROM $table WHERE `$col` LIKE  '%attach%'") or sqlerr(__FILE__, __LINE__);
    while($row = mysql_fetch_array($res)){
      $attstemp = array();
      preg_match_all('/\[attach\](.*?)\[\/attach\]/', $row[0], $attstemp);
	  if (count($attstemp[1]) != 0) {
      dbconn();
      sql_query("UPDATE attachments SET inuse = 1 WHERE dlkey IN (" . implode(",", array_map("sqlesc",$attstemp[1])) . ")") or sqlerr(__FILE__, __LINE__);
    }
      //$atts = array_merge($atts, $attstemp[1]);
    }

  /*  if (count($atts) != 0) {
      dbconn();
      sql_query("UPDATE attachments SET inuse = 1 WHERE dlkey IN (" . implode(",", array_map("sqlesc",$atts)) . ")") or sqlerr(__FILE__, __LINE__);
    }*/
  }
  
 
$res=sql_query("SELECT count(*), SUM(filesize) AS filesizes FROM attachments WHERE inuse = 0") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$deletecount=$row[0];
$filesizes=$row[1];
echo "\n\nAll:".$deletecount."COUNTS\nSIZE:".($filesizes/1000)."KB\n";

if($deleteattachments){
		$filepath = $savedirectory_attachment."/";
		$res = sql_query("SELECT location FROM attachments WHERE inuse = 0") or sqlerr(__FILE__, __LINE__);
		while($row = mysql_fetch_array($res)){
			if(file_exists($filepath.$row[0])&&!unlink($filepath.$row[0]))die('ERROR:Please Be Admin');
			if(file_exists($filepath.$row[0])&&!unlink($filepath.$row[0].".thumb.jpg"))die('ERROR:Please Be Admin');
			}
		sql_query("DELETE FROM attachments WHERE inuse = 0") or sqlerr(__FILE__, __LINE__);
		ECHO "\nDEL OVER\n";
}else echo "\nyou may del attachments by adding the parameter '-delall'\n";



die();
//检测不存在于SQL内的附件 请自信添加unlink
$thiscandel=$thishave=array();	
function listDir($dir){
global $thishave,$thiscandel,$deleteattachments; 
if(is_dir($dir)){
 
		if ($dh = opendir($dir)) { 
				while (($file= readdir($dh)) !== false){ 
						if((is_dir($dir."/".$file)) && $file!="." && $file!=".."){ 
							listDir($dir."/".$file); 
						}else{ 
							if($file!="." && $file!="..") 
							if(!$thishave[$dir."/".$file]){
							$thiscandel[] = $dir."/".$file; 
							if($deleteattachments&&file_exists($dir."/".$file)&&!unlink($dir."/".$file))die('ERROR:Please Be Admin');
							}
							unset($thishave[$dir."/".$file]);
							
						} 
						} 
				closedir($dh); 
		} else die('error1');
}  else die('error2');
}
$res = sql_query("SELECT id, location ,thumb FROM attachments") or sqlerr(__FILE__, __LINE__);
while ($row = mysql_fetch_array($res)){
$thishave['attachments/'.$row['location']] = $row['id'];
if($row['thumb'])$thishave['attachments/'.$row['location'].".thumb.jpg"] = $row['id'];
}
listDir("attachments"); 
print_r($thiscandel);
print_r($thishave);
if($deleteattachments&&count($thishave) != 0)sql_query ("DELETE FROM attachments WHERE id IN (" . implode(",", array_map("sqlesc",$thishave)).")");

echo "\n".TIMENOW-time()."S\n";