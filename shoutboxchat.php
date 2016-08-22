<?php
require_once("include/bittorrent.php");
dbconn();

if(isset($_POST["time"])){
if(get_row_count('shoutbox', "where date > ".(0+$_POST["time"])." limit 2"))die('have_new');
else die('have_no_new');
}elseif($_GET["C"])resimsimi_debug($_GET["C"],TRUE);
elseif(!$chatmarisa)die('error');



$robid=$ROBOTUSERID;
$userid=0+$CURUSER["id"];
if(!$userid)die();

function escape($str){ 
preg_match_all("/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e",$str,$r); 
$str = $r[0]; 
$l = count($str); 
for($i=0; $i <$l; $i++){ 
$value = ord($str[$i][0]); 
if($value < 223) 
$str[$i] = rawurlencode(utf8_decode($str[$i]));
else 
$str[$i] = "P_u".strtoupper(bin2hex(iconv("UTF-8","UCS-2",$str[$i]))); 
} 
return join("",$str); 
}

function chat($POST,$DEBUG=FALSE){ 
global $CURUSER;
if ($POST)
$content="p1=".urlencode($POST);
else 
$content="p0=".date("G",time());

	$option = array('http' => array('method' => "POST",'header' => 
	"Content-Type:application/x-www-form-urlencoded;charset=utf-8\r\n".
	"X-FORWARDED-FOR:202.203.204.129\r\n".
	"CLIENT-IP:202.203.204.189\r\n".
	"Accept-Language:zh-CN,zh;q=0.8\r\n".
	"Cookie:PHPSESSID=".hash('crc32',date("G",time()).$CURUSER['username'])."\r\n".
	"Origin:http://you.0w0.ca\r\n",
	'content' => $content,'timeout'=>5)); 
$xoption = stream_context_create($option); 
$var_dump2=@file_get_contents("http://you.0w0.ca/marisa/io?".lcg_value(), false, $xoption);
IF($DEBUG)PRINT $var_dump2;
$var_dump2=json_decode($var_dump2, true);
return html_entity_decode($var_dump2["message"],ENT_QUOTES,"UTF-8");
}

/*
function resimsimi($keyword){
global $CURUSER;
if (!$keyword)return 'Hi!';
$header = array();
$header[]= 'Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, application/x-shockwave-flash, text/html, * '. '/* '; 
$header[]= 'Accept-Language: zh-cn '; 
$header[]= 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:13.0) Gecko/20100101 Firefox/13.0.1'; 
$header[]= 'Host: www.simsimi.com'; 
$header[]= 'Connection: Keep-Alive '; 
$header[]= 'Cookie: JSESSIONID='.hash('crc32',date("G",time()).$CURUSER['username']);

$Ref="http://www.simsimi.com/talk.htm?lc=ch";
$Ch = curl_init();
$Options = array(
CURLOPT_HTTPHEADER => $header,
CURLOPT_URL => 'http://www.simsimi.com/func/req?msg='.urlencode($keyword).'&lc=ch', 
CURLOPT_RETURNTRANSFER => true,
CURLOPT_REFERER => $Ref,
CURLOPT_CONNECTTIMEOUT_MS=>10*1000,
CURLOPT_TIMEOUT_MS=>50*1000,
);
curl_setopt_array($Ch, $Options);
$Message = json_decode(curl_exec($Ch),true);
curl_close($Ch);
if($Message['result']=='100')return $Message['response'];
}
*/

$shbox_text=get_single_value('shoutbox',"text","WHERE userid=$userid and type='sb' and auto = 0 and id=".sqlesc(0+$_POST["id"]));

if (preg_match("/\[@$robid\]/i", $shbox_text)||$_COOKIE["c_secure_chat_marisa"]=='yes'&&$shbox_text||!$Cache->get_value('marisa_'.hash('crc32',date('Ymd').$CURUSER["passkey"]))&&$_POST["id"]==hash('crc32',date('Ymd').$CURUSER["passkey"])){
$Cache->cache_value('marisa_'.hash('crc32',date('Ymd').$CURUSER["passkey"]),'yes', 3600*24*7);
$s = preg_replace("/\[[^\]]*\]/i", '', $shbox_text);
$s = preg_replace("/^ *| *$/i", '', $s);
//$s = str_replace(array('teach','forget','application','status','hint','exit','  '), '', $s);
$schat=resimsimi($s);
if(!$schat)$schat=resimsimi($s);
if(!$schat)$schat=resimsimi($s);
if(!$schat)$schat=resimsimi($s);
if(!$schat)$schat=resimsimi($s);
if(!$schat)$schat='系统错误: 可能你点击按钮的姿势不正确!?';

$schat = preg_replace("/(<br>|&nbsp;)/i", ' ', $schat);
$schat=($s?$s.' <--- ':'').$schat;
sql_query("INSERT INTO shoutbox (userid,date,text,type,ip,auto ) VALUES (".sqlesc($robid).",".sqlesc(TIMENOW+2).",".sqlesc("[@".$userid."]  ".$schat).",".sqlesc("sb").",".sqlesc(getip()).",".sqlesc($userid).")");
sql_query("delete from shoutbox WHERE id = ". sqlesc(0+$_POST["id"]));
//sql_query("UPDATE shoutbox SET auto=" . sqlesc($userid) . " WHERE id = ". sqlesc(0+$_POST["id"]));
if($_COOKIE["c_secure_chat_marisa"]!='yes')setcookie("c_secure_chat_marisa",'yes');
die("succeed");
}
//setcookie("c_secure_chat_marisa",'no');
die("error");


function resimsimi($keyword){
if (!$keyword)return '主人抱抱!! >ω<';
$keyword=urlencode(str_replace(ICEMUSUME_ENTRYPOINT, "",$keyword));
$Message = json_decode(file_get_contents_function('http://api.simsimi.com/request.p?key=ae752867-ab2f-4827-ab64-88aebed49a1c&lc=ch&text='.$keyword),true);
if($Message['result']=='100')return $Message['response'];
else return false;
}

function resimsimi_debug($keyword){
if (!$keyword)return '主人抱抱!! >ω<';
$keyword=urlencode(str_replace(ICEMUSUME_ENTRYPOINT, "",$keyword));
$Message = file_get_contents_function('http://api.simsimi.com/request.p?key=ae752867-ab2f-4827-ab64-88aebed49a1c&lc=ch&text='.$keyword);
print $Message;
}
/*
ae752867-ab2f-4827-ab64-88aebed49a1c //BB
392746b0-a5c1-4b8f-8239-5a837da67de2
*/