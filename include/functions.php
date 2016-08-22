<?php
//header("Connection: keep-alive");
# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER'))
die('Hacking attempt!');
include_once($rootpath . 'include/config.php');
include_once($rootpath . 'include/globalfunctions.php');
include_once($rootpath . 'classes/class_advertisement.php');
require_once($rootpath . get_langfile_path("functions.php"));
//$tstart = getmicrotime();

function convertip($ip) {
    //IP数据文件路径
    $dat_path = 'include/qqwry.dat';
    //检查IP地址
    if(!ereg("^([0-9]{1,3}.){3}[0-9]{1,3}$", $ip)){
        return ;
    }
    //打开IP数据文件
    if(!$fd = @fopen($dat_path, 'rb')){
        return;
    }
    //分解IP进行运算，得出整形数
    $ip = explode('.', $ip);
    $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
    //获取IP数据索引开始和结束位置
    $DataBegin = fread($fd, 4);
    $DataEnd = fread($fd, 4);
    $ipbegin = implode('', unpack('L', $DataBegin));
    if($ipbegin < 0) $ipbegin += pow(2, 32);
    $ipend = implode('', unpack('L', $DataEnd));
    if($ipend < 0) $ipend += pow(2, 32);
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;
    $BeginNum = 0;
    $EndNum = $ipAllNum;
    //使用二分查找法从索引记录中搜索匹配的IP记录
    while($ip1num>$ipNum || $ip2num<$ipNum) {
        $Middle= intval(($EndNum + $BeginNum) / 2);
        //偏移指针到索引位置读取4个字节
        fseek($fd, $ipbegin + 7 * $Middle);
        $ipData1 = fread($fd, 4);
        if(strlen($ipData1) < 4) {
            fclose($fd);
            return;
        }
        //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
        $ip1num = implode('', unpack('L', $ipData1));
        if($ip1num < 0) $ip1num += pow(2, 32);
        //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
        if($ip1num > $ipNum) {
            $EndNum = $Middle;
            continue;
        }
        //取完上一个索引后取下一个索引
        $DataSeek = fread($fd, 3);
        if(strlen($DataSeek) < 3) {
            fclose($fd);
            return;
        }
        $DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
        fseek($fd, $DataSeek);
        $ipData2 = fread($fd, 4);
        if(strlen($ipData2) < 4) {
            fclose($fd);
            return;
        }
        $ip2num = implode('', unpack('L', $ipData2));
        if($ip2num < 0) $ip2num += pow(2, 32);
        //没找到提示未知
        if($ip2num < $ipNum) {
            if($Middle == $BeginNum) {
                fclose($fd);
                return;
            }
            $BeginNum = $Middle;
        }
    }
    //下面的代码读晕了，没读明白，有兴趣的慢慢读
    $ipFlag = fread($fd, 1);
    if($ipFlag == chr(1)) {
        $ipSeek = fread($fd, 3);
        if(strlen($ipSeek) < 3) {
            fclose($fd);
            return;
        }
        $ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
        fseek($fd, $ipSeek);
        $ipFlag = fread($fd, 1);
    }
    if($ipFlag == chr(2)) {
        $AddrSeek = fread($fd, 3);
        if(strlen($AddrSeek) < 3) {
            fclose($fd);
            return;
        }
        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return;
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while(($char = fread($fd, 1)) != chr(0))
            $ipAddr2 .= $char;
        $AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
        fseek($fd, $AddrSeek);
        while(($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;
    } else {
        fseek($fd, -1, SEEK_CUR);
        while(($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;
        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return;
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while(($char = fread($fd, 1)) != chr(0)){
            $ipAddr2 .= $char;
        }
    }
    fclose($fd);
    //最后做相应的替换操作后返回结果
	
	$ipAddr1=mb_convert_encoding($ipAddr1,"utf-8","gb2312".',auto');
	$ipAddr2=mb_convert_encoding($ipAddr2,"utf-8","gb2312".',auto');
	
       if(preg_match('/http/i', $ipAddr2)) {
        $ipAddr2 = '';
    }
	
	 if(preg_match('/大学/i', $ipAddr1)) {
        $ipAddr2 = '';
		$ipAddr1 =preg_replace("/大学.*/is", "", $ipAddr1)."大学";
    }elseif(preg_match('/大学/i', $ipAddr2)) {
        $ipAddr1 = '';
		$ipAddr2 =preg_replace("/大学.*/is", "", $ipAddr2)."大学";
    }
	
	if(preg_match('/学院/i', $ipAddr1)) {
        $ipAddr2 = '';
		$ipAddr1 =preg_replace("/学院.*/is", "", $ipAddr1)."学院";
    }elseif(preg_match('/学院/i', $ipAddr2)) {
        $ipAddr1 = '';
		$ipAddr2 =preg_replace("/学院.*/is", "", $ipAddr2)."学院";
    }
	
	
	
	
    $ipaddr = str_replace(" ", "",$ipAddr1.$ipAddr2);
	
	
	
    $ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
    $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
    $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
    if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
         return "IPV4";
    }   
	
    return ($ipaddr);
}

function convertipv6($ip,$clean=true) {
	global $Cache;
	if(preg_match("/[^0-9a-fA-F\:\.]/i",$ip))return '未知';
 	if (!$addr = $Cache->get_value('convertipv6_'.$ip.'_orgin')){
		exec("include\\whereis.exe $ip && exit", $info);
		$info[1]=mb_convert_encoding($info[1],"utf-8","gbk".',auto');
		$addr = explode(" ", $info[1]);
		$Cache->cache_value('convertipv6_'.$ip.'_orgin', $addr, 3600*24*3);
	}
	
$ipAddr1=$addr[1];
$ipAddr2=$addr[2];
if(!$clean)return  $ipAddr1." ".$ipAddr2;
 if(preg_match('/大学/i', $ipAddr1)) {
        $ipAddr2 = '';
    }elseif(preg_match('/大学/i', $ipAddr2)) {
        $ipAddr1 = '';
    }
	
	if(preg_match('/学院/i', $ipAddr1)) {
        $ipAddr2 = '';
    }elseif(preg_match('/学院/i', $ipAddr2)) {
        $ipAddr1 = '';
    }
	$ipaddr = str_replace(" ", "",$ipAddr1.$ipAddr2);
	$ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
    $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
    $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
    if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
         return "未知";
    }   
	
	

return $ipaddr;

}
function get_langfolder_cookie()
{
	global $deflang;
	if (!isset($_COOKIE["c_secure_lang_folder"])) {
		return $deflang;
	} else {
		$langfolder_array = get_langfolder_list();
		foreach($langfolder_array as $lf)
		{
			if($lf == $_COOKIE["c_secure_lang_folder"])
			return $_COOKIE["c_secure_lang_folder"];
		}
		return $deflang;
	}
}

  function matcheslink($FLAG)
{
$autodata="";
 preg_match_all('/<a.*?(?: |\\t|\\r|\\n)?href=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>(.+?)<\/a.*?>/sim', $FLAG, $strResult, PREG_PATTERN_ORDER);
 for($i = 0; $i < count($strResult[1]); $i++)
{
$autodata .= $strResult[2][$i]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

} 


$autodata = preg_replace("/<[^b][^>]*>|<b[^r][^>]*>/","", $autodata ); 
//$autodata = ereg_replace("<a [^>]*>|<\/a>","", $autodata); 

return $autodata;
 
}




function get_tracker_state($MODEMAX)
{
	global $lang_functions;
	$act=$lang_functions['TRACKER_MODE'].":<b  id=\"trackerstate\" >";	
	if ($MODEMAX == 6)$act .= "IPV6";
	elseif ($MODEMAX == 5)$act .="6TO4";
	elseif ($MODEMAX == 4)$act .="IPV4";
	else $act .= "无活动";
	$act .="</b>";
	return $act;
}


function get_user_lang($user_id)
{
	$lang = mysql_fetch_assoc(sql_query("SELECT site_lang_folder FROM language LEFT JOIN users ON language.id = users.lang WHERE language.site_lang=1 AND users.id= ". sqlesc($user_id) ." LIMIT 1"));
	return $lang['site_lang_folder'];
}

function get_langfile_path($script_name ="", $target = false, $lang_folder = "")
{
	global $CURLANGDIR;
	$CURLANGDIR = get_langfolder_cookie();
	if($lang_folder == "")
	{
		$lang_folder = $CURLANGDIR;
	}
	return "lang/" . ($target == false ? $lang_folder : "_target") ."/lang_". ( $script_name == "" ? substr(strrchr($_SERVER['SCRIPT_NAME'],'/'),1) : $script_name);
}

function get_row_count($table, $suffix = "")
{
	$r = sql_query("SELECT COUNT(*) FROM $table $suffix") or sqlerr(__FILE__, __LINE__);
	$a = mysql_fetch_row($r) or die(mysql_error());
	return $a[0];
}

function get_row_sum($table, $field, $suffix = "",$cachethis=0)
{

	global $Cache;
	
 	if (!$cachethis||!$a = $Cache->get_value('get_row_sum_'.$table.'_'.$field.'_'.$suffix)){
	
		$r = sql_query("SELECT SUM($field) FROM $table $suffix") or sqlerr(__FILE__, __LINE__);
		$a = mysql_fetch_row($r) or die(mysql_error());
		
	if ($cachethis)$Cache->cache_value('get_row_sum_'.$table.'_'.$field.'_'.$suffix, $a, $cachethis);
	}
	
	
	//$r = sql_query("SELECT SUM($field) FROM $table $suffix") or sqlerr(__FILE__, __LINE__);
	//$a = mysql_fetch_row($r) or die(mysql_error());
	return $a[0];
}

function get_single_value($table, $field, $suffix = ""){
	$a = @mysql_fetch_row(sql_query("SELECT $field FROM $table $suffix LIMIT 1"));// or sqlerr(__FILE__, __LINE__);
	//$a = mysql_fetch_row($r) or die(mysql_error());
	if ($a) {
		return $a[0];
	} else {
		return false;
	}
}

function stdmsg($heading, $text, $htmlstrip = false)
{
	if ($htmlstrip) {
		$heading = htmlspecialchars(trim($heading));
		$text = htmlspecialchars(trim($text));
	}
	print("<table align=\"center\" class=\"main\" width=\"500\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td class=\"embedded\">\n");
	if ($heading)
	print("<h2>".$heading."</h2>\n");
	print("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr><td class=\"text\">");
	print($text . "</td></tr></table></td></tr></table>\n");
}

function stdmsgnoprint($heading, $text, $htmlstrip = false)
{
	$print="";
	if ($htmlstrip) {
		$heading = htmlspecialchars(trim($heading));
		$text = htmlspecialchars(trim($text));
	}
	$print .=("<table align=\"center\" class=\"main\" width=\"500\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td class=\"embedded\">\n");
	if ($heading)
	$print .=("<h2>".$heading."</h2>\n");
	$print .=("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr><td class=\"text\">");
	$print .=($text . "</td></tr></table></td></tr></table>\n");
	return $print;
}


function stderr($heading, $text, $htmlstrip = true, $head = true, $foot = true, $die = true)
{
	if ($head) stdhead();
	stdmsg($heading, $text, $htmlstrip);
	if ($foot) stdfoot();
	if ($die) die;
}

function sqlerr($file = '', $line = '')
{
	print("<table border=\"0\" bgcolor=\"blue\" align=\"left\" cellspacing=\"0\" cellpadding=\"10\" style=\"background: blue;\">" .
	"<tr><td class=\"embedded\"><font color=\"white\"><h1>SQL Error</h1>\n" .
	"<b>" . mysql_error() . ($file != '' && $line != '' ? "<p>in $file, line $line</p>" : "") . "</b></font></td></tr></table>");
	die;
}

function format_quotesnopicback($user='')
{if($user)return "[reply:$user]";
else
return '[b]这里曾经有个附件[/b]'
;}	

function format_quotesnopic($s)
{		//$s = preg_replace("/\[quote=(.+?)\][\W\w]*\[\/quote\]?/i", format_quotesnopicback('\\1'), $s,-1);
		preg_match_all('/\\[quote.*?\\]/i', $s, $result, PREG_PATTERN_ORDER);
		if(($i=count($result[0]))>=5)while($i-->5)$s = preg_replace("/\[quote[^\]]*\]((?!\[\/?quote).)*\[\/quote\]/is", '[color=red](前略,天国的母亲大人......)[/color]', $s,-1);
		
		
		$s = preg_replace("/\[img\]([^\s'\"<>]+?)\[\/img\]/i", format_urls('\\1', $newtab), $s,-1);
		$s = preg_replace("/\[img=([^\s'\"<>]+?)\]/i", format_urls('\\1', $newtab), $s,-1);
		$s = preg_replace("/\[@([0-9]+?)\]/ei", "format_quotesnopicback(\\1)", $s);
		$s = preg_replace("/\[size=([0-9]+?)]/i", '[size=2]', $s);
		$s = preg_replace("/\[attach\]([0-9a-zA-z][0-9a-zA-z]*)\[\/attach\]/ies","format_quotesnopicback()", $s, -1);
		return $s;
}		

function at_user_message($text,$identify,$title,$type,$owner){
			global $Cache;
			if($type == "offer"){$subject='你在候选('.$title.')中被@';}
			elseif($type == "request"){$subject='你在求种('.$title.')中被@';}
			elseif($type == "topic"){$subject='你在帖子('.$title.')中被@';}
			else{$subject='你可能在种子('.$title.')中被@';}
			$haveowner=false;
			preg_match_all( "/\[@([0-9]+?)\]/ei",$text,$useridget); 
			$useridget[1] = array_unique($useridget[1]);
			for($i = 0;$i < min(10,count($useridget[1])); $i++){
			sql_query("Delete from messages where identify  =  ".sqlesc($identify)."  and sender = 0 and receiver=".$useridget[1][$i]);
			sql_query("INSERT INTO messages (sender, receiver, subject, msg, added,identify) VALUES(0, " . $useridget[1][$i] . ", ".sqlesc($subject).",'', ".sqlesc(date("Y-m-d H:i:s")).",".sqlesc('@'.$identify).")");
			$Cache->delete_value('user_'.$useridget[1][$i].'_unread_message_count');
			$Cache->delete_value('user_'.$useridget[1][$i].'_inbox_count');
			if($owner==$useridget[1][$i])$haveowner=true;
			}
			return $haveowner;
}
		
function format_quotes($s)
{
	global $lang_functions;
	preg_match_all('/\\[quote.*?\\]/i', $s, $result, PREG_PATTERN_ORDER);
	$openquotecount = count($openquote = $result[0]);
	preg_match_all('/\\[\/quote[\\]]?/i', $s, $result, PREG_PATTERN_ORDER);
	$closequotecount = count($closequote = $result[0]);

	if ($openquotecount != $closequotecount) return $s; // quote mismatch. Return raw string...

	// Get position of opening quotes
	$openval = array();
	$pos = -1;

	foreach($openquote as $val)
	$openval[] = $pos = strpos($s,$val,$pos+1);

	// Get position of closing quotes
	$closeval = array();
	$pos = -1;

	foreach($closequote as $val)
	$closeval[] = $pos = strpos($s,$val,$pos+1);


	for ($i=0; $i < count($openval); $i++)
	if ($openval[$i] > $closeval[$i]) return $s; // Cannot close before opening. Return raw string...


	$s = preg_replace("/\\[quote\\]/i","<fieldset><legend> ".$lang_functions['text_quote']." </legend><br />",$s);
	$s = preg_replace("/\\[quote=(.+?)\\]/i", "<fieldset><legend> ".$lang_functions['text_quote'].": \\1 </legend><br />", $s);
	$s = preg_replace("/\\[\\/quote[\\]]?/i","</fieldset><br />",$s);
	return $s;
}

function print_attachment($dlkey, $enableimage = true, $imageresizer = true)
{
	global $Cache, $httpdirectory_attachment,$thumbnailtype_attachment,$BASEURLRSS;
	global $lang_functions;
	global $thispagewidthscreen;
	if (strlen($dlkey) == 32){
	if (!$row = $Cache->get_value('attachment_'.$dlkey.'_content')){
		$res = sql_query("SELECT * FROM attachments WHERE dlkey=".sqlesc($dlkey)." LIMIT 1") or sqlerr(__FILE__,__LINE__);
		$row = mysql_fetch_array($res);
		$Cache->cache_value('attachment_'.$dlkey.'_content', $row, 3600);
	}
	}
	if (!$row)
	{
		return "<div style=\"text-decoration: line-through; font-size: 7pt\">".$lang_functions['text_attachment_key'].$dlkey.$lang_functions['text_not_found']."</div>";
	}
	else{
	$id = $row['id'];
	if ($row['isimage'] == 1)
	{	
			if($row['iszip'] == 1)$bordercssnote="[有压缩]";
			
		if($thispagewidthscreen)$Scalethis=1030;
			else $Scalethis=920;	
			
			
		if ($enableimage){
			if ($row['thumb'] == 1&&$thumbnailtype_attachment != 'no'){
				$url =$httpdirectory_attachment."/".$row['location'].".thumb.jpg";
				 $bordercss="style='border: 2px dotted red; max-width:".$Scalethis."px'";
				 
		}
			else{
				$bordercss="style='max-width:".$Scalethis."px'";
				$url = $httpdirectory_attachment."/".$row['location'];
			}
			if($imageresizer == true)
				$onclick = " onclick=\"Previewurl('".$httpdirectory_attachment."/".$row['location']."');\"  ";
			else $onclick = "";
			
			$return = "<img onload=\"Scale(this,$Scalethis,0);\"  onerror=\"errorimg(this);\" id=\"attach".$id."\" alt=\"".htmlspecialchars($row['filename'])."\" src=\"".$BASEURLRSS.$url."\" $bordercss ". $onclick .  " onmouseover=\"domTT_activate(this, event, 'content', '".htmlspecialchars("<strong>".$lang_functions['text_size']."</strong>: ".mksize($row['filesize'])."<br />$bordercssnote".gettime($row['added']))."', 'styleClass', 'attach', 'x', findPosition(this)[0], 'y', findPosition(this)[1]-58);\" />";
		}
		else $return = "";
	}
	else
	{
		switch($row['filetype'])
		{
			case 'application/x-bittorrent': {
				$icon = "<img alt=\"torrent\" src=\"pic/attachicons/torrent.gif\" />";
				break;
			}
			case 'application/zip':{
				$icon = "<img alt=\"zip\" src=\"pic/attachicons/archive.gif\" />";
				break;
			}
			case 'application/rar':{
				$icon = "<img alt=\"rar\" src=\"pic/attachicons/archive.gif\" />";
				break;
			}
			case 'application/x-7z-compressed':{
				$icon = "<img alt=\"7z\" src=\"pic/attachicons/archive.gif\" />";
				break;
			}
			case 'application/x-gzip':{
				$icon = "<img alt=\"gzip\" src=\"pic/attachicons/archive.gif\" />";
				break;
			}
			case 'audio/mpeg':{
			}
			case 'audio/ogg':{
				$icon = "<img alt=\"audio\" src=\"pic/attachicons/audio.gif\" />";
				break;
			}
			case 'video/x-flv':{
				$icon = "<img alt=\"flv\" src=\"pic/attachicons/flv.gif\" />";
				break;
			}
			default: {
				$icon = "<img alt=\"other\" src=\"pic/attachicons/common.gif\" />";
			}
		}
		$return = "<div class=\"attach\">".$icon."&nbsp;&nbsp;<a href=\"".htmlspecialchars("getattachment.php?id=".$id."&dlkey=".$dlkey)."\" target=\"_blank\" id=\"attach".$id."\" onmouseover=\"domTT_activate(this, event, 'content', '".htmlspecialchars("<strong>".$lang_functions['text_downloads']."</strong>: ".number_format($row['downloads'])."<br />".gettime($row['added']))."', 'styleClass', 'attach', 'x', findPosition(this)[0], 'y', findPosition(this)[1]-58);\">".htmlspecialchars($row['filename'])."</a>&nbsp;&nbsp;<font class=\"size\">(".mksize($row['filesize']).")</font></div>";
	}
	return addTempCode($return);
	}
}

function addTempCode($value) {
	global $tempCode, $tempCodeCount;
	$tempCode[$tempCodeCount] = $value;
	$return = "<tempCode_$tempCodeCount>";
	$tempCodeCount++;
	return $return;
}

function formatAdUrl($adid, $url, $content, $newWindow=true)
{
	return formatUrl("adredir.php?id=".$adid."&amp;url=".rawurlencode($url), $newWindow, $content);
}

function urlv6v4format($url){
global $BASEURLV4V6;
if($return=str_replace(get_protocol_prefix().$BASEURLV4V6."/","",$url))return $return;
else return $url;
}

function formatUrl($url, $newWindow = false, $text = '', $linkClass = '',$nosidtidtag=true) {
GLOBAL $BASEHOST;
	if (!$text) {
		$text = $url;
	}
	if(!$nosidtidtag&&preg_match('/.*'.$BASEHOST.'.*(details|download)\.php\?id\=([0-9]+).*/', $url, $m))			return (format_seed($m[2]));
	elseif(!$nosidtidtag&&preg_match('/.*'.$BASEHOST.'.*forums\.php.*topicid\=([0-9]+).*/', $url, $m))				return (format_topic($m[1]));
	elseif(!$nosidtidtag&&preg_match('/.*'.$BASEHOST.'.*viewrequests\.php.*id\=([0-9]+).*/', $url, $m))				return (format_requests($m[1]));
	elseif(!$nosidtidtag&&preg_match('/.*'.$BASEHOST.'.*bet_gameinfo\.php.*showgames\=([0-9]+).*/', $url, $m))		return (format_bet_gameinfo($m[1]));
	elseif(!$nosidtidtag&&preg_match('/.*'.$BASEHOST.'.*bet_coupong\.php.*id\=([0-9]+).*/', $url, $m))				return (format_bet_gameinfo($m[1]));
	elseif(!$nosidtidtag&&preg_match('/.*'.$BASEHOST.'.*bet_odds\.php.*id\=([0-9]+).*/', $url, $m))				return (format_bet_gameinfo($m[1]));	
	elseif(!$nosidtidtag&&($formatCodePhp2urldata=formatCodePhp2url($url))!='error')						return "[url=".urlv6v4format($url)."]"."外站传送门@".($formatCodePhp2urldata)."[/url]";
	else
	return addTempCode("<a".($linkClass ? " class=\"$linkClass\"" : '')." href=\"".urlv6v4format($url)."\"" . ($newWindow==true? " target=\"_blank\"" : "").">$text</a>");
	
}
function formatCode($text) {
	global $lang_functions;
	return addTempCode("<br /><div class=\"codetop\">".$lang_functions['text_code']."</div><div class=\"codemain\">$text</div><br />");
}


function formatCodePhp2img($text,$must=false) {
	global $Cache,$BASEURLV4;
	if ((!$return = $Cache->get_value('formatCodePhp2img_'.md5($text)))||$must){	
		if((!$return=get_single_value("formatcodephp2img ", "returncode", "WHERE md5 =".sqlesc(md5($text))))||$must){
				if(!$must){
					sql_query("INSERT INTO formatcodephping (type, org, md5 ) VALUES ('formatcodephp2img'," . sqlesc($text). ",".sqlesc(md5($text)).") ON DUPLICATE KEY update type=values(type) ");
					$Cache->delete_value('here_now_have_no_mail');
					$Cache->cache_value('formatCodePhp2img_'.md5($text), $text, 30);
					return $text;
				}

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL,htmlspecialchars_decode($text));
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_NOBODY, 1);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS,10*1000); 
			curl_setopt($curl, CURLOPT_TIMEOUT_MS,50*1000); 
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_ENCODING, "gzip, deflate");
			curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
			curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
			curl_exec($curl);
			if(preg_match("/text\/html/is",$CURLINFO_CONTENT_TYPE=curl_getinfo($curl,CURLINFO_CONTENT_TYPE))||curl_getinfo($curl,CURLINFO_HTTP_CODE)>'200')
				$return="error";
			else 
				$return=RemoveXSS($text);
		
		
		
			if($return!=$text)$return="error";
			if($return!="error")
				sql_query("INSERT INTO formatcodephp2img (md5,returncode,time,org,ContentType) VALUES (".sqlesc(md5($text)).",".sqlesc($return).",".TIMENOW.",".sqlesc($text).",".sqlesc($CURLINFO_CONTENT_TYPE).")ON DUPLICATE KEY update returncode=values(returncode)");
				
				//curl_close($curl);
		}
		sql_query("INSERT INTO formatcodephp2img (md5 , returncode , time, org,ContentType) VALUES ( " . sqlesc(md5($text)) . " , " . sqlesc($return). " , ".TIMENOW." , " . sqlesc($text). " ,".sqlesc($CURLINFO_CONTENT_TYPE)." )   ON DUPLICATE KEY update time=values(time)");
		$Cache->cache_value('formatCodePhp2img_'.md5($text), $return, 3600*24);
		if($must)$Cache->delete_value('formatCodePhp2img_'.md5($text));
	}
	return $return;
}

function formatCodePhp2url($text,$must=false) {
	global $Cache,$BASEURLV4;
	if (!preg_match("/^https?:\/\//is",$text))return 'error';
	if ((!$return = $Cache->get_value('formatCodePhp2url_'.md5($text)))||$must){	
		if((!$return=get_single_value("formatcodephp2url", "returncode", "WHERE md5 =".sqlesc(md5($text))))||$must){
			$return='error';
			if(!$must){
				sql_query("INSERT INTO formatcodephping (type, org ,md5) VALUES ('formatcodephp2url'," . sqlesc($text). ",".sqlesc(md5($text)).") ON DUPLICATE KEY update type=values(type) ");
				$Cache->delete_value('here_now_have_no_mail');
				$Cache->cache_value('formatCodePhp2url_'.md5($text),'识别中'.$text, 30);
				return '识别中:'.$text;
			}
		  $texturl = ($text);
			//$texturl = htmlspecialchars_decode(str_replace(array('www.imdb.com','us.imdb.com'),'72.21.214.36',($text)));
			
			//print_r($typeconnect=get_headers($texturl,1));
			//$texturl=preg_match("/text\/html/is",$typeconnect['Content-Type'])?$texturl:'';
			
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $texturl);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_NOBODY, 0);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS,10*1000); 
			curl_setopt($curl, CURLOPT_TIMEOUT_MS,50*1000);
			curl_setopt($curl, CURLOPT_MAX_RECV_SPEED_LARGE,50*1024);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			$cookies[]='getchu_adalt_flag=getchu.com';
			$cookies[]='2b606_winduser=BVYHUgJQPVFbCgNWCgEFVARcA1xUWAABUVAJDQZTAg8FDgIEAwEIaA%3D%3D';//ck
			$cookies[]='0857d_winduser=DVdXCFBoBgcJAFBXVAQBXlMFDVcFBFVSBgMGAVcHVQRXXVVYB1I%3D';//2dgal
			curl_setopt($curl, CURLOPT_COOKIE, implode("; ", $cookies));
			curl_setopt($curl, CURLOPT_ENCODING, "gzip,deflate");
			curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
			curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
			$data = curl_exec($curl);

			$ishtml =preg_match("/text\/html/is",$CURLINFO_CONTENT_TYPE=curl_getinfo($curl,CURLINFO_CONTENT_TYPE))&&(curl_getinfo($curl,CURLINFO_HTTP_CODE)=='200');
			
			if($ishtml&&preg_match('/<title>(.*?)<\/title>/si', $data, $m)){
				if(preg_match('/charset=[^\w]?([-\w]+)/i',curl_getinfo($curl,CURLINFO_CONTENT_TYPE),$temp))
					$encoding=strtolower($temp[1]);
				elseif(preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i",$data,$temp))
					$encoding=strtolower($temp[1]);
				else $encoding='utf-8,gb2312,gbk';
				if($encoding=='gb18030')$encoding='gbk';
				//print $encoding;
					$m[1] = mb_convert_encoding($m[1], 'UTF-8',$encoding.',auto');//print $temp[1];
					$m[1] = str_replace("\n","",$m[1]);
					$m[1] = str_replace("\r","",$m[1]);
					if($m[1])$return=html_entity_decode($m[1],ENT_QUOTES);
				}
			
			
			
			
			
			if(!$return)$return='error';

				if($return!='error')
				sql_query("INSERT INTO formatcodephp2url (md5,returncode,time,org,ContentType)VALUES (".sqlesc(md5($text)).",". sqlesc($return).",".TIMENOW.",".sqlesc($text).",".sqlesc($CURLINFO_CONTENT_TYPE).") ON DUPLICATE KEY update returncode=values(returncode)");
			}
		sql_query("INSERT INTO formatcodephp2url (md5 , returncode , time ,org,ContentType) VALUES ( " . sqlesc(md5($text)) . " , " . sqlesc($return). " , ".TIMENOW." , " . sqlesc($text). " ,".sqlesc($CURLINFO_CONTENT_TYPE)." ) ON DUPLICATE KEY update time=values(time)");

		$Cache->cache_value('formatCodePhp2url_'.md5($text), $return, 3600*24);
		if($must)$Cache->delete_value('formatCodePhp2url_'.md5($text));
	}

	return htmlspecialchars($return);
}

function formatImg($srcimg, $enableImageResizer, $image_max_width, $image_max_height) {
	$src=formatCodePhp2img($srcimg);
	if($src=='error')
	return ("[url=".$srcimg."]无效图片地址:".$srcimg."[/url]");
	
	if($image_max_width)$image_max_width2="max-width:".$image_max_width."px;";
	if($image_max_height)$image_max_height2="max-height:".$image_max_height."px;";

	return addTempCode("<img src=\"$src\" style='".$image_max_width2.$image_max_height2."' " .($enableImageResizer ?  " onerror=\"errorimg(this);\"  onload=\"Scale(this,$image_max_width,$image_max_height);\" onclick=\"Preview(this);\" " : "onload=\"Scale(this,$image_max_width,$image_max_height);\" onerror=\"errorimg(this);\" ") .  " />");

	//return addTempCode("<img   src=\"$src\" class=\"loading\"" .($enableImageResizer ?  " onerror=\"errorimg(this);\"  onload=\"Scale(this,$image_max_width,$image_max_height);\" onclick=\"Preview(this);\"  " : "onload=\"deleteloading(this);\" onerror=\"errorimg(this);\" ") .  " />");
}

function formatFlash($srcimg, $width, $height) {
	$src=formatCodePhp2img($srcimg);
	if($src=='error')
	return ("[url=".$srcimg."]无效动画地址:".$srcimg."[/url]");
	if (!$width) {
		$width = 480;
	}
	if (!$height) {
		$height = 400;
	}
	return addTempCode("<object width=\"$width\" height=\"$height\"><param name=\"movie\" value=\"$src\" /><embed src=\"$src\" width=\"$width\" height=\"$height\" type=\"application/x-shockwave-flash\"></embed></object>");
}
function formatFlv($srcimg, $width, $height) {
	$src=formatCodePhp2img($srcimg);
	if($src=='error')
	return ("[url=".$srcimg."]无效动画地址:".$srcimg."[/url]");
	if (!$width) {
		$width = 480;
	}
	if (!$height) {
		$height = 360;
	}
	$height=$height+24;
	return addTempCode("
	<object width=\"$width\" height=\"$height\">
	<param name=\"movie\" value=\"flvplayer.swf?file=$src\" />
	<param name=\"allowFullScreen\" value=\"true\" />
	<embed src=\"flvplayer.swf?file=$src\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" width=\"$width\" height=\"$height\"></embed>
	
	</object>");
}
function format_urls($text, $newWindow = false,$nosidtidtag=true) {
	//return preg_replace("/((https?|ftp|gopher|news|telnet|mms|rtsp):\/\/([^()\[\]<>\s\'\"]|\[2001:[0-9a-zA-Z:]*\])+)/ei","formatUrl('\\1', ".($newWindow==true ? 1 : 0).", '', 'faqlink')", $text);
	return preg_replace("/((https?|ftp|gopher|news|telnet|mms|rtsp):\/\/(\[2001:[0-9a-zA-Z:]*\])?[^()<>\[\]\s\'\"]+)/ei","formatUrl('\\1', ".($newWindow==true ? 1 : 0).", '', 'faqlink','".$nosidtidtag."')", $text);
}


function RemoveXSS($val) {
	$trueurl=html_entity_decode($val,ENT_QUOTES);
	$trueurl2=preg_replace('/^.*[\\\\\/]+|[\?]+.*$/i', '', $trueurl);
	$trueurl3=parse_url($trueurl);
	if(preg_match("/cc98bar|mybar|mybarplus|attachments|usercss/i",$trueurl2))return $val;
	if(preg_match("/cc98bar|mybar|mybarplus|attachments|usercss/i",$trueurl))return $val;
	if(@file_exists($trueurl)||@file_exists($trueurl2)||@file_exists($trueurl3['host']))return 'error';
	return $val;
} 


function format_seed($seedkey){

	global $Cache;
	if (!$name = $Cache->get_value('seedkey'.$seedkey.'_content')){
		//$name = get_single_value("torrents", "name","WHERE  banned = 'no' and  id=".sqlesc($seedkey));
		$get_second_name=get_second_name();
		$torrents = mysql_fetch_assoc(sql_query("SELECT category , name ,audiocodec FROM torrents WHERE  banned = 'no' and  id=".sqlesc($seedkey)));	
		if($torrents['name'])
		$name = htmlspecialchars("[url=details.php?id=".$seedkey."]种子传送门@".$get_second_name['categories']['name'][$torrents["category"]].$get_second_name['audiocodec'][$torrents["audiocodec"]].$torrents['name']."[/url]");
		else $name="<b>悲剧！ID为".$seedkey."的种子不存在</b>";
		$Cache->cache_value('seedkey'.$seedkey.'_content', $name,600);
	}
	return $name;
}
	
function format_requests($requestid){
	global $Cache;
	if (!$name = $Cache->get_value('requests'.$requestid.'_content')){
		//$get_second_name=get_second_name();
		$requests = mysql_fetch_assoc(sql_query("SELECT request , amount FROM requests WHERE id=".sqlesc($requestid)));
		if($requests['request'])$name = htmlspecialchars("[url=viewrequests.php?id=".$requestid."]求种传送门@[悬赏{$requests['amount']}]".$requests['request']."[/url]");
		else $name="<b>悲剧！ID为".$requestid."的求种不存在</b>";
		$Cache->cache_value('requests'.$requestid.'_content', $name,600);
	}
	return $name;
}
	
function format_bet_gameinfo($gameid){
	global $Cache;
	if (!$name = $Cache->get_value('bet_gameinfo'.$gameid.'_content')){
		$requests = mysql_fetch_assoc(sql_query("SELECT heading FROM betgames WHERE id=".sqlesc($gameid)));
		if($requests['heading'])$name = htmlspecialchars("[url=bet_gameinfo.php?showgames=".$gameid."]竞猜传送门@".$requests['heading']."[/url]");
		else $name="<b>悲剧！ID为".$gameid."的竞猜不存在</b>";
		$Cache->cache_value('bet_gameinfo'.$gameid.'_content', $name,600);
	}
	return $name;
}	
	
	
function format_topic($topicid){
	global $Cache;
	if (!$name = $Cache->get_value('topicid'.$topicid.'_content')){
		$get_second_name=get_second_name();
		//$name = get_single_value("topics", "subject","WHERE id=".sqlesc($topicid));
		$topics = mysql_fetch_assoc(sql_query("SELECT subject , forumid FROM topics WHERE id=".sqlesc($topicid)));
		if($topics['subject'])$name = htmlspecialchars("[url=forums.php?action=viewtopic&topicid=".$topicid."]帖子传送门@".$get_second_name['forums'][$topics["forumid"]].$topics['subject']."[/url]");
		else $name="<b>悲剧！ID为".$topicid."的帖子不存在</b>";
		$Cache->cache_value('topicid'.$topicid.'_content', $name,600);
	}
	return $name;
}

function format_imdb($imdbwordid){
	global $Cache;
	if (!$name = $Cache->get_value('format_imdb'.$imdbwordid.'_content')){
		$imdbid=parse_imdb_id($imdbwordid);
		$fdouban = get_single_value("imdbdoubanurl", "imdb","WHERE douban=".sqlesc($imdbwordid));
		if($fdouban)$imdbid=parse_imdb_id($fdouban);
		$idtype = get_single_value("imdbdoubanurl", "douban","WHERE imdb=".sqlesc($imdbwordid))?'IMDB':'豆瓣';
		$get_second_name=get_second_name();
		$name='';
		$torrentslist = sql_query("SELECT id FROM torrents WHERE visible='yes' and url = ".sqlesc($imdbid)." order by seeders limit 5 ");
		while ($torrents = mysql_fetch_assoc($torrentslist)){
			if($torrents['id'])
			$name .= format_seed($torrents['id'])."<br>";
			}
		if(!$name) $name="<b>悲剧！IMDB编号为".$imdbwordid."的资源暂时不存在</b>";
		$Cache->cache_value('format_imdb'.$imdbwordid.'_content', $name,3600);
	}
	return $name;
}
	
	function format_hr($hr){
	return "<fieldset style='border-width: medium 0px 0px 0px;'><legend  align='center'>$hr</legend></fieldset>";
	}
	
function 	get_plain_username_at($id){
global $CURUSER;
if(!$id)return "<b>@游客</b>";
elseif($CURUSER[id]==$id)return "<b>@</b>".get_username($id,false,false,true,true,false,false, "", false , true,false);
else return "<b>@".get_plain_username($id)."</b>";
}
	
function format_comment($text, $strip_html = true, $xssclean = false, $newtab = false, $imageresizer = true, $image_max_width = 700, $enableimage = true, $enableflash = true , $imagenum = -1, $image_max_height = 0, $adid = 0,$enableattach=true)
{
	global $lang_functions;
	global $CURUSER, $SITENAME, $BASEURL, $enableattach_attachment,$BASEURLV6,$BASEURLV4,$BASEURLV4V6,$BASEHOSTAGO;
	global $tempCode, $tempCodeCount;
	global $thispagewidthscreen;
	if($thispagewidthscreen&&$image_max_width == 700)$image_max_width = 900;
	$tempCode = array();
	$tempCodeCount = 0;
	$imageresizer = $imageresizer ? 1 : 0;
	$s=$text;
	//$s =RemoveXSS($s);
	
	$s = preg_replace("/http:\/\/10\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\//is","http://".$BASEURLV4."/", $s);
	$s = str_replace("ti<x>tle",'title', $s);
	$s = str_replace("ti&lt;x&gt;tle",'title', $s);
	
	

	if ($strip_html) {
		$s = htmlspecialchars($s);
	}
	// Linebreaks
	$s = nl2br($s);

	if (strpos($s,"[code]") !== false && strpos($s,"[/code]") !== false) {
		$s = preg_replace("/\[code\](.+?)\[\/code\]/eis","formatCode('\\1')", $s);
	}

		if (strpos($s,"[c]") !== false && strpos($s,"[/c]") !== false) {
		$s = preg_replace("/\[c\](.+?)\[\/c\]/eis","addTempCode('\\1')", $s);
	}
	
	//$words = array($BASEURLV4."/",$BASEURLV6."/");
	$s = str_replace($BASEHOSTAGO,$BASEURLV4V6, $s);
	
	
	
	
	$originalBbTagArray = array('[site]','[*]', '[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[pre]', '[/pre]', '[/color]', '[/font]', '[/size]', '  ','[s]','[/s]','[hr]','[f]','[fr]','[fl]','[/f]','[/align]','[mask]','[/mask]');
	$replaceXhtmlTagArray = array(" ".$SITENAME." ", '<img class="listicon listitem" src="pic/trans.gif" alt="list" />', '<b>', '</b>', '<i>', '</i>', '<ins>', '</ins>', '<pre>', '</pre>', '</span>', '</font>', '</font>', ' &nbsp;','<del>','</del>','<hr width=80% align=center>','<marquee>','<marquee direction=right>','<marquee direction=left>','</marquee>','</div>','<span style="background-color: Black;">','</span>');
	$s = str_replace($originalBbTagArray, $replaceXhtmlTagArray, $s);
	
	$originalBbTagArray = array('[siteurl]','[siteurl4]', '[siteurl6]','[v4]', '[v6]', '[v]', '[siteurl46]', '[v46]');
	$replaceXhtmlTagArray = array('[url]'.get_protocol_prefix().$BASEURL.'[/url]','[url]'.get_protocol_prefix().$BASEURLV4.'[/url]','[url]'.get_protocol_prefix().$BASEURLV6.'[/url]','[url]'.get_protocol_prefix().$BASEURLV4.'[/url]','[url]'.get_protocol_prefix().$BASEURLV6.'[/url]','[url]'.get_protocol_prefix().$BASEURL.'[/url]','[url]'.get_protocol_prefix().$BASEURLV4V6.'[/url]','[url]'.get_protocol_prefix().$BASEURLV4V6.'[/url]');
	$s = str_replace($originalBbTagArray, $replaceXhtmlTagArray, $s);
	
	
	$s = preg_replace("/\[sid([0-9]+?)\]/ei", "format_seed('\\1')", $s);
	$s = preg_replace("/\[tid([0-9]+?)\]/ei", "format_topic('\\1')", $s);
	$s = preg_replace("/\[rid([0-9]+?)\]/ei", "format_requests('\\1')", $s);
	$s = preg_replace("/\[bid([0-9]+?)\]/ei", "format_bet_gameinfo('\\1')", $s);
	$s = preg_replace("/\[uid([0-9]+?)\]/ei", "get_username('\\1',0,1,1,1)", $s);
	//$s = preg_replace("/\[imdb([0-9]+?)\]/ei", "format_imdb('\\1')", $s);
	$s = preg_replace("/\[@([0-9]+?)\]/ei", "get_plain_username_at('\\1')", $s);
	$s = preg_replace("/\[reply:([0-9]+?)\]/ei", "get_plain_username_at('\\1')", $s);
	$s = preg_replace("/\[hr=(.+?)\]/ei", "format_hr('\\1')", $s);
	
	
	$originalBbTagArray = array("/\[font=([^\[\(&\\;]+?)\]/is", "/\[color=([#0-9a-z]{1,15})\]/is", "/\[color=([a-z]+)\]/is", "/\[size=([1-7])\]/is","/\[align=([^\[\(&\\;]+?)\]/is",);
	$replaceXhtmlTagArray = array("<font face=\"\\1\">", "<span style=\"color: \\1;\">", "<span style=\"color: \\1;\">", "<font size=\"\\1\">","<div style=\"text-align: \\1;\">");
	$s = preg_replace($originalBbTagArray, $replaceXhtmlTagArray, $s);

	if ($enableattach_attachment == 'yes' && $imagenum != 1&&$enableattach){
		$limit = -1;
		//$limit = 20;
		$s = preg_replace("/\[attach\]([0-9a-zA-z][0-9a-zA-z]*)\[\/attach\]/ies", "print_attachment('\\1', ".($enableimage ? 1 : 0).", ".($imageresizer ? 1 : 0).")", $s, $limit);
	}

	if ($enableimage) {
		//$s = preg_replace("/\[img\]([^\s\r\n\"'\(\)\<\>]+?(jpg|png|gif|bmp))\[\/img\]/ei", "formatImg('\\1',".$imageresizer.",".$image_max_width.",".$image_max_height.")", $s, $imagenum, $imgReplaceCount);
		//$s = preg_replace("/\[img=([^\s\r\n\"'\(\)\<\>]+?(jpg|png|gif|bmp))\]/ei", "formatImg('\\1',".$imageresizer.",".$image_max_width.",".$image_max_height.")", $s, ($imagenum != -1 ? max($imagenum-$imgReplaceCount, 0) : -1));
		//$s = preg_replace("/\[img\]([^\s\r\n\"'\<\>]+?(jpg|png|gif|bmp))\[\/img\]/ei", "formatImg('\\1',".$imageresizer.",".$image_max_width.",".$image_max_height.")", $s, $imagenum, $imgReplaceCount);
		//$s = preg_replace("/\[img=([^\s\r\n\"'\<\>]+?(jpg|png|gif|bmp))\]/ei", "formatImg('\\1',".$imageresizer.",".$image_max_width.",".$image_max_height.")", $s, ($imagenum != -1 ? max($imagenum-$imgReplaceCount, 0) : -1));
		
		$s = preg_replace("/\[img\]([^\s\r\n\"'\<\>]+?)\[\/img\]/ei", "formatImg('\\1',".$imageresizer.",".$image_max_width.",".$image_max_height.")", $s, $imagenum, $imgReplaceCount);
		$s = preg_replace("/\[img=([^\s\r\n\"'\<\>]+?)\]/ei", "formatImg('\\1',".$imageresizer.",".$image_max_width.",".$image_max_height.")", $s, ($imagenum != -1 ? max($imagenum-$imgReplaceCount, 0) : -1));
		$s = preg_replace("/\[imdb([0-9]+?)\]/ei", "format_imdb('\\1')", $s);
	} else {
		$s = preg_replace("/\[img\]([^\s'\"<>]+?)\[\/img\]/i", format_urls('\\1', $newtab), $s,-1);
		$s = preg_replace("/\[img=([^\s'\"<>]+?)\]/i", format_urls('\\1', $newtab), $s,-1);
		$s = preg_replace("/\[imdb([0-9]+?)\]/ei",'', $s);
	}

	// [flash,500,400]http://www/image.swf[/flash]
	if (strpos($s,"[flash") !== false) { //flash is not often used. Better check if it exist before hand
		if ($enableflash) {
			$s = preg_replace("/\[flash(\,([1-9][0-9]*)\,([1-9][0-9]*))?\]((http|ftp):\/\/[^\s\r\n\"'\(\)\<\>]+)\[\/flash\]/ei", "formatFlash('\\4', '\\2', '\\3')", $s);
		} else {
			$s = preg_replace("/\[flash(\,([1-9][0-9]*)\,([1-9][0-9]*))?\]((http|ftp):\/\/[^\s'\"<>]+)\[\/flash\]/i", '\\4', $s);
		}
	}
	//[flv,320,240]http://www/a.flv[/flv]
	if (strpos($s,"[flv") !== false) { //flv is not often used. Better check if it exist before hand
		if ($enableflash) {
			$s = preg_replace("/\[flv(\,([1-9][0-9]*)\,([0-9][0-9]*))?\]((http|ftp):\/\/[^\s\r\n\"'\(\)\<\>]+)\[\/flv\]/ei", "formatFlv('\\4', '\\2', '\\3')", $s);
		} else {
			$s = preg_replace("/\[flv(\,([1-9][0-9]*)\,([1-9][0-9]*))?\]((http|ftp):\/\/[^\s'\"<>]+)\[\/flv\]/i", '\\4', $s);
		}
	}

	// [url=http://www.example.com]Text[/url]
	if ($adid) {
		$s = preg_replace("/\[url=([^\[\s\r\n\"'\(\)\<\>]+?)\](.+?)\[\/url\]/ei", "formatAdUrl(".$adid." ,'\\1', '\\2', ".($newtab==true ? 1 : 0).", 'faqlink')", $s);
	} else {
		$s = preg_replace("/\[url=([^\[\s\r\n\"'\(\)\<\>]+?)\](.+?)\[\/url\]/ei", "formatUrl('\\1', ".($newtab==true ? 1 : 0).", '\\2', 'faqlink')", $s);
	}

	// [url]http://www.example.com[/url]
	$s = preg_replace("/\[url\]([^\[\s\r\n\"'\(\)\<\>]+?)\[\/url\]/ei","formatUrl('\\1', ".($newtab==true ? 1 : 0).", '', 'faqlink')", $s);



		
		$s = format_urls($s, $newtab,false);
		$s = preg_replace("/\[url=([^\[\s\r\n\"'\(\)\<\>]+?)\](.+?)\[\/url\]/ei", "formatUrl('\\1', ".($newtab==true ? 1 : 0).", '\\2', 'faqlink')", $s);
		
	// Quotes
	if (strpos($s,"[quote") !== false && strpos($s,"[/quote") !== false) { //format_quote is kind of slow. Better check if [quote] exists beforehand
		$s = format_quotes($s);
	}
	
	$s = preg_replace("/\[em([1-9][0-9]*)\]/ie", "(\\1 < 192 ? '<img src=\"pic/smilies/\\1.gif\" alt=\"[em\\1]\" />' : '[em\\1]')", $s);
	reset($tempCode);
	$j = 0;
	while(count($tempCode) || $j > 5) {
		foreach($tempCode as $key=>$code) {
			$s = str_replace("<tempCode_$key>", $code, $s, $count);
			if ($count) {
				unset($tempCode[$key]);
				$i = $i+$count;
			}
		}
		$j++;
	}
	return $s;
}

function highlight($search,$subject,$hlstart='<b><font class="striking">',$hlend="</font></b>") 
{

	$srchlen=strlen($search);    // lenght of searched string
	if ($srchlen==0) return $subject;
	$find = $subject;
	while ($find = stristr($find,$search)) {    // find $search text in $subject -case insensitiv
		$srchtxt = substr($find,0,$srchlen);    // get new search text
		$find=substr($find,$srchlen);
		$subject = str_replace($srchtxt,"$hlstart$srchtxt$hlend",$subject);    // highlight founded case insensitive search text
	}
	return $subject;
}

function get_user_class()
{
	global $CURUSER;
	return $CURUSER["class"];
}

function get_user_class_name($class, $compact = false, $b_colored = false, $I18N = false,$namecolour="")
{
	static $en_lang_functions;
	static $current_user_lang_functions;
	if (!$en_lang_functions) {
		require(get_langfile_path("functions.php",false,"en"));
		$en_lang_functions = $lang_functions;
	}

	if(!$I18N) {
		$this_lang_functions = $en_lang_functions;
	} else {
		if (!$current_user_lang_functions) {
			require(get_langfile_path("functions.php"));
			$current_user_lang_functions = $lang_functions;
		}
		$this_lang_functions = $current_user_lang_functions;
	}
	
	$class_name = "";
	switch ($class)
	{
		case UC_PEASANT: {$class_name = $this_lang_functions['text_peasant']; break;}
		case UC_USER: {$class_name = $this_lang_functions['text_user']; break;}
		case UC_POWER_USER: {$class_name = $this_lang_functions['text_power_user']; break;}
		case UC_ELITE_USER: {$class_name = $this_lang_functions['text_elite_user']; break;}
		case UC_CRAZY_USER: {$class_name = $this_lang_functions['text_crazy_user']; break;}
		case UC_INSANE_USER: {$class_name = $this_lang_functions['text_insane_user']; break;}
		case UC_VETERAN_USER: {$class_name = $this_lang_functions['text_veteran_user']; break;}
		case UC_EXTREME_USER: {$class_name = $this_lang_functions['text_extreme_user']; break;}
		case UC_Warehouse: {$class_name = $this_lang_functions['text_Warehouse']; break;}////
		case UC_ULTIMATE_USER: {$class_name = $this_lang_functions['text_ultimate_user']; break;}
		case UC_NEXUS_MASTER: {$class_name = $this_lang_functions['text_nexus_master']; break;}
		case UC_VIP: {$class_name = $this_lang_functions['text_vip']; break;}
		case UC_UPLOADER: {$class_name = $this_lang_functions['text_uploader']; break;}
		case UC_RETIREE: {$class_name = $this_lang_functions['text_retiree']; break;}
		case UC_FORUM_MODERATOR: {$class_name = $this_lang_functions['text_forum_moderator']; break;}
		case UC_MODERATOR: {$class_name = $this_lang_functions['text_moderators']; break;}
		case UC_ADMINISTRATOR: {$class_name = $this_lang_functions['text_administrators']; break;}
		case UC_SYSOP: {$class_name = $this_lang_functions['text_sysops']; break;}
		case UC_STAFFLEADER: {$class_name = $this_lang_functions['text_staff_leader']; break;}
	}
	
	switch ($class)
	{
		case UC_PEASANT: {$class_name_color = $en_lang_functions['text_peasant']; break;}
		case UC_USER: {$class_name_color = $en_lang_functions['text_user']; break;}
		case UC_POWER_USER: {$class_name_color = $en_lang_functions['text_power_user']; break;}
		case UC_ELITE_USER: {$class_name_color = $en_lang_functions['text_elite_user']; break;}
		case UC_CRAZY_USER: {$class_name_color = $en_lang_functions['text_crazy_user']; break;}
		case UC_INSANE_USER: {$class_name_color = $en_lang_functions['text_insane_user']; break;}
		case UC_VETERAN_USER: {$class_name_color = $en_lang_functions['text_veteran_user']; break;}
		case UC_EXTREME_USER: {$class_name_color = $en_lang_functions['text_extreme_user']; break;}
		case UC_ULTIMATE_USER: {$class_name_color = $en_lang_functions['text_ultimate_user']; break;}
		case UC_NEXUS_MASTER: {$class_name_color = $en_lang_functions['text_nexus_master']; break;}
		case UC_Warehouse: {$class_name_color = $en_lang_functions['text_Warehouse']; break;}
		case UC_VIP: {$class_name_color = $en_lang_functions['text_vip']; break;}
		case UC_UPLOADER: {$class_name_color = $en_lang_functions['text_uploader']; break;}
		case UC_RETIREE: {$class_name_color = $en_lang_functions['text_retiree']; break;}
		case UC_FORUM_MODERATOR: {$class_name_color = $en_lang_functions['text_forum_moderator']; break;}
		case UC_MODERATOR: {$class_name_color = $en_lang_functions['text_moderators']; break;}
		case UC_ADMINISTRATOR: {$class_name_color = $en_lang_functions['text_administrators']; break;}
		case UC_SYSOP: {$class_name_color = $en_lang_functions['text_sysops']; break;}
		case UC_STAFFLEADER: {$class_name_color = $en_lang_functions['text_staff_leader']; break;}
	}
	
	$class_name = ( $compact == true ? str_replace(" ", "",$class_name) : $class_name);
	if ($class_name) return ($b_colored == true ? "<b class='" . str_replace(" ", "",$class_name_color) . "_Name' $namecolour>" . $class_name . "</b>" : $class_name);
}

function is_valid_user_class($class)
{
	return is_numeric($class) && floor($class) == $class && $class >= UC_PEASANT && $class <= UC_STAFFLEADER;
}

function int_check($value,$stdhead = true, $stdfood = true, $die = true, $log = true) {
	global $lang_functions;
	global $CURUSER;
	if (is_array($value))
	{
		foreach ($value as $val) int_check ($val);
	}
	else
	{
		if (!is_valid_id($value)) {
			$msg = "Invalid ID Attempt: Username: ".$CURUSER["username"]." - UserID: ".$CURUSER["id"]." - UserIP : ".getip()."@".$_SERVER['REQUEST_URI'].'@'.$_SERVER['HTTP_REFERER'];
			if ($log)
				write_log($msg,'mod');

			if ($stdhead)
				stderr($lang_functions['std_error'],$lang_functions['std_invalid_id']);
			else
			{
				print ("<h2>".$lang_functions['std_error']."</h2><table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr><td class=\"text\">");
				print ($lang_functions['std_invalid_id']."</td></tr></table>");
			}
			if ($stdfood)
				stdfoot();
			if ($die)
				die;
		}
		else
			return true;
	}
}

function is_valid_id($id)
{
	return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}


//-------- Begins a main frame
function begin_main_frame($caption = "", $center = false, $width = 100,$per=false)
{	global $thispagewidthscreen;
	
	$tdextra = "";
	if ($caption)
	print("<h2>".$caption."</h2>");

	if ($center)
	$tdextra .= " align=\"center\"";
	if(!$width)$width="0";
	elseif($thispagewidthscreen)$width="98%";
	else $width = 940 * $width /100;


	print("<table class=\"main\" width=\"".$width."\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" .
	"<tr><td class=\"embedded\" $tdextra>");
}

function end_main_frame()
{
	print("</td></tr></table>\n");
}

function begin_frame($caption = "", $center = false, $padding = 10, $width="100%", $caption_center="left")
{
	$tdextra = "";

	if ($center)
	$tdextra .= " align=\"center\"";

	print(($caption ? "<h2 align=\"".$caption_center."\">".$caption."</h2>" : "") . "<table width=\"".$width."\" border=\"1\" cellspacing=\"0\" cellpadding=\"".$padding."\">" . "<tr><td class=\"text\" $tdextra>\n");

}

function begin_frameindex($caption = "", $center = false, $padding = 10, $width="100%", $caption_center="left")
{
	$tdextra = "";

	if ($center)
	$tdextra .= " align=\"center\"";
	
	

	print(($caption ? "<h2 class=\"index\" align=\"".$caption_center."\"><span class=\"index\">".$caption."</span></h2>" : "") . "<table class=\"index\" width=\"".$width."\" border=\"1\" cellspacing=\"0\" cellpadding=\"".$padding."\">" . "<tr><td class=\"text\" $tdextra>\n");

}

function end_frame()
{
	print("</td></tr></table>\n");
}

function begin_table($fullwidth = false, $padding = 5,$id='')
{
	$width = "";

	if ($fullwidth)
	$width .= " width=50%";
	print("<table id='{$id}'   class=\"main".$width."\" border=\"1\" cellspacing=\"0\" cellpadding=\"".$padding."\">");
}

function end_table()
{
	print("</table>\n");
}

//-------- Inserts a smilies frame
//         (move to globals)

function insert_smilies_frame()
{
	global $lang_functions;
	begin_frame($lang_functions['text_smilies'], true);
	begin_table(false, 5);
	print("<tr><td class=\"colhead\">".$lang_functions['col_type_something']."</td><td class=\"colhead\">".$lang_functions['col_to_make_a']."</td></tr>\n");
	for ($i=1; $i<192; $i++) {
		print("<tr><td>[em$i]</td><td><img src=\"pic/smilies/".$i.".gif\" alt=\"[em$i]\" /></td></tr>\n");
	}
	end_table();
	end_frame();
}

function get_ratio_color($ratio)
{
	if ($ratio < 0.1) return "#ff0000";
	if ($ratio < 0.2) return "#ee0000";
	if ($ratio < 0.3) return "#dd0000";
	if ($ratio < 0.4) return "#cc0000";
	if ($ratio < 0.5) return "#bb0000";
	if ($ratio < 0.6) return "#aa0000";
	if ($ratio < 0.7) return "#990000";
	if ($ratio < 0.8) return "#880000";
	if ($ratio < 0.9) return "#770000";
	if ($ratio < 1) return "#660000";
	return "";
}

function get_slr_color($ratio)
{	if ($ratio < 0||$ratio > 0.4) return "";
	if ($ratio < 0.025) return "#ff0000";
	if ($ratio < 0.05) return "#ee0000";
	if ($ratio < 0.075) return "#dd0000";
	if ($ratio < 0.1) return "#cc0000";
	if ($ratio < 0.125) return "#bb0000";
	if ($ratio < 0.15) return "#aa0000";
	if ($ratio < 0.175) return "#990000";
	if ($ratio < 0.2) return "#880000";
	if ($ratio < 0.225) return "#770000";
	if ($ratio < 0.25) return "#660000";
	if ($ratio < 0.275) return "#550000";
	if ($ratio < 0.3) return "#440000";
	if ($ratio < 0.325) return "#330000";
	if ($ratio < 0.35) return "#220000";
	if ($ratio < 0.375) return "#110000";
	return "";
}

function get_elapsed_time($ts,$shortunit = false)
{
	global $lang_functions;
	$mins = floor(abs(TIMENOW - $ts) / 60);
	$hours = floor($mins / 60);
	$mins -= $hours * 60;
	$days = floor($hours / 24);
	$hours -= $days * 24;
	$months = floor($days / 30);
	$days2 = $days - $months * 30;
	$years = floor($days / 365);
	$months -= $years * 12;
	$t = "";
	if ($years > 0)
	return $years.($shortunit ? $lang_functions['text_short_year'] : $lang_functions['text_year'] . add_s($year)) ."&nbsp;".$months.($shortunit ? $lang_functions['text_short_month'] : $lang_functions['text_month'] . add_s($months));
	if ($months > 0)
	return $months.($shortunit ?  $lang_functions['text_short_month'] : $lang_functions['text_month'] . add_s($months)) ."&nbsp;".$days2.($shortunit ? $lang_functions['text_short_day'] : $lang_functions['text_day'] . add_s($days2));
	if ($days > 0)
	return $days.($shortunit ? $lang_functions['text_short_day'] : $lang_functions['text_day'] . add_s($days))."&nbsp;".$hours.($shortunit ? $lang_functions['text_short_hour'] : $lang_functions['text_hour'] . add_s($hours));
	if ($hours > 0)
	return $hours.($shortunit ? $lang_functions['text_short_hour'] : $lang_functions['text_hour'] . add_s($hours))."&nbsp;".$mins.($shortunit ? $lang_functions['text_short_min'] : $lang_functions['text_min'] . add_s($mins));
	if ($mins > 0)
	return $mins.($shortunit ? $lang_functions['text_short_min'] : $lang_functions['text_min'] . add_s($mins));
	return "&lt; 1".($shortunit ? $lang_functions['text_short_min'] : $lang_functions['text_min']);
}

function textbbcode($form,$text,$content="",$hastitle=false, $col_num = 130)
{
	global $lang_functions;
	global $subject, $BASEURL, $CURUSER, $enableattach_attachment,$useatuser;
?>

<script type="text/javascript">
//<![CDATA[
var b_open = 0;
var i_open = 0;
var u_open = 0;
var color_open = 0;
var list_open = 0;
var quote_open = 0;
var html_open = 0;
var s_open = 0;
var code_open = 0;
var f_open = 0;

var myAgent = navigator.userAgent.toLowerCase();
var myVersion = parseInt(navigator.appVersion);

var is_ie = (userAgent.indexOf('msie') != -1) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);//((myAgent.indexOf("msie") != -1) && (myAgent.indexOf("opera") == -1));
var is_nav = ((myAgent.indexOf('mozilla')!=-1) && (myAgent.indexOf('spoofer')==-1)
&& (myAgent.indexOf('compatible') == -1) && (myAgent.indexOf('opera')==-1)
&& (myAgent.indexOf('webtv') ==-1) && (myAgent.indexOf('hotjava')==-1));

var is_win = ((myAgent.indexOf("win")!=-1) || (myAgent.indexOf("16bit")!=-1));
var is_mac = (myAgent.indexOf("mac")!=-1);
var bbtags = new Array();
function cstat() {
	var c = stacksize(bbtags);
	if ( (c < 1) || (c == null) ) {c = 0;}
	if ( ! bbtags[0] ) {c = 0;}
	if ( c > 0)
	{document.<?php echo $form?>.tagcount.value = "Tags: "+c;}
	else
	{document.<?php echo $form?>.tagcount.value = "Close Tags";}
	
}
function stacksize(thearray) {
	for (i = 0; i < thearray.length; i++ ) {
		if ( (thearray[i] == "") || (thearray[i] == null) || (thearray == 'undefined') ) {return i;}
	}
	return thearray.length;
}
function pushstack(thearray, newval) {
	arraysize = stacksize(thearray);
	thearray[arraysize] = newval;
}
function popstackd(thearray) {
	arraysize = stacksize(thearray);
	theval = thearray[arraysize - 1];
	return theval;
}
function popstack(thearray) {
	arraysize = stacksize(thearray);
	theval = thearray[arraysize - 1];
	delete thearray[arraysize - 1];
	return theval;
}
function closeall() {
	if (bbtags[0]) {
		while (bbtags[0]) {
			tagRemove = popstack(bbtags)
			/*if ( (tagRemove != 'color') ) {
				doInsert("[/"+tagRemove+"]", "", false);
				eval("document.<?php echo $form?>." + tagRemove + ".value = ' " + tagRemoveto.UpperCase() + " '");
				eval(tagRemove + "_open = 0");
			} else {
				doInsert("[/"+tagRemove+"]", "", false);
			}*/
			eval("document.<?php echo $form?>." + tagRemove + ".value = ' " + tagRemove.toUpperCase() + " '");
			eval("document.<?php echo $form?>." + tagRemove + ".selectedIndex = ' 0 '");
			doInsert("[/"+tagRemove+"]", "", false);
			eval(tagRemove + "_open = 0");
			cstat();
			return;
		}
	}
	document.<?php echo $form?>.tagcount.value = "Close Tags";
	bbtags = new Array();
	document.<?php echo $form?>.<?php echo $text?>.focus();
}

function closealltags() {
//return ;
while (bbtags[0])add_code("[/"+popstack(bbtags)+"]");
				}

function add_code(NewCode) {
	document.<?php echo $form?>.<?php echo $text?>.value += NewCode;
	document.<?php echo $form?>.<?php echo $text?>.focus();
}
function alterfont(theval, thetag) {
	if (theval == 0) return;
	if(doInsert("[" + thetag + "=" + theval + "]", "[/" + thetag + "]", true)) pushstack(bbtags, thetag);
	//document.<?php echo $form?>.color.selectedIndex = 0;
	else eval("document.<?php echo $form?>." + thetag + ".selectedIndex = ' 0 '");
	cstat();
}

function tag_url(PromptURL, PromptTitle, PromptError) {
	var FoundErrors = '';
	var enterURL = prompt(PromptURL, "http://");
	var enterTITLE = prompt(PromptTitle, "");
	if (!enterURL || enterURL=="") {FoundErrors += " " + PromptURL + ",";}
	if (!enterTITLE) {FoundErrors += " " + PromptTitle;}
	if (FoundErrors) {alert(PromptError+FoundErrors);return;}
	doInsert("[url="+enterURL+"]"+enterTITLE+"[/url]", "", false);
}

function tag_list(PromptEnterItem, PromptError) {
	var FoundErrors = '';
	var enterTITLE = prompt(PromptEnterItem, "");
	if (!enterTITLE) {FoundErrors += " " + PromptEnterItem;}
	if (FoundErrors) {alert(PromptError+FoundErrors);return;}
	doInsert("[*]"+enterTITLE+"", "", false);
}

function tag_image(PromptImageURL, PromptError) {
	var FoundErrors = '';
	var enterURL = prompt(PromptImageURL, "http://");
	if (!enterURL || enterURL=="http://") {
		alert(PromptError+PromptImageURL);
		return;
	}
	doInsert("[img]"+enterURL+"[/img]", "", false);
}

function tag_hr() {
	doInsert("[hr]", "", false);
}

function tag_list2() {
	doInsert("[*]", "", false);
}


function tag_extimage(content) {
	doInsert(content, "", false);
}

function tag_email(PromptEmail, PromptError) {
	var emailAddress = prompt(PromptEmail, "");
	if (!emailAddress) {
		alert(PromptError+PromptEmail);
		return;
	}
	doInsert("[email]"+emailAddress+"[/email]", "", false);
}

function doInsert(ibTag, ibClsTag, isSingle)
{
	var isClose = false;
	var obj_ta = document.<?php echo $form?>.<?php echo $text?>;
	if ((obj_ta.selectionStart||obj_ta.selectionEnd)||obj_ta.selectionStart === 0)
	{
		var startPos = obj_ta.selectionStart;
		var endPos = obj_ta.selectionEnd;
		if(startPos!=endPos&&isSingle){ 
		obj_ta.value = obj_ta.value.substring(0, startPos) + ibTag + obj_ta.value.substring(startPos,endPos) + ibClsTag + obj_ta.value.substring(endPos, obj_ta.value.length);
		obj_ta.selectionEnd = endPos + ibTag.length;
		obj_ta.selectionStart= startPos + ibTag.length;
		}else{
		obj_ta.value = obj_ta.value.substring(0, startPos) +obj_ta.value.substring(startPos,endPos)+ ibTag + obj_ta.value.substring(endPos, obj_ta.value.length);
		obj_ta.selectionEnd = endPos + ibTag.length;//+obj_ta.value.substring(startPos,endPos).length;
		obj_ta.selectionStart= obj_ta.selectionEnd;
		if(isSingle)isClose = true;
		}
		//obj_ta.selectionStart=obj_ta.selectionEnd;
	}else if (is_ie&&obj_ta.isTextEdit){
			obj_ta.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null)
			{
				if(rng.text.length > 0)
				rng.text = ibTag + rng.text + ibClsTag;
				else if(isSingle) {isClose = true;rng.text = rng.text+ibTag;}
				else rng.text =  rng.text + ibTag;
			}
		
		
	}
	else
	{
		if(isSingle) isClose = true;
		obj_ta.value += ibTag;
	}
	obj_ta.focus();
	//obj_ta.value = obj_ta.value.replace(/ /, " ");
	return isClose;
}



function trans() {
var str = "";
rtf.focus();
rtf.document.body.innerHTML = "";
rtf.document.execCommand("paste");
str = rtf.document.body.innerHTML;
str =html_trans(str);
doInsert(str, "", false);
}

function html_trans(str) {
str = str.replace(/\r/g,"");
str = str.replace(/on(load|click|dbclick|mouseover|mousedown|mouseup)="[^"]+"/ig,"");
str = str.replace(/<script[^>]+?>([\w\W]+?)<\/script>/ig,"");
str = str.replace(/<a[^>]+href="([^"]+)"[^>]*>([\w\W]+?)<\/a>/ig,"[url=$1]$2[/url]");
str = str.replace(/<font[^>]+color=\"([^ \(\">]+)[^>]*>([\w\W]+?)<\/font>/ig,"[color=$1]$2[/color]");
str = str.replace(/<font[^>]+color=([^ \(\">]+)[^>]*>([\w\W]+?)<\/font>/ig,"[color=$1]$2[/color]");
str = str.replace(/<span style=\"color: ([^ \(\";>]+)[^>]*>([\w\W]+?)<\/span>/ig,"[color=$1]$2[/color]");
str = str.replace(/<font[^>]+size=([^ \">]+)[^>]*>([\w\W]+?)<\/font>/ig,"[size=$1]$2[/size]");
str = str.replace(/<font[^>]+size=\"([^ \">]+)[^>]*>([\w\W]+?)<\/font>/ig,"[size=$1]$2[/size]");
str = str.replace(/<img[^>]+src="([^ "]+)[^>]*>/ig,"[img]$1[/img]");
str = str.replace(/\[color=rgb\](.+?)\[\/color\]/ig,"$1");


//str = str.replace(/<([\/]?)b[^>]*>/ig,"[$1b]");
//str = str.replace(/<([\/]?)strong[^>]*>/ig,"[$1b]");
//str = str.replace(/<([\/]?)u[^>]*>/ig,"[$1u]");
//str = str.replace(/<([\/]?)i[^>]*>/ig,"[$1i]");

str = str.replace(/<b[^>]*>([\w\W]+?)<\/b[^>]*>/ig,"[b]$1[\/b]");
str = str.replace(/<strong[^>]*>([\w\W]+?)<\/strong[^>]*>/ig,"[b]$1[\/b]");
str = str.replace(/<u[^>]*>([\w\W]+?)<\/u[^>]*>/ig,"[u]$1[\/u]");
str = str.replace(/<i[^>]*>([\w\W]+?)<\/i[^>]*>/ig,"[i]$1[\/i]");


str = str.replace(/&nbsp;/g," ");
str = str.replace(/&amp;/g,"&");
str = str.replace(/&quot;/g,"\"");
str = str.replace(/&lt;/g,"<");
str = str.replace(/&gt;/g,">");
str = str.replace(/<legend>.*<\/legend>(.+?)<\/fieldset>/ig,"[quote]$1[\/quote]");
str = str.replace(/<class="codetop">.*<\/div>(.+?)<\/div>/ig,"[code]$1[\/code]");

str = str.replace(/<br[^>]*>/ig,"\n");
str = str.replace(/<[^>]+?>/g,"");
str = str.replace(/\[url=([^\]]+)\]\n(\[img\][^\[]+?\[\/img\])\n\[\/url\]/g,"[url=$1]$2[/url]");
str = str.replace(/\n+/g,"\n");

return str;
}



function winop()
{
windop = window.open("moresmilies.php?form=<?php echo $form?>&text=<?php echo $text?>","mywin","height=500,width=500,resizable=no,scrollbars=yes");
}

function winoppause()
{
windop = window.open("moresmilies.php?action=winoppause&form=<?php echo $form?>&text=<?php echo $text?>","mywin","height=500,width=500,resizable=no,scrollbars=yes");
}

function winop2()
{
url=document.getElementById('imdburl').value;
url=encodeURI(url);
url2=document.getElementById('name').value;
url2=encodeURI(url2);

if(url)windop = window.open("moresmilies.php?form=<?php echo $form?>&text=imdburl&keywords="+url,"mywin","height=500,width=500,resizable=no,scrollbars=yes");
else if(url2)windop = window.open("moresmilies.php?form=<?php echo $form?>&text=imdburl&keywords="+url2,"mywin","height=500,width=500,resizable=no,scrollbars=yes");
}

function imdbtypechange()  
{  
var douban=/douban/; 
var imdb=/imdb/; 
var url=document.getElementById('imdburl').value
if (douban.exec(url)) document.getElementById('imdbnum').value=2 ;
else if (imdb.exec(url)) document.getElementById('imdbnum').value=1; } 

function tagspreview(obj){
if (!is_ie || is_ie >= 7){
	var poststr = encodeURIComponent( document.<?php echo $form?>.<?php echo $text?>.value );
	var obj_ta =ajax.posts('preview.php','body='+poststr+'&action=light');
	$('#lightbox').css({"zoom":"100%"});
	$('#lightbox').html(obj_ta);
	$('#curtain').fadeIn();
	$('#lightbox').fadeIn();	
	}else if(typeof(preview) == "function")
	preview(obj);
}


function simpletag(thetag)
{
	var tagOpen = eval(thetag + "_open");
	if (tagOpen == 0) {
		if(doInsert("[" + thetag + "]", "[/" + thetag + "]", true))
		{
			eval(thetag + "_open = 1");
			eval("document.<?php echo $form?>." + thetag + ".value += '*'");
			pushstack(bbtags, thetag);
			cstat();
		}
	}
	else {
		lastindex = 0;
		for (i = 0; i < bbtags.length; i++ ) {
			if ( bbtags[i] == thetag ) {
				lastindex = i;
			}
		}

		while (bbtags[lastindex]) {
			tagRemove = popstack(bbtags);
			doInsert("[/" + tagRemove + "]", "", false)
			if ((tagRemove != 'COLOR') ){
				eval("document.<?php echo $form?>." + tagRemove + ".value = '" + tagRemove.toUpperCase() + "'");
				eval(tagRemove + "_open = 0");
			}
		}
		cstat();
	}
}
//]]>
</script>
<table width="100%" cellspacing="0" cellpadding="5" border="0">
<tr><td align="left" colspan="2">
<table cellspacing="1" cellpadding="2" border="0">
<tr style='margin:0px;padding:0px'>
<td class="embedded" ><input style="font-weight: bold;font-size:11px; margin-right:0px" type="button" name="b" value="B" onclick="javascript: simpletag('b')" /></td>
<td class="embedded" ><input class="codebuttons" style="font-style: italic;font-size:11px;margin-right:0px" type="button" name="i" value="I" onclick="javascript: simpletag('i')" /></td>
<td class="embedded" ><input class="codebuttons" style="text-decoration: underline;font-size:11px;margin-right:0px" type="button" name="u" value="U" onclick="javascript: simpletag('u')" /></td>
<td class="embedded" ><input class="codebuttons" style="text-decoration: line-through;font-size:11px;margin-right:0px" type="button" name="s" value="S" onclick="javascript: simpletag('s')" /></td>
<td class="embedded"><input class="codebuttons" style="font-size:11px;margin-right:0px" type="button" style="font-size:11px;margin-right:3px" name="list" value="*" onclick="tag_list2()" /></td>
<td class="embedded" ><input class="codebuttons" style="font-size:11px;margin-right:0px" type="button" name="hr" value="---" onclick="javascript: tag_hr()" /></td>
<td class="embedded"><input class="codebuttons" style="font-size:11px;margin-right:0px" type="button" name="quote" value="QUOTE" onclick="javascript: simpletag('quote')" /></td>
<td class="embedded"><input class="codebuttons" style="font-size:11px;margin-right:3px" type="button" name="code" value="CODE" onclick="javascript: simpletag('code')" /></td>
<?/*<td class="embedded"><input class="codebuttons" style="font-size:11px;margin-right:3px" type="button" name="UUB" title="复制时请使用鼠标右键复制" value="快捷粘贴(IE)" onclick="javascript:trans()" /></td>*/?>
<td class="embedded"><input class="codebuttons" style="font-size:11px;margin-right:3px" type="button" name="UUB" title="复制时请使用鼠标右键复制" value="粘贴" onclick="javascript:winoppause()" /></td>

<?php
print("<td class=\"embedded\"><input class=\"codebuttons\" style=\"font-size:11px;margin-right:0px\" type=\"button\" name='url' value='URL' onclick=\"javascript:tag_url('" . $lang_functions['js_prompt_enter_url'] . "','" . $lang_functions['js_prompt_enter_title'] . "','" . $lang_functions['js_prompt_error'] . "')\" /></td>");
print("<td class=\"embedded\"><input class=\"codebuttons\" style=\"font-size:11px;margin-right:0px\" type=\"button\" name=\"IMG\" value=\"IMG\" onclick=\"javascript: tag_image('" . $lang_functions['js_prompt_enter_image_url'] . "','" . $lang_functions['js_prompt_error'] . "')\" /></td>");
//print("<td class=\"embedded\"><input type=\"button\" style=\"font-size:11px;margin-right:3px\" name=\"list\" value=\"List\" onclick=\"tag_list('" . addslashes($lang_functions['js_prompt_enter_item']) . "','" . $lang_functions['js_prompt_error'] . "')\" /></td>");
?>
<td class="embedded"><input style="font-size:11px;margin-right:3px" type="button" onclick='javascript:closeall();' name='tagcount' value="Close Tags" /></td>
<td class="embedded"><input style="font-size:11px;margin-right:3px" type="button" onclick='javascript:tagspreview(this.parentNode);'  value="预览" /></td>
<td class="embedded">
<select class="med codebuttons" style="margin-right:0px;width:50px;" name='align' onchange="alterfont(this.options[this.selectedIndex].value, 'align')">
<option value="0">对齐</option>
<option value="left">左对齐</option>
<option value="center">居中</option>
<option value="right">右对齐</option>
</select>
</td>
<td class="embedded"><select class="med codebuttons" style="margin-right:0px;" name='color' onchange="alterfont(this.options[this.selectedIndex].value, 'color')">
<option value='0'><?php echo $lang_functions['select_color'] ?></option>
<option style="background-color:LightPink" value="LightPink">胖次粉</option>
<option style="background-color:Pink" value="Pink">圆神粉</option>
<option style="background-color:Crimson" value="Crimson">节操红</option>
<option style="background-color:LavenderBlush" value="LavenderBlush">淡紫红</option>
<option style="background-color:PaleVioletRed" value="PaleVioletRed">雏莓红</option>
<option style="background-color:HotPink" value="HotPink">文乃粉</option>
<option style="background-color:DeepPink" value="DeepPink">深粉红</option>
<option style="background-color:MediumVioletRed" value="MediumVioletRed">罗兰红</option>
<option style="background-color:Orchid" value="Orchid">兰花紫</option>
<option style="background-color:Thistle" value="Thistle">纯正蓟</option>
<option style="background-color:Plum" value="Plum">浅色紫</option>
<option style="background-color:Violet" value="Violet">罗兰紫</option>
<option style="background-color:Magenta" value="Magenta">八云紫</option>
<option style="background-color:Fuchsia" value="Fuchsia">海棠紫</option>
<option style="background-color:DarkMagenta" value="DarkMagenta">深洋红</option>
<option style="background-color:Purple" value="Purple">蔷薇紫</option>
<option style="background-color:MediumOrchid" value="MediumOrchid">兰花紫</option>
<option style="background-color:DarkViolet" value="DarkViolet">暗罗紫</option>
<option style="background-color:DarkOrchid" value="DarkOrchid">暗兰紫</option>
<option style="background-color:Indigo" value="Indigo">荡漾紫</option>
<option style="background-color:BlueViolet" value="BlueViolet">龙胆紫</option>
<option style="background-color:MediumPurple" value="MediumPurple">中色紫</option>
<option style="background-color:MediumSlateBlue" value="MediumSlateBlue">中板蓝</option>
<option style="background-color:SlateBlue" value="SlateBlue">板岩蓝</option>
<option style="background-color:DarkSlateBlue" value="DarkSlateBlue">蓝莓紫</option>
<option style="background-color:Lavender" value="Lavender">熏衣紫</option>
<option style="background-color:GhostWhite" value="GhostWhite">幽灵白</option>
<option style="background-color:Blue" value="Blue">吾王蓝</option>
<option style="background-color:MediumBlue" value="MediumBlue">中立蓝</option>
<option style="background-color:MidnightBlue" value="MidnightBlue">午夜蓝</option>
<option style="background-color:DarkBlue" value="DarkBlue">暗蓝色</option>
<option style="background-color:Navy" value="Navy">海军蓝</option>
<option style="background-color:RoyalBlue" value="RoyalBlue">皇家蓝</option>
<option style="background-color:CornflowerBlue" value="CornflowerBlue">矢车蓝</option>
<option style="background-color:LightSteelBlue" value="LightSteelBlue">亮钢蓝</option>
<option style="background-color:LightSlateGray" value="LightSlateGray">亮石灰</option>
<option style="background-color:SlateGray" value="SlateGray">石板灰</option>
<option style="background-color:DodgerBlue" value="DodgerBlue">道奇蓝</option>
<option style="background-color:AliceBlue" value="AliceBlue">爱丽蓝</option>
<option style="background-color:SteelBlue" value="SteelBlue">钢铁青</option>
<option style="background-color:LightSkyBlue" value="LightSkyBlue">亮天蓝</option>
<option style="background-color:SkyBlue" value="SkyBlue">天依蓝</option>
<option style="background-color:DeepSkyBlue" value="DeepSkyBlue">胖次蓝</option>
<option style="background-color:LightBlue" value="LightBlue">水亮蓝</option>
<option style="background-color:PowderBlue" value="PowderBlue">火药青</option>
<option style="background-color:CadetBlue" value="CadetBlue">军服蓝</option>
<option style="background-color:Azure" value="Azure">天蔚蓝</option>
<option style="background-color:LightCyan" value="LightCyan">淡草青</option>
<option style="background-color:PaleTurquoise" value="PaleTurquoise">万宝绿</option>
<option style="background-color:Cyan" value="Cyan">纯正青</option>
<option style="background-color:Aqua" value="Aqua">浅草绿</option>
<option style="background-color:DarkTurquoise" value="DarkTurquoise">暗宝绿</option>
<option style="background-color:DarkSlateGray" value="DarkSlateGray">暗瓦灰</option>
<option style="background-color:DarkCyan" value="DarkCyan">暗绿青</option>
<option style="background-color:Teal" value="Teal">水鸭绿</option>
<option style="background-color:MediumTurquoise" value="MediumTurquoise">中宝绿</option>
<option style="background-color:LightSeaGreen" value="LightSeaGreen">浅海绿</option>
<option style="background-color:Turquoise" value="Turquoise">绿宝石</option>
<option style="background-color:Aquamarine" value="Aquamarine">宝石碧</option>
<option style="background-color:MediumAquamarine" value="MediumAquamarine">中宝碧</option>
<option style="background-color:MediumSpringGreen" value="MediumSpringGreen">中春绿</option>
<option style="background-color:MintCream" value="MintCream">奶油白</option>
<option style="background-color:SpringGreen" value="SpringGreen">春风绿</option>
<option style="background-color:MediumSeaGreen" value="MediumSeaGreen">中海绿</option>
<option style="background-color:SeaGreen" value="SeaGreen">海洋绿</option>
<option style="background-color:Honeydew" value="Honeydew">蜜瓜绿</option>
<option style="background-color:LightGreen" value="LightGreen">淡草绿</option>
<option style="background-color:PaleGreen" value="PaleGreen">星石翠</option>
<option style="background-color:DarkSeaGreen" value="DarkSeaGreen">暗海绿</option>
<option style="background-color:LimeGreen" value="LimeGreen">深光绿</option>
<option style="background-color:Lime" value="Lime">闪光绿</option>
<option style="background-color:ForestGreen" value="ForestGreen">言和绿</option>
<option style="background-color:Green" value="Green">节操绿</option>
<option style="background-color:DarkGreen" value="DarkGreen">绅士绿</option>
<option style="background-color:Chartreuse" value="Chartreuse">查特绿</option>
<option style="background-color:LawnGreen" value="LawnGreen">早苗绿</option>
<option style="background-color:GreenYellow" value="GreenYellow">绿黄色</option>
<option style="background-color:DarkOliveGreen" value="DarkOliveGreen">甲基绿</option>
<option style="background-color:YellowGreen" value="YellowGreen">草黄绿</option>
<option style="background-color:OliveDrab" value="OliveDrab">橄榄褐</option>
<option style="background-color:Beige" value="Beige">米灰棕</option>
<option style="background-color:LightGoldenrodYellow" value="LightGoldenrodYellow">亮菊黄</option>
<option style="background-color:Ivory" value="Ivory">象牙白</option>
<option style="background-color:LightYellow" value="LightYellow">蛋碎黄</option>
<option style="background-color:Yellow" value="Yellow">学姐黄</option>
<option style="background-color:Olive" value="Olive">橄榄黄</option>
<option style="background-color:DarkKhaki" value="DarkKhaki">叽布黄</option>
<option style="background-color:LemonChiffon" value="LemonChiffon">柠檬绸</option>
<option style="background-color:PaleGoldenrod" value="PaleGoldenrod">麒麟黄</option>
<option style="background-color:Khaki" value="Khaki">卡叽黄</option>
<option style="background-color:Gold" value="Gold">克拉金</option>
<option style="background-color:Cornsilk" value="Cornsilk">玉米黄</option>
<option style="background-color:Goldenrod" value="Goldenrod">菊花黄</option>
<option style="background-color:DarkGoldenrod" value="DarkGoldenrod">暗菊黄</option>
<option style="background-color:FloralWhite" value="FloralWhite">开花白</option>
<option style="background-color:OldLace" value="OldLace">蕾丝黄</option>
<option style="background-color:Wheat" value="Wheat">小麦黄</option>
<option style="background-color:Moccasin" value="Moccasin">鹿皮黄</option>
<option style="background-color:Orange" value="Orange">伊藤橙</option>
<option style="background-color:PapayaWhip" value="PapayaWhip">木瓜黄</option>
<option style="background-color:BlanchedAlmond" value="BlanchedAlmond">杏仁白</option>
<option style="background-color:NavajoWhite" value="NavajoWhite">土著白</option>
<option style="background-color:AntiqueWhite" value="AntiqueWhite">古董白</option>
<option style="background-color:Tan" value="Tan">苦荞茶</option>
<option style="background-color:BurlyWood" value="BurlyWood">硬木棕</option>
<option style="background-color:Bisque" value="Bisque">陶坯黄</option>
<option style="background-color:DarkOrange" value="DarkOrange">世纪橙</option>
<option style="background-color:Linen" value="Linen">亚麻白</option>
<option style="background-color:Peru" value="Peru">秘鲁金</option>
<option style="background-color:PeachPuff" value="PeachPuff">桃肉粉</option>
<option style="background-color:SandyBrown" value="SandyBrown">沙棕色</option>
<option style="background-color:Chocolate" value="Chocolate">巧克棕</option>
<option style="background-color:SaddleBrown" value="SaddleBrown">马鞍棕</option>
<option style="background-color:Seashell" value="Seashell">海贝白</option>
<option style="background-color:Sienna" value="Sienna">黄土赭</option>
<option style="background-color:LightSalmon" value="LightSalmon">鲑鱼粉</option>
<option style="background-color:Coral" value="Coral">珊瑚红</option>
<option style="background-color:OrangeRed" value="OrangeRed">橙红色</option>
<option style="background-color:DarkSalmon" value="DarkSalmon">深鲜红</option>
<option style="background-color:Tomato" value="Tomato">番茄红</option>
<option style="background-color:MistyRose" value="MistyRose">浅玫瑰</option>
<option style="background-color:Salmon" value="Salmon">鲑鱼红</option>
<option style="background-color:Snow" value="Snow">纯雪白</option>
<option style="background-color:LightCoral" value="LightCoral">淡珊红</option>
<option style="background-color:RosyBrown" value="RosyBrown">玫瑰红</option>
<option style="background-color:IndianRed" value="IndianRed">印度红</option>
<option style="background-color:Red" value="Red">火神红</option>
<option style="background-color:Brown" value="Brown">纯正棕</option>
<option style="background-color:FireBrick" value="FireBrick">火砖红</option>
<option style="background-color:DarkRed" value="DarkRed">文乃红</option>
<option style="background-color:Maroon" value="Maroon">凛酱红</option>
<option style="background-color:White" value="White">大雪白</option>
<option style="background-color:WhiteSmoke" value="WhiteSmoke">当麻白</option>
<option style="background-color:Gainsboro" value="Gainsboro">生石灰</option>
<option style="background-color:LightGrey" value="LightGrey">石膏灰</option>
<option style="background-color:Silver" value="Silver">波斯灰</option>
<option style="background-color:DarkGray" value="DarkGray">星石苍</option>
<option style="background-color:Gray" value="Gray">纯种灰</option>
<option style="background-color:DimGray" value="DimGray">水泥灰</option>
<option style="background-color:Black" value="Black">高端黑</option>
<option style="background-color: transparent" value="Transparent">灯里透</option>
</select></td>
<td class="embedded">
<select class="med codebuttons" style="margin-right:0px;width:50px;" name='font' onchange="alterfont(this.options[this.selectedIndex].value, 'font')">
<option value="0"><?php echo $lang_functions['select_font'] ?></option>
<option value="仿宋_GB2312">仿宋</option>
<option value="黑体">黑体</option>
<option value="楷体_GB2312">楷体</option>
<option value="宋体">宋体</option>
<option value="新宋体">新宋体</option>
<option value="微软雅黑">微软雅黑</option>
<option value="Trebuchet MS">Trebuchet</option>
<option value="Tahoma">Tahoma</option>
<option value="Arial">Arial</option>
<option value="Impact">Impact</option>
<option value="Verdana">Verdana</option>
</select>
</td>
<td class="embedded">
<select class="med codebuttons" style="margin-right:0px;width:50px;" name='size' onchange="alterfont(this.options[this.selectedIndex].value, 'size')">
<option value="0"><?php echo $lang_functions['select_size'] ?></option>
<option value="1">再小一点</option>
<option value="2">正常</option>
<option value="3">大一点点</option>
<option value="4">再大一点</option>
<option value="5">再大一些</option>
<option value="6">已经很大</option>
<option value="7">不能再大</option>
</select></td></tr>
</table>
</td>
</tr>
<?php
if ($enableattach_attachment == 'yes'&&$CURUSER){
?>
<tr>
<td colspan="2" valign="middle">
<iframe src="attachment.php" width="100%" height="24" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
</td>
</tr>
<?php
}
print("<tr>");
print("<td align=\"left\" width=\"100%\"><textarea class=\"bbcode\" cols=\"100\" style=\"width: 99.6%;\" name=\"".$text."\" id=\"".$text."\" rows=\"20\" onkeydown=\"ctrlenter(event,'compose','qr')\">".$content."</textarea>");
if($useatuser){
?>
<link rel="stylesheet" href="javascript/userAutoTips.css" type="text/css" />
<script type="text/javascript" src="javascript/userAutoTips.js"></script>
<script type="text/javascript">userAutoTips({id:'<?php  echo $text?>'});$(window).bind('scroll resize', function(e){userAutoTips({id:'<?php  echo $text?>'})})</script>
<?}?>


<iframe name="rtf" width=0 height=0 scrolling=no frameborder=0></iframe>
</td>
<td align="center" width="99px">
<table cellspacing="1" cellpadding="3">
<tr>
<?php
$i = 0;
$quickSmilies = array(1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 13, 16, 17, 19, 20, 21, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 39, 40, 41);
foreach ($quickSmilies as $smily) {
	if ($i%4 == 0 && $i > 0) {
		print('</tr><tr>');
	}
	print("<td class=\"embedded\" style=\"padding: 3px;\">".getSmileIt($form, $text, $smily)."</td>");
	$i++;
}
?>
</tr></table>
<br />
<a href="javascript:winop();"><?php echo $lang_functions['text_more_smilies'] ?></a>
</td></tr></table>	

<script language="javascript" type="text/javascript">
<!--
rtf.document.designMode="on";
rtf.document.designMode="on";
rtf.document.open();
rtf.document.writeln('<html><body></body></html>');
rtf.document.close();
// -->
</script>
<?php
}

function begin_compose($title = "",$type="new", $body="", $hassubject=true, $subject="", $maxsubjectlength=100){
	global $lang_functions;
	if ($title)
		print("<h1 align=\"center\">".$title."</h1>");
	switch ($type){
		case 'new': 
		{
			$framename = $lang_functions['text_new'];
			break;
		}
		case 'reply': 
		{
			$framename = $lang_functions['text_reply'];
			break;
		}
		case 'quote':
		{
			$framename = $lang_functions['text_quote'];
			break;
		}
		case 'edit':
		{
			$framename = $lang_functions['text_edit'];
			break;
		}
		default:
		{
			$framename = $lang_functions['text_new'];
			break;
		}
	}
	begin_frame($framename, true);
	print("<table class=\"main\" width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");
	if ($hassubject)
		print("<tr><td class=\"rowhead\">".$lang_functions['row_subject']."</td>" .
"<td class=\"rowfollow\" align=\"left\"><input type=\"text\" style=\"width: 650px;\" name=\"subject\" maxlength=\"".$maxsubjectlength."\" value=\"".$subject."\" /></td></tr>\n");
	print("<tr><td class=\"rowhead\" valign=\"top\">".$lang_functions['row_body']."</td><td class=\"rowfollow\" align=\"left\"><span style=\"display: none;\" id=\"previewouter\"></span><div id=\"editorouter\">");
	textbbcode("compose","body", $body, false);
	print("</div></td></tr>");
}

function end_compose(){
	global $lang_functions;
	print("<tr><td colspan=\"2\" align=\"center\"><table><tr><td class=\"embedded\"><input id=\"qr\" type=\"submit\" class=\"btn\"  onclick=\"javascript:{closealltags();this.disabled=true;this.form.submit()}\"   value=\"".$lang_functions['submit_submit']."\" /></td><td class=\"embedded\">");
	print("<input type=\"button\" class=\"btn2\" name=\"previewbutton\" id=\"previewbutton\" value=\"".$lang_functions['submit_preview']."\" onclick=\"javascript:preview(this.parentNode);\" />");
	print("<input type=\"button\" class=\"btn2\" style=\"display: none;\" name=\"unpreviewbutton\" id=\"unpreviewbutton\" value=\"".$lang_functions['submit_edit']."\" onclick=\"javascript:unpreview(this.parentNode);\" />");
	print("</td></tr></table>");
	print("</td></tr>");
	print("</table>\n");
	end_frame();
	print("<p align=\"center\"><a href=\"tags.php\" target=\"_blank\">".$lang_functions['text_tags']."</a> | <a href=\"smilies.php\" target=\"_blank\">".$lang_functions['text_smilies']."</a></p>\n");
}

function insert_suggest($keyword, $userid, $pre_escaped = true)
{
	if(mb_strlen($keyword,"UTF-8") >= 2)
	{
		$userid = 0 + $userid;
		if($userid)
		sql_query("INSERT INTO suggest(keywords, userid, adddate,newupdate) VALUES (" . ($pre_escaped == true ? "'" . $keyword . "'" : sqlesc($keyword)) . "," . sqlesc($userid) . ", " . sqlesc(date('Y-m-d',strtotime(date('Y-m', time()).'-15'))) . " , NOW() ) ON DUPLICATE KEY update times=times+1 , newupdate=values(newupdate)") or sqlerr(__FILE__,__LINE__);
	}
}

function get_external_tr($imdb_url = "",$type=2)
{
	global $lang_functions;
	global $showextinfo;
	$imdbNumber = parse_imdb_id($imdb_url);
	$textimdb="imdburl";
	
	$SELECT="<SELECT name=\"imdbnum\" id=\"imdbnum\" >
<OPTION value=\"1\" ".($type==1?"selected=\"selected\"":"")." >IMDB</OPTION>
<OPTION value=\"2\" ".($type==2?"selected=\"selected\"":"").">豆瓣电影</OPTION>
</SELECT>";
	($showextinfo['imdb'] == 'yes' ? tr($lang_functions['row_imdb_url'],  "
	
	<input type=\"text\" style=\"width: 450px;\" name=\"url\" id=\"".$textimdb."\"  
	value=\"".($imdbNumber ? ($type==2?"http://movie.douban.com/subject/".parse_imdb_id($imdb_url):"http://www.imdb.com/title/tt".parse_imdb_id($imdb_url)) : "")."\"  onchange='imdbtypechange()' />".$SELECT."
	




<INPUT   type=button  alt=search value=\"点击搜索\" align=bottom onclick=\"javascript:winop2();\">
<br />


<font class=\"medium\">".$lang_functions['text_imdb_url_note']."</font>", 1) : "");
}



function get_external_tr2($imdb_url = "",$type=1)
{
	global $lang_functions;
	global $showextinfo;
?>	

	
	<script type="text/javascript">


function winop2()
{
url=document.getElementById('imdburl').value;
url=encodeURI(url);

if(url)windop = window.open("moresmilies.php?form=edittorrent&text=imdburl&keywords="+url,"mywin","height=500,width=500,resizable=no,scrollbars=yes");
}

function imdbtypechange()  
{  
var douban=/douban/; 
var imdb=/imdb/; 
var url=document.getElementById('imdburl').value
if (douban.exec(url)) document.getElementById('imdbnum').value=2 ;
else if (imdb.exec(url)) document.getElementById('imdbnum').value=1; } 


//]]>
</script>
<?php


	$SELECT="<SELECT name=\"imdbnum\" id=\"imdbnum\">
<OPTION value=\"1\" ".($type==1?"selected=\"selected\"":"")." >IMDB</OPTION>
<OPTION value=\"2\" ".($type==2?"selected=\"selected\"":"").">豆瓣电影</OPTION>
</SELECT>";



	$imdbNumber = parse_imdb_id($imdb_url);
	$textimdb="imdburl";
	($showextinfo['imdb'] == 'yes' ? tr($lang_functions['row_imdb_url'],  "
	
	<input type=\"text\" style=\"width: 450px;\" name=\"url\" id=\"".$textimdb."\"  
	value=\"".($imdbNumber ? ($type==2?"http://movie.douban.com/subject/".parse_imdb_id($imdb_url):"http://www.imdb.com/title/tt".parse_imdb_id($imdb_url)) : "")."\" onchange='imdbtypechange()'/>".$SELECT."

<INPUT   type=button alt=search value=\"点击搜索\" align=bottom onclick=\"javascript:winop2();\">
<br />


<font class=\"medium\">".$lang_functions['text_imdb_url_note']."</font>", 1) : "");
}



function get_torrent_extinfo_identifier($torrentid)
{
	$torrentid = 0 + $torrentid;

	$result = array('imdb_id');
	unset($result);

	if($torrentid)
	{
		$res = sql_query("SELECT url FROM torrents WHERE id=" . $torrentid) or sqlerr(__FILE__,__LINE__);
		if(mysql_num_rows($res) == 1)
		{
			$arr = mysql_fetch_array($res) or sqlerr(__FILE__,__LINE__);

			$imdb_id = parse_imdb_id($arr["url"]);
			$result['imdb_id'] = $imdb_id;
		}
	}
	return $result;
}

function parse_imdb_id($url)
{	if(preg_match('/(book|music)\.douban/si',$url))return false;
	elseif ($url != "" && preg_match("/[0-9]{7,8}/i", $url, $matches)) {
		return $matches[0];
	} else if ($url != "" && preg_match("/[0-9]{6}/i", $url, $matches)){
		return str_pad($matches[0], 7, '0', STR_PAD_LEFT);
	}else if ($url && is_numeric($url) && strlen($url) < 7) {
		return str_pad($url, 7, '0', STR_PAD_LEFT);
	} else {
		return false;
	}
}

function build_imdb_url($imdb_id)
{
	return $imdb_id == "" ? "" : "http://www.imdb.com/title/tt" . $imdb_id . "/";
}

// it's a stub implemetation here, we need more acurate regression analysis to complete our algorithm
function get_torrent_2_user_value($user_snatched_arr)
{
	// check if it's current user's torrent
	$torrent_2_user_value = 1.0;

	$torrent_res = sql_query("SELECT * FROM torrents WHERE id = " . $user_snatched_arr['torrentid']) or sqlerr(__FILE__, __LINE__);
	if(mysql_num_rows($torrent_res) == 1)	// torrent still exists
	{
		$torrent_arr = mysql_fetch_array($torrent_res) or sqlerr(__FILE__, __LINE__);
		if($torrent_arr['owner'] == $user_snatched_arr['userid'])	// owner's torrent
		{
			$torrent_2_user_value *= 0.7;	// owner's torrent
			$torrent_2_user_value += ($user_snatched_arr['uploaded'] / $torrent_arr['size'] ) -1 > 0 ? 0.2 - exp(-(($user_snatched_arr['uploaded'] / $torrent_arr['size'] ) -1)) : ($user_snatched_arr['uploaded'] / $torrent_arr['size'] ) -1;
			$torrent_2_user_value += min(0.1 , ($user_snatched_arr['seedtime'] / 37*60*60 ) * 0.1);
		}
		else
		{
			if($user_snatched_arr['finished'] == 'yes')
			{
				$torrent_2_user_value *= 0.5;
				$torrent_2_user_value += ($user_snatched_arr['uploaded'] / $torrent_arr['size'] ) -1 > 0 ? 0.4 - exp(-(($user_snatched_arr['uploaded'] / $torrent_arr['size'] ) -1)) : ($user_snatched_arr['uploaded'] / $torrent_arr['size'] ) -1;
				$torrent_2_user_value += min(0.1, ($user_snatched_arr['seedtime'] / 22*60*60 ) * 0.1);
			}
			else
			{
				$torrent_2_user_value *= 0.2;
				$torrent_2_user_value += min(0.05, ($user_snatched_arr['leechtime'] / 24*60*60 ) * 0.1);	// usually leechtime could not explain much
			}
		}
	}
	else	// torrent already deleted, half blind guess, be conservative
	{
		
		if($user_snatched_arr['finished'] == 'no' && $user_snatched_arr['uploaded'] > 0 && $user_snatched_arr['downloaded'] == 0)	// possibly owner
		{
			$torrent_2_user_value *= 0.55;	//conservative
			$torrent_2_user_value += min(0.05, ($user_snatched_arr['leechtime'] / 31*60*60 ) * 0.1);
			$torrent_2_user_value += min(0.1, ($user_snatched_arr['seedtime'] / 31*60*60 ) * 0.1);
		}
		else if($user_snatched_arr['downloaded'] > 0)	// possibly leecher
		{
			$torrent_2_user_value *= 0.38;	//conservative
			$torrent_2_user_value *= min(0.22, 0.1 * $user_snatched_arr['uploaded'] / $user_snatched_arr['downloaded']);	// 0.3 for conservative
			$torrent_2_user_value += min(0.05, ($user_snatched_arr['leechtime'] / 22*60*60 ) * 0.1);
			$torrent_2_user_value += min(0.12, ($user_snatched_arr['seedtime'] / 22*60*60 ) * 0.1);
		}
		else
			$torrent_2_user_value *= 0.0;
	}
	return $torrent_2_user_value;
}

function cur_user_check () {
	global $lang_functions;
	global $CURUSER;
	if ($CURUSER)
	{
		sql_query("UPDATE users SET lang=" . get_langid_from_langcookie() . " WHERE id = ". $CURUSER['id']);
		if (!empty($_GET["returnto"]))redirect("$_GET[returnto]");
		elseif (!empty($_POST["returnto"]))redirect("$_POST[returnto]");
		else redirect("index.php");
		stderr ($lang_functions['std_permission_denied'], $lang_functions['std_already_logged_in']);
	}
}

function KPS($type = "+", $point = "1.0", $id = "") {
	global $bonus_tweak;
	if ($point != 0){
		$point = sqlesc($point);
		if ($bonus_tweak == "enable" || $bonus_tweak == "disablesave"){
			sql_query("UPDATE users SET seedbonus = seedbonus$type$point WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
		}
	}
	else return;
}

function get_agent($peer_id, $agent)
{
	return substr($agent, 0, (strpos($agent, ";") == false ? strlen($agent) : strpos($agent, ";")));
}

function EmailBanned($newEmail)
{
	$newEmail = trim(strtolower($newEmail));
	$sql = sql_query("SELECT * FROM bannedemails") or sqlerr(__FILE__, __LINE__);
	$list = mysql_fetch_array($sql);
	$addresses = explode(' ', preg_replace("/[[:space:]]+/", " ", trim($list[value])) );
	
	if(preg_match("/\@edu\.cn/", $newEmail))
	return true;
	elseif(count($addresses) > 0)
	{
		foreach ( $addresses as $email )
		{
			$email = trim(strtolower(preg_replace('/\./', '\\.', $email)));
			if(strstr($email, "@"))
			{
				if(preg_match('/^@/', $email))
				{// Any user @host?
					// Expand the match expression to catch hosts and
					// sub-domains
					$email = preg_replace('/^@/', '[@\\.]', $email);
					if(preg_match("/".$email."$/", $newEmail))
					return true;
				}
			}
			elseif(preg_match('/@$/', $email))
			{    // User at any host?
				if(preg_match("/^".$email."/", $newEmail))
				return true;
			}
			else
			{                // User@host
				if(strtolower($email) == $newEmail)
				return true;
			}
		}
	}

	return false;
}

function EmailAllowed($newEmail)
{
global $restrictemaildomain;
if ($restrictemaildomain == 'yes'){
	$newEmail = trim(strtolower($newEmail));
	$sql = sql_query("SELECT * FROM allowedemails") or sqlerr(__FILE__, __LINE__);
	$list = mysql_fetch_array($sql);
	$addresses = explode(' ', preg_replace("/[[:space:]]+/", " ", trim($list[value])) );

	if(count($addresses) > 0)
	{
		foreach ( $addresses as $email )
		{
			$email = trim(strtolower(preg_replace('/\./', '\\.', $email)));
			if(strstr($email, "@"))
			{
				if(preg_match('/^@/', $email))
				{// Any user @host?
					// Expand the match expression to catch hosts and
					// sub-domains
					$email = preg_replace('/^@/', '[@\\.]', $email);
					if(preg_match('/'.$email.'$/', $newEmail))
					return true;
				}
			}
			elseif(preg_match('/@$/', $email))
			{    // User at any host?
				if(preg_match("/^".$email."/", $newEmail))
				return true;
			}
			else
			{                // User@host
				if(strtolower($email) == $newEmail)
				return true;
			}
		}
	}
	return false;
}
else return true;
}

function allowedemails()
{
	$sql = sql_query("SELECT * FROM allowedemails") or sqlerr(__FILE__, __LINE__);
	$list = mysql_fetch_array($sql);
	return $list['value'];
}

function redirect($url)
{
	if(!headers_sent())
	{
	header("Location : $url");
	}
	//else
	echo "<script type=\"text/javascript\">window.location.href = '$url';</script>";
	exit;
}

function set_cachetimestamp($id, $field = "cache_stamp")
{
	sql_query("UPDATE torrents SET $field = " . time() . " WHERE id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
}
function reset_cachetimestamp($id, $field = "cache_stamp")
{
	sql_query("UPDATE torrents SET $field = 0 WHERE id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
}

function cache_check ($file = 'cachefile',$endpage = true, $cachetime = 600) {
	global $lang_functions;
	global $rootpath,$cache,$CURLANGDIR;
	$cachefile = $rootpath.$cache ."/" . $CURLANGDIR .'/'.$file.'.html';
	// Serve from the cache if it is younger than $cachetime
	if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile)))
	{
		include($cachefile);
		if ($endpage)
		{
			print("<p align=\"center\"><font class=\"small\">".$lang_functions['text_page_last_updated'].date('Y-m-d H:i:s', filemtime($cachefile))."</font></p>");
			end_main_frame();
			stdfoot();
			exit;
		}
		return false;
	}
  	ob_start();
	return true;
}

function cache_save  ($file = 'cachefile') {
	global $rootpath,$cache;
	global $CURLANGDIR;
	$cachefile = $rootpath.$cache ."/" . $CURLANGDIR . '/'.$file.'.html';
	$fp = fopen($cachefile, 'w');
	// save the contents of output buffer to the file
	fwrite($fp, ob_get_contents());
	// close the file
	fclose($fp);
	// Send the output to the browser
	ob_end_flush();
}

function get_email_encode($lang)
{	return "utf-8";
	if($lang == 'chs' || $lang == 'cht')
	return "gbk";
	else
	return "utf-8";
}

function change_email_encode($lang, $content)
{
	return iconv("utf-8", get_email_encode($lang) . "//IGNORE", $content);
}

function safe_email($email) {
	$email = str_replace("<","",$email);
	$email = str_replace(">","",$email);
	$email = str_replace("\'","",$email);
	$email = str_replace('\"',"",$email);
	$email = str_replace("\\\\","",$email);

	return $email;
}

function check_email ($email) {
	if(preg_match('/^[A-Za-z0-9][A-Za-z0-9_.+\-]*@[A-Za-z0-9][A-Za-z0-9_+\-]*(\.[A-Za-z0-9][A-Za-z0-9_+\-]*)+$/', $email))
	return true;
	else
	return false;
}

function sent_mail_store($to,$fromname,$fromemail,$subject,$body,$type = "confirmation",$showmsg=true,$multiple=false,$multiplemail='',$hdr_encoding = 'UTF-8', $specialcase = '') {
	global $lang_functions;
	global $rootpath,$SITENAME,$SITEEMAIL,$smtptype,$smtp,$smtp_host,$smtp_port,$smtp_from,$smtpaddress,$smtpport,$accountname,$accountpassword;
	# Is the OS Windows or Mac or Linux?
	if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
		$eol="\r\n";
		$windows = true;
	}
	elseif (strtoupper(substr(PHP_OS,0,3)=='MAC'))
		$eol="\r";
	else
		$eol="\n";
	if ($smtptype == 'none')
		return false;
	if ($smtptype == 'default') {
		if(!@mail($to, "=?".$hdr_encoding."?B?".base64($subject)."?=", $body, "From: ".$SITEEMAIL.$eol."Content-type: text/html; charset=".$hdr_encoding.$eol, "-f$SITEEMAIL"))return false;
	}
	elseif ($smtptype == 'advanced') {
		$fromname="=?".$hdr_encoding."?B?".base64($fromname)."?=";
		$mid = md5(getip() . $fromname);
		$name = $_SERVER["SERVER_NAME"];
		$headers .= "From: $fromname <$fromemail>".$eol;
		$headers .= "Reply-To: $fromname <$fromemail>".$eol;
		$headers .= "Return-Path: $fromname <$fromemail>".$eol;
		$headers .= "Message-ID: <$mid thesystem@$name>".$eol;
		$headers .= "X-Mailer: PHP v".phpversion().$eol;
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-type: text/html; charset=".$hdr_encoding.$eol;
		$headers .= "X-Sender: PHP".$eol;
		if ($multiple)
		{
			$bcc_multiplemail = "";
			foreach ($multiplemail as $toemail)
			$bcc_multiplemail = $bcc_multiplemail . ( $bcc_multiplemail != "" ? "," : "") . $toemail;

			$headers .= "Bcc: $multiplemail.$eol";
		}
		if(!@mail($to,"=?".$hdr_encoding."?B?".base64($subject)."?=",$body,$headers))return false;
	}
	elseif ($smtptype == 'external') {
		require_once ($rootpath . 'include/smtp/smtp.lib.php');
		$mail = new smtp($hdr_encoding,'ANTSOUL');
		if (get_user_class() >= UC_SYSOP)
		$mail->debug(TRUE);
		ELSE
		$mail->debug(false);
		
		$mail->open(gethostbyname($smtpaddress), $smtpport);
		$mail->auth($accountname, $accountpassword);
		$mail->from($accountname,$SITENAME);
		if ($multiple)
		{
			$mail->multi_to_head($to);
			foreach ($multiplemail as $toemail)
			$mail->multi_to($toemail);
		}
		else
		$mail->to($to);
		$mail->mime_content_transfer_encoding();
		$mail->mime_charset('text/html', $hdr_encoding);
		$mail->subject($subject);
		$mail->body($body);
		if(!$mail->send()) 
					return false;;
		$mail->close();
	}

	return true;
}



function sent_mail($to,$fromname,$fromemail,$subject,$body,$type = "confirmation",$showmsg=true,$multiple=false,$multiplemail='',$hdr_encoding = 'UTF-8', $specialcase = '') {
global $Cache,$lang_functions;

if($Cache->get_value('this_ip_sending_mail'.getip())&&get_user_class() < UC_SYSOP)stderr("失败","发信速度太快,请五秒以后再进行尝试");
$Cache->cache_value('this_ip_sending_mail'.getip(), 'yes',5);
sql_query("INSERT INTO mail_store(touser,fromname,fromemail,subjecttitle,body,type,showmsg,multiple,multiplemail,hdr_encoding,specialcase) VALUES (" . sqlesc(serialize($to)) . "," . sqlesc(serialize($fromname)) . "," . sqlesc(serialize($fromemail)) . "," . sqlesc(serialize($subject)) . "," . sqlesc(serialize($body)) . "," . sqlesc(serialize($type)) . "," . sqlesc(serialize($showmsg)) . "," . sqlesc(serialize($multiple)) . "," . sqlesc(serialize($multiplemail)) . "," . sqlesc(serialize($hdr_encoding)) . "," . sqlesc(serialize($specialcase)) . ")");
$Cache->delete_value('here_now_have_no_mail');

if ($showmsg) {
		if ($type == "confirmation")
		stderr($lang_functions['std_success'], $lang_functions['std_confirmation_email_sent']."<b>". htmlspecialchars($to) ."</b>.\n" .
		$lang_functions['std_please_wait'],false);
		elseif ($type == "details")
		stderr($lang_functions['std_success'], $lang_functions['std_account_details_sent']."<b>". htmlspecialchars($to) ."</b>.\n" .
		$lang_functions['std_please_wait'],false);
	}else
	return true;
}


function failedloginscheck ($type = 'Login') {
	global $lang_functions;
	global $maxloginattempts;
	$total = 0;
	$ip = sqlesc(getip());
	$Query = sql_query("SELECT attempts, banned ,attempts as sum  FROM loginattempts WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
	$total = mysql_fetch_array($Query);
	if($total["banned"]=='yes')stderr($type.$lang_functions['std_locked'].$type.$lang_functions['std_attempts_reached'], $lang_functions['std_your_ip_banned']);
	elseif ($total["sum"] >= $maxloginattempts) {
		sql_query("UPDATE loginattempts SET banned = 'yes' WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
		}

}
function failedlogins ($type = 'login', $recover = false, $head = true)
{
	global $lang_functions;
	$ip = sqlesc(getip());
	$added = sqlesc(date("Y-m-d H:i:s"));
	$a = (@mysql_fetch_row(@sql_query("select count(*) from loginattempts where ip=$ip"))) or sqlerr(__FILE__, __LINE__);
	if ($a[0] == 0)
	sql_query("INSERT INTO loginattempts (ip, added, attempts) VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
	else
	sql_query("UPDATE loginattempts SET attempts = attempts + 1 where ip=$ip") or sqlerr(__FILE__, __LINE__);
	if ($recover)
	sql_query("UPDATE loginattempts SET type = 'recover' WHERE ip = $ip") or sqlerr(__FILE__, __LINE__);
	if ($type == 'silent')
	return;
	elseif ($type == 'login')
	{
		stderr($lang_functions['std_login_failed'],$lang_functions['std_login_failed_note'],false);
	}
	else
	stderr($lang_functions['std_failed'],$type,false, $head);

}

function failedloginsmax ($numper=1)
{
	
	$ip = sqlesc(getip());
	$added = sqlesc(date("Y-m-d H:i:s"));
	$a = (@mysql_fetch_row(@sql_query("select count(*) from loginattempts where ip=$ip"))) or sqlerr(__FILE__, __LINE__);
	if ($a[0] == 0)
	sql_query("INSERT INTO loginattempts (ip, added, attempts) VALUES ($ip, $added, $numper)") or sqlerr(__FILE__, __LINE__);
	else
	sql_query("UPDATE loginattempts SET attempts = attempts + $numper where ip=$ip") or sqlerr(__FILE__, __LINE__);
	
}



function login_failedlogins($type = 'login', $recover = false, $head = true)
{
	global $lang_functions;
	$ip = sqlesc(getip());
	$added = sqlesc(date("Y-m-d H:i:s"));
	$a = (@mysql_fetch_row(@sql_query("select count(*) from loginattempts where ip=$ip"))) or sqlerr(__FILE__, __LINE__);
	if ($a[0] == 0)
	sql_query("INSERT INTO loginattempts (ip, added, attempts) VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
	else
	sql_query("UPDATE loginattempts SET attempts = attempts + 1 where ip=$ip") or sqlerr(__FILE__, __LINE__);
	if ($recover)
	sql_query("UPDATE loginattempts SET type = 'recover' WHERE ip = $ip") or sqlerr(__FILE__, __LINE__);
	if ($type == 'silent')
	return;
	elseif ($type == 'login')
	{
		stderr($lang_functions['std_login_failed'],$lang_functions['std_login_failed_note'],false);
	}
	else
	stderr($lang_functions['std_recover_failed'],$type,false, $head);
}

function remaining ($type = 'login') {
	global $maxloginattempts;
	$total = 0;
	$ip = sqlesc(getip());
	$Query = sql_query("SELECT SUM(attempts) FROM loginattempts WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
	list($total) = mysql_fetch_array($Query);
	$remaining = $maxloginattempts - $total;
	if ($remaining <= 2 )
	$remaining = "<font color=\"red\" size=\"2\">[".$remaining."]</font>";
	else
	$remaining = "<font color=\"green\" size=\"2\">[".$remaining."]</font>";

	return $remaining;
}

function registration_check($type = "invitesystem", $maxuserscheck = true, $ipcheck = true) {
	global $lang_functions;
	global $invitesystem, $registration, $maxusers, $maxusers2, $SITENAME, $maxip;
	if ($type == "invitesystem") {
		if ($invitesystem == "no") {
			stderr($lang_functions['std_oops'], $lang_functions['std_invite_system_disabled'], 0);
		}
	}

	if ($type == "normal") {
		if ($registration == "no") {
		httperr();
			stderr($lang_functions['std_sorry'], $lang_functions['std_open_registration_disabled'], 0);
		}
	}
	
	if ($type == "dean") {
		if ($registration == "no"&&$invitesystem == "no") {
		httperr();
			stderr($lang_functions['std_sorry'], $lang_functions['std_open_registration_disabled'], 0);
		}
	}

	if ($maxuserscheck) {
		$res = sql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
		$arr = mysql_fetch_row($res);
		if ($arr[0] >= $maxusers)
		stderr($lang_functions['std_sorry'], $lang_functions['std_account_limit_reached'], 0);
		elseif ($arr[0] >= $maxusers2&&$type == "normal")
		stderr($lang_functions['std_sorry'], $lang_functions['std_open_registration_disabled'], 0);
	}

	if ($ipcheck&&$maxip) {
		$ip = getip () ;
		$a = (@mysql_fetch_row(@sql_query("select count(*) from users where status = 'pending' and ip='" . mysql_real_escape_string($ip) . "'"))) or sqlerr(__FILE__, __LINE__);
		if ($a[0] > $maxip)
		stderr($lang_functions['std_sorry'], $lang_functions['std_the_ip']."<b>" . htmlspecialchars($ip) ."</b>". $lang_functions['std_used_many_times'],false);
	}
	return true;
}

function random_str($length="6")
{
	//$set = array("A","B","C","D","E","F","G","H","P","R","M","N","1","2","3","4","5","6","7","8","9");
	$set = array("0","1","2","3","4","5","6","7","8","9");
	$str="";
	for($i=1;$i<=$length;$i++)
	{
		$ch = rand(0, count($set)-1);
		$str .= $set[$ch];
	}
	return $str;
}
function image_code ($twice="") {


	$randomstr = random_str();
	$dateline = time();
	
	$ip=getip();
	if(!$twice)
	$imagehash = md5(md5($randomstr)+md5($dateline)+mksecret()+$ip);
	else $imagehash =$twice;
	$sql = 'INSERT INTO `regimages` (`imagehash`, `imagestring`, `dateline` , `ip`) VALUES ('.sqlesc($imagehash).', \''.$randomstr.'\', \''.$dateline.'\', '.sqlesc($ip).');';
	sql_query($sql) or die(mysql_error());
	return $imagehash;
}
 
function check_code ($imagehash, $imagestring, $where = 'signup.php',$maxattemptlog=false,$head=true) {
	global $lang_functions;
	$query = sprintf("SELECT * FROM regimages WHERE imagehash='%s' AND imagestring='%s' and dateline >  ".sqlesc(time()-300),
	mysql_real_escape_string($imagehash),
	mysql_real_escape_string($imagestring));
	$sql = sql_query($query);
	$imgcheck = mysql_fetch_array($sql);
	if(!$imgcheck['dateline']) {
		$delete = sprintf("DELETE FROM regimages WHERE imagehash='%s'",
		mysql_real_escape_string($imagehash));
		sql_query($delete);
		if (!$maxattemptlog)
		bark($lang_functions['std_invalid_image_code']."<a href=\"".htmlspecialchars($where)."\">".$lang_functions['std_here_to_request_new']);
		else
		failedlogins($lang_functions['std_invalid_image_code']."<a href=\"".htmlspecialchars($where)."\">".$lang_functions['std_here_to_request_new'],true,$head);
	}else{
		$delete = sprintf("DELETE FROM regimages WHERE imagehash='%s'",
		mysql_real_escape_string($imagehash));
		sql_query($delete);
		return true;
	}
}
function show_image_code () {
	global $lang_functions;
	global $iv;
	if ($iv == "yes") {
		unset($imagehash);
		$imagehash = image_code () ;
		print ("<tr><td class=\"rowhead\">".$lang_functions['row_security_image']."</td>");
		//print ("<td align=\"left\"><img src=\"".htmlspecialchars("image.php?action=regimage&imagehash=".$imagehash)."\" border=\"0\" alt=\"CAPTCHA\" id=\"codeimg\" /></td></tr>");
		print("<td align=\"left\">
		<script>document.write('<img  src=\"".htmlspecialchars("image.php?action=regimage&imagehash=".$imagehash."&random=")."'+Math.random()+'\">');</script>
		</td></tr>");
		print ("<tr><td class=\"rowhead\">".$lang_functions['row_security_code']."</td><td align=\"left\">");
		print("<input type=\"text\" autocomplete=\"off\" style=\"width: 180px; border: 1px solid gray\" name=\"imagestring\" value=\"\" />");
		print("<input type=\"hidden\" name=\"imagehash\" value=\"$imagehash\" /></td></tr>");
		/*print("<script>
		var element = document.getElementById('codeimg');
       element.src = element.src+'&random='+Math.random();
</script>");*/
	}elseif ($iv == "op") {
		unset($imagehash);
		$res = sql_query("SELECT imdb,name  FROM imdbinfo where name != '' and info like '%日%' ORDER BY RAND() LIMIT 10");
		//$res = sql_query("SELECT imdb,name  FROM imdbinfo where name != '' and info like '%日%' ORDER BY RAND() LIMIT 10");
		$res = sql_query("SELECT imdb,name  FROM imdbinfo where name != '' ORDER BY RAND() LIMIT 10");
		while($a = mysql_fetch_assoc($res))
		$show_douban[] = $a;
		$imagechosse=$show_douban[mt_rand(0,count($show_douban)-1)];
		$imagehash = md5(md5(random_str()).md5(time()).mksecret());
		sql_query('INSERT INTO regimages (imagehash, imagestring, dateline , ip,imdb) VALUES ('.sqlesc($imagehash).', '.sqlesc(hash('crc32',md5($imagechosse[name].TIMENOW))).', '.sqlesc(time()).','.sqlesc(getip()).','.sqlesc($imagechosse[imdb]).')');
		print ("<tr><td class=\"rowhead\">".$lang_functions['row_security_image']."</td>");
		print("<td align=\"left\">
		<script>document.write('<img  src=\"".htmlspecialchars("image.php?action=regimage&imagehash=".$imagehash."&random=")."'+Math.random()+'\">');</script>
		</td></tr>");
		print ("<tr><td class=\"rowhead\">".$lang_functions['row_security_code']."</td><td align=\"left\">");
		
		print("<select name='imagestring'>");
		print("<option value=0>请选择上侧图片对应的作品名</option>");
		foreach($show_douban as $row)print("<option value=".hash('crc32',md5($row[name].TIMENOW)).">".$row[name]."</option>");
		print("</select>");

		
		print("<input type=\"hidden\" name=\"imagehash\" value=\"$imagehash\" /></td></tr>");

	}
}

function get_ip_location($ip)
{
	global $lang_functions;
	global $Cache;
	
if (!$location = $Cache->get_value('get_ip_location'.$ip)){	

	if (!$ret = $Cache->get_value('location_list')){
		$ret = array();
		$res = sql_query("SELECT * FROM locations") or sqlerr(__FILE__, __LINE__);
		while ($row = mysql_fetch_array($res))
			$ret[] = $row;
		$Cache->cache_value('location_list', $ret, 152800);
	}
	$location = false;

	foreach($ret AS $arr)
	{
		if(in_ip_range(false, $ip, $arr["start_ip"], $arr["end_ip"]))
		{
			$location = array($arr["name"], "");
			break;
		}
	}
	if(!$location)$location = array((convertipv6($ip)),"");

	
		$Cache->cache_value('get_ip_location'.$ip, $location,3600*24);
}


	return $location;
}

function in_ip_range($long, $targetip, $ip_one, $ip_two=false)
{
	// if only one ip, check if is this ip
	if($ip_two===false){
		if(($long ? (long2ip6($ip_one) == $targetip) : ( $ip_one == $targetip))){
			$ip=true;
		}
		else{
			$ip=false;
		}
	}
	else{
		if($long ? ($ip_one<=ip2long6($targetip) && $ip_two>=ip2long6($targetip)) : (ip2long6($ip_one)<=ip2long6($targetip) && ip2long6($ip_two)>=ip2long6($targetip))){
			$ip=true;
		}
		else{
			$ip=false;
		}
	}
	
	
	return $ip;
}


function validip_format($ip)
{
	$ipPattern =
	'/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.' .
	'(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.' .
	'(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.' .
	'(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/';

	return preg_match($ipPattern, $ip);
}

function maxslots () {
	global $lang_functions;
	global $CURUSER, $maxdlsystem,$maxdlsystem_time;
	
	
	
	/*$gigs = $CURUSER["uploaded"] / (1024*1024*1024);
	$ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 1);
	if ($ratio < 0.5 || $gigs < 5) $max = 1;
	elseif ($ratio < 0.65 || $gigs < 6.5) $max = 2;
	elseif ($ratio < 0.8 || $gigs < 8) $max = 3;
	elseif ($ratio < 0.95 || $gigs < 9.5) $max = 4;
	else $max = 0;
	*/
	$gigs = $CURUSER["downloaded"] / (1024*1024*1024);
	$ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 1);
	$max = 9999;
	if($gigs > 10){
	if ($ratio < 0.95) $max = 1;
	elseif ($ratio < 1.95) $max = 2;
	elseif ($ratio < 2.95) $max = 3;
	elseif ($ratio < 3.95) $max = 4;
	}
	
	if($maxdlsystem_time)$max = intval(min((get_user_class()/2+1.5),$max));
	 
	if ($maxdlsystem == "yes") {
		if (get_user_class() < UC_VIP) {
			if ($max > 0 && $max < 1000)
			print ("<a href=\"faq.php#id66\"><font class='color_slots'>".$lang_functions['text_slots']."</font>$max</a>");
			else
			print ("<font class='color_slots'>".$lang_functions['text_slots']."</font>".$lang_functions['text_unlimited']);
		}else
		print ("<font class='color_slots'>".$lang_functions['text_slots']."</font>".$lang_functions['text_unlimited']);
	}//else
	//print ("<font class='color_slots'>".$lang_functions['text_slots']."</font>".$lang_functions['text_unlimited']);
}

function WriteConfig ($configname = NULL, $config = NULL) {
	global $lang_functions, $CONFIGURATIONS;

	if (file_exists('config/allconfig.php')) {
		require('config/allconfig.php');
	}
	if ($configname) {
		$$configname=$config;
	}
	$path = './config/allconfig.php';
	if (!file_exists($path) || !is_writable ($path)) {
		stdmsg($lang_functions['std_error'], $lang_functions['std_cannot_read_file']."[<b>".htmlspecialchars($path)."</b>]".$lang_functions['std_access_permission_note']);
	}
	$data = "<?php\n";
	foreach ($CONFIGURATIONS as $CONFIGURATION) {
		$data .= "\$$CONFIGURATION=".getExportedValue($$CONFIGURATION).";\n";
	}
	$fp = @fopen ($path, 'w');
	if (!$fp) {
		stdmsg($lang_functions['std_error'], $lang_functions['std_cannot_open_file']."[<b>".htmlspecialchars($path)."</b>]".$lang_functions['std_to_save_info'].$lang_functions['std_access_permission_note']);
	}
	$Res = @fwrite($fp, $data);
	if (empty($Res)) {
		stdmsg($lang_functions['std_error'], $lang_functions['text_cannot_save_info_in']."[<b>".htmlspecialchars($path)."</b>]".$lang_functions['std_access_permission_note']);
	}
	fclose($fp);
	return true;
}

function getExportedValue($input,$t = null) {
	switch (gettype($input)) {
		case 'string':
			return "'".str_replace(array("\\","'"),array("\\\\","\'"),$input)."'";
		case 'array':
			$output = "array(\r";
			foreach ($input as $key => $value) {
				$output .= $t."\t".getExportedValue($key,$t."\t").' => '.getExportedValue($value,$t."\t");
				$output .= ",\n";
			}
			$output .= $t.')';
			return $output;
		case 'boolean':
			return $input ? 'true' : 'false';
		case 'NULL':
			return 'NULL';
		case 'integer':
		case 'double':
		case 'float':
			return "'".(string)$input."'";
	 }
	 return 'NULL';
}

function dbconn($autoclean = false)
{
	global $lang_functions;
	global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;
	global $useCronTriggerCleanUp;
	
	dbconn_error_check();
	if (!@mysql_pconnect($mysql_host, $mysql_user, $mysql_pass))
	{
		switch (mysql_errno())
		{	
			case 1040:
			case 2002:
				/*die("<html><head><meta http-equiv=refresh content=\"10 $_SERVER[REQUEST_URI]\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><table border=0 width=100% height=100%><tr><td><h3 align=center>".$lang_functions['std_server_load_very_high']."</h3></td></tr></table></body></html>");*/
			default:
				dbconn_error_check("[" . mysql_errno() . "] dbconn1: mysql_connect: " . mysql_error());
				die("[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
		}
	}
	mysql_query("SET NAMES UTF8");
	mysql_query("SET collation_connection = 'utf8_general_ci'");
	mysql_query("SET sql_mode=''");
	mysql_select_db($mysql_db) or die('dbconn: mysql_select_db: ' + mysql_error());

	userlogin();

	if (!$useCronTriggerCleanUp && $autoclean) {
		//register_shutdown_function("autoclean");
		autoclean();
	}
}
function get_user_row($id)
{
	global $Cache, $CURUSER;
	static $curuserRowUpdated = false;
	static $neededColumns = array('id', 'noad', 'class', 'enabled', 'privacy', 'avatar', 'signature', 'uploaded', 'downloaded', 'last_access', 'username', 'donor', 'leechwarn', 'warned', 'title','namecolour','school','hrwarned');
	if ($id == $CURUSER['id']) {
		$row = array();
		foreach($neededColumns as $column) {
			$row[$column] = $CURUSER[$column];
		}
		if (!$curuserRowUpdated) {
			$Cache->cache_value('user_'.$CURUSER['id'].'_content', $row, 3200);
			$curuserRowUpdated = true;
		}
	} elseif (!$row = $Cache->get_value('user_'.$id.'_content')){
		$res = sql_query("SELECT ".implode(',', $neededColumns)." FROM users WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
		$row = mysql_fetch_array($res,MYSQL_ASSOC);
		if (!$row)$row['nothere']='yes';
		$Cache->cache_value('user_'.$id.'_content', $row, 3570);
	}

	//if (!$row)
	if($row['nothere']=='yes')
		return false;
	else return $row;
}

function userlogin() {
	global $lang_functions;
	global $Cache;
	global $SITE_ONLINE, $oldip;
	global $enablesqldebug_tweak, $sqldebug_tweak,$thispagewidthscreen,$global_hr_hit;
	unset($GLOBALS["CURUSER"]);

	$ip = getip();
	if(validip($ip)){
	$nip = ip2long6($ip);
	$get_ip_location=get_ip_location($ip);
	if ($nip){ //$nip would be false for IPv6 address

	//Res = mysql_num_rows(sql_query("SELECT * FROM bans WHERE $nip >= first AND $nip <= last")) or sqlerr(__FILE__, __LINE__);
	
		if (!$res = $Cache->get_value('IP_BAND_'.$nip)){
			$res = @mysql_fetch_assoc(sql_query("SELECT comment FROM bans WHERE $nip >= first AND $nip <= last and banweb = 1 "));
			$res = ($res['comment']?$res['comment']:'no');
			$Cache->cache_value('IP_BAND_'.$nip, $res, 120);
		}
		
		if ($res != 'no')
		{
			header("HTTP/1.0 403 Forbidden");
			print("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body>".$lang_functions['text_unauthorized_ip']."<br />IP : ".$ip."<br />原因 : ".$res."</body></html>\n");
			die;
		}
	}
	}
	

	

	if (empty($_COOKIE["c_secure_pass"]) || empty($_COOKIE["c_secure_uid"]))
		return;
	if ($_COOKIE["c_secure_login"] == base64("yeah"))
	{
		//if (empty($_SESSION["s_secure_uid"]) || empty($_SESSION["s_secure_pass"]))
		//return;
	}
	$b_id = base64($_COOKIE["c_secure_uid"],false);
	$id = 0 + $b_id;
	if (!$id || !is_valid_id($id) || strlen($_COOKIE["c_secure_pass"]) != 32)
	return;

	if ($_COOKIE["c_secure_login"] == base64("yeah"))
	{
		//if (strlen($_SESSION["s_secure_pass"]) != 32)
		//return;
	}
	if ($_COOKIE["c_secure_thispagewidth"] == base64("nope"))
	$thispagewidthscreen=false;
	else
	$thispagewidthscreen=true;

	$res = sql_query("SELECT * FROM users WHERE users.id = ".sqlesc($id)." AND users.enabled='yes' AND users.status = 'confirmed' LIMIT 1");
	$row = mysql_fetch_array($res);

	if (!$row)
	return;

	$sec = hash_pad($row["secret"]);
	$global_hr_hit=$row["hrwarned"];
	//die(base64_decode($_COOKIE["c_secure_login"]));
	//if (base64_decode($_COOKIE["logouttime"])< $row["logouttime"])return;
	
	
	if ($_COOKIE["c_secure_login"] == base64("yeah"))
	{

		if ($_COOKIE["c_secure_pass"] != md5($row["logouttime"].$row["passhash"].$_SERVER["REMOTE_ADDR"]))
		return;
	}
	else
	{
		if ($_COOKIE["c_secure_pass"] !== md5($row["logouttime"].$row["passhash"]))
		return;
	}

	if ($_COOKIE["c_secure_login"] == base64("yeah"))
	{
		//if ($_SESSION["s_secure_pass"] !== md5($row["passhash"].$_SERVER["REMOTE_ADDR"]))
		//return;
	}
	if (!$row["passkey"]){
		$passkey = md5($row['username'].date("Y-m-d H:i:s").$row['passhash']);
		sql_query("UPDATE users SET passkey = ".sqlesc($passkey)." WHERE id=" . sqlesc($row["id"]));// or die(mysql_error());
	}

	$oldip = $row['ip'];
	$row['ip'] = $ip;
	$GLOBALS["CURUSER"] = $row;
	if ($_GET['clearcache'] && get_user_class() >= UC_MODERATOR) {
	    $Cache->setClearCache(1);
	}
	//if ($enablesqldebug_tweak == 'yes' && get_user_class() == $sqldebug_tweak)
	if (get_user_class() == $sqldebug_tweak) {
		error_reporting(E_ALL & ~E_NOTICE | E_STRICT);
	}
}

function autoclean() {
	global $autoclean_interval_one, $rootpath,$lang_cleanup_target,$showextinfo;
	$now = TIMENOW;

	$res = sql_query("SELECT value_u FROM avps WHERE arg = 'lastcleantime'");
	$row = mysql_fetch_array($res);
	/*if (!$row) {
		sql_query("INSERT INTO avps (arg, value_u) VALUES ('lastcleantime',$now)") or sqlerr(__FILE__, __LINE__);
		return false;
	}*/
	$ts = $row[0];
	if ($ts + $autoclean_interval_one > $now) {
		return false;
	}
	/*sql_query("UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts") or sqlerr(__FILE__, __LINE__);
	if (!mysql_affected_rows()) {
		return false;
	}*/
	//write_log("AUTO_Cleanup",'mod');
	//imdbdoubanautoupdate();
	require_once($rootpath . 'include/cleanup.php');
	return docleanup();
}

function unesc($x) {
	return $x;
}


function getsize_int($amount, $unit = "G")
{
	if ($unit == "B")
	return floor($amount);
	elseif ($unit == "K")
	return floor($amount * 1024);
	elseif ($unit == "M")
	return floor($amount * 1048576);
	elseif ($unit == "G")
	return floor($amount * 1073741824);
	elseif($unit == "T")
	return floor($amount * 1099511627776);
	elseif($unit == "P")
	return floor($amount * 1125899906842624);
}

function mksize_compact($bytes)
{
	if ($bytes < 1000 * 1024)
	return number_format($bytes / 1024, 2) . "<br />KB";
	elseif ($bytes < 1000 * 1048576)
	return number_format($bytes / 1048576, 2) . "<br />MB";
	elseif ($bytes < 1000 * 1073741824)
	return number_format($bytes / 1073741824, 2) . "<br />GB";
	elseif ($bytes < 1000 * 1099511627776)
	return number_format($bytes / 1099511627776, 3) . "<br />TB";
	else
	return number_format($bytes / 1125899906842624, 3) . "<br />PB";
}

function mksize_loose($bytes)
{
	if ($bytes < 1000 * 1024)
	return number_format($bytes / 1024, 2) . "&nbsp;KB";
	elseif ($bytes < 1000 * 1048576)
	return number_format($bytes / 1048576, 2) . "&nbsp;MB";
	elseif ($bytes < 1000 * 1073741824)
	return number_format($bytes / 1073741824, 2) . "&nbsp;GB";
	elseif ($bytes < 1000 * 1099511627776)
	return number_format($bytes / 1099511627776, 3) . "&nbsp;TB";
	else
	return number_format($bytes / 1125899906842624, 3) . "&nbsp;PB";
}

function mksize($bytes)
{
	if ($bytes < 1000 * 1024)
	return number_format($bytes / 1024, 2) . "KB";
	elseif ($bytes < 1000 * 1048576)
	return number_format($bytes / 1048576, 2) . "MB";
	elseif ($bytes < 1000 * 1073741824)
	return number_format($bytes / 1073741824, 2) . "GB";
	elseif ($bytes < 1000 * 1099511627776)
	return number_format($bytes / 1099511627776, 3) . "TB";
	else
	return number_format($bytes / 1125899906842624, 3) . "PB";
}


function mksizeint($bytes)
{
	$bytes = max(0, $bytes);
	if ($bytes < 1000)
	return floor($bytes) . " B";
	elseif ($bytes < 1000 * 1024)
	return floor($bytes / 1024) . " kB";
	elseif ($bytes < 1000 * 1048576)
	return floor($bytes / 1048576) . " MB";
	elseif ($bytes < 1000 * 1073741824)
	return floor($bytes / 1073741824) . " GB";
	elseif ($bytes < 1000 * 1099511627776)
	return floor($bytes / 1099511627776) . " TB";
	else
	return floor($bytes / 1125899906842624) . " PB";
}

function deadtime() {
	global $anninterthree;
	return time() - floor($anninterthree * 1.1);
}

function mkprettytime($s) {
	global $lang_functions;
	if ($s < 0)
	$s = 0;
	$t = array();
	foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
		$y = explode(":", $x);
		if ($y[0] > 1) {
			$v = $s % $y[0];
			$s = floor($s / $y[0]);
		}
		else
		$v = $s;
		$t[$y[1]] = $v;
	}

	if ($t["day"])
	return $t["day"] . $lang_functions['text_day'] . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
	if ($t["hour"])
	return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
	//    if ($t["min"])
	return sprintf("%d:%02d", $t["min"], $t["sec"]);
	//    return $t["sec"] . " secs";
}

function mkglobal($vars) {
	if (!is_array($vars))
	$vars = explode(":", $vars);
	foreach ($vars as $v) {
		if (isset($_GET[$v]))
		$GLOBALS[$v] = unesc(trim($_GET[$v]));
		elseif (isset($_POST[$v]))
		$GLOBALS[$v] = unesc(trim($_POST[$v]));
		else
		return 0;
	}
	return 1;
}

function tr($x,$y,$noesc=0,$relation='') {
	if ($noesc)
	$a = $y;
	else {
		$a = htmlspecialchars($y);
		$a = str_replace("\n", "<br />\n", $a);
	}
	print("<tr".( $relation ? " relation = \"$relation\"" : "")."><td class=\"rowhead nowrap\" valign=\"top\" align=\"right\">$x</td><td class=\"rowfollow\" valign=\"top\" align=\"left\">".$a."</td></tr>\n");
}

function tr_small($x,$y,$noesc=0,$relation='') {
	if ($noesc)
	$a = $y;
	else {
		$a = htmlspecialchars($y);
		//$a = str_replace("\n", "<br />\n", $a);
	}
	print("<tr".( $relation ? " relation = \"$relation\"" : "")."><td width=\"1%\" class=\"rowhead nowrap\" valign=\"top\" align=\"right\">".$x."</td><td width=\"99%\" class=\"rowfollow\" valign=\"top\" align=\"left\">".$a."</td></tr>\n");
}

function twotd($x,$y,$nosec=0){
	if ($noesc)
	$a = $y;
	else {
		$a = htmlspecialchars($y);
		$a = str_replace("\n", "<br />\n", $a);
	}
	print("<td class=\"rowhead\">".$x."</td><td class=\"rowfollow\">".$y."</td>");
}

function validfilename($name) {
	return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email) {
	return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}

function validlang($langid) {
	global $deflang;
	$langid = 0 + $langid;
	$res = sql_query("SELECT * FROM language WHERE site_lang = 1 AND id = " . sqlesc($langid)) or sqlerr(__FILE__, __LINE__);
	if(mysql_num_rows($res) == 1)
	{
		$arr = mysql_fetch_array($res)  or sqlerr(__FILE__, __LINE__);
		return $arr['site_lang_folder'];
	}
	else return $deflang;
}

function get_if_restricted_is_open()
{
	global $sptime; 
	// it's sunday
	//if($sptime == 'yes' && (date("w",time()) == '0' || (date("w",time()) == 6) && (date("G",time()) >=12 && date("G",time()) <=23)))
	if($sptime == 'yes' && (date("w",time()) == '0' || (date("w",time()) == 6)))
	{
		return true;
	}
	else
	return false;
}
include_once($rootpath . 'include/functions_plus.php');