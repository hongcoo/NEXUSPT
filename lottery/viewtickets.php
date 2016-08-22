<?php
/*made by putyn */
if(!defined('IN_LOTTERY'))
  die('You can\'t access this file directly!');

ini_set('display_errors',1);

//get config from database 
$lconf = sql_query('SELECT * FROM lottery_config') or sqlerr(__FILE__,__LINE__);
while($ac = mysql_fetch_assoc($lconf))
  $lottery_config[$ac['name']] = $ac['value'];


if(!$lottery_config['enable'])
  stderr('Sorry','Lottery is closed');

  
$Cache->new_page('lotterystats', 600, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row(); 
$html ="";
$html .= "<h2>本期乐透时间:<b>".(date("m-d H:i:s",($lottery_config['start_date'])))."</b> 至 <b>".(date("m-d H:i:s",($lottery_config['start_date']+$lottery_config['end_date'])))."</b> 剩余时间: <span style='color:#ff0000;'>".mkprettytime($lottery_config['start_date']+$lottery_config['end_date']-TIMENOW)."</span></h2>";

$qs = sql_query('SELECT count(t.id) as tickets , u.username, u.id, u.seedbonus FROM tickets as t LEFT JOIN users as u ON u.id = t.user GROUP BY u.id ORDER BY tickets DESC, username ASC') or sqlerr(__FILE__,__LINE__);
if(!mysql_num_rows($qs))
$html .= "<h2>还暂时没有人购买</h2>";
else {
  $html .= "<table width='80%' cellpadding='5' cellspacing='0' border='1' align='center'>
    <tr>
      <td width='100%'>用户名</td>
      <td style='white-space:nowrap;'>下注数</td>
      <td style='white-space:nowrap;'>当前魔力值</td>
    </tr>";
    while($ar = mysql_fetch_assoc($qs))
      $html .= "<tr>
                  <td align='left'><a href='userdetails.php?id={$ar['id']}'>{$ar['username']}</a></td>
                  <td align='center'>{$ar['tickets']}</td>
                  <td align='center'>{$ar['seedbonus']}</td>
        </tr>";
  $html .= "</table>";
}

  $html .= "<p style='text-align:center'><a href='lottery.php?do=tickets'>[购买]</a></p>";
	print ($html);
	$Cache->end_whole_row();
	$Cache->cache_page();
	}
	
	
  stdhead('乐透彩券');
   begin_main_frame();
   begin_frame('当前状态');
  echo $Cache->next_row();
  end_frame();
  end_main_frame();
  stdfoot();
?>
