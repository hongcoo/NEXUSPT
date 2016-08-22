<?php
if(!defined('IN_TRACKER'))
die('Hacking attempt!');

function get_global_sp_state($promotion=0,$secondtype=0)
{
	global $Cache,$promotion_secondtype,$global_hr_hit;
	static $global_promotion_state_res;
	
		if (!$global_promotion_state_res){
		if(!$global_promotion_state_res = $Cache->get_value('global_promotion_state')){
		$r = mysql_query("SELECT * FROM torrents_state");
		while ($row = mysql_fetch_array($r))$global_promotion_state_res[$row[secondtype]] = $row[global_sp_state];
		$Cache->cache_value('global_promotion_state', $global_promotion_state_res, 3600);
		}
		}
		
		if($global_promotion_state_res[9999]>1 && !$global_hr_hit )return $global_promotion_state_res[9999];
		else
		return $promotion_secondtype[0+$promotion][0+$global_promotion_state_res[$secondtype]];
}

// IP Validation
function validip($ip)
{

		
if(preg_match("/[^0-9a-fA-FRO<>\:\.]/i",$ip))return false;	
if(preg_match( '/^2001:0:/i',$ip)||preg_match( '/^2002:/i',$ip)||preg_match( '/^f/i',$ip)||preg_match( '/^:/i',$ip))return false;	
//$novalidip=array('unknown',' ',',');
//foreach ( $novalidip as $novalid )	if(preg_match("/".$novalid."/i",$ip))return false;
		
//if(in_array($ip, $novalidip))return false;

if (!ip2long($ip)) //IPv6
return true;

		
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
		$reserved_ips = array (
		array('192.0.2.0','192.0.2.255'),
		array('192.168.0.0','192.168.255.255'),
		array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
			$min = ip2long($r[0]);
			$max = ip2long($r[1]);
			if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

function getip() {
	if (isset($_SERVER)) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	} else {
		if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR'))) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP'))) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else {
			$ip = getenv('REMOTE_ADDR');
		}
	}

	return $ip;
}

function write_log($text, $security = "normal"){
	if(!in_array($security, array('mod','normal')))return error_log($text);

	$text = sqlesc($text);
	$added = sqlesc(date("Y-m-d H:i:s"));
	$security = sqlesc($security);
	mysql_query("INSERT INTO sitelog (added, txt, security_level) VALUES($added, $text, $security)");
}


if($enablesqldebug_tweak == 'yes'){

function sql_query($query){
global $query_name,$query_time,$query_name_num;//timechenzhuyu
	//mysql_ping();
	$begin=microtime(1);
	if(!$query_return = @mysql_query($query))write_log($query.'@'.mysql_error().'@'.$_SERVER["URL"],'file');
	$query_time += $alltime = microtime(1)-$begin;
	$query_name[] =($alltime)."@".$query;
	$query_name_num++;
	return $query_return;
}

}else{

function sql_query($query){
global $query_name_num;
	$query_name_num++;
	if($query_return = @mysql_query($query))return $query_return;
	write_log($query.'@'.mysql_error().'@'.$_SERVER["URL"],'file');
}

}

function sqlesc($value,$string=true) {
	if ($string||!is_numeric($value)) 
	{
		$value = "'" . mysql_real_escape_string($value) . "'";
	}
	return $value;
}

function hash_pad($hash) {
    return str_pad($hash, 20);
}

function hash_where($name, $hash) {
	$shhash = preg_replace('/ *$/s', "", $hash);
	return " $name in (" . sqlesc($hash) . " , " . sqlesc($shhash) . ") ";
}


function ip2long6($ipv6) { 
global $Cache;
if (!$result = $Cache->get_value('ip2long6_'.$ipv6))
{
  $ip_n = inet_pton($ipv6); 
  $bits = 15; // 16 x 8 bit = 128bit 
  while ($bits >= 0) { 
    $bin = sprintf("%08b",(ord($ip_n[$bits]))); 
    $ipv6long = $bin.$ipv6long; 
    $bits--; 
  } 
  $result=gmp_strval(gmp_init($ipv6long,2),10); 
  $Cache->cache_value('ip2long6_'.$ipv6, $result, 3600);
}
  return $result;
} 

function long2ip6($ipv6long) { 
global $Cache;
if (!$result = $Cache->get_value('long2ip6_'.$ipv6long)){	
  $bin = gmp_strval(gmp_init($ipv6long,10),2); 
  if (strlen($bin) < 128) { 
    $pad = 128 - strlen($bin); 
    for ($i = 1; $i <= $pad; $i++) { 
    $bin = "0".$bin; 
    } 
  } 
  $bits = 0; 
  while ($bits <= 7) { 
    $bin_part = substr($bin,($bits*16),16); 
    $ipv6 .= dechex(bindec($bin_part)).":"; 
    $bits++; 
  }  
    $result=inet_ntop(inet_pton(substr($ipv6,0,-1)));
    $Cache->cache_value('long2ip6_'.$ipv6long, $result, 3600);
}
  return $result; 
} 

function dbconn_error_check($cachenew='') {
	global $Cache;
	
	if($cachenew)$Cache->cache_value('dbconn_mysql_connect_error', $cachenew,60);
	elseif ($cachenew=$Cache->get_value('dbconn_mysql_connect_error'))die('MySql Error : '.$cachenew);
	
}


?>
