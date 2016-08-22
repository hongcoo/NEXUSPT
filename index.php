<?php
require "include/bittorrent.php";
dbconn(true);
require_once(get_langfile_path("index.php"));
//header("Cache-Control: no-cache, must-revalidate" ); 
//header("Pragma: no-cache" );
loggedinorreturn(true);
$notshowfriendstags=true;$thispagewidthscreen=false;
if ($showextinfo['imdb'] == 'yes')require_once ("imdb/imdb.class.php");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($showpolls_main == "yes")
	{
		$choice = $_POST["choice"];
		if ($CURUSER && $choice != "" && $choice < 256 && $choice == floor($choice))
		{
			$res = sql_query("SELECT * FROM polls ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
			$arr = mysql_fetch_assoc($res) or die($lang_index['std_no_poll']);
			$pollid = $arr["id"];

			$hasvoted = get_row_count("pollanswers","WHERE pollid=".sqlesc($pollid)." && userid=".sqlesc($CURUSER["id"]));
			if ($hasvoted)
				stderr($lang_index['std_error'],$lang_index['std_duplicate_votes_denied']);
			sql_query("INSERT INTO pollanswers VALUES(0, ".sqlesc($pollid).", ".sqlesc($CURUSER["id"]).", ".sqlesc($choice).")") or sqlerr(__FILE__, __LINE__);
			$Cache->delete_value('current_poll_content');
			$Cache->delete_value('current_poll_result', true);
			if (mysql_affected_rows() != 1)
			stderr($lang_index['std_error'], $lang_index['std_vote_not_counted']);
			//add karma
			KPS("+",$pollvote_bonus,$userid);

			header("Location: " . get_protocol_prefix() . "$BASEURL/");
			die;
		}
		else
		stderr($lang_index['std_error'], $lang_index['std_option_unselected']);
	}
}

stdhead($lang_index['head_home'],true);

?>
<script type="text/javascript">

$(function(){$('#shbox_text').keypress(function(e){ if(e.ctrlKey && e.which == 13 || e.which == 10) {$('form[name=shbox]').submit();}});});
</script>

<?php


  if ($CURUSER["gotgift"] > TIMENOW )
   {	echo "
   <div align='center'><a href='/gift.php?open=1'>
   <img src='pic/gift.png' style='float: center;border-style: none;' alt='Gift' title='Gift' /></a>
   </div>";
   }
   

begin_main_frame();

// ------------- start: recent news ------------------//
print("<h2 class=\"index\"><span class=\"index\">".$lang_index['text_recent_news'].(get_user_class() >= $newsmanage_class ? " - <font class=\"small\">[<a class=\"altlink\" href=\"news.php\"><b>".$lang_index['text_news_page']."</b></a>]</font>" : "")."</span></h2>");

$Cache->new_page('recent_news', 3600, true);
if (!$Cache->get_page()){
$res = sql_query("SELECT * FROM news ORDER BY added DESC LIMIT ".(int)$maxnewsnum_main) or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
	$Cache->add_whole_row();
	print("<table class=\"index\" width=\"100%\"><tr><td class=\"text\"><div style=\"margin-left: 16pt;\">\n");
	$Cache->end_whole_row();
	$news_flag = 0;
	while($array = mysql_fetch_array($res))
	{
		$Cache->add_row();
		$Cache->add_part();
		if ($news_flag < 1) {
		
		if(strtotime($array['added'])>(TIMENOW-3600*24*3) || $array['notify']=='yes')print("<script>$(function(){klappe_news('a".$array['id']."')});</script>" );
			print("<a href=\"javascript: klappe_news('a".$array['id']."')\"><img class=\"plus\" src=\"pic/trans.gif\" id=\"pica".$array['id']."\" alt=\"Show/Hide\" title=\"".$lang_index['title_show_or_hide']."\" />&nbsp;" . date("Y.m.d",strtotime($array['added'])) . " - " ."<b>". $array['title'] . "</b></a>");
			print("<div id=\"ka".$array['id']."\" style=\"display: none;\"> ".format_comment($array["body"],0)." </div> ");
			$news_flag = $news_flag + 1;
		}
		else
		{
			print("<a href=\"javascript: klappe_news('a".$array['id']."')\"><br /><img class=\"plus\" src=\"pic/trans.gif\" id=\"pica".$array['id']."\" alt=\"Show/Hide\" title=\"".$lang_index['title_show_or_hide']."\" />&nbsp;" . date("Y.m.d",strtotime($array['added'])) . " - " ."<b>". $array['title'] . "</b></a>");
			print("<div id=\"ka".$array['id']."\" style=\"display: none;\"> ".format_comment($array["body"],0)." </div> ");
		}
		$Cache->end_part();
		$Cache->add_part();
		print("  &nbsp; [<a class=\"faqlink\" href=\"news.php?action=edit&amp;newsid=" . $array['id'] . "\"><b>".$lang_index['text_e']."</b></a>]");
		print(" [<a class=\"faqlink\" href=\"news.php?action=delete&amp;newsid=" . $array['id'] . "\"><b>".$lang_index['text_d']."</b></a>]");
		$Cache->end_part();
		$Cache->end_row();
	}
	$Cache->break_loop();
	$Cache->add_whole_row();
	print("</div></td></tr></table>\n");
	$Cache->end_whole_row();
}
	$Cache->cache_page();
}
echo $Cache->next_row();
while($Cache->next_row()){
	echo $Cache->next_part();
	if (get_user_class() >= $newsmanage_class)
	echo $Cache->next_part();
}
echo $Cache->next_row();
// ------------- end: recent news ------------------//
// ------------- start: hot and classic movies ------------------//
if ($showextinfo['imdb'] == 'yes' && ($showmovies['hot'] == "yes" || $showmovies['classic'] == "yes"))
{
	$type = array('classic','hot');
	//hot为pick classic为new
	foreach($type as $type_each)
	{
		if($showmovies[$type_each] == 'yes' && (!isset($CURUSER) || $CURUSER['show' . $type_each] == 'yes'))
		{
			$Cache->new_page($type_each.'_resources', 900, true);
			if (!$Cache->get_page())
			{
				$Cache->add_whole_row();

				$imdbcfg = new imdb_config();
			if($type_each=='hot')
				$res = sql_query("SELECT torrents.id ,torrents.url ,torrents.name ,torrents.sp_state FROM torrents LEFT JOIN categories ON torrents.category=categories.id where categories.mode = '$browsecatmode' AND picktype !=  " . sqlesc('normal') . " AND havenoseed = 'no' AND url != '' and banned = 'no' and seeders >0  ORDER BY id DESC LIMIT 100") or sqlerr(__FILE__, __LINE__);
			else 
				$res = sql_query("SELECT torrents.id ,torrents.url ,torrents.name ,torrents.sp_state FROM torrents LEFT JOIN categories ON torrents.category=categories.id where categories.mode = '$browsecatmode' AND picktype = " . sqlesc('normal'). "   AND havenoseed = 'no'  AND url != '' and banned = 'no'  and seeders >0  ORDER BY id DESC LIMIT 50") or sqlerr(__FILE__, __LINE__);
		   
				if (mysql_num_rows($res) > 10)
				{
					$movies_list = "";
					$count = 0;
					$allImdb = array();
					while($array = mysql_fetch_array($res))
					{
						//$pro_torrent = get_torrent_promotion_append($array[sp_state],'word');
						if ($imdb_id = parse_imdb_id($array["url"]))
						{
							if (array_search($imdb_id, $allImdb) !== false) { //a torrent with the same IMDb url already exists
								continue;
							}
							//if($type_each=='hot')$allImdb[]=$imdb_id;
							$photo_url = $imdbcfg->photodir . $imdb_id. $imdbcfg->imageext;

							if (file_exists($photo_url))
								$thumbnail = "<img class=\"transitionpic\"  width=\"101\" height=\"140\" src=\"".$photo_url."\" border=\"0\" title=\"".$array['name']."\" alt=\"".$array['name']."\" />";
							else continue;
						}
						else 	continue;
						$thumbnail = "<a href=\"details.php?id=" . $array['id'] . "&amp;hit=1\" >" . $thumbnail . "</a>";
						$movies_list .= $thumbnail;
						$count++;
						if ($count >= 9)
							break;
					}
?>
<h2 class="index"><span class="index"><?php echo $lang_index['text_' . $type_each . 'movies'] ?></span></h2>
<table width="100%"  class="index" border="1" cellspacing="0" cellpadding="5"><tr><td class="text nowrap" align="center">
<?php echo $movies_list ?></td></tr></table>
<?php
				}
				$Cache->end_whole_row();
				$Cache->cache_page();
			}
			echo $Cache->next_row();
		}
	}
}
// ------------- end: hot and classic movies ------------------//

 if ($Advertisement->enable_ad()){
			$belownavad=$Advertisement->get_ad('belownav');
			if ($belownavad)
			echo "<div align=\"center\" style=\"margin-bottom: 10px\" id=\"ad_belownav\">".$belownavad[0]."</div>";
	}

// ------------- start: shoutbox ------------------//
if ($showshoutbox_main == "yes") {
?>
<h2 class="index"><span class="index"><?php echo $lang_index['text_shoutbox'] ?><font class="small"><span id="shoutboxfont"> - [<a class="altlink shoutboxheight" href='javascript:shoutboxheight();'>展开</a>] - [<a class='altlink shoutboxstopre' href='javascript:shoutboxstopre();'>暂停</a>]</span> - <?php echo $lang_index['text_auto_refresh_after']?></font><font class='striking' id="countdown"></font><font class="small"><?php echo $lang_index['text_seconds']?></font></span></h2>
<?php
	print("<table  class=\"index\" width=\"100%\" ><tr><td class=\"text\" align=\"center\">\n");
	print("<iframe id='shoutboxwindows' src='shoutbox.php?type=shoutbox' width='100%' height='300' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");
	print("<form action='shoutbox.php' method='post' target='sbox' name='shbox' id='shbox' >\n");
	print("<label for='shbox_text'>".$lang_index['text_message']."</label>
	<input type='text' name='shbox_text' id='shbox_text' size='100' rows=1 style='width: 650px;height: 14px; border: 1px solid gray;margin-bottom: -5px;' x-webkit-speech onwebkitspeechchange=\"SmileIT('♪♪','shbox','shbox_text')\" />
	
	    

	
	
	<input type='submit' class='btn' name='shout' value=\"".$lang_index['sumbit_shout']."\" id='hbsubmit' />");
	if ($CURUSER['hidehb'] != 'yes' && $showhelpbox_main =='yes')
		print("<input type='submit' class='btn' name='toguest' value=\"".$lang_index['sumbit_to_guest']."\" />");
	print("<input type='reset' class='btn' value=\"".$lang_index['submit_clear']."\" onclick=\"javascript:{this.form.reset();this.form.submit()}\"/> <input type='hidden' name='sent' value='yes' /><input type='hidden' name='type' value='shoutbox' /><br />\n");
	print(smile_row("shbox","shbox_text"));
	print("</form></td></tr></table>");
}
// ------------- end: shoutbox ------------------//

// ------------- start: polls ------------------//
if ($CURUSER && $showpolls_main == "yes")
{
		// Get current poll
		if (!$arr = $Cache->get_value('current_poll_content')){
			$res = sql_query("SELECT * FROM polls ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
			$arr = mysql_fetch_array($res);
			$Cache->cache_value('current_poll_content', $arr, 7226);
		}
		if (!$arr)
			$pollexists = false;
		else $pollexists = true;

		print("<h2 class=\"index\"><span class=\"index\">".$lang_index['text_polls']);

			if (get_user_class() >= $pollmanage_class)
			{
				print("<font class=\"small\"> - [<a class=\"altlink\" href=\"makepoll.php?returnto=main\"><b>".$lang_index['text_new']."</b></a>]\n");
				if ($pollexists)
				{
					print(" - [<a class=\"altlink\" href=\"makepoll.php?action=edit&amp;pollid=".$arr[id]."&amp;returnto=main\"><b>".$lang_index['text_edit']."</b></a>]\n");
					print(" - [<a class=\"altlink\" href=\"log.php?action=poll&amp;do=delete&amp;pollid=".$arr[id]."&amp;returnto=main\"><b>".$lang_index['text_delete']."</b></a>]");
					print(" - [<a class=\"altlink\" href=\"polloverview.php?id=".$arr[id]."\"><b>".$lang_index['text_detail']."</b></a>]");
					print(" - [<a class=\"altlink\" href=\"log.php?action=poll\"><b>".$lang_index['text_previous_polls']."</b></a>]");
					
				}
				print("</font>");
			}
			print("</span></h2>");
		if ($pollexists)
		{
			$pollid = 0+$arr["id"];
			$userid = 0+$CURUSER["id"];
			$question = $arr["question"];
			$o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
			$arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
			$arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
			$arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);

			print("<table width=\"100%\" class=\"index\"><tr><td class=\"text\" align=\"center\">\n");
			print("<table width=\"59%\" class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr><td class=\"text\" align=\"left\">");
			print("<p align=\"center\"><b>".$question."</b></p>\n");

			// Check if user has already voted
				if(!($voted = $Cache->get_value('current_fun_vote_pollanswers_voted_'.$CURUSER[id].'_'.$pollid))){
					$res = sql_query("SELECT selection FROM pollanswers WHERE pollid=".sqlesc($pollid)." AND userid=".sqlesc($CURUSER["id"])) or sqlerr();
					$voted = mysql_fetch_assoc($res);
					$Cache->cache_value('current_fun_vote_pollanswers_voted_'.$CURUSER[id].'_'.$pollid, $voted, 756);
				}
	

			if ($voted) //user has already voted
			{
				$uservote = $voted["selection"];
				$Cache->new_page('current_poll_result', 3652, true);
				if (!$Cache->get_page())
				{
				// we reserve 255 for blank vote.
				$res = sql_query("SELECT selection FROM pollanswers WHERE pollid=".sqlesc($pollid)." AND selection < 20") or sqlerr();

				$tvotes = mysql_num_rows($res);

				$vs = array();
				$os = array();

				// Count votes
				while ($arr2 = mysql_fetch_row($res))
				$vs[$arr2[0]] ++;

				reset($o);
				for ($i = 0; $i < count($o); ++$i){
					if ($o[$i])
						$os[$i] = array($vs[$i], $o[$i], $i);
				}

				function srt($a,$b)
				{
					if ($a[0] > $b[0]) return -1;
					if ($a[0] < $b[0]) return 1;
					return 0;
				}

				// now os is an array like this: array(array(123, "Option 1", 1), array(45, "Option 2", 2))
				$Cache->add_whole_row();
				print("<table class=\"main\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
				$Cache->end_whole_row();
				$i = 0;
				while ($a = $os[$i])
				{
					if ($tvotes == 0)
						$p = 0;
					else
						$p = round($a[0] / $tvotes * 100);
					$Cache->add_row();
					$Cache->add_part();
					print("<tr><td width=\"1%\" class=\"embedded nowrap\">" . $a[1] . "&nbsp;&nbsp;</td><td width=\"99%\" class=\"embedded nowrap\"><img class=\"bar_end\" src=\"pic/trans.gif\" alt=\"\" /><img ");
					$Cache->end_part();
					$Cache->add_part();
					print(" src=\"pic/trans.gif\" style=\"width: " . ($p * 6) ."px;\" alt=\"\" /><img class=\"bar_end\" src=\"pic/trans.gif\" alt=\"\" /> $p%</td></tr>\n");
					$Cache->end_part();
					$Cache->end_row();
					++$i;
				}
				$Cache->break_loop();
				$Cache->add_whole_row();
				print("</table>\n");
				$tvotes = number_format($tvotes);
				print("<p align=\"center\">".$lang_index['text_votes']." ".$tvotes."</p>\n");
				$Cache->end_whole_row();
				$Cache->cache_page();
				}
				echo $Cache->next_row();
				$i = 0;
				while($Cache->next_row()){
					echo $Cache->next_part();
					if ($i == $uservote)
						echo "class=\"sltbar\"";
					else
						echo "class=\"unsltbar\"";
					echo $Cache->next_part();
					$i++;
				}
				echo $Cache->next_row();
			}
			else //user has not voted yet
			{
				print("<form method=\"post\" action=\"index.php\">\n");
				$i = 0;
				while ($a = $o[$i])
				{
					print("<input type=\"radio\" name=\"choice\" value=\"".$i."\">".$a."<br />\n");
					++$i;
				}
				//print("<br />");
				print("<input type=\"radio\" name=\"choice\" value=\"255\">".$lang_index['radio_blank_vote']."<br />\n");
				print("<p align=\"center\"><input type=\"submit\" class=\"btn\" value=\"".$lang_index['submit_vote']."\" /></p></form>");
			}
			print("</td></tr></table>");

			if ($voted && get_user_class() >= $log_class)
				print("<p align=\"center\"></p>\n");
			print("</td></tr></table>");
		}
}
// ------------- end: polls ------------------//

// ------------- start: funbox ------------------//
if ($showfunbox_main == "yes" && (!isset($CURUSER) || $CURUSER['showfb'] == "yes")){
	// Get the newest fun stuff
	if (!$row = $Cache->get_value('current_fun_content')){
		$result = sql_query("SELECT fun.*, IF(ADDTIME(added,'24:0:0') < NOW(),true,false) AS neednew FROM fun WHERE status != 'banned' AND status != 'dull' ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__,__LINE__);
		$row = mysql_fetch_array($result);
		$Cache->cache_value('current_fun_content', $row, 1043);
	}
	if (!$row) //There is no funbox item
	{
		print("<h2 class=\"index\"><span class=\"index\">".$lang_index['text_funbox'].(get_user_class() >= $newfunitem_class ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"fun.php?action=new\"><b>".$lang_index['text_new_fun']."</b></a>]</font>" : "")."</span></h2>");
	}
	else
	{
	//$totalvote = $Cache->get_value('current_fun_vote_count');
	//if ($totalvote == ""){
	if(!($totalvote = $Cache->get_value('current_fun_vote_count'))){
		$totalvote = get_row_count("funvotes", "WHERE funid = ".sqlesc($row['id']));
		$Cache->cache_value('current_fun_vote_count', $totalvote, 756);
	}
	//$funvote = $Cache->get_value('current_fun_vote_funny_count');
	//if ($funvote == ""){
	if(!($funvote = $Cache->get_value('current_fun_vote_funny_count'))){
		$funvote = get_row_count("funvotes", "WHERE funid = ".sqlesc($row['id'])." AND vote='fun'");
		$Cache->cache_value('current_fun_vote_funny_count', $funvote, 756);
	}
//check whether current user has voted
	//$funvoted = get_row_count("funvotes", "WHERE funid = ".sqlesc($row['id'])." AND userid=".sqlesc($CURUSER[id]));
	if(!($funvoted = $Cache->get_value('current_fun_vote_funny_funvoted_'.$CURUSER[id]))){
		$funvoted = get_row_count("funvotes", "WHERE funid = ".sqlesc($row['id'])." AND userid=".sqlesc($CURUSER[id]));
		$Cache->cache_value('current_fun_vote_funny_funvoted_'.$CURUSER[id], $funvoted, 756);
	}
	print ("<h2 class=\"index\"><span class=\"index\">".$lang_index['text_funbox']);
	if ($CURUSER)
	{
		print("<font class=\"small\"> - [<a class=\"altlink\" target=\"_blank\" href=\"fun.php?action=view\"><b>新窗口打开</b></a>]".(get_user_class() >= $log_class ? " - [<a class=\"altlink\" href=\"log.php?action=funbox\"><b>".$lang_index['text_more_fun']."</b></a>]": "").($row['neednew']&&0 || get_user_class() >= $newfunitem_class ? " - [<a class=altlink href=\"fun.php?action=new\"><b>".$lang_index['text_new_fun']."</b></a>]" : "" ).( ($CURUSER['id'] == $row['userid'] || get_user_class() >= $funmanage_class) ? " - [<a class=\"altlink\" href=\"fun.php?action=edit&amp;id=".$row['id']."&amp;returnto=index.php\"><b>".$lang_index['text_edit']."</b></a>]" : "").(get_user_class() >= $funmanage_class ? " - [<a class=\"altlink\" href=\"fun.php?action=delete&amp;id=".$row['id']."&amp;returnto=index.php\"><b>".$lang_index['text_delete']."</b></a>] - [<a class=\"altlink\" href=\"fun.php?action=ban&amp;id=".$row['id']."&amp;returnto=index.php\"><b>".$lang_index['text_ban']."</b></a>]" : "")."</font>");
	}
	print("</span></h2>");

	print("<table  class=\"index\" width=\"100%\"><tr><td class=\"text\">");
	print("<iframe src=\"fun.php?action=view\" width='900' height='200' frameborder='1' name='funbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");

	if ($CURUSER)
	{
		$funonclick = " onclick=\"funvote(".$row['id'].",'fun'".")\"";
		$dullonclick = " onclick=\"funvote(".$row['id'].",'dull'".")\"";
		
		
	$funcomment ="<form action='fun.php#bottom' method='POST' target='funbox'  name='funboxcomment'  >
	<input type='text' name='fun_text' id='fun_text' size='100' style='width: 750px; border: 1px solid gray;' /> 
	<input type=hidden name=funid value=".$row['id'].">
	<input type='submit' class='btn' value=\"评论\"  name='tofunboxcomment'  /></form>";
	
	
	
	
	
		print("<span id=\"funvote\"><b>".$funvote."</b>".$lang_index['text_out_of'].$totalvote.$lang_index['text_people_found_it'].($funvoted ? "" : "<font class=\"striking\">".$lang_index['text_your_opinion']."</font>&nbsp;&nbsp;<input type=\"button\" class='btn' name='fun' id='fun' ".$funonclick." value=\"".$lang_index['submit_fun']."\" />&nbsp;<input type=\"button\" class='btn' name='dull' id='dull' ".$dullonclick." value=\"".$lang_index['submit_dull']."\" />")."</span><span id=\"voteaccept\" style=\"display: none;\">".$lang_index['text_vote_accepted']."</span>");
	}
	print("</td></tr></table>");
	}
}
// ------------- end: funbox ------------------//

if($CURUSER['showhot'] != 'yes'&&$CURUSER['showclassic'] != 'yes'&& $CURUSER['showfb'] != "yes"){$showlastxtorrents_main = "no";$showlastxforumposts_main = "no";}
// ------------- start: latest forum posts ------------------//

if ($showlastxforumposts_main == "yes" && $CURUSER)
{

	$Cache->new_page('latest_forum_posts', 300, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
	
	$res = sql_query("SELECT posts.id AS pid, topics.userid AS userpost, posts.userid AS postsuser, posts.added, topics.id AS tid, topics.subject, topics.forumid, topics.views, forums.name FROM posts, topics, forums WHERE posts.id = topics.lastpost AND topics.forumid = forums.id AND minclassread <=" . sqlesc(UC_VIP) . " ORDER BY topics.lastpost  DESC LIMIT 8 ") or sqlerr(__FILE__,__LINE__);
	if(mysql_num_rows($res) != 0)
	{
		//print("<h2>".$lang_index['text_last_five_posts']."</h2>");
		print("<br /><table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr><td class=\"colhead\" width=\"100%\" align=\"left\">".$lang_index['text_last_five_posts']."</td><td class=\"colhead\" align=\"center\">".$lang_index['col_view']."</td><td class=\"colhead\" align=\"center\">".$lang_index['col_author']."</td><td class=\"colhead\" align=\"center\">".$lang_index['col_poster']."</td><td class=\"colhead\" align=\"left\">".$lang_index['col_posted_at']."</td></tr>");

		while ($postsx = mysql_fetch_assoc($res))
		{
			print("<tr><td>[<a href=\"forums.php?action=viewforum&amp;forumid=".$postsx["forumid"]."\">".htmlspecialchars($postsx["name"])."</a>]&nbsp;&nbsp;<a href=\"forums.php?action=viewtopic&amp;topicid=".$postsx["tid"]."&amp;page=p".$postsx["pid"]."#pid".$postsx["pid"]."\"><b>".htmlspecialchars($postsx["subject"])."</b></a><br /></td><td align=\"center\">".$postsx["views"]."</td><td align=\"center\">" . get_username($postsx["userpost"]) ."</td><td align=\"center\">" . get_username($postsx["postsuser"]) ."</td><td>".gettime($postsx["added"],true,false,true)."</td></tr>");
		}
		print("</table>");
	}
	
		$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();

}


// ------------- end: latest forum posts ------------------//
// ------------- start: latest torrents ------------------//


$get_second_name=get_second_name();

if ($showlastxtorrents_main == "yes") {

	$Cache->new_page('Latest_Torrents', 300, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
	
		$result = sql_query("SELECT torrents.id as id, categories.name AS categories , torrents.name as name , seeders ,size, leechers ,times_completed,audiocodec FROM torrents LEFT JOIN categories ON torrents.category=categories.id  where categories.mode = '$browsecatmode' and  picktype != 'hot' and havenoseed = 'no' AND  url = ''  and banned = 'no' and seeders >0  ORDER BY added DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
		if(mysql_num_rows($result) != 0 )
		{
			//print ("<h2>Latest Torrents</h2>");
			print ("<br /><table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr><td class=\"colhead\" width=\"100%\">".$lang_index['new_zuixin']."</td><td class=\"colhead\" align=\"center\">大小</td><td class=\"colhead\" align=\"center\">".$lang_index['col_seeder']."</td><td class=\"colhead\" align=\"center\">".$lang_index['col_leecher']."</td><td class=\"colhead\" align=\"center\">完成</td></tr>");

			while( $row = mysql_fetch_assoc($result) )
			{
				print ("<tr><td><a href=\"details.php?id=". $row['id'] ."&amp;hit=1\"><b>" . htmlspecialchars("[".$row['categories']."]".$get_second_name['audiocodec'][$row["audiocodec"]]) . htmlspecialchars($row['name']) . "</b></a></td><td align=\"center\">" . mksize($row['size']) . "</td><td align=\"center\">" . $row['seeders'] . "</td><td align=\"center\">" . $row['leechers'] . "</td><td align=\"center\">" . $row['times_completed'] . "</td></tr>");
			}
			print ("</table>");
		}
		
			$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();
}


	
// ------------- end: latest torrents ------------------//




// ------------- start: latest torrents ------------------//

if ($showlastxtorrents_main == "yes") {
	$Cache->new_page('Hotest_Torrents', 3200, true);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
		$result = sql_query("SELECT torrents.id as id, categories.name AS categories , torrents.name as name , seeders , leechers,size,times_completed,audiocodec  FROM torrents LEFT JOIN categories ON torrents.category=categories.id where categories.mode = '$browsecatmode' and picktype = 'hot' and havenoseed = 'no' and url = '' and banned = 'no' and seeders >0  ORDER BY ID DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
		

		if(mysql_num_rows($result) != 0 )
		{
			//print ("<h2>Hotest Torrents</h2>");
			print ("<br /><table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr><td class=\"colhead\" width=\"100%\">".$lang_index['new_remen']."</td><td class=\"colhead\" align=\"center\">大小</td><td class=\"colhead\" align=\"center\">".$lang_index['col_seeder']."</td><td class=\"colhead\" align=\"center\">".$lang_index['col_leecher']."</td><td class=\"colhead\" align=\"center\">完成</td></tr>");

			while( $row = mysql_fetch_assoc($result) )
			{
				print ("<tr><td><a href=\"details.php?id=". $row['id'] ."&amp;hit=1\"><b>" . htmlspecialchars("[".$row['categories']."]".$get_second_name['audiocodec'][$row["audiocodec"]]) . htmlspecialchars($row['name']) . "</b></a></td><td align=\"center\">" . mksize($row['size']) . "</td><td align=\"center\">" . $row['seeders'] . "</td><td align=\"center\">" . $row['leechers'] . "</td><td align=\"center\">" . $row['times_completed'] . "</td></tr>");
			}
			print ("</table>");
		}
		
			$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();

}
// ------------- end: latest torrents ------------------//





// ------------- start: stats ------------------//
// ------------- start: tracker load ------------------//
if ($showtrackerload == "yes") 
{
	$uptimeresult=exec('uptime');
	if ($uptimeresult)
	{
?>
<h2 class="index"><span class="index"><?php echo $lang_index['text_tracker_load'] ?></span></h2>
<table class="index" width="100%" border="1" cellspacing="0" cellpadding="10"><tr><td class="text" align="center">
<?php
	//uptime, work in *nix system
	print ("<div align=\"center\">" . trim($uptimeresult) . "</div>");
	print("</td></tr></table>");
	}
}
// ------------- end: tracker load ------------------//

// ------------- start: disclaimer ------------------//
?>
<h2 class="index"><span class="index"><?php echo $lang_index['text_disclaimer'] ?></span></h2>
<table class="index" width="100%"><tr><td class="text">
  <?php echo $lang_index['text_disclaimer_content'] ?></td></tr></table>
<?php
// ------------- end: disclaimer ------------------//
// ------------- start: links ------------------//
	print("<h2  class=\"index\"><span class=\"index\">".$lang_index['text_links']);
	if (get_user_class() >= $applylink_class)
		print("<font class=\"small\"> - [<a class=\"altlink\" href=\"linksmanage.php?action=apply\"><b>".$lang_index['text_apply_for_link']."</b></a>]</font>");
	if (get_user_class() >= $linkmanage_class)
	{
		print("<font class=\"small\">");
		print(" - [<a class=\"altlink\" href=\"linksmanage.php\"><b>".$lang_index['text_manage_links']."</b></a>]\n");
		print("</font>");
	}
	print("</span></h2>");
	$Cache->new_page('links', 86400, false);
	if (!$Cache->get_page()){
	$Cache->add_whole_row();
	$res = sql_query("SELECT * FROM links ORDER BY Y,X ASC") or sqlerr(__FILE__, __LINE__);
	$y=0;
	if (mysql_num_rows($res) > 0)
	{
		$linksdisplay = $links = "";
		while($array = mysql_fetch_array($res))
		{
		if($y!=$array['Y'])
		{
			$y=$array['Y'];
			if($y>1)$linksbr ="<br>";
		}else  $linksbr="";
			
			
		if($y==0)
		$links .= "<a href=\"" . $array['url'] . "\" title=\"" . $array['title'] . "\" target=\"_blank\">" . $array['name'] . "</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		else
		$linksdisplay .= "<a href=\"" . $array['url'] . "\" title=\"" . $array['title'] . "\" target=\"_blank\">" . $array['name'] . "</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$linksbr;
			
			
			
		}
		if($linksdisplay)$linksdisplay="<div class='hidelink' style='display: none;'>".trim($linksdisplay)."</div>";
		print("<table class=\"index\" width=\"100%\" id='sitelink'><tr><td class=\"text\">".trim($links).$linksdisplay."</td></tr></table>");
		print('<script>$("#sitelink").hover(function(){$(this).find(".hidelink").stop(true,true).slideDown(100); },function(){$(this).find(".hidelink").slideUp(100);});</script>');
	}
	$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();
			
// ------------- end: links ------------------//
// ------------- start: browser, client and code note ------------------//
?>

<?php
// ------------- end: browser, client and code note ------------------//
if ($CURUSER)
	$USERUPDATESET[] = "last_home = ".sqlesc(date("Y-m-d H:i:s"));
$Cache->delete_value('user_'.$CURUSER["id"].'_unread_news_count');
end_main_frame();
stdfoot();
?>