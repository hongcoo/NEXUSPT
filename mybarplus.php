<?php
require "include/bittorrent.php";
dbconn();
header("Content-type: image/png");
$userid = 0 + $_GET["userid"];
$bgpic = 0 + $_GET["bgpic"];


function mksize2($bytes)
{
	if ($bytes < 1000 * 1024)
	return number_format($bytes / 1024, 2) . " KB";
	elseif ($bytes < 1000 * 1048576)
	return number_format($bytes / 1048576, 2) . " MB";
	elseif ($bytes < 1000 * 1073741824)
	return number_format($bytes / 1073741824, 2) . " GB";
	elseif ($bytes < 1000 * 1099511627776)
	return number_format($bytes / 1099511627776, 2) . " TB";
	else
	return number_format($bytes / 1125899906842624, 2) . " PB";
}







if (!$userid)
	die;
if (!preg_match("/.*userid=([0-9]+)\.png$/i", $_SERVER['REQUEST_URI']))
	die;
if (!$row = $Cache->get_value('userbar_'.$_SERVER['REQUEST_URI'])){
$res = sql_query("SELECT username, uploaded, downloaded, class, privacy,seedbonus,gender FROM users WHERE id=".sqlesc($userid)." LIMIT 1");
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
	$uploaded = mksize2($row['uploaded']);
	$downloaded = mksize2($row['downloaded']);
	
	
			if ($row['downloaded'] > 0)
		{
			$ratio = $row['uploaded'] / $row['downloaded'];
			if($ratio>10000)
			$ratio = "INF";
			else 
			$ratio = number_format($ratio, 3);

				
			}
		elseif ($row['uploaded'] > 0)
			$ratio = "INF";
		else
			$ratio = "---";
				
	$seedbonus=$row['seedbonus'];
	$class=$row['class'];
	

if($row['gender']!="Male")

$my_img=imagecreatefrompng("pic/userbar/female.png");
else
$my_img=imagecreatefrompng("pic/userbar/male.png");
//$my_img=imagecreatefrompng("test/pic/pic.png");
//imagealphablending($my_img, false);



 $red=255;
 $green=255;
 $blue=255;
 $size=4.5;
$size2=12;
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

 $namex=200;
 $y=15;
	//imagettfText($my_img,$size2,0,$namex,$y+1,0,$rootpath ."pic/userbar/gbk.ttf",$username); 
	//imagettfText($my_img,$size2,0,$namex,$y-1,0,$rootpath ."pic/userbar/gbk.ttf",$username); 
//	imagettfText($my_img,$size2,0,$namex-1,$y,0,$rootpath ."pic/userbar/gbk.ttf",$username); 
//	imagettfText($my_img,$size2,0,$namex+1,$y,0,$rootpath ."pic/userbar/gbk.ttf",$username);
	
	
		ImagettfText($my_img,$size2,0,$namex,$y,$colour,$rootpath ."pic/userbar/gbk.ttf",$username); 
	//imagestring($my_img, $namesize, $namex, $namey, $username, $name_colour);
}

if (!$_GET['noup'])
{
 $upx=187;
  $y=38;
 $uploaded="Up : ".$uploaded;

	//imagettfText($my_img,$size,0,$upx,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	//imagettfText($my_img,$size,0,$upx,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
//	imagettfText($my_img,$size,0,$upx-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
//	imagettfText($my_img,$size,0,$upx+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	
	ImagettfText($my_img,$size,0,$upx,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	//ImagettfText($my_img,$size,0,$upx,$y,$colour2,$rootpath ."pic/userbar/ttf.ttf","u");
}

if (!$_GET['nodown'])
{
 $y=46;
 $downx=175;
 $downloaded="Down : ".$downloaded;

	
//	imagettfText($my_img,$size,0,$downx,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
//	imagettfText($my_img,$size,0,$downx,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	//imagettfText($my_img,$size,0,$downx-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
//	imagettfText($my_img,$size,0,$downx+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	
	
	ImagettfText($my_img,$size,0,$downx,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	//ImagettfText($my_img,$size,0,$downx,$y,$colour1,$rootpath ."pic/userbar/ttf.ttf","d"); 
}


if (!$_GET['noradio'])
{
 $y=54;
 $downx=171;
 $ratio="ratio : ".$ratio;

	
//	imagettfText($my_img,$size,0,$downx,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$ratio); 
//	imagettfText($my_img,$size,0,$downx,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$ratio); 
//	imagettfText($my_img,$size,0,$downx-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$ratio); 
//	imagettfText($my_img,$size,0,$downx+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$ratio); 
	
	
	ImagettfText($my_img,$size,0,$downx,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$ratio); 
	//ImagettfText($my_img,$size,0,$downx,$y,$colour1,$rootpath ."pic/userbar/ttf.ttf","d"); 
}

if (!$_GET['noseedbonus'])
{
 $y=30;
 $downx=169;
 $seedbonus="bonus : ".$seedbonus;

	
//	imagettfText($my_img,$size,0,$downx,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$seedbonus); 
//	imagettfText($my_img,$size,0,$downx,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$seedbonus); 
//	imagettfText($my_img,$size,0,$downx-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$seedbonus); 
//	imagettfText($my_img,$size,0,$downx+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$seedbonus); 
	
	
	ImagettfText($my_img,$size,0,$downx,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$seedbonus); 
	//ImagettfText($my_img,$size,0,$downx,$y,$colour1,$rootpath ."pic/userbar/ttf.ttf","d"); 
}



if (!$_GET['noid'])
{
 $y=54;
 if($userid>99999)
 $downx=260;
  elseif($userid>9999)
 $downx=270;
  elseif($userid>999)
 $downx=280;
  elseif($userid>99)
 $downx=290;
  else
 $downx=300;
 
 $userid2="#".$userid;
$size2=18;
	
	//imagettfText($my_img,$size2,0,$downx,$y+1,0,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
	//imagettfText($my_img,$size2,0,$downx,$y-1,0,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
//	imagettfText($my_img,$size2,0,$downx-1,$y,0,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
//	imagettfText($my_img,$size2,0,$downx+1,$y,0,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
	
	
	//ImagettfText($my_img,$size2,0,$downx,$y,$colour,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
	//ImagettfText($my_img,$size,0,$downx,$y,$colour1,$rootpath ."pic/userbar/ttf.ttf","d"); 
}

if (!$_GET['noclass'])
{
 $y=54;
 
 $downx=290;
 $userid2="LV: ".$class;
$size2=18;
	
	//imagettfText($my_img,$size2,0,$downx,$y+1,0,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
	//imagettfText($my_img,$size2,0,$downx,$y-1,0,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
//	imagettfText($my_img,$size2,0,$downx-1,$y,0,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
//	imagettfText($my_img,$size2,0,$downx+1,$y,0,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
	
	
	ImagettfText($my_img,$size2,0,$downx,$y,$colour,$rootpath ."pic/userbar/gbk.ttf",$userid2); 
	//ImagettfText($my_img,$size,0,$downx,$y,$colour1,$rootpath ."pic/userbar/ttf.ttf","d"); 
}

{

 $y=54;
 $downx=3;
 $site=$BASEURLV4V6;

	
	imagettfText($my_img,$size,0,$downx,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	imagettfText($my_img,$size,0,$downx,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	imagettfText($my_img,$size,0,$downx-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	imagettfText($my_img,$size,0,$downx+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	
	
	ImagettfText($my_img,$size,0,$downx,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$site);
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

