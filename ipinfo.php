<?php 
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
$showipx=false;
if(!($ip=str_replace(array('[',']',' '),'', $_POST["ip"]))&&isset($_GET["guestid"])){
$row =@mysql_fetch_assoc(sql_query("SELECT trueuserid,ip FROM shoutbox WHERE userid =0 and id =".sqlesc(0+$_GET['guestid'])));
preg_match( "/^([0-9a-f]{1,4}(\.|\:)){2}[0-9a-f]{0,4}(:[0-9a-f]{0,4}:|\.)?/i",$row["ip"], $ipresult);
if(!$ip=$ipresult[0])stderr('错误','用户信息不存在或不正确');
$showipx=true;
$trueuserid=0+$row["trueuserid"];
}

if(!$ip||get_user_class() >= $sbmanage_class){
if(!$ip)$ip=getip();
preg_match( "/^([0-9a-f]{1,4}(\.|\:)){2}[0-9a-f]{0,4}(:[0-9a-f]{0,4}:|\.)?/i",$ip, $ipresult);
if(!$ip=$ipresult[0])stderr('错误','IP地址不正确或者太短(IPV4需要三节,IPV6需要四节)');
/*$showuser = sql_query("
SELECT DISTINCT (userid) ,max(access) as access FROM(
(SELECT DISTINCT (userid) ,max(access) as access FROM iplog where ip LIKE '$ip%' GROUP BY userid ORDER BY max(access)  DESC LIMIT 5)
UNION
(SELECT   id as userid ,last_access   as access FROM users where ip LIKE '$ip%'   ORDER BY last_access  DESC LIMIT 5)
) as a GROUP BY a.userid ORDER BY max(a.access)  DESC LIMIT 5
") ;*/
//$showuser = sql_query("SELECT DISTINCT (userid) ,max(access) as access FROM iplog where ip LIKE '$ip%' GROUP BY userid ORDER BY max(access)  DESC LIMIT 5");
//$showuser = sql_query("SELECT DISTINCT (l.userid) AS userid, max(l.access) AS access FROM (( SELECT DISTINCT (userid), max(access) AS access FROM iplog WHERE ip LIKE '$ip%' GROUP BY userid ORDER BY max(access) DESC LIMIT 5 ) UNION ( SELECT users.id, last_access FROM users WHERE ip LIKE '$ip%' LIMIT 5 )) AS l GROUP BY userid ORDER BY l.access DESC LIMIT 5");

$showuser = sql_query("SELECT * FROM (( SELECT userid, access AS last_access FROM iplog WHERE ip LIKE '$ip%' ORDER BY access DESC LIMIT 30 ) UNION ( SELECT users.id AS userid, last_access FROM users WHERE ip LIKE '$ip%' ORDER BY last_access DESC LIMIT 5 )) AS L ORDER BY L.last_access DESC");
$showipx=true;
}

preg_match( "/^([0-9a-f]{1,4}(\.|\:)){2}([0-9]{1,4}|[0-9a-f]{0,4}:[0-9a-f]{0,4})[0-9a-f:\.]*/i",$ip, $ipresult);
if(!$ip=$ipresult[0])stderr('错误','IP地址不正确或者太短(IPV4需要三节,IPV6需要四节)');

if(!preg_match( "/^([0-9a-f]{1,4}\:){3}([0-9a-f]{0,4}\:)+|^([0-9]{1,3}\.){3}/i",$ip)){
$ip=$ip.(strpos($ip,':')?":":".");
$showipx=true;
}


stdhead("IP信息");


?>
<table width="300px">
<tr>
<td class="text" align="center" COLSPAN="2">
<? print convertipv6($ip,false)." ".(strpos($ip,':')?"IPV6":"IPV4")."<br /><br />".$ip.($showipx?"X":"");?></td>
</tr>
<?/*
$alluser=get_single_value('iplog','COUNT(DISTINCT userid)',"WHERE ip LIKE '$ip%'");
$onlineuser=get_single_value('users','COUNT(DISTINCT id)',"WHERE ip LIKE '$ip%'");
$peeruser=get_single_value('peers','COUNT(DISTINCT userid)',"WHERE ip LIKE '$ip%'");
$deleteuser=get_single_value('iplog','COUNT(DISTINCT userid)',"WHERE userid not in (SELECT DISTINCT id FROM users) and ip LIKE '$ip%' ");
$nouseuser=get_single_value('users','COUNT(DISTINCT users.id)',"left join iplog on userid=users.id where (leechwarn ='yes' or parked ='yes' or enabled='no') and iplog.ip like '$ip%' ");

<tr>
<td class="text" align="center" COLSPAN="2"><? 
print "该IP段存在历史用户：".$alluser."<br />";
print "最后访问该IP段用户：".$onlineuser."<br />";
print "当前该IP段做种用户：".$peeruser."<br />";
print "该IP段上被删除用户：".$deleteuser."<br />";
print "封存,禁止,警告用户：".$nouseuser."<br />";
?></td></tr>
<?*/
if($trueuserid){?>
<tr>
<td class="colhead" align="center" COLSPAN="2">该游客用户名</td></tr>
<?
print "<tr><td class='rowfollow' align='center' COLSPAN=2>".get_username($trueuserid)."</td></tr>";
}

if($showuser){?>
<tr>
<td class="colhead" align="center" COLSPAN="2">⑨位最近访问该IP段的用户与时间</td></tr>
<?
if(!mysql_num_rows($showuser))print "<tr><td class='rowfollow' align='center' COLSPAN=2>暂无信息</td></tr>";
while ($row = mysql_fetch_array($showuser)){
if(!$useridarray[$row['userid']]){
$useridarray[$row['userid']]=$row['last_access'];
print "<tr><td class='rowfollow' align='left'>".get_username($row['userid'])."</td><td class='rowfollow' align='center'>".($row['last_access'])."</td></tr>";
}
if(count($useridarray)>=9)break;
}

}//else
if(isset($_GET["guestid"]))
{
?><td class="colhead" align="center" COLSPAN="2"><a href='<? echo $_SERVER['PHP_SELF'] ?>' >点击查看自己IP段信息</a></td></tr><?
}else{
?>
<tr><td class="colhead" align="center" COLSPAN="2">下方可输入IP进行查询</td></tr>
<tr>
<td class="text" align="center" COLSPAN="2"> 
<form action="<? echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="text" name="ip" size="50"></form>
</td>
</tr>
<?}


?>


</table>
<?php

stdfoot();
?>

 
  
 