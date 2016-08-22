<?php
require "include/bittorrent.php";
dbconn();
if ($CURUSER['class'] < UC_MODERATOR)die;
/*
-----------------------------------------------------
@名称:检测HTTP Header文件头
@作者:风吟
@演示:目前还没有！太消耗流量了。买不起主机，钱多的请捐赠我.
@网站:http://demos.fengyin.name/
@博客:http://fengyin.name/
@更新:2009年9月21日 17:40:32
@版权:Copyright (c) 风吟版权所有，本程序为开源程序(开放源代码)。
只要你遵守 MIT licence 协议.您就可以自由地传播和修改源码以及创作衍生作品.
-------------------------------------------------------
*/
function _get_header($url) {
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, htmlspecialchars_decode($url));
curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_NOBODY, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS,10*1000); 
curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
curl_setopt($curl, CURLOPT_TIMEOUT_MS,30*1000); 
curl_setopt($curl, CURLOPT_ENCODING, "gzip, deflate");
			$cookies[]='getchu_adalt_flag=getchu.com';
			$cookies[]='2b606_winduser=BVYHUgJQPVFbCgNWCgEFVARcA1xUWAABUVAJDQZTAg8FDgIEAwEIaA%3D%3D';//ck
			$cookies[]='0857d_winduser=DVdXCFBoBgcJAFBXVAQBXlMFDVcFBFVSBgMGAVcHVQRXXVVYB1I%3D';//2dgal
			curl_setopt($curl, CURLOPT_COOKIE, implode("; ", $cookies));
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
//curl_exec($curl);

$data=curl_exec($curl);
//print nl2br((var_dump(curl_getinfo($curl))));
				if(preg_match('/charset=[^\w]?([-\w]+)/i',curl_getinfo($curl,CURLINFO_CONTENT_TYPE),$temp))
					$encoding=strtolower($temp[1]);
				elseif(preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i",$data,$temp))
					$encoding=strtolower($temp[1]);
				else $encoding='utf-8,gb2312,gbk';
				if($encoding=='gb18030')$encoding='gbk';
curl_close($curl);
	$data = @mb_convert_encoding($data, 'UTF-8',$encoding.',auto');
echo nl2br(htmlspecialchars($data));

}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<meta charset="utf-8">
<title>HTTP Header信息检测</title>
<style type="text/css" media="all">
body {color: #333;font: 12px Tahoma,Lucida Grande, sans-serif;}
</style>
<form method="post" action="">
输入网址:<input type="text" name="url" size="48"/> <input type="submit" value="检测"/>
<?php
echo $_POST['url']?'':'利用CURL获取你网址或域名的HTTP头,可以检测是否开启GZIP或者测试其他信息.';
?>
</form>

<?php
if(!$_POST['url'])die;
$_POST['url'] = preg_replace('/@.*/', '', $_POST['url']);
echo '<br />网页: '.$_POST['url'].' 的文件头.<br /><br />';
print '<b>标题:'.(formatCodePhp2url($_POST['url'],1)).'</b><br />';
print '<b>图片:'.(formatCodePhp2img($_POST['url'],1)).'</b><br />';
$_POST['url'] = str_replace(array('www.imdb.com','us.imdb.com'),'72.21.214.36',($_POST['url']));
_get_header($_POST['url']);
?>
