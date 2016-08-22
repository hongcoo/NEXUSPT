<?php
# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER'))
	die('Hacking attempt!');
include_once($rootpath . 'include/config.php');
include_once($rootpath . 'include/globalfunctions.php');


function dbconn_announce() {
	global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;
	dbconn_error_check();
	if (!@mysql_pconnect($mysql_host, $mysql_user, $mysql_pass))
	{
		dbconn_error_check("[" . mysql_errno() . "] dbconn2: mysql_connect: " . mysql_error());
		err("[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
	}
	mysql_query("SET NAMES UTF8");
	mysql_query("SET collation_connection = 'utf8_general_ci'");
	mysql_query("SET sql_mode=''");
	mysql_select_db($mysql_db) or err('dbconn: mysql_select_db: ' + mysql_error());
}

function get_single_value_useriptype($field, $suffix = "",$MODEMAX=4){
	global $Cache,$USERUPDATESET;
	$a = @mysql_fetch_row(sql_query("SELECT $field ,reuseriptype FROM useriptype $suffix LIMIT 1")) ;
	if($a[1]){
	$Cache->delete_value('user_passkey_'.$a[1].'_content');		
	sql_query("UPDATE useriptype SET reuseriptype=0 where userid=".sqlesc($a[1]));
	$USERUPDATESET[] = "MODEMAX = $MODEMAX";
	}
	if ($a) {
		return $a[0];
	} else {
		return false;
	}
}

function hash_where_arr($name, $hash_arr) {
	$new_hash_arr = Array();
	foreach ($hash_arr as $hash) {
		//$new_hash_arr[] = sqlesc((urldecode($hash)));
		$new_hash_arr[] = sqlesc((urldecode(pack("H*",bin2hex($hash)))));
	}
	return $name." IN ( ".implode(", ",$new_hash_arr)." )";
}

function emu_getallheaders() {
	foreach($_SERVER as $name => $value)
		if(substr($name, 0, 5) == 'HTTP_')
			$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
	return $headers;
}

function block_browser(){
	global $BASEURLV6,$BASEURLV4;
	header("Content-Type: text/html; charset=utf-8");
	$agent = $_SERVER["HTTP_USER_AGENT"];
	if (preg_match("/^Mozilla/", $agent) || preg_match("/^Opera/", $agent) || preg_match("/^Links/", $agent) || preg_match("/^Lynx/", $agent) )
		{
print("你现在的IP地址是 : ".($ip=getip()));
if(preg_match( '/^2001:0:/i',$ip)||preg_match( '/^2002:/i',$ip))
print("<font color=\"#FF0000\"> <br /><br />这是一个无效的IPV6地址.");
elseif ((preg_match( '/2001:da8:/i',$ip)||preg_match( '/2001:250:/i',$ip))&&!preg_match( '/:0:5efe:|:200:5efe:/i',$ip))
print("<font color=\"#008B00\"> <br /><br />恭喜你,你现在正在使用原生(纯)IPV6.<br /><br /><a href=\"http://$BASEURLV4/announce.php\">IPV4 测试</a>");
elseif (preg_match( '/:0:5efe:|:200:5efe:/i',$ip))
print("<font color=\"#FFD700\"> <br /><br />你现在可能正在使用IPV6隧道.<br /><br /><a href=\"http://$BASEURLV4/announce.php\">IPV4 测试</a>");
else 
print("<font color=\"#FF0000\"> <br /><br />你现在处于IPV4环境.<br /><br /><a href=\"http://$BASEURLV6/announce.php\">IPV6 测试</a>");
	exit();
	}
	
// check headers
	if (function_exists('getallheaders')) //getallheaders() is only supported when PHP is installed as an Apache module
		$headers = getallheaders();
	else
		$headers = emu_getallheaders();

	if($_SERVER["HTTPS"] != "on")
	{
		if (isset($headers["Cookie"]) || isset($headers["Accept-Language"]) || isset($headers["Accept-Charset"]))
			err("Anti-Cheater: You cannot use this agent");
	}
	
}

function benc_resp($d)
{
	benc_resp_raw(benc(array('type' => 'dictionary', 'value' => $d)));
}
function benc_resp_raw($x) {
	header("Content-Type: text/plain; charset=utf-8");
	header("Pragma: no-cache");

	if ($_SERVER["HTTP_ACCEPT_ENCODING"] == "gzip" && function_exists('gzencode'))
	{
		header("Content-Encoding: gzip");
		echo gzencode($x, 9, FORCE_GZIP);
	} 
	else
		echo $x;
}
function err($msg, $userid = 0, $torrentid = 0)
{
//sql_query("INSERT INTO chenzhuyudubug (num ,torrentid, page , time ,userid,ip) VALUES (".count($query_name).",".($torrentid).",".sqlesc($_SERVER["REQUEST_URI"]."//" . join("//", $query_name) )." , ".$dt." , ".$userid.", ".sqlesc($ip).")");
	benc_resp(array('failure reason' => array('type' => 'string', 'value' => $msg),'interval' => array('type' => 'integer', 'value' => 1800),'min interval' => array('type' => 'integer', 'value' => 60)));
	exit();
}
function check_cheater($userid, $torrentid, $uploaded, $downloaded, $anctime, $seeders=0, $leechers=0,$agent=''){
	global $cheaterdet_security,$nodetect_security;
	
	$time = date("Y-m-d H:i:s");
	$upspeed = ($uploaded > 0 ? $uploaded / $anctime : 0);

	if ($uploaded > 1073741824 && $upspeed > (52428800/$cheaterdet_security)) //Uploaded more than 1 GB with uploading rate higher than 50 MByte/S (For Consertive level). This is no doubt cheating.
	{
		$comment = "你的账户因为作弊而被禁用".$agent;
		sql_query("INSERT INTO cheaters (added, userid, torrentid, uploaded, downloaded, anctime, seeders, leechers, comment) VALUES (".sqlesc($time).", $userid, $torrentid, $uploaded, $downloaded, $anctime, $seeders, $leechers, ".sqlesc($comment).")") or err("Tracker error 51");
		//sql_query("UPDATE users SET enabled = 'no' warneduntil=".sqlesc(date("Y-m-d H:i:s"))." WHERE id=$userid") or err("Tracker error 50"); //automatically disable user account;
		err($comment);
		return true;
	}
	if ($uploaded > 1073741824 && $upspeed > (13437184*2/$cheaterdet_security)) //Uploaded more than 1 GB with uploading rate higher than 9 MByte/S (For Consertive level). This is likely cheating.
	{
		$secs = 24*60*60; //24 hours
		$dt = sqlesc(date("Y-m-d H:i:s",(strtotime(date("Y-m-d H:i:s")) - $secs))); // calculate date.
		//$countres = sql_query("SELECT id FROM cheaters WHERE userid=$userid AND torrentid=$torrentid AND added > $dt");
		$countres = sql_query("SELECT id FROM cheaters WHERE userid=$userid AND torrentid=$torrentid  ");
		$comment = "上传速度非常快,疑似作弊".$agent;
		//if (mysql_num_rows($countres) == 0)
		if (1)
		{
			sql_query("INSERT INTO cheaters (added, userid, torrentid, uploaded, downloaded, anctime, seeders, leechers, hit, comment) VALUES (".sqlesc($time).", $userid, $torrentid, $uploaded, $downloaded, $anctime, $seeders, $leechers, 1,".sqlesc($comment).")") or err("Tracker error 52");
		}
		else{
			$row = mysql_fetch_row($countres);
			sql_query("UPDATE cheaters SET hit=hit+1, dealtwith = 0  , comment = ".sqlesc($comment)." WHERE id=".$row[0]);
			
		}
		//sql_query("UPDATE users SET downloadpos = 'no' WHERE id=$userid") or err("Tracker error 53"); //automatically remove user's downloading privileges;
		err($comment);
		return true;

	}
if ($cheaterdet_security > 1){// do not check this with consertive level
	if ($uploaded > 1073741824 && $upspeed > 9437184 && $leechers < (2 * $cheaterdet_security)) //Uploaded more than 1 GB with uploading rate higher than 9 MByte/S when there is less than 8 leechers (For Consertive level). This is likely cheating.
	{
		$secs = 24*60*60; //24 hours
		$dt = sqlesc(date("Y-m-d H:i:s",(strtotime(date("Y-m-d H:i:s")) - $secs))); // calculate date.
		$countres = sql_query("SELECT id FROM cheaters WHERE userid=$userid AND torrentid=$torrentid");
		$comment = "上传速度太快,并且下载者很少,疑似作弊".$agent;
		//$countres = sql_query("SELECT id FROM cheaters WHERE userid=$userid AND torrentid=$torrentid AND added > $dt");
		//if (mysql_num_rows($countres) == 0)
		if (1)
		{
			sql_query("INSERT INTO cheaters (added, userid, torrentid, uploaded, downloaded, anctime, seeders, leechers, comment) VALUES (".sqlesc($time).", $userid, $torrentid, $uploaded, $downloaded, $anctime, $seeders, $leechers, ".sqlesc($comment).")") or err("Tracker error 52");
		}
		else
		{
			$row = mysql_fetch_row($countres);
			sql_query("UPDATE cheaters SET hit=hit+1, dealtwith = 0 , comment = ".sqlesc($comment)." WHERE id=".$row[0]);
		}
		err($comment);
		//sql_query("UPDATE users SET downloadpos = 'no' WHERE id=$userid") or err("Tracker error 53"); //automatically remove user's downloading privileges;
		return true;
	}
	if ($uploaded > 10485760 && $upspeed > 1048576*3 && $leechers == 0) //Uploaded more than 10 MB with uploading speed faster than 1024*3 KByte/S when there is no leecher. This is likely cheating.
	{
		$secs = 24*60*60; //24 hours
		$dt = sqlesc(date("Y-m-d H:i:s",(strtotime(date("Y-m-d H:i:s")) - $secs))); // calculate date.
		$countres = sql_query("SELECT id FROM cheaters WHERE userid=$userid AND torrentid=$torrentid ");
		$comment = "在没有下载用户时上传,疑似作弊".$agent;
		//$countres = sql_query("SELECT id FROM cheaters WHERE userid=$userid AND torrentid=$torrentid AND added > $dt");
		//if (mysql_num_rows($countres) == 0)
		if (1)
		{
			sql_query("INSERT INTO cheaters (added, userid, torrentid, uploaded, downloaded, anctime, seeders, leechers, comment) VALUES (".sqlesc($time).", $userid, $torrentid, $uploaded, $downloaded, $anctime, $seeders, $leechers, ".sqlesc($comment).")") or err("Tracker error 52");
		}
		else
		{
			$row = mysql_fetch_row($countres);
			sql_query("UPDATE cheaters SET hit=hit+1, dealtwith = 0 ,comment = ".sqlesc($comment)."  WHERE id=".$row[0]);
		}
		err($comment);
		//sql_query("UPDATE users SET downloadpos = 'no' WHERE id=$userid") or err("Tracker error 53"); //automatically remove user's downloading privileges;
		return true;
	}
}
	return false;
}
function portblacklisted($port)
{
	// direct connect
	if ($port >= 411 && $port <= 413) return true;
	// bittorrent
	if ($port >= 6881 && $port <= 6889) return true;
	// kazaa
	if ($port == 1214) return true;
	// gnutella
	if ($port >= 6346 && $port <= 6347) return true;
	// emule
	if ($port == 4662) return true;
	// winmx
	if ($port == 6699) return true;
	return false;
}

function ipv4_to_compact($ip, $port)
{
	$compact = pack("Nn", sprintf("%d",ip2long($ip)), $port);
	return $compact;
}

function check_client($peer_id,$agent)
{
	global $BASEURL, $Cache ,$client_familyid;
	$client_familyid=0;
	if (!$clients = $Cache->get_value('allowed_client_list')){
		$clients = array();
		$res = sql_query("SELECT * FROM agent_allowed_family ORDER BY hits DESC") or err("check err");
		while ($row = mysql_fetch_array($res))
			$clients[] = $row;
		$Cache->cache_value('allowed_client_list', $clients, 86400);
	}
	foreach ($clients as $row_allowed_ua)
	{
		$allowed_flag_peer_id = false;
		$allowed_flag_agent = false;
		$version_low_peer_id = false;
		$version_low_agent = false;

		if($row_allowed_ua['peer_id_pattern'] != '')
		{
			if(!preg_match($row_allowed_ua['peer_id_pattern'], $row_allowed_ua['peer_id_start'], $match_bench)){
			write_log("peer_id_pattern_error:".$row_allowed_ua['peer_id_pattern']."///".$row_allowed_ua['peer_id_start'],'file');
			err("regular expression err for: " . $row_allowed_ua['peer_id_start'] . ", please ask sysop to fix this");
			
			}

			if(preg_match($row_allowed_ua['peer_id_pattern'], $peer_id, $match_target))
			{
				if($row_allowed_ua['peer_id_match_num'] != 0)
				{
					for($i = 0 ; $i < $row_allowed_ua['peer_id_match_num']; $i++)
					{
						if($row_allowed_ua['peer_id_matchtype'] == 'dec')
						{
							$match_target[$i+1] = 0 + $match_target[$i+1];
							$match_bench[$i+1] = 0 + $match_bench[$i+1];
						}
						else if($row_allowed_ua['peer_id_matchtype'] == 'hex')
						{
							$match_target[$i+1] = hexdec($match_target[$i+1]);
							$match_bench[$i+1] = hexdec($match_bench[$i+1]);
						}

						if ($match_target[$i+1] > $match_bench[$i+1])
						{
							$allowed_flag_peer_id = true;
							break;
						}
						else if($match_target[$i+1] < $match_bench[$i+1])
						{
							$allowed_flag_peer_id = false;
							$version_low_peer_id = true;
							$low_version = "Your " . $row_allowed_ua['family'] . " 's version is too low, please update it after " . $row_allowed_ua['start_name'];
							break;
						}
						else if($match_target[$i+1] == $match_bench[$i+1])//equal
						{
							if($i+1 == $row_allowed_ua['peer_id_match_num'])		//last
							{
								$allowed_flag_peer_id = true;
							}
						}
					}
				}
				else // no need to compare version
				$allowed_flag_peer_id = true;
			}
		}
		else	// not need to match pattern
		$allowed_flag_peer_id = true;

		if($row_allowed_ua['agent_pattern'] != '')
		{
			if(!preg_match($row_allowed_ua['agent_pattern'], $row_allowed_ua['agent_start'], $match_bench)){
			write_log("peer_id_pattern_error2:".$row_allowed_ua['agent_pattern']."///".$row_allowed_ua['agent_start'],'file');
			err("regular expression err for: " . $row_allowed_ua['agent_start'] . ", please ask sysop to fix this");}

			if(preg_match($row_allowed_ua['agent_pattern'], $agent, $match_target))
			{
				if( $row_allowed_ua['agent_match_num'] != 0)
				{
					for($i = 0 ; $i < $row_allowed_ua['agent_match_num']; $i++)
					{
						if($row_allowed_ua['agent_matchtype'] == 'dec')
						{
							$match_target[$i+1] = 0 + $match_target[$i+1];
							$match_bench[$i+1] = 0 + $match_bench[$i+1];
						}
						else if($row_allowed_ua['agent_matchtype'] == 'hex')
						{
							$match_target[$i+1] = hexdec($match_target[$i+1]);
							$match_bench[$i+1] = hexdec($match_bench[$i+1]);
						}

						if ($match_target[$i+1] > $match_bench[$i+1])
						{
							$allowed_flag_agent = true;
							break;
						}
						else if($match_target[$i+1] < $match_bench[$i+1])
						{
							$allowed_flag_agent = false;
							$version_low_agent = true;
							$low_version = "Your " . $row_allowed_ua['family'] . " 's version is too low, please update it after " . $row_allowed_ua['start_name'];
							break;
						}
						else //equal
						{
							if($i+1 == $row_allowed_ua['agent_match_num'])		//last
							$allowed_flag_agent = true;
						}
					}
				}
				else // no need to compare version
				$allowed_flag_agent = true;
			}
		}
		else
		$allowed_flag_agent = true;

		if($allowed_flag_peer_id && $allowed_flag_agent)
		{
			$exception = $row_allowed_ua['exception'];
			$family_id = $row_allowed_ua['id'];
			$allow_https = $row_allowed_ua['allowhttps'];
			break;
		}
		elseif(($allowed_flag_peer_id || $allowed_flag_agent) || ($version_low_peer_id || $version_low_agent))	//client spoofing possible
		;//add anti-cheat code here
	}

	if($allowed_flag_peer_id && $allowed_flag_agent)
	{
		if($exception == 'yes')
		{
			if (!$clients_exp = $Cache->get_value('allowed_client_exception_family_'.$family_id.'_list')){
				$clients_exp = array();
				$res = sql_query("SELECT * FROM agent_allowed_exception WHERE family_id = $family_id") or err("check err");
				while ($row = mysql_fetch_array($res))
					$clients_exp[] = $row;
				$Cache->cache_value('allowed_client_exception_family_'.$family_id.'_list', $clients_exp, 86400);
			}
			if($clients_exp)
			{
				foreach ($clients_exp as $row_allowed_ua_exp)
				{
					if(($row_allowed_ua_exp['agent'] == $agent||!$row_allowed_ua_exp['agent']) && preg_match("/^" . $row_allowed_ua_exp['peer_id'] . "/", $peer_id))
					return "客户端 " . $row_allowed_ua_exp['name'] . " 被禁止,原因归结于: " . $row_allowed_ua_exp['comment'] . ".";
				}
			}
			$client_familyid = $row_allowed_ua['id'];
		}
		else
		{
			$client_familyid = $row_allowed_ua['id'];
		}
		
		if($row_allowed_ua['comment'])
			return $row_allowed_ua['comment'];
		elseif($_SERVER["HTTPS"] == "on")
		{
			if($allow_https == 'yes')
			return 0;
			else
			return "This client does not support https well, Please goto $BASEURL/faq.php#id29 for a list of proper clients";
		}
		else
			return 0;	// no exception found, so allowed or just allowed
	}
	else
	{
		if($version_low_peer_id && $version_low_agent)
		return $low_version;
		else
		return "你目前使用的客户端是被禁止的";
	}
}

function get_row_count($table, $suffix = "")
{
	$r = sql_query("SELECT COUNT(*) FROM $table $suffix") or err(__FILE__, __LINE__);
	$a = mysql_fetch_row($r) or die(mysql_error());
	return $a[0];
}


function announceipcheck($ip,$userid){
global $Cache;

if (!$ipbanned =$Cache->get_value('announceipcheck_'.$ip.'_'.$userid)){
sql_query("INSERT INTO iplog (ip, userid, access) VALUES (" . sqlesc($ip) . ", " . $userid . ", " . sqlesc(date("Y-m-d H:i:s")) . ")   ON DUPLICATE KEY update access=values(access)");

$ipbanned='no';$nip = ip2long6($ip);

//$res = @mysql_fetch_array(sql_query("SELECT * FROM locations WHERE $nip >= start_ip6 AND $nip <= end_ip6"));
//if(ip2long($ip)&&!$res)$ipbanned='IPV6可以给校外的你带来更多惊喜';				
			
$res = @mysql_fetch_assoc(sql_query("SELECT comment FROM bans WHERE $nip >= first AND $nip <= last and bantracker = 1 "));
 if($res['comment'])$ipbanned="禁止的IP,原因:".$res['comment']."@".$ip;

$Cache->cache_value('announceipcheck_'.$ip.'_'.$userid,$ipbanned,3600);
}
if($ipbanned!='no')err($ipbanned);	
}