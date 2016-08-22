<?php
@set_time_limit(5);
# IMPORTANT: Do not edit below unless you know what you are doing!
define('IN_TRACKER', true);
define('IN_TRACKER_ANNOUNCE', true);
//$rootpath=realpath(dirname(__FILE__) . '/..')."/";
$rootpath=realpath(dirname(__FILE__) . '/..');
set_include_path(get_include_path() . PATH_SEPARATOR . $rootpath);
$rootpath .= "/";

include($rootpath . 'include/core.php');
include_once($rootpath . 'include/functions_announce.php');
?>
