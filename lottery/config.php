<?php
/*made by putyn */
if(!defined('IN_LOTTERY'))
  die('You can\'t access this file directly!');

if($CURUSER['class'] < UC_SYSOP)
  stderr('Err','What you doing here dude?');

//get the config from db
$lconf = sql_query('SELECT * FROM lottery_config') or sqlerr(__FILE__,__LINE__);
while($ac = mysql_fetch_assoc($lconf))
  $lottery_config[$ac['name']] = $ac['value'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    //can't be 0
    foreach(array('ticket_amount'=>0,'class_allowed'=>1,'user_tickets'=>0,'end_date'=>0) as $key=>$type) {
      if(isset($_POST[$key]) && ($type == 0 && $_POST[$key] == 0 || $type == 1 && count($_POST[$key]) == 0))
      stderr('Err','You forgot to fill some data');
    }
    foreach($lottery_config as $c_name=>$c_value)
    if(isset($_POST[$c_name]) && $_POST[$c_name] != $c_value)
      $update[] = '('.sqlesc($c_name).','.sqlesc(is_array($_POST[$c_name]) ? join('|',$_POST[$c_name]) : $_POST[$c_name]).')';
    
    if(sql_query('INSERT INTO lottery_config(name,value) VALUES '.join(',',$update).' ON DUPLICATE KEY update value=values(value)'))
      stderr('Success','Lottery configuration was saved! Click <a href=\'lottery.php?do=config\'>here to get back</a>',false);
      else
      stderr('Error','There was an error while executing the update query. Mysql error: '.mysql_error());
  exit;
}
$html = "";

  $html .="";
  $html .="<form action='lottery.php?do=config' method='post'>
  <table width='100%' border='1' cellpadding='5' cellspacing='0' >
	<tr>
    <td width='50%' class='table' align='left'>乐透有效</td>
    <td class='table' align='left'>Yes <input class='table' type='radio' name='enable' value='1' ".($lottery_config['enable'] ? 'checked=\'checked\'' : '')." /> No <input class='table' type='radio' name='enable' value='0' ".(!$lottery_config['enable'] ? 'checked=\'checked\'' : '')." />
    </td>
  </tr>
	<tr>
    <td width='50%' class='table' align='left'>只使用奖金(否则还使用奖池金)</td><td class='table' align='left'>Yes <input class='table' type='radio' name='use_prize_fund' value='1' ".($lottery_config['use_prize_fund'] ? 'checked=\'checked\'' : '')." /> No <input class='table' type='radio' name='use_prize_fund' value='0' ".(!$lottery_config['use_prize_fund'] ? 'checked=\'checked\'' : '')." /></td>
  </tr>
	<tr>
   <td width='50%' class='table' align='left'>总奖金</td>
   <td class='table' align='left'><input type='text' name='prize_fund' value='{$lottery_config['prize_fund']}' /></td>
  </tr>
	<tr>
   <td width='50%' class='table' align='left'>每注价格</td>
   <td class='table' align='left'><input type='text' name='ticket_amount' value='{$lottery_config['ticket_amount']}' /></td>
  </tr>
	<tr>
    <td width='50%' class='table' align='left'>奖励条件</td>
    <td class='table' align='left'><select name='ticket_amount_type'><option value='seedbonus' selected='selected'>seedbonus</option></select></td>
  </tr>
	<tr><td width='50%' class='table' align='left'>每个用户最大下注数</td><td class='table' align='left'><input type='text' name='user_tickets' value='{$lottery_config['user_tickets']}' /></td>
  </tr>
	<tr><td width='50%' class='table' align='left' valign='top'>允许用户</td><td class='table' align='left'>";
  for($i=UC_USER;$i<=UC_SYSOP;$i++)
    $html .= "<label for='c{$i}'><input checked=\'checked\' type='checkbox' value='{$i}' id='c{$i}' name='class_allowed[]'/> ".get_user_class_name($i)."</label><br/>";
  $html .= "</td></tr>";
  $html .= "
   <tr>
    <td width='50%' class='table' align='left'>总获奖用户</td>
    <td class='table' align='left'><input type='text' name='total_winners' value='{$lottery_config['total_winners']}' /></td>
  </tr>
    <tr>
      <td width='50%' class='table' align='left'>每期时间</td><td class='table' align='left'><select name='end_date'>
        ";
        for($i=1;$i<=7;$i++)
          $html .= "<option value='".((84600*$i))."'>{$i} days</option>";
  $html .= "</select></td>
    </tr>
    <tr>
      <td colspan='2' class='table' align='center'><input type='submit' value='Apply changes' /></td>
    </tr>";
  
  $html .= "</table></form>";


stdhead('Lottery configuration');
begin_main_frame();
 begin_frame('Lottery configuration');
print($html);
end_frame();
end_main_frame();
stdfoot();
?>
