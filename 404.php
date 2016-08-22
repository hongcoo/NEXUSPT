<?php
require_once("include/bittorrent.php");
dbconn(true);
httperr(false);
		
//require_once(get_langfile_path("", false, $CURLANGDIR));

stdhead("404~这个萝莉无法被攻略");
	
	
	
	
return_audio("pic/error.wav","pic/error2.wav");
	?>	
	<p>
<table border="1" cellspacing="0" cellpadding="10"> 





<tr><td style='padding: 0px'><img src="pic/error-404.jpg" /></td> 
</tr> 


</table> 

<?php		
//sql_query("INSERT INTO 404page (page,userid,nowtime) VALUES (".sqlesc($_SERVER['REQUEST_URI']).",".sqlesc(cookietureuserid()).",".sqlesc(date("Y-m-d H:i:s")).") ON DUPLICATE KEY update nowtime=values(nowtime)");
stdfoot();
