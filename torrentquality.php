<?php
require_once("include/bittorrent.php");
require_once("include/benc.php");
dbconn();
loggedinorreturn();
if($_GET['type']=='yes')$wheretype=" and quality='yes' ";
elseif($_GET['type']=='pend')$wheretype=" and quality='pend' ";
elseif($_GET['type']=='no'||!isset($_GET['type'])&&get_user_class() < UC_UPLOADER){$wheretype=" and quality='no' ";$_GET['type']='no';}
else $wheretype=" and 1 ";

if($_GET['my']=='my'||get_user_class() < UC_UPLOADER)$whereuserid=" and owner=".sqlesc($CURUSER[id]);

if(0+$_GET['cat'])$wherecat=" and category=".sqlesc(0+$_GET['cat']);


$rescount = mysql_fetch_assoc(sql_query("SELECT count(*) as num FROM torrents LEFT JOIN categories ON category = categories.id WHERE visible = 'yes' AND categories.mode = $browsecatmode $wheretype $wherecat $whereuserid "));
list($pagertop, $pagerbottom, $limit) = pager(100, $rescount['num'], "?cat={$_GET[cat]}&type={$_GET['type']}&my={$_GET['my']}&");
$rows=sql_query("SELECT torrents.id,torrents.name,category,audiocodec,source,medium,standard,team,processing,quality,editdate,url FROM torrents LEFT JOIN categories ON category = categories.id WHERE visible = 'yes' AND categories.mode = $browsecatmode $wheretype $wherecat $whereuserid ORDER BY editdate DESC $limit");

$bgcolor=array(''=>'','yes'=>'bgcolor=PaleGreen','no'=>'bgcolor=Pink');


stdhead("种子质量");
print("<h1>种子质量查看</h1>");
print("<p>
<a ".($_GET['type']=='all'|!$_GET['type']?" class='faqlink' ":"")." href='?type=all&cat={$_GET['cat']}&my={$_GET['my']}'><b>全部</b></a> | 
<a ".($_GET['type']=='yes'?" class='faqlink' ":"")." href='?type=yes&cat={$_GET['cat']}&my={$_GET['my']}'><b>规范</b></a> | 
<a ".($_GET['type']=='pend'?" class='faqlink' ":"")." href='?type=pend&cat={$_GET['cat']}&my={$_GET['my']}'><b>待定</b></a> | 
<a ".($_GET['type']=='no'?" class='faqlink' ":"")." href='?type=no&cat={$_GET['cat']}&my={$_GET['my']}'><b>不规范</b></a><br />");



$a="<p><a  href='?type={$_GET['type']}'><b>全部</b></a> | <a ".($whereuserid?" class='faqlink' ":"")." href='?my=my&type={$_GET['type']}&cat={$_GET[cat]}'><b>与我相关</b></a>";	
$r = sql_query("SELECT id,name FROM categories where mode  = $browsecatmode order by sort_index ");
while ($row = mysql_fetch_array($r))$a .= " | <a ".($_GET['cat']==$row['id']?" class='faqlink' ":"")." href='?cat={$row[id]}&type={$_GET['type']}&my={$_GET['my']}'><b>{$row[name]}</b></a>";
print $a;


//print($pagertop);
print("<table width=98% border=1 cellspacing=0 cellpadding=5 style=border-collapse:collapse >\n");
if(!$rescount['num'])print("<tr><td class='colhead' align='center'>森马都没有找到</td></tr>");
else{
print("<tr><td class=colhead align=left>名称</td><td class=colhead align=center>分类</td><td class=colhead align=center>次分类</td><td class=colhead  align=center>地区</td><td class=colhead  align=center>平台</td><td class=colhead align=center>分辨率</td><td class=colhead align=center>文件格式</td><td class=colhead align=center>连载状况</td><td class=colhead align=center>豆瓣链接</td><td class=colhead align=center>最后编辑</td>".(get_user_class() >= $torrentmanage_class?"<td class='colhead'>行为<form action='torrentsmanagement.php' method='post'></td>":"")."
</tr></tr>\n");
$get_second_name=get_second_name();
while($row = mysql_fetch_array( $rows )){
	print("<tr {$bgcolor[$row['quality']]}>
	<td align=left class='rowfollow'><a target='_blank' href=\"details.php?id=".$row['id']."\"><b>".htmlspecialchars($row['name'])."</b></a></td>
	<td align=center class='rowfollow nowrap'>".$get_second_name['categories']['name'][$row['category']]."</td>
	<td align=center class='rowfollow nowrap'>".$get_second_name['audiocodec'][$row['audiocodec']]."</td>
	<td align=center class='rowfollow nowrap'>".$get_second_name['source'][$row['source']]."</td>
	<td align=center class='rowfollow nowrap'>".$get_second_name['medium'][$row['medium']]."</td>
	<td align=center class='rowfollow nowrap'>".$get_second_name['standard'][$row['standard']]."</td>
	<td align=center class='rowfollow nowrap'>".$get_second_name['team'][$row['team']]."</td>
	<td align=center class='rowfollow nowrap'>".$get_second_name['processing'][$row['processing']]."</td>
	<td align=center class='rowfollow nowrap'>".parse_imdb_id($row['url'])."</td>
	<td align=center class='rowfollow nowrap'>".gettime($row['editdate'],true,false)."</td>
	".(get_user_class() >= $torrentmanage_class?"<td align=center class='rowfollow nowrap'><input class=checkbox type='checkbox' name='torrentsmanagementid[]' value=".$row['id']."></td>":"")."</tr>\n");
	}
	if (get_user_class() >= UC_UPLOADER)print("<tr><td class=colhead align=left colspan='4'></td><td class=colhead align=center colspan='2'>".(get_user_class() >= $torrentmanage_class?"<input type='submit' name='torrentlowquality' value='分类不规范'>":"")."</td>
	<td class=colhead align=center colspan='3'><input type='button' value='全选' onClick=this.value=check(form,'全选','全不')>
	<input type='button' value='反选' onClick=checktocheck(form)></td>
	<td class=colhead align=center colspan='2'>
	<input type='hidden' name='returnto' value='{$_SERVER['REQUEST_URI']}'>
	<input type='submit' name='torrentqualityyes' value='规范'>
	<input type='submit' name='torrentqualitypend' value='待定'>
	<input type='submit' name='torrentqualityno' value='不规范'></td></form></tr>");
	}
	print("</table>\n");
	print($pagerbottom);
	stdfoot();