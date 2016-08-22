<?php
require_once("include/bittorrent.php");
dbconn();
@set_time_limit(130);
if($Cache->get_value('here_now_have_no_mail')||$Cache->get_value('here_now_have_no_mail_2'))die('no');
$Cache->cache_value('here_now_have_no_mail', 'sending',120);
$Cache->cache_value('here_now_have_no_mail_2', 'sending',60);

file_get_contents_function('');




$res = sql_query("SELECT * FROM mail_store WHERE trytimes < 3 ORDER BY RAND() limit 3");

while ($row = mysql_fetch_assoc($res)){
if(!get_row_count("mail_store","where id = ".$row['id']))continue;
if(sent_mail_store(unserialize($row['touser']),unserialize($row['fromname']),unserialize($row['fromemail']),unserialize($row['subjecttitle']),unserialize($row['body']),unserialize($row['type']),(false),unserialize($row['multiple']),unserialize($row['multiplemail']),unserialize($row['hdr_encoding']),unserialize($row['specialcase']))){
sql_query("delete FROM mail_store where id=".$row['id']);
//write_log("OK@".$accountname,'file');
}else{
sql_query("UPDATE  mail_store SET trytimes=trytimes+1 where id=".$row['id']);
//write_log("ERROR@".$accountname,'file');
}
}

$res = sql_query("SELECT * FROM formatcodephping ORDER BY RAND() limit 5");
while ($row = mysql_fetch_assoc($res)){
if($row['type']=='formatcodephp2url')print formatCodePhp2url($row['org'],true);
elseif($row['type']=='formatcodephp2img')print formatCodePhp2img($row['org'],true);
sql_query("delete FROM formatcodephping where id=".$row['id']);
}


if(!get_row_count("mail_store","WHERE trytimes < 3 ")&&!get_row_count("formatcodephping","WHERE 1"))
			$Cache->cache_value('here_now_have_no_mail', 'yes',600);
			else $Cache->delete_value('here_now_have_no_mail');

	
	//$Cache->delete_value('here_now_have_no_mail_2');