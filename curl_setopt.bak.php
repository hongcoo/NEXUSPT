<?php
require_once('img/include.php');


if($_REQUEST['ptpasskey']!=ANTSOUL_IIS_PASSKEY){
	if(!iisstate())
	die('500:ERROR');
	else
	die('200:OK');
}


$url=$_REQUEST['conurl'];

if(ip2long($ip=getip()))
mysql_query("INSERT INTO slave_server_ip(upname,upvalue,uptime) VALUES ('ipv4','" . mysql_real_escape_string($ip) . "',".TIMENOW.") ON DUPLICATE KEY update upvalue=values(upvalue),uptime=values(uptime)");
else
mysql_query("INSERT INTO slave_server_ip(upname,upvalue,uptime) VALUES ('ipv6','" . mysql_real_escape_string($ip) . "',".TIMENOW.") ON DUPLICATE KEY update upvalue=values(upvalue),uptime=values(uptime)");


if($_REQUEST['contype']=='html') print CurlGet_html($url);
elseif($_REQUEST['contype']=='img') print CurlGet_img($url);
elseif($_REQUEST['contype']=='http') print file_get_contents_function($url);
else die("d14:failure reason21:蚂蚁PT永久关闭!8:intervali1800e12:min intervali120ee");






