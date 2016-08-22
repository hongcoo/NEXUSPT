<?php 
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

if (get_user_class() >= UC_ADMINISTRATOR){
$wherefinduser= "";
$wheredeleteuser= " and state !='同意' ";
$whereedituser="";
$MODERATOR=TRUE;
}elseif($CURUSER["class"]>=UC_ELITE_USER){//测试部分同步
$wherefinduser= " and userid=".sqlesc($CURUSER[id])." ";
$wheredeleteuser= " and userid=".sqlesc($CURUSER[id])." and state !='同意' ";
$whereedituser=" and userid=".sqlesc($CURUSER[id])." and state !='同意' ";
$MODERATOR=FALSE;
}else stderr('没有权限', get_user_class_name(UC_ELITE_USER,true,false,true).'等级以下为观察处分者',false);

function formattest($s){
$wordf = array('[b]','[i]','[u]','[s]','[/b]','[/i]','[/u]','[/s]');
$words = array('<b>', '<i>', '<ins>','<del>','</b>', '</i>', '</ins>','</del>');
return str_replace($wordf,$words,htmlspecialchars($s));
}
	if($_POST["question"]&&$_POST["serchquestion"])$wherefinduser=$wherefinduser." and (question like'%".mysql_real_escape_string($_POST["question"])."%' or answer1 like'%".mysql_real_escape_string($_POST["question"])."%' or answer2 like'%".mysql_real_escape_string($_POST["question"])."%' or answer4 like'%".mysql_real_escape_string($_POST["question"])."%'or answer8 like'%".mysql_real_escape_string($_POST["question"])."%') ";
	elseif($_POST["question"]&&$_POST["answer"]){
	$level=min(0+$_POST['level'],3);
		if(!$_POST["questionid"])sql_query("INSERT INTO  bakatest  (userid,lasttime,question,answer1,answer2,answer4,answer8,answer,questiontip,level,anonymous) VALUES (".sqlesc($CURUSER[id])." ,".sqlesc(TIMENOW)." ,".sqlesc($_POST['question'])." , ".sqlesc($_POST['answer1'])." , ". sqlesc($_POST['answer2'])." , ". sqlesc($_POST['answer4']).",".sqlesc($_POST['answer8'])." , ".sqlesc("0+".join("+",$_POST["answer"]))." , ".sqlesc($_POST['questiontip']).", $level ,".sqlesc($_POST['anonymous']=='yes'?'yes':'no')." )");
		
			else sql_query("UPDATE  bakatest set  level = $level , lasttime=".sqlesc(TIMENOW)." ,question=".sqlesc($_POST['question'])." ,answer1=".sqlesc($_POST['answer1'])." , answer2=".sqlesc($_POST['answer2'])." ,answer4=".sqlesc($_POST['answer4'])." ,  answer8=".sqlesc($_POST['answer8'])." , answer=".sqlesc("0+".join("+",$_POST["answer"])).",questiontip=".sqlesc($_POST['questiontip']).",anonymous=".sqlesc($_POST['anonymous']=='yes'?'yes':'no')." where id=".sqlesc(0+$_POST['questionid']).$whereedituser);
	}
	elseif($_GET['questionid']&&$_GET['action']=='delete')
		sql_query("delete from bakatest where id = ".sqlesc(0+$_GET['questionid']).$wheredeleteuser);
	elseif($_GET['questionid']&&$_GET['action']=='edit'){
		$editselect=@mysql_fetch_assoc(sql_query("select * from bakatest where id = ".sqlesc(0+$_GET['questionid']).$wherefinduser));
		$editselectanswer=array_sum(explode('+',$editselect['answer']));}
	elseif($_GET['questionid']&&$_GET['action']&&$MODERATOR){
	
	if($_GET['action']=='accept')
		sql_query("UPDATE bakatest SET state='同意',lasttime=".sqlesc(TIMENOW)." WHERE id = ".sqlesc(0+$_GET['questionid']).$wherefinduser);
	elseif($_GET['action']=='refuse')
		sql_query("UPDATE bakatest SET state='拒绝',lasttime=".sqlesc(TIMENOW)." WHERE id = ".sqlesc(0+$_GET['questionid']).$wherefinduser);
	elseif($_GET['action']=='pendding')
		sql_query("UPDATE bakatest SET state='候选',lasttime=".sqlesc(TIMENOW)." WHERE id = ".sqlesc(0+$_GET['questionid']).$wherefinduser);
}

$bakateststate=@mysql_fetch_array(sql_query("select 
(select count(id)  FROM   bakatest   where state='同意' ) as accept, 
(select count(id)  FROM   bakatest   where state='拒绝' ) as refuse, 
(select count(id)  FROM   bakatest   where state='候选' ) as pendding, 
(select count(DISTINCT  userid)  FROM   bakatest  ) as usernum ,
(select count(id)  FROM   bakatest   where state='同意' and level =0 ) as level0, 
(select count(id)  FROM   bakatest   where state='同意' and level =1 ) as level1, 
(select count(id)  FROM   bakatest   where state='同意' and level =2 ) as level2,
(select count(id)  FROM   bakatest   where state='同意' and level =3 ) as level3,
(select count(id)  FROM   users   where showdlnotice = 0 ) as needed
"));
$bakateststateshow="<tr><td class='text' align='left'>题库状况:同意".$bakateststate[accept]."(新手级".$bakateststate[level0].",大师级".$bakateststate[level1].",老手级".$bakateststate[level2].",坑爹级".$bakateststate[level3]."),候选".$bakateststate[pendding].",拒绝".$bakateststate[refuse].",参与编题用户".$bakateststate[usernum]."<br/>用户状况:已答题用户".$bakateststate[needed]."</td></tr>";
stdhead("BAKA&TEST系统管理");
?>
 <?php
begin_main_frame("",true);


?>
<table width=700px align="center" style="background-image:url('pic/questionbg.png');background-position: right bottom;background-repeat: no-repeat;"><form action="<? echo $_SERVER['PHP_SELF'] ?>" method="post">

<tr><td class="colhead" align="center">添加测试题目</td></tr>
<tr><td class="text" align="left"> 
题目<input type="text" name="question" size="100" value="<? echo $editselect['question'];?>"><input type="hidden" name="questionid" value="<? echo $editselect['id']; ?>"><br />
选项<input type="text" name="answer1" size="90" value="<? echo $editselect['answer1']; ?>"><input type="checkbox" name="answer[]" value="1" <?echo (($editselectanswer/1)%2==1?"checked":"");?>>对<br />
选项<input type="text" name="answer2" size="90" value="<? echo $editselect['answer2']; ?>"><input type="checkbox" name="answer[]" value="2" <?echo (($editselectanswer/2)%2==1?"checked":"");?>>对<br />
选项<input type="text" name="answer4" size="90" value="<? echo $editselect['answer4']; ?>"><input type="checkbox" name="answer[]" value="4" <?echo (($editselectanswer/4)%2==1?"checked":"");?>>对<br />
选项<input type="text" name="answer8" size="90" value="<? echo $editselect['answer8']; ?>"><input type="checkbox" name="answer[]" value="8" <?echo (($editselectanswer/8)%2==1?"checked":"");?>>对<br />
提示<input type="text" name="questiontip" size="90" value="<? echo $editselect['questiontip'];?>"><input type="checkbox" name="anonymous" value="yes" <? echo ($editselect['anonymous']=='yes'?'checked':''); ?>>匿名<br />
</td> </tr>
<tr><td class="rowfollow" align="center">
<select name="level">
<option value=3 <?echo ($editselect['level']==3?"selected=\"selected\"":"")?>>坑爹级</option>
<?if($MODERATOR){?>
<option value=2 <?echo ($editselect['level']==2?"selected=\"selected\"":"")?>>老手级</option>
<option value=1 <?echo ($editselect['level']==1?"selected=\"selected\"":"")?>>大师级</option>
<option value=0 <?echo ($editselect&&$editselect['level']==0?"selected=\"selected\"":"")?>>新手级</option>
<?}?>
</select>
<input type="submit"  value="添加" /><input type="submit"  name="serchquestion" value="搜索" /></td></tr></form>
<tr><td class="text" align="left">
1、BAKA&TEST系统在答题时会自动识别,因此不需要说明题目类型(单选、多选);<br/>
2、BAKA&TEST系统自动将捣乱/秀<del>吉</del>下限者进行警告或者封号;<br/>
3、任何人无法删除BAKA&TEST系统中已同意的问题;<br/>
4、任何人添加时请先回忆起高中所学习的语文知识;<br/>
5、仅支持使用[b][i][u][s]这四个标签;<br/>
6.1、新手级用于新注册用户;<br/>
6.2、大师级用于一年生上用户;<br/>
6.3、老手级、坑爹级用于每日签到;<br/>
7、提示信息仅在每日签到的时候显示</br>
8、只统计每日签到正确错误的数目</br>
9、如果有建议或者意见请到论坛进行反馈;<br/>
</td></tr>
<? echo $bakateststateshow;?>
</table>
<?php
end_main_frame();


if($_GET['type']=='accept')$wheretype=" and state='同意' ";
elseif($_GET['type']=='pendding')$wheretype=" and state='候选' ";
elseif($_GET['type']=='refuse')$wheretype=" and state='拒绝' ";
elseif($_GET['type']=='level0')$wheretype=" and level=0 ";
elseif($_GET['type']=='level1')$wheretype=" and level=1 ";
elseif($_GET['type']=='level2')$wheretype=" and level=2 ";
elseif($_GET['type']=='level3')$wheretype=" and level=3 ";
else $wheretype='';
 
$rescount = mysql_fetch_assoc(sql_query("SELECT count(*) as num FROM bakatest where 1 $wherefinduser $wheretype  ORDER BY  id  DESC"));

list($pagertop, $pagerbottom, $limit) = pager(20, $rescount['num'], "?type=".$_GET['type']."&");

print("
<p><a href='?type=all'><b>全部</b></a> | 
<a href='?type=accept'><b>同意</b></a> | 
<a href='?type=level0'><b>新手级</b></a> | 
<a href='?type=level1'><b>大师级</b></a> | 
<a href='?type=level2'><b>老手级</b></a> | 
<a href='?type=level3'><b>坑爹级</b></a> | 
<a href='?type=pendding'><b>候选</b></a> | 
<a href='?type=refuse'><b>拒绝</b></a><br />");

begin_main_frame("",true);
print($pagertop);
print("<table width='100%'>");
if($rescount['num']){
print("<tr><td class='colhead' align='center'>ID</td>
<td class='colhead' align='center'>难度</td>
<td class='colhead' align='center'>答对<br />答错</td>
<td class='colhead' align='center'>题目</td>
<td class='colhead' align='center'>选项</td>
<td class='colhead' align='center'>答案</td>
<td class='colhead' align='center'>用户</td>
<td class='colhead' align='center'>状态</td>
<td class='colhead' align='center'>操作</td></tr>");

$res = sql_query("SELECT * FROM bakatest where 1 $wherefinduser $wheretype  ORDER BY  id  DESC $limit ");
$levelarray=array('新手','大师','老手','坑爹');
$levelbgarray=array('bgcolor=PaleGreen','bgcolor=DeepSkyBlue','bgcolor=Pink','bgcolor=Gray');
while ($row = mysql_fetch_assoc($res)){
if($row[state]=='同意')$bgcolor='bgcolor=PaleGreen';
elseif($row[state]=='拒绝')$bgcolor='bgcolor=Wheat';
else $bgcolor='bgcolor=DeepSkyBlue';

if($row[state] !='同意'&&!$MODERATOR)
$delete="<a href='".$_SERVER['PHP_SELF']."?questionid=".$row[id]."&action=edit&type=".$_GET['type']."'><b>修改</b></a><br /><a href='".$_SERVER['PHP_SELF']."?questionid=".$row[id]."&action=delete&type=".$_GET['type']."'><b>删除</b></a>";
elseif($MODERATOR&&$row[state] =='拒绝') $delete="<a href='".$_SERVER['PHP_SELF']."?questionid=".$row[id]."&action=delete&type=".$_GET['type']."'><b>删除</b></a>";
else $delete='';

if($MODERATOR)
$MODERATORACTION="
<a href='".$_SERVER['PHP_SELF']."?questionid=".$row[id]."&action=accept&type=".$_GET['type']."'><b>同意</b></a><br />
<a href='".$_SERVER['PHP_SELF']."?questionid=".$row[id]."&action=pendding&type=".$_GET['type']."'><b>候选</b></a><br />
<a href='".$_SERVER['PHP_SELF']."?questionid=".$row[id]."&action=edit&type=".$_GET['type']."'><b>修改</b></a><br />
<a href='".$_SERVER['PHP_SELF']."?questionid=".$row[id]."&action=refuse&type=".$_GET['type']."'><b>拒绝</b></a><br />";
else $MODERATORACTION="";
if(!$delete&&!$MODERATORACTION)$delete='-';

print("<tr $bgcolor>
<td class='rowfollow' align='center' ".$levelbgarray[$row[level]].">".$row[id]."</td>
<td class='rowfollow' align='center' ".$levelbgarray[$row[level]].">".$levelarray[$row[level]]."</td>
<td class='rowfollow' align='center' ".$levelbgarray[$row[level]].">".$row[rightusernum]."<br />".$row[wrongusernum]."</td>
<td class='rowfollow' align='center' ".$levelbgarray[$row[level]].">".formattest($row[question])."</td>
<td class='rowfollow' align='left'>1:".formattest($row[answer1])."<br />2:".formattest($row[answer2])."<br />4:".formattest($row[answer4])."<br />8:".formattest($row[answer8]).($row[questiontip]?"<br /><b>提示:</b>".formattest($row[questiontip]):"")."</td>
<td class='rowfollow' align='center'>".$row[answer]."</td>
<td class='rowfollow' align='center'>".get_username($row[userid]).($row[anonymous]=='yes'?"<br /><b>匿名</b>":"")."</td>
<td class='rowfollow' align='center'>".$row[state]."</td>
<td class='rowfollow' align='center'>".$MODERATORACTION.$delete."</a></td>
</tr>");

}
}else
print("<tr><td class='colhead' align='center'>森马都没有找到</td></tr>");


print("</table>");
print($pagerbottom);
end_main_frame();
 
stdfoot();
?>

 
  
 