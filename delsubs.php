<?php
if (php_sapi_name() != 'cli') die('This program can be run only in php_cli mode.');
echo "\nSTART\n";
require "include/bittorrent.php";
dbconn();
@set_time_limit(1200);
//ini_set("memory_limit","5G");
$deleteattachments=in_array($argv[1], array('-delall'));
//print ($deleteattachments?"1":"2");

//检测不存在的字幕文件
function deletedir($dir){
global $deleteattachments;
if($handle=@opendir($dir)) 
     while(false !=($file=readdir($handle))){
               if($file!='.'&&$file!='..'){       //排除当前目录与父级目录
                            echo $file=$dir .DIRECTORY_SEPARATOR. $file;
                            if(is_dir($file)){
                                  deletedir($file);
                            }else{
                                  if($deleteattachments){
								  if(@unlink($file)){
                                         echo "file<b>$file</b>del over。<br>";
                                  }else{
                                         die('ERROR:Please Be Admin');
                                 }
								}else echo "file<b>$file</b>will del。<br>";
								}
     }}
    if(@rmdir($dir)){
           echo "dir<b>$dir</b>del over<br>\n";
    }else{
           echo "file<b>$dir</b>del fail<br>\n";
  }

}	 
$res = sql_query("Select DISTINCT torrent_id FROM subs") or sqlerr(__FILE__, __LINE__);
$thishave=array();	

while ($row = mysql_fetch_array($res))
$thishave[$row['torrent_id']] = $row['torrent_id'];


$dir=$SUBSPATH;
if($dh = opendir($dir))
while (($file= readdir($dh)) !== false)
if(!$thishave[$file]&&$file!="." && $file!="..")deletedir($dir."/".$file);


$dir=$SUBSPATH;
if($dh = opendir($dir))
while (($file= readdir($dh)) != false)if(@rmdir($dir."/".$file))print($dir."/".$file."<br/>\n");

echo "\nyou may del attachments by adding the parameter '-delall'\n";

echo "\n".TIMENOW-time()."S\n";