<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

$userid=0+$_GET['userid'];
if(!$userid)$userid=$CURUSER['id'];
$type=0+$_GET['type'];

if($user["id"] != $CURUSER["id"]&&get_user_class() < $viewhistory_class)stderr('错误','等级不够，'.get_user_class_name($viewhistory_class,false,true,true)." 方可。",false);

if($type==1)$where="userid= $userid and isimage=1 and inuse=1 ";
elseif($type==2)$where="userid= $userid and isimage=0  and inuse=1";
else $where="userid= $userid and inuse=1";


$count=get_row_count("attachments","where $where");
$perpage = 20;
list($pagertop, $pagerbottom, $limit) = pager($perpage,$count, "?userid=$userid&type=$type&");


stdhead ("附件清单");
print("<h1>". get_username($userid)."的历史附件</h1>");
print "<br><b><a href=?type=0>全部</a> | <a href=?type=1>图片</a> | <a href=?type=2>文档</a></b>";
print("<table>");
$res = sql_query("SELECT * FROM  attachments where $where ORDER BY id DESC $limit") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0){
		print $pagertop;
		print("<table>");
		print("<tr><td><b>Nothing found</b></td></tr>\n");
		print("</table>");
		print $pagertop;
		}else{  
	print $pagertop;
		print("<table>");
  while ($arr = mysql_fetch_assoc($res))
  {
  if(!$arr[isimage])$tabletrtd="<td class=\"colhead\"><b>下载量:</b>$arr[downloads]</td>";
  elseif($arr[thumb])$tabletrtd="<td class=\"colhead\" style=\"background-color:red\"><b>宽度:</b>$arr[width]Px(有缩略图)</td>";
  elseif($arr[iszip])$tabletrtd="<td class=\"colhead\" style=\"background-color:green\"><b>宽度:</b>$arr[width]Px(有压缩)</td>";
  else $tabletrtd="<td class=\"colhead\" style=\"background-color:black\"><b>宽度:</b>$arr[width]Px(无压缩)</td>";
    
  
  print("<tr><td class=\"colhead\"><b>添加时间:</b>$arr[added]</td><td class=\"colhead\"><b>文件类型:</b>$arr[filetype]</td><td class=\"colhead\"><b>大小:</b>".mksize($arr[filesize])."</td>$tabletrtd</tr>\n");
  
  
  print("<tr><td colspan=4>".format_comment("[code][attach]".$arr[dlkey]."[/attach][/code][attach]".$arr[dlkey]."[/attach]")."</td></tr>\n");
  
  
  
  }
	print("</table>");
	print $pagertop;
}

	stdfoot();

?>
