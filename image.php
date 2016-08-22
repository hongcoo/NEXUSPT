<?php
require_once("include/bittorrent.php");
dbconn();
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );

$action = $_GET['action'];
$imagehash = $_GET['imagehash'];
if($action == "regimage")
{	
		$query = "SELECT * FROM regimages WHERE imagehash= ".sqlesc($imagehash) ." and dateline >  ".sqlesc(time()-180). " ORDER BY  dateline DESC "  ;
		$sql = sql_query($query);
		$regimage = mysql_fetch_array($sql);
		
		if($regimage['imdb']||$iv == "op"){
		if(file_exists("imdb/images/".parse_imdb_id($regimage['imdb']).".jpg"))
		$im = imagecreatefromjpeg("imdb/images/".parse_imdb_id($regimage['imdb']).".jpg");
		else $im = imagecreatefromgif("pic/imdb_pic/nophoto.gif");
		// $im = imagecreatefromgif("pic/imdb_pic/nophoto.gif");
		header("Content-type: image/jpeg");
		imagejpeg($im);
		imagedestroy($im);
		exit;
		}
		
		
		if($regimage['imagestring'])
		$imagestring = $regimage['imagestring'];
		else {//$imagestring="ERROR"; 
		//die('invalid action');
		image_code ($imagehash);
		$query = "SELECT * FROM regimages WHERE imagehash= ".sqlesc($imagehash)  ;
		$sql = sql_query($query);
		$regimage = mysql_fetch_array($sql);
		$imagestring = $regimage['imagestring'];
		}
		

		for($i=0;$i<strlen($imagestring);$i++)
		{
			$newstring .= $space.$imagestring[$i];
			$space = " ";
		}
		$imagestring = $newstring;
	
	if(function_exists("imagecreatefrompng"))
	{
		$fontwidth = imageFontWidth(5);
		$fontheight = imageFontHeight(5);
		$textwidth = $fontwidth*strlen($imagestring);
		$textheight = $fontheight;
	
		$randimg = rand(1, 5);
		$im = imagecreatefrompng("pic/regimages/reg".$randimg.".png");
	
		$imgheight = 40;
		$imgwidth = 150;
		$textposh = ($imgwidth-$textwidth)/2;
		$textposv = ($imgheight-$textheight)/2;		
		
			$dots = $imgheight*$imgwidth/35;
			for($i=1;$i<=$dots;$i++)
			{
				imagesetpixel($im, rand(0, $imgwidth), rand(0, $imgheight), $textcolor);
			}
		
		$textcolor = imagecolorallocate($im, 0, 0, 0);
		imagestring($im, 5, $textposh, $textposv, $imagestring, $textcolor);
		//ImageTTFText($im,12,0,$textposh,$textposv,$textcolor,"pic/userbar/GBK.ttf",$imagestring); 
	
		// output the image
		header("Content-type: image/png");
		imagepng($im);
		imagedestroy($im);
		exit;
	}
	else
	{
		header("Location: pic/clear.gif");
	}
}
else
{
	die('invalid action');
}
?>
