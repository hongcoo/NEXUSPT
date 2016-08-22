<?php
require_once('include/bittorrent_announce.php');
header("Location: http://".$BASEURLV6."/announce.php?" . $_SERVER['QUERY_STRING']);
die();
?>