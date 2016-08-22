<?php
require_once("include/bittorrent.php");
dbconn();
require_once($rootpath . 'include/cleanup.php');
docleanup();

if(!$Cache->get_value('imdbdoubanautoupdate_continue_at_wap')){
$Cache->cache_value('imdbdoubanautoupdate_continue_at_wap','notmore', 600);
imdbdoubanautoupdate();
}

$return=array('state'=>0,'body'=>'授权失败?请重新绑定蚂蚁账号');
$res = sql_query("SELECT * FROM users WHERE id = " . sqlesc(0+$_GET['uid']));
$CURUSER=$row = mysql_fetch_array($res);
if ($row&&$row["passhash"] == md5($row["secret"] . $_GET['pass'] . $row["secret"])){
$return['state']=1;

switch($_GET['act']){
		case 'info':{$return['body']=pt_info($row['id']);break;}
		case 'bakatest':{$return['body']=pt_qd($row['id']);break;}
		}
}
$return['body']=strip_tags($return['body']);
print json_encode($return);die;


function pt_info($uid){
global $Cache,$CURUSER;
	if (!$return = $Cache->get_value('pt_info_user_'.$CURUSER["id"])){
	
	$activeseed = number_format(get_single_value("peers","COUNT(DISTINCT(torrent))","WHERE userid=" . sqlesc($CURUSER["id"]) . " AND seeder='yes'"));
	$activeleech = number_format(get_single_value("peers","COUNT(DISTINCT(torrent))","WHERE userid=" . sqlesc($CURUSER["id"]) . " AND seeder='no'"));
	$invite_have = (get_row_count("invites", "WHERE inviter=".$CURUSER['id']))."/".(0+$CURUSER['invites']);
	$hr_have = number_format(0+get_row_count("snatched", "WHERE userid=".$CURUSER['id']." and hr='A' AND finished='yes'")).'/'.number_format(0+get_row_count("snatched", "WHERE userid=".$CURUSER['id']." and hr='C' AND finished='yes' "));
	$return="用户ID:{$CURUSER['id']}\n用户名:{$CURUSER['username']}\n上传量:".mksize($CURUSER['uploaded'], 1)."\n下载量:".mksize($CURUSER['downloaded'])."\n分享率:".number_format(get_ratio($CURUSER['id'],false),2)."\n魔力值:".number_format($CURUSER['seedbonus'], 1)."\n当前做种:{$activeseed}\n当前下载:{$activeleech}\n邀请码:{$invite_have}\nH&R:{$hr_have}\n";
	$Cache->cache_value('pt_info_user_'.$CURUSER["id"], $return, 120);
	}
	return  $return;
}

function pt_qd($uid){
global $Cache,$CURUSER;

if($CURUSER['addbonus'] <= TIMENOW)
	{
		if(strtotime(date("Y-m-d",$CURUSER['addbonus'])) == strtotime(date("Y-m-d")))
			$CURUSER['addbonusday']++;
		 else
			$CURUSER['addbonusday']=0;
		 

		$addbounsper=mt_rand($CURUSER['addbonusday']*2+10,10+$CURUSER['addbonusday']*5);
		KPS("+",$addbounsper,$CURUSER['id']);
		
		$until = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
		sql_query("UPDATE users SET addbonus=".sqlesc($until)." , addbonusday = ".$CURUSER['addbonusday']." WHERE id = ".$CURUSER['id']);
		
		sql_query("INSERT INTO bakaperday (userid ,daytime ,addbonusday ,answer ,ways ,bouns ,usercomment,questionid) VALUES (".$CURUSER['id'].",".TIMENOW.",".$CURUSER['addbonusday'].",'正确','换题',".sqlesc($addbounsper).",".sqlesc('微信签到').",'1')");
		
		$return="签到成功\n连续签到{$CURUSER['addbonusday']}天\n获得魔力值$addbounsper";
	}else
	$return="您已签到\n连续签到{$CURUSER['addbonusday']}天";		
	return  $return;
}

die;