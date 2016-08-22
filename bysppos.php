<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

 
 


	//$vip_until = date("Y-m-d H:i:s",(strtotime(date("Y-m-d H:i:s")) + 28*86400));
//	     $futuretime = strtotime($until);
  //  $timeout = gettime(date("Y-m-d H:i:s", strtotime($row["until"])), false, false, true, false, true);
	
$BASEBONDS[0]=0;//	
$BASEBONDS[2]=8;//free
$BASEBONDS[3]=8;//*2
$BASEBONDS[4]=12;//free*2
$BASEBONDS[5]=4;//50%
$BASEBONDS[6]=10;//50%*2
$BASEBONDS[7]=6;//30%



///////////////////////////////////////////////////////////////////////////////////////////////////////
$id = 0 + (int)$_GET["id"];
$buy_spstate= 0 + (int)$_POST["sel_spstate"];
$buy_posstate= 0 + (int)$_POST["sel_posstate"];
$buyid= 0 +(int) $_POST["torrentid"];

if($buyid)
{$id =$buyid;
//int_check($buy_posstate);
}

if($id){
int_check($id);

$res = sql_query("SELECT torrents.id,sp_state,audiocodec,torrents.name,small_descr, pos_state,size ,promotion_until,promotion_time_type,added,money,torrents.owner,categories.mode as categories FROM buysp right JOIN torrents ON 
 buysp.torrent=torrents.id LEFT JOIN categories ON category = categories.id  WHERE torrents.id = $id LIMIT 1") or sqlerr();
$torrents = mysql_fetch_array($res);


if (!$torrents)
	stderr("错误", "没有该ID的种子");
	
	$BASEBONDS[1]=$torrents['size'] / 104857600*3;
	if($torrents['owner'] ==$CURUSER['id'])$BASEBONDS[1]=$BASEBONDS[1]/2;

	
if($torrents["categories"]>0)$categories= "categories.mode = '".$torrents["categories"]."' and ";
	

		
if($buyid){
	if (!($buy_posstate || $buy_spstate) )
		stderr("Error", "Missing form data.");
		
	if ($CURUSER['seedbonus'] < $BASEBONDS[1]*$BASEBONDS[$buy_spstate]||$CURUSER['seedbonus'] < $buy_posstate)
	stderr("错误", "魔力值不足");
	
$until = date("Y-m-d H:i:s",(strtotime(date("Y-m-d H:i:s")) + 3*86400))	;

if($buy_spstate){
//if($torrents["sp_state"] != 1)stderr("错误", "已有促销");
int_check($buy_spstate);
$bonds=(int)$BASEBONDS[1]*$BASEBONDS[$buy_spstate];
			$updateset[] ="sp_state =" .sqlesc($buy_spstate);
			$updateset[] = "promotion_time_type = 2";
			$updateset[] = "promotion_until = ".sqlesc($until);
			sql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");
KPS("-",$bonds,$CURUSER['id']);//Thanks receiver get bonus
}else if($buy_posstate){


if($torrents["pos_state"] != 'normal')stderr("错误", "已置顶");
int_check($buy_posstate);
$bonds=(int)$buy_posstate;

if(get_row_count("buysp", "where torrent=$id")){
			$updateset[] ="money  =money +" .sqlesc($buy_posstate);
			$updateset[] ="times  =times +1";
			//$updateset[] = "useruid  =  ".$CURUSER['id'];
			//$updateset[] = "until  = ".sqlesc($until);
			sql_query("UPDATE buysp  SET " . join(",", $updateset) . " WHERE torrent = $id");
}else{

sql_query("INSERT INTO buysp (torrent ,money ,until ,useruid) VALUES ( ".$id.",". $buy_posstate.", ".sqlesc($until).",". $CURUSER['id'].")");


}

KPS("-",$bonds,$CURUSER['id']);//Thanks receiver get bonus

sql_query("UPDATE  torrents SET nobuymoney  = 'yes' where nobuymoney  = 'no'");
$buynobuymoneyid = array();	
$buynobuymoneyid[]=1;	
$buyres = sql_query("SELECT  torrents.id  from buysp LEFT JOIN torrents  ON buysp.torrent=torrents.id where seeders > 0  and buysp.until > NOW() and pos_state ='normal'  and torrents.id>0 ORDER BY buysp.money DESC LIMIT 5") ;
while ($row = mysql_fetch_assoc($buyres))
$buynobuymoneyid[] = $row["id"];
sql_query("UPDATE  torrents SET nobuymoney  = 'no'  where id in (".join(",", $buynobuymoneyid).")");
	
 
}
	header("Location: $_SERVER[REQUEST_URI]?id=$id");
	die;

		
}	
	
	
	
	
	
	
	
	
}	

stdhead("种子购买");
?>


<h1>种子购买</h1>
<?php
begin_main_frame("",false);
if ($torrents)
{

if($torrents['pos_state']!='normal')
$issis="<b>已置顶</b>";
else if($torrents["money"]>0)
$issis="<b>有竞价</b>[".$torrents["money"]."]";
else $issis="未竞价";

	print("<table border=1 cellspacing=0 cellpadding=5 width=\"100%\">\n");
	print("<tr><td class=rowhead  width=\"20%\">种子序号</td><td>".$torrents['id']."</td></tr>\n");
	print("<tr><td class=rowhead>种子名称</td><td>".$torrents['name']."</td></tr>\n");
	print("<tr><td class=rowhead>种子大小</td><td>".number_format($torrents['size'] / 1048576, 0)."MB</td></tr>\n");
	print("<tr><td class=rowhead>种子位置</td><td>".$issis."</td></tr>\n");
	$timeout=(($torrents["sp_state"] == 1)?"<b>[普通]</b>":get_torrent_promotion_append($torrents['sp_state'],$torrents['audiocodec'],"word",true,$torrents["added"], $torrents['promotion_time_type'], $torrents['promotion_until']));
	
	
	
	print("<tr><td class=rowhead>种子促销</td><td>".$sp.$timeout."</td></tr>\n");

	


$tdhead="<form  method=post action=bysppos.php><input type=\"hidden\" name=\"torrentid\" value=\"".$torrents['id']."\" />";


$BASEBONDSTEXT="个魔力换";


if($CURUSER['seedbonus'] >= $BASEBONDS[1]*4){

if($torrents["sp_state"] == 1)
	{
	$setsp_statebutton="确认";
	$setsp_state="";}
	else {
	$setsp_statebutton="再次申请会覆盖原有促销";
	//$setsp_state="disabled";
	}
	
	
	if($torrents['pos_state']=='normal')
	{$setpos_state="";
	$setpos_statebutton="确认";
	}else{
	$setpos_state="disabled";
	$setpos_statebutton="已置顶";}
	}else{
	$setsp_statebutton="魔力值不足";
	$setpos_statebutton="魔力值不足";
	}
	


 


		$selection .= "<option value=\"2\"".($selected == 2||$selected == 1 ? " selected=\"selected\"" : "").">".number_format($BASEBONDS[1]*$BASEBONDS[2], 0).$BASEBONDSTEXT.$lang_functions['text_free']."</option>";
		$selection .= "<option value=\"3\"".($selected == 3 ? " selected=\"selected\"" : "").">".number_format($BASEBONDS[1]*$BASEBONDS[3], 0).$BASEBONDSTEXT.$lang_functions['text_two_times_up']."</option>";
		$selection .= "<option value=\"4\"".($selected == 4 ? " selected=\"selected\"" : "").">".number_format($BASEBONDS[1]*$BASEBONDS[4], 0).$BASEBONDSTEXT.$lang_functions['text_free_two_times_up']."</option>";
		$selection .= "<option value=\"5\"".($selected == 5 ? " selected=\"selected\"" : "").">".number_format($BASEBONDS[1]*$BASEBONDS[5], 0).$BASEBONDSTEXT.$lang_functions['text_half_down']."</option>";
		$selection .= "<option value=\"6\"".($selected == 6 ? " selected=\"selected\"" : "").">".number_format($BASEBONDS[1]*$BASEBONDS[6], 0).$BASEBONDSTEXT.$lang_functions['text_half_down_two_up']."</option>";
		$selection .= "<option value=\"7\"".($selected == 7 ? " selected=\"selected\"" : "").">".number_format($BASEBONDS[1]*$BASEBONDS[7], 0).$BASEBONDSTEXT.$lang_functions['text_thirty_percent_down']."</option>";
		
		
	

	
	$sel_spstate=$tdhead."<select name=\"sel_spstate\">" .$selection. "</select><input type=\"button\" name=\"btnsubmit\"  ".$setsp_state."  onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$setsp_statebutton."\" /></form >";
	
	print("<tr><td class=rowhead>用魔力求促销</td><td style='padding: 0px;'>".$sel_spstate."</td></tr>");
	
	$sel_posstate=$tdhead."<input type=\"text\" name=\"sel_posstate\" style=\"width: 100px\" maxlength=\"30\" /><input type=\"button\" name=\"btnsubmit\"  ".$setpos_state."  onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$setpos_statebutton."\" /></form >";

	print("<tr><td   class=rowhead>用魔力换置顶</td><td style='padding: 0px;'>".$sel_posstate."</td></tr>");
	print("<tr><td colspan=2>
	所有促销期限为3天,过时后自动清除<br />
	竞价期限为2天,竞价期内可以无限添加出价<br />
	种子列表最前面出现排序前五的有种种子,用绿色高亮,并且下载免流量<br />
	购买者后括号内数值为二次购买魔力值的用户数<br />
	只显示排名前20的种子<br />
	绿色为有种种子,前五的有种种子可出现在种子列表里,可能会有60分钟延迟<br />
	只有简体中文版,BUG较多,欢迎反馈</td></tr>");
	print("</table>\n");
	
	

	

	
	

	
	
}


sql_query("DELETE FROM buysp WHERE until <= NOW()");

$res = sql_query("SELECT buysp.money  ,buysp.until, torrents.id,torrents.category,torrents.leechers,
 torrents.seeders, torrents.name,buysp.times, torrents.small_descr, torrents.times_completed, torrents.size, torrents.added,torrents.last_action,
 buysp.useruid as owner FROM buysp  LEFT JOIN torrents ON 
 buysp.torrent=torrents.id LEFT JOIN categories ON category = categories.id where $categories buysp.until > NOW() and pos_state ='normal' and  torrents.id>0
	ORDER BY buysp.money DESC LIMIT 20");
	print("<br />");
 torrenttable2($res);


end_main_frame();
stdfoot();
