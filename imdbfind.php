<?php
require_once("include/bittorrent.php");
require ("imdb/imdb.class.php");
dbconn();
loggedinorreturn();
if (get_user_class() < UC_SYSOP) 
permissiondenied();
@set_time_limit(600);

//@ini_set("memory_limit",(512+512)."M");
$TIMEUSE=time();
$timeprintnotetime=microtime(1);
$sqlimdbdoubanupdate[]="(0,0,0)";
$printnotetime=0;

function printnote($note)
{
global $printnote,$printnotetime,$timeprintnotetime;
$printnote .= $note."@".(microtime(1)-$timeprintnotetime)."<br/>";
$timeprintnotetime=microtime(1);
$printnotetime++;
}

function printdir($path,$ext)
{
		$dp = @opendir($path);
		if (!$dp)return;
		$ar = array();
		$ar[] =0;
		while (($file = readdir($dp)) !== false) {
			if (!preg_match('/^(\d+)\.'.$ext.'$/', $file, $m))
			continue;

				$ar[] = $m[1];
		}
		closedir($dp);
		return ($ar);

}


		
function imdbfunction($url,$action=1,$urltype=1,$torrentid=0)//typt=1imdb 2豆瓣
{
global $printnote,$sqlimdbdoubanupdate;
$id=parse_imdb_id($url);
if(!$id||$id!=($url)){
return printnote($url."@URL错误");//IMDB错误
}

$movie = new imdb ($id);
$movie->setid ($id);
$movie->settypt($urltype);
//$movie->purge_single_jpg();

   switch ($action) {

	case 1://更新缓存
			if($movie->cachestate())return;
			elseif($movie->photo_localurl()&&$movie->doubantureid())return printnote($id."@缓存更新成功");
			else{$movie->purge_single(TRUE ,TRUE); return printnote($id."@缓存更新失败$torrentid");}
			break;
			
	case 2://删除缓存	
			 
			if(!$movie->cachestate()){$movie->purge_single(true,true);return printnote($id."@没有缓存");}
			elseif($urltype==2&&$movie->imdbtureid()==$id||$urltype==1&&$movie->doubantureid()==$id)return imdbfunction($url,5,$urltype);
			elseif(!$movie->doubantureid()){/*$movie->purge_single(TRUE ,TRUE);*/	return printnote($id."@缓存出错");}
			else return imdbfunction($url,4,$urltype);
			break;
	
	case 3://缓存状态	
			if($movie->cachestate())return true;
			else {/*$movie->purge_single(TRUE ,TRUE);*/return false;}
			break;
	
	case 4://更新SQL	
			if(!$movie->cachestate())return;
			elseif($urltype==1&&$movie->imdbtureid()&&$movie->doubantureid())//IMDB里面寻找
			//$sqlimdbdoubanupdate[] = "(" . sqlesc($movie->imdbtureid()) . "," . sqlesc($movie->doubantureid()) . ",".TIMENOW.")";
			sql_query("INSERT INTO imdbdoubanurl (imdb, douban, time) VALUES (" . sqlesc($movie->imdbtureid()) . "," . sqlesc($movie->doubantureid()) . ",".TIMENOW.")  ON DUPLICATE KEY update douban=values(douban)");			
			sql_query("INSERT INTO imdbinfo (imdb,name,info,rating,time) VALUES  (". sqlesc($id) .",". sqlesc($movie->alsoknowcnname ()) ."," .sqlesc($movie->movieallinfo()) . "," .sqlesc($movie->rating()) . ",".TIMENOW.") ON DUPLICATE KEY update time=values(time)");
			sql_query("UPDATE torrents SET rating =" . sqlesc($movie->rating()) . " WHERE urltype = " . sqlesc($urltype) . " and url = " . sqlesc($id));
			break;
   	case 5://更新缓存类型	
			if($urltype==1)sql_query("UPDATE torrents SET urltype = 2 WHERE urltype = 1 and url = " . sqlesc($id));
			elseif($urltype==2)sql_query("UPDATE torrents SET urltype = 1 WHERE urltype = 2 and url = " . sqlesc($id));
			return imdbfunction($url,2,$urltype);
			break;
	case 6://更新豆瓣中的IMDB	
		if($urltype==1)return;
		elseif($movie->imdbtureid()){
		
		imdbfunction($movie->imdbtureid(),1,1);
		
		if(imdbfunction($movie->imdbtureid(),3,1)){
		sql_query("UPDATE torrents SET urltype = 1 , url = " . sqlesc($movie->imdbtureid()) ." WHERE urltype = 2 and url = " . sqlesc($id));
		//$movie->purge_single(true,true);
		printnote($id."@升级IMDB成功");
		}//else printnote($id."@升级IMDB失败");
		
		}
		break;
	case 7://删除不存在
	printnote($id."@删除不存在缓存");
	$movie->purge_single(true,true);
	break;
	
	case 8://更新IMDB中豆瓣
	if($urltype==2)return;
	elseif($movie->doubantureid()){
	
	imdbfunction($movie->doubantureid(),1,2);
	
	if(imdbfunction($movie->doubantureid(),3,1))
	{
	sql_query("UPDATE torrents SET urltype = 2 , url = " . sqlesc($movie->doubantureid()) ." WHERE urltype = 1 and url = " . sqlesc($id));
	//$movie->purge_single(true,true);
	printnote($id."@升级豆瓣成功");
	}//else printnote($id."@升级豆瓣失败");
	
	}
	break;
		
		
		
	case 66://更新缓存
		if(!$movie->cachestate())return printnote($movie->cachestate()."@缓存更新成功");
		break;
   
   }
}


//////////////////////////////////////////////////////////////////////////////////////////////
if ($_SERVER["REQUEST_METHOD"] == "POST")ipv6statue('NETWORK',true);
if(ipv6statue('NETWORK'))
{



if($_POST['update']){
$res = sql_query("SELECT DISTINCT  url , urltype   FROM torrents   where url > 0  and urltype = 2  ORDER BY torrents.id  DESC");
while ($row = mysql_fetch_assoc($res))imdbfunction($row[url],6,$row[urltype]);
printnote("升级豆瓣为IMDB@".$printnotetime."个");
}


if($_POST['deupdate']){
$res = sql_query("SELECT DISTINCT  url , urltype   FROM torrents   where  url > 0  and urltype = 1  ORDER BY torrents.id  DESC");
while ($row = mysql_fetch_assoc($res))imdbfunction($row[url],8,$row['urltype']);
printnote("降级IMDB为豆瓣@".$printnotetime."个");
}


if($_POST['freshimg']){
$temparr=printdir('./imdb/images','jpg');
foreach($temparr as $row)if(!@imagecreatefromjpeg('./imdb/images/'.$row.'.jpg'))imdbfunction($row,7);
}


if($_POST['find']){

/*
$res = sql_query("SELECT  id,url ,urltype FROM torrents where url > 0 and 
 (url not in (" . join(",", printdir('./imdb/cache','Title')).")  or  url not in (" . join(",", printdir('./imdb/images','jpg'))."))

 ORDER BY  id  DESC");
while ($row = mysql_fetch_assoc($res)){imdbfunction($row[url],7,$row[urltype],$row[id]);imdbfunction($row[url],1,$row[urltype],$row[id]);}*/


		$urlar = array();
		
		$temparr=printdir('./imdb/cache','Title');		 
		foreach($temparr as $row) $urlar['cache'][$row]=$row;
		
		$temparr=printdir('./imdb/images','jpg');
		foreach($temparr as $row)$urlar['images'][$row]=$row;
		//print_r($urlar);
	$res = sql_query("SELECT  DISTINCT  url , urltype , id  FROM torrents where url > 0 GROUP BY  urltype,url ORDER BY RAND()") or sqlerr(__FILE__, __LINE__);
		while ($row = mysql_fetch_array($res)){
		$row[url]=parse_imdb_id($row[url]);
			if(!$urlar['cache'][$row[url]]||!$urlar['images'][$row[url]]){
				 //imdbfunction($row[url],7,$row[urltype],$row[id]);
				 imdbfunction($row[url],1,$row[urltype],$row[id]);
				}
			}
		set_cachetimestamp_url();
printnote("更新不存在数据@".$printnotetime."个");

}

}else printnote("网络错误");



if($_POST['freshall']){

$res = sql_query("SELECT DISTINCT url FROM torrents") or sqlerr(__FILE__, __LINE__);
		$urlar = array();
		while ($row = mysql_fetch_array($res))$urlar[parse_imdb_id($row['url'])] = 1;
		
		$temparr=printdir('./imdb/cache','Title');		 
		foreach($temparr as $row)if(!$urlar[$row])imdbfunction($row,7);
		
		$temparr=printdir('./imdb/images','jpg');
		foreach($temparr as $row)if(!$urlar[$row])imdbfunction($row,7);
		
sql_query("TRUNCATE TABLE  imdbdoubanurl");
sql_query("TRUNCATE TABLE  imdbinfo");
$res = sql_query("SELECT DISTINCT url , urltype   FROM torrents where url > 0 and urltype = 1  ORDER BY  id  DESC");
while ($row = mysql_fetch_assoc($res))imdbfunction($row[url],2,$row[urltype]);

$res = sql_query("SELECT DISTINCT url , urltype   FROM torrents where url > 0 and urltype = 2  ORDER BY  id  DESC");
while ($row = mysql_fetch_assoc($res))imdbfunction($row[url],2,$row[urltype]);


//sql_query('INSERT INTO imdbdoubanurl (imdb, douban, time) VALUES '.join(',',$sqlimdbdoubanupdate).' ON DUPLICATE KEY update douban=values(douban)');




printnote("更新全部数据@".$printnotetime."个");
}



 $resnumnocache = mysql_fetch_assoc(sql_query("SELECT  count(DISTINCT url) as numnocache FROM torrents where url>0 and url not in (select imdb from imdbinfo )"));
 $numurl = mysql_fetch_assoc(sql_query("SELECT  count(DISTINCT  url) as numurl FROM torrents where url>0 "));
 $numdoubanurl = mysql_fetch_assoc(sql_query("SELECT count(DISTINCT  url) as numurl  FROM torrents  where  url>0 and urltype = 2"));
 $numimdburl = mysql_fetch_assoc(sql_query("SELECT count(DISTINCT  url) as numurl  FROM torrents  where url>0 and  urltype = 1"));

stdhead("IMDB整理");
begin_main_frame();
?>
<h1 align="center">IMDB数据整理,用时:<? print (time()-$TIMEUSE)?>秒</h1>


<div style="text-align: center; margin-top: 10px;">
<?php print $printnote;  ?><p>
<table width=100% ><tr style="text-align: center; margin-top: 10px;"><td>
<form method="post" action="imdbfind.php"><input type="hidden" name="freshall" value="1" /><input type="submit"  value="更新全部数据 ( <? echo $numurl[numurl]?> )" onclick="javascript:{this.disabled=true;this.form.submit()}"/></form></td><td>
<form method="post" action="imdbfind.php"><input type="hidden" name="find" value="1" /><input type="submit"  value="更新不存在数据 ( <? echo $resnumnocache[numnocache]?> )" onclick="javascript:{this.disabled=true;this.form.submit()}" /></form></td><td>
<form method="post" action="imdbfind.php"><input type="hidden" name="update" value="1" /><input type="submit"  value="升级豆瓣为IMDB ( <? echo $numdoubanurl[numurl]?> )" onclick="javascript:{this.disabled=true;this.form.submit()}"/></form>
</td>
<td>
<form method="post" action="imdbfind.php"><input type="hidden" name="freshimg" value="1" /><input type="submit"  value="删除无效图片" onclick="javascript:{this.disabled=true;this.form.submit()}"/></form>
</td>
<td>
<form method="post" action="imdbfind.php"><input type="hidden" name="deupdate" value="1" /><input type="submit"  value="降级IMDB为豆瓣 ( <? echo $numimdburl[numurl]?> )" onclick="javascript:{this.disabled=true;this.form.submit()}"/></form>
</td><td>
<form method="post" action="imdbfind.php"><input type="hidden" name="freshall" value="1" /><input type="submit"  value="更新全部数据" onclick="javascript:{this.disabled=true;this.form.submit()}"/></form>
</td>
</tr></table>
</div>
<?php

end_main_frame();
stdfoot();

?>
