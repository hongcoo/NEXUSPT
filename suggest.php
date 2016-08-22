<?php
require "include/bittorrent.php";
dbconn();

//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

if (isset($_GET['q']) && $_GET['q'] != '')
{
	$searchstr = unesc(trim($_GET['q']));	
	$suggest_query = sql_query("SELECT keywords AS suggest, sum(times) AS count FROM suggest WHERE keywords LIKE " . sqlesc($searchstr . "%")." AND adddate >".sqlesc(date('Y-m-d',strtotime('-6 month', strtotime(date('Y-m', time()).'-01'))))."GROUP BY keywords ORDER BY count DESC, keywords DESC LIMIT 10") or sqlerr(__FILE__,__LINE__);
	$result = "";
	$i = 0;
	while($suggest = mysql_fetch_array($suggest_query)){
		if (strlen($suggest['suggest']) > 25) continue;
		$result .= ($result == "" ? "" : "\r\n" ). $suggest['suggest'] . "\r\n" . $suggest['count'];
		$i++;
		if ($i >= 5) break;
	}
	echo $result;
}
?>
