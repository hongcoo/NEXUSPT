<?php
//$_NO_COMPRESS = true; //== For pdq's improvements mods
//ob_start("ob_gzhandler");
require_once "include/bittorrent.php";

dbconn(false);
loggedinorreturn();

//$lang = array_merge( load_language('global') );

if ($CURUSER['class'] < UC_USER)
{
        stderr("Sorry...", "You must be a Power User or above to play Blackjack.");
        exit;
}

function begin_table2($fullwidth = false, $padding = 5)
{
	$width = "";

	if ($fullwidth)
	$width .= " width=50%";
	return ("<table class=\"main".$width."\" border=\"1\" cellspacing=\"0\" cellpadding=\"".$padding."\">");
}

function end_table2()
{
	return ("</table>\n");
}


function begin_frame2($caption = "", $center = false, $padding = 10, $width="100%", $caption_center="left")
{
	$tdextra = "";

	if ($center)
	$tdextra .= " align=\"center\"";

	return(($caption ? "<h2 align=\"".$caption_center."\">".$caption."</h2>" : "") . "<table width=\"".$width."\" border=\"1\" cellspacing=\"0\" cellpadding=\"".$padding."\">" . "<tr><td class=\"text\" $tdextra>\n");

}

function end_frame2()
{
	return("</td></tr></table>\n");
}



function bjtable($res, $frame_caption)
{
        $htmlout='';
        $htmlout .= begin_frame2($frame_caption, true);
        $htmlout .= begin_table2();
        $htmlout .="<tr>
        <td class='colhead'>Rank</td>
        <td class='colhead' align='left'>User</td>
        <td class='colhead' align='right'>Wins</td>
        <td class='colhead' align='right'>Losses</td>
        <td class='colhead' align='right'>Games</td>
        <td class='colhead' align='right'>Percentage</td>
        <td class='colhead' align='right'>Win/Loss</td>
        </tr>";

        $num = 0;
        while ($a = mysql_fetch_assoc($res))
        {
                ++$num;
                //==Calculate Win %
                $win_perc = number_format(($a['win'] / $a['lost']) * 100, 1);
                //==Add a user's +/- statistic
                $plus_minus = $a['win'] - $a['lost'];
                if ($plus_minus >= 0)
                {
                $plus_minus = mksize($a['win'] - $a['lost']);
                }
                else
                {
                        $plus_minus = "-";
                        $plus_minus .= mksize($a['lost'] - $a['win']);
                }
                
                $htmlout .="<tr><td>$num</td><td align='left'>".
                "<b><a href='userdetails.php?id=".$a['userid']."'>".get_username($a["userid"])."</a></b></td>".
                "<td align='right'>".mksize($a['win'], 0)."</td>".
                "<td align='right'>".mksize($a['lost'], 0)."</td>".
                "<td align='right'>".number_format($a['trys'], 0)."</td>".
                "<td align='right'>$win_perc</td>".
                "<td align='right'>$plus_minus</td>".
                "</tr>\n";
        }
        $htmlout .= end_table2();
        $htmlout .= end_frame2();
        return $htmlout;
}



     $cachefile = "cache/bjstats.txt";
     $cachetime = 60 * 30; // 30 minutes
     //$cachetime = 10 * 3;
$Cache->new_page('casinostats', $cachetime, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
     
$mingames =5;
$HTMLOUT='';
$HTMLOUT .="<h1>Casino Stats</h1>";
$HTMLOUT .="<p>Stats are cached and updated every 30 minutes. You need to play at least $mingames games to be included.</p>";
$HTMLOUT .="<br />";
//==Most Games Played
$res = sql_query("SELECT userid,  win, lost,trys FROM casino WHERE trys > $mingames ORDER BY trys DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .= bjtable($res, "Most Games Played","Users");
$HTMLOUT .="<br /><br />";
//==Most Games Played
//==Highest Win %
$res = sql_query("SELECT userid,  win, lost,trys, win /lost AS winperc FROM casino WHERE trys > $mingames ORDER BY winperc DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .= bjtable($res, "Highest Win Percentage","Users");
$HTMLOUT .="<br /><br />";
//==Highest Win %
//==Most Credit Won
$res = sql_query("SELECT userid,  win, lost,trys, win - lost AS winnings FROM casino WHERE trys > $mingames ORDER BY winnings DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .= bjtable($res, "Most Credit Won","Users");
$HTMLOUT .="<br /><br />";
//==Most Credit Won
//==Most Credit Lost
$res = sql_query("SELECT userid, win, lost,trys, lost - win AS losings FROM casino WHERE trys > $mingames ORDER BY losings DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .= bjtable($res, "Most Credit Lost","Users");
//==Most Credit Lost
$HTMLOUT .="<br /><br />";
;
print  $HTMLOUT ;


	$Cache->end_whole_row();
	$Cache->cache_page();
	}
	
	
	stdhead('Casino Stats'); 
	echo $Cache->next_row();
	
	
	
	
		

	
	
	
	
	
	
	
	
	
	
		$htmlout='';
        $htmlout .= begin_frame2("Deposit Log", true);
        $htmlout .= begin_table2();
        $htmlout .="<tr>
        <td class='colhead'>Date</td>
        <td class='colhead' align='left'>Log</td>
        </tr>";
			$subres = sql_query("SELECT * FROM casinolog ORDER BY id desc LIMIT 50 ");
			while ($subrow = mysql_fetch_array($subres)) 		
			$htmlout .="<tr><td>".$subrow['added']."</td><td>".$subrow['txt']."</td></tr>";
			$htmlout .= end_table2();
			$htmlout .= end_frame2();
			
		
        $htmlout .= begin_frame2("游戏方法", true);
        $htmlout .= begin_table2();
        $htmlout .="<tr>
        
        <td align='left'>
		根据我目前掌握的数据来看,本Casino有两种游戏方法<br/>
		1:自己放注,别人开注,50%几率获胜,获胜方拿走对应上传量,战败方失去对应上传量(开注者还要支付10%场地费,输赢结果不进行统计)<br/>
		2:按照颜色/数字押注,押注成功将获得对应上传量,否则失去对应上传量<br/>
		请自行承担因为错误操作而导致的所有责任</td>
        </tr>";
						$htmlout .= end_table2();
			$htmlout .= end_frame2();
			
			
				

	print  $htmlout ;
	
	
	
	
	stdfoot();
?>