<?php 
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

$userid=$CURUSER['id'];
if (get_user_class() >= UC_ADMINISTRATOR&&$_GET['userid'])$userid=0+$_GET['userid'];

function get_hr_ratio($uped,$downed){
		if ($downed > 0)
		{
			$ratio = $uped / $downed;
			$color = get_ratio_color($ratio);
			if($ratio>10000)$ratio='Inf.';
			else
			$ratio = number_format($ratio, 3);

			if ($color)
				$ratio = "<font color=\"".$color."\">".$ratio."</font>";
		}elseif ($uped > 0)
			$ratio = 'Inf.';
		else
			$ratio = "---";
	
	return $ratio;
}

	stdhead("H&R");
	print("<h1>H&R记录</h1>");
		if($_GET['hrtype']=='A')$hrtype="A";
	elseif($_GET['hrtype']=='B')$hrtype="B";
	elseif($_GET['hrtype']=='C')$hrtype="C";
	elseif($_GET['hrtype']=='D')$hrtype="D";
	else $hrtype="A";
		
	print("
<p>
<a href='?hrtype=A' ".($hrtype=="A"?"class='faqlink'":"")."><b>考察中</b></a> | 
<a href='?hrtype=B' ".($hrtype=="B"?"class='faqlink'":"")."><b>已达标</b></a> | 
<a href='?hrtype=C' ".($hrtype=="C"?"class='faqlink'":"")."><b>未达标</b></a> | 
<a href='?hrtype=D' ".($hrtype=="D"?"class='faqlink'":"")."><b>已免罪</b></a><br />");

	begin_main_frame("",true);

		sql_query("UPDATE snatched SET HR ='B'  where HR ='A' AND (uploaded >= downloaded OR seedtime > 24*3600) 
		AND finished='yes' AND userid=".$userid." ") ;
	
		
				$rescount = get_row_count("snatched", "WHERE userid=".$userid." and hr='$hrtype' AND finished='yes'");
				list($pagertop, $pagerbottom, $limit) = pager(50, $rescount, "?hrtype=$hrtype&");
				print("<table width='100%'>");
				print("<tr>
				<td class='colhead' align='center'>HR编号</td>
				<td class='colhead' align='center'>种子名称</td>
				<td class='colhead' align='center'>上传量</td>
				<td class='colhead' align='center'>下载量</td>
				<td class='colhead' align='center'>分享率</td>
				<td class='colhead' align='center'>还需做种时间</td>
				<td class='colhead' align='center'>完成时间</td>
				<td class='colhead' align='center'>剩余达标时间</td>
				</tr>");
					if($rescount){			
			
				$res = sql_query("SELECT torrents.id,name,uploaded,downloaded,seedtime,completedat,finished FROM snatched LEFT JOIN torrents ON torrentid=torrents.id WHERE userid=".sqlesc($userid)." AND HR='$hrtype' AND finished='yes' ORDER BY completedat DESC $limit");

				while ($row = mysql_fetch_assoc($res)){
				print("<tr>
				<td class='rowfollow nowrap' align='center'>".$row['id']."</td>
				<td class='rowfollow' align='left'><a href='details.php?id=".$row['id']."'>".$row['name']."</a></td>
				<td class='rowfollow nowrap' align='center'>".mksize($row['uploaded'])."</td>
				<td class='rowfollow nowrap' align='center'>".mksize($row['downloaded'])."</td>
				<td class='rowfollow nowrap' align='center'>". get_hr_ratio($row['uploaded'],$row['downloaded'])."</td>
				<td class='rowfollow nowrap' align='center'>".mkprettytime(3600*24-$row['seedtime'])."</td>
				<td class='rowfollow nowrap' align='center'>".$row['completedat']."</td>
				<td class='rowfollow nowrap' align='center' >".mkprettytime(strtotime($row['completedat'])-TIMENOW+14*3600*24)."</td>
				
				</tr>");				
													}

				}else
				print("<tr><td class='owfollow nowrap' align='center' colspan='9'>森马都没有找到</td></tr>");
				
				
				print("</table>");
				print($pagerbottom);
			end_main_frame();
	stdfoot();
	die();
	
