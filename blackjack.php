<?php
require_once "include/bittorrent.php";
//require_once "include/user_functions.php";
dbconn();
loggedinorreturn();

//$lang = array_merge( load_language('global') );

$HTMLOUT='';


if ($CURUSER['class'] < UC_USER)
        stderr("Sorry", "您的等级太低",false);

$mb = 100*1024*1024;
$maxbj=100;
$now = sqlesc(date("Y-m-d H:i:s"));
$game = isset($_POST["game"]) ? htmlspecialchars(trim($_POST["game"])) : '';
$start = isset($_POST["start"]) ? htmlspecialchars(trim($_POST["start"])) : '';

if ($game)
{
        function cheater_check($arg)
        {       
                if ($arg)
                {
                        header('Location: '.$_SERVER['PHP_SELF']);
                        exit;
                }
        }
        
        $cardcount = 52;
        $points='';
  $showcards='';
  $aces='';
  
  
               // $playeres = sql_query("SELECT * FROM blackjack WHERE gameover !='yes' and userid = ".sqlesc($CURUSER['id'])." ORDER BY date DESC LIMIT 1");
               // $playerarr = mysql_fetch_assoc($playeres);
		$res = sql_query("SELECT COUNT(*) AS num FROM blackjack WHERE  gameover='yes' and userid = ".sqlesc($CURUSER['id']));
        $arr = mysql_fetch_assoc($res);
		if($arr['num'] >= $maxbj)cheater_check(true);		
				
        if ($start != 'yes')
        {
				
						
                $playeres = sql_query("SELECT * FROM blackjack WHERE gameover !='yes' and userid = ".sqlesc($CURUSER['id'])." ORDER BY date DESC LIMIT 1");
               $playerarr = mysql_fetch_assoc($playeres);
                if ($game == 'hit')
                $points = $aces = 0;
                $gameover = ($playerarr['gameover'] == 'yes' ? true : false);
                $HTMLOUT .= cheater_check($gameover && ($game == 'hit' ^ $game == 'stop'));
                $cards = $playerarr["cards"];
                $usedcards = explode(" ", $cards);

                $arr = array();
                foreach ($usedcards as $array_list)
                $arr[] = $array_list;
                foreach ($arr as $card_id)
                {
                $used_card = sql_query("SELECT * FROM cards WHERE id=".sqlesc($card_id));
                $used_cards = mysql_fetch_assoc($used_card);
                $showcards .= "<img src='cards/".$used_cards["pic"]."'  style=\"border: 1px\" alt='Cards' title='Cards' />";
                if ($used_cards["points"] > 1)
                $points += $used_cards['points'];
                else
                $aces++;
                }
          }
        
        if ($_POST["game"] == 'hit')
        {
                if ($start == 'yes')
                {
                        if ($CURUSER["uploaded"] < $mb)
                        stderr("Sorry ".$CURUSER["username"], "您现在需要 ".mksize($mb)." 上传.",false);
                        $required_ratio = 1;
                       if ($CURUSER["downloaded"] > 0)
                        $ratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 3);
                        elseif ($CURUSER["uploaded"] > 0)
                        $ratio = 999;
                        else
                        $ratio = 0;
                        if ($ratio < $required_ratio)
                        stderr("Sorry ".$CURUSER["username"], "您的分享率小于 ".$required_ratio."%.",false);
                        $res = sql_query("SELECT status, gameover FROM blackjack WHERE status='playing' and  userid = ".sqlesc($CURUSER['id']));
                        $arr = mysql_fetch_assoc($res);
                        
                        if ($arr['status'] == 'waiting')
                        stderr("Sorry", "您需要等待上一局结束",false);
                        elseif ($arr['status'] == 'playing')
                        stderr("Sorry", "您需要继续玩完上一盘.<form method='post' action='".$_SERVER['PHP_SELF']."'><input type='hidden' name='game' value='hit' readonly='readonly' /><input type='hidden' name='continue' value='yes' readonly='readonly' /><input type='submit' value='Continue old game' onclick=\"javascript:{this.disabled=true;this.form.submit()}\" /></form>",false);
        
                        $HTMLOUT .= cheater_check($arr['gameover'] == 'yes');
                        $cardids = array();
                        for ($i = 0; $i <= 1; $i++)
                        $cardids[] = rand(1, $cardcount);
                        foreach ($cardids as $cardid)
                        {
                        while (in_array($cardid, $cardids))
                        $cardid = rand(1, $cardcount);
                        $cardres = sql_query("SELECT points, pic FROM cards WHERE id='$cardid'");
                        $cardarr = mysql_fetch_assoc($cardres);
                        if ($cardarr["points"] > 1)
                        $points += $cardarr["points"];
                        else
                        $aces++;
                        $showcards .= "<img src='cards/".$cardarr['pic']."'  style=\"border: 1px\" alt='Cards' title='Cards' />";
                        $cardids2[] = $cardid;
                        }

                        for ($i = 0; $i < $aces; $i++)
                        $points += ($points < 11 && $aces - $i == 1 ? 11 : 1);
                        sql_query("INSERT INTO blackjack (userid, points, cards, date) VALUES(".sqlesc($CURUSER['id']).", '$points', '".join(" ",$cardids2)."', ".TIMENOW.")");
                        $playerarr['id'] = mysql_insert_id();
                        if ($points < 21)
                        {
                                $HTMLOUT .="<h1>Welcome, {$CURUSER['username']}!</h1>
                                <table cellspacing='0' cellpadding='3' >
                                <tr><td colspan='2'>
                                <table class='message' width='100%' cellspacing='0' cellpadding='5' bgcolor='white'>
                                <tr><td align='center'>".trim($showcards)."</td></tr>
                                <tr><td align='center'><b>Points = {$points}</b></td></tr>
                                <tr><td align='center'>
                                <form method='post' action='".$_SERVER['PHP_SELF']."'><input type='hidden' name='game' value='hit' readonly='readonly'  /><input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\" value='拿牌' /></form>
                                </td></tr>";
                                
                                if ($points >= 10)
                                {
                                $HTMLOUT .="<tr><td align='center'>
                                <form method='post' action='".$_SERVER['PHP_SELF']."'><input type='hidden' name='game' value='stop' readonly='readonly' /><input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value='停牌' /></form>
                                </td></tr>";
                                }
                                
                                $HTMLOUT .="</table></td></tr></table>";
                                 stdhead('Blackjack'); 
 print  $HTMLOUT ;
 stdfoot();
                                die();
                        }
                }
                elseif (($start != 'yes' && isset($_POST['continue']) != 'yes') && !$gameover)
                {
                        $HTMLOUT .= cheater_check(empty($playerarr));
                        $cardid = rand(1, $cardcount);
                        while (in_array($cardid, $arr))
                        $cardid = rand(1, $cardcount);
                        $cardres = sql_query("SELECT points, pic FROM cards WHERE id='$cardid'");
                        $cardarr = mysql_fetch_assoc($cardres);
                        $showcards .= "<img src='cards/".$cardarr['pic']."'  style=\"border: 1px\" alt='Cards' title='Cards' />";
                        
                        if ($cardarr["points"] > 1)
                        $points += $cardarr["points"];
                        else
                        $aces++;
                                
                        for ($i = 0; $i < $aces; $i++)
                  $points += ($points < 11 && $aces - $i == 1 ? 11 : 1);
                        sql_query("UPDATE blackjack SET points='$points', cards='".$cards." ".$cardid."' WHERE  status = 'playing'  and userid=".sqlesc($CURUSER['id']));
                  }
                
                if ($points == 21 || $points > 21)
                {
                        $waitres = sql_query("SELECT COUNT(userid) AS c FROM blackjack WHERE status = 'waiting' AND userid != ".sqlesc($CURUSER['id']));
                        $waitarr = mysql_fetch_assoc($waitres);
                        $HTMLOUT .="<h1>游戏结束</h1>
                        <table cellspacing='0' cellpadding='3' >
                        <tr><td colspan='2'>
                        <table width='100%' cellspacing='0' cellpadding='5' bgcolor='white'>
                        <tr><td align='center'>".trim($showcards)."</td></tr>
                        <tr><td align='center'><b>Points = {$points}</b></td></tr>";
                }

                if ($points == 21)
                {
                        if ($waitarr['c'] > 0)
                        {
                                $r = sql_query("SELECT bj.*, u.username FROM blackjack AS bj LEFT JOIN users AS u ON u.id=bj.userid WHERE bj.status='waiting' AND bj.userid != ".sqlesc($CURUSER['id'])." ORDER BY bj.date ASC LIMIT 1");
                                $a = mysql_fetch_assoc($r);

                                if ($a["points"] != 21)
                                {
                                        
                                        $winorlose = "赢局,你赢了 ".mksize($mb);
                                        sql_query("UPDATE users SET uploaded = uploaded + $mb, bjwins = bjwins + 1 WHERE id=".sqlesc($CURUSER['id']));
                                        sql_query("UPDATE users SET uploaded = uploaded - $mb, bjlosses = bjlosses + 1 WHERE id=".sqlesc($a['userid']));
                                        $msg = sqlesc("[url=blackjack.php]RT[/url]");
										$subject = sqlesc("BlackJack 结果 : 输局 (你有 ".$a['points']." 点, ".$CURUSER['username']." 有 21 点)");
                                }
                                else
                                {
                                
                                $winorlose = "平局";
                                $msg = sqlesc("[url=blackjack.php]RT[/url]");
								$subject = sqlesc("BlackJack 结果 : 平局 (你和 ".$CURUSER['username']."都有21点)");
                                }

                                sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, ".$a['userid'].", $now, $msg, $subject)");
                                sql_query("DELETE FROM blackjack WHERE id IN (".sqlesc($playerarr['id']).", ".sqlesc($a['id']).")");
                          $HTMLOUT .="<tr><td align='center'>你的对手是 ".$a["username"].",TA有 ".$a['points']." 点, $winorlose.<br />
						  <form method='post' action='".$_SERVER['PHP_SELF']."'>
 <input type='hidden' name='game' value='hit' readonly='readonly' />
 <input type='hidden' name='start' value='yes' readonly='readonly' />
 <input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value='再来一局' /></form></td></tr>";
                          }
                        else
                        {
                        sql_query("UPDATE blackjack SET status = 'waiting', date=".TIMENOW.", gameover = 'yes' WHERE  status = 'playing' and userid = ".sqlesc($CURUSER['id']));
                        $HTMLOUT .="<tr><td align='center'>暂时没有其他玩家, 所以你需要等待.<br />游戏结束你将会收到PM.<form method='post' action='".$_SERVER['PHP_SELF']."'>
 <input type='hidden' name='game' value='hit' readonly='readonly' />
 <input type='hidden' name='start' value='yes' readonly='readonly' />
 <input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value='再来一局' /></form></td></tr>";
                        }
                        
                        $HTMLOUT .="</table></td></tr></table><br />";
                         stdhead('Blackjack'); 
 print  $HTMLOUT ;
 stdfoot();
                }
                elseif ($points > 21)
                {
                        if ($waitarr['c'] > 0)
                        {
                                $r = sql_query("SELECT bj.*, u.username FROM blackjack AS bj LEFT JOIN users AS u ON u.id=bj.userid WHERE bj.status='waiting' AND bj.userid != ".sqlesc($CURUSER['id'])." ORDER BY bj.date ASC LIMIT 1");
                                $a = mysql_fetch_assoc($r);
                        
                                if ($a["points"] > 21)
                                {
                                        
                                        $winorlose = "平局";
                                        $msg = sqlesc("[url=blackjack.php]RT[/url]");
										$subject = sqlesc("BlackJack 结果 : 平局 (你和 ".$CURUSER['username']." 的点数都超过 21)");
                                }
                                else
                                {
                                        
                                        $winorlose = "输局,你失去了".mksize($mb);
                                        sql_query("UPDATE users SET uploaded = uploaded + $mb, bjwins = bjwins + 1 WHERE id=".sqlesc($a['userid']));
                                        sql_query("UPDATE users SET uploaded = uploaded - $mb, bjlosses = bjlosses + 1 WHERE id=".sqlesc($CURUSER['id']));
                                        $msg = sqlesc("[url=blackjack.php]RT[/url]");
										$subject = sqlesc("BlackJack 结果 : 赢局 (你有 ".$a['points']." 点, ".$CURUSER['username']." 的点数超过 21)");
                                }

                                sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, ".$a['userid'].", $now, $msg, $subject)");
                                sql_query("DELETE FROM blackjack WHERE id IN (".sqlesc($playerarr['id']).", ".sqlesc($a['id']).")");
                                
                                $HTMLOUT .="<tr><td align='center'>你的对手是 ".$a["username"].",TA有 ".$a['points']." 点, $winorlose.<br /><form method='post' action='".$_SERVER['PHP_SELF']."'>
 <input type='hidden' name='game' value='hit' readonly='readonly' />
 <input type='hidden' name='start' value='yes' readonly='readonly' />
 <input type='submit' value='再来一局' onclick=\"javascript:{this.disabled=true;this.form.submit()}\" /></form></td></tr>";
                        }
                        else
                        {
                                sql_query("UPDATE blackjack SET status = 'waiting', date=".TIMENOW.", gameover='yes' WHERE status = 'playing' and userid = ".sqlesc($CURUSER['id']));
                                
                        $HTMLOUT .="<tr><td align='center'>暂时没有其他玩家, 所以你需要等待.<br />游戏结束你将会收到PM.<form method='post' action='".$_SERVER['PHP_SELF']."'>
 <input type='hidden' name='game' value='hit' readonly='readonly' />
 <input type='hidden' name='start' value='yes' readonly='readonly' />
 <input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value='再来一局' /></form></td></tr>";
                        }
                        $HTMLOUT .="</table></td></tr></table><br />";
                
                         stdhead('Blackjack'); 
 print  $HTMLOUT ;
 stdfoot();
                }
                else
                {
                        $HTMLOUT .= cheater_check(empty($playerarr));
                        $HTMLOUT .="<h1>Welcome, {$CURUSER['username']}!</h1>
                        <table cellspacing='0' cellpadding='3' >
                        <tr><td colspan='2'>
                        <table class='message' width='100%' cellspacing='0' cellpadding='5' bgcolor='white'>
                        <tr><td align='center'>{$showcards}</td></tr>
                        <tr><td align='center'><b>Points = {$points}</b></td></tr>";
                        $HTMLOUT .="<tr>
      <td align='center'><form method='post' action='".$_SERVER['PHP_SELF']."'><input type='hidden' name='game' value='hit' readonly='readonly' /><input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\" value='拿牌' /></form></td>
      </tr>";
                        $HTMLOUT .="<tr>
      <td align='center'><form method='post' action='".$_SERVER['PHP_SELF']."'><input type='hidden' name='game' value='stop' readonly='readonly' /><input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\" value='停牌' /></form></td>
      </tr>";
                        $HTMLOUT .="</table></td></tr></table><br />";
                         stdhead('Blackjack'); 
 print  $HTMLOUT ;
 stdfoot();
                }
        }
        elseif ($_POST["game"] == 'stop')
        {
                $HTMLOUT .= cheater_check(empty($playerarr));
                $waitres = sql_query("SELECT COUNT(userid) AS c FROM blackjack WHERE status='waiting' AND userid != ".sqlesc($CURUSER['id']));
                $waitarr = mysql_fetch_assoc($waitres);
                $HTMLOUT .="<h1>Game over</h1>
                <table cellspacing='0' cellpadding='3' >
                <tr><td colspan='2'>
                <table class='message' width='100%' cellspacing='0' cellpadding='5' bgcolor='white'>
                <tr><td align='center'>{$showcards}</td></tr>
                <tr><td align='center'><b>Points = {$playerarr['points']}</b></td></tr>";
                
                if ($waitarr['c'] > 0)
                {
                        $r = sql_query("SELECT bj.*, u.username FROM blackjack AS bj LEFT JOIN users AS u ON u.id=bj.userid WHERE bj.status='waiting' AND bj.userid != ".sqlesc($CURUSER['id'])." ORDER BY bj.date ASC LIMIT 1");
                        $a = mysql_fetch_assoc($r);
                        
                        if ($a["points"] == $playerarr['points'])
                        {
                                
                                $winorlose = "平局";
                                $msg = sqlesc("[url=blackjack.php]RT[/url]");
								$subject = sqlesc("BlackJack 结果 : 平局 (你和".$CURUSER['username']."都有 ".$a['points']." 点)");
                        }
                        else
                        {
                                if (($a["points"] < $playerarr['points'] && $a['points'] < 21) || ($a["points"] > $playerarr['points'] && $a['points'] > 21))
                                {
                                        
                                        $msg = sqlesc("[url=blackjack.php]RT[/url]");
										$subject = sqlesc("BlackJack 结果 : 输局 (你有 ".$a['points']." 点, ".$CURUSER['username']." 有 ".$playerarr['points']." 点)");
                                        $winorlose = "赢局,你获得了 ".mksize($mb);
                                        $st_query = "+ ".$mb.", bjwins = bjwins +";
                                        $nd_query = "- ".$mb.", bjlosses = bjlosses +";
                                }
                                elseif (($a["points"] > $playerarr['points'] && $a['points'] < 21) || $a["points"] == 21 || ($a["points"] < $playerarr['points'] && $a['points'] > 21))
                                {
                                       
                                        $msg = sqlesc("[url=blackjack.php]RT[/url]");
                                        $winorlose = "输局,你失去了 ".mksize($mb);
										$subject = sqlesc("BlackJack 结果 : 赢局 (你有 ".$a['points']." 点, ".$CURUSER['username']." 有 ".$playerarr['points']." 点)");
                                        $st_query = "- ".$mb.", bjlosses = bjlosses +";
                                        $nd_query = "+ ".$mb.", bjwins = bjwins +";
                                }
                                
                        sql_query("UPDATE users SET uploaded = uploaded ".$st_query." 1 WHERE id=".sqlesc($CURUSER['id']));
                        sql_query("UPDATE users SET uploaded = uploaded ".$nd_query." 1 WHERE id=".sqlesc($a['userid']));
                        }

                        sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, ".$a['userid'].", $now, $msg, $subject)");
                        sql_query("DELETE FROM blackjack WHERE id IN (".sqlesc($playerarr['id']).", ".sqlesc($a['id']).")");
                        $HTMLOUT .="<tr><td align='center'>你的对手是 ".$a["username"].", TA有 ".$a['points']." 点, $winorlose.<br />
						 <form method='post' action='".$_SERVER['PHP_SELF']."'>
 <input type='hidden' name='game' value='hit' readonly='readonly' />
 <input type='hidden' name='start' value='yes' readonly='readonly' />
 <input type='submit' value='再来一局' onclick=\"javascript:{this.disabled=true;this.form.submit()}\" /></form></td></tr>";
                }
                else
                {
                sql_query("UPDATE blackjack SET status = 'waiting', date=".TIMENOW.", gameover='yes' WHERE status = 'playing' and  userid = ".sqlesc($CURUSER['id']));
                $HTMLOUT .="<tr><td align='center'>暂时没有其他玩家, 所以你需要等待.<br />游戏结束你将会收到PM.<form method='post' action='".$_SERVER['PHP_SELF']."'>
 <input type='hidden' name='game' value='hit' readonly='readonly' />
 <input type='hidden' name='start' value='yes' readonly='readonly' />
 <input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value='再来一局' /></form></td></tr>";
                }
                $HTMLOUT .="</table></td></tr></table><br />";
                 stdhead('Blackjack'); 
 print  $HTMLOUT ;
 stdfoot();
        }
}
else
{
                        $waitres = sql_query("SELECT COUNT(DISTINCT(id)) AS c FROM users WHERE  last_access  > ".sqlesc(date("Y-m-d H:i:s",TIMENOW- 60))."  AND page= ".sqlesc($_SERVER['PHP_SELF'])." and  id != ".sqlesc($CURUSER['id']));
                        $waitarr = mysql_fetch_assoc($waitres);
						
						$waitres = sql_query("SELECT COUNT(userid) AS c FROM blackjack WHERE   status ='waiting'  AND userid != ".sqlesc($CURUSER['id']));
                        $waitarr2 = mysql_fetch_assoc($waitres);
						
						$res = sql_query("SELECT COUNT(*) AS num FROM blackjack WHERE gameover='yes' and  userid = ".sqlesc($CURUSER['id']));
                        $arr = mysql_fetch_assoc($res);
                        
                        
                      
						
        $tot_wins = $CURUSER['bjwins'];
		$tot_losses = $CURUSER['bjlosses'];
        $tot_games = $tot_wins + $tot_losses;
        $win_perc = ($tot_losses==0?($tot_wins==0?"---":"100%"):($tot_wins==0?"0":number_format(($tot_wins/$tot_games)*100,1)).'%');
        $plus_minus = ($tot_wins-$tot_losses<0?'-':'').mksize((($tot_wins-$tot_losses>=0?($tot_wins-$tot_losses):($tot_losses-$tot_wins)))*$mb);
        $HTMLOUT .="<h1>Blackjack</h1>
        <table cellspacing='0' cellpadding='3' width='400'>
        <tr><td colspan='2' align='center'>
        <table class='message' width='100%' cellspacing='0' cellpadding='10' bgcolor='white'>
        <tr><td align='center'><img src='cards/tp.bmp'  style=\"border: 1px\" alt='' />&nbsp;<img src='cards/vp.bmp'  style=\"border: 1px\" alt='' /></td></tr>
        <tr><td align='left'>传统的21点游戏,您要抓足够接近21点，和对手对抗。<br />A在总分不超过21时作11，总分超过21则作1。J,Q,K作为10。<br /><br />
        <b>当前可用局数: </b>". $waitarr2['c']."+".($maxbj-$arr['num'])."<br/>
		<b>当前在线人数: </b>". $waitarr['c']."<br/>
		<b>提示:</b> 每局赌注为100MB上传量<br/>
		</td></tr>
        <tr><td align='center'>
        <form method='post' action='".$_SERVER['PHP_SELF']."'>
		
		

		
		".($arr['num'] >= $maxbj?
		"请等待前21局结束<br /><input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value='刷新' />":
		"<input type='hidden' name='game' value='hit' readonly='readonly' />
		<input type='hidden' name='start' value='yes' readonly='readonly' />
		<input type='submit' onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value='开牌'/>
		
		")."
		
		
		</form>
        </td></tr></table>
        </td></tr></table>
        <br /><br /><br />
  <table cellspacing='0' cellpadding='3' width='400'>
    <tr><td colspan='2' align='center'>
    <h1>个人记录</h1></td></tr>
    <tr><td align='left'><b>胜局</b></td><td align='center'><b>{$tot_wins}</b></td></tr>
	<tr><td align='left'><b>平局</b></td><td align='center'><b>待开发</b></td></tr>
    <tr><td align='left'><b>输局</b></td><td align='center'><b>{$tot_losses}</b></td></tr>
    <tr><td align='left'><b>游戏次数</b></td><td align='center'><b>{$tot_games}</b></td></tr>
    <tr><td align='left'><b>获胜率</b></td><td align='center'><b>{$win_perc}</b></td></tr>
    <tr><td align='left'><b>+/-</b></td><td align='center'><b>{$plus_minus}</b></td></tr>
    </table><br /><b><div align=\"center\"><a href=\"/bjstats.php\">历史统计</a></div></b> ";
      stdhead("Blackjack $maxbj limited");
  print 	  $HTMLOUT ;
  stdfoot();
}
?>