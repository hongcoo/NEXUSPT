<?php 
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
//起始$CURUSER['showdlnotice']: 1:新注册 2:升级以后 //cleanup同步
//终止$CURUSER['showdlnotice']=0
function get_my_question()
{
global $Cache,$CURUSER;	
	if (!$a = $Cache->get_value('get_baka_my_question_'.$CURUSER['id']))
	{
	$a[0] = ' bgcolor=green ';
	$r = sql_query("SELECT id  FROM bakatest where userid= ".$CURUSER['id']);
	while ($row = mysql_fetch_array($r))$a[$row[id]] = ' bgcolor=green ';
	$Cache->cache_value('get_baka_my_question_'.$CURUSER['id'], $a, 3600);
	}
return $a;
}
	
if($CURUSER['showdlnotice']==1){//新注册
$addbouns=1000;//答题奖励
$LIMIT=5;//最大答题数
$addbounsperadd=0;//答题奖励魔力值
$addbounsperdec=0;//答题失去魔力
$bakatype='question';
$wherebakatype=" type = 'question' and ";
$level=' and level = 0 ';
}elseif($CURUSER['showdlnotice']==2){
$addbouns=5000;//$addbouns=3000;//答题奖励,默认3000
$LIMIT=10;//$LIMIT=50;//最大答题数,默认50
$addbounsperadd=0;//答题奖励魔力值
$addbounsperdec=5;//答题失去魔力
$bakatype='question';
$wherebakatype=" type = 'question' and ";
$level=' and level = 1 ';//$level=' and level = 1 ';
}else{//if($_GET["showup"]=='today'||$_POST["showup"]=='today'){
$addbouns=5;//答错
//$showup='today';
$LIMIT=1;//最大答题数
$addbounsperadd=0;//答题奖励魔力值
$addbounsperdec=5;//答题失去魔力
$bakatype='dayper';
$wherebakatype=" type = 'dayper' and time= ".sqlesc(strtotime(date("Y-m-d")))." and ";
$level=' and level >= 2 ';
$ischange=$Cache->get_value('user_get_baka_'.$CURUSER["id"].'_changebefore'.strtotime(date("Y-m-d")));
if(!$ischange&&isset($_POST["wantchange"])){
$Cache->cache_value('user_get_baka_'.$CURUSER["id"].'_changebefore'.strtotime(date("Y-m-d")),true,3600);
sql_query("Delete from bakatestanswer where $wherebakatype userid=".sqlesc($CURUSER['id']));
$level=' and level <= 2 and level >= 1 ';
}elseif(!$ischange){
	$canchange=true;
	$canskip=true;
	$addbounsperdec=1;//答题失去魔力
	}

$isskip=(isset($_POST["wantskip"])&&$canskip?true:false);
}

$questinguser = mysql_fetch_assoc(sql_query("SELECT questionid,num FROM bakatestanswer where $wherebakatype userid=".sqlesc($CURUSER['id'])));

function formattest($s){
$wordf = array('[b]','[i]','[u]','[s]','[/b]','[/i]','[/u]','[/s]');
$words = array('<b>', '<i>', '<ins>','<del>','</b>', '</i>', '</ins>','</del>');
return str_replace($wordf,$words,htmlspecialchars($s));
}

	if($CURUSER['showdlnotice']>=1&&!$questinguser&&$bakatype=='question'||$CURUSER['addbonus'] <= TIMENOW&&$bakatype=='dayper'&&!$questinguser)//未抽题
{
	$res = sql_query("SELECT id FROM bakatest where state ='同意'  $level ORDER BY RAND() LIMIT $LIMIT");//抽题
	while ($row = mysql_fetch_assoc($res))$id[] = $row[id];
	$questinguser['questionid']=join("+",$id)."+0";
	$questinguser['num']=count($id);
	sql_query("INSERT INTO  bakatestanswer  (userid,questionid, type ,num ,time) VALUES (".sqlesc($CURUSER['id']).",".sqlesc($questinguser['questionid']).",".sqlesc($bakatype).",".sqlesc(count($id)).",".sqlesc(strtotime(date("Y-m-d"))).")");
	$Cache->cache_value('user_get_baka_'.$CURUSER["id"].'_wrongbeforebouns', 0 ,3600);
	$Cache->delete_value('user_get_baka_'.$CURUSER["id"].'_wrongbefore');
		if($bakatype=='question')
		stderr('PT使用资格认证考试',"为了让您更好的使用蚂蚁PT,下载资源前先要进行一些简单的测试<br />测试答案可以到论坛相关板块 , <a href='rules.php'><b>网站规则</b></a> , <a href='faq.php'><b>常见问题</b></a> 等处查找<br />这货不是签到!<a href='".$_SERVER['PHP_SELF']."'><b>开始测试</b></a>",false);
}



if($_POST['questionid']&&$_POST["choice"]||$isskip){//验证答案

$questioncheck = @mysql_fetch_assoc(sql_query("SELECT answer FROM  bakatest  where  state ='同意' and id=".sqlesc(0+$_POST[questionid])));
if($questioncheck){
			if(is_array($_POST["choice"]))
				$answersum=array_sum($_POST["choice"]);
			else 
				$answersum=0+$_POST["choice"];
		
		if(!$answersum)stderr("出错了","没有选择选项哦,就算不会也要随便选择一个选项哦");
		$thisisright=((array_sum(explode('+',$questioncheck['answer']))==$answersum)?true:false);
		if($thisisright){
			$questinguserbefore=$questinguser['questionid'];
			$questinguser['questionid']=preg_replace('/(?<!\d)'.(0+$_POST[questionid]).'\+/','',$questinguser['questionid']);
					sql_query("UPDATE   bakatestanswer  SET   questionid  =  ".sqlesc($questinguser['questionid'])." WHERE  $wherebakatype userid=".sqlesc($CURUSER['id']));
					if($addbounsperadd&&$questinguserbefore!=$questinguser['questionid']){
						KPS("+",$addbounsperadd,$CURUSER['id']);
						$notice=array('colour' =>"green",'text' =>"回答正确,获得 $addbounsperadd 魔力值,请继续");
					}
					else $notice=array('colour' =>"green",'text' =>"回答正确,请继续");
						}else{
						$Cache->cache_value('user_get_baka_'.$CURUSER["id"].'_wrongbefore', "yes",3600);
							if($isskip){
							$questinguser['questionid']=preg_replace('/(?<!\d)'.(0+$_POST[questionid]).'\+/','',$questinguser['questionid']);
							sql_query("UPDATE   bakatestanswer  SET   questionid  =  ".sqlesc($questinguser['questionid'])." WHERE  $wherebakatype userid=".sqlesc($CURUSER['id']));
							}
							elseif($addbounsperdec){
								$notice=array('colour' =>"red",'text' =>"回答错误,失去 $addbounsperdec 魔力值,这道题还会再考一次");
								KPS("-",$addbounsperdec,$CURUSER['id']);
								$Cache->cache_value('user_get_baka_'.$CURUSER["id"].'_wrongbeforebouns', 0+$Cache->get_value('user_get_baka_'.$CURUSER["id"].'_wrongbeforebouns')+$addbounsperdec,3600);
								}
							else $notice=array('colour' =>"red",'text' =>"回答错误,这道题还会再考一次");
								}
					}
}

$questionallid=(strpos($questinguser['questionid'],'+') ? explode('+',preg_replace("/\+0$/","",$questinguser['questionid'])) : $questinguser['questionid']);
	if(!is_array($questionallid)){//答题完毕
		sql_query("Delete from bakatestanswer where $wherebakatype userid=".sqlesc($CURUSER['id']));


	if($bakatype=='dayper'){

	if($CURUSER['addbonus'] <= TIMENOW)
	{
		if(strtotime(date("Y-m-d",$CURUSER['addbonus'])) == strtotime(date("Y-m-d")))
			$CURUSER['addbonusday']++;
		 else
			$CURUSER['addbonusday']=0;
		 
		 if(get_user_class()>0)
			$addbounsper=mt_rand(10+$CURUSER['addbonusday'],10+$CURUSER['addbonusday']*2);
		else
			$addbounsper=5;
		
		
		if($thisisright){
		if($isskip){$addbounsperex='+'.$addbouns;KPS("+",$addbouns,$CURUSER['id']);}
		KPS("+",$addbouns=$addbounsper,$CURUSER['id']);
		}
		else 
		KPS("+",$addbouns,$CURUSER['id']);
		
		$yourtodayid=get_row_count("bakaperday"," where answer='正确' and ways != '换题' and daytime >=".strtotime(date("Y-m-d")));
		$yourtodayid=($yourtodayid<10&&!$Cache->get_value('user_get_baka_'.$CURUSER["id"].'_wrongbefore')&&!$ischange?((10-$yourtodayid)*20):0);
		
		if(date("s")==0)$yourtodayid=$yourtodayid+60;
		elseif(date("s")==30)$yourtodayid=$yourtodayid+30;
		if($yourtodayid){
		KPS("+",$yourtodayid,$CURUSER['id']);
		$yourtodayid='+'.$yourtodayid;
		}else
		$yourtodayid='';
		
		
		
		if($Cache->get_value('user_get_baka_'.$CURUSER["id"].'_wrongbefore')=='yes'){
		sql_query("UPDATE  bakatest SET  wrongusernum=wrongusernum+1  where id=".sqlesc(0+$_POST[questionid]));
		sql_query("INSERT INTO bakaperday (userid ,daytime ,addbonusday ,answer ,ways ,bouns ,usercomment,questionid) VALUES (".$CURUSER['id'].",".TIMENOW.",".$CURUSER['addbonusday'].",'错误',".($ischange?"'换题'":($isskip?"'不会'":"'提交'")).",".sqlesc($addbouns.$addbounsperex."-".(0+$Cache->get_value('user_get_baka_'.$CURUSER["id"].'_wrongbeforebouns')).$yourtodayid).",".sqlesc(preg_replace("/此刻心情:?/", "",$_POST[usercomment])).",".sqlesc(0+$_POST[questionid]).")");
		}else{
		sql_query("UPDATE  bakatest SET  rightusernum=rightusernum+1  where id=".sqlesc(0+$_POST[questionid]));
		sql_query("INSERT INTO bakaperday (userid ,daytime ,addbonusday ,answer ,ways ,bouns ,usercomment,questionid) VALUES (".$CURUSER['id'].",".TIMENOW.",".$CURUSER['addbonusday'].",'正确',".($ischange?"'换题'":($isskip?"'不会'":"'提交'")).",".sqlesc($addbouns.$addbounsperex."-".(0+$Cache->get_value('user_get_baka_'.$CURUSER["id"].'_wrongbeforebouns')).$yourtodayid).",".sqlesc(preg_replace("/此刻心情:?/", "",$_POST[usercomment])).",".sqlesc(0+$_POST[questionid]).")");
		}
		
		$until = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
		sql_query("UPDATE users  SET addbonus=".sqlesc($until)." , addbonusday = ".$CURUSER['addbonusday']." WHERE id = ".$CURUSER['id']);
		
		
		if(!$CURUSER['addbonusday']){
			if(!$thisisright)
			$notice=array('colour' =>"orange",'text' =>"答题错误,获得".$addbouns.$addbounsperex.$yourtodayid."点魔力值");
			else
			$notice=array('colour' =>"green",'text' =>"签到成功,获得".$addbouns.$addbounsperex.$yourtodayid."点魔力值");
			}
		else{
			if(!$thisisright)
			$notice=array('colour' =>"orange",'text' =>"答题错误,获得".$addbouns.$addbounsperex.$yourtodayid."点魔力值(连续".$CURUSER['addbonusday']."天签到)");
			else
			$notice=array('colour' =>"green",'text' =>"连续".$CURUSER['addbonusday']."天签到,获得".$addbouns.$addbounsperex.$yourtodayid."点魔力值");
			}
			
			//header('Refresh: 3; url=index.php'); 
	}else
		$notice=array('colour' =>"red",'text' =>"今天已经签过到了(已连续".$CURUSER['addbonusday']."天签到)");

	stdhead("每日签到");
		print("<p><table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" style='background: transparent;'><tr><td style='text-align: center;box-shadow: 2px 2px 5px gray;border-radius: 3px;border: none; padding: 10px; background: ".$notice[colour]."'>\n");
		print("<b><font color=\"white\">".$notice[text]."</font></b>");
		print("</td></tr></table></p><br />");
		$datetime=(strtotime($_GET[datetime])?date("Y-m-d",strtotime($_GET[datetime])):date("Y-m-d"));
		$wheredatetime="where daytime >= ".strtotime($datetime)." and daytime < ".strtotime($datetime)."+3600*24";
		print("<h1>$datetime 签到记录</h1>");
		begin_main_frame("",true);
				$rescount = mysql_fetch_assoc(sql_query("SELECT count(*) as num FROM bakaperday $wheredatetime ORDER BY  id  DESC"));
				list($pagertop, $pagerbottom, $limit) = pager(50, $rescount['num'], "?datetime=$datetime&");
				print("<table width='100%'>");

				print("<tr>
				<td class='colhead' align='center'>时间</td>
				<td class='colhead' align='center'>用户</td>
				<td class='colhead' align='center'>题目</td>
				<td class='colhead' align='center'>回答</td>
				<td class='colhead' align='center'>最后</td>
				<td class='colhead' align='center'>连续</td>
				<td class='colhead' align='center'>魔力</td>
				<td class='colhead' align='center' width=100%>此刻心情</td>
				</tr>");
					if($rescount['num']){			
			
				$res = sql_query("SELECT bakaperday.* , bakatest.question,bakatest.rightusernum,bakatest.wrongusernum FROM bakaperday LEFT JOIN bakatest ON bakaperday.questionid=bakatest.id $wheredatetime ORDER BY bakaperday .id DESC $limit");
				$bgarray=array('正确'=>'bgcolor=green','错误'=>'bgcolor=red','提交'=>'bgcolor=green','不会'=>'bgcolor=red','换题'=>'bgcolor=gray');
				$get_my_question=get_my_question();
				while ($row = mysql_fetch_assoc($res)){
				print("<tr $bgcolor>
				<td class='rowfollow nowrap' align='center'>".date("H:i:s",$row[daytime])."</td>
				<td class='rowfollow nowrap' align='center'>".get_username($row[userid])."</td>
				<td class='rowfollow nowrap' align='center' title='".($row[question])."' ".$get_my_question[$row[questionid]].">".$row[questionid]."</td>
				<td class='rowfollow nowrap' align='center' title='对:".$row[rightusernum]."/错:".$row[wrongusernum]."' ".$bgarray[$row[answer]].">".$row[answer]."</td>
				<td class='rowfollow nowrap' align='center' ".$bgarray[$row[ways]].">".$row[ways]."</td>
				<td class='rowfollow nowrap' align='center'>".$row[addbonusday]."</td>
				<td class='rowfollow nowrap' align='center' title='基础值+风险值-错误值+幸运值'>".$row[bouns]."</td>
				<td class='rowfollow' align='center' >".$row[usercomment]."</td>
				</tr>");				
													}

				}else
				print("<tr><td class='owfollow nowrap' align='center' colspan='8'>森马都没有找到</td></tr>");
				
			print("<tr><td align='left' class='colhead' colspan='7'><form method='get' action='".$_SERVER['PHP_SELF']."'><b>查询历史记录:</b><input type='text' maxlength='10' size='10' name='datetime' value='".$datetime."' /><input type='submit' value='提交' /></form></td>
			<td align='left' class='colhead'>".($CURUSER["class"]>=UC_ELITE_USER?"<a href='bakatestconfig.php'><b>让我来添加题目</b></a>":"")."</td></tr>");
				
				print("</table>");
				print($pagerbottom);
			end_main_frame();
	stdfoot();
	die();
	

	}
	
	if($bakatype=='question'){
		sql_query("UPDATE users SET showdlnotice= 0 WHERE id = ".$CURUSER["id"]);
		header('Refresh: 3; url=torrents.php'); 
		if($CURUSER['showdlnotice']>=1){
		
							
					$bonuscomment = date("Y-m-d") . " - BAKA & TEST SYSTEM - 获得" .$addbouns. "魔力值\n" .$CURUSER['bonuscomment'];
					sql_query("UPDATE users SET bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($CURUSER["id"]));


			KPS("+",$addbouns,$CURUSER['id']);
			if($CURUSER['showdlnotice']==1)stderr("答题完毕,以后还会再考一次","获得 $addbouns 魔力值,可以进行下载了");
			stderr("答题完毕","获得 $addbouns 魔力值,可以进行下载了");
			
										}
										else stderr("答题完毕","可以进行下载了");
}

}
shuffle($questionallid);
$questionid=$questionallid[0];//当前题目
$questing=@mysql_fetch_assoc(sql_query( "SELECT * FROM bakatest WHERE  state ='同意' and id= $questionid "));//根据随机数取出题目
	if(!$questing){
		sql_query("delete from bakatestanswer where $wherebakatype userid=".sqlesc($CURUSER['id']));
		stderr("题目出错","请刷新页面重新抽题");
	}
stdhead("BAKA & TEST SUMMON");

$type=strpos(preg_replace("/^0\+/","",$questing['answer']),'+')?"checkbox":"radio";//确定题目类型

for ($i=1; $i<=8; $i*=2)
if($questing["answer".$i])$choices[]="<input type='".$type."' name='choice[]' value='".$i."' >".formattest($questing["answer".$i])."<br />";//将选项存入数组
shuffle($choices); //乱序排列
//$notice=array('colour' =>"red",'text' =>"cew");
if($notice){

		print("<p><table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" style='background: transparent;'><tr><td style='box-shadow: 2px 2px 5px gray;border-radius: 3px;border: none; padding: 10px; background: ".$notice[colour]."'>\n");
		print("<b><font color=\"white\">".$notice[text]."</font></b>");
		print("</td></tr></table></p><br />");

}
if($bakatype=='question'){
if($questinguser['num']==count($questionallid))print "<h1>全员拿起笔来开始答题！</h1>";
elseif(count($questionallid)-1) print "<h1>还剩余".(count($questionallid)-1)."道题目</h1>";}
elseif($bakatype=='dayper')
print "<h1>每日签到</h1>";
$levelprint=array('[新手级]','[大师级]','[老手级]','[坑爹级]');
?>

<form action="<? echo $_SERVER['PHP_SELF']?>" method="post">
<table width="60%" border="1" cellspacing="0" cellpadding="10" <? if($bakatype=='dayper'){?>style="background-image:url('pic/questionbg.png');background-position: right bottom;background-repeat: no-repeat;"<?}?>>
<tr><td class="text"  align="left" width="100%"><?print ($questinguser['num']-count($questionallid)+1)."、".$levelprint[$questing['level']].($type=='checkbox'?'[多选]':'[单选]');?> 请问：<? echo formattest($questing["question"]); ?></td></tr>
<tr><td class="text"  style="white-space: nowrap;" align="left" width="100%">
<?/*<input type="hidden" name="showup" value="<? echo $showup ?>" />*/?>
<input type="hidden" name="questionid" value="<? echo $questionid ?>" />
<? echo $choices[0].$choices[1].$choices[2].$choices[3];?>
</td></tr>
<? 
//$questinguser['questiontip']
if($bakatype=='dayper'){
if($_POST[usercomment])$Cache->cache_value('user_get_baka_'.$CURUSER["id"].'_todayusercomment',$_POST[usercomment],600);
if(!$todayusercomment=$Cache->get_value('user_get_baka_'.$CURUSER["id"].'_todayusercomment'))$todayusercomment='此刻心情:无';
print"<tr><td class='text'  align='left' width='100%'><textarea cols='100%' rows=2 name='usercomment'>".$todayusercomment."</textarea ></td></tr>"; 
}
if($bakatype=='dayper'&&$questing['questiontip'])
print"<tr><td class='text'  align='left' width='100%'>提示：".$questing['questiontip']."</td></tr>"; 

?>
<tr><td class="text"  align="center" width="100%"><input type="submit" name="submit" value="提交" /><?
if($canchange){?><input type="submit" name="wantchange" value="仅可换一题" /><?}
if($canskip){?><input type="submit" name="wantskip" value="不会" /><?}

?>

</td></tr>
<? 

if($bakatype=='dayper')
{print"<tr><td class='text'  align='left' width='100%'>
出题人：".($questing[anonymous]=='yes'?get_username($ROBOTUSERID):get_username($questing[userid]))."  累计答对用户：".($questing[rightusernum])."  累计答错用户：".($questing[wrongusernum])."</td></tr>";
print"<tr><td class='text'  align='left' width='100%'><br />
1:不会用户,获得5魔力值<br />
2:已退学用户签到,获得10魔力值<br />
3:其他等级用户签到,随机获得(连续天数)至(连续天数*2)魔力值<br />
4:不会却回答正确额外获得5魔力值<br />
5:回答错误，将扣除1个魔力(换题情况下扣除更多)。<br />
</td></tr>";} ?>
</table></form>
<? 

stdfoot();



 
  
 