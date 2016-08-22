<?php
//function_exists('gzencode')//20120906
require_once('include/bittorrent_announce.php');
require_once('include/benc.php');
dbconn_announce();
//1. BLOCK ACCESS WITH WEB BROWSERS AND CHEATS!
$agent = $_SERVER["HTTP_USER_AGENT"];
block_browser();
//2. GET ANNOUNCE VARIABLES
// get string type passkey, info_hash, peer_id, event, ip from client
foreach (array("passkey","info_hash","peer_id","event") as $x)
{
	if(isset($_GET["$x"]))
	$GLOBALS[$x] = $_GET[$x];
}
// get integer type port, downloaded, uploaded, left from client
foreach (array("port","downloaded","uploaded","left","compact","no_peer_id") as $x)
{
	$GLOBALS[$x] = 0 + $_GET[$x];
}
//check info_hash, peer_id and passkey
foreach (array("passkey","info_hash","peer_id","port","downloaded","uploaded","left") as $x)
	if (!isset($x)) err("丢失参数: $x");
foreach (array("info_hash","peer_id") as $x)
	if (strlen($GLOBALS[$x]) != 20) err("错误的 $x (" . strlen($GLOBALS[$x]) . " - " . rawurlencode($GLOBALS[$x]) . ")");
if (strlen($passkey) != 32)err("错误的 passkey(" . strlen($passkey) . " - $passkey)");

$info_hash=pack("H*",bin2hex($info_hash));


//4. GET IP AND CHECK PORT

/* if (preg_match("/6/i", $_SERVER["HTTP_HOST"])){
	if(!ip2long(getip()))
		$ip = getip();
	elseif(preg_match( '/2001:da8:/i',$_GET['ipv6'])||preg_match( '/2001:250:/i',$_GET['ipv6']))
		$ip = $_GET['ipv6'];
	elseif(preg_match( '/2001:da8:/i',$_GET['ip'])||preg_match( '/2001:250:/i',$_GET['ip']))
		$ip = $_GET['ip'];
	else
		err("没有找到有效IPV6地址");//$only_IPV6 ="";
}else */
$ip = getip();	// avoid to get the spoof ip from some agent
//TODO 添加了注释


if (!$port || $port > 0xffff)
	err("错误的端口");
if (!ip2long($ip)) //Disable compact announce with IPv6
{
$compact = 0;
}
else
{
$compact = 1;
}

$canipv4=false;

// check port and connectable
if (portblacklisted($port))
	err(" $port 端口是禁止的.");
	
	
	
	

	

	
	
	

//5. GET PEER LIST
// Number of peers that the client would like to receive from the tracker.This value is permitted to be zero. If omitted, typically defaults to 50 peers.
 $rsize = 20;
 
foreach(array("numwant", "num want", "num_want") as $k)
{
	if (isset($_GET[$k]))
	{
		$rsize = 0 + $_GET[$k];
		break;
	}
}
//if($rsize>20)$rsize=20;
	
// set if seeder based on left field
$seeder = ($left == 0) ? "yes" : "no";

// check passkey
if (!$az = $Cache->get_value('user_passkey_'.$passkey.'_content')){
	$res = sql_query("SELECT id, downloadpos, enabled, uploaded, downloaded, class, parked, clientselect, showclienterror, MODEMAX, hrwarned FROM users WHERE passkey=". sqlesc($passkey)." LIMIT 1");
	$az = mysql_fetch_array($res);
	$Cache->cache_value('user_passkey_'.$passkey.'_content', $az, 600);
}

$global_hr_hit=$az["hrwarned"];

if (!$az) err("错误的 passkey! 请重新下载种子");
$userid = 0+$az['id'];

announceipcheck($ip,$userid);
//3. CHECK IF CLIENT IS ALLOWED

$clicheck_res = check_client($peer_id,$agent);
if($clicheck_res){
	if ($az['showclienterror'] == 'no')
	{
		sql_query("UPDATE users SET showclienterror = 'yes' WHERE id = ".sqlesc($userid));
		$Cache->delete_value('user_passkey_'.$passkey.'_content');
		$USERUPDATESET[] = "showclienterror = 'yes'";
	//write_log("CLIENT_NEW_ERROR_AGENT:".$agent.";CLIENT_NEW_ERROR_IP/USER:".$ip."/".$userid.",CLIENT_NEW_ERROR_PEER:".$peer_id,"mod");
	sql_query("INSERT INTO chenzhuyudubug (userid,ip,page) VALUES (".sqlesc($userid).", ".sqlesc(getip()).", ".sqlesc($agent."///".$peer_id).")");	
	}

	err($clicheck_res);
}
/*elseif ($az['showclienterror'] == 'yes'){
	$USERUPDATESET[] = "showclienterror = 'no'";
	$Cache->delete_value('user_passkey_'.$passkey.'_content');
}*/




// check torrent based on info_hash
if (!$torrent = $Cache->get_value('torrent_hash_'.bin2hex($info_hash).'_content')){
	$res = sql_query("SELECT id, owner, sp_state, audiocodec ,pos_state ,seeders, leechers,times_completed , UNIX_TIMESTAMP(added) AS ts, banned ,nobuymoney FROM torrents WHERE " . hash_where("info_hash", $info_hash));
	$torrent = mysql_fetch_array($res);
	//$Cache->cache_value('torrent_hash_'.$info_hash.'_content', $torrent, 350);
	$Cache->cache_value('torrent_hash_'.bin2hex($info_hash).'_content', $torrent, 120);
}
if (!$torrent) err("该种子还未上传到服务器");
elseif ($torrent['banned'] == 'yes' &&!$torrent["leechers"]) err("禁止的种子");
//elseif ($torrent['banned'] == 'yes' && $az['class'] < $seebanned_class&&!$torrent["leechers"]) err("禁止的种子");
// select peers info from peers table for this torrent
$torrentid = $torrent["id"];
$numpeers = $torrent["seeders"]+$torrent["leechers"];



if ($seeder == 'yes'){ //Don't report seeds to other seeders
	$only_leech_query = " AND (seeder = 'no' or userid = $userid ) ";
	$newnumpeers = $torrent["leechers"];
}

else{
	$only_leech_query = "";
	$newnumpeers = $numpeers;
}	

if ($newnumpeers > $rsize)
	$limit = " ORDER BY  seeder , RAND() LIMIT $rsize";
else $limit = " LIMIT $rsize ";





if (!$compact&&!preg_match( '/:0:5efe:|:200:5efe:/i',$ip)&&!preg_match( '/error/i',$ip))
$iptype=6;
elseif (!$compact)
$iptype=5;
else
$iptype=4;




$MODEMAX=$az['MODEMAX'];
if($MODEMAX<6&&$iptype==6){
 $USERUPDATESET[] = "MODEMAX = 6";
 $MODEMAX = 6;
 $Cache->delete_value('user_passkey_'.$passkey.'_content');
}
elseif($MODEMAX!=5&&$iptype==5) {
 $USERUPDATESET[] = "MODEMAX = 5";
 $MODEMAX = 5;
 $Cache->delete_value('user_passkey_'.$passkey.'_content');
}
elseif($az['MODEMAX']<4){
 $USERUPDATESET[] = "MODEMAX = 4";
 $MODEMAX = 4;
 $Cache->delete_value('user_passkey_'.$passkey.'_content');
}


 
if($iptype==6) {
	$connectable = "yes";
	$ipenable=1;
	$only_IPV6 = " AND ((iptype =  6 ) or  (iptype =  5) or  (iptype =  4 and connectable = 'yes'))";
}elseif($iptype==5) {
	$only_IPV6 = " AND ((iptype =  6 ) or  (iptype =  5) or  (iptype =  4 and connectable = 'yes'))";
	$connectable = "yes";
	$ipenable=1;
}elseif($iptype==4) {
if(get_single_value_useriptype("ipv6", "WHERE userid = $userid and torrent= $torrentid",$MODEMAX)==='0'||$MODEMAX==4){
	$only_IPV6 = " AND iptype = 4 ";//and userid > 0 ";
	$connectable = "yes";
	$ipenable=1;
}else{	
	$only_IPV6 = " AND ((iptype =  6 ) or  (iptype =  5 )  or  (iptype =  4 and connectable = 'yes'))";
	$limit = " ORDER BY RAND() LIMIT 0";
	$connectable = "no";
	$ipenable=0;
	}
}

	if($iptype==4)
		$only_IPV6 =" AND iptype = 4";// and userid > 0 ";
	else{		
		if (!$Cache->get_value($userid.'_'.$torrentid.'_peer_first_20130605'))
			$Cache->cache_value($userid.'_'.$torrentid.'_peer_first_20130605', true, 3600*24);
		else 
			$only_IPV6 ="";
		}
		
		//$only_IPV6 ="";
/*
$banned_by_time=!(date('G')<21);
$banned_by_time_ipv4=($banned_by_time&&ip2long($ip));
if($torrent['owner'] == $userid||$az["class"] > UC_VIP||$torrent['pos_state']=='sticky'||$torrent['nobuymoney']=='no'||$torrent["seeders"] <= 2)$banned_by_time_ipv4=$only_IPV6 ="";
elseif($banned_by_time_ipv4){$only_IPV6 =" and (isipv4=0 or userid ={$torrent['owner']})";$connectable = "no";}
elseif($banned_by_time)$only_IPV6 =" and (isipv4=0 or userid ={$torrent['owner']} )";
else $only_IPV6 ="";


$banned_by_time=(date('G')>=21)&&$az["class"] <= UC_VIP&&$torrent['owner'] != $userid&&$torrent['pos_state']!='sticky'&&$torrent['nobuymoney']!='no';

if($banned_by_time&&$seeder == 'no')err("网站21:00-00:00关闭下载功能");
else

$banned_by_time=(date('G')>=24)&&$az["class"] <= UC_VIP&&$torrent['owner'] != $userid;
if($banned_by_time)$only_IPV6 =$only_IPV6." AND userid = $userid ";
*/


if ($anninterthreeage && ($anninterthree > $announce_wait) && (TIMENOW - $torrent['ts']) >= ($anninterthreeage * 86400))
$real_annnounce_interval = $anninterthree;
elseif ($annintertwoage && ($annintertwo > $announce_wait) && (TIMENOW - $torrent['ts']) >= ($annintertwoage * 86400))
$real_annnounce_interval = $annintertwo;
else
$real_annnounce_interval = $announce_interval;


//$real_annnounce_interval=$real_annnounce_interval+mt_rand (0,600);

$real_annnounce_interval_dead=TIMENOW+$real_annnounce_interval+600;

 
if (!ip2long($ip))
sql_query("INSERT INTO useriptype (userid,torrent, ipv6 ) VALUES (" . $az['id'] . ",$torrentid ," .  $real_annnounce_interval_dead. " )   ON DUPLICATE KEY update ipv6=values(ipv6)");
else 
sql_query("INSERT INTO useriptype (userid,torrent, ipv4 ) VALUES (" . $az['id'] . ",$torrentid ," . $real_annnounce_interval_dead. " )   ON DUPLICATE KEY update ipv4=values(ipv4)");





if($torrent['owner'] == $userid||$userid==$MASTERUSERID)$announce_wait = 60;
else $announce_wait = 90;

$fields = "seeder, peer_id,ip, port, uploaded, downloaded, (".TIMENOW." - UNIX_TIMESTAMP(last_action) + 1 ) AS announcetime, UNIX_TIMESTAMP(prev_action) AS prevts, isipv4";


$peerlistsql = "SELECT ".$fields." FROM peers WHERE torrent = ".$torrentid.$only_IPV6.$only_leech_query.$limit;

$res = sql_query($peerlistsql);





if (!$ipenable)  //IPV6通过IPV4
	{
	$resp = "d" . benc_str("interval") . "i" . $real_annnounce_interval . "e" . benc_str("min interval") . "i" . $announce_wait . "e". benc_str("complete") . "i0e" . benc_str("incomplete") . "i0e"  . benc_str("downloaded") . "i0e". benc_str("peers");
	}
	else{
	
	$resp = "d" . benc_str("interval") . "i" . $real_annnounce_interval . "e" . benc_str("min interval") . "i" . $announce_wait . "e". benc_str("complete") . "i" . $torrent["seeders"] . "e" . benc_str("incomplete") . "i" . $torrent["leechers"] . "e" . benc_str("downloaded") . "i" . ($torrent["times_completed"]?$torrent["times_completed"]:1) . "e". benc_str("peers");
}
//$resp = "d" . benc_str("interval") . "i" . $real_annnounce_interval . "e" . benc_str("min interval") . "i" . $announce_wait . "e". benc_str("complete") . "i" . $torrent["seeders"] . "e" . benc_str("incomplete") . "i" . $torrent["leechers"] . "e" . benc_str("peers");


$peer_list = "";
unset($self);
// bencoding the peers info get for this announce
while ($row = mysql_fetch_assoc($res))
{
	$row["peer_id"] = hash_pad($row["peer_id"]);

	// $peer_id is the announcer's peer_id while $row["peer_id"] is randomly selected from the peers table
	if ($row["peer_id"] === $peer_id&&$row["isipv4"] == $compact)
	{	
		$self = $row;
		continue;
	}

if ($compact == 1&&$canipv4){
	$longip = ip2long($row['ip']);
	if ($longip) //Ignore ipv6 address
		$peer_list .= pack("Nn", sprintf("%d",$longip), $row['port']);
}
elseif ($no_peer_id == 1)
	$peer_list .= "d" .
	benc_str("ip") . benc_str($row["ip"]) .
	benc_str("port") . "i" . $row["port"] . "e" .
	"e";
else
	$peer_list .= "d" .
	benc_str("ip") . benc_str($row["ip"]) .
	benc_str("peer id") . benc_str($row["peer_id"]) .
	benc_str("port") . "i" . $row["port"] . "e" .
	"e";
}
if ($compact == 1&&$canipv4)
$resp .= benc_str($peer_list);
else
$resp .= "l".$peer_list."e";

$resp .= "e";
$selfwhere = "torrent = $torrentid AND isipv4 = $compact and agent = ".sqlesc($agent)." and ( " . hash_where("peer_id", $peer_id). " or  port = ".sqlesc($port)."  or ip = ".sqlesc($ip)."  ) AND " . hash_where("passkey", $passkey);
//$selfwhere = "torrent = $torrentid  and " .hash_where("ip", $ip). "AND" . hash_where("passkey", $passkey);

//no found in the above random selection
if (!isset($self))
{
	$res = sql_query("SELECT $fields FROM peers WHERE $selfwhere LIMIT 1");
	$row = mysql_fetch_assoc($res);
	if ($row)
	{
		$self = $row;
	}
}

if(isset($self))$self[announcetime]=abs($self[announcetime]);

// min announce time
if(isset($self) && $self['prevts'] > (TIMENOW - $announce_wait))
	err('最小通信时间为 ' . $announce_wait . ' 秒');

// current peer_id, or you could say session with tracker not found in table peers
if (!isset($self))
{

$valid = @mysql_fetch_row(@sql_query("SELECT COUNT(*) FROM peers WHERE torrent = $torrentid AND isipv4 = $compact AND userid=" . sqlesc($userid)));
if ($valid[0] >= 1 && $seeder == 'no') err("清理冗余种子吧!你下载该资源的地点超过一处");	
if ($valid[0] >= 3 && $seeder == 'yes') err("清理冗余种子吧!你上传该资源的地点超过三处");

	if ($az["enabled"] == "no")
	err("你的账户被禁用");
	elseif ($az["parked"] == "yes")
	err("你的账户已冻结");
	elseif ($az["downloadpos"] == "no")
	err("你的账户禁止下载");

	if ($az["class"] < UC_VIP)
	{
		$ratio = (($az["downloaded"] > 0) ? ($az["uploaded"] / $az["downloaded"]) : 1);
		$gigs = $az["downloaded"] / (1024*1024*1024);
		if ($waitsystem == "yes")
		{
			if($gigs > 10)
			{
				$elapsed = strtotime(date("Y-m-d H:i:s")) - $torrent["ts"];
				if ($ratio < 0.4) $wait = 24;
				elseif ($ratio < 0.5) $wait = 12;
				elseif ($ratio < 0.6) $wait = 6;
				elseif ($ratio < 0.8) $wait = 3;
				else $wait = 0;

				if ($elapsed < $wait)
				err("Your ratio is too low! You need to wait " . mkprettytime($wait * 3600 - $elapsed) . " to start, please read $BASEURL/faq.php#id46 for details");
			}
		}
		if ($maxdlsystem == "yes")
		{
			if($gigs > 10)
			if ($ratio < 0.95) $max = 1;
			elseif ($ratio < 1.95) $max = 2;
			elseif ($ratio < 2.95) $max = 3;
			elseif ($ratio < 3.95) $max = 4;
			else $max = 9999;
			
			if($maxdlsystem_time)$max = min($az["class"]/2+1.5,$max);
			
			if ($max > 0 && $max < 1000 && $seeder == 'no')
			{
				$res = sql_query("SELECT COUNT(DISTINCT(torrent)) AS num FROM peers WHERE userid='$userid' AND seeder='no' and torrent != $torrentid") or err("Tracker error 5");
				$row = mysql_fetch_assoc($res);
				if ($row['num'] >= $max) err("由于最大下载数限制限制! 你最多同时下载 $max 个资源");
			}
		}
	}
}
else // continue an existing session
{
	$upthis = $trueupthis = max(0, $uploaded - $self["uploaded"]);
	$downthis = $truedownthis = max(0, $downloaded - $self["downloaded"]);
	$USERUPDATESET[]=$announcetime = ($self["seeder"] == "yes" ? "seedtime = seedtime + $self[announcetime]" : "leechtime = leechtime + $self[announcetime]");
	$is_cheater = false;
	
	if ($cheaterdet_security){
		if ($az['class'] < $nodetect_security && $self['announcetime'] > 10&&$ipenable)
	//	if ($self['announcetime'] > 10)
		{
			$is_cheater = check_cheater($userid, $torrent['id'], $upthis, $downthis, $self['announcetime'], $torrent['seeders'], $torrent['leechers'],'('.$agent.')');
		}
	}

	if (!$is_cheater && ($trueupthis > 10 || $truedownthis > 10))
	{
				if($az["class"] == UC_VIP){
		$torrent['owner'] = $userid;
		//$truedownthis=0;
		}
		if($torrent['pos_state']=='sticky'||$torrent['nobuymoney']=='no')$truedownthisiffree=0;
								else $truedownthisiffree=1;
		
		$global_promotion_state = get_global_sp_state($torrent['sp_state'],$torrent['audiocodec']);
		if($global_promotion_state == 1)// Normal, see individual torrent
		{
		


		
		
			if($torrent['sp_state']==3) //2X
			{	$upthis=2*$trueupthis;
				$downthis=$downthis*$truedownthisiffree;
				$USERUPDATESET[] = "uploaded = uploaded + $upthis";
				$USERUPDATESET[] = "downloaded = downloaded + $downthis";
				
			}
			elseif($torrent['sp_state']==4) //2X Free
			{	$upthis=2*$trueupthis;
				$downthis=0;
				$USERUPDATESET[] = "uploaded = uploaded + $upthis";
				$USERUPDATESET[] = "downloaded = downloaded + 1";
				
				
			}
			elseif($torrent['sp_state']==6) //2X 50%
			{	$upthis=2*$trueupthis;
				$downthis=$truedownthis/2;
				$downthis=$downthis*$truedownthisiffree;
				$USERUPDATESET[] = "uploaded = uploaded + $upthis";
				$USERUPDATESET[] = "downloaded = downloaded + $downthis";
				
			}
			else{
				if ($torrent['owner'] == $userid && $uploaderdouble_torrent > 0)
					$upthis = $trueupthis * $uploaderdouble_torrent;

				if($torrent['sp_state']==2) //Free
				{	$downthis=0;
					$USERUPDATESET[] = "uploaded = uploaded + $upthis";
					$USERUPDATESET[] = "downloaded = downloaded + 1";
					
				}
				elseif($torrent['sp_state']==5) //50%
				{	$downthis=$truedownthis/2;
					$downthis=$downthis*$truedownthisiffree;
					$USERUPDATESET[] = "uploaded = uploaded + $upthis";
					$USERUPDATESET[] = "downloaded = downloaded + $downthis";
					
				}
				elseif($torrent['sp_state']==7) //30%
				{	$downthis=$truedownthis*3/10;
					$downthis=$downthis*$truedownthisiffree;
					$USERUPDATESET[] = "uploaded = uploaded + $upthis";
					$USERUPDATESET[] = "downloaded = downloaded + $downthis";
					
				}
				elseif($torrent['sp_state']==1) //Normal
				{	$downthis=$downthis*$truedownthisiffree;
					$USERUPDATESET[] = "uploaded = uploaded + $upthis";
					$USERUPDATESET[] = "downloaded = downloaded + $downthis";
				}
			}
		}
		elseif($global_promotion_state == 2) //Free
		{
			if ($torrent['owner'] == $userid && $uploaderdouble_torrent > 0)
				$upthis = $trueupthis * $uploaderdouble_torrent;
				$downthis=0;
			$USERUPDATESET[] = "uploaded = uploaded + $upthis";
			$USERUPDATESET[] = "downloaded = downloaded + 1";
			
		}
		elseif($global_promotion_state == 3) //2X
		{
			if ($uploaderdouble_torrent > 2 && $torrent['owner'] == $userid && $uploaderdouble_torrent > 0)
				$upthis = $trueupthis * $uploaderdouble_torrent;
			else $upthis = 2*$trueupthis;
			$downthis=$downthis*$truedownthisiffree;
			$USERUPDATESET[] = "uploaded = uploaded + $upthis";
			$USERUPDATESET[] = "downloaded = downloaded + $downthis";
		}
		elseif($global_promotion_state == 4) //2X Free
		{
			if ($uploaderdouble_torrent > 2 && $torrent['owner'] == $userid && $uploaderdouble_torrent > 0)
				$upthis = $trueupthis * $uploaderdouble_torrent;
			else $upthis = 2*$trueupthis;
			$downthis=0;
			$USERUPDATESET[] = "uploaded = uploaded + $upthis";
			$USERUPDATESET[] = "downloaded = downloaded + 1";
			
		}
		elseif($global_promotion_state == 5){ // 50%
			if ($torrent['owner'] == $userid && $uploaderdouble_torrent > 0)
				$upthis = $trueupthis * $uploaderdouble_torrent;
				$downthis=$truedownthis/2;
				$downthis=$downthis*$truedownthisiffree;
			$USERUPDATESET[] = "uploaded = uploaded + $upthis";
			$USERUPDATESET[] = "downloaded = downloaded + $downthis";
			
		}
		elseif($global_promotion_state == 6){ //2X 50%
			if ($uploaderdouble_torrent > 2 && $torrent['owner'] == $userid && $uploaderdouble_torrent > 0)
				$upthis = $trueupthis * $uploaderdouble_torrent;
			else $upthis = 2*$trueupthis;
			$downthis=$truedownthis/2;
			$downthis=$downthis*$truedownthisiffree;
			$USERUPDATESET[] = "uploaded = uploaded + $upthis";
			$USERUPDATESET[] = "downloaded = downloaded + $downthis";
			
		}
		elseif($global_promotion_state == 7){ //30%
			if ($torrent['owner'] == $userid && $uploaderdouble_torrent > 0)
				$upthis = $trueupthis * $uploaderdouble_torrent;
				$downthis=$truedownthis*3/10;
				$downthis=$downthis*$truedownthisiffree;
			$USERUPDATESET[] = "uploaded = uploaded + $upthis";
			$USERUPDATESET[] = "downloaded = downloaded + $downthis";
			
		}
		

		//$USERUPDATESET[] = "seedbonus = seedbonus+".($trueupthis/1024/1024/50)."-".($downthis/1024/1024/50);
		$USERUPDATESET[] = "seedbonus = seedbonus -".($downthis/1024/1024/50);
		
		
		
	}
}

$dt = sqlesc(date("Y-m-d H:i:s"));
$updateset = array();
// set non-type event
if (!isset($event))
	$event = "";
if (isset($self) && $event == "stopped")
{
	sql_query("DELETE FROM peers WHERE $selfwhere") or err("D Err");
	if (mysql_affected_rows())
	{	
		$updateset[] = ($self["seeder"] == "yes" ? "seeders = seeders - 1" : "leechers = leechers - 1");
		if($ipenable)sql_query("UPDATE snatched SET uploaded = uploaded + $trueupthis, downloaded = downloaded + $truedownthis, imdownloaded = imdownloaded + $downthis , imuploaded = imuploaded + $upthis , to_go = $left, $announcetime, last_action = ".$dt." WHERE torrentid = $torrentid AND userid = $userid") or err("SL Err 1");
	}
}
elseif(isset($self))
{
	if ($event == "completed")
	{
		//sql_query("UPDATE snatched SET  finished  = 'yes', completedat = $dt WHERE torrentid = $torrentid AND userid = $userid");
		$finished = ", finishedat = ".TIMENOW;
		$finished_snatched = ", completedat = ".$dt . ", finished  = 'yes'";
		$updateset[] = "times_completed = times_completed + 1";
	}else{
	//$finished_snatched=($seeder == "yes" ? ", finished  = 'yes'" : ", completedat = ".$dt . ",  finished  = 'no'");
	$finished_snatched=($seeder == "yes" ? ", finished  = 'yes'" : ", completedat = ".$dt);
	if($seeder == 'no')$finished = ", finishedat = ".TIMENOW;}
	$announcetimepeers = ($self["seeder"] == "yes" ? "" : " ,leechtime = leechtime + $self[announcetime]").
	", seedtime = seedtime + $self[announcetime] ,announcetime = $self[announcetime] ,upthis = $trueupthis, downthis =$truedownthis";
	
	
	
	
	sql_query("UPDATE peers SET ip = ".sqlesc($ip).",peer_id = ".sqlesc($peer_id).", port = $port, uploaded = $uploaded, connectable= '$connectable', downloaded = $downloaded, to_go = $left, prev_action = last_action, last_action = $dt, seeder = '$seeder' ,agent = ".sqlesc($agent).",iptype =$iptype, next_action = $real_annnounce_interval_dead $finished $announcetimepeers   WHERE $selfwhere") or err("PL Err 1");

	if (mysql_affected_rows())
	{
		if ($seeder <> $self["seeder"])
		$updateset[] = ($seeder == "yes" ? "seeders = seeders + 1, leechers = leechers - 1" : "seeders = seeders - 1, leechers = leechers + 1");
		if($ipenable)sql_query("UPDATE snatched SET uploaded = uploaded + $trueupthis, downloaded = downloaded + $truedownthis, to_go = $left, imdownloaded = imdownloaded + $downthis , imuploaded = imuploaded + $upthis , $announcetime, last_action = ".$dt." $finished_snatched WHERE torrentid = $torrentid AND userid = $userid") or err("SL Err 2");
	}
}
else
{
	

	
	sql_query("INSERT INTO peers (torrent, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, started, last_action, seeder, agent, downloadoffset, uploadoffset, passkey,iptype,isipv4,next_action) VALUES ($torrentid, $userid, ".sqlesc($peer_id).", ".sqlesc($ip).", $port, '$connectable', $uploaded, $downloaded, $left, $dt, $dt, '$seeder', ".sqlesc($agent).", $downloaded, $uploaded, ".sqlesc($passkey)." ,$iptype,$compact,$real_annnounce_interval_dead)") or err("PL Err 2");

	if (mysql_affected_rows())
	{
		$updateset[] = ($seeder == "yes" ? "seeders = seeders + 1" : "leechers = leechers + 1");
		//$updatesetsnatched = ($seeder == "yes" ? ", finished  = 'yes'" : ", completedat = ".$dt . ", finished  = 'no'");
		$updatesetsnatched = ($seeder == "yes" ? ", finished  = 'yes'" : ", completedat = ".$dt);
		$check = @mysql_fetch_row(@sql_query("SELECT torrentid FROM snatched WHERE torrentid = $torrentid AND userid = $userid LIMIT 1"));
		if (!$check['0'])
			sql_query("INSERT INTO snatched (torrentid, userid, ip, port, uploaded, downloaded, to_go, startdat, last_action, completedat, imuploaded, imdownloaded ) VALUES ( $torrentid, $userid, ".sqlesc($ip).", $port, $uploaded, $downloaded, $left, $dt, $dt, $dt , $uploaded, $downloaded )  ON DUPLICATE KEY update ip=values(ip)") or err("SL Err 4");
		else
			sql_query("UPDATE snatched SET to_go = $left, last_action = ".$dt.$updatesetsnatched ." WHERE torrentid = $torrentid AND userid = $userid") or err("SL Err 3.1");
	}
}



if (count($updateset)&&$ipenable) // Update only when there is change in peer counts
{
	

	if($seeder == 'yes')
	{$updateset[] = "last_action = $dt";
	$updateset[] = "visible = 'yes'";
	$updateset[] = "havenoseed = 'no'";}
	sql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $torrentid");
}



if($client_familyid != 0 && $client_familyid != $az['clientselect'])
	$USERUPDATESET[] = "clientselect = ".sqlesc($client_familyid);

if(count($USERUPDATESET) && $userid&&$ipenable)
{
	sql_query("UPDATE users SET " . join(",", $USERUPDATESET) . " WHERE id = ".$userid);
	sql_query('INSERT INTO peershis(userid,timenow,truploaded,trdownloaded,imuploaded,imdownloaded) VALUES 
	('.sqlesc($userid).
	','.sqlesc(date("Y-m-d")).
	','.sqlesc(0+$trueupthis).
	','.sqlesc(0+$truedownthis).
	','.sqlesc(0+$upthis).
	','.sqlesc(0+$downthis).') 
	ON DUPLICATE KEY update 
	truploaded=truploaded+values(truploaded),
	trdownloaded=trdownloaded+values(trdownloaded),
	imuploaded=imuploaded+values(imuploaded),
	imdownloaded=imdownloaded+values(imdownloaded)');
}


//if($banned_by_time_ipv4)err('网站暂时不支持IPV4哦！');

benc_resp_raw($resp);


if (!$Cache->get_value('user_passkey_curl_setopt')){
$Cache->cache_value('user_passkey_curl_setopt',"yes", 60);	
//$returndata=@file_get_contents('http://'.$BASEURLV4.'/curl_setopt.php?ptpasskey=fenwsjabhLFGhjrsabewvhu');
//error_log('http://'.$BASEURLV4.'/curl_setopt.php?ptpasskey=fenwsjabhLFGhjrsabewvhu'.$returndata);
}


/*
if (!$Cache->get_value('user_passkey_chenzhuyuchenzhuyu')){
	sql_query("DELETE FROM peers WHERE next_action < ".TIMENOW) or sqlerr(__FILE__, __LINE__);
	sql_query("DELETE FROM useriptype where ipv4 < ".TIMENOW." and ipv6 < ".TIMENOW) or die(mysql_error());
	$Cache->cache_value('user_passkey_chenzhuyuchenzhuyu',"yes", 600);
}*/
/*
if($userid==$MASTERUSERID)
sql_query("INSERT INTO chenzhuyudubug (num,page,time ,userid,pagecreatetime,torrentid) VALUES ("
.count($query_name).","
.sqlesc(join("//", $query_name) )." , "
.$dt." , "
.$userid." , "
.(TIMENOWSTART-microtime(1)).
" ,$torrentid )");
	
*/
	
exit();