<?php
@set_time_limit(120);
define('IN_TRACKER', true);
define("PROJECTNAME","NexusPHP");
define("NEXUSPHPURL","http://pt.antsoul.com");
define("NEXUSWIKIURL","http://pt.antsoul.com");
define("VERSION","Powered by <a href=\"http://sourceforge.net/projects/nexusphp\">".PROJECTNAME."</a>");
define("THISTRACKER","General");
//if($_SERVER[HTTP_HOST]=='pt.swjtu6.edu.cn')
//ini_set("memory_limit",(512+256)."M");
//echo ini_get("memory_limit")."\n";
$nozip=array('/downloadsubs.php','/getattachment.php','/shoutbox2.php','/0docleanup.php');
//if(Extension_Loaded('zlib')&&!in_array($_SERVER['PHP_SELF'], $nozip)) 
//{Ob_Start('ob_gzhandler');}
error_reporting(1);
//header("Connection: Keep-Alive");
//header("Keep-Alive: timeout=15, max=98");

$showversion = " - Powered by ".PROJECTNAME;
$rootpath=realpath(dirname(__FILE__) . '/..');
set_include_path(get_include_path() . PATH_SEPARATOR . $rootpath);
$rootpath .= "/";
include($rootpath . 'include/core.php');
include_once($rootpath . 'include/functions.php');

