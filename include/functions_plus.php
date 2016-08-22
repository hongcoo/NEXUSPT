<?php

function menu ($selected = "home") {
	global $lang_functions;
	global $BASEURL,$CURUSER;
	global $enableoffer, $enablespecial, $enablespecial2, $enableextforum, $extforumurl, $where_tweak,$enabledonation;
	global $USERUPDATESET;
	$script_name = $_SERVER["SCRIPT_FILENAME"];
	if (preg_match("/index/i", $script_name)) {
		$selected = "home";
	}elseif (preg_match("/forums/i", $script_name)) {
		$selected = "forums";
	}elseif (preg_match("/torrentsother/i", $script_name)) {
		$selected = "torrentsother";
	}elseif (preg_match("/torrentshd/i", $script_name)) {
		$selected = "torrentshd";
	}elseif (preg_match("/torrentquality/i", $script_name)) {
		$selected = "torrentquality";
	}elseif (preg_match("/torrents/i", $script_name)) {
		$selected = "torrents";
	}elseif (preg_match("/offers/i", $script_name) OR preg_match("/offcomment/i", $script_name)) {
		$selected = "offers";
	}elseif (preg_match("/upload/i", $script_name)) {
		$selected = "upload";
		}
	elseif (preg_match("/request/i", $script_name)) {
		$selected = "request";
	}elseif (preg_match("/subtitles/i", $script_name)) {
		$selected = "subtitles";
	}elseif (preg_match("/usercp/i", $script_name)) {
		$selected = "usercp";
	}elseif (preg_match("/topten/i", $script_name)) {
		$selected = "topten";
	}elseif (preg_match("/log/i", $script_name)) {
		$selected = "log";
	}elseif (preg_match("/rules/i", $script_name)) {
		$selected = "rules";
	}elseif (preg_match("/faq/i", $script_name)) {
		$selected = "faq";
	}elseif (preg_match("/staff/i", $script_name)) {
		$selected = "staff";
	}else
	$selected = "";
	print ("<div id=\"nav\" class=\"scroll\" ><ul id=\"mainmenu\" class=\"menu\" >");
	print ("<li" . ($selected == "home" ? " class=\"selected\"" : "") . "><a class=\"active\" style=\"margin-top: -24px; \" href=\"index.php\">&nbsp;茵蒂克丝&nbsp;</a>
	<a href=\"index.php\" class=\"normalMenu\" >" . $lang_functions['text_home'] . "</a></li>");
	
	
	/*print ("<li" . ($selected == "forums" ? " class=\"selected\"" : "") . "><a class=\"active\" style=\"margin-top: -24px; \" href=\"forums.php\">Bbs</a>
	<a  href=\"forums.php\" class=\"normalMenu\">".$lang_functions['text_forums']."</a></li>");
	print ("<li><a class=\"active\" style=\"margin-top: -24px; \" href=\"" . $extforumurl."\">FORUM</a>
	<a  href=\"" . $extforumurl."\" class=\"normalMenu\">".$lang_functions['text_forums2']."</a></li>");*/
		
	if($enableextforum == 'yes') print ("<li" . ($selected == "forums" ? " class=\"selected\"" : "") . ">
	<a class=\"active\" style=\"margin-top: -24px; \"  href=\"" . $extforumurl."\">&nbsp;外部论坛&nbsp;</a>
	<a  href=\"" . $extforumurl."\" class=\"normalMenu\">&nbsp;锦城驿站&nbsp;</a></li>");
	
	elseif($enableextforum == 'all') print ("<li" . ($selected == "forums" ? " class=\"selected\"" : "") . ">
	<a class=\"normalMenu\"  href=\"forums.php\">&nbsp;蚂蚁学园&nbsp;</a>
	<a  href=\"" . $extforumurl."\" class=\"active2\" style=\"position:absolute;display: none; \" >&nbsp;锦城驿站&nbsp;</a>
	</li>");

	elseif($enableextforum == 'no')
	print ("<li" . ($selected == "forums" ? " class=\"selected\"" : "") . ">
	<a class=\"active\" style=\"margin-top: -24px; \" href=\"forums.php\">&nbsp;内部论坛&nbsp;</a>
	<a  href=\"forums.php\" class=\"normalMenu\">&nbsp;蚂蚁学园&nbsp;</a></li>");
	
	
	print ("<li" . ($selected == "torrents" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px; \" href=\"torrents.php\">Seeds</a>
	<a  href=\"torrents.php\" class=\"normalMenu\">".$lang_functions['text_torrents']."</a></li>");
	if ($enablespecial2 == 'yes')
	print ("<li" . ($selected == "torrentshd" ? " class=\"selected\"" : "") . "><a class=\"active\" style=\"margin-top: -24px; \"  href=\"torrentshd.php\">FullHD</a>
	<a  href=\"torrentshd.php\" class=\"normalMenu\">精&nbsp;品&nbsp;区</a></li>");
	

	
	/*if($selected == "torrentshd")
	print ("<li" . (($selected == "torrents"||$selected == "torrentshd") ? " class=\"selected\"" : "") . "><a  class=\"normalMenu\"  href=\"torrentshd.php\"  >精&nbsp;品&nbsp;区</a>
	<a  href=\"torrents.php\" class=\"active2\"  style=\"position:absolute ;display: none;\"  >".$lang_functions['text_torrents']."</a></li>");
	else
	print ("<li" . (($selected == "torrents"||$selected == "torrentshd") ? " class=\"selected\"" : "") . "><a  class=\"normalMenu\" href=\"torrents.php\"  >".$lang_functions['text_torrents']."</a>
	<a  href=\"torrentshd.php\" class=\"active2\"  style=\"position:absolute ;display: none;\"  >精&nbsp;品&nbsp;区</a></li>");*/
	
	
	if ($enableoffer == 'yes')
		print ("<li" . ($selected == "offers" ? " class=\"selected\"" : "") . "><a class=\"active\" style=\"margin-top: -24px; \"  href=\"offers.php\">Offer</a>
		<a  href=\"offers.php\" class=\"normalMenu\">".$lang_functions['text_offers']."</a></li>");
	
	/*if ($enablespecial == 'yes'){
	
	if(get_torrentsothercount('showother')&&get_torrentsothercount('showquality')){
	if($selected == "torrentquality")
	print ("<li" . (($selected == "torrentquality"||$selected =="torrentsother") ? " class=\"selected\"" : "") . "><a  class=\"normalMenu\"  href=\"torrentquality.php\" ".get_torrentsothercount('qualitycolour').">鉴&nbsp;定&nbsp;区</a>
	<a  href=\"torrentsother.php\" class=\"active2\" style=\"position:absolute ;display: none;\" ".get_torrentsothercount('othercolour').">".$lang_functions['text_music']."</a></li>");
	else
	print ("<li" . (($selected == "torrentquality"||$selected =="torrentsother") ? " class=\"selected\"" : "") . "><a  class=\"normalMenu\"  href=\"torrentsother.php\" ".get_torrentsothercount('othercolour').">".$lang_functions['text_music']."</a>
	<a  href=\"torrentquality.php\" class=\"active2\" style=\"position:absolute;display: none; \" ".get_torrentsothercount('qualitycolour')." >鉴&nbsp;定&nbsp;区</a></li>");
	}	elseif(get_torrentsothercount('showother'))	
	print ("<li" . ($selected == "torrentsother" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px; \" href=\"torrentsother.php\">Test</a>
	<a  href=\"torrentsother.php\" class=\"normalMenu\" ".get_torrentsothercount('othercolour').">".$lang_functions['text_music']."</a></li>");
	elseif(get_torrentsothercount('showquality'))	
	print ("<li" . ($selected == "torrentquality" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px; \" href=\"torrentquality.php\" >Quality</a>
	<a  href=\"torrentquality.php\" class=\"normalMenu\" ".get_torrentsothercount('qualitycolour').">鉴&nbsp;定&nbsp;区</a></li>");
	}elseif(get_torrentsothercount('showquality'))	
	print ("<li" . ($selected == "torrentquality" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px; \" href=\"torrentquality.php\">Quality</a>
	<a  href=\"torrentquality.php\" class=\"normalMenu\" ".get_torrentsothercount('qualitycolour').">鉴&nbsp;定&nbsp;区</a></li>");*/
	
	if ($enablespecial == 'yes'&&get_torrentsothercount('showother'))
	print ("<li" . ($selected == "torrentsother" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px;\" href=\"torrentsother.php\">TEST</a>
	<a  href=\"torrentsother.php\" class=\"normalMenu\" ".get_torrentsothercount('othercolour').">".$lang_functions['text_music']."</a></li>");
	
	print ("<li" . ($selected == "request" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px; \" href=\"viewrequests.php\">Req</a>
	<a  href=\"viewrequests.php\" class=\"normalMenu\"".get_requestcount().">求&nbsp;种&nbsp;区</a></li>");
	
	print ("<li" . ($selected == "upload" ? " class=\"selected\"" : "") . "><a class=\"active\" style=\"margin-top: -24px; \"  href=\"upload.php\">Share</a>
	<a  href=\"upload.php\" class=\"normalMenu\">".$lang_functions['text_upload']."</a></li>");
	/*print ("<li" . ($selected == "topten" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px; \" href=\"topten.php\">Top</a>
	<a  href=\"topten.php\" class=\"normalMenu\">".$lang_functions['text_top_ten']."</a></li>");
	print ("<li" . ($selected == "log" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px; \" href=\"log.php\">Log</a>
	<a  href=\"log.php\" class=\"normalMenu\">".$lang_functions['text_log']."</a></li>");
	
	print ("<li" . ($selected == "rules" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px; \" href=\"rules.php\">Rule</a>
	<a  href=\"rules.php\" class=\"normalMenu\">".$lang_functions['text_rules']."</a></li>");
	print ("<li" . ($selected == "faq" ? " class=\"selected\"" : "") . "><a class=\"active\" style=\"margin-top: -24px; \"  href=\"faq.php\">Faq</a>
	<a href=\"faq.php\"  class=\"normalMenu\">".$lang_functions['text_faq']."</a></li>");*/
	
	if($selected == "topten")
		print ("<li" . (($selected == "log"||$selected =="topten") ? " class=\"selected\"" : "") . "><a  class=\"normalMenu\"  href=\"topten.php\">".$lang_functions['text_top_ten']."</a>
	<a  href=\"log.php\" class=\"active2\" style=\"position:absolute ;display: none;\">".$lang_functions['text_log']."</a></li>");
	else
	print ("<li" . (($selected == "log"||$selected =="topten") ? " class=\"selected\"" : "") . "><a  class=\"normalMenu\"  href=\"log.php\">".$lang_functions['text_log']."</a>
	<a  href=\"topten.php\" class=\"active2\" style=\"position:absolute;display: none; \">".$lang_functions['text_top_ten']."</a></li>");

	
	
	if($selected == "rules")
print ("<li" . (($selected == "rules"||$selected == "faq") ? " class=\"selected\"" : "") . "><a  class=\"normalMenu\"  href=\"rules.php\">".$lang_functions['text_rules']."</a>
	<a  href=\"faq.php\" class=\"active2\" style=\"position:absolute ;display: none;\" >".$lang_functions['text_faq']."</a></li>");
	else
		print ("<li" . (($selected == "rules"||$selected == "faq") ? " class=\"selected\"" : "") . "><a  class=\"normalMenu\"  href=\"faq.php\">".$lang_functions['text_faq']."</a>
	<a  href=\"rules.php\" class=\"active2\" style=\"position:absolute ;display: none;\" >".$lang_functions['text_rules']."</a></li>");
	
	print ("<li" . ($selected == "staff" ? " class=\"selected\"" : "") . "><a  class=\"active\" style=\"margin-top: -24px; \" href=\"staff.php\">Staff</a>
	<a href=\"staff.php\" class=\"normalMenu\">".$lang_functions['text_staff']."</a></li>");
	print ("</ul></div>");

	if ($CURUSER){
		if ($where_tweak == 'yes')
			$USERUPDATESET[] = "page = ".sqlesc($_SERVER['PHP_SELF']);
	}
}

function get_torrentsothercount($type='colour') {
	global $CURUSER, $Cache,$specialcatmode,$torrentmanage_class;
	//return;
	$CURUSERID = 0+$CURUSER['id'];
	if (!$count = $Cache->get_value($CURUSERID.'_torrentsothercount')){
	
	
		$rowother = @mysql_fetch_array(sql_query("SELECT count(*) FROM torrents LEFT JOIN categories ON category = categories.id  WHERE mode=$specialcatmode and banned = 'no' and seeders=1 "));
		$rowmyother = @mysql_fetch_array(sql_query("SELECT count(*) FROM torrents LEFT JOIN categories ON category = categories.id  WHERE mode=$specialcatmode and banned = 'no' AND owner=".$CURUSERID));
		
		
		$count['othercolour']=(($count['showother']=($rowmyother[0]||$rowother[0]&&get_user_class() >= $torrentmanage_class))?" style='background: none red;' ":" style='' ");

		/*$rowquality = @mysql_fetch_array(sql_query("SELECT count(*) FROM torrents  WHERE quality = 'no' and banned = 'no' AND owner=".$CURUSERID));
		$count['qualitycolour']=($rowquality[0]?" style='background: none red;' ":" style='' ");
		$count['showquality']=(get_user_class() >= UC_UPLOADER||$rowquality[0]);
		*/
		
		$Cache->cache_value($CURUSERID.'_torrentsothercount', $count, 120);
	}
	return $count[$type];
}

function get_requestcount() {
	global $CURUSER, $Cache;
	//return;
	$CURUSERID = 0+$CURUSER['id'];
	if (!$count = $Cache->get_value($CURUSERID.'_get_requestcount')){
		$row = @mysql_fetch_array(sql_query(" SELECT count(*) FROM requests LEFT JOIN resreq ON reqid=requests.id WHERE reqid>0 and finish = 'no' and userid= ".$CURUSERID));
		$count=($row[0]?" style='background: none red;' ":" style='' ");
		$Cache->cache_value($CURUSERID.'_get_requestcount', $count, 120);
	}
	return $count;
}

function get_css_row() {
	global $CURUSER, $defcss, $Cache;
	static $rows;
	$cssid = $CURUSER ? $CURUSER["stylesheet"] : $defcss;
	if (!$rows && !$rows = $Cache->get_value('stylesheet_content')){
		$rows = array();
		$res = sql_query("SELECT * FROM stylesheets ORDER BY id ASC");
		while($row = mysql_fetch_array($res)) {
			$rows[$row['id']] = $row;
		}
		$Cache->cache_value('stylesheet_content', $rows, 95400);
	}
	return $rows[$cssid];
}
function get_css_uri($file = "")
{
	$cssRow = get_css_row();
	$ss_uri = $cssRow['uri'];
	if (!$ss_uri)
		$ss_uri = get_single_value("stylesheets","uri","WHERE id=".sqlesc($defcss));
	if ($file == "")
		return $ss_uri;
	else return $ss_uri.$file;
}

function get_font_css_uri(){
	global $CURUSER;
	if ($CURUSER['fontsize'] == 'large')
		$file = 'largefont.css';
	elseif ($CURUSER['fontsize'] == 'small')
		$file = 'smallfont.css';
	else $file = 'mediumfont.css';
	return "styles/".$file;
}

function get_style_addicode()
{
	$cssRow = get_css_row();
	return $cssRow['addicode'];
}

function get_cat_folder($cat = 101)
{
	static $catPath = array();
	if (!$catPath[$cat]) {
		global $CURUSER, $CURLANGDIR;
		$catrow = get_category_row($cat);
		$catmode = $catrow['catmodename'];
		$caticonrow = get_category_icon_row($CURUSER['caticon']);
		$catPath[$cat] = "category/".$caticonrow['folder'] . ($caticonrow['multilang'] == 'yes' ? $CURLANGDIR."/" : "");
	}
	return $catPath[$cat];
}

function get_style_highlight()
{
	global $CURUSER;
	if ($CURUSER)
	{
		$ss_a = @mysql_fetch_array(@sql_query("select hltr from stylesheets where id=" . $CURUSER["stylesheet"]));
		if ($ss_a) $hltr = $ss_a["hltr"];
	}
	if (!$hltr)
	{
		$r = sql_query("SELECT hltr FROM stylesheets WHERE id=5") or die(mysql_error());
		$a = mysql_fetch_array($r) or die(mysql_error());
		$hltr = $a["hltr"];
	}
	return $hltr;
}



function ipv6statue($type = 'IPV6',$refresh=false){//1IPV6,2NETWORK

global $Cache,$schoolipv6;

$thisstate = $Cache->get_value('IPV6online_count_'.$type);
if ($thisstate==""||$refresh){
$Cache->cache_value('IPV6online_count_'.$type,"error", 60);
IF($type=='IPV6'){

	$res=mysql_fetch_row(sql_query("SELECT COUNT(DISTINCT(userid)) FROM peers WHERE  ip not LIKE '%".$schoolipv6."%' and userid > 0 "));
	if($res[0]<5)
	$thisstate=(file_get_contents_function("http://6rank.edu.cn/",30))?"ok":"error";
	else
	$thisstate="ok";
	
	
	/*$data=file_get_contents_function("http://6rank.edu.cn/",30);
	if(!$data)$thisstate="error";
	ELSE $thisstate="ok";*/

}ELSEIF($type=='NETWORK'){
$data=file_get_contents_function("http://api.douban.com/v2/movie/search?tag=cowboy&start-index=1&count=1",30);
if(!$data)$thisstate="error";
ELSE $thisstate="ok";
}
	$Cache->cache_value('IPV6online_count_'.$type, $thisstate, mt_rand(900,1800));
	if(!$refresh)imdbdoubanautoupdate();
}
	if($thisstate=="ok")return true;	
	else return false;

	}
	
function stdhead($title = "", $msgalert = false, $script = "", $place = "",$widthscreen=false)
{
	global $lang_functions;
	global $CURUSER, $CURLANGDIR, $USERUPDATESET, $iplog1, $oldip, $SITE_ONLINE, $FUNDS, $SITENAME, $SLOGAN, $logo_main, $BASEURL, $offlinemsg, $showversion,$enabledonation, $staffmem_class, $titlekeywords_tweak, $metakeywords_tweak, $metadescription_tweak, $cssdate_tweak, $deletenotransfertwo_account, $neverdelete_account, $iniupload_main,$schoolipv6;
	global $tstart;
	global $Cache;
	global $Advertisement;
	global $thispagewidthscreen;

	if($thispagewidthscreen)$widthscreen=$thispagewidthscreen; 	

	$Cache->setLanguage($CURLANGDIR);
	$Advertisement = new ADVERTISEMENT($CURUSER['id']);
	$cssupdatedate = $cssdate_tweak;
	// Variable for Start Time
	$tstart = getmicrotime(); // Start time

	//Insert old ip into iplog
	if ($CURUSER){
		if ($iplog1 == "yes") {
			if (($oldip != $CURUSER["ip"]) && $CURUSER["ip"])
			sql_query("INSERT INTO iplog (ip, userid, access) VALUES (" . sqlesc($CURUSER['ip']) . ", " . $CURUSER['id'] . ", '" . $CURUSER['last_access'] . "')   ON DUPLICATE KEY update access=values(access) ");
		}
		$USERUPDATESET[] = "last_access = ".sqlesc(date("Y-m-d H:i:s"));
		$USERUPDATESET[] = "ip = ".sqlesc($CURUSER['ip']);
	}
	header("Content-Type: text/html; charset=utf-8");
	header("Cache-Control: private");
	//header("Pragma: No-cache");
	
	if (preg_match("/index/i", $_SERVER['PHP_SELF'])){
			$title = $SITENAME;
			if ($titlekeywords_tweak)$title .= " :: ".htmlspecialchars($titlekeywords_tweak);}
	else
	{
			if($title)$title = htmlspecialchars($title)." :: ".$SITENAME;
			elseif ($titlekeywords_tweak)$title =  $SITENAME." :: ".htmlspecialchars($titlekeywords_tweak);
			else $title =  $SITENAME;
	}

	/*
		if ($title == "")
	$title = $SITENAME;
	else
	$title = $SITENAME." :: " . htmlspecialchars($title);
	if ($titlekeywords_tweak)
		$title .= " ".htmlspecialchars($titlekeywords_tweak);
	$title .= $showversion;
	*/
	

	
	if ($SITE_ONLINE == "no") {
		if (get_user_class() < UC_ADMINISTRATOR) {
			die($lang_functions['std_site_down_for_maintenance']);
		}
		else
		{
			$offlinemsg = true;
		}
	}

//if(Extension_Loaded('zlib')) Ob_Start('ob_gzhandler'); //gzipchenzhuyu
//Header("Content-type: text/html"); //gzipchenzhuyu
//header("Connection: keep-alive");
// <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
//<meta name="robots" content="noindex,nofollow,nosnippet,noarchive">
//<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
//<html xmlns="http://www.w3.org/1999/xhtml">
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
if ($metakeywords_tweak){
?>
<meta name="keywords" content="<?php echo htmlspecialchars($metakeywords_tweak)?>" />
<?php
}
if ($metadescription_tweak){
?>
<meta name="description" content="<?php echo htmlspecialchars($metadescription_tweak)?>" />
<?php
}
?>
<meta name="generator" content="<?php echo PROJECTNAME?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="application-name" content="<?php echo $SITENAME?>"/>
<base href="<?echo get_protocol_prefix().$BASEURL.$_SERVER['SCRIPT_NAME'].($_SERVER['QUERY_STRING']?"?".htmlspecialchars($_SERVER['QUERY_STRING']):"")?>" />
<?php
print(get_style_addicode());
$css_uri = get_css_uri();
$cssupdatedate=($cssupdatedate ? "?".htmlspecialchars($cssupdatedate) : "");
?>
<title><?php echo ($title)?></title>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<!--<link rel="search" type="application/opensearchdescription+xml" title="<?php echo $SITENAME?> Torrents" href="opensearch.php" />-->
<link rel="stylesheet" href="<?php echo get_font_css_uri().$cssupdatedate?>" type="text/css" />
<link rel="stylesheet" href="styles/sprites.css<?php echo $cssupdatedate?>" type="text/css" />
<link rel="stylesheet" href="<?php echo get_forum_pic_folder()."/forumsprites.css".$cssupdatedate?>" type="text/css" />
<link rel="stylesheet" href="styles/curtain_imageresizer.css<?php echo $cssupdatedate?>" type="text/css" />
<link rel="stylesheet" href="<?php echo $css_uri."theme.css".$cssupdatedate?>" type="text/css" />
<link rel="stylesheet" href="<?php echo $css_uri."DomTT.css".$cssupdatedate?>" type="text/css" />
<?php
if ($CURUSER){
	$caticonrow = get_category_icon_row($CURUSER['caticon']);
	if($caticonrow['cssfile']){
?>
<link rel="stylesheet" href="<?php echo htmlspecialchars($caticonrow['cssfile']).$cssupdatedate?>" type="text/css" />
<?php
	}
}
?>

<!--<link rel="alternate" type="application/rss+xml" title="Latest Torrents" href="torrentrss.php" />-->
<script type="text/javascript" src="javascript/jquery.js<?php echo $cssupdatedate?>"></script> 
<script type="text/javascript" src="javascript/curtain_imageresizer.js<?php echo $cssupdatedate?>"></script>
<script type="text/javascript" src="javascript/ajaxbasic.js<?php echo $cssupdatedate?>"></script>
<script type="text/javascript" src="javascript/common.js<?php echo $cssupdatedate?>"></script>
<script type="text/javascript" src="javascript/domLib.js<?php echo $cssupdatedate?>"></script>
<script type="text/javascript" src="javascript/domTT.js<?php echo $cssupdatedate?>"></script>
<script type="text/javascript" src="javascript/domTT_drag.js<?php echo $cssupdatedate?>"></script>
<script type="text/javascript" src="javascript/fadomatic.js<?php echo $cssupdatedate?>"></script>
<!--<script type="text/javascript" src="<?php echo get_css_uri()."theme.js".$cssupdatedate?>"></script>
<script type="text/javascript" src="javascript/scrollcontrol.js<?php echo $cssupdatedate?>" ></script>
<script type="text/javascript" src="javascript/sort.js<?php echo $cssupdatedate?>" ></script>-->
<script type="text/javascript" src="javascript/sort.js<?php echo $cssupdatedate?>" ></script>
<?
print userccss();
?>
</head>
<body>

<?php
if($widthscreen)
print("<div id=toppic><table class=\"headwide\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");
else
print("<div id=toppic><table class=\"head\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");
?>



	<tr>
		<td class="clear">
<?php
if ($logo_main == "")
{
?>
			<div class="logo"><?php echo htmlspecialchars($SITENAME)?></div>
			<div class="slogan"><?php echo htmlspecialchars($SLOGAN)?></div>
<?php
}
else
{
?>
			<div class="logo_img"><img src="<?php echo $logo_main?>" alt="<?php echo htmlspecialchars($SITENAME)?>" title="<?php echo htmlspecialchars($SITENAME)?> - <?php echo htmlspecialchars($SLOGAN)?>" /></div>
<?php
}
?>
		</td>
		<td class="clear nowrap" align="right" valign="middle">
<?php if ($Advertisement->enable_ad()){
		$headerad=$Advertisement->get_ad('header');
		if ($headerad){
			echo "<span id=\"ad_header\">".$headerad[0]."</span>";
		}
}
/*if ($enabledonation == 'yes'){?>
			
			<a href="http://www.antsoul.com/"><img src="<?php echo get_forum_pic_folder()?>/donate.png" alt="Make a donation" style="margin-left: 5px; margin-top: 00px;" /></a>
<?php
}*/
?>
		</td>
	</tr>
</table>
<?php
if($widthscreen)
print("<table class=\"mainouter\" width=\"90%\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\">");
else
print("<table class=\"mainouter\" width=\"982\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\">");
?>


	<tr><td id="nav_block" class="text" align="center"><div class="stickers png"></div>
<?php if (!$CURUSER) { ?>
			<a href="login.php"><font class="big"><b><?php echo $lang_functions['text_login'] ?></b></font></a><?php /* / <a href="signup.php"><font class="big"><b><?php echo $lang_functions['text_signup'] ?></b></font></a>*/?>
<?php 
print(" / <a href=\"signup.php\"><font class=\"big\"><b>".$lang_functions['text_signup']."</b></font></a>");

} 
else {
	begin_main_frame('',false,0);
	menu ();
	end_main_frame();

	$datum = getdate();
	$datum["hours"] = sprintf("%02.0f", $datum["hours"]);
	$datum["minutes"] = sprintf("%02.0f", $datum["minutes"]);
	$datum["seconds"] = sprintf("%02.0f", $datum["seconds"]);
	$ratio = get_ratio($CURUSER['id']);

	//// check every 15 minutes //////////////////
/*	$messages = $Cache->get_value('user_'.$CURUSER["id"].'_inbox_count');
	if ($messages == ""){
		$messages = get_row_count("messages", "WHERE receiver=" . sqlesc($CURUSER["id"]) . " AND location<>0");
		$Cache->cache_value('user_'.$CURUSER["id"].'_inbox_count', $messages, 900);
	}
	$outmessages = $Cache->get_value('user_'.$CURUSER["id"].'_outbox_count');
	if ($outmessages == ""){
		$outmessages = get_row_count("messages","WHERE sender=" . sqlesc($CURUSER["id"]) . " AND saved='yes'");
		$Cache->cache_value('user_'.$CURUSER["id"].'_outbox_count', $outmessages, 3600);
	}
	

if (!$connect = $Cache->get_value('user_'.$CURUSER["id"].'_connect')){
		$res3 = sql_query("SELECT connectable FROM peers WHERE userid=" . sqlesc($CURUSER["id"]) . " LIMIT 1");
		if($row = mysql_fetch_row($res3))
			$connect = $row[0];
		else $connect = 'unknown';
		$Cache->cache_value('user_'.$CURUSER["id"].'_connect', $connect, 900);
	}

	if($connect == "yes")
		$connectable = "<b><font color=\"green\">".$lang_functions['text_yes']."</font></b>";
	elseif ($connect == 'no')
		$connectable = "<a href=\"faq.php#id21\"><b><font color=\"red\">".$lang_functions['text_no']."</font></b></a>";
	else
		$connectable = $lang_functions['text_unknown'];
*/
	//// check every 60 seconds //////////////////
	$activeseed = $Cache->get_value('user_'.$CURUSER["id"].'_active_seed_count');
	if ($activeseed == ""){
		//$activeseed = get_row_count("peers","WHERE userid=" . sqlesc($CURUSER["id"]) . " AND seeder='yes' AND connectable = 'yes' ");
		$activeseed = number_format(get_single_value("peers","COUNT(DISTINCT(torrent))","WHERE userid=" . sqlesc($CURUSER["id"]) . " AND seeder='yes'"));
		$Cache->cache_value('user_'.$CURUSER["id"].'_active_seed_count', $activeseed, 120);
	}
	$activeleech = $Cache->get_value('user_'.$CURUSER["id"].'_active_leech_count');
	if ($activeleech == ""){
	$activeleech = number_format(get_single_value("peers","COUNT(DISTINCT(torrent))","WHERE userid=" . sqlesc($CURUSER["id"]) . " AND seeder='no'"));
		//$activeleech = get_row_count("peers","WHERE userid=" . sqlesc($CURUSER["id"]) . " AND seeder='no' AND connectable = 'yes' ");
		$Cache->cache_value('user_'.$CURUSER["id"].'_active_leech_count', $activeleech, 120);
	}
	$unread = $Cache->get_value('user_'.$CURUSER["id"].'_unread_message_count');
	if ($unread == ""){
		$unread = get_row_count("messages","WHERE unread='yes' and receiver=" . sqlesc($CURUSER["id"]));
		$Cache->cache_value('user_'.$CURUSER["id"].'_unread_message_count', $unread, 60);
	}
	$invite_have = $Cache->get_value('user_'.$CURUSER["id"].'_invite_have_count');
	if ($invite_have == ""){
		$invite_have = (get_row_count("invites", "WHERE inviter=".$CURUSER['id']))."/".(0+$CURUSER['invites']);
		$Cache->cache_value('user_'.$CURUSER["id"].'_invite_have_count', $invite_have, 60);
	}
	
	$hr_have = $Cache->get_value('user_'.$CURUSER["id"].'_hr_have_count');
	if ($hr_have == ""){
		$hr_have = number_format(0+get_row_count("snatched", "WHERE userid=".$CURUSER['id']." and hr='A' AND finished='yes'")).'/'.number_format(0+get_row_count("snatched", "WHERE userid=".$CURUSER['id']." and hr='C' AND finished='yes' "));
		$Cache->cache_value('user_'.$CURUSER["id"].'_hr_have_count', $hr_have, 1800);
	}
	
	$inboxpic = "<img class=\"".($unread ? "redbookmark" : "bookmark")."\" src=\"pic/trans.gif\" alt=\"inbox\" title=\"".($unread ? $lang_functions['title_inbox_new_messages'] : $lang_functions['title_inbox_no_new_messages'])."\" />";
?>

<table id="info_block" cellpadding="4" cellspacing="0" border="0" width="100%"><tr>
	<td><table width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
		<td class="bottom" align="left"><span class="medium"><?php echo $lang_functions['text_welcome_back'] ?>, <?php echo get_username($CURUSER['id'])?>  [<a href="logout.php" title="且退出在其他电脑上的登录"><?php echo $lang_functions['text_logout'] ?></a>] [<a href="usercp.php"><?php echo $lang_functions['text_user_cp2'] ?></a>] <?php if (get_user_class() >= UC_UPLOADER) { ?> [<a href="staffpanel.php"><?php echo $lang_functions['text_staff_panel'] ?></a>] <?php } if (get_user_class() >= UC_SYSOP) { ?> [<a href="settings.php"><?php echo $lang_functions['text_site_settings'] ?></a>]<?php } ?> 
		
		[<a href="torrents.php?inclbookmarked=1&amp;allsec=1&amp;incldead=0"><?php echo $lang_functions['text_bookmarks'] ?></a>] 
				<?if($CURUSER['addbonus'] > TIMENOW){?>
				[<a id="game" href="javascript:game();"><b>Gamers</b></a>]
				<span id="gamelist" class="dropmenu" style="display: none"><ul>
				<li><a href="bakatest.php"><b>每日签到</b></a></li>
				<li><a href="lottery.php"><b>乐透彩券</b></a></li>
				<li><a href="blackjack.php"><b>二十一点</b></a></li>
				<li><a href="casino.php"><b>博彩大厅</b></a></li>
				<li><a href="bet.php"><b>竞猜大厅</b></a></li>
				</ul></span>
				<?}else{?>
				[<a id="game" href="bakatest.php"><b><font color="red">每日签到</font></b></a>]
				<?}?>
		[<a  href="javascript: IPV6mark();" title=Switch><?php echo get_tracker_state($CURUSER['MODEMAX']) ?></a>]
	<?php 
	
	/*if(!ipv6statue('NETWORK'))
print("<img class=\"netwarned\" src=\"pic/trans.gif\"  title=\"虽然不怎么清楚,但是无法连接豆瓣服务器\">&nbsp;");

	if(!ipv6statue('IPV6'))
print("<img class=\"ipv6warned\" src=\"pic/trans.gif\"  title=\"".$lang_functions['IPV6BROKEN']."\">&nbsp;"); */
//TODO 增加了注释



		/*$prolinkclicks = $Cache->get_value('user_'.$CURUSER["id"].'_prolinkclicks');
	if ($prolinkclicks==""){
		$prolinkclicks=get_row_count("prolinkclicks", "WHERE userid=".$CURUSER['id']);
		$Cache->cache_value('user_'.$CURUSER["id"].'_prolinkclicks', $prolinkclicks, 3600);
		
	}<a href="promotionlink.php"><font class = 'color_bonus'>&nbsp;<?php echo $lang_functions['promotion']?></font><?php echo $prolinkclicks;?></a>*/

  ?>
		<br />
		<a href="mybonus.php"><font class = 'color_bonus'><?php echo $lang_functions['text_bonus'] ?></font><?php echo number_format($CURUSER['seedbonus'], 1)?></a>
		<a href="invite.php"><font class = 'color_invite'>&nbsp;<?php echo $lang_functions['text_invite'] ?></font><?php echo $invite_have;?></a><a href="myhr.php"><font class='color_bonus'>&nbsp;&nbsp;H&amp;R:</font><?php echo ($hr_have)?></a>
	<font class="color_ratio">&nbsp;<?php echo $lang_functions['text_ratio'] ?></font><?php echo $ratio?><font class='color_uploaded'>&nbsp;&nbsp;<?php echo $lang_functions['text_uploaded'] ?></font><?php echo mksize($CURUSER['uploaded'])?><font class='color_downloaded'>&nbsp;&nbsp;<?php echo $lang_functions['text_downloaded'] ?></font><?php echo mksize($CURUSER['downloaded'])?>
	<font class='color_active'>&nbsp;<?php echo $lang_functions['text_active_torrents'] ?></font>&nbsp;
	<a href="getusertorrentlist.php?type=seeding"><img class="arrowup" alt="Torrents seeding" title="<?php echo $lang_functions['title_torrents_seeding'] ?>" src="pic/trans.gif" /><?php echo $activeseed?></a>&nbsp;&nbsp;<a href="getusertorrentlist.php?type=leeching"><img class="arrowdown" alt="Torrents leeching" title="<?php echo $lang_functions['title_torrents_leeching'] ?>" src="pic/trans.gif" /><?php echo $activeleech?></a>&nbsp;&nbsp;
	<?php echo maxslots();?></span></td>

	<td class="bottom" align="right"><span class="medium">
	<?php 

echo $lang_functions['text_the_time_is_now'] ;
echo $datum[hours].":".$datum[minutes].":".$datum[seconds];
		
  ?>
	
	<br />

<?php 	
	if (get_user_class() >= $staffmem_class&&preg_match("/staffpanel/i", $_SERVER['PHP_SELF'])){
	$totalreports = $Cache->get_value('staff_report_count');
	if ($totalreports == ""){
		$totalreports = get_row_count("reports");
		$Cache->cache_value('staff_report_count', $totalreports, 900);
	}
	$totalsm = $Cache->get_value('staff_message_count');
	if ($totalsm == ""){
		$totalsm = get_row_count("staffmessages");
		$Cache->cache_value('staff_message_count', $totalsm, 900);
	}
	$totalcheaters = $Cache->get_value('staff_cheater_count');
	if ($totalcheaters == ""){
		$totalcheaters = get_row_count("cheaters");
		$Cache->cache_value('staff_cheater_count', $totalcheaters, 900);
	}
	print("<a href=\"cheaterbox.php\"><img class=\"cheaterbox\" alt=\"cheaterbox\" title=\"".$lang_functions['title_cheaterbox']."\" src=\"pic/trans.gif\" />  </a>".$totalcheaters."  <a href=\"reports.php\"><img class=\"reportbox\" alt=\"reportbox\" title=\"".$lang_functions['title_reportbox']."\" src=\"pic/trans.gif\" />  </a>".$totalreports."  <a href=\"staffbox.php\"><img class=\"staffbox\" alt=\"staffbox\" title=\"".$lang_functions['title_staffbox']."\" src=\"pic/trans.gif\" />  </a>".$totalsm."  ");
	}

	print("<a href=\"messages.php\">".$inboxpic."</a> "
	//.($unread ? "(".$unread.$lang_functions['text_message_new'].")": "")
	);
	//print("  <a href=\"messages.php?action=viewmailbox&amp;box=-1\"><img class=\"sentbox\" alt=\"sentbox\" title=\"".$lang_functions['title_sentbox']."\" src=\"pic/trans.gif\" /></a> ".($outmessages ? $outmessages : "0"));
	print(" <a href=\"friends.php\"><img class=\"buddylist\" alt=\"Buddylist\" title=\"".$lang_functions['title_buddylist']."\" src=\"pic/trans.gif\" /></a>");
	print(" <a href=\"getrss.php\"><img class=\"rss\" alt=\"RSS\" title=\"".$lang_functions['title_get_rss']."\" src=\"pic/trans.gif\" /></a>");
?>

	</span></td>
	</tr></table></td>
</tr></table>

</td></tr>

<tr><td id="outer" align="center" class="outer" style="padding-top: 20px; padding-bottom: 20px">
<?php
	/*if ($Advertisement->enable_ad()){
			$belownavad=$Advertisement->get_ad('belownav');
			if ($belownavad)
			echo "<div align=\"center\" style=\"margin-bottom: 10px\" id=\"ad_belownav\">".$belownavad[0]."</div>";
	}*/
if ($msgalert)
{
	function msgalert($url, $text, $bgcolor = "red")
	{
		print("<p><table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" style='background: transparent;'><tr><td style='box-shadow: 2px 2px 5px gray;border-radius: 3px;border: none; padding: 10px; background: ".$bgcolor."'>\n");
		print("<b><a href=\"".$url."\"><font color=\"white\">".$text."</font></a></b>");
		print("</td></tr></table></p><br />");
		
	}
	
	
		/*if($CURUSER['addbonus'] <= TIMENOW)
	{
		if($_GET["action"]=='addbonus'){
		 
		 
		 if($CURUSER['addbonus'] >= TIMENOW-3600*30)
		 $CURUSER['addbonusday']++;
		 else
		 $CURUSER['addbonusday']=0;
		 
		
		
		if(get_user_class()>1)
		$bonds=mt_rand (5+get_user_class()+$CURUSER['addbonusday']/2,20+get_user_class()+$CURUSER['addbonusday']);
		else
		$bonds=5;
		KPS("+",$bonds,$CURUSER['id']);
		$until = strtotime(date("Y-m-d H:i:s")) + 3600*20;
		sql_query("UPDATE users  SET addbonus=".sqlesc($until)." , addbonusday = ".$CURUSER['addbonusday']." WHERE id = ".$CURUSER['id']);
		
		if(!$CURUSER['addbonusday'])
		$text = $lang_functions['text_addbonus2'].$bonds.$lang_functions['text_addbonus3'];
		else
		$text = "连续".$CURUSER['addbonusday']."天登录,".$lang_functions['text_addbonus2'].$bonds.$lang_functions['text_addbonus3'];
		msgalert("index.php", $text, "green");
		
	}else{	
		
		$text =$lang_functions['text_addbonus1'];
		msgalert("bakatest.php", $text, "orange");
	}
	}*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	if($CURUSER['leechwarn'] == 'yes')
	{
		$kicktimeout = gettime($CURUSER['leechwarnuntil'], false, false, true);
		$text = $lang_functions['text_please_improve_ratio_within'].$kicktimeout.$lang_functions['text_or_you_will_be_banned'];
		msgalert("downloadnotice.php?type=ratio", $text, "orange");
	}
	if($deletenotransfertwo_account) //inactive account deletion notice
	{
		if ($CURUSER['downloaded'] == 0 && ($CURUSER['uploaded'] == 0 || $CURUSER['uploaded'] == $iniupload_main))
		{
			$neverdelete_account = ($neverdelete_account <= UC_VIP ? $neverdelete_account : UC_VIP);
			if (get_user_class() < $neverdelete_account)
			{
				$secs = $deletenotransfertwo_account*24*60*60;
				$addedtime = strtotime($CURUSER['added']);
				if (TIMENOW > $addedtime+($secs/3)) // start notification if one third of the time has passed
				{
					$kicktimeout = gettime(date("Y-m-d H:i:s", $addedtime+$secs), false, false, true);
					$text = $lang_functions['text_please_download_something_within'].$kicktimeout.$lang_functions['text_inactive_account_be_deleted'];
					msgalert("rules.php", $text, "gray");
				}
			}
		}
	}
	if($CURUSER['showclienterror'] == 'yes')
	{
		$text = $lang_functions['text_banned_client_warning'];
		msgalert("downloadnotice.php?type=client", $text, "black");
	}
	if ($unread)
	{
			//print("<bgsound src='pic/msg.wav' loop= '0'>");
			
			//print("<object type=\"application/x-mplayer2\" data=\"pic/msg.wav\" width=\"0\" height=\"0\"> <param name=\"src\" value=\"pic/msg.wav\"> <param name=\"autoplay\" value=\"1\"> </object>");
		return_audio("pic/msg.wav","pic/msg2.wav");
		$text = $lang_functions['text_you_have'].$unread.$lang_functions['text_new_message'] . add_s($unread) . $lang_functions['text_click_here_to_read'];
		msgalert("messages.php",$text, "red");
	}
/*
	$pending_invitee = $Cache->get_value('user_'.$CURUSER["id"].'_pending_invitee_count');
	if ($pending_invitee == ""){
		$pending_invitee = get_row_count("users","WHERE status = 'pending' AND invited_by = ".sqlesc($CURUSER[id]));
		$Cache->cache_value('user_'.$CURUSER["id"].'_pending_invitee_count', $pending_invitee, 900);
	}
	if ($pending_invitee > 0)
	{
		$text = $lang_functions['text_your_friends'].add_s($pending_invitee).is_or_are($pending_invitee).$lang_functions['text_awaiting_confirmation'];
		msgalert("invite.php?id=".$CURUSER[id],$text, "red");
	}*/
	$settings_script_name = $_SERVER["SCRIPT_FILENAME"];
	if (!preg_match("/index/i", $settings_script_name))
	{
		$new_news = $Cache->get_value('user_'.$CURUSER["id"].'_unread_news_count');
		if ($new_news == ""){
			$new_news = get_row_count("news","WHERE notify = 'yes' AND added > ".sqlesc($CURUSER['last_home']));
			$Cache->cache_value('user_'.$CURUSER["id"].'_unread_news_count', $new_news, 300);
		}
		if ($new_news > 0)
		{
			$text = $lang_functions['text_there_is'].is_or_are($new_news).$new_news.$lang_functions['text_new_news'];
			msgalert("index.php",$text, "green");
		}
	}

	if (get_user_class() >= $staffmem_class)
	{
		$numreports = $Cache->get_value('staff_new_report_count');
		if ($numreports == ""){
			$numreports = get_row_count("reports","WHERE dealtwith=0");
			$Cache->cache_value('staff_new_report_count', $numreports, 900);
		}
		if ($numreports){
			$text = $lang_functions['text_there_is'].is_or_are($numreports).$numreports.$lang_functions['text_new_report'] .add_s($numreports);
			msgalert("reports.php",$text, "blue");
		}
		$nummessages = $Cache->get_value('staff_new_message_count');
		if ($nummessages == ""){
			$nummessages = get_row_count("staffmessages","WHERE answered='no'");
			$Cache->cache_value('staff_new_message_count', $nummessages, 900);
		}
		if ($nummessages > 0) {
			$text = $lang_functions['text_there_is'].is_or_are($nummessages).$nummessages.$lang_functions['text_new_staff_message'] . add_s($nummessages);
			msgalert("staffbox.php",$text, "blue");
		}
		$numcheaters = $Cache->get_value('staff_new_cheater_count');
		if ($numcheaters == ""){
			$numcheaters = get_row_count("cheaters","WHERE dealtwith=0");
			$Cache->cache_value('staff_new_cheater_count', $numcheaters, 900);
		}
		if ($numcheaters&&preg_match("/staffpanel/i", $_SERVER['PHP_SELF'])){
			$text = $lang_functions['text_there_is'].is_or_are($numcheaters).$numcheaters.$lang_functions['text_new_suspected_cheater'] .add_s($numcheaters);
			msgalert("cheaterbox.php",$text, "blue");
		}
	}
}
		if ($offlinemsg)
		{
			print("<p><table width=\"737\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr><td style='padding: 10px; background: red' class=\"text\" align=\"center\">\n");
			print("<font color=\"white\">".$lang_functions['text_website_offline_warning']."</font>");
			print("</td></tr></table></p><br />\n");
		}
}
}


function showanalyticscode($return=false){
if($_COOKIE["c_secure_user_link_online"]=='error')return false;
else return true;

if($_COOKIE["c_secure_user_link_online"]=='success'||$return&&!$_COOKIE["c_secure_user_link_online"])return true;
else return false;
$ip=getip();
if (!ip2long($ip))return false;


		$reserved_ips = array (
		array('10.0.0.0','11.0.0.0'),
		);

		foreach ($reserved_ips as $r)
		{
			$min = ip2long($r[0]);
			$max = ip2long($r[1]);
			if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
}

function stdfoot() {
	global $SITENAME,$BASEURL,$Cache,$datefounded,$tstart,$icplicense_main,$add_key_shortcut,$query_name,$query_name_num, $USERUPDATESET, $CURUSER, $enablesqldebug_tweak, $sqldebug_tweak, $Advertisement, $analyticscode_tweak,$query_time,$analyticscodelocation_tweak;
	print("</td></tr></table>");
	print("<div id=\"footer\">");
	if(!$Advertisement)write_log("STDFOOT_ERROR:".$_SERVER["REQUEST_URI"],'mod');
	if ($Advertisement->enable_ad()){
			$footerad=$Advertisement->get_ad('footer');
			if ($footerad)
			echo "<div align=\"center\" style=\"margin-top: 10px\" id=\"ad_footer\">".$footerad[0]."</div>";
	}
	//print("<div style=\"margin-top: 10px; margin-bottom: 30px;\" align=\"center\">");
	print("<div style=\"margin-top: 10px;\" align=\"center\">");
	if ($CURUSER){
		sql_query("UPDATE users SET " . join(",", $USERUPDATESET) . " WHERE id = ".$CURUSER['id']);
	}
	// Variables for End Time
	$tend = getmicrotime();
	$totaltime = ($tend - $tstart);
	$alltotaltime=0+($tend-TIMENOWSTART);
	$year = substr($datefounded, 0, 4);
	$yearfounded = ($year ? $year : 2007);
	$query_time=0+$query_time;
	$totaltime=0+$totaltime;
	//sql_query("INSERT INTO chenzhuyudubug (num, page , time ,sqltotaltime ,pagecreatetime) VALUES (".count($query_name).",".sqlesc($_SERVER["REQUEST_URI"])." , ".sqlesc(date("Y-m-d H:i:s"))." , $query_time ,$totaltime )");
print ("<a  style=\"display: none;\" id=\"lightbox\"  class=\"lightbox\"  onclick=\"Return();\" onmousewheel=\"return false;\"  ondragstart='return false;' onselectstart='return false;'></a>
<div style=\"display: none;\" id=\"curtain\" class=\"curtain\" onclick=\"Return();\" onmousewheel=\"return false;\" ></div>");
	print(" (c) "." <a href=\"aboutnexus.php\" target=\"_self\">".$SITENAME."</a> ".($icplicense_main ? " ".$icplicense_main." " : "").(date("Y") != $yearfounded ? $yearfounded."-" : "").date("Y")." ".VERSION);
	
	if (showanalyticscode(true))
	print("\n<span style=\"display: none\"><img src=\"http://www.baidu.com/search/img/logo.gif?".lcg_value()."\" onError=\"javascript:linestate('error')\" onLoad=\"javascript:linestate('success');\"/></span>");
	print '<script type="text/javascript" src="'. get_css_uri().'theme.js"></script>';
?>
	<script type="text/javascript" src="javascript/scrollcontrol.js" ></script>
	<!--[if IE]><script type="text/javascript" src="javascript/letskillie6.zh_CN.js" ></script><![endif]-->
	<!--[if (gt IE 6)|!(IE)]><!-->
	<script type="text/javascript" src="javascript/colorfade.js<?php echo $cssupdatedate?>"></script>
	<?php
	if($CURUSER['namecolour'])print("<script>LinkEndColor = \"".str_replace("#","",$CURUSER['namecolour'])."\"; </script>");
	?> 
	<!--<![endif]--> 
<?
	if ($analyticscodelocation_tweak)print("\n".$analyticscodelocation_tweak."\n");	
	if ($analyticscode_tweak&&showanalyticscode())print("\n".$analyticscode_tweak."\n");		
	print ("<br /><br />");
	//printf ("[page created in <b> %f </b>", $query_time);
	//printf ("@<b> %f </b> sec", $totaltime);
	printf ("[page created in <b> %f @ %f </b>sec", $totaltime,$alltotaltime);
	print (" with <b>".($query_name_num)."</b> db queries, <b>".$Cache->getCacheReadTimes()."</b> reads and <b>".$Cache->getCacheWriteTimes()."</b> writes of memcached and <b>".mksize(memory_get_usage())."</b> ram]");
	print ("</div>\n");
	print ("<br /><br />");
	
	if ($enablesqldebug_tweak == 'yes' && get_user_class() == $sqldebug_tweak)
	{
		print("<div id=\"sql_debug\">SQL query list@$query_time: <ul>");
		foreach($query_name as $query) {
			print("<li>".htmlspecialchars($query)."</li>");
		}
		print("</ul>");
		print("Memcached key read: <ul>");
		foreach($Cache->getKeyHits('read') as $keyName => $hits) {
			print("<li>".htmlspecialchars($keyName)." : ".$hits."</li>");
		}
		print("</ul>");
		print("Memcached key write: <ul>");
		foreach($Cache->getKeyHits('write') as $keyName => $hits) {
			print("<li>".htmlspecialchars($keyName)." : ".$hits."</li>");
		}
		print("</ul>");
		print("</div>");
	}
	//print ("<a onclick=\"Return();\" onmousewheel=\"return bbimg(this);\" style=\"display: none;\" id=\"lightbox\" class=\"lightbox\" ></a><div style=\"display: none;\" id=\"curtain\" class=\"curtain\"></div>");
	if ($add_key_shortcut != "")
	print($add_key_shortcut);
	if(!$Cache->get_value('here_now_have_no_mail'))print("<script>jQuery.get('sendmail.php');</script>");
	print("</div>");
	
	print("</div></body></html>");
//if(Extension_Loaded('zlib')) {Ob_End_Flush();} //gzipchenzhuyu
	//echo replacePngTags(ob_get_clean());
	//unset($_SESSION['queries']);
	//mysql_close();//chenzhuyu
}

function genbark($x,$y) {
	stdhead($y);
	print("<h1>" . htmlspecialchars($y) . "</h1>\n");
	print("<p>" . htmlspecialchars($x) . "</p>\n");
	stdfoot();
	exit();
}

function mksecret($len = 20) {
	$ret = "";
	for ($i = 0; $i < $len; $i++)
	$ret .= chr(mt_rand(100, 120));
	return $ret;
}

function httperr($die = true) {
	if (substr(php_sapi_name(), 0, 3) == 'cgi')
		header("Status: 404 Not Found", TRUE);
	else
		header("HTTP/1.0 404 Not found");  	
	if($die){print("<h1>请求无效或已过期</h1>\n");exit();}
}

function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff, $securelogin=false, $ssl=false, $trackerssl=false,$thispagewidth=false){ 
	global $BASEHOST;
	//logoutcookie();
	if ($expires != 0x7fffffff)
	$expires = time()+$expires;

	
	//setcookie("c_secure_uid", base64($id), $expires, "/");
	setcookie("c_secure_uid", base64($id), 0x7fffffff, "/");
	setcookie("c_secure_pass", $passhash, $expires, "/");
	if($ssl)
	setcookie("c_secure_ssl", base64("yeah"), $expires, "/");
	//else
	//setcookie("c_secure_ssl", base64("nope"), $expires, "/");

	if($trackerssl)
	setcookie("c_secure_tracker_ssl", base64("yeah"), $expires, "/");
	//else
	//setcookie("c_secure_tracker_ssl", base64("nope"), $expires, "/");

	if ($securelogin)
	setcookie("c_secure_login", base64("yeah"), $expires, "/");
	//else
	//setcookie("c_secure_login", base64("nope"), $expires, "/");
	
	if (!$thispagewidth)
	setcookie("c_secure_thispagewidth", base64("nope"), $expires, "/");
	else
	setcookie("c_secure_thispagewidth", "", 0x7fffffff,"/");
	//setcookie("c_secure_thispagewidth", base64("yeah"), $expires, "/");
	

	//setcookie("logouttime", base64(TIMENOW), $expires, "/");
	if ($updatedb)
	sql_query("UPDATE users SET last_login = NOW(), lang=" . sqlesc(get_langid_from_langcookie()) . " WHERE id = ".sqlesc($id));
}

function set_langfolder_cookie($folder, $expires = 0x7fffffff)
{	global $BASEHOST;
	if ($expires != 0x7fffffff)
	$expires = time()+$expires;

	setcookie("c_secure_lang_folder", $folder, $expires, "/");
}

function get_protocol_prefix()
{
	global $securelogin;
	if ($securelogin == "yes"||$_SERVER["HTTPS"]=='on') {
		return "https://";
	} elseif ($securelogin == "no") {
		return "http://";
	} else {
		if (!isset($_COOKIE["c_secure_ssl"])) {
			return "http://";
		} else {
			return base64($_COOKIE["c_secure_ssl"],false) == "yeah" ? "https://" : "http://";
		}
	}
}

function get_langid_from_langcookie()
{
	global $CURLANGDIR;
	$row = mysql_fetch_array(sql_query("SELECT id FROM language WHERE site_lang = 1 AND site_lang_folder = " . sqlesc($CURLANGDIR) . "ORDER BY id ASC")) or sqlerr(__FILE__, __LINE__);
	return $row['id'];
}

function make_folder($pre, $folder_name)
{
	$path = $pre . $folder_name;
	if(!file_exists($path))
	mkdir($path,0777,true);
	return $path;
}

function logoutcookie() {
global $BASEHOST;
	//setcookie("c_secure_uid", "", 0x7fffffff, "/");
	setcookie("c_secure_pass", "", 0x7fffffff, "/");
	setcookie("c_secure_ssl", "", 0x7fffffff, "/");
	setcookie("c_secure_tracker_ssl", "", 0x7fffffff, "/");
	setcookie("c_secure_login", "", 0x7fffffff, "/");
	setcookie("c_secure_lang_folder", "", 0x7fffffff, "/");
}

function base64 ($string, $encode=true) {
	if ($encode)
	return base64_encode($string);
	else
	return base64_decode($string);
}

function checkloggedinorreturn(){
	if(!preg_match('/(text\/html|application\/xaml\+xml)/i', $_SERVER['HTTP_ACCEPT'])&&get_user_class()>=UC_FORUM_MODERATOR)
	{
	//header("HTTP/1.0 403 Forbidden");
	print("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body>管理组请更换标准浏览器<br /></body></html>\n");
	sql_query("INSERT INTO chenzhuyudubug (page) VALUES (".sqlesc($_SERVER['HTTP_ACCEPT'].$_SERVER['REQUEST_URI'].$_SERVER['HTTP_USER_AGENT']).")");
	die;}
}

function loggedinorreturn($mainpage = false) {
	global $CURUSER,$BASEURL,$BASEURLV4,$BASEURLV6;
	

/*
if(($_SERVER["HTTP_HOST"]!=$BASEURLV4)&&( $_SERVER["HTTP_HOST"]!=$BASEURLV6))

{
print("请使用正确域名登陆网站");
header("Refresh: 2; url=" . get_protocol_prefix() . "$BASEURLV4/");
die();}
*/

	if (!$CURUSER) {
		if ($mainpage)
		redirect("login.php");
		else {
			$to = $_SERVER["REQUEST_URI"];
			$to = basename($to);
			redirect("login.php?returnto=" . rawurlencode($to));
			//print "<meta http-equiv=\"refresh\" content=\"0; url='" . get_protocol_prefix() . "$BASEURL/login.php?returnto=" . rawurlencode($to)."'\">";
		}
		exit();
	}
//checkloggedinorreturn();
}

function deletetorrent($id) {
	global $torrent_dir,$SUBSPATH,$rootpath;
	sql_query("DELETE FROM torrents WHERE id = ".mysql_real_escape_string($id));
	sql_query("DELETE FROM snatched WHERE torrentid = ".mysql_real_escape_string($id));
	
	foreach(array("files", "comments") as $x) {
		sql_query("DELETE FROM $x WHERE torrent = ".mysql_real_escape_string($id));
	}
	//unlink($rootpath."$torrent_dir/$id.torrent");
	
	
	
	$res = sql_query("SELECT * FROM subs  where torrent_id =".mysql_real_escape_string($id)) or sqlerr();
		while ($arr = mysql_fetch_assoc($res)){ 
		sql_query("DELETE FROM subs WHERE id=".$arr['id']) or sqlerr(__FILE__, __LINE__);
		unlink($rootpath."$SUBSPATH/$arr[torrent_id]/$arr[id].$arr[ext]");
		}
					
					
					
}

function pager($rpp, $count, $href, $opts = array(), $pagename = "page") {
	global $lang_functions,$add_key_shortcut;
	$pages = ceil($count / $rpp);

	if (!$opts["lastpagedefault"])
	$pagedefault = 0;
	else {
		$pagedefault = floor(($count - 1) / $rpp);
		if ($pagedefault < 0)
		$pagedefault = 0;
	}

	if (isset($_GET[$pagename])) {
		$page = 0 + $_GET[$pagename];
		if ($page < 0)
		$page = $pagedefault;
	}
	else
	$page = $pagedefault;

	$pager = "";
	$mp = $pages - 1;
	$page=min($page,max(0,$mp));

	//Opera (Presto) doesn't know about event.altKey
	$is_presto = strpos($_SERVER['HTTP_USER_AGENT'], 'Presto');
	$as = "<b title=\"".($is_presto ? $lang_functions['text_shift_pageup_shortcut'] : $lang_functions['text_alt_pageup_shortcut'])."\">&lt;&lt;&nbsp;".$lang_functions['text_prev']."</b>";
	if ($page >= 1) {
		$pager .= "<a href=\"".htmlspecialchars($href.$pagename."=" . ($page - 1) ). "\">";
		$pager .= $as;
		$pager .= "</a>";
	}
	else
	$pager .= "<font class=\"gray\">".$as."</font>";
	
	if($mp>0)
	$pager .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"".htmlspecialchars($href.$pagename."=" . mt_rand (0,$mp) ). "\"><b>随便看看</b></a>&nbsp;&nbsp;&nbsp;&nbsp;";
	else
	$pager .= "<font class=\"gray\"><b>&nbsp;&nbsp;随便看看&nbsp;&nbsp;</b></font>";
	
	$as = "<b title=\"".($is_presto ? $lang_functions['text_shift_pagedown_shortcut'] : $lang_functions['text_alt_pagedown_shortcut'])."\">".$lang_functions['text_next']."&nbsp;&gt;&gt;</b>";
	if ($page < $mp && $mp >= 0) {
		$pager .= "<a href=\"".htmlspecialchars($href.$pagename."=" . ($page + 1) ). "\">";
		$pager .= $as;
		$pager .= "</a>";
	}
	else
	$pager .= "<font class=\"gray\">".$as."</font>";

	if ($count) {
		$pagerarr = array();
		$dotted = 0;
		$dotspace = 3;
		$dotend = $pages - $dotspace;
		$curdotend = $page - $dotspace;
		$curdotstart = $page + $dotspace;
		for ($i = 0; $i < $pages; $i++) {
			if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
				if (!$dotted)
				$pagerarr[] = "...";
				$dotted = 1;
				continue;
			}
			$dotted = 0;
			$start = $i * $rpp + 1;
			$end = $start + $rpp - 1;
			if ($end > $count)
			$end = $count;
			$text = "$start&nbsp;-&nbsp;$end";
			if ($i != $page)
			$pagerarr[] = "<a href=\"".htmlspecialchars($href.$pagename."=".$i)."\"><b>$text</b></a>";
			else
			$pagerarr[] = "<font class=\"gray\"><b>$text</b></font>";
		}
		$pagerstr = join(" | ", $pagerarr);
		$pagertop = "<p align=\"center\">$pager<br />$pagerstr</p>\n";
		$pagerbottom = "<p align=\"center\">$pagerstr<br />$pager</p>\n";
	}
	else {
		$pagertop = "<p align=\"center\">$pager</p>\n";
		$pagerbottom = $pagertop;
	}

	$start = $page * $rpp;
	$add_key_shortcut = key_shortcut($page,$pages-1);
	return array($pagertop, $pagerbottom, "LIMIT $start,$rpp");
}

function commenttable($rows, $type, $parent_id, $review = false)
{
	global $lang_functions;
	global $CURUSER, $commanage_class;
	global $Advertisement,$showschool;
	begin_main_frame();
	begin_frame();

	$count = 0;
	if ($Advertisement->enable_ad())
		$commentad = $Advertisement->get_ad('comment');
	foreach ($rows as $row)
	{
		$userRow = get_user_row($row['user']);
		if ($count>=1)
		{
			if ($Advertisement->enable_ad()){
				if ($commentad[$count-1])
				echo "<div align=\"center\" style=\"margin-top: 10px\" id=\"ad_comment_".$count."\">".$commentad[$count-1]."</div>";
			}
		}
		print("<div style=\"margin-top: 8pt; margin-bottom: 8pt;\"><table id=\"cid".$row["id"]."\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr><td class=\"embedded\" width=\"99%\">#" . $row["id"] . "&nbsp;&nbsp;<font color=\"gray\">".$lang_functions['text_by']."</font>");
		print(get_username($row["user"],false,true,true,false,false,true));
		print("&nbsp;&nbsp;<font color=\"gray\">".$lang_functions['text_at']."</font>".gettime($row["added"]).
		($row["editedby"] && get_user_class() >= $commanage_class ? " - [<a href=\"comment.php?action=vieworiginal&amp;cid=".$row[id]."&amp;type=".$type."\">".$lang_functions['text_view_original']."</a>]" : "") . "</td><td class=\"embedded nowrap\" width=\"1%\"><a href=\"#top\"><img class=\"top\" src=\"pic/trans.gif\" alt=\"Top\" title=\"Top\" /></a>&nbsp;&nbsp;</td></tr></table></div>");
		$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars(trim($userRow["avatar"])) : "");
		if (!$avatar)
			$avatar = "pic/default_avatar.png";
		$text = format_comment($row["text"]);
		$text_editby = "";
		if ($row["editedby"]){
			$lastedittime = gettime($row['editdate'],true,false);
			$text_editby = "<br /><br /><br /><p><font class=\"small\">".$lang_functions['text_last_edited_by'].get_username($row['editedby']).$lang_functions['text_edited_at'].$lastedittime."</font></p>\n";
		}

		print("<table class=\"main\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">\n");
		$secs = 900;
		$dt = sqlesc(date("Y-m-d H:i:s",(TIMENOW - $secs))); // calculate date.
		print("<tr>\n");
		print("<td rowspan=\"2\" class=\"rowfollow\" width=\"150\" valign=\"top\" style=\"padding: 0px;\">".return_avatar_image($avatar)."
		
		
		

		
		
		<br />&nbsp;&nbsp;<img alt=\"".get_user_class_name($userRow["class"],false,false,true)."\" title=\"".get_user_class_name($userRow["class"],false,false,true)."\" src=\"".get_user_class_image($userRow["class"])."\" />
		
		<br />&nbsp;&nbsp;<B>U</B>:".mksize($userRow["uploaded"])."
		<br />&nbsp;&nbsp;<B>D</B>:".mksize($userRow["downloaded"])."
		<br />&nbsp;&nbsp;<B>R</B>:".get_ratio($userRow['id']).($showschool == 'yes'?"<br />&nbsp;&nbsp;<B>S</B>:".return_school_name($userRow['school']):"")."
		</td>\n");
		print("<td class=\"rowfollow\" valign=\"middle\" style='border-bottom: 0px;word-break:break-all;'><br />".$text.$text_editby."</td>\n");
		print("</tr>\n");
				
		$actionbar = 
		
		"<a href=\"#qrbody\"  onclick=\"javascript:quick_reply_to('[@{$userRow["id"]}]');\"><img class=\"f_reply\" src=\"pic/trans.gif\" alt=\"Add Reply\" title=\"".$lang_functions['title_add_reply']."\" /></a>".
		
		"<a href=\"comment.php?action=add&amp;sub=quote&amp;cid=".$row[id]."&amp;pid=".$parent_id."&amp;type=".$type."\"><img class=\"f_quote\" src=\"pic/trans.gif\" alt=\"Quote\" title=\"".$lang_functions['title_reply_with_quote']."\" /></a>".
		(get_user_class() >= $commanage_class ? "<a href=\"comment.php?action=delete&amp;cid=".$row[id]."&amp;type=".$type."\"><img class=\"f_delete\" src=\"pic/trans.gif\" alt=\"Delete\" title=\"".$lang_functions['title_delete']."\" /></a>" : "").($row["user"] == $CURUSER["id"] || get_user_class() >= $commanage_class ? "<a href=\"comment.php?action=edit&amp;cid=".$row[id]."&amp;type=".$type."\"><img class=\"f_edit\" src=\"pic/trans.gif\" alt=\"Edit\" title=\"".$lang_functions['title_edit']."\" />"."</a>" : "");
			
			
		$signature = ($CURUSER["signatures"] == "yes" ? $userRow["signature"] : "");
		if ($signature)
		$bodysignature= "<div style='vertical-align:bottom;'><br />------------------------<br />" . format_comment($signature,false,false,false,false,500,true,false, 2,200,0,false) . "</div>";
		else $bodysignature="";

		print("<tr><td valign=\"bottom\" style='border-top: 0px;padding: 0px;'>".$bodysignature."</td></tr>");
		
		print("<tr><td class=\"toolbox\" align=\"center\" valign=\"middle\"> ".("'".$userRow['last_access']."'"> $dt ? "<img class=\"f_online\" src=\"pic/trans.gif\" alt=\"Online\" title=\"".$lang_functions['title_online']."\" />":"<img class=\"f_offline\" src=\"pic/trans.gif\" alt=\"Offline\" title=\"".$lang_functions['title_offline']."\" />" )."<a href=\"sendmessage.php?receiver=".htmlspecialchars(trim($row["user"]))."\"><img class=\"f_pm\" src=\"pic/trans.gif\" alt=\"PM\" title=\"".$lang_functions['title_send_message_to'].htmlspecialchars($userRow["username"])."\" /></a><a href=\"report.php?commentid=".htmlspecialchars(trim($row["id"]))."\"><img class=\"f_report\" src=\"pic/trans.gif\" alt=\"Report\" title=\"".$lang_functions['title_report_this_comment']."\" /></a></td><td class=\"toolbox\" align=\"right\">".$actionbar."</td>");

		print("</tr></table>\n");
		$count++;
	}
	end_frame();
	end_main_frame();
}

function searchfield($s) {
	return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function genrelist($catmode = 1) {
	global $Cache;
	if (!$ret = $Cache->get_value('category_list_mode_'.$catmode)){
		$ret = array();
		$res = sql_query("SELECT id, mode, name, image FROM categories WHERE mode = ".sqlesc($catmode)." ORDER BY sort_index, id");
		while ($row = mysql_fetch_array($res))
			$ret[] = $row;
		$Cache->cache_value('category_list_mode_'.$catmode, $ret, 152800);
	}
	return $ret;
}

function searchbox_item_list($table = "sources", $lid = 0){
	global $Cache;
	if (!$ret = $Cache->get_value($table.'_list_'.$lid)){
		$ret = array();
		if($lid == 0)$res = sql_query("SELECT * FROM ".$table." ORDER BY sort_index, id");
		else $res = sql_query("SELECT * FROM ".$table." WHERE lid='".$lid."' ORDER BY sort_index, id");
		while ($row = mysql_fetch_array($res))
			$ret[] = $row;
		$Cache->cache_value($table.'_list_'.$lid, $ret, 152800);
	}
	return $ret;
}


function langlist($type) {
	global $Cache;
	if (!$ret = $Cache->get_value($type.'_lang_list')){
		$ret = array();
		$res = sql_query("SELECT id, lang_name, flagpic, site_lang_folder FROM language WHERE ". $type ."=1 ORDER BY site_lang DESC, id ASC");
		while ($row = mysql_fetch_array($res))
			$ret[] = $row;
		$Cache->cache_value($type.'_lang_list', $ret, 152800);
	}
	return $ret;
}

function linkcolor($num) {
	if (!$num)
	return "red";
	//    if ($num == 1)
	//        return "yellow";
	return "green";
}

function writecomment($userid, $comment) {
	$res = sql_query("SELECT modcomment FROM users WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_assoc($res);

	$modcomment = date("Y-m-d") . " - " . $comment . "" . ($arr[modcomment] != "" ? "\n" : "") . "$arr[modcomment]";
	$modcom = sqlesc($modcomment);

	return sql_query("UPDATE users SET modcomment = $modcom WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
}

function return_torrent_bookmark_array($userid){
	global $Cache;
	static $ret;
	if (!$ret&&!$ret = $Cache->get_value('user_'.$userid.'_bookmark_array')){
			$ret = array(0);
			$res = sql_query("SELECT * FROM bookmarks WHERE userid=" . sqlesc($userid));
			while ($row = mysql_fetch_array($res))$ret[] = $row['torrentid'];	
			$Cache->cache_value('user_'.$userid.'_bookmark_array', $ret, 132800);
		}
	return $ret;
}
function get_torrent_bookmark_state($userid, $torrentid, $text = false)
{
	global $lang_functions;
	$userid = 0 + $userid;
	$torrentid = 0 + $torrentid;
	$ret = array();
	$ret = return_torrent_bookmark_array($userid);
	if (!count($ret) || !in_array($torrentid, $ret, false)) // already bookmarked
		$act = ($text == true ?  "<img class=\"delbookmark\" src=\"pic/trans.gif\" alt=\"Unbookmarked\" title=\"".$lang_functions['title_bookmark_torrent']."\" /><b><font class=\"small\">".$lang_functions['title_bookmark_torrent'] ."</font></b>" : "<img class=\"delbookmark\" src=\"pic/trans.gif\" alt=\"Unbookmarked\" title=\"".$lang_functions['title_bookmark_torrent']."\" />");
	else
		$act = ($text == true ? "<img class=\"bookmark\" src=\"pic/trans.gif\" alt=\"Bookmarked\" title=\"".$lang_functions['title_delbookmark_torrent']."\" /><b><font class=\"small\">".$lang_functions['title_delbookmark_torrent']."</font></b>" : "<img class=\"bookmark\" src=\"pic/trans.gif\" alt=\"Bookmarked\" title=\"".$lang_functions['title_delbookmark_torrent']."\" />");
	return $act;
}

function return_topic_bookmark_array($userid)
{
	global $Cache;
	static $ret;
	if (!$ret){
		if (!$ret = $Cache->get_value('user_'.$userid.'_bookmark_array_topic')){
			$ret = array();
			$res = sql_query("SELECT topicid FROM bookmarks_topic WHERE userid=" . sqlesc($userid));
			if (mysql_num_rows($res) != 0){
				while ($row = mysql_fetch_array($res))
					$ret[] = $row['topicid'];
				$Cache->cache_value('user_'.$userid.'_bookmark_array_topic', $ret, 132800);
			} else {
				$Cache->cache_value('user_'.$userid.'_bookmark_array_topic', array(0), 132800);
			}
		}
	}
	return $ret;
}
function get_topic_bookmark_state($userid, $topicid,$counter=0)
{
	global $lang_functions;
	$userid = 0 + $userid;
	$topicid = 0 + $topicid;
	$ret = array();
	$ret = return_topic_bookmark_array($userid);
	if (!count($ret) || !in_array($topicid, $ret, false)) // already bookmarked
		$act = ("<img class=\"delbookmark\" src=\"pic/trans.gif\" alt=\"Unbookmarked\" title=\"".$lang_functions['title_bookmark_torrent']."\" />");
	else
		$act = ("<img class=\"bookmark\" src=\"pic/trans.gif\" alt=\"Bookmarked\" title=\"".$lang_functions['title_delbookmark_torrent']."\" />");
	return "<a id=\"bookmark".$counter."\" href=\"javascript: bookmark_topic(".$topicid.",".$counter.");\" >".$act."</a>";
	
	
}


function get_torrent_bookmark_state_in_details($userid, $torrentid, $text)
{
	global $lang_functions;
	$userid = 0 + $userid;
	$torrentid = 0 + $torrentid;
	$ret = array();
	$ret = return_torrent_bookmark_array($userid);
	if (!count($ret) || !in_array($torrentid, $ret, false)) // already bookmarked
		$act =($text == 'bookmarked' ? " style=\"display: none;\"" : "");
		else
		$act =($text == 'bookmarked' ? "" : " style=\"display: none;\"");
			return $act;
}





function torrenttable($res, $variant = "torrent",$resseedleech='',$torrentmanagephp=false) {
	global $Cache;
	global $lang_functions;
	global $CURUSER, $waitsystem;
	global $showextinfo;
	global $torrentmanage_class, $smalldescription_main, $enabletooltip_tweak;
	global $CURLANGDIR;


	if ($variant == "torrent"){
		$last_browse = $CURUSER['last_browse'];
		$sectiontype = $browsecatmode;
	}
	elseif($variant == "music"){
		$last_browse = $CURUSER['last_music'];
		$sectiontype = $specialcatmode;
	}
	else{
		$last_browse = $CURUSER['last_browse'];
		$sectiontype = "";
	}

	$time_now = TIMENOW;
	if ($last_browse > $time_now) {
		$last_browse=$time_now;
	}

$last_browse=0;	
if (!$last_browse = $Cache->get_value('last_browse_'.$variant.'_'.$CURUSER['id']))
{	


if ($variant == "torrent"){
		$last_browse = $CURUSER['last_browse'];
		$sectiontype = $browsecatmode;
	}
	elseif($variant == "music"){
		$last_browse = $CURUSER['last_music'];
		$sectiontype = $specialcatmode;
	}
	else{
		$last_browse = $CURUSER['last_browse'];
		$sectiontype = "";
	}

	$time_now = TIMENOW;
	if ($last_browse > $time_now) {
		$last_browse=$time_now;
	}
$Cache->cache_value('last_browse_'.$variant.'_'.$CURUSER['id'], $last_browse, 300);
}
//print $Cache->get_value('last_browse_'.$variant.'_'.$CURUSER['id']);
	if (get_user_class() < UC_VIP && $waitsystem == "yes") {
		$ratio = get_ratio($CURUSER["id"], false);
		$gigs = $CURUSER["uploaded"] / (1024*1024*1024);
		if($gigs > 10)
		{
			if ($ratio < 0.4) $wait = 24;
			elseif ($ratio < 0.5) $wait = 12;
			elseif ($ratio < 0.6) $wait = 6;
			elseif ($ratio < 0.8) $wait = 3;
			else $wait = 0;
		}
		else $wait = 0;
	}

if (get_user_class() >= $torrentmanage_class) { ?>
<form action="torrentsmanagement.php" method="post">
<?php } ?>
<table class="torrents" cellspacing="0" cellpadding="5" width="100%">

<tr>
<?php
$count_get = 0;
$oldlink = "";
foreach ($_GET as $get_name => $get_value) {
	$get_name = mysql_real_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_name)));
	$get_value = mysql_real_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_value)));
	if ($get_name != "sort" && $get_name != "type"&&$get_name != "page") {
		if ($count_get > 0) {
			$oldlink .= "&amp;" . $get_name . "=" . $get_value;
		}
		else {
			$oldlink .= $get_name . "=" . $get_value;
		}
		$count_get++;
	}
}
if ($count_get > 0) {
	$oldlink = $oldlink . "&amp;";
}
$sort = $_GET['sort'];
$link = array();
for ($i=1; $i<=11; $i++){
	if ($sort == $i)
		$link[$i] = ($_GET['type'] == "desc" ? "asc" : "desc");
	else $link[$i] = ($i == 1 ? "asc" : "desc");
}
?>
<td class="colhead" style="padding: 0px" rowspan ="2"><?php echo $lang_functions['col_type'] ?></td>
<td class="colhead" rowspan ="2"><a href="?<?php echo $oldlink?>sort=1&amp;type=<?php echo $link[1]?>"><?php echo $lang_functions['col_name'] ?></a><a href="?<?php echo $oldlink?>sort=11&amp;type=<?php echo $link[11]?>">[评分]</a></td>
<?php

if ($wait)
{
	print("<td class=\"colhead\" rowspan =\"2\">".$lang_functions['col_wait']."</td>\n");
}
 ?>

<td class="colhead" rowspan ="2"><a href="?<?php echo $oldlink?>sort=7&amp;type=<?php echo $link[7]?>"><img class="seeders" src="pic/trans.gif" alt="seeders" title="<?php echo $lang_functions['title_number_of_seeders'] ?>" /></a></td>
<td class="colhead" rowspan ="2"><a href="?<?php echo $oldlink?>sort=8&amp;type=<?php echo $link[8]?>"><img class="leechers" src="pic/trans.gif" alt="leechers" title="<?php echo $lang_functions['title_number_of_leechers'] ?>" /></a></td>
<td class="colhead" rowspan ="2"><a href="?<?php echo $oldlink?>sort=6&amp;type=<?php echo $link[6]?>"><img class="snatched" src="pic/trans.gif" alt="snatched" title="<?php echo $lang_functions['title_number_of_snatched']?>" /></a></td>
<td class="colhead" rowspan ="2"><a href="?<?php echo $oldlink?>sort=10&amp;type=<?php echo $link[10]?>"><img class="unsnatched" src="pic/trans.gif" alt="unsnatched" title="未<?php echo $lang_functions['title_number_of_snatched']?>" /></a></td>
<?php


if ($CURUSER['showcomnum'] != 'no') { 
 $COLSPANowner=3;} 
ELSE $COLSPANowner=2;
?>





<td class="colhead" COLSPAN ="<?php ECHO $COLSPANowner?>"><a href="?<?php echo $oldlink?>sort=9&amp;type=<?php echo $link[9]?>"><?php echo $lang_functions['col_uploader']?></a></td>
<?php
if (get_user_class() >= $torrentmanage_class) { ?>
	<td class="colhead" rowspan ="2"><?php echo $lang_functions['col_action'] ?></td>
<?php } ?>
</tr>
<tr>

<?php
if ($CURUSER['showcomnum'] != 'no') { ?>
<td class="colhead" ><a href="?<?php echo $oldlink?>sort=3&amp;type=<?php echo $link[3]?>"><img class="comments" src="pic/trans.gif" alt="comments" title="<?php echo $lang_functions['title_number_of_comments'] ?>" /></a></td>
<?php } ?>


<td class="colhead"><a href="?<?php echo $oldlink?>sort=4&amp;type=<?php echo $link[4]?>"><img class="time" src="pic/trans.gif" alt="time" title="<?php echo ($CURUSER['timetype'] != 'timealive' ? $lang_functions['title_time_added'] : $lang_functions['title_time_alive'])?>" /></a></td>
<td class="colhead"><a href="?<?php echo $oldlink?>sort=5&amp;type=<?php echo $link[5]?>"><img class="size" src="pic/trans.gif" alt="size" title="<?php echo $lang_functions['title_size'] ?>" /></a></td>

</tr>
<?php
$caticonrow = get_category_icon_row($CURUSER['caticon']);
if ($caticonrow['secondicon'] == 'yes')
$has_secondicon = true;
else $has_secondicon = false;
$counter = 0;
if ($smalldescription_main == 'no' || $CURUSER['showsmalldescr'] == 'no')
	$displaysmalldescr = false;
else $displaysmalldescr = true;
$get_second_name=get_second_name();
//while ($row = mysql_fetch_array($res))
foreach ($res as $row)
{
	$id = $row["id"];
	if($row['nobuymoney']=='no')$sphighlight= " class='halfdown_bg'";
	elseif($row['pos_state'] == 'sticky')$sphighlight= " class='twouphalfdown_bg'";
	else
	$sphighlight=get_torrent_bg_color($row['sp_state'],$row["audiocodec"]);
	
	
	print("<tr" . $sphighlight . ">\n");

	print("<td class=\"rowfollow nowrap\"  rowspan =\"2\" valign=\"middle\" style='padding: 0px'>");
	if (isset($row["category"])) {
		print(return_category_image($row["category"], "?"));
		if ($has_secondicon){
			print(get_second_icon($row, "pic/".$catimgurl."additional/"));
		}
	}
	else
		print("-");
	print("</td>\n");


$row["name"]=$get_second_name['source'][$row["source"]].$row["name"];		
$row["name"] = $get_second_name['audiocodec'][$row["audiocodec"]].$row["name"];
$row["small_descr"]=$get_second_name['medium'][$row["medium"]].$row["small_descr"];
$row["small_descr"]=$get_second_name['standard'][$row["standard"]].$row["small_descr"];
$row["small_descr"]=$get_second_name['team'][$row["team"]].$row["small_descr"];
$row["small_descr"]=$get_second_name['processing'][$row["processing"]].$row["small_descr"];	
	
	
	//torrent name
	$dispname = trim($row["name"]);
	$short_torrent_name_alt = "";
	$mouseovertorrent = "";
	$tooltipblock = "";
	$has_tooltip = false;
	if ($enabletooltip_tweak == 'yes')
		$tooltiptype = $CURUSER['tooltip'];
	else
		$tooltiptype = 'off';
		
	
		
	switch ($tooltiptype){
		case 'minorimdb' : {
			if ($showextinfo['imdb'] == 'yes' && $row["url"])
				{
				$url = $row['url'];
				$cache = $row['cache_stamp'];
				$type = 'minor';
				$has_tooltip = true;
				}
			break;
			}
		case 'medianimdb' :
			{
			if ($showextinfo['imdb'] == 'yes' && $row["url"])
				{
				$url = $row['url'];
				$cache = $row['cache_stamp'];
				$type = 'median';
				$has_tooltip = true;
				}
			break;
			}
		case 'off' :  break;
	}
	if (!$has_tooltip)
		$short_torrent_name_alt = "title=\"".htmlspecialchars($dispname)."\"";
	else{
	$torrent_tooltip[$counter]['id'] = "torrent_" . $counter;
	$torrent_tooltip[$counter]['content'] = "";
	$mouseovertorrent = "onmouseover=\"get_ext_info_ajax('".$torrent_tooltip[$counter]['id']."','".$url."','".$cache."','".$type."'); domTT_activate(this, event, 'content', document.getElementById('" . $torrent_tooltip[$counter]['id'] . "'), 'trail', false, 'delay',600,'lifetime',6000,'fade','both','styleClass','niceTitle', 'fadeMax',87, 'maxWidth', 500);\"";
	}
	/*$count_dispname=mb_strlen($dispname,"gb2312");
	if (!$displaysmalldescr || $row["small_descr"] == "")// maximum length of torrent name
		$max_length_of_torrent_name = 140;
	elseif ($CURUSER['fontsize'] == 'large')
		$max_length_of_torrent_name = 120;
	elseif ($CURUSER['fontsize'] == 'small')
		$max_length_of_torrent_name = 160;
	else $max_length_of_torrent_name = 140;

	if($count_dispname > $max_length_of_torrent_name)
		$dispname=mb_strcut($dispname, 0, ($max_length_of_torrent_name*3/2),"UTF-8") . "..";
	*/
	if ($row['pos_state'] == 'sticky' && $CURUSER['appendsticky'] == 'yes')
		$stickyicon = "<img class=\"sticky\" src=\"pic/trans.gif\" alt=\"Sticky\" title=\"".$lang_functions['title_sticky']."\" />&nbsp;";
	else
	$stickyicon = "";
	
	//print("<td  rowspan =\"2\" class=\"rowfollow\" width=\"100%\" align=\"left\" style='padding-top:0;padding-bottom: 0px;'><table " . $sphighlight . " width=\"100%\"><tr" . $sphighlight . "><td class=\"embedded\" style='padding-top:0;padding-bottom: 0px;'>".$stickyicon."<a $short_torrent_name_alt $mouseovertorrent href=\"details.php?id=".$id."&amp;hit=1\"><b>".htmlspecialchars($dispname)."</b></a>");
	
	$sp_torrent = get_torrent_promotion_append($row['sp_state'],$row['audiocodec'],"",true,$row["added"], $row['promotion_time_type'], $row['promotion_until']);
	$subcount_torrent =($row['subcount']?"<b>[<font class='striking'>字幕</font>]</b>":"");
	$picked_torrent = "";
	$imdbpicked_torrent="";
	if ($CURUSER['appendpicked'] != 'no'){
	if($row['url']&&$showextinfo['imdb'] == 'yes'){
	
	$thenumbers = parse_imdb_id($row["url"]);
	
	
/*	
	if (!$imdbpicked_torrent = $Cache->get_value('ranting_'.$thenumbers)){	
	
	$rating=number_format(get_single_value('imdbinfo', 'rating','where imdb = '.sqlesc($thenumbers)),1,".","");

	
	IF($row["urltype"]==2)
	$imdbpicked_torrent = " <b>[<font class='recommended'>豆瓣".((INT)($rating)?":".$rating:"")."</font>]</b>";
	ELSE
	$imdbpicked_torrent = " <b>[<font class='recommended'>IMDB".((INT)($rating)?":".$rating:"")."</font>]</b>";
	
	$Cache->cache_value('ranting_'.$thenumbers, $imdbpicked_torrent, 3600*24);
}
*/

	
	$rating=number_format($row["rating"],1,".","");
	$imdbpicked_torrent = " <b>[<font class='recommended'>".($row["urltype"]==2?"豆瓣":"IMDB").((INT)($rating)?":".$rating:"")."</font>]</b>";


	
	}
	if($row['picktype']=="hot")
	$picked_torrent .= " <b>[<font class='hot'>".$lang_functions['text_hot']."</font>]</b>";
	elseif($row['picktype']=="classic")
	$picked_torrent .= " <b>[<font class='classic'>".$lang_functions['text_classic']."</font>]</b>";
	elseif($row['picktype']=="recommended")
	$picked_torrent .= " <b>[<font class='recommended'>".$lang_functions['text_recommended']."</font>]</b>";
	///////CZY

	
	}
	
	print("<td  rowspan =\"2\" class=\"rowfollow\" width=\"100%\" align=\"left\" style='padding-top:0;padding-bottom: 0px;'><table " . $sphighlight . " width=\"100%\"><tr" . $sphighlight . "><td class=\"embedded\" style='padding-top:0;padding-bottom: 0px;'>".$stickyicon);
	
	if ($CURUSER['appendnew'] != 'no' && strtotime($row["added"]) >= $last_browse)
	print("<b> [<font class='new'>".$lang_functions['text_new_uppercase']."</font>]</b>");
	
	

	

		


	$banned_torrent = ($row["banned"] == 'yes' ? " <b>(<font class=\"striking\">".$lang_functions['text_banned']."</font>)</b>" : "");
	
	//print("<a $short_torrent_name_alt $mouseovertorrent href=\"details.php?id=".$id."&amp;hit=1\">".$picked_torrent."<b>".htmlspecialchars($dispname)."</b>".$banned_torrent."</a>".$sp_torrent.$sp_torrent_sub);
	
	
	
	if ($displaysmalldescr){
		//small descr
		$dissmall_descr = trim($row["small_descr"]);
		/*$count_dissmall_descr=mb_strlen($dissmall_descr,"gb2312");
		$max_lenght_of_small_descr=$max_length_of_torrent_name; // maximum length
		if($count_dissmall_descr > $max_lenght_of_small_descr)
		{
			$dissmall_descr=mb_strcut($dissmall_descr, 0, $max_lenght_of_small_descr+20,"UTF-8") . "..";
		}*/
		print($dissmall_descr == "" ? "<a $short_torrent_name_alt $mouseovertorrent href=\"details.php?id=".$id."&amp;hit=1\">".$picked_torrent."<b>".htmlspecialchars($dispname)."</b>".$banned_torrent."</a>".$sp_torrent.
		($imdbpicked_torrent||$subcount_torrent?"<br />".$imdbpicked_torrent.$subcount_torrent:"")
 : "<a $short_torrent_name_alt $mouseovertorrent href=\"details.php?id=".$id."&amp;hit=1\">".$picked_torrent."<b>".htmlspecialchars($dispname)."</b>".$banned_torrent."</a>".$sp_torrent
."<br />".$imdbpicked_torrent.$subcount_torrent.htmlspecialchars($dissmall_descr));
		
	}

	
	print("</td>");

		$act = "";
		if($row['added']!=$row['last_action']||$CURUSER["id"] == $row["owner"]){
		if ($CURUSER["dlicon"] == 'no' || $CURUSER["downloadpos"] == "no"||$row['visible'] == 'no')
		$act .="";
		else
		$act .= "<a href=\"download.php?id=".$id."\"><img class=\"download\" src=\"pic/trans.gif\" style='padding-bottom: 2px;' alt=\"download\" title=\"".$lang_functions['title_download_torrent']."\" /></a>" ;
		if ($CURUSER["bmicon"] == 'yes'){
			$bookmark = " href=\"javascript: bookmark(".$id.",".$counter.");\"";
			//$act .= "<a id=\"bookmark".$counter."\" ".$bookmark." >".get_torrent_bookmark_state($CURUSER['id'], $id)."</a>";
			$act .= ($act ? "<br />" : "")."<a id=\"bookmark".$counter."\" ".$bookmark." >".get_torrent_bookmark_state($CURUSER['id'], $id)."</a>";

			}}
	//print("<td width=\"60\" class=\"embedded\" style=\"text-align: center; \" valign=\"middle\">".$banned_torrent.$picked_torrent.$sp_torrent.$act."</td>\n");

	print("<td  rowspan =\"2\" width=\"20\" class=\"embedded\" style=\"text-align: right; \" valign=\"middle\">".$act."</td>\n");

	print("</tr></table></td>");
	if ($wait)
	{
		$elapsed = floor((TIMENOW - strtotime($row["added"])) / 3600);
		if ($elapsed < $wait)
		{
			$color = dechex(floor(127*($wait - $elapsed)/48 + 128)*65536);
			print("<td rowspan =\"2\" class=\"rowfollow nowrap\"><a href=\"faq.php#id46\"><font color=\"".$color."\">" . number_format($wait - $elapsed) . $lang_functions['text_h']."</font></a></td>\n");
		}
		else
		print("<td  rowspan =\"2\" class=\"rowfollow nowrap\">".$lang_functions['text_none']."</td>\n");
	}
	


	$time = $row["added"] ;
	$time = gettime($time,false,false);

			$unsnatchedstyle=$leechingstyle=$seedingstyle=$snatchedstyle="";
			switch ($resseedleech[$id]){
				case 'seeder' 		: $seedingstyle=" style='background-color: Green;' title='做种中'";break;
				case 'leecher' 		: $leechingstyle=" style='background-color: Red;' title='下载中'";break;
				case 'snatched' 	: $snatchedstyle=" style='background-color: Green;' title='已完成'";break;
				case 'unsnatched' 	: $unsnatchedstyle=" style='background-color: Red;' title='未完成'";break;
			}

	if ($row["seeders"]) {
			$ratio = ($row["leechers"] ? ($row["seeders"] / $row["leechers"]) : 1);
			$ratiocolor = get_slr_color($ratio);
			print("<td   rowspan =\"2\"  class=\"rowfollow\" align=\"center\" $seedingstyle><b><a href=\"details.php?id=".$id."&amp;hit=1&amp;dllist=1#seeders\">".($ratiocolor ? "<font color=\"" .
			$ratiocolor . "\">" . number_format($row["seeders"]) . "</font>" : number_format($row["seeders"]))."</a></b></td>\n");
	}
	else
		print("<td rowspan =\"2\"  class=\"rowfollow\"><span class=\"" . linkcolor($row["seeders"]) . "\">" . number_format($row["seeders"]) . "</span></td>\n");

			if ($row["leechers"]) {
		print("<td rowspan =\"2\"  class=\"rowfollow\" $leechingstyle ><b><a href=\"details.php?id=".$id."&amp;hit=1&amp;dllist=1#leechers\">" .
		number_format($row["leechers"]) . "</a></b></td>\n");
	}
	else
		print("<td  rowspan =\"2\"  class=\"rowfollow\">0</td>\n");
		

	if ($row["times_completed"] >=1)
	print("<td class=\"rowfollow\" rowspan =\"2\" $snatchedstyle><a href=\"viewsnatches.php?id=".$row['id']."\"><b>" . number_format($row["times_completed"]) . "</b></a></td>\n");
	else
	print("<td class=\"rowfollow\" rowspan =\"2\">" . number_format($row["times_completed"]) . "</td>\n");
	
	if ($row["times_uncompleted"] >=1)
	print("<td class=\"rowfollow\" rowspan =\"2\" $unsnatchedstyle><a href=\"viewsnatches.php?all=1&id=".$row['id']."\"><b>" . number_format($row["times_uncompleted"]) . "</b></a></td>\n");
	else
	print("<td class=\"rowfollow\" rowspan =\"2\">" . number_format($row["times_uncompleted"]) . "</td>\n");
	
		
	


		if ($row["anonymous"] == "yes" && get_user_class() >= $torrentmanage_class)
		{
						//print("<td  COLSPAN =\"2\" class=\"rowfollow\" align=\"center\"><i>".$lang_functions['text_anonymous']."</i><br />".(isset($row["owner"]) ? "(" . get_username($row["owner"]) .")" : "<i>".$lang_functions['text_orphaned']."</i>") . "</td>\n");

			print("<td  COLSPAN =\"".$COLSPANowner."\" class=\"rowfollow\" align=\"center\" >".(isset($row["owner"]) ? "(" . get_username($row["owner"]) .")" : "<i>".$lang_functions['text_orphaned']."</i>") . "</td>\n");
		}
		elseif ($row["anonymous"] == "yes")
		{
			print("<td  COLSPAN =\"".$COLSPANowner."\" class=\"rowfollow\"><i>".$lang_functions['text_anonymous']."</i></td>\n");
		}
		else
		{
			print("<td  COLSPAN =\"".$COLSPANowner."\" class=\"rowfollow\">" . (isset($row["owner"]) ? get_username($row["owner"]) : "<i>".$lang_functions['text_orphaned']."</i>") . "</td>\n");
		}

	if (get_user_class() >= $torrentmanage_class)
	{
		//print("<td class=\"rowfollow\" rowspan =\"2\"><a href=\"".htmlspecialchars("fastdelete.php?id=".$row['id'])."\"><img class=\"staff_delete\" src=\"pic/trans.gif\" alt=\"D\" title=\"".$lang_functions['text_delete']."\" /></a>");
		//print("<br /><a href=\"edit.php?returnto=" . rawurlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><img class=\"staff_edit\" src=\"pic/trans.gif\" alt=\"E\" title=\"".$lang_functions['text_edit']."\" /></a></td>\n");
			print("<td class=\"rowfollow\" rowspan =\"2\"><input class=checkbox type=\"checkbox\" name=\"torrentsmanagementid[]\" value=\"" . $id . "\"><br /><a href=\"edit.php?returnto=" . rawurlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><img class=\"staff_edit\" src=\"pic/trans.gif\" alt=\"E\" title=\"".$lang_functions['text_edit']."\" /></a></td>\n");
	}
	print("</tr>\n");
	
	print("<tr" . $sphighlight . ">\n");
	

		
	if ($CURUSER['showcomnum'] != 'no')
	{
	print("<td   class=\"rowfollow\" >");
	$nl = "";

	//comments

	$nl = "<br />";
	if (!$row["comments"]) {
		print("<a href=\"comment.php?action=add&amp;pid=".$id."&amp;type=torrent\" title=\"".$lang_functions['title_add_comments']."\">" . $row["comments"] .  "</a>");
	} else {
		if ($enabletooltip_tweak == 'yes' && $CURUSER['showlastcom'] != 'no')
		{
			if (!$lastcom = $Cache->get_value('torrent_'.$id.'_last_comment_content')){
				$res2 = sql_query("SELECT user, added, text FROM comments WHERE torrent = $id ORDER BY id DESC LIMIT 1");
				$lastcom = mysql_fetch_array($res2);
				$Cache->cache_value('torrent_'.$id.'_last_comment_content', $lastcom, 1855);
			}
			$timestamp = strtotime($lastcom["added"]);
			$hasnewcom = ($lastcom['user'] != $CURUSER['id'] && $timestamp >= $last_browse);
			if ($lastcom)
			{
				if ($CURUSER['timetype'] != 'timealive')
					$lastcomtime = $lang_functions['text_at_time'].$lastcom['added'];
				else
					$lastcomtime = $lang_functions['text_blank'].gettime($lastcom["added"],true,false,true);
					$lastcom_tooltip[$counter]['id'] = "lastcom_" . $counter;
					$lastcom_tooltip[$counter]['content'] = ($hasnewcom ? "<b>(<font class='new'>".$lang_functions['text_new_uppercase']."</font>)</b> " : "").$lang_functions['text_last_commented_by'].get_username($lastcom['user']) . $lastcomtime."<br />". format_comment(mb_substr($lastcom['text'],0,100,"UTF-8") . (mb_strlen($lastcom['text'],"UTF-8") > 100 ? " ......" : "" ),true,false,false,true,600,false,false);
					$onmouseover = "onmouseover=\"domTT_activate(this, event, 'content', document.getElementById('" . $lastcom_tooltip[$counter]['id'] . "'), 'trail', false, 'delay', 500,'lifetime',3000,'fade','both','styleClass','niceTitle','fadeMax', 87,'maxWidth', 400);\"";
			}
		} else {
			$hasnewcom = false;
			$onmouseover = "";
		}
		print("<b><a href=\"details.php?id=".$id."&amp;hit=1&amp;cmtpage=1#startcomments\" ".$onmouseover.">". ($hasnewcom ? "<font class='new'>" : ""). $row["comments"] .($hasnewcom ? "</font>" : ""). "</a></b>");
	}

	print("</td>");
	}
	
	
	
		print("<td class=\"rowfollow nowrap\">". $time. "</td>");

	//size
	print("<td class=\"rowfollow\">" . mksize($row["size"])."</td>");
	PRINT("</tr>");
	$counter++;
}
if (get_user_class() >= $torrentmanage_class) { ?>
	<tr><td class='colhead'  style='padding: 0px'><input type="submit" name="deletetorrent" onclick='return confirm("\n确认删除种子吗?");' value="删除"></td>
	<td class='colhead' style='padding: 0px' align='right' colspan='6'>
	
	<?if($torrentmanagephp){?><select name="sel_banstate"><option selected="selected" value="0">普通</option><option value="1">禁止</option></select>
	<input type="submit" name="bannedtorrent" value="禁止">
	
	<select name="sel_posstate"><option selected="selected" value="0">普通</option><option value="1">置顶</option></select><input type="submit" name="posstatetorrent" value="位置">
	
	<select name="sel_recmovie"><option selected="selected" value="0">普通</option><option value="1">热门</option><option value="2">经典</option><option value="3">推荐</option></select>
	<input type="submit" name="picktorrent" value="挑选">
	
<select name="sel_spstate" ><option value="1">普通</option><option value="2">免费</option><option value="3">2X</option><option value="4">2X免费</option><option value="5">50%</option><option value="6">2X 50%</option><option value="7">30%</option></select>
<select name="promotion_time_type" ><option selected="selected" value="0">全局</option><option value="1">永久</option><option value="2">直到</option></select>
<input type="text" style="width: 70px" name="promotionuntil" value="<?echo date("Y-m-d",TIMENOW)?>" />
<input type="submit" name="promotiontorrent" value="促销"><input type="submit" name="torrentlowquality" value="不规范">
<?}?>
	</td>
	<td class='colhead' style='padding: 0px' align='right' colspan='3'>
	<input type="button" value="全选" onClick="this.value=check(form,'全选','全不')">
	<input type="button" value="反选" onClick="checktocheck(form)"></td></tr></table></form>
<?php }else
print("</table>");
//if ($CURUSER['appendpromotion'] == 'highlight')
//	print("<p align=\"center\"> ".$lang_functions['text_promoted_torrents_note']."</p>\n");

if($enabletooltip_tweak == 'yes' && (!isset($CURUSER) || $CURUSER['showlastcom'] == 'yes'))
create_tooltip_container($lastcom_tooltip, 400);
create_tooltip_container($torrent_tooltip, 500);
}
function torrenttable2($res) {
	global $Cache;
	global $lang_functions;
	global $CURUSER, $waitsystem;
	global $showextinfo;
	global $torrentmanage_class, $smalldescription_main, $enabletooltip_tweak;
	global $CURLANGDIR;





?>
<table class="torrents" cellspacing="0" cellpadding="5" width="100%">
<tr>
<?php
$count_get = 0;

foreach ($_GET as $get_name => $get_value) {
	$get_name = mysql_real_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_name)));
	$get_value = mysql_real_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_value)));


}



?>
<td class="colhead" style="padding: 0px" rowspan ="2"><?php echo $lang_functions['col_type'] ?></td>
<td class="colhead" rowspan ="2"><?php echo $lang_functions['col_name'] ?></td>


<td class="colhead" ><img class="seeders" src="pic/trans.gif" alt="seeders" title="<?php echo $lang_functions['title_number_of_seeders'] ?>" /></td>
<td class="colhead" rowspan ="2"><img class="snatched" src="pic/trans.gif" alt="snatched" title="<?php echo $lang_functions['title_number_of_snatched']?>" /></td>






<td class="colhead" COLSPAN ="2">竞价者</td>

	<td class="colhead" >出价</td>

</tr>
<tr>
<td class="colhead" ><img class="leechers" src="pic/trans.gif" alt="leechers" title="<?php echo $lang_functions['title_number_of_leechers'] ?>" /></td>




<td class="colhead"><img class="time" src="pic/trans.gif" alt="time" title="<?php echo ($CURUSER['timetype'] != 'timealive' ? $lang_functions['title_time_added'] : $lang_functions['title_time_alive'])?>" /></td>
<td class="colhead"><img class="size" src="pic/trans.gif" alt="size" title="<?php echo $lang_functions['title_size'] ?>" /></td>
<td class="colhead" >期限</td>
</tr>
<?php

$counter = 0;
if ($smalldescription_main == 'no' || $CURUSER['showsmalldescr'] == 'no')
	$displaysmalldescr = false;
else $displaysmalldescr = true;
while ($row = mysql_fetch_assoc($res))
{
	$id = $row["id"];
	$sphighlight =($row["seeders"]>0?" class='halfdown_bg'":"");
	print("<tr" . $sphighlight . ">\n");

	print("<td class=\"rowfollow nowrap\"  rowspan =\"2\" valign=\"middle\" style='padding: 0px'>");
	if (isset($row["category"])) {
		print(return_category_image($row["category"]));

	}
	else
		print("-");
	print("</td>\n");

	//torrent name
	$dispname = trim($row["name"]);



		
	
		


	$count_dispname=mb_strlen($dispname,"gb2312");
	if (!$displaysmalldescr || $row["small_descr"] == "")// maximum length of torrent name
		$max_length_of_torrent_name = 120;
	elseif ($CURUSER['fontsize'] == 'large')
		$max_length_of_torrent_name = 70;
	elseif ($CURUSER['fontsize'] == 'small')
		$max_length_of_torrent_name = 90;
	else $max_length_of_torrent_name = 80;

	if($count_dispname > $max_length_of_torrent_name)
		$dispname=mb_strcut($dispname, 0, ($max_length_of_torrent_name*3/2),"UTF-8") . "..";


	
	print("<td  rowspan =\"2\" class=\"rowfollow\" width=\"100%\" align=\"left\" style='padding-top:0;padding-bottom: 0px;'><table " . $sphighlight . " width=\"100%\"><tr" . $sphighlight . "><td class=\"embedded\"><a  href=\"details.php?id=".$id."&amp;hit=1\"><b>".htmlspecialchars($dispname)."</b></a>");
	







	if ($displaysmalldescr){
		//small descr
		$dissmall_descr = trim($row["small_descr"]);
		$count_dissmall_descr=mb_strlen($dissmall_descr,"gb2312");
		$max_lenght_of_small_descr=$max_length_of_torrent_name; // maximum length
		if($count_dissmall_descr > $max_lenght_of_small_descr)
		{
			$dissmall_descr=mb_strcut($dissmall_descr, 0, $max_lenght_of_small_descr+20,"UTF-8") . "..";
		}
		print($dissmall_descr == "" ? "" : "<br />".htmlspecialchars($dissmall_descr));
		
	}

	
	print("</td>");




	

	print("</tr></table></td>");

	


	$time = $row["added"];
	$time = gettime($time,false,false);


	if ($row["seeders"]) {
			$ratio = ($row["leechers"] ? ($row["seeders"] / $row["leechers"]) : 1);
			$ratiocolor = get_slr_color($ratio);
			print("<td    class=\"rowfollow\" align=\"center\"><b><a href=\"details.php?id=".$id."&amp;hit=1&amp;dllist=1#seeders\">".($ratiocolor ? "<font color=\"" .
			$ratiocolor . "\">" . number_format($row["seeders"]) . "</font>" : number_format($row["seeders"]))."</a></b></td>\n");
	}
	else
		print("<td class=\"rowfollow\"><span class=\"" . linkcolor($row["seeders"]) . "\">" . number_format($row["seeders"]) . "</span></td>\n");



	if ($row["times_completed"] >=1)
	print("<td class=\"rowfollow\" rowspan =\"2\"><a href=\"viewsnatches.php?id=".$row[id]."\"><b>" . number_format($row["times_completed"]) . "</b></a></td>\n");
	else
	print("<td class=\"rowfollow\" rowspan =\"2\">" . number_format($row["times_completed"]) . "</td>\n");
	
		
	


			print("<td  COLSPAN =\"2\" class=\"rowfollow\" align=\"center\" >".(isset($row["owner"]) ?  get_username($row["owner"])  : "<i>".$lang_functions['text_orphaned']."</i>") .($row["times"]>0 ?  "(".$row["times"].")"  :""). "</td>\n");
		


	
		print("<td class=\"rowfollow\" >".$row["money"]."</td>\n");
	
	print("</tr>\n");
	
	print("<tr" . $sphighlight . ">\n");
	
		if ($row["leechers"]) {
		print("<td class=\"rowfollow\" ><b><a href=\"details.php?id=".$id."&amp;hit=1&amp;dllist=1#leechers\">" .
		number_format($row["leechers"]) . "</a></b></td>\n");
	}
	else
		print("<td  class=\"rowfollow\">0</td>\n");
		
		
	
	
	
		print("<td class=\"rowfollow nowrap\">". $time. "</td>");


	print("<td class=\"rowfollow\">" . mksize($row["size"])."</td>");
	$timeout=gettime(date("Y-m-d H:i:s", strtotime($row["until"])), false, false, true, false, true);
	 print("<td class=\"rowfollow nowrap\">" .$timeout."</td>");
	PRINT("</tr>");
	$counter++;
}
print("</table>");

}


function get_username($id, $big = false, $link = true, $bold = true, $target = false, $bracket = false, $withtitle = false, $link_ext = "", $underline = false , $namecolour= true,$showpic=true)
{
	static $usernameArray = array();
	global $lang_functions;
	$id = 0+$id;

	if (func_num_args() == 1 && $usernameArray[$id]) {  //One argument=is default display of username. Get it directly from static array if available
		return $usernameArray[$id];
	}
	$arr = get_user_row($id);
	if ($arr){
		if ($big)
		{
			$donorpic = "starbig";
			$leechwarnpic = "leechwarnedbig";
			$warnedpic = "warnedbig";
			$disabledpic = "disabledbig";
			$style = "style='margin-left: 4pt'";
		}
		else
		{
			$donorpic = "star";
			$leechwarnpic = "leechwarned";
			$warnedpic = "warned";
			$disabledpic = "disabled";
			$style = "style='margin-left: 2pt'";
		}
		
		$pics=get_friends_row($id);
		$pics .= $arr["donor"] == "yes" ? "<img class=\"".$donorpic."\" src=\"pic/trans.gif\" alt=\"Donor\" ".$style." />" : "";
		
		if ($arr["enabled"] == "yes")
			$pics .= ($arr["leechwarn"] == "yes" ? "<img class=\"".$leechwarnpic."\" src=\"pic/trans.gif\" alt=\"Leechwarned\" ".$style." />" : "") . ($arr["warned"] == "yes" ? "<img class=\"".$warnedpic."\" src=\"pic/trans.gif\" alt=\"Warned\" ".$style." />" : "").($arr["hrwarned"] ? "<img class=\"hrwarned\" src=\"pic/trans.gif\" title=\"Hrwarned@".$arr["hrwarned"]."\" ".$style." />" : "");
		else
			$pics .= "<img class=\"".$disabledpic."\" src=\"pic/trans.gif\" alt=\"Disabled\" ".$style." />\n";
			
			if(!$showpic)$pics="";
if($namecolour&&$arr['class']>0&&$arr['namecolour'])$namecolour="style=\"color:".$arr['namecolour']."\"";
else $namecolour="";
		$username = ($underline == true ? "<u>" . $arr['username'] . "</u>" : $arr['username']);
		$username = ($bold == true ? "<b>" . $username . "</b>" : $username);
		
		$username = ($link == true ? "<a ". $link_ext . " href=\"userdetails.php?id=" . $id . "\"" . ($target == true ? " target=\"_blank\"" : "") . " class='". get_user_class_name($arr['class'],true) . "_Name' $namecolour>" . $username . "</a>" : "<span class='". get_user_class_name($arr['class'],true) . "_Name' $namecolour>" . $username . "</span>" ) . $pics. ($withtitle == true ? " (" . ($arr['title'] == "" ?  get_user_class_name($arr['class'],false,true,true) : "<span class='".get_user_class_name($arr['class'],true) . "_Name' $namecolour ><b>".htmlspecialchars($arr['title'])."</b></span>" ). ")" : "");

		$username = "<span class=\"nowrap\">" . ( $bracket == true ? "(" . $username . ")" : $username) . "</span>";
	}
	else
	{
		$username = "<i>".$lang_functions['text_orphaned']."</i>";
		$username = "<span class=\"nowrap\" title=\"UID:$id\">" . ( $bracket == true ? "(" . $username . ")" : $username) . "</span>";
	}
	if (func_num_args() == 1) { //One argument=is default display of username, save it in static array
		$usernameArray[$id] = $username;
	}
	return $username;
}

function get_percent_completed_image($p) {
	$maxpx = "45"; // Maximum amount of pixels for the progress bar

	if ($p == 0) $progress = "<img class=\"progbarrest\" src=\"pic/trans.gif\" style=\"width: " . ($maxpx) . "px;\" alt=\"\" />";
	if ($p == 100) $progress = "<img class=\"progbargreen\" src=\"pic/trans.gif\" style=\"width: " . ($maxpx) . "px;\" alt=\"\" />";
	if ($p >= 1 && $p <= 30) $progress = "<img class=\"progbarred\" src=\"pic/trans.gif\" style=\"width: " . ($p*($maxpx/100)) . "px;\" alt=\"\" /><img class=\"progbarrest\" src=\"pic/trans.gif\" style=\"width: " . ((100-$p)*($maxpx/100)) . "px;\" alt=\"\" />";
	if ($p >= 31 && $p <= 65) $progress = "<img class=\"progbaryellow\" src=\"pic/trans.gif\" style=\"width: " . ($p*($maxpx/100)) . "px;\" alt=\"\" /><img class=\"progbarrest\" src=\"pic/trans.gif\" style=\"width: " . ((100-$p)*($maxpx/100)) . "px;\" alt=\"\" />";
	if ($p >= 66 && $p <= 99) $progress = "<img class=\"progbargreen\" src=\"pic/trans.gif\" style=\"width: " . ($p*($maxpx/100)) . "px;\" alt=\"\" /><img class=\"progbarrest\" src=\"pic/trans.gif\" style=\"width: " . ((100-$p)*($maxpx/100)) . "px;\" alt=\"\" />";
	return "<img class=\"bar_left\" src=\"pic/trans.gif\" alt=\"\" />" . $progress ."<img class=\"bar_right\" src=\"pic/trans.gif\" alt=\"\" />";
}

function get_ratio_img($ratio)
{
	if ($ratio >= 16)
	$s = "16";
	else if ($ratio >= 8)
	$s = "8";
	else if ($ratio >= 4)
	$s = "4";
	else if ($ratio >= 2)
	$s = "2";
	else if ($ratio >= 1)
	$s = "1";
	else if ($ratio >= 0.5)
	$s = "05";
	else if ($ratio >= 0.25)
	$s = "025";
	else if ($ratio >= 0.125)
	$s = "0125";
	else
	$s = "";

	return "<img src=\"pic/ratio/ratio".$s.".gif\" alt=\"\" />";
}

function GetVar ($name) {
	if ( is_array($name) ) {
		foreach ($name as $var) GetVar ($var);
	} else {
		if ( !isset($_REQUEST[$name]) )
		return false;
		$GLOBALS[$name] = $_REQUEST[$name];
		return $GLOBALS[$name];
	}
}

function ssr ($arg) {
	if (is_array($arg)) {
		foreach ($arg as $key=>$arg_bit) {
			$arg[$key] = ssr($arg_bit);
		}
	} else {
		$arg = stripslashes($arg);
	}
	return $arg;
}

function parked()
{
	global $lang_functions;
	global $CURUSER;
	if ($CURUSER["parked"] == "yes")
	stderr($lang_functions['std_access_denied'], $lang_functions['std_your_account_parked']);
}

function validusername($username)
{
	if ($username == "")
	return false;

	// The following characters are allowed in user names
	$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	for ($i = 0; $i < strlen($username); ++$i)
	if (strpos($allowedchars, $username[$i]) === false)
	return false;

	return true;
}
	function check_username($username) {
		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
		if($username!=iconv("GB2312","UTF-8",iconv("UTF-8","GB2312",$username)))return FALSE;
		$len = strlen($username);
		if($len > 25 || $len < 5 || preg_match("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&]|$guestexp/is", $username)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
//Code for Viewing NFO file

// code: Takes a string and does a IBM-437-to-HTML-Unicode-Entities-conversion.
// swedishmagic specifies special behavior for Swedish characters.
// Some Swedish Latin-1 letters collide with popular DOS glyphs. If these
// characters are between ASCII-characters (a-zA-Z and more) they are
// treated like the Swedish letters, otherwise like the DOS glyphs.
function code($ibm_437, $swedishmagic = false) {
$table437 = array("\200", "\201", "\202", "\203", "\204", "\205", "\206", "\207",
"\210", "\211", "\212", "\213", "\214", "\215", "\216", "\217", "\220",
"\221", "\222", "\223", "\224", "\225", "\226", "\227", "\230", "\231",
"\232", "\233", "\234", "\235", "\236", "\237", "\240", "\241", "\242",
"\243", "\244", "\245", "\246", "\247", "\250", "\251", "\252", "\253",
"\254", "\255", "\256", "\257", "\260", "\261", "\262", "\263", "\264",
"\265", "\266", "\267", "\270", "\271", "\272", "\273", "\274", "\275",
"\276", "\277", "\300", "\301", "\302", "\303", "\304", "\305", "\306",
"\307", "\310", "\311", "\312", "\313", "\314", "\315", "\316", "\317",
"\320", "\321", "\322", "\323", "\324", "\325", "\326", "\327", "\330",
"\331", "\332", "\333", "\334", "\335", "\336", "\337", "\340", "\341",
"\342", "\343", "\344", "\345", "\346", "\347", "\350", "\351", "\352",
"\353", "\354", "\355", "\356", "\357", "\360", "\361", "\362", "\363",
"\364", "\365", "\366", "\367", "\370", "\371", "\372", "\373", "\374",
"\375", "\376", "\377");

$tablehtml = array("&#x00c7;", "&#x00fc;", "&#x00e9;", "&#x00e2;", "&#x00e4;",
"&#x00e0;", "&#x00e5;", "&#x00e7;", "&#x00ea;", "&#x00eb;", "&#x00e8;",
"&#x00ef;", "&#x00ee;", "&#x00ec;", "&#x00c4;", "&#x00c5;", "&#x00c9;",
"&#x00e6;", "&#x00c6;", "&#x00f4;", "&#x00f6;", "&#x00f2;", "&#x00fb;",
"&#x00f9;", "&#x00ff;", "&#x00d6;", "&#x00dc;", "&#x00a2;", "&#x00a3;",
"&#x00a5;", "&#x20a7;", "&#x0192;", "&#x00e1;", "&#x00ed;", "&#x00f3;",
"&#x00fa;", "&#x00f1;", "&#x00d1;", "&#x00aa;", "&#x00ba;", "&#x00bf;",
"&#x2310;", "&#x00ac;", "&#x00bd;", "&#x00bc;", "&#x00a1;", "&#x00ab;",
"&#x00bb;", "&#x2591;", "&#x2592;", "&#x2593;", "&#x2502;", "&#x2524;",
"&#x2561;", "&#x2562;", "&#x2556;", "&#x2555;", "&#x2563;", "&#x2551;",
"&#x2557;", "&#x255d;", "&#x255c;", "&#x255b;", "&#x2510;", "&#x2514;",
"&#x2534;", "&#x252c;", "&#x251c;", "&#x2500;", "&#x253c;", "&#x255e;",
"&#x255f;", "&#x255a;", "&#x2554;", "&#x2569;", "&#x2566;", "&#x2560;",
"&#x2550;", "&#x256c;", "&#x2567;", "&#x2568;", "&#x2564;", "&#x2565;",
"&#x2559;", "&#x2558;", "&#x2552;", "&#x2553;", "&#x256b;", "&#x256a;",
"&#x2518;", "&#x250c;", "&#x2588;", "&#x2584;", "&#x258c;", "&#x2590;",
"&#x2580;", "&#x03b1;", "&#x00df;", "&#x0393;", "&#x03c0;", "&#x03a3;",
"&#x03c3;", "&#x03bc;", "&#x03c4;", "&#x03a6;", "&#x0398;", "&#x03a9;",
"&#x03b4;", "&#x221e;", "&#x03c6;", "&#x03b5;", "&#x2229;", "&#x2261;",
"&#x00b1;", "&#x2265;", "&#x2264;", "&#x2320;", "&#x2321;", "&#x00f7;",
"&#x2248;", "&#x00b0;", "&#x2219;", "&#x00b7;", "&#x221a;", "&#x207f;",
"&#x00b2;", "&#x25a0;", "&#x00a0;");
$s = htmlspecialchars($ibm_437);


// 0-9, 11-12, 14-31, 127 (decimalt)
$control =
array("\000", "\001", "\002", "\003", "\004", "\005", "\006", "\007",
"\010", "\011", /*"\012",*/ "\013", "\014", /*"\015",*/ "\016", "\017",
"\020", "\021", "\022", "\023", "\024", "\025", "\026", "\027",
"\030", "\031", "\032", "\033", "\034", "\035", "\036", "\037",
"\177");

/* Code control characters to control pictures.
http://www.unicode.org/charts/PDF/U2400.pdf
(This is somewhat the Right Thing, but looks crappy with Courier New.)
$controlpict = array("&#x2423;","&#x2404;");
$s = str_replace($control,$controlpict,$s); */

// replace control chars with space - feel free to fix the regexp smile.gif
/*echo "[a\\x00-\\x1F]";
//$s = preg_replace("/[ \\x00-\\x1F]/", " ", $s);
$s = preg_replace("/[ \000-\037]/", " ", $s); */
$s = str_replace($control," ",$s);




if ($swedishmagic){
$s = str_replace("\345","\206",$s);
$s = str_replace("\344","\204",$s);
$s = str_replace("\366","\224",$s);
// $s = str_replace("\304","\216",$s);
//$s = "[ -~]\\xC4[a-za-z]";

// couldn't get ^ and $ to work, even through I read the man-pages,
// i'm probably too tired and too unfamiliar with posix regexps right now.
$s = preg_replace("/([ -~])\305([ -~])/", "\\1\217\\2", $s);
$s = preg_replace("/([ -~])\304([ -~])/", "\\1\216\\2", $s);
$s = preg_replace("/([ -~])\326([ -~])/", "\\1\231\\2", $s);

$s = str_replace("\311", "\220", $s); //
$s = str_replace("\351", "\202", $s); //
}

$s = str_replace($table437, $tablehtml, $s);
return $s;
}


//Tooltip container for hot movie, classic movie, etc
function create_tooltip_container($id_content_arr, $width = 400)
{
	if(count($id_content_arr))
	{
		$result = "<div id=\"tooltipPool\" style=\"display: none\">";
		foreach($id_content_arr as $id_content_arr_each)
		{
			$result .= "<div id=\"" . $id_content_arr_each['id'] . "\">" . $id_content_arr_each['content'] . "</div>";
		}
		$result .= "</div>";
		print($result);
	}
}

function getimdb($imdb_id, $cache_stamp, $mode = 'minor')
{
	global $lang_functions;
	global $showextinfo;
	$thenumbers = $imdb_id;
	$movie = new imdb ($thenumbers);
	$movieid = $thenumbers;
	$movie->setid ($movieid);

	$target = array('Title', 'Credits', 'Plot');
	switch ($movie->cachestate($target))
	{
		case "0": //cache is not ready
			{
			return false;
			break;
			}
		case "1": //normal
			{
				$title = $movie->title ();
				$year = $movie->year ();
				$country = $movie->country ();
				$countries = "";
				$temp = "";
				//for ($i = 0; $i < count ($country); $i++)
				//{
				//	$temp .="$country[$i], ";
				//}
				//$countries = rtrim(trim($temp), ",");
				
				$countries=$country;

				$director = $movie->director();
				//$director_or_creator = "";
				if ($director)
				{
					//$temp = "";
				//	for ($i = 0; $i < count ($director); $i++)
				//	{
				//		$temp .= $director[$i]["name"].", ";
				//	}
					//$director_or_creator = "<strong><font color=\"DarkRed\">".$lang_functions['text_director'].": </font></strong>".rtrim(trim($temp), ",");

					$director_or_creator = "<strong><font color=\"DarkRed\">".$lang_functions['text_director'].": </font></strong>".$director;
				}
				else { //for tv series
					$creator = $movie->creator();
					$director_or_creator = "<strong><font color=\"DarkRed\">".$lang_functions['text_creator'].": </font></strong>".$creator;
				}
				$cast = $movie->cast();
				$temp = "";
				//for ($i = 0; $i < count ($cast); $i++) //get names of first three casts
				//{
				//	if ($i > 2)
				//	{
				//		break;
				//	}
				//	$temp .= $cast[$i]["name"].", ";
				//}
				$casts =$movie->cast();
				//$casts = rtrim(trim($temp), ",");
				$gen = $movie->genres();
				$genres = $gen[0].(count($gen) > 1 ? ", ".$gen[1] : ""); //get first two genres;
				$rating = $movie->rating ();
				$votes = $movie->votes ();
				if ($votes)
					$imdbrating = "<b>".$rating."</b>/10 (".$votes.$lang_functions['text_votes'].")";
				else $imdbrating = $lang_functions['text_awaiting_five_votes'];

				$tagline = $movie->tagline ();
				switch ($mode)
				{
				case 'minor' : 
					{
					//$autodata = "<font class=\"big\"><b>".$title."</b></font> (".$year.") <br /><strong><font color=\"DarkRed\">".$lang_functions['text_imdb'].": </font></strong>".$imdbrating." <strong><font color=\"DarkRed\">".$lang_functions['text_country'].": </font></strong>".$countries." <strong><font color=\"DarkRed\">".$lang_functions['text_genres'].": </font></strong>".$genres."<br />".$director_or_creator."<strong><font color=\"DarkRed\"> ".$lang_functions['text_starring'].": </font></strong>".$casts."<br /><p><strong>".$tagline."</strong></p>";

					$autodata = "<font class=\"big\"><b>".$title."</b></font> (".$year.") <br /><strong><font color=\"DarkRed\">".$lang_functions['text_imdb'].": </font></strong>".$imdbrating." <br /><strong>".($countries?"<font color=\"DarkRed\">".$lang_functions['text_country'].": </font></strong>".$countries." <br />":"")."<strong><font color=\"DarkRed\">".$lang_functions['text_genres'].": </font></strong>".$gen."<br /><strong><font color=\"DarkRed\">标签: </font></strong>".$tagline."";
					break;
					}
				case 'median':
					{
					if (($photo_url = $movie->photo_localurl() ) != FALSE)
						$smallth = "<img src=\"".$photo_url. "\" width=\"105\" alt=\"poster\" />";
					else $smallth = "";
					//$runtime = $movie->runtime_all();
					$runtime = str_replace(" min",$lang_details['text_mins'], $movie->runtime_all());
					//$tagline = $movie->tagline ();
					$language = $movie->language ();
					$plot = $movie->plot ();
					$plots = "";
					/*if(count($plot) != 0){ //get plots from plot page
							$plots .= "<font color=\"DarkRed\">*</font> ".strip_tags($plot[0], '<br /><i>');
							$plots = mb_substr($plots,0,300,"UTF-8") . (mb_strlen($plots,"UTF-8") > 300 ? " ..." : "" );
							$plots .= (strpos($plots,"<i>") == true && strpos($plots,"</i>") == false ? "</i>" : "");//sometimes <i> is open and not ended because of mb_substr;
							$plots = "<font class=\"small\">".$plots."</font>";
						}
					elseif ($plotoutline = $movie->plotoutline ()){ //get plot from title page
						$plots .= "<font color=\"DarkRed\">*</font> ".strip_tags($plotoutline, '<br /><i>');
						$plots = mb_substr($plots,0,300,"UTF-8") . (mb_strlen($plots,"UTF-8") > 300 ? " ..." : "" );
						$plots .= (strpos($plots,"<i>") == true && strpos($plots,"</i>") == false ? "</i>" : "");//sometimes <i> is open and not ended because of mb_substr;
						$plots = "<font class=\"small\">".$plots."</font>";
						}*/
						
					$plots =$plot;
					$autodata = "<table style=\"background-color: transparent;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
".($smallth ? "<td class=\"clear\" valign=\"top\" align=\"right\">
$smallth
</td>" : "")
."<td class=\"clear\" valign=\"top\" align=\"left\">
<table style=\"background-color: transparent;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" >
<tr><td class=\"clear\" colspan=\"2\"><img class=\"imdb\" src=\"pic/trans.gif\" alt=\"imdb\" /> <font class=\"big\"><b>".$title."</b></font> (".$year.") 
<br /><strong><font color=\"DarkRed\">".$lang_functions['text_imdb'].": </font></strong>".$imdbrating.( $runtime ? "<br /><strong><font color=\"DarkRed\">".$lang_functions['text_runtime'].": </font></strong>".$runtime : "")."
<br /><strong><font color=\"DarkRed\">".$lang_functions['text_country'].": </font></strong>".$countries.( $language ? "<br /><strong><font color=\"DarkRed\">".$lang_functions['text_language'].": </font></strong>".$language: "")."
<br /><strong><font color=\"DarkRed\">".$lang_functions['text_genres'].": </font></strong>".$gen."
 <br /><strong><font color=\"DarkRed\">标签: </font></strong>".$tagline."
 </td></tr>


</table>
</td>
</table>";
//<td class=\"clear\">".$director_or_creator."</td>
//".( $plots ? "<tr><td class=\"clear\" colspan=\"2\">".$plots."</td></tr>" : "")."
//<tr><td class=\"clear\" colspan=\"2\"><strong><font color=\"DarkRed\">".$lang_functions['text_starring'].": </font></strong>".$casts."</td></tr>
					break;
					}
				}
				return $autodata;
			}
			case "2" : 
			{
				return false;
				break;
			}
			case "3" :
			{
				return false;
				break;
			}
	}
}

function quickreply($formname, $taname,$submit){
	print("<textarea id=\"qrbody\" name='".$taname."' cols=\"100\" rows=\"8\" style=\"width: 450px\" onkeydown=\"ctrlenter(event,'compose','qr')\"></textarea>");
	print(smile_row($formname, $taname));
	print("<br />");
 	print("<input type=\"submit\" id=\"qr\" class=\"btn\" onclick=\"javascript:{this.disabled=true;this.form.submit()}\" value=\"".$submit."\" />");?>
	
<link rel="stylesheet" href="javascript/userAutoTips.css" type="text/css" />
<script type="text/javascript" src="javascript/userAutoTips.js"></script>
<script type="text/javascript">userAutoTips({id:'qrbody'});$(window).bind('scroll resize', function(e){userAutoTips({id:'qrbody'})})</script>

<?}

function smile_row($formname, $taname){
	$quickSmilesNumbers = array(50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69);
	$smilerow = "<div align=\"center\">";
	foreach ($quickSmilesNumbers as $smilyNumber) {
		$smilerow .= getSmileIt($formname, $taname, $smilyNumber);
	}
	$smilerow .="<a href=\"javascript: window.open('moresmilies.php?form=$formname&text=$taname','mywin','height=500,width=500,resizable=no,scrollbars=yes')\"><img class=\"transitionpic\" style=\"max-width: 25px;\" src=\"pic/smilies/0.gif\" alt=\"\" /></a>";
	$smilerow .= "</div>";
	return $smilerow;
}
function getSmileIt($formname, $taname, $smilyNumber) {
	return "<a href=\"javascript: SmileIT('[em$smilyNumber]','".$formname."','".$taname."')\"><img class=\"transitionpic\" style=\"max-width: 25px;\" src=\"pic/smilies/$smilyNumber.gif\" alt=\"\" /></a>";
	//return "<a href=\"javascript: SmileIT('[em$smilyNumber]','".$formname."','".$taname."') \" onmouseover=\"domTT_activate(this, event, 'content', '".htmlspecialchars("<table><tr><td><img src=\'pic/smilies/$smilyNumber.gif\' alt=\'\' /></td></tr></table>")."', 'trail', false, 'delay', 0,'lifetime',10000,'styleClass','smilies','maxWidth', 400);\"><img style=\"max-width: 25px;\" src=\"pic/smilies/$smilyNumber.gif\" alt=\"\" /></a>";
	
}

function classlist($selectname,$maxclass, $selected, $minClass = 0){
	$list = "<select name=\"".$selectname."\">";
	for ($i = $minClass; $i <= $maxclass; $i++)
		if($get_user_class_name=get_user_class_name($i,false,false,true))$list .= "<option value=\"".$i."\"" . ($selected == $i ? " selected=\"selected\"" : "") . ">" . $get_user_class_name. "</option>\n";
	$list .= "</select>";
	return $list;
}

function permissiondenied(){
	global $lang_functions;
	stderr($lang_functions['std_error'], $lang_functions['std_permission_denied']);
}

function gettime($time, $withago = true, $twoline = false, $forceago = false, $oneunit = false, $isfuturetime = false){
	global $lang_functions, $CURUSER;
	if ($CURUSER['timetype'] != 'timealive' && !$forceago){
		$newtime = $time;
		if ($twoline){
		$newtime = str_replace(" ", "<br />", $newtime);
		}
	}
	else{
		$timestamp = strtotime($time);
		if ($isfuturetime && $timestamp < TIMENOW)
			$newtime = false;
		else
		{
			$newtime = get_elapsed_time($timestamp,$oneunit).($withago ? ($timestamp <= TIMENOW?$lang_functions['text_ago']:$lang_functions['text_before']): "");
			if($twoline){
				$newtime = str_replace("&nbsp;", "<br />", $newtime);
			}
			elseif($oneunit){
				if ($length = strpos($newtime, "&nbsp;"))
					$newtime = substr($newtime,0,$length);
			}
			else $newtime = str_replace("&nbsp;", $lang_functions['text_space'], $newtime);
			$newtime = "<span title=\"".$time."\">".$newtime."</span>";
		}
	}
	return $newtime;
}

function get_forum_pic_folder(){
	global $CURLANGDIR;
	return "pic/forum_pic/".$CURLANGDIR;
}

function get_category_icon_row($typeid)
{
	global $Cache;
	static $rows;
	if (!$typeid) {
		$typeid=1;
	}
	if (!$rows && !$rows = $Cache->get_value('category_icon_content')){
		$rows = array();
		$res = sql_query("SELECT * FROM caticons ORDER BY id ASC");
		while($row = mysql_fetch_array($res)) {
			$rows[$row['id']] = $row;
		}
		$Cache->cache_value('category_icon_content', $rows, 156400);
	}
	return $rows[$typeid];
}
function get_category_row($catid = NULL)
{
	global $Cache;
	static $rows;
	if (!$rows && !$rows = $Cache->get_value('category_content')){
		$res = sql_query("SELECT categories.*, searchbox.name AS catmodename FROM categories LEFT JOIN searchbox ON categories.mode=searchbox.id");
		while($row = mysql_fetch_array($res)) {
			$rows[$row['id']] = $row;
		}
		$Cache->cache_value('category_content', $rows, 126400);
	}
	if ($catid) {
		return $rows[$catid];
	} else {
		return $rows;
	}
}

function get_second_icon($row, $catimgurl) //for CHDBits
{
	global $CURUSER, $Cache;
	$source=$row['source'];
	$medium=$row['medium'];
	$codec=$row['codec'];
	$standard=$row['standard'];
	$processing=$row['processing'];
	$team=$row['team'];
	$audiocodec=$row['audiocodec'];
	if (!$sirow = $Cache->get_value('secondicon_'.$source.'_'.$medium.'_'.$codec.'_'.$standard.'_'.$processing.'_'.$team.'_'.$audiocodec.'_content')){
		$res = sql_query("SELECT * FROM secondicons WHERE (source = ".sqlesc($source)." OR source=0) AND (medium = ".sqlesc($medium)." OR medium=0) AND (codec = ".sqlesc($codec)." OR codec = 0) AND (standard = ".sqlesc($standard)." OR standard = 0) AND (processing = ".sqlesc($processing)." OR processing = 0) AND (team = ".sqlesc($team)." OR team = 0) AND (audiocodec = ".sqlesc($audiocodec)." OR audiocodec = 0) LIMIT 1");
		$sirow = mysql_fetch_array($res);
		if (!$sirow)
			$sirow = 'not allowed';
		$Cache->cache_value('secondicon_'.$source.'_'.$medium.'_'.$codec.'_'.$standard.'_'.$processing.'_'.$team.'_'.$audiocodec.'_content', $sirow, 116400);
	}
	$catimgurl = get_cat_folder($row['category']);
	if ($sirow == 'not allowed')
		return "<img src=\"pic/cattrans.gif\" style=\"background-image: url(pic/". $catimgurl. "additional/notallowed.png);\" alt=\"" . $sirow["name"] . "\" alt=\"Not Allowed\" />";
	else {
		return "<img".($sirow['class_name'] ? " class=\"".$sirow['class_name']."\"" : "")." src=\"pic/cattrans.gif\" style=\"background-image: url(pic/". $catimgurl. "additional/". $sirow['image'].");\" alt=\"" . $sirow["name"] . "\" title=\"".$sirow['name']."\" />";
	}
}


/*function get_second_name($id, $type) //for CHDBits
{
	global $Cache;	
	if ($id>0&&!$name = $Cache->get_value('secondname_'.$type.'_'.$id))
	{
		if($id>0)$name=get_single_value($type,"name","WHERE id=".sqlesc($id));
		if($name)$name="[".$name."]";
		$Cache->cache_value('secondname_'.$type.'_'.$id, $name, 3600*24);
	}
	return $name;
}*/

function get_second_name(){
global $Cache;
static $a;
	if (!$a && !$a = $Cache->get_value('get_second_name')){
	
$r = (sql_query("SELECT id,name,image FROM categories "));
while ($row = mysql_fetch_array($r)){
$a['categories']['name'][$row[id]] = "[".$row[name]."]";
$a['categories']['image'][$row[id]] = "[".$row[image]."]";
}


$r = (sql_query("SELECT id,name FROM audiocodecs "));
while ($row = mysql_fetch_array($r))
$a['audiocodec'][$row[id]] = "[".$row[name]."]";

$r = (sql_query("SELECT id,name FROM codecs "));
while ($row = mysql_fetch_array($r))
$a['codec'][$row[id]] = "[".$row[name]."]";

$r = (sql_query("SELECT id,name FROM sources "));
while ($row = mysql_fetch_array($r))
$a['source'][$row[id]] = "[".$row[name]."]";

$r = (sql_query("SELECT id,name FROM media "));
while ($row = mysql_fetch_array($r))
$a['medium'][$row[id]] = "[".$row[name]."]";

$r = (sql_query("SELECT id,name FROM standards "));
while ($row = mysql_fetch_array($r))
$a['standard'][$row[id]] = "[".$row[name]."]";

$r = (sql_query("SELECT id,name FROM teams "));
while ($row = mysql_fetch_array($r))
$a['team'][$row[id]] = "[".$row[name]."]";

$r = (sql_query("SELECT id,name FROM processings "));
while ($row = mysql_fetch_array($r))
$a['processing'][$row[id]] = "[".$row[name]."]";

$r = (sql_query("SELECT id,name FROM forums "));
while ($row = mysql_fetch_array($r))
$a['forums'][$row[id]] = "[".$row[name]."]";


$Cache->cache_value('get_second_name', $a, 3600*24);
	}


	return $a;
}
function form_second_name($name)
{
global $Cache,$rootpath;
	
include_once($rootpath . 'include/transfer.class.php');

if (!$a = $Cache->get_value('form_second_name')){
$r = (sql_query("SELECT name FROM audiocodecs "));
while ($row = mysql_fetch_array($r))$a[] = "[".$row[name]."]";
$r = (sql_query("SELECT name FROM sources "));
while ($row = mysql_fetch_array($r))$a[] = "[".$row[name]."]";
$r = (sql_query("SELECT name FROM media "));
while ($row = mysql_fetch_array($r))$a[] = "[".$row[name]."]";
$r = (sql_query("SELECT name FROM standards "));
while ($row = mysql_fetch_array($r)){
$a[] = "[".$row[name]."]";
$a[] = "[".str_replace("*", "X", $row[name])."]";
}
$r = (sql_query("SELECT name FROM teams "));
while ($row = mysql_fetch_array($r))$a[] = "[".$row[name]."]";
$r = (sql_query("SELECT name FROM processings "));
while ($row = mysql_fetch_array($r))$a[] = "[".$row[name]."]";
$Cache->cache_value('form_second_name', $a, 3600*24);
}

$name = str_replace("【", "[", $name);
$name = str_replace("】", "]", $name);
$name = str_replace("] ", "]", $name);
$name = str_replace("[ ", "[", $name);
$name = str_replace(" ]", "]", $name);
$name = str_replace(" [", "[", $name);
$name = str_replace("]]", "]", $name);
$name = str_replace("[[", "[", $name);
$name = str_replace("[*", "[", $name);
$name = str_replace("[]", "", $name);
$name = str_ireplace($a,"",$name);
$transferObject = new Transfer();
$name = $transferObject->twToCn($name);

return $name;
}
function get_torrent_bg_color($promotion = 1,$secondtype=0)
{
	global $CURUSER;

	if ($CURUSER['appendpromotion'] == 'highlight'){
		$global_promotion_state = get_global_sp_state($promotion,$secondtype);
		if ($global_promotion_state == 1){
			if($promotion==1)
				$sphighlight = "";
			elseif($promotion==2)
				$sphighlight = " class='free_bg'";
			elseif($promotion==3)
				$sphighlight = " class='twoup_bg'";
			elseif($promotion==4)
				$sphighlight = " class='twoupfree_bg'";
			elseif($promotion==5)
				$sphighlight = " class='halfdown_bg'";
			elseif($promotion==6)
				$sphighlight = " class='twouphalfdown_bg'";
			elseif($promotion==7)
				$sphighlight = " class='thirtypercentdown_bg'";
			else $sphighlight = "";
		}
		elseif($global_promotion_state == 2)
			$sphighlight = " class='free_bg'";
		elseif($global_promotion_state == 3)
			$sphighlight = " class='twoup_bg'";
		elseif($global_promotion_state == 4)
			$sphighlight = " class='twoupfree_bg'";
		elseif($global_promotion_state == 5)
			$sphighlight = " class='halfdown_bg'";
		elseif($global_promotion_state == 6)
			$sphighlight = " class='twouphalfdown_bg'";
		elseif($global_promotion_state == 7)
			$sphighlight = " class='thirtypercentdown_bg'";
		else
			$sphighlight = "";
	}
	else $sphighlight = "";
	return $sphighlight;
}

function get_torrent_promotion_append($promotion = 1,$secondtype=0,$forcemode = "",$showtimeleft = false, $added = "", $promotionTimeType = 0, $promotionUntil = ''){
	global $CURUSER,$lang_functions;
	global $expirehalfleech_torrent, $expirefree_torrent, $expiretwoup_torrent, $expiretwoupfree_torrent, $expiretwouphalfleech_torrent, $expirethirtypercentleech_torrent;
	$sp_torrent = "";
	$onmouseover = "";
	if (get_global_sp_state($promotion,0) == 1) {
	switch ($promotion){
		case 2:
		{
			if ($showtimeleft && (($expirefree_torrent && $promotionTimeType == 0) || $promotionTimeType == 2))
			{
				if ($promotionTimeType == 2) {
					$futuretime = strtotime($promotionUntil);
				} else {
					$futuretime = strtotime($added) + $expirefree_torrent * 86400;
				}
				$timeout = gettime(date("Y-m-d H:i:s", $futuretime), false, false, true, false, true);
				if ($timeout)
				$onmouseover = "：<b>".$timeout."</b>";
				//else $promotion = 1;
			}
			break;
		}
		case 3:
		{
			if ($showtimeleft && (($expiretwoup_torrent && $promotionTimeType == 0) || $promotionTimeType == 2))
			{
				if ($promotionTimeType == 2) {
					$futuretime = strtotime($promotionUntil);
				} else {
					$futuretime = strtotime($added) + $expiretwoup_torrent * 86400;
				}
				$timeout = gettime(date("Y-m-d H:i:s", $futuretime), false, false, true, false, true);
				if ($timeout)
				$onmouseover = "：<b>".$timeout."</b>";
				//else $promotion = 1;
			}
			break;
		}
		case 4:
		{
			if ($showtimeleft && (($expiretwoupfree_torrent && $promotionTimeType == 0) || $promotionTimeType == 2))
			{
				if ($promotionTimeType == 2) {
					$futuretime = strtotime($promotionUntil);
				} else {
					$futuretime = strtotime($added) + $expiretwoupfree_torrent * 86400;
				}
				$timeout = gettime(date("Y-m-d H:i:s", $futuretime), false, false, true, false, true);
				if ($timeout)
				$onmouseover = "：<b>".$timeout."</b>";
				//else $promotion = 1;
			}
			break;
		}
		case 5:
		{
			if ($showtimeleft && (($expirehalfleech_torrent && $promotionTimeType == 0) || $promotionTimeType == 2))
			{
				if ($promotionTimeType == 2) {
					$futuretime = strtotime($promotionUntil);
				} else {
					$futuretime = strtotime($added) + $expirehalfleech_torrent * 86400;
				}
				$timeout = gettime(date("Y-m-d H:i:s", $futuretime), false, false, true, false, true);
				if ($timeout)
				$onmouseover = "：<b>".$timeout."</b>";
				//else $promotion = 1;
			}
			break;
		}
		case 6:
		{
			if ($showtimeleft && (($expiretwouphalfleech_torrent && $promotionTimeType == 0) || $promotionTimeType == 2))
			{
				if ($promotionTimeType == 2) {
					$futuretime = strtotime($promotionUntil);
				} else {
					$futuretime = strtotime($added) + $expiretwouphalfleech_torrent * 86400;
				}
				$timeout = gettime(date("Y-m-d H:i:s", $futuretime), false, false, true, false, true);
				if ($timeout)
				$onmouseover = "：<b>".$timeout."</b>";
				//else $promotion = 1;
			}
			break;
		}
		case 7:
		{
			if ($showtimeleft && (($expirethirtypercentleech_torrent && $promotionTimeType == 0) || $promotionTimeType == 2))
			{
				if ($promotionTimeType == 2) {
					$futuretime = strtotime($promotionUntil);
				} else {
					$futuretime = strtotime($added) + $expirethirtypercentleech_torrent * 86400;
				}
				$timeout = gettime(date("Y-m-d H:i:s", $futuretime), false, false, true, false, true);
				if ($timeout)
				$onmouseover = "：<b>".$timeout."</b>";
			}
			break;
		}
	}

	}
	

	
	if (($CURUSER['appendpromotion'] == 'word' && $forcemode == "" ) || $forcemode == 'word'){
		if(($promotion==2 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 2){
			$sp_torrent = "<b>[<font class='free' >".$lang_functions['text_free'].$onmouseover."</font>]</b>";
		}
		elseif(($promotion==3 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 3){
			$sp_torrent = "<b>[<font class='twoup' >".$lang_functions['text_two_times_up'].$onmouseover."</font>]</b>";
		}
		elseif(($promotion==4 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 4){
			$sp_torrent = "<b>[<font class='twoupfree' >".$lang_functions['text_free_two_times_up'].$onmouseover."</font>]</b>";
		}
		elseif(($promotion==5 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 5){
			$sp_torrent = "<b>[<font class='halfdown' >".$lang_functions['text_half_down'].$onmouseover."</font>]</b>";
		}
		elseif(($promotion==6 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 6){
			$sp_torrent = "<b>[<font class='twouphalfdown'>".$lang_functions['text_half_down_two_up'].$onmouseover."</font>]</b>";
		}
		elseif(($promotion==7 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 7){
			$sp_torrent = "<b>[<font class='thirtypercent'>".$lang_functions['text_thirty_percent_down'].$onmouseover."</font>]</b>";
		}
	}
		elseif (($CURUSER['appendpromotion'] == 'icon' && $forcemode == "") || $forcemode == 'icon'){
		if($onmouseover)$onmouseover="[剩余".$onmouseover."]";
		if(($promotion==2 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 2)
			$sp_torrent = " <img class=\"pro_free\" src=\"pic/trans.gif\" alt=\"\" title=\"".$lang_functions['text_free']."\" /><b><font class='free' >".$onmouseover."</font></b>";
		elseif(($promotion==3 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 3)
			$sp_torrent = " <img class=\"pro_2up\" src=\"pic/trans.gif\"  title=\"".$lang_functions['text_two_times_up']."\"/><b><font class='twoup' >".$onmouseover."</font></b>";
		elseif(($promotion==4 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 4)
			$sp_torrent = " <img class=\"pro_free2up\" src=\"pic/trans.gif\"  title=\"".$lang_functions['text_free_two_times_up']."\"/><b><font class='twoupfree' >".$onmouseover."</font></b>";
		elseif(($promotion==5 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 5)
			$sp_torrent = " <img class=\"pro_50pctdown\" src=\"pic/trans.gif\"  title=\"".$lang_functions['text_half_down']."\"/><b><font class='halfdown' >".$onmouseover."</font></b>";
		elseif(($promotion==6 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 6)
			$sp_torrent = " <img class=\"pro_50pctdown2up\" src=\"pic/trans.gif\" title=\"".$lang_functions['text_half_down_two_up']."\" /><b><font class='twouphalfdown'>".$onmouseover."</font></b>";
		elseif(($promotion==7 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 7)
			$sp_torrent = " <img class=\"pro_30pctdown\" src=\"pic/trans.gif\" title=\"".$lang_functions['text_thirty_percent_down']."\" /><b><font class='thirtypercent'>".$onmouseover."</font></b>";
	}
	
	/*
	
		elseif (($CURUSER['appendpromotion'] == 'icon' && $forcemode == "") || $forcemode == 'icon'){
		if(($promotion==2 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 2)
			$sp_torrent = " <img class=\"pro_free\" src=\"pic/trans.gif\" alt=\"Free\" ".($onmouseover ? $onmouseover : "title=\"".$lang_functions['text_free']."\"")." />";
		elseif(($promotion==3 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 3)
			$sp_torrent = " <img class=\"pro_2up\" src=\"pic/trans.gif\" alt=\"2X\" ".($onmouseover ? $onmouseover : "title=\"".$lang_functions['text_two_times_up']."\"")." />";
		elseif(($promotion==4 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 4)
			$sp_torrent = " <img class=\"pro_free2up\" src=\"pic/trans.gif\" alt=\"2X Free\" ".($onmouseover ? $onmouseover : "title=\"".$lang_functions['text_free_two_times_up']."\"")." />";
		elseif(($promotion==5 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 5)
			$sp_torrent = " <img class=\"pro_50pctdown\" src=\"pic/trans.gif\" alt=\"50%\" ".($onmouseover ? $onmouseover : "title=\"".$lang_functions['text_half_down']."\"")." />";
		elseif(($promotion==6 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 6)
			$sp_torrent = " <img class=\"pro_50pctdown2up\" src=\"pic/trans.gif\" alt=\"2X 50%\" ".($onmouseover ? $onmouseover : "title=\"".$lang_functions['text_half_down_two_up']."\"")." />";
		elseif(($promotion==7 && get_global_sp_state($promotion,$secondtype) == 1) || get_global_sp_state($promotion,$secondtype) == 7)
			$sp_torrent = " <img class=\"pro_30pctdown\" src=\"pic/trans.gif\" alt=\"30%\" ".($onmouseover ? $onmouseover : "title=\"".$lang_functions['text_thirty_percent_down']."\"")." />";
	}
	
	*/
	return $sp_torrent;
}

function get_user_id_from_name($username){
	global $lang_functions;
	$res = sql_query("SELECT id FROM users WHERE LOWER(username)=LOWER(" . sqlesc($username).")");
	$arr = mysql_fetch_array($res);
	if (!$arr){
		stderr($lang_functions['std_error'],$lang_functions['std_no_user_named']."'".$username."'");
	}
	else return $arr['id'];
}

function is_forum_moderator($id, $in = 'post'){
	global $CURUSER;
	switch($in){
		case 'post':{
			$res = sql_query("SELECT topicid FROM posts WHERE id=$id") or sqlerr(__FILE__, __LINE__);
			if ($arr = mysql_fetch_array($res)){
				if (is_forum_moderator($arr['topicid'],'topic'))
					return true;
			}
			return false;
			break;
		}
		case 'topic':{
			$modcount = sql_query("SELECT COUNT(forummods.userid) FROM forummods LEFT JOIN topics ON forummods.forumid = topics.forumid WHERE topics.id=$id AND forummods.userid=".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
			$arr = mysql_fetch_array($modcount);
			if ($arr[0])
				return true;
			else return false;
			break;
		}
		case 'forum':{
			$modcount = get_row_count("forummods","WHERE forumid=$id AND userid=".sqlesc($CURUSER['id']));
			if ($modcount)
				return true;
			else return false;
			break;
		}
		default: {
		return false;
		}
	}
}

function get_guest_lang_id(){
	global $CURLANGDIR;
	$langfolder=$CURLANGDIR;
	$res = sql_query("SELECT id FROM language WHERE site_lang_folder=".sqlesc($langfolder)." AND site_lang=1");
	$row = mysql_fetch_array($res);
	if ($row){
		return $row['id'];
	}
	else return 6;//return English
}

function set_forum_moderators($name, $forumid, $limit=3){
	$name = rtrim(trim($name), ",");
	$users = explode(",", $name);
	$userids = array();
	foreach ($users as $user){
		$userids[]=get_user_id_from_name(trim($user));
	}
	$max = count($userids);
	sql_query("DELETE FROM forummods WHERE forumid=".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);
	for($i=0; $i < $limit && $i < $max; $i++){
		sql_query("INSERT INTO forummods (forumid, userid) VALUES (".sqlesc($forumid).",".sqlesc($userids[$i]).")") or sqlerr(__FILE__, __LINE__);
	}
}

function get_plain_username($id){
	$row = get_user_row($id);	
	if ($row)
		$username = $row['username'];
	else $username = "";
	return $username;
}

function get_searchbox_value($mode = 1, $item = 'showsubcat'){
	global $Cache;
	static $rows;
	if (!$rows && !$rows = $Cache->get_value('searchbox_content')){
		$rows = array();
		$res = sql_query("SELECT * FROM searchbox ORDER BY id ASC");
		while ($row = mysql_fetch_array($res)) {
			$rows[$row['id']] = $row;
		}
		$Cache->cache_value('searchbox_content', $rows, 100500);
	}
	return $rows[$mode][$item];
}

function get_ratio($userid, $html = true){
	global $lang_functions;
	$row = get_user_row($userid);
	$uped = $row['uploaded'];
	$downed = $row['downloaded'];
	if ($html == true){
		if ($downed > 0)
		{
			$ratio = $uped / $downed;
			$color = get_ratio_color($ratio);
			if($ratio>10000)$ratio=$lang_functions['text_inf'];
			else
			$ratio = number_format($ratio, 3);

			if ($color)
				$ratio = "<font color=\"".$color."\">".$ratio."</font>";
		}
		elseif ($uped > 0)
			$ratio = $lang_functions['text_inf'];
		else
			$ratio = "---";
	}
	else{
		if ($downed > 0)
		{
			$ratio = $uped / $downed;
			if($ratio>10000)$ratio=$lang_functions['text_inf'];
		}
		else $ratio = 1;
	}
	return $ratio;
}

function add_s($num, $es = false)
{
	global $lang_functions;
	return ($num > 1 ? ($es ? $lang_functions['text_es'] : $lang_functions['text_s']) : "");
}

function is_or_are($num)
{
	global $lang_functions;
	return ($num > 1 ? $lang_functions['text_are'] : $lang_functions['text_is']);
}

function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

function get_user_class_image($class){
	$UC = array(
		"Staff Leader" => "pic/staffleader.gif",
		"SysOp" => "pic/sysop.gif",
		"Administrator" => "pic/administrator.gif",
		"Moderator" => "pic/moderator.gif",
		"Forum Moderator" => "pic/forummoderator.gif",
		"Warehouse"=> "pic/uploader.gif",
		"Uploader" => "pic/uploader.gif",
		"Retiree" => "pic/retiree.gif",
		"VIP" => "pic/vip.gif",
		"Nexus Master" => "pic/nexus.gif",
		"Ultimate User" => "pic/ultimate.gif",
		"Extreme User" => "pic/extreme.gif",
		"Veteran User" => "pic/veteran.gif",
		"Insane User" => "pic/insane.gif",
		"Crazy User" => "pic/crazy.gif",
		"Elite User" => "pic/elite.gif",
		"Power User" => "pic/power.gif",
		"User" => "pic/user.gif",
		"Peasant" => "pic/peasant.gif"
	);
	if (isset($class))
		$uclass = $UC[get_user_class_name($class,false,false,false)];
	else $uclass = "pic/banned.gif";
	return $uclass;
}

function user_can_upload($where = "torrents"){
	global $CURUSER,$upload_class,$enablespecial,$uploadspecial_class,$enablespecial2,$uploadspecial_class2;

	if ($CURUSER["uploadpos"] != 'yes')
		return false;
	if ($where == "torrents")
	{
		if (get_user_class() >= $upload_class)
			return true;
		if (get_if_restricted_is_open())
			return true;
	}
	if ($where == "music")
	{
		if ($enablespecial == 'yes' && get_user_class() >= $uploadspecial_class)
			return true;
	}
	
		if ($where == "music2")
	{
		if ($enablespecial2 == 'yes' && get_user_class() >= $uploadspecial_class2)
			return true;
	}
	
		if ($where == "music3")
	{
		if ($enablespecial3 == 'yes' && get_user_class() >= $uploadspecial_class3)
			return true;
	}
	return false;
}

function torrent_selection($name,$selname,$listname,$selectedid = 0,$onchange=false)
{
	global $lang_functions;
	
	$selection = "<b>".$name."</b>&nbsp;<select name=\"".$selname."\" id=\"id".$selname."\" ".($onchange?" onchange=\"javascript:secondtype();notechange();\"":"")." >\n<option value=\"0\">".$lang_functions['select_choose_one']."</option>\n";
	$listarray = searchbox_item_list($listname);
	foreach ($listarray as $row)
		$selection .= "<option value=\"" . $row["id"] . "\"". ($row["id"]==$selectedid ? " selected=\"selected\"" : "").">" . htmlspecialchars($row["name"]) . "</option>\n";
	$selection .= "</select>&nbsp;&nbsp;&nbsp;\n";
	return "<span id=\"dispid".$selname."\">".$selection."</span>";
}

function get_hl_color($color=0)
{
	switch ($color){
		case 0: return false;
		case 1: return "Black";
		case 2: return "Sienna";
		case 3: return "DarkOliveGreen";
		case 4: return "DarkGreen";
		case 5: return "DarkSlateBlue";
		case 6: return "Navy";
		case 7: return "Indigo";
		case 8: return "DarkSlateGray";
		case 9: return "DarkRed";
		case 10: return "DarkOrange";
		case 11: return "Olive";
		case 12: return "Green";
		case 13: return "Teal";
		case 14: return "Blue";
		case 15: return "SlateGray";
		case 16: return "DimGray";
		case 17: return "Red";
		case 18: return "SandyBrown";
		case 19: return "YellowGreen";
		case 20: return "SeaGreen";
		case 21: return "MediumTurquoise";
		case 22: return "RoyalBlue";
		case 23: return "Purple";
		case 24: return "Gray";
		case 25: return "Magenta";
		case 26: return "Orange";
		case 27: return "Yellow";
		case 28: return "Lime";
		case 29: return "Cyan";
		case 30: return "DeepSkyBlue";
		case 31: return "DarkOrchid";
		case 32: return "Silver";
		case 33: return "Pink";
		case 34: return "Wheat";
		case 35: return "LemonChiffon";
		case 36: return "PaleGreen";
		case 37: return "PaleTurquoise";
		case 38: return "LightBlue";
		case 39: return "Plum";
		case 40: return "White";
		default: return false;
	}
}

function get_forum_moderators($forumid, $plaintext = true)
{
	global $Cache;
	static $moderatorsArray;

	if (!$moderatorsArray && !$moderatorsArray = $Cache->get_value('forum_moderator_array')) {
		$moderatorsArray = array();
		$res = sql_query("SELECT forumid, userid FROM forummods ORDER BY forumid ASC") or sqlerr(__FILE__, __LINE__);
		while ($row = mysql_fetch_array($res)) {
			$moderatorsArray[$row['forumid']][] = $row['userid'];
		}
		$Cache->cache_value('forum_moderator_array', $moderatorsArray, 86200);
	}
	$ret = (array)$moderatorsArray[$forumid];

	$moderators = "";
	foreach($ret as $userid) {
		if ($plaintext)
			$moderators .= get_plain_username($userid).", ";
		else $moderators .= get_username($userid).", ";
	}
	$moderators = rtrim(trim($moderators), ",");
	return $moderators;
}
function key_shortcut($page=1,$pages=1)
{
	$currentpage = "var currentpage=".$page.";";
	$maxpage = "var maxpage=".$pages.";";
	$key_shortcut_block = "\n<script type=\"text/javascript\">\n//<![CDATA[\n".$maxpage."\n".$currentpage."\n//]]>\n</script>\n";
	return $key_shortcut_block;
}
function promotion_selection($selected = 0, $hide = 0)
{
	global $lang_functions;
	$selection = "";
	if ($hide != 1)
		$selection .= "<option value=\"1\"".($selected == 1 ? " selected=\"selected\"" : "").">".$lang_functions['text_normal']."</option>";

	if ($hide != 2)
		$selection .= "<option value=\"2\"".($selected == 2 ? " selected=\"selected\"" : "").">".$lang_functions['text_free']."</option>";
	if ($hide != 3)
		$selection .= "<option value=\"3\"".($selected == 3 ? " selected=\"selected\"" : "").">".$lang_functions['text_two_times_up']."</option>";
	if ($hide != 4)
		$selection .= "<option value=\"4\"".($selected == 4 ? " selected=\"selected\"" : "").">".$lang_functions['text_free_two_times_up']."</option>";
	if ($hide != 5)
		$selection .= "<option value=\"5\"".($selected == 5 ? " selected=\"selected\"" : "").">".$lang_functions['text_half_down']."</option>";
	if ($hide != 6)
		$selection .= "<option value=\"6\"".($selected == 6 ? " selected=\"selected\"" : "").">".$lang_functions['text_half_down_two_up']."</option>";
	if ($hide != 7)
		$selection .= "<option value=\"7\"".($selected == 7 ? " selected=\"selected\"" : "").">".$lang_functions['text_thirty_percent_down']."</option>";

	return $selection;
}

function promotion_selectionsearch($selected = 0, $hide = 0)
{
	global $lang_functions;
	$selection = "";

	if ($hide != 1)
		$selection .= "<option value=\"1\"".($selected == 1 ? " selected=\"selected\"" : "").">".$lang_functions['text_normal']."</option>";
	if ($hide != 8)
		$selection .= "<option value=\"8\"".($selected == 8 ? " selected=\"selected\"" : "").">".$lang_functions['text_promotion']."</option>";
	if ($hide != 2)
		$selection .= "<option value=\"2\"".($selected == 2 ? " selected=\"selected\"" : "").">".$lang_functions['text_free']."</option>";

	if ($hide != 3)
		$selection .= "<option value=\"3\"".($selected == 3 ? " selected=\"selected\"" : "").">".$lang_functions['text_two_times_up']."</option>";
	if ($hide != 4)
		$selection .= "<option value=\"4\"".($selected == 4 ? " selected=\"selected\"" : "").">".$lang_functions['text_free_two_times_up']."</option>";
	if ($hide != 5)
		$selection .= "<option value=\"5\"".($selected == 5 ? " selected=\"selected\"" : "").">".$lang_functions['text_half_down']."</option>";
	if ($hide != 6)
		$selection .= "<option value=\"6\"".($selected == 6 ? " selected=\"selected\"" : "").">".$lang_functions['text_half_down_two_up']."</option>";
	if ($hide != 7)
		$selection .= "<option value=\"7\"".($selected == 7 ? " selected=\"selected\"" : "").">".$lang_functions['text_thirty_percent_down']."</option>";

	return $selection;
}



function get_post_row($postid)
{
	global $Cache;
	if (!$row = $Cache->get_value('post_'.$postid.'_content')){
		$res = sql_query("SELECT * FROM posts WHERE id=".sqlesc($postid)." LIMIT 1") or sqlerr(__FILE__,__LINE__);
		$row = mysql_fetch_array($res);
		$Cache->cache_value('post_'.$postid.'_content', $row, 7200);
	}
	if (!$row)
		return false;
	else return $row;
}

function get_country_row($id)
{
	global $Cache;
	if (!$row = $Cache->get_value('country_'.$id.'_content')){
		$res = sql_query("SELECT * FROM countries WHERE id=".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__,__LINE__);
		$row = mysql_fetch_array($res);
		$Cache->cache_value('country_'.$id.'_content', $row, 86400);
	}
	if (!$row)
		return false;
	else return $row;
}

function get_downloadspeed_row($id)
{
	global $Cache;
	if (!$row = $Cache->get_value('downloadspeed_'.$id.'_content')){
		$res = sql_query("SELECT * FROM downloadspeed WHERE id=".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__,__LINE__);
		$row = mysql_fetch_array($res);
		$Cache->cache_value('downloadspeed_'.$id.'_content', $row, 86400);
	}
	if (!$row)
		return false;
	else return $row;
}

function get_uploadspeed_row($id)
{
	global $Cache;
	if (!$row = $Cache->get_value('uploadspeed_'.$id.'_content')){
		$res = sql_query("SELECT * FROM uploadspeed WHERE id=".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__,__LINE__);
		$row = mysql_fetch_array($res);
		$Cache->cache_value('uploadspeed_'.$id.'_content', $row, 86400);
	}
	if (!$row)
		return false;
	else return $row;
}

function get_isp_row($id)
{
	global $Cache;
	if (!$row = $Cache->get_value('isp_'.$id.'_content')){
		$res = sql_query("SELECT * FROM isp WHERE id=".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__,__LINE__);
		$row = mysql_fetch_array($res);
		$Cache->cache_value('isp_'.$id.'_content', $row, 86400);
	}
	if (!$row)
		return false;
	else return $row;
}

function valid_file_name($filename)
{
	$allowedchars = "abcdefghijklmnopqrstuvwxyz0123456789_./";

	$total=strlen($filename);
	for ($i = 0; $i < $total; ++$i)
	if (strpos($allowedchars, $filename[$i]) === false)
		return false;
	return true;
}

function valid_class_name($filename)
{
	$allowedfirstchars = "abcdefghijklmnopqrstuvwxyz";
	$allowedchars = "abcdefghijklmnopqrstuvwxyz0123456789_";

	if(strpos($allowedfirstchars, $filename[0]) === false)
		return false;
	$total=strlen($filename);
	for ($i = 1; $i < $total; ++$i)
	if (strpos($allowedchars, $filename[$i]) === false)
		return false;
	return true;
}

function return_avatar_image($url,$align="center",$scale=true)
{
	global $CURLANGDIR;
	return "<div align=\"$align\" ><img src=\"".$url."\" style='max-width:150px' alt=\"avatar\" onerror=\"errorimg(this);\"  ".($scale?"onload=\"check_avatar(this, '".$CURLANGDIR."');":"onload=\"deleteloading(this);")."\" /></div>";
}
function return_category_image($categoryid, $link="")
{
	static $catImg = array();
	if ($catImg[$categoryid]) {
		$catimg = $catImg[$categoryid];
	} else {
		$categoryrow = get_category_row($categoryid);
		$catimgurl = get_cat_folder($categoryid);
		$catImg[$categoryid] = $catimg = "<img".($categoryrow['class_name'] ? " class=\"".$categoryrow['class_name']."\"" : "")." src=\"pic/cattrans.gif\" alt=\"" . $categoryrow["name"] . "\" title=\"" .$categoryrow["name"]. "\" style=\"background-image: url(pic/" . $catimgurl . $categoryrow["image"].");\" />";
	}
	if ($link) {
		$catimg = "<a href=\"".$link."cat=" . $categoryid . "\">".$catimg."</a>";
	}
	return $catimg;
}


function return_school_name($id)
{
	global $Cache;
	
	if(!is_numeric($id))return $id;
	elseif($id==0)return "未设置";
	elseif (!$row = $Cache->get_value('school_'.$id.'_content')){
		$row = get_single_value("schools","name","WHERE id=".sqlesc($id));
		$Cache->cache_value('school_'.$id.'_content', $row, 86400);
	}
	if (!$row)
		return "未知";
	else return $row;
}

function return_audio($id,$id2='')
{
	if(!$id2)$id2=$id1;
	//$return_audio="<audio src='$id' autoplay='autoplay' type='audio/wav'><bgsound src='$id2' loop= '0'></audio>";
	$return_audio="
	<!--[if !IE]><!--><audio src='$id' autoplay='autoplay' type='audio/wav'><bgsound src='$id2'></audio><!--<![endif]-->
	<!--[if IE]><bgsound src='$id2'><![endif]-->";
	//$return_audio="<audio src='$id' autoplay='autoplay'><embed src='$id2' height='0' width='0' autostart='true' ></audio>";
	
	print $return_audio;
}

function userccss()
{
global $Cache,$CURUSER,$thispagewidthscreen,$Advertisement;
if(!$CURUSER["id"])return;
if(!$cssuserid=$_GET["useridcss"])$cssuserid=$CURUSER["id"];
if (!$row = $Cache->get_value('user_'.$cssuserid.'_css')){

		$res = mysql_fetch_array(sql_query('SELECT css FROM  usercss WHERE  userid  ='.sqlesc($cssuserid).' LIMIT 1 '));
		$row = $res['css'];
		$row="<style type='text/css'>\n".$row."\n</style>";
		$Cache->cache_value('user_'.$cssuserid.'_css', $row, 86400);
	}
	if((date('m')==4&&date('d')==1)&&!$thispagewidthscreen)//$row .="\n<--<style type='text/css'>\nhtml{Filter:FlipH;}\nbody{ -moz-transform: rotate(180deg); -webkit-transform: rotate(45deg);\ntransform: rotate(90deg);}\n</style>-->\n";
	$row .="\n<![if !IE]><script type='text/javascript' src='usercss/mohu4.1.js'></script><![endif]>\n";
	elseif(date('m')==2&&date('d')==14){$row .="\n<style type='text/css'>\nbody{background:url(usercss/festival/goodman.gif) repeat;background-color:#000000;}\ntable.mainouter{filter:alpha(opacity=93);opacity:0.93;}\n#toppic{background-image:none}\n
	#ad_header{background:url(usercss/festival/goodman.gif) no-repeat;}</style>";$Advertisement->adrow['header'][0]="<img src='usercss/festival/goodmanlogo.jpg'>";}

	return $row;

}

function imdbupdatefunction($url,$urltype){
GLOBAL $imdbupdatefail;
	$id=parse_imdb_id($url);
	if(!$id)return false;
	$movie = new imdb ($id);
	$movie->setid ($id);
	$movie->settypt($urltype);
	$movie->purge_single(true,false,30);
	if(!$movie->cachestate()&&$movie->photo_localurl()&&$movie->doubantureid())return true;
	elseif(!$movie->cachestate()){write_log("AUTO_IMDBUPDATE_ERROR_".$url,'file');$imdbupdatefail++;set_cachetimestamp_url($url,1);}
	return false;
}
					
function imdbdoubanautoupdate(){
 global $Cache,$showextinfo;
 GLOBAL $imdbupdatefail;
 $imdbupdatefail=0;
 if($showextinfo['imdb'] != 'yes'||!file_get_contents_function("http://api.douban.com/v2/movie/search?tag=cowboy&start-index=1&count=1")||$_GET['clearcache']||$Cache->get_value('imdbdoubanautoupdate_continue')=='notmore')return;
	$Cache->cache_value('imdbdoubanautoupdate_continue','notmore', 60);
	require_once ("imdb/imdb.class.php");
		$time=0;
		$res = sql_query("SELECT DISTINCT url , urltype FROM torrents where cache_stamp <> 1 ORDER BY RAND()");
		while ($row = mysql_fetch_assoc($res)){
		if(imdbupdatefunction($row[url],$row[urltype]))$time++;
		if($time>20)break;
		}
if($time<19){$Cache->cache_value('imdbdoubanautoupdate_continue','notmore', 3600*24);
write_log("AUTO_IMDBUPDATE_ALLDONE_ERROR_".$imdbupdatefail,'mod');}
}

function CurlGet($url,$timeout=15){ 
global $BASEURLV4,$curlCurlGet;
	$curlCurlGet = curl_init();

	
	
	curl_setopt($curlCurlGet, CURLOPT_URL,$url);
	
	
	curl_setopt($curlCurlGet, CURLOPT_HEADER, false);
	curl_setopt($curlCurlGet, CURLOPT_CONNECTTIMEOUT_MS,$timeout*1000/3); 
	curl_setopt($curlCurlGet, CURLOPT_TIMEOUT_MS,$timeout*1000); 
	curl_setopt($curlCurlGet, CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($curlCurlGet, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curlCurlGet, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curlCurlGet, CURLOPT_SSL_VERIFYHOST, FALSE);
	//curl_setopt($curlCurlGet, CURLOPT_ENCODING, "gzip, deflate");
	//curl_setopt($curlCurlGet, CURLOPT_HTTPHEADER, array('Expect:'));  
	curl_setopt($curlCurlGet, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
	//curl_setopt($curl, CURLOPT_REFERER,htmlspecialchars_decode($url));
	$values = curl_exec($curlCurlGet);
	//curl_close($curl);
	return $values;
}
 
 
function file_get_contents_function($url,$timeout=15){
 /*$opts = array(
           'http'=>array(
	         'method'=>"GET",
	         'timeout'=>$timeout //设置超时，单位是秒，可以试0.1之类的float类型数字
		)
	);
$context = stream_context_create($opts);
$returndata=@file_get_contents($url,false,$context);
if(!$returndata)$returndata=@file_get_contents($url,false,$context);*/
if(!$returndata=CurlGet($url,$timeout))$returndata=CurlGet($url,$timeout);
return $returndata;
}

function  sp_torrent_type($picktype){
global $lang_functions;
	if($picktype=="hot")
	return "<b>[<font class='hot'>".$lang_functions['text_hot']."</font>]</b>";
	elseif($picktype=="classic")
	return "<b>[<font class='classic'>".$lang_functions['text_classic']."</font>]</b>";
	elseif($picktype=="recommended")
	return "<b>[<font class='recommended'>".$lang_functions['text_recommended']."</font>]</b>";
}

function isCompleteJpg($jpg){
$fp=fopen($jpg,"rb");
$size=filesize($jpg)-2;
fseek($fp,$size);
$data = fread($fp, $size);
if(bin2hex($data)=='ffd9')
return true;
else
return false;
}

function  doubanapikey(){
static $doubanapikeyextension = array('0685543954595f93138ffcabfd0c1eb9','0081d3eeeb524f0e01857bccc1f6ed2a','000374292a2712c8000a5c7b7e753858','0487449fd00f9a5e0d95cddf702ecf1a','006880c42125df0a0139824c63719d1e','00dec34f2f920ec2029c49ed8eb62c46','0215711a52b5067a0640534ef81f136e','0f3b9794d034d1592db2c6be709e740b','0886dc71dba01b1b1994955592e05151','0e6cd31394bb6a722b46793abe323f56','00ab0289a2705afc0201079ce75110f4','08fdf9f2356abee11af9edd6a0403ca3');
//08fdf9f2356abee11af9edd6a0403ca3.
return $doubanapikeyextension[mt_rand (0,count($doubanapikeyextension)-1)];
}

function dean_check($deannumber,$deanpassword,$deantype=0){
failedlogins('silent');
if($deantype==1){
if(!dean_check_get($deannumber,$deanpassword)&&!dean_check_post($deannumber,$deanpassword))
stderr("姿势不正确","本科教务认证失败");
}elseif($deantype==2){
stderr("姿势不正确","研究生教务认证暂不存在");
}elseif($deantype==3){
if(!cams_check_header($deannumber,$deanpassword))
stderr("姿势不正确","上网帐号认证失败");
}else
stderr("姿势不正确","认证类型失败");
}

function dean_check_post($deannumber,$deanpassword){
if(is_valid_id($deannumber)||1){
	$opts = array(
           'http'=>array(
	         'method'=>"POST",
			 'content'=>'user_type=student&user_id='.$deannumber.'&password='.($deanpassword),
			 'timeout'=>10
		)
	);
$context = stream_context_create($opts);
$returndata=@file_get_contents('http://202.115.71.132/servlet/UserLoginSQLAction',false,$context);
}else{
	$opts = array(
           'http'=>array(
	         'method'=>"POST",
			 'content'=>'UserName='.$deannumber.'&Password='.md5($deanpassword),
			 'timeout'=>10
		)
	);
$context = stream_context_create($opts);
$returndata=@file_get_contents('http://dean.swjtu.edu.cn/servlet/TeacherLoginAction',false,$context);
}

$returndata=@mb_convert_encoding($returndata, 'UTF-8','GB2312'.',auto');
//echo nl2br(htmlspecialchars($returndata));
return preg_match( "/登录成功/i", $returndata);
}

function  dean_check_get($deannumber,$deanpassword){
if(is_valid_id($deannumber))
$xml=file_get_contents_function("http://dean.swjtu.edu.cn/servlet/DeanStudentLoginAction?UserName=$deannumber&Password=$deanpassword&AttestationUser=SWJTU-DEAN-USER-0001&AttestationPassword=");
else
$xml=file_get_contents_function("http://dean.swjtu.edu.cn/servlet/DeanTeacherLoginAction?UserName=$deannumber&Password=$deanpassword&AttestationUser=SWJTU-DEAN-USER-0001&AttestationPassword=");
return preg_match( "/\<STATE\>1\<\/STATE\>/i", $xml);
}

function cams_check_header($deannumber,$deanpassword){ 
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "http://service.swjtu.edu.cn/Default.aspx?username=$deannumber&userpasswrd=$deanpassword&passlock=y");
	curl_setopt($curl, CURLOPT_HEADER, 1);
	curl_setopt($curl, CURLOPT_NOBODY, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS,5*1000/3); 
	curl_setopt($curl, CURLOPT_TIMEOUT_MS,5*1000); 
	$values = curl_exec($curl);
	curl_close($curl);
	return preg_match("/ASP\.NET_SessionId/i", $values)&&!preg_match("/@/i", $deannumber);
}

function cookietureuserid($name=false){
$userid=(0+base64($_COOKIE["c_secure_uid"],false));
if($name)return get_plain_username($userid);
return $userid;
}

function set_bet_moderators($name){
	$name = rtrim(trim($name), ",");
	$users = explode(",", $name);
	$userids = array();
	foreach ($users as $user){
		$userids[]=get_user_id_from_name(trim($user));
	}
	$max = count($userids);
	sql_query("DELETE FROM betmods") or sqlerr(__FILE__, __LINE__);
	for($i=0; $i < $max; $i++){
		sql_query("INSERT INTO betmods (userid) VALUES (".sqlesc($userids[$i]).")") or sqlerr(__FILE__, __LINE__);
	}
}

function get_bet_moderators($plant=false)
{
		$moderators = "";
		$res = sql_query("SELECT userid FROM betmods") or sqlerr(__FILE__, __LINE__);
		while ($row = mysql_fetch_array($res)) {
			if($plant)$moderators .= get_username($row['userid']).", ";
			else $moderators .= get_plain_username($row['userid']).", ";
		}
		return $moderators;
}

function get_bet_moderators_is($gameid=0){	 
global $Cache,$CURUSER;
	if($gameid)return ($CURUSER["class"] >= UC_POWER_USER)&&($gameid==get_single_value('betgames', 'id',"where id=$gameid and creator=".sqlesc($CURUSER['id'])));
	if (!$moderators = $Cache->get_value('moderators_content')){
		$moderators =array();
		$res = sql_query("SELECT userid FROM betmods") or sqlerr(__FILE__, __LINE__);
		while ($row = mysql_fetch_array($res)) {
			$moderators[$row['userid']] = $row['userid'];
		}
		$Cache->cache_value('moderators_content', $moderators,1800);
		}
		
		return $moderators[$CURUSER['id']];
}

function get_friends_row($userid=0) {
	global $CURUSER, $Cache,$notshowfriendstags;
	static $rows;
	if($notshowfriendstags)return;
	$CURUSERuserid=0+$CURUSER["id"];
	if (!$rows && !$rows = $Cache->get_value('get_friends_row_'.$CURUSERuserid)){
		$rows = array();
		$rows[0]['sname']=$rows[0]['isset'] = true;
		$res = sql_query("SELECT friendid as id,sname from friends WHERE userid=".sqlesc($CURUSERuserid));
		while($row = mysql_fetch_array($res)) {
			$rows[$row['id']]['sname'] = $row['sname'];
			$rows[$row['id']]['isset'] = true;
		}
		$Cache->cache_value('get_friends_row_'.$CURUSERuserid, $rows, 600);
	}
	if($rows[$userid]['sname'])return "<img class=\"buddylist\" src=\"pic/trans.gif\" title=\"".$rows[$userid]['sname']."\">";
	elseif($rows[$userid]['isset'])return "<img  title=\"我的好友\" class=\"buddylist\" src=\"pic/trans.gif\">";
	else return;
}

function set_cachetimestamp_url($url='',$num=0)
{	if($url)
	sql_query("UPDATE torrents SET cache_stamp = $num WHERE url = " . sqlesc($url)) or sqlerr(__FILE__, __LINE__);
	else
	sql_query("UPDATE torrents SET cache_stamp = $num ") or sqlerr(__FILE__, __LINE__);
}

function user_refresh_time($mintime=5,$err=true) {
	global $CURUSER, $Cache;
	if (!$time=$Cache->get_value('user_refresh_time'.$CURUSER['id'].$_SERVER["PHP_SELF"]))
		$Cache->cache_value('user_refresh_time'.$CURUSER['id'].$_SERVER["PHP_SELF"], TIMENOW, $mintime);
	else
		{
		if($err)
			stderr('刷新太快','请在'.($time+$mintime-TIMENOW+1).'秒以后尝试');
		else
			return '刷新太快,请在'.($time+$mintime-TIMENOW+1).'秒以后尝试';
		}
		
	
	
}

function shoutbox_into($body='') {
global $ROBOTUSERID;
sql_query("INSERT INTO shoutbox (userid,date,text,type,ip) VALUES (".sqlesc($ROBOTUSERID).",".sqlesc(TIMENOW).",".sqlesc($body).",".sqlesc("sb").",".sqlesc(getip()).")");
}