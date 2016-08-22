<?php
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');
//error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL & ~E_NOTICE | E_STRICT);
ini_set('display_errors', 0);
if(defined('IN_TRACKER_ANNOUNCE'))
		include_once($rootpath . 'classes/class_cache_announce.php'); //Require the caching class
	else 
		include_once($rootpath . 'classes/class_cache.php'); //Require the caching class
$Cache = NEW CACHE(); //Load the caching class
$Cache->setLanguageFolderArray(get_langfolder_list());
$Cache->setkeyPre('AS_');
define('TIMENOW', time());
define('TIMENOWSTART',microtime(1));
$USERUPDATESET = array();
$query_name=array();
$query_name_num=0;
$global_hr_hit=0;

define ("UC_PEASANT", 0);
define ("UC_USER", 1);
define ("UC_POWER_USER", 2);
define ("UC_ELITE_USER", 3);
define ("UC_CRAZY_USER", 4);
define ("UC_INSANE_USER", 5);
define ("UC_VETERAN_USER", 6);
define ("UC_EXTREME_USER", 7);
define ("UC_ULTIMATE_USER", 8);
define ("UC_NEXUS_MASTER", 9);
define ("UC_VIP", 10);
define ("UC_RETIREE",11);
define ("UC_Warehouse",12);//保种员
define ("UC_UPLOADER",13);
define ("UC_FORUM_MODERATOR", 16);
define ("UC_MODERATOR",17);
define ("UC_ADMINISTRATOR",18);
define ("UC_SYSOP",19);
define ("UC_STAFFLEADER",20);
ignore_user_abort(1);
function strip_magic_quotes($arr)
{
	foreach ($arr as $k => $v)
	{
		if (is_array($v))
		{
			$arr[$k] = strip_magic_quotes($v);
		} else {
			$arr[$k] = stripslashes($v);
		}
	}
	return $arr;
}

if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
{
	if (!empty($_GET)) {
		$_GET = strip_magic_quotes($_GET);
	}
	if (!empty($_POST)) {
		$_POST = strip_magic_quotes($_POST);
	}
	if (!empty($_COOKIE)) {
		$_COOKIE = strip_magic_quotes($_COOKIE);
	}
}

function get_langfolder_list()
{
	//do not access db for speed up, or for flexibility
	return array("en", "chs", "cht", "ko", "ja");
}
