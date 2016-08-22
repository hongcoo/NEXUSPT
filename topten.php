<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
parked();
$notshowfriendstags=true;$thispagewidthscreen=false;
function bark($msg) {
	global $lang_topten;
	genbark($msg, $lang_topten['std_error']);
}
if (get_user_class() < $topten_class){
	stderr($lang_topten['std_sorry'],$lang_topten['std_permission_denied_only'].get_user_class_name($topten_class,false,true,true).$lang_topten['std_or_above_can_view'],false);
}

function usershare_table($res, $frame_caption)
{
	global $lang_topten;
	global $CURUSER;
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();
?>
<tr>
<td class="colhead"><?php echo $lang_topten['col_rank'] ?></td>
<td class="colhead" align="left"> <?php echo $lang_topten['col_user'] ?> </td>
<td class="colhead"> <?php echo $lang_topten['col_uploaded'] ?> </td>
<td class="colhead" align="left"> <?php echo $lang_topten['col_ul_speed'] ?> </td>
<td class="colhead"> <?php echo $lang_topten['col_downloaded'] ?></td>
<td class="colhead" align="left"> <?php echo $lang_topten['col_dl_speed'] ?> </td>
<td class="colhead" align="right"> <?php echo $lang_topten['col_ratio'] ?> </td>
<td class="colhead" align="left"> <?php echo $lang_topten['col_joined'] ?> </td>
</tr>
<?php
$num = 0;
while ($a = mysql_fetch_assoc($res))
{
	++$num;
	if ($a["downloaded"])
	{
		$ratio = $a["uploaded"] / $a["downloaded"];
		$color = get_ratio_color($ratio);
		$ratio = number_format($ratio, 2);
		if ($color)
		$ratio = "<font color=\"$color\">$ratio</font>";
	}
	else
		$ratio = $lang_topten['text_inf'];
	print("<tr><td class=\"rowfollow\" align=\"center\">$num</td><td class=\"rowfollow\" align=\"left\">" . get_username($a["userid"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . mksize($a["uploaded"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . mksize($a["upspeed"]) . "/s" .
	"</td><td class=\"rowfollow\" align=\"right\">" . mksize($a["downloaded"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . mksize($a["downspeed"]) . "/s" .
	"</td><td class=\"rowfollow\" align=\"right\">" . $ratio .
	"</td><td class=\"rowfollow\" align=\"left\">" . gettime($a["added"],true,false). "</td></tr>");
}
end_table();
end_frame();
}

function _torrenttable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();
?>
<tr>
<td class="colhead" align="center"><?php echo $lang_topten['col_rank'] ?></td>
<td class="colhead" align="left"><?php echo $lang_topten['col_name'] ?></td>
<td class="colhead" align="right"><?php echo "<img class=\"snatched\" src=\"pic/trans.gif\" alt=\"snatched\" title=\"".$lang_topten['title_sna']."\" />" ?></td>
<td class="colhead" align="right"><?php echo $lang_topten['col_data'] ?></td>
<td class="colhead" align="right"><?php echo "<img class=\"seeders\" src=\"pic/trans.gif\" alt=\"seeders\" title=\"".$lang_topten['title_se']."\" />" ?></td>
<td class="colhead" align="right"><?php echo "<img class=\"leechers\" src=\"pic/trans.gif\" alt=\"leechers\" title=\"".$lang_topten['title_le']."\" />" ?></td>
<td class="colhead" align="right"><?php echo $lang_topten['col_to'] ?></td>
<td class="colhead" align="right"><?php echo $lang_topten['col_ratio'] ?></td>
</tr>
<?php
$num = 0;
while ($a = mysql_fetch_assoc($res))
{
	++$num;
	if ($a["leechers"])
	{
		$r = $a["seeders"] / $a["leechers"];
		$ratio = "<font color=\"" . get_ratio_color($r) . "\">" . number_format($r, 2) . "</font>";
	}
	else
	$ratio = $lang_topten['text_inf'];
	print("<tr><td class=\"rowfollow\" align=\"center\">$num</td><td class=\"rowfollow\" align=\"left\"><a href=\"details.php?id=" . $a["id"] . "&amp;hit=1\"><b>" .
	$a["name"] . "</b></a></td><td class=\"rowfollow\" align=\"right\">" . number_format($a["times_completed"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . mksize($a["data"]) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($a["seeders"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . number_format($a["leechers"]) . "</td><td class=\"rowfollow\" align=\"right\">" . ($a["leechers"] + $a["seeders"]) .
	"</td><td class=\"rowfollow\" align=\"right\">$ratio</td>\n");
}
end_table();
end_frame();
}

function countriestable($res, $frame_caption, $what)
{
	global $CURUSER;
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();
?>
<tr>
<td class="colhead"><?php echo $lang_topten['col_rank'] ?></td>
<td class="colhead" align="left"><?php echo $lang_topten['col_country'] ?></td>
<td class="colhead" align="right"><?php echo $what?></td>
</tr>
<?php
$num = 0;
while ($a = mysql_fetch_assoc($res))
{
	++$num;
	if ($what == $lang_topten['col_users'])
	$value = number_format($a["num"]);
	elseif ($what == $lang_topten['col_uploaded'])
	$value = mksize($a["ul"]);
	elseif ($what == $lang_topten['col_average'])
	$value = mksize($a["ul_avg"]);
	elseif ($what == $lang_topten['col_ratio'])
	$value = number_format($a["r"],2);
	print("<tr><td class=\"rowfollow\" align=\"center\">$num</td><td class=\"rowfollow\" align=\"left\"><table border=\"0\" class=\"main\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"embedded\">".
	"<img align=\"center\" src=\"pic/flag/$a[flagpic]\" alt=\"\" /></td><td class=\"embedded\" style='padding-left: 5px'><b>$a[name]</b></td>".
	"</tr></table></td><td class=\"rowfollow\" align=\"right\">$value</td></tr>\n");
}
end_table();
end_frame();
}

function peerstable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_username']."</td><td class=\"colhead\">".$lang_topten['col_upload_rate']."</td><td class=\"colhead\">".$lang_topten['col_download_rate']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		print("<tr><td class=\"rowfollow\">$n</td><td class=\"rowfollow\">" . get_username($arr["userid"]) . "</td><td class=\"rowfollow\">" . mksize($arr["uprate"]) . "/s</td><td class=\"rowfollow\">" . mksize($arr["downrate"]) . "/s</td></tr>\n");
		++$n;
	}

	end_table();
	end_frame();
}

function bonustable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_username']."</td><td class=\"colhead\">".$lang_topten['col_bonus']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		//die();
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . get_username($arr["id"]) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["seedbonus"], 1) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function prolinkclicktable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_username']."</td><td class=\"colhead\">".$lang_topten['col_clicks']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		//die();
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . get_username($arr["userid"]) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["count"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function charityTable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_username']."</td><td class=\"colhead\">".$lang_topten['col_bonus']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		//die();
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . get_username($arr["id"]) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["charity"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function cmttable($res, $frame_caption, $col2_name)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_username']."</td><td class=\"colhead\">".$col2_name."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . get_username($arr["userid"]) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["num"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function locationtable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_location']."</td><td class=\"colhead\">".$lang_topten['col_number']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . get_username($arr["location_name"]) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["num"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function postable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_username']."</td><td class=\"colhead\">".$lang_topten['col_topics']."</td><td class=\"colhead\">".$lang_topten['col_posts']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . get_username($arr["userid"]) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["usertopics"]) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["userposts"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function bigtopic_table($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_subject']."</td><td class=\"colhead\">".$lang_topten['col_posts']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		// topics_posts.topicid, topics_posts.postnum, forums.id as forumid
		$topic = "<a href =\"forums.php?action=viewtopic&forumid=" . $arr["forumid"] . "&topicid=" . $arr["topicid"] . "\">" . $arr["topicsubject"] . "</a>";
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . $topic. "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["postnum"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function donortable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_username']."</td><td class=\"colhead\">".$lang_topten['col_donated_usd']."</td><td class=\"colhead\">".$lang_topten['col_donated_cny']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . get_username($arr["id"]) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["donated"], 2) . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["donated_cny"], 2) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function clienttable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_name']."</td><td class=\"colhead\">".$lang_topten['col_number']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . $arr["client_name"] . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["client_num"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function lastsearch_table($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_keyword']."</td><td class=\"colhead\">".$lang_topten['col_datetime']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\"><a href=\"torrents.php?search=" . rawurlencode($arr["keywords"]) . "\">" . $arr["keywords"] . "</a></td><td class=\"rowfollow\" align=\"right\">" . gettime($arr["adddate"], true,false) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function search_ranktable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_keyword']."</td><td class=\"colhead\">".$lang_topten['col_times']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\"><a href=\"torrents.php?search=" . rawurlencode($arr["keywords"]) . "\">" . $arr["keywords"] . "</a></td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["count"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}


function supply_snatchtable($res, $frame_caption)
{
	global $lang_topten;
	global $CURUSER;
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();
?>

<tr>
<td class="colhead"><?php echo $lang_topten['col_rank'] ?></td>
<td class="colhead" align="left"> <?php echo $lang_topten['col_user'] ?> </td>
<td class="colhead" align="left"> <?php echo $lang_topten['col_torrent_uploaded'] ?> </td>
<td class="colhead"> <?php echo $lang_topten['col_uploaded'] ?> </td>
<td class="colhead" align="left"> <?php echo $lang_topten['col_torrent_downloaded'] ?> </td>
<td class="colhead"> <?php echo $lang_topten['col_downloaded'] ?></td>
<td class="colhead" align="right"> <?php echo $lang_topten['col_ratio'] ?> </td>
<td class="colhead" align="left"> <?php echo $lang_topten['col_joined'] ?> </td>
</tr>
<?php
$num = 0;
while ($a = mysql_fetch_assoc($res))
{
	++$num;
	if ($a["downloaded"])
	{
		$ratio = $a["uploaded"] / $a["downloaded"];
		$color = get_ratio_color($ratio);
		$ratio = number_format($ratio, 2);
		if ($color)
		$ratio = "<font color=\"$color\">$ratio</font>";
	}
	else
	$ratio = $lang_topten['text_inf'];
	print("<tr><td class=\"rowfollow\" align=\"center\">$num</td><td class=\"rowfollow\" align=\"left\">" . get_username($a["userid"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . number_format($a["supplied"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . mksize($a["uploaded"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . number_format($a["snatched"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . mksize($a["downloaded"]) .
	"</td><td class=\"rowfollow\" align=\"right\">" . $ratio .
	"</td><td class=\"rowfollow\" align=\"left\">" . gettime($a["added"]). "</td></tr>");
}
end_table();
end_frame();
}

function stylesheettable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_name']."</td><td class=\"colhead\">".$lang_topten['col_number']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		//die();
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . $arr["stylesheet_name"] . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["stylesheet_num"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

function languagetable($res, $frame_caption)
{
	global $lang_topten;
	begin_frameindex($frame_caption, true);
	begin_table();

	print("<tr><td class=\"colhead\">".$lang_topten['col_rank']."</td><td class=\"colhead\">".$lang_topten['col_name']."</td><td class=\"colhead\">".$lang_topten['col_number']."</td></tr>");

	$n = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		//die();
		print("<tr><td class=\"rowfollow\" align=\"center\">$n</td><td class=\"rowfollow\" align=\"left\">" . $arr["lang_name"] . "</td><td class=\"rowfollow\" align=\"right\">" . number_format($arr["lang_num"]) . "</td></tr>\n");
		$n++;
	}

	end_table();
	end_frame();
}

stdhead($lang_topten['head_top_ten']);
begin_main_frame();
$type = isset($_GET["type"]) ? 0 + $_GET["type"] : 0;
if (!in_array($type,array(1,2,3,4,5,6,7,8)))
$type = 1;
$limit = isset($_GET["lim"]) ? 0 + $_GET["lim"] : false;
$subtype = isset($_GET["subtype"]) ? $_GET["subtype"] : false;

print("<p align=\"center\">"  .
($type == 1 && !$limit ? "<b>".$lang_topten['text_users']."</b>" : "<a href=\"topten.php?type=1\">".$lang_topten['text_users']."</a>") .	" | " .
($type == 2 && !$limit ? "<b>".$lang_topten['text_torrents']."</b>" : "<a href=\"topten.php?type=2\">".$lang_topten['text_torrents']."</a>") . " | " .
//($type == 3 && !$limit ? "<b>".$lang_topten['text_countries']."</b>" : "<a href=\"topten.php?type=3\">".$lang_topten['text_countries']."</a>") . " | " .
//($type == 4 && !$limit ? "<b>".$lang_topten['text_peers']."</b>" : "<a href=\"topten.php?type=4\">".$lang_topten['text_peers']."</a>")  . " | " .
($type == 5 && !$limit ? "<b>".$lang_topten['text_community']."</b>" : "<a href=\"topten.php?type=5\">".$lang_topten['text_community']."</a>")  . " | " .
($type == 7 && !$limit ? "<b>".$lang_topten['text_search']."</b>" : "<a href=\"topten.php?type=7\">".$lang_topten['text_search']."</a>")  . " | " .
($type == 6 && !$limit ? "<b>".$lang_topten['text_other']."</b>" : "<a href=\"topten.php?type=6\">".$lang_topten['text_other']."</a>")  . " | " .
//($type == 8 && !$limit ? "<b>豆瓣TOP</b>" : "<a href=\"topten.php?type=8\">豆瓣TOP</a>")  . 
"</p>\n");

if (!$limit)
$limit = 10;
elseif ($limit > 250)
$limit = 250;
else
$limit = 100;

$cachename = "topten_type_".$type."_limit_".$limit."_subtype_".$subtype;
$cachetime = 60 * (mt_rand (1,15)+58); // 60 minutes
// START CACHE
$Cache->new_page($cachename, $cachetime, true);
if (!$Cache->get_page())
{
$Cache->add_whole_row();

/////////////////////////////////////////////////////////

if ($type == 1)
{
	$mainquery = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed FROM users WHERE enabled = 'yes'";


	if ($limit == 10 || $subtype == "ul")
	{
		$order = "uploaded DESC";
		$r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_uploaders'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=ul\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "dl")
	{
		$order = "downloaded DESC";
		$r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_downloaders']  . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=dl\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "uls")
	{
		$order = "upspeed DESC";
		$extrawhere = " AND uploaded > 53687091200";
		$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_fastest_uploaders'] . "<font class=\"small\">".$lang_topten['text_fastest_up_note'] . "</font>" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=uls\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "dls")
	{
		$order = "downspeed DESC";
		$r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_fastest_downloaders'] ."<font class=\"small\">" . $lang_topten['text_fastest_note'] . "</font>" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=dls\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "bsh")
	{
		$order = "uploaded / downloaded DESC";
		$extrawhere = " AND downloaded > 53687091200";
		$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_best_sharers'] ."<font class=\"small\">".$lang_topten['text_sharers_note']."</font>"  . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=bsh\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "wsh")
	{
		$order = "uploaded / downloaded ASC, downloaded DESC";
		$extrawhere = " AND downloaded > 53687091200";
		$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		usershare_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_worst_sharers'] .$lang_topten['text_sharers_note'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=wsh\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
/*
	if ($limit == 10 || $subtype == "sp")
	{
		$r = sql_query( "SELECT users_torrents.userid, users_torrents.supplied, users_torrents.uploaded, users_torrents.downloaded, users_torrents.added, COUNT(snatched.id) as snatched FROM (SELECT users.id as userid, COUNT(torrents.id) as supplied, users.uploaded, users.downloaded, users.added from users LEFT JOIN torrents ON torrents.owner = users.id GROUP BY userid) as users_torrents LEFT JOIN snatched ON snatched.userid = users_torrents.userid where snatched.finished='yes' AND snatched.torrentid IN(SELECT id FROM torrents where torrents.owner != users_torrents.userid) GROUP BY users_torrents.userid ORDER BY users_torrents.supplied DESC LIMIT $limit") or sqlerr();
		supply_snatchtable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_supplied'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=sp\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "sn")
	{
		$r = sql_query( "SELECT users_torrents.userid, users_torrents.supplied, users_torrents.uploaded, users_torrents.downloaded, users_torrents.added, COUNT(snatched.id) as snatched FROM (SELECT users.id as userid, COUNT(torrents.id) as supplied, users.uploaded, users.downloaded, users.added from users LEFT JOIN torrents ON torrents.owner = users.id GROUP BY userid) as users_torrents LEFT JOIN snatched ON snatched.userid = users_torrents.userid where snatched.finished='yes' AND snatched.torrentid IN(SELECT id FROM torrents where torrents.owner != users_torrents.userid) GROUP BY users_torrents.userid ORDER BY snatched DESC LIMIT $limit") or sqlerr();
		supply_snatchtable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_snatched'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=sn\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}*/
	
	if ($limit == 10 || $subtype == "pc")
	{
		$r = sql_query("SELECT userid, COUNT(DISTINCT(torrent)) AS count FROM peers where  seeder='yes' GROUP BY userid ORDER BY count DESC LIMIT $limit") or sqlerr();
		prolinkclicktable($r, $lang_topten['text_top']."$limit 当前做种数" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=pc\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
	
	if ($limit == 10 || $subtype == "pl")
	{
		$r = sql_query("SELECT userid, COUNT(DISTINCT(torrent)) AS count FROM peers where  seeder='no' GROUP BY userid ORDER BY count DESC LIMIT $limit") or sqlerr();
		prolinkclicktable($r, $lang_topten['text_top']."$limit 当前下载数" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=pl\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
	
	if ($limit == 10 || $subtype == "hr"){
		$r = sql_query("SELECT id as userid, hrwarned AS count FROM users ORDER BY hrwarned DESC LIMIT $limit") or sqlerr();
		prolinkclicktable($r, $lang_topten['text_top']."$limit H&R数" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=hr\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
	
}
elseif ($type == 2)
{
	if ($limit == 10 || $subtype == "act")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_active_torrents']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=act\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=act\">Top 50</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "sna")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_snatched_torrents']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&lim=25&subtype=sna\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=sna\">Top 50</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "mdt")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND times_completed > 0 GROUP BY t.id ORDER BY data DESC, added ASC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_data_transferred_torrents']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=mdt\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=mdt\">Top 50</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "bse")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND seeders >= 5 GROUP BY t.id ORDER BY seeders / leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_best_seeded_torrents']."<font class=\"small\">".$lang_topten['text_best_seeded_torrents_note']."</font>" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=bse\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=bse\">Top 50</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "wse")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed) AS data FROM torrents AS t WHERE leechers > 0 AND times_completed > 0 ORDER BY seeders / leechers ASC, leechers DESC LIMIT $limit") or sqlerr();
		_torrenttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_worst_seeded_torrents']."<font class=\"small\">" . $lang_topten['text_worst_seeded_torrents_note'] . "</font>" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=wse\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=wse\">Top 50</a>]</font>" : ""));
	}
}
/*elseif ($type == 3)
{
	if ($limit == 10 || $subtype == "us")
	{
		$r = sql_query("SELECT name, flagpic, COUNT(users.country) as num FROM countries LEFT JOIN users ON users.country = countries.id GROUP BY name ORDER BY num DESC LIMIT $limit") or sqlerr();
		countriestable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_countries_users']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=us\">Top 25</a>]</font>" : ""),$lang_topten['col_users']);
	}

	if ($limit == 10 || $subtype == "ul")
	{
		$r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded) AS ul FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name ORDER BY ul DESC LIMIT $limit") or sqlerr();
		countriestable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_countries_uploaded']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=ul\">Top 25</a>]</font>" : ""),$lang_topten['col_uploaded']);
	}

	if ($limit == 10 || $subtype == "avg")
	{
		$r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/count(u.id) AS ul_avg FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY ul_avg DESC LIMIT $limit") or sqlerr();
		countriestable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_countries_per_user']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=avg\">Top 25</a>]</font>" : ""),$lang_topten['col_average']);
	}

	if ($limit == 10 || $subtype == "r")
	{
		$r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/sum(u.downloaded) AS r FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND sum(u.downloaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY r DESC LIMIT $limit") or sqlerr();
		countriestable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_countries_ratio']. ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=r\">Top 25</a>]</font>" : ""),$lang_topten['col_ratio']);
	}
}

elseif ($type == 4)
{
	if ($limit == 10 || $subtype == "ul")
	{
		$r = sql_query( "SELECT users.id AS userid, username,snatched.uploaded AS uprate, snatched.downloaded AS downrate FROM peers LEFT JOIN snatched ON snatched.userid = peers.userid AND snatched.torrentid = peers.torrent LEFT JOIN users ON users.id = peers.userid ORDER BY uprate DESC LIMIT $limit") or sqlerr();
		peerstable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_fastest_uploaders'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=ul\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "dl")
	{
		$r = sql_query("SELECT users.id AS userid, username,snatched.uploaded AS uprate, snatched.downloaded AS downrate FROM peers LEFT JOIN snatched ON snatched.userid = peers.userid AND snatched.torrentid = peers.torrent LEFT JOIN users ON users.id = peers.userid ORDER BY downrate DESC LIMIT $limit") or sqlerr();

		peerstable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_fastest_downloaders'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=dl\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "mloc")
	{
		$r = sql_query( "SELECT FROM peers LEFT JOIN locations ON peers.ip GROUP BY users.id ORDER BY commentnum DESC LIMIT $limit") or sqlerr();
		locationtable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_locations'] . ($limit == 10 ? " <font class=\"small\"> - [<a href=\"topten.php?type=$type&lim=100&subtype=mloc>".$lang_topten['text_one_hundred']."</a>] - [<a href=\"topten.php?type=$type&lim=250&subtype=mloc>".$lang_topten['text_top_250']."</a>]</font>" : ""));
	}
}
*/
elseif ($type == 5)
{
	if ($limit == 10 || $subtype == "mtop")
	{
		$r = sql_query( "SELECT users_topics.userid,  users_topics.usertopics, COUNT(posts.id) as userposts FROM (SELECT users.id as userid, COUNT(topics.id) as usertopics from users LEFT JOIN topics ON users.id = topics.userid GROUP BY users.id) as users_topics LEFT JOIN posts ON users_topics.userid = posts.userid GROUP BY users_topics.userid ORDER BY usertopics DESC LIMIT $limit") or sqlerr();
		postable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_topic'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mtop\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
	
	if ($limit == 10 || $subtype == "mpos")
	{
		$r = sql_query( "SELECT users_topics.userid,  users_topics.usertopics, COUNT(posts.id) as userposts FROM (SELECT users.id as userid, COUNT(topics.id) as usertopics from users LEFT JOIN topics ON users.id = topics.userid GROUP BY users.id) as users_topics LEFT JOIN posts ON users_topics.userid = posts.userid GROUP BY users_topics.userid ORDER BY userposts DESC LIMIT $limit") or sqlerr();
		postable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_post'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mpos\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
	
	if ($reviewenabled == 'yes' && ($limit == 10 || $subtype == "mrev"))
	{
		$r = sql_query( "SELECT users.id as userid, COUNT(reviews.id) as num FROM users LEFT JOIN reviews ON users.id = reviews.user GROUP BY users.id ORDER BY num DESC LIMIT $limit") or sqlerr();
		cmttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_reviewer'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mrev\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""), $lang_topten['col_reviews']);
	}	

	if ($limit == 10 || $subtype == "mcmt")
	{
		$r = sql_query( "SELECT users.id as userid, COUNT(comments.id) as num FROM users LEFT JOIN comments ON users.id = comments.user GROUP BY users.id ORDER BY num DESC LIMIT $limit") or sqlerr();
		cmttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_commenter'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mcmt\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""), $lang_topten['col_comments']);
	}
	
	if ($limit == 10 || $subtype == "btop")
	{
		$r = sql_query("SELECT topics_posts.topicid, topics_posts.topicsubject, topics_posts.postnum, forums.id as forumid FROM (SELECT topics.id as topicid, topics.subject as topicsubject, COUNT(posts.id) as postnum, topics.forumid FROM topics LEFT JOIN posts ON topics.id = posts.topicid GROUP BY topics.id) as topics_posts LEFT JOIN forums ON topics_posts.forumid = forums.id AND forums.minclassread <= 1 ORDER BY postnum DESC LIMIT $limit") or sqlerr();
		bigtopic_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_biggest_topics'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=btop\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
}
elseif ($type == 6)
{
	if ($limit == 10 || $subtype == "bo")
	{
		$r = sql_query("SELECT * FROM users ORDER BY seedbonus DESC LIMIT $limit") or sqlerr();
		bonustable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_bonuses'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=bo\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
	
		if ($limit == 10 || $subtype == "bop")
	{
		$r = sql_query("SELECT id , seedbonusper as seedbonus FROM users ORDER BY seedbonusper DESC LIMIT $limit") or sqlerr();
		bonustable($r, $lang_topten['text_top']."$limit 每小时获取魔力值" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=bop\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

if ($prolinkpoint_bonus){
	if ($limit == 10 || $subtype == "pl")
	{
		$r = sql_query("SELECT userid, COUNT(id) AS count FROM prolinkclicks GROUP BY userid ORDER BY count DESC LIMIT $limit") or sqlerr();
		prolinkclicktable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_clicks'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=pl\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
}


	if ($limit == 10 || $subtype == "charity")
	{
		$r = sql_query("SELECT * FROM users ORDER BY charity DESC LIMIT $limit") or sqlerr();
		charityTable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_charity_giver'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=charity\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

if ($enabledonation == 'yes'){
	/*if ($limit == 10 || $subtype == "do_usd")
	{
		$r = sql_query( "SELECT id, donated, donated_cny from users where donated > 0 ORDER BY donated DESC, donated_cny DESC LIMIT $limit") or sqlerr();
		donortable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_donated_USD'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=do_usd\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}*/

	if ($limit == 10 || $subtype == "do_cny")
	{
		$r = sql_query( "SELECT id, donated, donated_cny from users where donated_cny > 0 ORDER BY  donated_cny DESC , donated DESC LIMIT $limit") or sqlerr();
		donortable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_donated_CNY'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=do_cny\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
}
	
	
	/*if ($limit == 10 || $subtype == "mbro")
	{
		$r = sql_query( "SELECT id, donated, donated_cny from users where donated_cny > 0 ORDER BY donated DESC, donated_cny DESC LIMIT $limit") or sqlerr();
		donortable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_browser'] . ($limit == 10 ? " <font class=\"small\"> - [<a href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mbro\">".$lang_topten['text_one_hundred']."</a>] - [<a href=\"topten.php?type=$type&amp;lim=250&amp;subtype=mbrossssssssssss]</font>" : ""));
	}
	*/
	
	if ($limit == 10 || $subtype == "mcli")
	{
		$r = sql_query( "SELECT agent_allowed_family.family as client_name, COUNT(users.id) as client_num from users RIGHT JOIN agent_allowed_family ON agent_allowed_family.id = users.clientselect where last_access >= ". sqlesc(date("Y-m-d H:i:s",(TIMENOW - 3600*24*30)))." GROUP BY clientselect ORDER BY client_num DESC LIMIT $limit") or sqlerr();
		clienttable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_client'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=mcli\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
	
				if ($limit == 10 || $subtype == "pagenow")
	{
		$r =sql_query("SELECT COUNT(DISTINCT(id)) as client_num ,page as client_name FROM users WHERE last_access  > ".sqlesc(date("Y-m-d H:i:s",TIMENOW- $cachetime))." GROUP BY PAGE ORDER BY client_num DESC LIMIT $limit" );
		clienttable($r, $lang_topten['text_top']."$limit 当前页面在线数" . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=pagenow\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
	
	
	if ($limit == 10 || $subtype == "ss")
	{
		$r = sql_query( "SELECT stylesheets.name as stylesheet_name, COUNT(users.id) as stylesheet_num from users JOIN stylesheets ON stylesheets.id = users.stylesheet GROUP BY stylesheet ORDER BY stylesheet_num DESC LIMIT $limit") or sqlerr();
		stylesheettable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_stylesheet'] . ($limit == 10 ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=ss\">Top 25</a>] - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=50&amp;subtype=ss\">Top 50</a>]</font>" : ""));
	}
	if ($limit == 10 || $subtype == "lang")
	{
		$r = sql_query( "SELECT language.lang_name as lang_name, COUNT(users.id) as lang_num from users JOIN language ON language.id = users.lang WHERE site_lang=1 GROUP BY lang ORDER BY lang_num DESC LIMIT $limit") or sqlerr();
		languagetable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_most_language'] . ($limit == 10 ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=25&amp;subtype=lang\">Top 25</a>]</font>" : ""));
	}
}

elseif ($type == 7)	// search
{
	if ($limit == 10 || $subtype == "lse")
	{
		$r = sql_query( "SELECT keywords , newupdate AS adddate FROM suggest ORDER BY newupdate DESC LIMIT $limit") or sqlerr();
		lastsearch_table($r, $lang_topten['text_top']."$limit ".$lang_topten['text_latest_search'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=lse\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "tcmo")
	{
		$current_month = sqlesc(date('Y-m-d',strtotime(date('Y-m', time()).'-01')));
		$r = sql_query("SELECT keywords , sum(times) AS count FROM suggest WHERE adddate >=" . $current_month . "  GROUP BY keywords ORDER BY count DESC LIMIT $limit") or sqlerr();
		search_ranktable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_current_month_search'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=tcmo\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}

	if ($limit == 10 || $subtype == "tlmo")
	{
		$last_month_begin = sqlesc(date('Y-m-d',strtotime('-1 month', strtotime(date('Y-m', time()).'-01'))));
		$last_month_end = sqlesc(date('Y-m-d',strtotime(date('Y-m', time()).'-01')));
		$r = sql_query("SELECT keywords , sum(times) AS count FROM suggest WHERE adddate >=" . $last_month_begin . " AND adddate <" . $last_month_end . "  GROUP BY keywords ORDER BY count DESC LIMIT $limit") or sqlerr();
		search_ranktable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_last_month_search'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=tlmo\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
	
	if ($limit == 10 || $subtype == "tcy")
	{
		$current_year = sqlesc(date('Y-m-d',strtotime(date('Y', time()).'-01-01')));
		$r = sql_query("SELECT keywords , sum(times) AS count FROM suggest WHERE adddate >=" . $current_year . "  GROUP BY keywords ORDER BY count DESC LIMIT $limit") or sqlerr();
		search_ranktable($r, $lang_topten['text_top']."$limit ".$lang_topten['text_current_year_search'] . ($limit == 10 ? " <font class=\"small\"> - [<a class=\"altlink\" href=\"topten.php?type=$type&amp;lim=100&amp;subtype=tcy\">".$lang_topten['text_one_hundred']."</a>]</font>" : ""));
	}
}
elseif ($type == 8)	// search
{?>
<style type="text/css">
#list_view tr.item{cursor:pointer}
#list_view tr.info{color:#666;display:none}
#list_view tr.info td{padding:0 10px}
#list_view tr.info .quote{margin:5px 0}
#list_view td{
padding:5px 0;
border-right: #004F4F 0px solid;
border-left: #004F4F 0px solid;
}
#list_view td.m_order{text-align:left;}
#list_view td a{display:inline-block;*zoom:1;line-height:1em;padding:1px}
#list_view td em{color:#ff5c38}
#list_view td .year{margin-left:3px;color:#666}
#list_view .highlight,#list_view .expanded{background:#eefcee}
#list_view .expanded td{border:0}

.quote span.inq {
display: inline;
color: #333;
padding-right: 15px;
word-break: break-all;
width: auto;
}


</style>


<?
//if (!$moderators = $Cache->get_value('douban_top250'))
//		$Cache->cache_value('douban_top250', $moderators=file_get_contents_function('http://movie.douban.com/top250?format=text'),3600);
		
		
		
	$cachefile="imdb/top250.cache";
	
	if(@$fp = fopen ($cachefile,"rb"))while (!feof ($fp))$moderators .= fread($fp, 4096);
	fclose($fp);
	$cachetime=filemtime($cachefile);
	
	if ((!$Cache->get_value('imdb_top250_cache_ing'))&&(time()- $cachetime > 3600*24*30)){
		$Cache->cache_value('imdb_top250_cache_ing','ing' ,3600*7);	
		if ($moderators_get=file_get_contents_function('http://movie.douban.com/top250?format=text')){
		@$fp2 = fopen ($cachefile, "w");
		fputs ($fp2,$moderators =$moderators_get);
		fclose($fp2);	
		}			
	}	
	
	
			
	

	//fclose($fp);
	
	begin_main_frame();
	begin_frameindex('豆瓣电影TOP250',true);
	
		preg_match_all("/(<tbody>.*<\/tbody>)/sim",$moderators,$text);
		$s=$text[1][0];
		//$s = preg_replace("/\ headers=\"[^\"]*\"/sim","",$text[1][0]);
		$s = preg_replace("/http:\/\/movie.douban.com\/subject\/(\d*)\//ie", "'torrents.php?search_area=4&search=\\1'", $s);
		
		//xml_parse_into_struct(xml_parser_create(),$text[1][0],$dbarray);
	begin_table(false,5,'list_view');
	?>
	<thead>
        <tr>
            <td class="colhead" width="20">排名</th>
            <td class="colhead" width="520">名称</th>
            <td class="colhead" width="39">评分</th>
            <td class="colhead">评价人数</th>
        </tr>
    </thead>
		
		<?
	print($s);
	end_table();
	print "数据更新时间:".date("Y-m-d H:i:s",$cachetime);
	end_frame();
	end_main_frame();
		?><script>
    $(function() {
        $('#list_view .item').toggle(function(e) {
            var oLink = $(e.target);
            if (oLink.is('a')) {
                location.href = oLink.attr('href');
                return;
            }
            $(this).next('.info').show();
        }, function(e) {
            var oLink = $(e.target);
            if (oLink.is('a')) {
                location.href = oLink.attr('href');
                return;
            }
            $(this).next('.info').hide();
        });
    });
</script><?
}

	end_main_frame();
	print("<p><font class=\"small\">".$lang_topten['text_this_page_last_updated'].date('Y-m-d H:i:s'). ", ".$lang_topten['text_started_recording_date'].$datefounded.$lang_topten['text_update_interval']."</font></p>");
	$Cache->end_whole_row();
	$Cache->cache_page();
}
echo $Cache->next_row();
stdfoot();
?>
