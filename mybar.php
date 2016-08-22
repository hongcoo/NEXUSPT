<?php
require "include/bittorrent.php";
dbconn();
header("Content-type: image/png");
$userid = 0 + $_GET["userid"];
$bgpic = 0 + $_GET["bgpic"];
if (!$userid)
	die;
if (!preg_match("/.*userid=([0-9]+)\.png$/i", $_SERVER['REQUEST_URI']))
	die;
if (!$row = $Cache->get_value('userbar_'.$_SERVER['REQUEST_URI'])){
$res = sql_query("SELECT username, uploaded, downloaded, class, privacy FROM users WHERE id=".sqlesc($userid)." LIMIT 1");
$row = mysql_fetch_array($res);
$Cache->cache_value('userbar_'.$_SERVER['REQUEST_URI'], $row, 300);
}
if (!$row)
	die;
elseif($row['privacy'] == 'strong')
	die('隐私级别太高');
//elseif($row['class'] < $userbar_class)
elseif($row['class'] < 1)
	die('用户等级太低');



$Cache->new_page('userbar_png_'.$_SERVER['REQUEST_URI'], 300);
if (!$Cache->get_page()){
$Cache->add_whole_row();

	$username = $row['username'];
	$uploaded = mksize($row['uploaded']);
	$downloaded = mksize($row['downloaded']);
	
$my_img=imagecreatefrompng("pic/userbar/".mt_rand (0,3).".png");
//$my_img=imagecreatefrompng("test/pic/pic.png");
//imagealphablending($my_img, false);



 $red=255;
 $green=255;
 $blue=255;
 $size=4.5;
   $size2=8.5;
 $y=13;
$colour = imagecolorallocate($my_img, $red, $green, $blue);

/*
 $red1=255;
 $green1=100;
 $blue1=100;
$colour1 = imagecolorallocate($my_img, $red1, $green1, $blue1);

 $red2=100;
 $green2=255;
 $blue2=100;
$colour2 = imagecolorallocate($my_img, $red2, $green2, $blue2);

 $red3=128;
 $green3=128;
 $blue3=255;
$colour3 = imagecolorallocate($my_img, $red3, $green3, $blue3);
	*/
	
	
if (!$_GET['noname'])
{

 $namex=10;
 
	imagettfText($my_img,$size2,0,$namex,$y+3,0,$rootpath ."pic/userbar/gbk.ttf",$username); 
	imagettfText($my_img,$size2,0,$namex,$y+1,0,$rootpath ."pic/userbar/gbk.ttf",$username); 
	imagettfText($my_img,$size2,0,$namex-1,$y+2,0,$rootpath ."pic/userbar/gbk.ttf",$username); 
	imagettfText($my_img,$size2,0,$namex+1,$y+2,0,$rootpath ."pic/userbar/gbk.ttf",$username);
	
	
		ImagettfText($my_img,$size2,0,$namex,$y+1,$colour,$rootpath ."pic/userbar/gbk.ttf",$username); 
	//imagestring($my_img, $namesize, $namex, $namey, $username, $name_colour);
}

if (!$_GET['noup'])
{
 $upx=100;
 $uploaded="Up ".$uploaded;

	imagettfText($my_img,$size,0,$upx,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	imagettfText($my_img,$size,0,$upx,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	imagettfText($my_img,$size,0,$upx-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	imagettfText($my_img,$size,0,$upx+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	
	ImagettfText($my_img,$size,0,$upx,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	//ImagettfText($my_img,$size,0,$upx,$y,$colour2,$rootpath ."pic/userbar/ttf.ttf","u");
}

if (!$_GET['nodown'])
{

 $downx=170;
 $downloaded="Down ".$downloaded;

	
	imagettfText($my_img,$size,0,$downx,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	imagettfText($my_img,$size,0,$downx,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	imagettfText($my_img,$size,0,$downx-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	imagettfText($my_img,$size,0,$downx+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	
	
	ImagettfText($my_img,$size,0,$downx,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	//ImagettfText($my_img,$size,0,$downx,$y,$colour1,$rootpath ."pic/userbar/ttf.ttf","d"); 
}

{

 $sitex=265;
 $site=$BASEURLV4V6;

	
	imagettfText($my_img,$size,0,$sitex,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	imagettfText($my_img,$size,0,$sitex,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	imagettfText($my_img,$size,0,$sitex-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	imagettfText($my_img,$size,0,$sitex+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	
	
	ImagettfText($my_img,$size,0,$sitex,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$site);
//ImagettfText($my_img,$size,0,$sitex,$y,$colour2,$rootpath ."pic/userbar/ttf.ttf","p");	
}



imagesavealpha($my_img, true);


imagepng($my_img);
//imagecreatefrompng($img); 
imagedestroy($my_img);

$Cache->end_whole_row();
$Cache->cache_page();
}
header("Content-type: image/png");
echo $Cache->next_row();
?>

