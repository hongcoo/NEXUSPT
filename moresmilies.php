<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
parked();

function countarray($array){
if (count($array)<2) return $array[0];
 for($i = 0;$i < (count($array)-1); $i++)
{
$return .= $array[$i]."&nbsp;&nbsp;";
}
return $return.$array[$i];
}
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
img {border: none;}
body {color: #000000; background-color: #ffffff}
</style>
</head>
<body>
<script type="text/javascript">
function SmileIT(smile,form,text){

if (typeof(window.opener.doInsert) == "function") { 
   window.opener.doInsert(smile, "", false);
} else{ 
      window.opener.document.forms[form].elements[text].value = window.opener.document.forms[form].elements[text].value+" "+smile+" ";
   window.opener.document.forms[form].elements[text].focus();
}

   window.close();
}

function SmileITURL(smile,num){

form='<?php echo $_GET['form']?>';
text='<?php echo $_GET['text']?>';
   window.opener.document.forms[form].elements[text].value = smile;
   window.opener.document.forms[form].elements["imdbnum"].value= num;
   window.opener.document.forms[form].elements[text].focus();
   window.close();
}
</script>


<?php



 
 
 
 


if($_GET['action']=="winoppause"){

?>
<script>
function trans() {
var str = "";
str = rtf.document.body.innerHTML;
str =window.opener.html_trans(str);
<?php
print("SmileIT(str,'".$_GET["form"]."','".$_GET["text"]."')");

?>

}

</script>
<p align="center">
<iframe name="rtf" style="width:300px; height:200px;" src="about:blank" ></iframe>

<script language="javascript" type="text/javascript">
<!--
rtf.document.designMode="on";
rtf.document.open();
rtf.document.writeln('<html><style type="text/css">body {color: #000000; background-color: #eee}</style><body></body></html>');
rtf.document.close();
// -->
</script>
<title>蚂蚁粘贴系统</title>
 <?php 
 
 print("<br /><input type=\"button\" value=\"确认\" onclick=\"javascript:trans();\" /><br />鼠标右键复制想要的网页文本,使用鼠标右键将内容粘贴到上面的框架中,然后点击确认按钮</p>");
 
 print("<div align=\"center\"><FORM action='moresmilies.php' method=GET>
<input type=hidden name='form' value='{$_GET['form']}'>
<input type=hidden name='text' value='{$_GET['text']}'><input type=hidden name='action' value='winoppause'>
种子ID<input name='tidwinoppause' type=text size=10 maxlength='10' value='' >
<INPUT type=submit class=btn alt=search value='引用种子介绍' align=bottom></form></div>");
?>
<p align="center">
<?if($_GET["tidwinoppause"])?>
<script>rtf.document.body.innerHTML = <?echo sqlesc(get_single_value("torrents ", "descr", "WHERE id =".sqlesc(0+$_GET["tidwinoppause"])))?>;</script>

</p>
 <?
 }elseif(!$_GET['keywords']){


?>
<title><?php echo $lang_moresmilies['head_more_smilies']?></title>
<table class="lista" width="100%" cellpadding="1" cellspacing="1"><?php

$count = 0;
for($i=0; $i<192; $i++) {
  if ($count % 10==0)
     print("\n<tr>");

     print("\n\t<td class=\"lista\" align=\"center\"><a href=\"javascript: SmileIT('[em$i]','".$_GET["form"]."','".$_GET["text"]."')\"><img src=\"pic/smilies/$i.gif\" alt=\"\" ></a></td>");
     $count++;

  if ($count % 10==0)
     print("\n</tr>");
}

?>
</table>
<div align="center">
 <a href="javascript: window.close()"><?php echo $lang_moresmilies['text_close']?></a>
 
 <?php 
 
 
 }else{
?>
 <style type="text/css">
#sea {

border: 1px solid #a3bfe8;

}
#douban {
COLOR: #339966;
}

A:link {
	COLOR: red; TEXT-DECORATION: none
}
A:visited {
	COLOR: #339966; TEXT-DECORATION: none
}
A:hover {
	COLOR: orange; TEXT-DECORATION: none
}
</style>

<? 

$url=$_GET['keywords'];
$url = str_replace(" ","+",$url);
//$url=(mb_convert_encoding($url,"GB2312", "UTF-8"));
$url=urlencode($url);
if(!ipv6statue('NETWORK')){
$url='http://movie.douban.com/subject_search?cat=1002&search_text='.$url;
print "<a  href='$url' class='normalMenu'>跳转中....点击这里直接跳转到豆瓣搜索页面</a>";
print"<meta http-equiv=\"Refresh\" content=\"2; url=$url\">";
print("<script text='text/javascript'>alert('由于服务器网络出错,无法拉取豆瓣信息\\n直接将豆瓣连接复制添加即可')</script>");
//redirect($url);
die();
}
$url="http://api.douban.com/v2/movie/search?count=30&apikey=".doubanapikey()."&q=".$url;
?>

<title>点击插入(IMDB查询系统V0.11β)</title>
<FORM action="moresmilies.php" method=GET>
<input type=hidden name="form" value="<?php echo $_GET['form'] ?>">
<input type=hidden name="text" value="<?php echo $_GET['text'] ?>">
<input  name="keywords" type=text size=50 maxlength="50" value="<?php echo $_GET['keywords'] ?>" >
<INPUT   type=submit class=btn alt=search value="重新搜索" align=bottom><br /><br />
</form>

 <?php 
$opts = array(
           'http'=>array(
	         'method'=>"GET",
	         'timeout'=>10 //设置超时，单位是秒，可以试0.1之类的float类型数字
		)
	);
$context = stream_context_create($opts);

if($data=user_refresh_time(3,false))die("<div id=sea> <br /> <br />".$data." <br />  <br /><br /></div>");
//print $url;
else
$data=file_get_contents_function($url);
/*
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,$url);
curl_setopt($curl, CURLOPT_HEADER, 0);   // make sure we get the body   
curl_setopt($curl, CURLOPT_NOBODY, 0);   
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_TIMEOUT,8); 
$data = curl_exec($curl);
curl_close($curl);*/
//$data=(mb_convert_encoding($data,"UTF-8", "gb2312"));


 
    if(!$data) {  print("<div id=sea> <br /> <br />查询服务器太忙,请稍后再试 <br />  <br /><br /></div>");}
else
{

$data=(json_decode($data));
if(!$data->total)print("<div id=sea> <br /> <br />没有找到相关资源，请更换关键字<br>当前关键字：{$_GET['keywords']} <br />  <br /><br /></div>");
else print("<div id=sea>没有找到想要资源？可尝试其他关键字<br>当前关键字：{$_GET['keywords']}</div><br />");
$data=$data->subjects;
foreach($data as $block )
{

//preg_match_all( "/\<db:attribute name=\"imdb\"\>(.*?)\<\/db:attribute\>/", $block, $imdb );

	$doubanurl = $block->alt;
	$picurl=$block->images->small;
	if (preg_match("/default/i", $picurl))continue;

 if($imdb[1][0]){
 echo "<div id=sea><table><tr><td><a href=javascript:SmileITURL(\"".$imdb[1][0]."\",\"1\")><img src=\"".$picurl."\"></a></td><td><a href=javascript:SmileITURL(\"".$imdb[1][0]."\",\"1\")>".countarray($block->attrs->title)."</a>".($block->attrs->pubdate?  "(".countarray($block->attrs->pubdate).")":"")."(IMDB)<a href=javascript:SmileITURL(\"".$doubanurl."\",\"2\")>(豆瓣)</a>";//."<a id=douban href=javascript:SmileITURL(\"".$doubanurl[1][0]."\",\"2\")>♨</a>";
 }
 else
 echo "<div id=sea><table><tr><td><a href=javascript:SmileITURL(\"".$doubanurl."\",\"2\")><img src=\"".$picurl."\"></a></td><td><a href=javascript:SmileITURL(\"".$doubanurl."\",\"2\")>".($block->title).($block->original_title?  " ( ".($block->original_title)." ) ":"")."</a>".($block->year?  " ( ".($block->year)." ) ":"")."(豆瓣)";
 
 echo "<br />";
 

 
 
	if($block->alt_title)echo $block->alt_title;
	//if($block->alt_title)echo $block->alt_title;
	if($block->attrs->cast)echo '<br />'.countarray($block->attrs->cast);




echo "<br><a href=\"".$doubanurl."\" target=\"_blank\">浏览豆瓣页面</a></td></tr></table></div> <br />";





}

 }
  ?>
  
    

</FORM>


</body>
</html>
 <?php 
 
 
 }
?>