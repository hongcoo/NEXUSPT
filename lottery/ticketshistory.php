<?php
/*made by putyn */
if(!defined('IN_LOTTERY'))
  die('You can\'t access this file directly!');
  $_GET['finduid']=0+$_GET['finduid'];
if(!$_GET['finduid']){

			stdhead("乐透历史记录");
			print("<h1>乐透历史记录</h1>");
				begin_main_frame("",true);
				$rescount = mysql_fetch_assoc(sql_query("SELECT count(*) as num FROM lotteryhistory where  type ='winner' ORDER BY  id  DESC"));
				list($pagertop, $pagerbottom, $limit) = pager(50, $rescount['num'], "?finduid={$_GET['finduid']}&do=ticketshistory&");
				print("<table width='100%'>");

				print("<tr>
				<td class='colhead' align='center'>时间</td>
				<td class='colhead' align='center'>奖励</td>
				<td class='colhead' align='center'>彩券号</td>
				<td class='colhead' align='center'>用户</td>
				</tr>");
					if($rescount['num']){			
			
				$res = sql_query("SELECT * FROM lotteryhistory  where  type ='winner' ORDER BY opentime desc $limit");
				

				while ($row = mysql_fetch_assoc($res)){
				print("
				<td class='rowfollow nowrap' align='center'>".date("Y-m-d H:i:s",$row[opentime])."</td>
				<td class='rowfollow nowrap' align='center'>".($row[amount])."</td>
				<td class='rowfollow nowrap' align='center'>".($row[ticket])."</td>
				<td class='rowfollow nowrap' align='center'>".implode("&nbsp;|&nbsp;", array_map("get_username",explode('|',$row[user])))."</td>
				</tr>");				
													}

				}else
				print("<tr><td class='owfollow nowrap' align='center' colspan='4'>森马都没有找到</td></tr>");
				
			print("<tr><td align='left' class='colhead' colspan='7'><form method='get' action='".$_SERVER['PHP_SELF']."'><b>查询用户(UID)记录:</b><input type='text' maxlength='10' size='10' name='finduid' value='".$_GET['finduid']."' /><input type='hidden'  name='do' value='ticketshistory' /><input type='submit' value='提交' /></form></td></tr>");
				
				print("</table>");
				print($pagerbottom);
			end_main_frame();
			stdfoot();
  }else{
				stdhead("历史记录");
				print("<h1>".get_username($_GET['finduid'])."的记录</h1>");
				begin_main_frame("",true);
				$rescount = mysql_fetch_assoc(sql_query("SELECT count(*) as num FROM lotteryhistory where  user  like'{$_GET['finduid']}|%' or user  like '%|{$_GET['finduid']}|%' or user  like'%|{$_GET['finduid']}' or user  like'{$_GET['finduid']}' ORDER BY  id  DESC"));
				list($pagertop, $pagerbottom, $limit) = pager(50, $rescount['num'], "?finduid={$_GET['finduid']}&do=ticketshistory&");
				print("<table width='100%'>");

				print("<tr>
				<td class='colhead' align='center'>时间</td>
				<td class='colhead' align='center'>状态</td>
				<td class='colhead' align='center'>奖励</td>
				<td class='colhead' align='center'>用户</td>
				
				
				
				</tr>");
					if($rescount['num']){			
			
				$res = sql_query("SELECT * FROM lotteryhistory  where  user  like'{$_GET['finduid']}|%' or user  like '%|{$_GET['finduid']}|%' or user  like'%|{$_GET['finduid']}' or user  like'{$_GET['finduid']}'  ORDER BY opentime desc $limit");
				
				$bgarray=array('winner'=>'bgcolor=green','loser'=>'bgcolor=red');
				while ($row = mysql_fetch_assoc($res)){
				print("
				<td class='rowfollow nowrap' align='center'>".date("Y-m-d H:i:s",$row[opentime])."</td>
				<td class='rowfollow nowrap' align='center' {$bgarray[$row[type]]}>".($row[type])."</td>
				<td class='rowfollow nowrap' align='center'>".($row[amount])."</td>
				<td class='rowfollow nowrap' align='center'>".get_username($_GET['finduid'])."</td>
				</tr>");				
													}

				}else
				print("<tr><td class='owfollow nowrap' align='center' colspan='4'>森马都没有找到</td></tr>");
				
			print("<tr><td align='left' class='colhead' colspan='7'><form method='get' action='".$_SERVER['PHP_SELF']."'><b>查询用户(UID)记录:</b><input type='text' maxlength='10' size='10' name='finduid' value='".$_GET['finduid']."' /><input type='hidden'  name='do' value='ticketshistory' /><input type='submit' value='提交' /></form></td></tr>");
				
				print("</table>");
				print($pagerbottom);
	end_main_frame();
	stdfoot();
  }
?>
