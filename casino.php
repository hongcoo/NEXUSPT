<?php
require ("include/bittorrent.php");

//== Updated casino.php by Bigjoos
dbconn(false);
loggedinorreturn();

function trcasino($x,$y,$noesc=0,$relation='') {
	if ($noesc)
	$a = $y;
	else {
		$a = htmlspecialchars($y);
		$a = str_replace("\n", "<br />\n", $a);
	}
	return ("<tr".( $relation ? " relation = \"$relation\"" : "")."><td class=\"rowhead nowrap\" valign=\"top\" align=\"right\">$x</td><td class=\"rowfollow\" valign=\"top\" align=\"left\">".$a."</td></tr>\n");
}

function write_casinolog($text)
{
	$text = sqlesc($text);
	$added = sqlesc(date("Y-m-d H:i:s"));
	sql_query("INSERT INTO casinolog (added, txt) VALUES($added, $text)");
}


//$lang = array_merge( load_language('global') );
//== Config
$amnt=0;
$nobits = 0;
$dummy ='';
$abcdefgh =0;
$player = UC_POWER_USER;
$mb_basic = 1024 * 1024; 
$max_download_user = $mb_basic * 1024 * 5000; //= 25 Gb
$max_download_global = $mb_basic * $mb_basic * 500; //== 2.5 Tb
$required_ratio = 2; //== Min ratio
$user_everytimewin_mb = $mb_basic * 10; //== Means users that wins under 70 mb get a cheat_value of 0 -> win every time
$cheat_value = 1; //== Higher value -> less winner8
$cheat_breakpoint = 10; //== Very important value -> if (win MB > max_download_global/cheat_breakpoint)
$cheat_value_max = 3; //== Then cheat_value = cheat_value_max -->> i hope you know what i mean. ps: must be higher as cheat_value.
$cheat_ratio_user = .4; //== If casino_ratio_user > cheat_ratio_user -> $cheat_value = rand($cheat_value,$cheat_value_max)
$cheat_ratio_global = .4; //== Same as user just global
$win_amount = 2; //== How much do the player win in the first game eg. bet 300, win_amount=3 ---->>> 300*3= 900 win
$win_amount_on_number = 5; //== Same as win_amount for the number game
$show_real_chance = false; //== Shows the user the real chance true or false
$bet_value1 = $mb_basic * 32;
$bet_value2 = $mb_basic * 63; //== This is in MB but you can also choose gb or tb
$bet_value3 = $mb_basic * 128;
$bet_value4 = $mb_basic * 256;
$bet_value5 = $mb_basic * 512;
$bet_value6 = $mb_basic * 1024;
$bet_value7 = $mb_basic * 2048;

$maxtimes=10;//每天次数
//== Config game 3
$minclass = $player; //== Lowest class allowed to play
$maxusrbet = '3'; //==Amount of bets to allow per person
$maxtotbet = '30'; //== Amount of total open bets allowed
$alwdebt = 'n'; //== Allow users to get into debt
$writelog = 'y'; //== Writes results to log
$delold = 'n'; //== Clear bets once finished - watch this as the table may go huge if off.
$sendfrom = '0'; //== The id of the user which notification PM's are noted as sent from
$casino = "casino.php"; //== Name of file
//== End of Config

         //== Reset user gamble stats!
         $hours = 2; //== Hours to wait after using all tries, until they will be restarted
         $dt = time() - $hours * 3600;
        /* $res = sql_query("SELECT userid, trys, date, enableplay FROM casino WHERE date < $dt AND trys >= '51' AND enableplay = 'yes'");
         while ($arr = mysql_fetch_assoc($res)) {
         sql_query("UPDATE casino SET trys='0' WHERE userid=".sqlesc($arr['userid'])."") or sqlerr(__FILE__, __LINE__);
         }*/

         if ($CURUSER['class'] < $player)
         stderr("出错啦", "".get_user_class_name($player)." 以上等级才允许进入游戏",false);

         $query = "SELECT * from casino where userid = ".sqlesc($CURUSER['id'])."";
         $result = sql_query($query) or sqlerr(__FILE__, __LINE__);
         if (mysql_affected_rows() != 1) {
         sql_query("INSERT INTO casino (userid, win, lost, trys, date) VALUES(" .sqlesc($CURUSER["id"]).", 0, 0, 0, '" . time() . "')") or mysql_error();
         $result = sql_query($query) or sqlerr(__FILE__, __LINE__);
         }

         $row = mysql_fetch_assoc($result);
         $user_win = $row["win"];
         $user_lost = $row["lost"];
         $user_trys = $row["trys"];
         $user_date = $row["date"];
         $user_deposit = $row["deposit"];
         $user_enableplay = $row["enableplay"];

         if ($user_enableplay == "no")
         stderr("出错啦", "".htmlspecialchars($CURUSER["username"])." 你被禁止玩本游戏",false);

         if (($user_win - $user_lost) > $max_download_user)
         stderr("出错啦","".htmlspecialchars($CURUSER["username"])." 本游戏你已经获得了最多的上传量",false);

         if ($CURUSER["downloaded"] > 0)
         $ratio = ($CURUSER["uploaded"] / $CURUSER["downloaded"]);
         else
         if ($CURUSER["uploaded"] > 0)
         $ratio = 999;
         else
         $ratio = 0;
         if ($ratio < $required_ratio)
         stderr("出错啦", "".htmlspecialchars($CURUSER["username"])." 你的分享率低于 {$required_ratio}",false);

          $global_down2 = sql_query(" select (sum(win)-sum(lost)) as globaldown,(sum(deposit)) as globaldeposit, sum(win) as win, sum(lost) as lost from casino") or sqlerr(__FILE__, __LINE__);
          $row = mysql_fetch_assoc($global_down2);
          $global_down = $row["globaldown"];
          $global_win = $row["win"];
          $global_lost = $row["lost"];
          $global_deposit = $row["globaldeposit"];

          if ($user_win > 0)
          $casino_ratio_user = ($user_lost / $user_win);
          else
          if ($user_lost > 0)
          $casino_ratio_user = 999;
          else
          $casino_ratio_user = 0.00;

          if ($global_win > 0)
          $casino_ratio_global =($global_lost / $global_win);
          else
          if ($global_lost > 0)
          $casino_ratio_global = 999;
          else
          $casino_ratio_global = 0.00;
    
          if ($user_win < $user_everytimewin_mb)
          $cheat_value = 2;
          else {
          if ($global_down > ($max_download_global / $cheat_breakpoint))
          $cheat_value = $cheat_value_max;
          if ($casino_ratio_global < $cheat_ratio_global)
          $cheat_value = rand($cheat_value, $cheat_value_max);

          if (($user_win - $user_lost) > ($max_download_user / $cheat_breakpoint))
          $cheat_value = $cheat_value_max;
          if ($casino_ratio_user < $cheat_ratio_user)
          $cheat_value = rand($cheat_value, $cheat_value_max);
          }

          if ($global_down > $max_download_global)
          stderr("出错啦", "".htmlspecialchars($CURUSER["username"])." 全局获得上传量达到最大 " . htmlspecialchars(mksize($max_download_global)),false);
           
           //== Updated post color/number by pdq
           $goback = "<a href='$casino'>返回</a>";
           $color_options = array('red' => 1, 'black' => 2);
           $number_options = array(1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1);
           $betmb_options = array($bet_value1/$mb_basic => 1, $bet_value2/$mb_basic => 1, $bet_value3/$mb_basic => 1, $bet_value4/$mb_basic => 1, $bet_value5/$mb_basic => 1, $bet_value6/$mb_basic => 1, $bet_value7/$mb_basic => 1);
           $post_color = (isset($_POST['color']) ? $_POST['color'] : '');
           $post_number = (isset($_POST['number']) ? $_POST['number'] : '');
           $post_betmb = (isset($_POST['betmb']) ? $_POST['betmb'] : '');
           if (isset($color_options[$post_color]) && isset($number_options[$post_number]) || isset($betmb_options[$post_betmb/$mb_basic])) 
           {
           $betmb = 0 + $_POST["betmb"];
           if (isset($_POST["number"])) {
           $win_amount = $win_amount_on_number;
           $cheat_value = $cheat_value + 5;
           $winner_was = 0 + $_POST["number"];
           } else
           $winner_was = $_POST["color"];
           $win = $win_amount * $betmb;

           if ($CURUSER["uploaded"] < $betmb)
           stderr("出错啦 ".htmlspecialchars($CURUSER["username"])." 你没有 ".htmlspecialchars(mksize($betmb)."上传量"),false);

           if (rand(0, $cheat_value) == $cheat_value) {
		 if(get_single_value("casino", "times", "WHERE userid = ".sqlesc($CURUSER["id"])) > $maxtimes)
		 stderr("出错", "每天只允许 $maxtimes 次赌场游戏",false);
		 
           sql_query("UPDATE users SET uploaded = uploaded + ".sqlesc($win)." WHERE id=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
           sql_query("UPDATE casino SET date = '".time()."', trys = trys + 1,times=times + 1, win = win + ".sqlesc($win)."  WHERE userid=" . sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
           stderr("Yes", "".htmlspecialchars($winner_was)." 是本次结果 ".htmlspecialchars($CURUSER["username"])." 你押对了并且获得 " . htmlspecialchars(mksize($win))."&nbsp;&nbsp;&nbsp;$goback",false);
           } else {
           if (isset($_POST["number"])) {
           do {
           $fake_winner = rand(1, 6);
           } while ($_POST["number"] == $fake_winner);
           } else {
           if ($_POST["color"] == "black")
           $fake_winner = "red";
           else
           $fake_winner = "black";
           }
        if(get_single_value("casino", "times", "WHERE userid = ".sqlesc($CURUSER["id"])) > $maxtimes)
		 stderr("出错", "每天只允许 $maxtimes 次赌场游戏",false);
           sql_query("UPDATE users SET uploaded = uploaded - ".sqlesc($betmb)." WHERE id=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
           sql_query("UPDATE casino SET date = '" . time() . "', trys = trys + 1,times=times+1,lost = lost + ".sqlesc($betmb)." WHERE userid=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
           stderr("出错啦", "".htmlspecialchars($fake_winner)." 是本次结果而不是 ".htmlspecialchars($winner_was).", ".htmlspecialchars($CURUSER["username"])." 你失去 ".htmlspecialchars(mksize($betmb))."&nbsp;&nbsp;&nbsp;$goback",false);
           }
           } else {
           //== Get user stats
           $betsp = sql_query("SELECT challenged FROM casino_bets WHERE userid =".sqlesc($CURUSER['id'])."");
           $openbet = 0;
           while ($tbet2 = mysql_fetch_assoc($betsp)) {
           if ($tbet2['challenged'] == 'empty')
           $openbet++;
           }
           //== Convert bet amount into bits
           if (isset($_POST['unit'])) {
           if (0 + $_POST["unit"] == '1')
           $nobits = $amnt * $mb_basic;
            else
           $nobits = $amnt * $mb_basic * 1024;
           }

           if ($CURUSER['uploaded'] == 0 || $CURUSER['downloaded'] == 0)
           $ratio = '0';
           else
           $ratio = ($CURUSER['uploaded'] - $nobits) / $CURUSER['downloaded'];
           $time = time();
           //== Take Bet
           if (isset($_GET["takebet"])) {
           $betid = 0 + $_GET["takebet"];
           $random = rand(0, 1);
           $loc = sql_query("SELECT * FROM casino_bets WHERE id = ".sqlesc($betid)."");
           $tbet = mysql_fetch_assoc($loc);
           $nogb = mksize($tbet['amount']);

            if ($CURUSER['id'] == $tbet['userid'])
            stderr("出错啦", "你想和自己打赌?&nbsp;&nbsp;&nbsp;$goback",false);
            elseif ($tbet['challenged'] != "empty")
            stderr("出错啦", "某人已经接受本次赌注了&nbsp;&nbsp;&nbsp;$goback",false);

            if ($CURUSER['uploaded'] < $tbet['amount']) {
            $debt = $tbet['amount'] - $CURUSER['uploaded'];
            $newup = $CURUSER['uploaded'] - $debt;
            }

            if (isset($debt) && $alwdebt != 'y')
            stderr("出错啦", "<h2>你还需要 ".htmlspecialchars(mksize(($tbet['amount']-$CURUSER['uploaded'])))." 上传</h2>&nbsp;&nbsp;&nbsp;$goback",false);

            if ($random == 1) {
			$temptbet=$tbet['amount']*9/10;   
			sql_query("UPDATE users SET uploaded = uploaded+".sqlesc($temptbet)." WHERE id = " . sqlesc($CURUSER['id']) . "") or sqlerr(__FILE__, __LINE__);
			//sql_query("UPDATE users SET uploaded = uploaded-".sqlesc($tbet['amount'])." WHERE id = " . sqlesc($tbet['userid']) . "") or sqlerr(__FILE__, __LINE__);
            sql_query("UPDATE casino SET deposit = deposit-".sqlesc($tbet['amount'])." WHERE userid = " . sqlesc($tbet['userid']) . "") or sqlerr(__FILE__, __LINE__);
            if (mysql_affected_rows() == 0)
            sql_query("INSERT INTO casino (userid, date, deposit) VALUES (".sqlesc($tbet['userid']).", '$time', -" . sqlesc($tbet['amount']) . ")") or sqlerr(__FILE__, __LINE__);
			sql_query("UPDATE casino_bets SET challenged = ".sqlesc($CURUSER['username']).", winner = ".sqlesc($CURUSER['username'])." WHERE id =".sqlesc($betid)."") or sqlerr(__FILE__, __LINE__);
            $subject = sqlesc("Casino Results");
            //sql_query("INSERT INTO messages (subject, id, sender, receiver, added, msg, unread) VALUES ($subject,'', '$sendfrom', ".sqlesc($tbet['userid']).", $time, '本赌局你输了" . htmlspecialchars($CURUSER['username']) . " 从你那里获得了 " . htmlspecialchars($nogb) . " 上传!' , 'yes')") or sqlerr(__FILE__, __LINE__);
			sql_query("INSERT INTO messages (subject,  sender, receiver, added, msg, unread) VALUES ('Casino结果:本赌局你输了," . htmlspecialchars($CURUSER['username']) . " 获得了你的 " . htmlspecialchars($nogb) . " 上传!!', '$sendfrom', ".sqlesc($tbet['userid']).", ".sqlesc(date("Y-m-d H:i:s")).", ".sqlesc("[url=casino.php]RT[/url]")." , 'yes')") or sqlerr(__FILE__, __LINE__);
            if($writelog == 'y')
            write_casinolog("开注成功! ".$CURUSER['username']." 拿走了 $tbet[proposed] $nogb 上传");
            if ($delold == 'y')
            sql_query("DELETE FROM casino_bets WHERE id =".sqlesc($tbet['id'])."") or sqlerr(__FILE__, __LINE__);
            stderr("你赢了", "<h2>你本次赌局从<a href='userdetails.php?id=$tbet[userid]'>$tbet[proposed]'s</a>赢得了, ".htmlspecialchars($nogb)." !</h2>&nbsp;&nbsp;&nbsp;$goback",false);
            exit();
            } else {
            if (empty($newup))
            $newup = $CURUSER['uploaded'] - $tbet['amount']*11/10;
            $newup2 = $tbet['amount']*2;//*2
            sql_query("UPDATE users SET uploaded = $newup WHERE id =".sqlesc($CURUSER['id']) . "") or sqlerr(__FILE__, __LINE__);
            sql_query("UPDATE users SET uploaded = uploaded + $newup2 WHERE id = ".sqlesc($tbet['userid'])."") or sqlerr(__FILE__, __LINE__);
            sql_query("UPDATE casino SET deposit = deposit-".sqlesc($tbet['amount'])." WHERE userid = ".sqlesc($tbet['userid']) . "");
            if (mysql_affected_rows() == 0)
            sql_query("INSERT INTO casino (userid, date, deposit) VALUES (".sqlesc($tbet['userid']).", '$time', -".sqlesc($tbet['amount']).")") or sqlerr(__FILE__, __LINE__);
            sql_query("UPDATE casino_bets SET challenged = ".sqlesc($CURUSER['username']).", winner = ".sqlesc($tbet[proposed])." WHERE id = ".sqlesc($betid)."") or sqlerr(__FILE__, __LINE__);
            $subject = sqlesc("Casino Results");
            //sql_query("INSERT INTO messages (subject, sender, receiver, added, msg, unread) VALUES ($subject, $sendfrom, ".sqlesc($tbet['userid']).", $time, 'You just won " . htmlspecialchars($nogb) . " of upload credit from " . htmlspecialchars($CURUSER['username']) . " !', 'yes')") or sqlerr(__FILE__, __LINE__);
			sql_query("INSERT INTO messages (subject, sender, receiver, added, msg, unread) VALUES ('Casino结果:本赌局你赢了,你从 " . htmlspecialchars($CURUSER['username']) . " 获得了 " .htmlspecialchars($nogb)  . " 上传!', $sendfrom, ".sqlesc($tbet['userid']).",".sqlesc(date("Y-m-d H:i:s")).",".sqlesc("[url=casino.php]RT[/url]")." , 'yes')") or sqlerr(__FILE__, __LINE__);
            if($writelog == 'y')
            write_casinolog("开注失败! ".$CURUSER['username']." 送给了 $tbet[proposed] $nogb 上传");
            if ($delold == 'y')
            sql_query("DELETE FROM casino_bets WHERE id =".sqlesc($tbet['id'])."") or sqlerr(__FILE__, __LINE__);
            stderr("你输了", "<h2><a href='userdetails.php?id=$tbet[userid]'>$tbet[proposed]</a> 从你身上获得了 ".htmlspecialchars($nogb)." !</h2> &nbsp;&nbsp;&nbsp;$goback",false);
            }
            exit();
            }
            
            //== Add a new bet
            $loca = sql_query("SELECT * FROM casino_bets WHERE challenged ='empty'") or sqlerr(__FILE__, __LINE__);
            $totbets = mysql_num_rows($loca);

            if (isset($_POST['unit'])) {
            if (0 + $_POST["unit"] == '1')
            $nobits = 0 + $_POST["amnt"] * $mb_basic;
            else
            $nobits = 0 + $_POST["amnt"] * $mb_basic * 1024;
            }
			
					
            if (isset($_POST["unit"])) {
			if ($CURUSER["passhash"] != md5($CURUSER["secret"] . $_POST['oldpassword'] . $CURUSER["secret"]))
					stderr("错误", "密码错误");
			if($nobits > 0 + 100 * $mb_basic * 1024)stderr ("出错啦", "每注下注最高100GB",false);
			if (get_row_count("casino_bets","where userid =  ".sqlesc($CURUSER['id'])." and time > ".TIMENOW."-".(3600*24))>=5) 
			stderr ("出错啦", "每天只允许放注5次",false);;
            if ($openbet >= $maxusrbet)
            stderr ("出错啦", "已有 ".htmlspecialchars($openbet)." 注了 ,开注或者等待其他玩家!",false);
            if ($nobits <= 1 * $mb_basic)
            stderr ("出错啦", " This won't work enter a positive value, are you trying to cheat?",false);
            $newup = $CURUSER['uploaded'] - $nobits;
            $debt = $nobits - $CURUSER['uploaded'];
            
            if ($CURUSER['uploaded'] < $nobits) {
            if ($alwdebt != 'y')
            stderr("出错啦", "<h2>押注 ".htmlspecialchars(mksize($debt))."太多了!</h2>$goback",false);
            }
            
            $betsp = sql_query("SELECT id, amount FROM casino_bets WHERE userid = ".sqlesc($CURUSER['id'])." ORDER BY time ASC") or sqlerr(__FILE__, __LINE__);
            $tbet2 = mysql_fetch_row($betsp);
            $dummy = "<h2>已添加, 有人开注你将会收到消息</h2>";
            sql_query("INSERT INTO casino_bets ( userid, proposed, challenged, amount, time) VALUES (".sqlesc($CURUSER['id']).",".sqlesc($CURUSER['username']).", 'empty', '$nobits', '$time')") or sqlerr(__FILE__, __LINE__);
            sql_query("UPDATE users SET uploaded = $newup WHERE id = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
            sql_query("UPDATE casino SET deposit = deposit + $nobits WHERE userid = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
            if (mysql_affected_rows() == 0)
            sql_query("INSERT INTO casino (userid, date, deposit) VALUES (".sqlesc($CURUSER['id']).", '$time', ".sqlesc($nobits).")") or sqlerr(__FILE__, __LINE__);
            }

            $loca = sql_query("SELECT * FROM casino_bets WHERE challenged ='empty'");
            $totbets = mysql_num_rows($loca);
            //== Output html begin
            $HTMLOUT='';
            $HTMLOUT .= "<table class='message' width='650' cellspacing='0' cellpadding='5'>
            <tr>
            <td align='center'>";
            $HTMLOUT = $dummy ;
            //== Place bet table
            if ($openbet < $maxusrbet) {
            if ($totbets >= $maxtotbet)
            $HTMLOUT .= "<br />已有 ".htmlspecialchars($maxtotbet)." 注, 开注吧 !<br />";
            else {
            $HTMLOUT .= "<br />
            <form name=\"p2p\" method=\"post\" action=\"casino.php\">
            <h1>Casino</h1>
            <table width='650' cellspacing='0' cellpadding='3'>";
            $HTMLOUT .= "<tr><td align=\"center\" colspan=\"2\" class=\"colhead\">放注</td></tr>";
            $HTMLOUT .= "<tr><td align=\"center\"><b>押注:</b>
            <input type=\"text\" name=\"amnt\" size=\"5\" value=\"1\" />
            <select name=\"unit\">
            <option value=\"1\">MB</option>
            <option value=\"2\">GB</option>
            </select>&nbsp;&nbsp;用户密码:<input type=password name=oldpassword style=\"width: 100px\"><input type=\"submit\" value=\"赌吧!\" /><br /></td></tr>";
            //$HTMLOUT .= "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\"赌吧!\" />";
           // $HTMLOUT .= "</td></tr></table></form><br />";
		    $HTMLOUT .= "</table></form><br />";
            }
            } else
            $HTMLOUT .= "<b>你已经有 ".htmlspecialchars($maxusrbet)." 注, 请等待其他玩家.</b><br /><br />";
            //== Open Bets table
            $HTMLOUT .= "<table width=\"650\" cellspacing=\"0\" cellpadding=\"3\">";
            $HTMLOUT .= "<tr><td align=\"center\" class=\"colhead\" colspan=\"4\">开注</td></tr>";
            $HTMLOUT .="<tr>
            <td align=\"center\" ><b>用户名</b></td><td width=\"15%\" align=\"center\"><b>赌注</b></td>
            <td width=\"45%\" align=\"center\"><b>时间</b></td><td align=\"center\"><b>开注</b></td>
            </tr>";
            while ($res = mysql_fetch_assoc($loca)) {
            $HTMLOUT .="<tr>
            <td align=\"center\">".htmlspecialchars($res['proposed'])."</td>
            <td align=\"center\">".htmlspecialchars(mksize($res['amount']))."</td>
            <td align=\"center\">".date("m-d H:i:s",$res['time'])."</td>
            <td align=\"center\"><b><a href='{$casino}?takebet=".htmlspecialchars($res['id'])."'>开这个</a></b></td>
            </tr>";
            $abcdefgh = 1;
            }
            if ($abcdefgh == false)
            $HTMLOUT .="<tr><td align='center' colspan='4'>对不起 现在没有赌注</td></tr>";
            $HTMLOUT .="</table><br />";
            //== Bet on color table
            $HTMLOUT .="<form name=\"casino\" method=\"post\" action=\"casino.php\">
            <table class=\"message\" width=\"650\" cellspacing=\"0\" cellpadding=\"5\">\n";
            $HTMLOUT .= "<tr><td align=\"center\" class=\"colhead\" colspan=\"2\">按颜色押注</td></tr>";
            $HTMLOUT .= trcasino("押注颜色","<input name=\"color\" type=\"radio\" checked=\"checked\" value=\"black\" />黑色&nbsp;&nbsp;<input name=\"color\" type=\"radio\" checked=\"checked\" value=\"red\" />红色", 1);
            //$HTMLOUT .= trcasino("红色", "<input name=\"color\" type=\"radio\" checked=\"checked\" value=\"red\" />", 1);
            $HTMLOUT .= trcasino("押注多少", "
            <select name=\"betmb\">
            <option value=\"{$bet_value1}\">".mksize($bet_value1)."</option>
            <option value=\"{$bet_value2}\">".mksize($bet_value2)."</option>
            <option value=\"{$bet_value3}\">".mksize($bet_value3)."</option>
            <option value=\"{$bet_value4}\">".mksize($bet_value4)."</option>
            <option value=\"{$bet_value5}\">".mksize($bet_value5)."</option>
            <option value=\"{$bet_value6}\">".mksize($bet_value6)."</option>
            <option value=\"{$bet_value7}\">".mksize($bet_value7)."</option>
            </select>", 1);
            if ($show_real_chance)
            $real_chance = $cheat_value + 1;
            else
            $real_chance = 2;
            $HTMLOUT .= trcasino("赔率", "1 : " . $real_chance, 1);
            $HTMLOUT .= trcasino("你能获得", $win_amount . " * 赌注", 1);
            $HTMLOUT .= trcasino("押注颜色", "<input type=\"submit\" value=\"确认\" />", 1);
            $HTMLOUT .="</table></form><br />";
            //== Bet on number table
            $HTMLOUT .="<form name=\"casino\" method=\"post\" action=\"casino.php\">
            <table class=\"message\" width=\"650\" cellspacing=\"0\" cellpadding=\"5\">\n";
            $HTMLOUT .= "<tr><td align=\"center\" class=\"colhead\" colspan=\"2\">按数字押注</td></tr>";
            $HTMLOUT .= trcasino("号码", '<input name="number" type="radio" checked="checked" value="1" />1&nbsp;&nbsp;<input name="number" type="radio" value="2" />2&nbsp;&nbsp;<input name="number" type="radio" value="3" />3&nbsp;&nbsp;<input name="number" type="radio" value="4" />4&nbsp;&nbsp;<input name="number" type="radio" value="5" />5&nbsp;&nbsp;<input name="number" type="radio" value="6" />6', 1);
            //$HTMLOUT .= trcasino("", '<input name="number" type="radio" value="4" />4&nbsp;&nbsp;<input name="number" type="radio" value="5" />5&nbsp;&nbsp;<input name="number" type="radio" value="6" />6', 1);
            $HTMLOUT .= trcasino("押注多少", "
            <select name=\"betmb\">
            <option value=\"{$bet_value1}\">".mksize($bet_value1)."</option>
            <option value=\"{$bet_value2}\">".mksize($bet_value2)."</option>
            <option value=\"{$bet_value3}\">".mksize($bet_value3)."</option>
            <option value=\"{$bet_value4}\">".mksize($bet_value4)."</option>
            <option value=\"{$bet_value5}\">".mksize($bet_value5)."</option>
            <option value=\"{$bet_value6}\">".mksize($bet_value6)."</option>
            <option value=\"{$bet_value7}\">".mksize($bet_value7)."</option>
            </select>", 1);
            if ($show_real_chance)
            $real_chance = $cheat_value + 5;
            else
            $real_chance = 6;
            $HTMLOUT .= trcasino("赔率", "1 : " . $real_chance, 1);
            $HTMLOUT .= trcasino("你能获得", $win_amount_on_number . " * 赌注", 1);
            $HTMLOUT .= trcasino("押注号码", "<input type=\"submit\" value=\"确认\" />", 1);
            $HTMLOUT .="</table></form><br />";
            //== User stats table
            $HTMLOUT .="<table cellspacing='0' width='650' cellpadding='3'>";
            $HTMLOUT .= "<tr><td align=\"center\" class=\"colhead\" colspan=\"3\">{$CURUSER['username']}的状态</td></tr>
            <tr><td align='center'>
            <h1>用户 @ Casino</h1>
            <table class='message'  cellspacing='0' cellpadding='5'>";
            $HTMLOUT .= trcasino("你能够获得",mksize($max_download_user),1);
            $HTMLOUT .= trcasino("获得",mksize($user_win),1);
            $HTMLOUT .= trcasino("失去",mksize($user_lost),1);
            $HTMLOUT .= trcasino("比例",$casino_ratio_user,1);
            $HTMLOUT .= trcasino('P2P押金', mksize($user_deposit+$nobits));
            $HTMLOUT .="</table>";
            $HTMLOUT .=" </td><td align='center'>
            <h1>全局状况</h1>
            <table class='message'  cellspacing='0' cellpadding='5'>";
            $HTMLOUT .= trcasino("用户能够获得",mksize($max_download_global),1);
            $HTMLOUT .= trcasino("一共获得",mksize($global_win),1);
            $HTMLOUT .= trcasino("一共失去",mksize($global_lost),1);
            $HTMLOUT .= trcasino("比例",$casino_ratio_global,1);
            $HTMLOUT .= trcasino("总赌注",mksize($global_deposit));
            $HTMLOUT .="</table>";
            $HTMLOUT .="</td><td align='center'>
            <h1>用户状况</h1>
            <table class='message'  cellspacing='0' cellpadding='5'>";
            $HTMLOUT .= trcasino('上传量',mksize($CURUSER['uploaded'] - $nobits));
            $HTMLOUT .= trcasino('下载量',mksize($CURUSER['downloaded']));
            $HTMLOUT .= trcasino('比例',$ratio);
            $HTMLOUT .="</table></td></tr></table><p><a href=\"/casinostats.php\">历史统计与帮助</a>";
            }
stdhead("Casino");print ( $HTMLOUT); stdfoot();
?>