<?php
/*made by putyn */
if(!defined('IN_LOTTERY'))
  die('You can\'t access this file directly!');
//get config from database 
$lconf = sql_query('SELECT * FROM lottery_config') or sqlerr(__FILE__,__LINE__);
while($ac = mysql_fetch_assoc($lconf))
  $lottery_config[$ac['name']] = $ac['value'];
  
  if(!$lottery_config['enable'])
  stderr('Sorry','Lottery is closed');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  
  $tickets = isset($_POST['tickets']) ? 0+$_POST['tickets'] : '';
  if(!$tickets)
    stderr('Hmm','您下多少注?');
  $user_tickets = get_row_count('tickets','where user='.$CURUSER['id']);
  if($user_tickets + $tickets > $lottery_config['user_tickets'])
    stderr('Hmmm','已达购买上限');
  if($CURUSER['seedbonus'] < $tickets*$lottery_config['ticket_amount'])
    stderr('Hmmmm','您需要更多的魔力值');
  
  for($i=1;$i<=$tickets;$i++)
    $t[] = '('.$CURUSER['id'].')';
  if(sql_query('INSERT INTO tickets(user) VALUES '.join(', ',$t))) {
    sql_query('UPDATE users SET seedbonus = seedbonus - '.($tickets*$lottery_config['ticket_amount']).' WHERE id = '.$CURUSER['id']);
	$Cache->delete_value('lotterystats',true);
    stderr('成功','您买了'.$tickets.'注乐透, 您现在共有'.($tickets+$user_tickets).'注乐透');
  }
  else
    stderr('错误','There was an error with the update query, mysql error: '.mysql_error());
exit;
}

  $classes_allowed = (strpos($lottery_config['class_allowed'],'|') ? explode('|',$lottery_config['class_allowed']) : $lottery_config['class_allowed']);
  if(!(is_array($classes_allowed) ? in_array($CURUSER['class'],$classes_allowed) : $CURUSER['class'] == $classes_allowed))
    stderr('错误','您的等级暂时不允许下注');

  //some default values
  $lottery['total_pot'] = 0;
  $lottery['current_user'] = array();
  $lottery['current_user']['tickets'] = array();
  $lottery['total_tickets'] = 0;
  //select the total amount of tickets
  $qt = sql_query('SELECT id,user FROM tickets ORDER BY id ASC ') or sqlerr(__FILE__,__LINE__);
  while($at = mysql_fetch_assoc($qt)){
    $lottery['total_tickets'] +=1;
    if($at['user'] == $CURUSER['id'])
    $lottery['current_user']['tickets'][] = $at['id'];
  }
  //set the current user total tickets amount
  $lottery['current_user']['total_tickets'] = count($lottery['current_user']['tickets']);
  //check if the prize setting is set to calculate the totat pot
  if($lottery_config['use_prize_fund'])
    $lottery['total_pot'] = $lottery_config['prize_fund'];
  else
    $lottery['total_pot'] = $lottery_config['ticket_amount'] * $lottery['total_tickets']+$lottery_config['prize_fund'];
  //how much the winner gets
  $lottery['per_user'] = round($lottery['total_pot']/$lottery_config['total_winners'],2);
  //how many tickets could the user buy
  $lottery['current_user']['could_buy'] = $lottery['current_user']['can_buy'] = $lottery_config['user_tickets'] - $lottery['current_user']['total_tickets'];
  //if he has less bonus points calculate how many tickets can he buy with what he has
  if($CURUSER['seedbonus'] < ($lottery['current_user']['could_buy']*$lottery_config['ticket_amount']))
     for($lottery['current_user']['can_buy'];$CURUSER['seedbonus']<($lottery_config['ticket_amount']*$lottery['current_user']['can_buy']);--$lottery['current_user']['can_buy']);
  //check if the lottery ended if the lottery ended don't allow the user to buy more tickets or if he has already bought the max tickets 
  if(time() > $lottery_config['start_date']+$lottery_config['end_date'] || $lottery_config['user_tickets'] <= $lottery['current_user']['total_tickets'])
    $lottery['current_user']['can_buy'] = 0;

  //print('<pre>'.print_r($lottery,1));
  $html = "";
  $html .= "<ul style='text-align:left;'>
	<li>珍爱生命,远离赌博</li>
    <li>下注后不退款</li>
    <li>每注乐透消耗 <b>".$lottery_config['ticket_amount']."</b> 魔力值</li>
    <li>本期乐透将于: <b>".(date("m-d H:i:s",($lottery_config['start_date']+$lottery_config['end_date'])))."</b>开奖</li>
    <li>本期乐透设置获奖人数为 <b>".$lottery_config['total_winners']."</b> 人</li>
    <li>每位中奖会员会获得 <b>".$lottery['per_user']."</b> 魔力值,并用短信告知</li>
	<li>买的越多,中奖机会也越高!</li>";
  if(!$lottery_config['use_prize_fund'])
  $html .="<li>总注数越多,奖金越高!</li>";
  if(count($lottery['current_user']['tickets']))
  $html .="<li>您目前持有的乐透号为 : <b>".join('</b>, <b>',$lottery['current_user']['tickets'])."</b></li>";
  $html .= "</ul><hr/>
   <table width='400' class='main' align='center' border='1' cellspacing='0' cellpadding='5'>
    <tr>
      <td class='table'>奖池金</td>
      <td class='table' align='right'>".($lottery['total_pot']-$lottery_config['prize_fund'])."+".$lottery_config['prize_fund']."</td>
    </tr>
    <tr>
      <td class='table'>目前总注数</td>
      <td class='table' align='right'>".$lottery['total_tickets']." 注</td>
    </tr>
    <tr>
      <td class='table'>您已经购买</td>
      <td class='table' align='right'>".$lottery['current_user']['total_tickets']." 注</td>
    </tr>
    <tr>
      <td class='table'>您还能购买</td>
      <td class='table' align='right'>".( $lottery['current_user']['can_buy']."注")."</td>
    </tr>";
   if($lottery['current_user']['can_buy'] > 0)
    $html .= "
      <tr>
        <td class='table' colspan='2' align='center'> 
          <form action='lottery.php?do=tickets' method='post'>
              <input type='text' size='5' name='tickets' /><input type='submit' value='购买' />
          </form>
        </td>
      </tr>";
  $html .="</table>";
	$html .= "<p style='text-align:center'><a href='lottery.php?do=viewtickets'>[查看本期参与信息]</a>&nbsp;&nbsp;</p>";

  
  stdhead('购买乐透');
  begin_main_frame();
  begin_frame('购买乐透',true);
  
 
  print($html);
   end_frame();
  end_main_frame();
  stdfoot();
  
?>
