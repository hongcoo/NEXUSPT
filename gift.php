<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

$userid = 0 + $CURUSER["id"];
$USERUPDATESETADD=array();
$RESULT=array();


if($CURUSER['class'] >=UC_SYSOP){
 if($_GET["action"]=='set')
 stderr("设置(以今日00:00起计算)", "<a href='gift.php?action=on1'>[开启一天]</a><a href='gift.php?action=on2'>[开启两天]</a><a href='gift.php?action=on3'>[开启三天]</a><a href='gift.php?action=on7'>[开启一周]</a><br /><a href='gift.php?action=on14'>[开启两周]</a><a href='gift.php?action=on21'>[开启三周]</a><a href='gift.php?action=on30'>[开启一月]</a><a href='gift.php?action=off'>[关闭礼物]</a><br /><a href='gift.php?action=mytest'>[测试模式]</a>",false);
 else if($_GET["action"]=='on1'){
 $until = (strtotime(date("Y-m-d")) + 1*86400);
sql_query("UPDATE users SET gotgift='".$until."'");
 stderr("OK", "开启一天成功");
 }
  else if($_GET["action"]=='on2'){
  $until = (strtotime(date("Y-m-d")) + 2*86400);
sql_query("UPDATE users SET gotgift='".$until."'");
 stderr("OK", "开启两天成功");
 }
  else if($_GET["action"]=='on3'){
  $until = (strtotime(date("Y-m-d")) + 3*86400);
sql_query("UPDATE users SET gotgift='".$until."'");
 stderr("OK", "开启三天成功");
 }
   else if($_GET["action"]=='on7'){
  $until = (strtotime(date("Y-m-d")) + 7*86400);
sql_query("UPDATE users SET gotgift='".$until."'");
 stderr("OK", "开启一周成功");
 }
  else if($_GET["action"]=='on14'){
  $until = (strtotime(date("Y-m-d")) + 14*86400);
sql_query("UPDATE users SET gotgift='".$until."'");
 stderr("OK", "开启两周成功");
 }
   else if($_GET["action"]=='on21'){
  $until = (strtotime(date("Y-m-d")) + 21*86400);
sql_query("UPDATE users SET gotgift='".$until."'");
 stderr("OK", "开启两周成功");
 }
   else if($_GET["action"]=='on30'){
  $until = (strtotime(date("Y-m-d")) + 30*86400);
sql_query("UPDATE users SET gotgift='".$until."'");
 stderr("OK", "开启一月成功");
 }
 else if($_GET["action"]=='off'){
 sql_query("UPDATE users SET gotgift='0'");
 stderr("OK", "关闭成功");
 }  else if($_GET["action"]=='mytest'){
	$CURUSER["gotgift"] = TIMENOW+10;
	$_GET["open"]=1;
 }

 }



if ($_GET["open"]&&$CURUSER["gotgift"] > TIMENOW) {

header('Refresh: 5; url=/index.php');
sql_query("UPDATE users SET gotgift='0' WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);

$rand_no=0;//不中奖概率 n/10
$rand_vip=5;//VIP中奖概率 n/10
$rand_upload=3;//上传量中奖概率 n/10
$rand_invite=1;//邀请码中奖概率 n/10
$rand_invitenum=3;//邀请名额中奖概率 n/10
$rand_seedbonus=8;//魔力值中奖概率 n/10

$add_vip=mt_rand (11,11);//VIP增加天数
$add_upload=mt_rand (11,11);//上传量增加数
$add_invite=mt_rand (11,11);//邀请码增加数
$add_invite_outtime=mt_rand (11,11);//邀请码过期时间
$add_invitenum=mt_rand (1,1);//邀请名额增加数
$add_seedbonus=mt_rand (11111,11111);//魔力值增加数



$USERUPDATESETADD=array();
$RESULT=array();

function rand_add_gift($rand_add=10){
return ($rand_add >= mt_rand (0,10));
}


if(rand_add_gift($rand_no))stderr("没中奖耶...", "你是一名好人!咦,你想做什么?");

if($add_vip=(rand_add_gift($rand_vip) && $CURUSER["class"] < UC_VIP ? $add_vip : 0)){
$vip_until = date("Y-m-d H:i:s",(strtotime(date("Y-m-d H:i:s")) + $add_vip*86400));
$USERUPDATESETADD[]="class = '".UC_VIP." ', vip_added = 'yes', vip_until = ".sqlesc($vip_until);
$RESULT[]= $add_vip."天VIP资格";
}

if($add_upload=(rand_add_gift($rand_upload) ? $add_upload : 0)){
$USERUPDATESETADD[]="uploaded=uploaded+1024*1024*1024*".$add_upload;
$RESULT[]= $add_upload."GB上传量";
}

if($add_invite=(rand_add_gift($rand_invite) ? $add_invite : 0)){
$RESULT[]=$add_invite."个有效期为".$add_invite_outtime."天的邀请码";
$add_invite_outtime=date("Y-m-d H:i:s",(TIMENOW +($add_invite_outtime-$invite_timeout)*24*60*60));
while($add_invite--)
sql_query("INSERT INTO invites (inviter, invitee, hash, time_invited,sent) VALUES (".sqlesc($userid).", '', ".sqlesc(md5(mksecret())).", " . sqlesc($add_invite_outtime) . " ,1)");
}

if($add_invitenum=(rand_add_gift($rand_invitenum) ? $add_invitenum : 0)){
$USERUPDATESETADD[]="invites=invites+".$add_invitenum;
$RESULT[]= $add_invitenum."个邀请名额";
}

if($add_seedbonus=(rand_add_gift($rand_seedbonus) ? $add_seedbonus : 0)){
$USERUPDATESETADD[]="seedbonus=seedbonus+".$add_seedbonus;
$RESULT[]= $add_seedbonus."个魔力值";
}


if(!$USERUPDATESETADD)
stderr("没中奖耶...", "本次礼物你什么都没有抽中");

if($_GET["action"]=='mytest')stderr("测试模式", join(",", $RESULT));


if($_GET["action"]=='myon'){stderr("没中奖耶...", "本次礼物你什么都没有抽中");}
$USERUPDATESETADD[] = "bonuscomment = ".sqlesc(date("Y-m-d") . " - GIFT SYSTEM - " .join(",", $RESULT). " \n" .$CURUSER['bonuscomment']);
$USERUPDATESETADD[]="gotgift='0'";

sql_query("UPDATE users SET " . join(",", $USERUPDATESETADD) . " WHERE id = ".$userid);

stderr("标题什么的都不重要了!", "<img src=\"pic/gift.png\" style=\"float: left; padding-right:10px;\" alt=\" Gift\" title=\" Gift\" /> <h2>您获得了".join(",", $RESULT)."</h2><br />感谢您的分享和对 ".$SITENAME." 的一贯支持 ! <br /> ".$SITENAME." 祝您硬盘早日变红 ! 进入首页 5..4..3..2..1",false);


		
}
	else{
	header('Refresh: 5; url=/index.php');
	stderr("Sorry...", "您已经获得过礼物了!");
        }
   
?>