<?php
require_once('include/bittorrent_announce.php');
header("Location: http://".$BASEURLV6."/scrape.php?" . $_SERVER['QUERY_STRING']);
die();
?>