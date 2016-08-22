<?php
require "include/bittorrent.php";
dbconn();
$pattern = "/.*cc98bar\.php\/(nn([0,1]{1}))?(nr([0-9]+))?(ng([0-9]+))?(nb([0-9]+))?(ns([1-5]{1}))?(nx([0-9]+))?(ny([0-9]+))?(nu([0,1]{1}))?(ur([0-9]+))?(ug([0-9]+))?(ub([0-9]+))?(us([1-5]{1}))?(ux([0-9]+))?(uy([0-9]+))?(nd([0,1]{1}))?(dr([0-9]+))?(dg([0-9]+))?(db([0-9]+))?(ds([1-5]{1}))?(dx([0-9]+))?(dy([0-9]+))?(bg([0-9]+))?id([0-9]+)\.png$/i";
if (!preg_match($pattern, $_SERVER['REQUEST_URI'])){
echo "Error! Invalid URL format.";
	die;
}

$nn = preg_replace($pattern, "\\2", $_SERVER['REQUEST_URI']);
$nr = preg_replace($pattern, "\\4", $_SERVER['REQUEST_URI']);
$ng = preg_replace($pattern, "\\6", $_SERVER['REQUEST_URI']);
$nb = preg_replace($pattern, "\\8", $_SERVER['REQUEST_URI']);
$ns = preg_replace($pattern, "\\10", $_SERVER['REQUEST_URI']);
$nx = preg_replace($pattern, "\\12", $_SERVER['REQUEST_URI']);
$ny = preg_replace($pattern, "\\14", $_SERVER['REQUEST_URI']);
$nu = preg_replace($pattern, "\\16", $_SERVER['REQUEST_URI']);
$ur = preg_replace($pattern, "\\18", $_SERVER['REQUEST_URI']);
$ug = preg_replace($pattern, "\\20", $_SERVER['REQUEST_URI']);
$ub = preg_replace($pattern, "\\22", $_SERVER['REQUEST_URI']);
$us = preg_replace($pattern, "\\24", $_SERVER['REQUEST_URI']);
$ux = preg_replace($pattern, "\\26", $_SERVER['REQUEST_URI']);
$uy = preg_replace($pattern, "\\28", $_SERVER['REQUEST_URI']);
$nd = preg_replace($pattern, "\\30", $_SERVER['REQUEST_URI']);
$dr = preg_replace($pattern, "\\32", $_SERVER['REQUEST_URI']);
$dg = preg_replace($pattern, "\\34", $_SERVER['REQUEST_URI']);
$db = preg_replace($pattern, "\\36", $_SERVER['REQUEST_URI']);
$ds = preg_replace($pattern, "\\38", $_SERVER['REQUEST_URI']);
$dx = preg_replace($pattern, "\\40", $_SERVER['REQUEST_URI']);
$dy = preg_replace($pattern, "\\42", $_SERVER['REQUEST_URI']);
$bg = 0+ preg_replace($pattern, "\\44", $_SERVER['REQUEST_URI']);
$id = preg_replace($pattern, "\\45", $_SERVER['REQUEST_URI']);


if (!$row = $Cache->get_value('userbar_'.$_SERVER['REQUEST_URI'])){
$res = sql_query("SELECT username, uploaded, downloaded, class, privacy FROM users WHERE id=".sqlesc($id)." LIMIT 1");
$row = mysql_fetch_array($res);
$Cache->cache_value('userbar_'.$_SERVER['REQUEST_URI'], $row, 300);
}


if (!$row)
	die;
elseif($row['privacy'] == 'strong')
	die;
//elseif($row['class'] < $userbar_class)
elseif($row['class'] < 1)
	die;

	

$Cache->new_page('userbar_png_'.$_SERVER['REQUEST_URI'], 300);
if (!$Cache->get_page()){
$Cache->add_whole_row();

	$username = $row['username'];
	$uploaded = mksize($row['uploaded']);
	$downloaded = mksize($row['downloaded']);
	
$my_img=imagecreatefrompng("pic/userbar/".mt_rand (0,3).".png");
//$my_img=imagecreatefrompng("pic/userbar/".$bg.".png");
//imagealphablending($my_img, false);
 $red=255;
 $green=255;
 $blue=255;
 $size=4.5;
  $size2=8.5;
 $y=13;
$colour = imagecolorallocate($my_img, $red, $green, $blue);



if (!$nn)
{
	 $namex=10;
 
	imagettfText($my_img,$size2,0,$namex,$y+3,0,$rootpath ."pic/userbar/gbk.ttf",$username); 
	imagettfText($my_img,$size2,0,$namex,$y+1,0,$rootpath ."pic/userbar/gbk.ttf",$username); 
	imagettfText($my_img,$size2,0,$namex-1,$y+2,0,$rootpath ."pic/userbar/gbk.ttf",$username); 
	imagettfText($my_img,$size2,0,$namex+1,$y+2,0,$rootpath ."pic/userbar/gbk.ttf",$username);
	
	
		ImagettfText($my_img,$size2,0,$namex,$y+1,$colour,$rootpath ."pic/userbar/gbk.ttf",$username); 
}

if (!$nu)
{
 $upx=100;
 $uploaded="UP ".$uploaded;

	imagettfText($my_img,$size,0,$upx,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	imagettfText($my_img,$size,0,$upx,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	imagettfText($my_img,$size,0,$upx-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	imagettfText($my_img,$size,0,$upx+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
	
	ImagettfText($my_img,$size,0,$upx,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$uploaded); 
}

if (!$nd)
{
 $downx=170;
 $downloaded="DOWN ".$downloaded;

	
	imagettfText($my_img,$size,0,$downx,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	imagettfText($my_img,$size,0,$downx,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	imagettfText($my_img,$size,0,$downx-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	imagettfText($my_img,$size,0,$downx+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
	
	
	ImagettfText($my_img,$size,0,$downx,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$downloaded); 
}

{

 $sitex=265;
 $site=$BASEURLV4V6;

	
	imagettfText($my_img,$size,0,$sitex,$y+1,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	imagettfText($my_img,$size,0,$sitex,$y-1,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	imagettfText($my_img,$size,0,$sitex-1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	imagettfText($my_img,$size,0,$sitex+1,$y,0,$rootpath ."pic/userbar/ttf.ttf",$site); 
	
	
	ImagettfText($my_img,$size,0,$sitex,$y,$colour,$rootpath ."pic/userbar/ttf.ttf",$site); 
}


imagesavealpha($my_img, true);


imagepng($my_img);
imagedestroy($my_img);

$Cache->end_whole_row();
$Cache->cache_page();
}
header("Content-type: image/png");
echo $Cache->next_row();

?>

