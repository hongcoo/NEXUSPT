<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path("shoutbox.php"));

if (isset($_GET['del']))
{
	if (is_valid_id($_GET['del']))
	{
		if((get_user_class() >= $sbmanage_class))
		{	//checkloggedinorreturn();
			sql_query("DELETE FROM shoutbox WHERE id=".mysql_real_escape_string($_GET['del']));
		}
	}
}

if(!$where=$_POST["type"])$where=htmlspecialchars($_GET["type"]);

if($_GET["long"]&&$CURUSER)$longview=1;
else $longview=0;

$refresh = ($CURUSER['sbrefresh'] ? $CURUSER['sbrefresh'] : 120);
if($refresh<60||$longview)$refresh=60;
$refresh=0;
$cssupdatedate=($cssdate_tweak ? "?".htmlspecialchars($cssdate_tweak) : "");
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex,nofollow,nosnippet,noarchive">
<!--<meta http-equiv="Refresh" content="<?php echo $refresh?>; url=<?php echo get_protocol_prefix() . $BASEURL?>/shoutbox.php?type=<?php echo $where?>&long=<?php echo $longview?>">-->
<link rel="stylesheet" href="<?php echo get_font_css_uri().$cssupdatedate?>" type="text/css">
<link rel="stylesheet" href="<?php echo get_css_uri()."theme.css".$cssupdatedate?>" type="text/css">
<link rel="stylesheet" href="styles/curtain_imageresizer.css<?echo $cssupdatedate?>" type="text/css"> 
<link rel="stylesheet" href="styles/sprites.css<?echo $cssupdatedate?>" type="text/css"> 
<script src="javascript/curtain_imageresizer.js<?echo $cssupdatedate?>" type="text/javascript"></script>
<style type="text/css">
body {overflow-y:scroll; overflow-x: hidden;min-width: 1px;}
p{font-size:12px;}
.shoutrowbak {font-family:Lucida Console, Microsoft YaHe, tahoma, arial, helvetica, sans-serif;}
</style>
<?php
print(get_style_addicode());
$startcountdown = "startcountdown(".$refresh.")";
?>
<script type="text/javascript" src="javascript/jquery.js<?echo $cssupdatedate?>"></script> 
<script type="text/javascript">
//<![CDATA[
var t;
var r;
function startcountdown(time)
{
parent.document.getElementById("hbsubmit").disabled=false;
parent.document.getElementById('countdown').innerHTML=time;
time=time+1;
t=setTimeout("startcountdown("+time+")",1000);
}
function countdown(time)
{
	if (time <= 0){
	parent.document.getElementById("hbsubmit").disabled=false;
	parent.document.getElementById("hbsubmit").value=parent.document.getElementById("sbword").innerHTML;
	}
	else {
	parent.document.getElementById("hbsubmit").disabled=true;
	parent.document.getElementById("hbsubmit").value=time;
	time=time-1;
	setTimeout("countdown("+time+")", 1000); 
	}
}

function hbquota(){
parent.document.getElementById("hbsubmit").disabled=true;
var time=10;
countdown(time);
//]]>
}

$(function(){

$(".shoutrow").hover(function(){
	//$(this).css("background-color","#e4e2dd");
	$s="<p class=\"replay\" style=\"display:inline; color:#a7a7a7\" title=\"交谈中请勿轻信汇款、中奖信息、陌生电话。\" >&nbsp;&nbsp;&lt;AT有风险，双击需谨慎~&gt;</p>";
	$(this).append($s);
	},function(){
	$(this).css("background","none");
	$s="<p style=\"display:none;\">I would like to say: </p>";
	$(".replay").remove();})

$(".shoutrow").dblclick(function(){
	$user=$(this).attr('ocr');
	parent.document.forms['shbox'].shbox_text.value="[@"+$user+"]  "+parent.document.forms['shbox'].shbox_text.value;
	parent.document.forms['shbox'].shbox_text.focus();
	})
});

function check_shoutbox(){
jQuery.post('shoutboxchat.php?',{time:'<?echo TIMENOW?>'},function(data){
if(data=='have_new')location.replace(location.href);
r=setTimeout(check_shoutbox,5000);});
}

function shoutboxstop(run){
if(run==0){
clearTimeout(r); 
clearTimeout(t); 
}else{
check_shoutbox();
<? echo $startcountdown?>
}
}


$(function(){check_shoutbox();jQuery.post('sendmail.php');});

</script>
</head>
<body class='inframe' <?php 
if ($_POST["type"] != "helpbox"){?> onload="<?php echo $startcountdown?>" <?php }
else{?> onload="<?php echo $startcountdown?>;hbquota()" <?php } ?>>
<?php
if($_POST["sent"]=="yes"){
if(!$_POST["shbox_text"])
{
	$userid=0+$CURUSER["id"];
}
else
{
	if($_POST["type"]=="helpbox")
	{
	
		$addedtimeout=10;
		
			$a = mysql_fetch_row(sql_query("SELECT date FROM shoutbox WHERE ip = ".sqlesc(getip())." ORDER BY  id desc LIMIT 1")) ;
			$dateline = $a[0];
		
		if ($showhelpbox_main != 'yes')
		{
			
			write_log("Someone is hacking shoutbox. - IP : ".getip(),'mod');
			die($lang_shoutbox['text_helpbox_disabled']);
		}
		$userid=0;
		$type='hb';
	}
	elseif ($_POST["type"] == 'shoutbox')
	{
		$userid=0+$CURUSER["id"];
		if (!$userid){
			write_log("Someone is hacking shoutbox. - IP : ".getip(),'mod');
			die($lang_shoutbox['text_no_permission_to_shoutbox']);
		}
		if ($_POST["toguest"]||$_POST["toguest"]=preg_match("/\[@0\]/i", $_POST["shbox_text"]))
			$type ='hb';
		else $type = 'sb';
	}else $type ='hb';
	//if(preg_match("/([0-9a-z]{32})/i", $_POST["shbox_text"]))die("请不要直接发放邀请码");
	$date=sqlesc(time());
	$text=trim($_POST["shbox_text"]);
	
	if ($dateline <= (TIMENOW - $addedtimeout)){
sql_query("INSERT INTO shoutbox (userid, date, text, type ,ip ,trueuserid ) VALUES (" . sqlesc($userid) . ", $date, " . sqlesc($text) . ", ".sqlesc($type).", ".sqlesc(getip()).", ".sqlesc(cookietureuserid()).")") or sqlerr(__FILE__, __LINE__);
if($chatmarisa)print ("<script>
jQuery.post('shoutboxchat.php?',{id:".mysql_insert_id()."},function(data){if(data=='succeed')location.replace(location.href);});
</script>");
print "<script type=\"text/javascript\">parent.document.forms['shbox'].shbox_text.value='';</script>";
}


}
}

$limit = ($CURUSER['sbnum'] ? $CURUSER['sbnum'] : 20); 
if($longview)$limit=min($limit*5,500);
else $limit=min($limit,100);

$cookie_chat_marisa=$_COOKIE["c_secure_chat_marisa"];
if(!$chatmarisa){;}
elseif($_GET["chat_marisa"]=='no'){
setcookie("c_secure_chat_marisa",'');
$cookie_chat_marisa='';
}elseif($_GET["chat_marisa"]=='yes'){
if($cookie_chat_marisa!='yes')$firstis_chat_marisa=TRUE;
setcookie("c_secure_chat_marisa", 'yes');
$cookie_chat_marisa='yes';
}

if(!$chatmarisa){$chat_marisawhere=' auto = 0 ';}
elseif($cookie_chat_marisa=='yes'){
$chat_marisawhere=' 1 ';
$chat_word=" | <a class=\"faqlink\" href=\"shoutbox.php?chat_marisa=no\">关闭聊天机器人</a>";
}else{
$chat_marisawhere=' auto = 0 ';
$chat_word=" | <a href=\"shoutbox.php?chat_marisa=yes\"><b>开启聊天机器人</b></a>";
}

if ($where == "helpbox"&&$showhelpbox_main == 'yes'){		
$sql = "SELECT * FROM shoutbox WHERE type='hb' and auto = 0  ORDER BY date DESC LIMIT ".$limit;
}
elseif ($CURUSER['hidehb'] == 'yes'){
$sql = "SELECT * FROM shoutbox  WHERE type='sb' and $chat_marisawhere  ORDER BY date DESC LIMIT ".$limit;
}elseif ($CURUSER){
if ($_POST["toguest"])
$sql = "SELECT * FROM shoutbox  WHERE type='hb' and $chat_marisawhere  ORDER BY date DESC LIMIT ".$limit;
elseif($_POST["shout"]) 
$sql = "SELECT * FROM shoutbox  WHERE type='sb' and $chat_marisawhere  ORDER BY date DESC LIMIT ".$limit;
else
$sql = "SELECT * FROM shoutbox where $chat_marisawhere ORDER BY date DESC LIMIT ".$limit;
}else{
die("<h1>".$lang_shoutbox['std_access_denied']."</h1>"."<p>".$lang_shoutbox['std_access_denied_note']."</p></body></html>");
}
$res = sql_query($sql) or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 0)
print("\n");
else
{	
	print("<table border='0' cellspacing='0' cellpadding='2' width='100%' align='left'>\n");
	
	
	if ($CURUSER)
	print("<tr><td ".($chatmarisa?"class=\"shoutrow\"":"class=\"bottom\"")." ocr=$ROBOTUSERID >".get_username($ROBOTUSERID,false,true,true,true,false,false,"",true)." 聊天区仅供……聊天 | 刷屏、粗口禁止 | 慎重发放邀请码 | 点击游客名可查看游客信息".$chat_word."</td></tr>\n");
	else
	print("<tr><td class=\"bottom\" ocr=$ROBOTUSERID >".get_username($ROBOTUSERID,false,true,true,true,false,false,"",true)." " . format_comment('求助区仅供……求助 | 刷屏、粗口禁止 | 邀请码申请格式：邮箱+学校+[s]性取向[/s]',true,false,true,true,600,false,false)."</td></tr>\n
	<tr><td class=\"bottom\" ocr=$ROBOTUSERID >".get_username($ROBOTUSERID,false,true,true,true,false,false,"",true)." " . format_comment('请优先使用教育网邮箱或者本科教务或者上网帐号注册，对于帐号屡次被BAN的同学请反省自己是否适合使用PT',true,false,true,true,600,false,false)."</td></tr>\n");

	IF($firstis_chat_marisa)print("<tr><td class=\"shoutrow\" ocr=$ROBOTUSERID>[<a href=\"#\">".$lang_shoutbox['text_del']."</a>]<span class='date'>[".(($CURUSER['timetype'] == 'timeadded')?strftime("%m.%d %H:%M",TIMENOW):get_elapsed_time(TIMENOW).$lang_shoutbox['text_ago'])."]</span>".get_username($ROBOTUSERID,false,true,true,true,false,false,"",true)." " . format_comment("[@{$CURUSER['id']}]你好,我是魔理沙机器人 >ω<",true,false,true,true,600,false,false)."</td></tr>\n");
	
	while ($arr = mysql_fetch_assoc($res))
	{
	
		if($arr["text"]==$temptxt&&$tempuserid==$arr["userid"])continue;
		
		$temptxt=$arr["text"];
		$tempuserid=$arr["userid"];
		
		if (get_user_class() >= $sbmanage_class)
			$del="[<a href=\"shoutbox.php?del=".$arr[id]."\">".$lang_shoutbox['text_del']."</a>]";
		
		if ($arr["userid"]) {		
			$username = get_username($arr["userid"],false,true,true,true,false,false,"",true);
			if ($_POST["type"] != 'helpbox' && $arr["type"] == 'hb')
				$username .= $lang_shoutbox['text_to_guest'];
			}else{
		$username =(get_user_class() >= UC_POWER_USER)?"<a href='ipinfo.php?guestid=".$arr["id"]."' target='_blank'>".($arr["trueuserid"]?'<b><u>游客</u></b>':$lang_shoutbox['text_guest'])."</a>":$lang_shoutbox['text_guest'];
		}
		
		if ($CURUSER['timetype'] == 'timeadded')
			$time = strftime("%m.%d %H:%M",$arr["date"]);
		else $time = get_elapsed_time($arr["date"]).$lang_shoutbox['text_ago'];
		
		print("<tr><td class=\"shoutrow\" ocr=".($arr["userid"]).">".$del."<span class='date'>[".$time."]</span>".$username." " . format_comment($arr["text"],true,false,true,true,600,false,false)."</td></tr>\n");




	}
	print("</table>");
}
?>
</body>
</html>