<?php
/*made by putyn */
require "include/bittorrent.php";
dbconn();
loggedinorreturn();

 // $lang = array_merge( load_language('global') );
  define('IN_LOTTERY','yeah');
  $lottery_root = "lottery/";
  $valid = array('config'=>array('minclass'=>UC_SYSOP,'file'=>$lottery_root.'config.php'),
                 'viewtickets'=>array('minclass'=>UC_USER,'file'=>$lottery_root.'viewtickets.php'),
                 'tickets'=> array('minclass'=>UC_USER,'file'=>$lottery_root.'tickets.php'),
				 'ticketshistory'=> array('minclass'=>UC_USER,'file'=>$lottery_root.'ticketshistory.php'),
                );
  $do = isset($_GET['do']) && in_array($_GET['do'],array_keys($valid)) ? $_GET['do'] : '';

  switch(true) {
    case $do == 'config' && $CURUSER['class'] >= $valid['config']['minclass'] :
      require_once($valid['config']['file']);
    break;
    case $do == 'viewtickets' && $CURUSER['class'] >= $valid['viewtickets']['minclass'] :
      require_once($valid['viewtickets']['file']);
    break;
    case $do == 'tickets' && $CURUSER['class'] >= $valid['tickets']['minclass'] :
      require_once($valid['tickets']['file']);
    break;
	case $do == 'ticketshistory' && $CURUSER['class'] >= $valid['ticketshistory']['minclass'] :
      require_once($valid['ticketshistory']['file']);
    break;
    default : 
      $html = "";
      //get config from database 
      $lconf = sql_query('SELECT * FROM lottery_config') or sqlerr(__FILE__,__LINE__);
      while($ac = mysql_fetch_assoc($lconf))
        $lottery_config[$ac['name']] = $ac['value'];
      if(!$lottery_config['enable'])
        $html .= stdmsgnoprint('Sorry','Lottery is closed a the moment');
      elseif($lottery_config['start_date']+$lottery_config['end_date'] > TIMENOW)
	   $html .= stdmsgnoprint('乐透进行中'," <b>".(date("m-d H:i:s",($lottery_config['start_date'])))."</b> 至 <b>".(date("m-d H:i:s",($lottery_config['start_date']+$lottery_config['end_date'])))."</b> <BR />剩余时间: <span style='color:#ff0000;'>".mkprettytime($lottery_config['start_date']+$lottery_config['end_date']-TIMENOW)."</span>");
      else 
	  $html .= stdmsgnoprint('本期乐透已经结束',"请等待开奖");

	  //get last lottery data:
	  
      $uids = (strpos($lottery_config['lottery_winners'],'|') ? explode('|',$lottery_config['lottery_winners']) : $lottery_config['lottery_winners']);
	  if(!$uids)$uids =0;
      $last_winners = array();
        //$qus = sql_query('SELECT id,username FROM users WHERE id '.(is_array($uids) ? 'IN ('.join(',',$uids).')' : '='.$uids)) or sqlerr(__FILE__,__LINE__);
		  
		  if(is_array($uids)){
			foreach($uids as $winner){$last_winners[] = get_username($winner);$last_winnersid['@'][]="[@".$winner."]";$last_winnersid['uid'][]="[uid".$winner."]";};
		  }else
			$last_winners[] = get_username($uids);
	
	
			
			
      $html .= stdmsgnoprint('上一期乐透',"<ul style='text-align:left;'>
        <li>中奖人: ".join(', ',$last_winners)."</li>
		<li>乐透号: ".$lottery_config['lottery_winnertid']."</li>
        <li>获得奖金: ".$lottery_config['lottery_winners_amount']."</li>
		<!--\n[quote=".(date("Y-m-d H:i:s",($lottery_config['start_date'])))."]".@join('',$last_winnersid['@'])."[/quote]
		\n[quote=".(date("Y-m-d H:i:s",($lottery_config['start_date'])))."]".@join('',$last_winnersid['uid'])."[/quote]\n-->
        <li>时间: ".(date("Y-m-d H:i:s",($lottery_config['start_date'])))."</li>
      </ul>");
      $html .= "<p style='text-align:center'><a href='lottery.php?do=viewtickets'>[查看本期参与信息]</a>&nbsp;&nbsp;<a href='lottery.php?do=ticketshistory'>[历史]</a>&nbsp;&nbsp;".($CURUSER['class'] >= UC_SYSOP ? "<a href='lottery.php?do=config'>[设置]</a>&nbsp;&nbsp;" : "")."<a href='lottery.php?do=tickets'>[购买]</a></p>";

 
	  
	  stdhead('乐透中心');
	  begin_main_frame();
      print($html);    
	  end_main_frame();
	  stdfoot();
  }
?>
