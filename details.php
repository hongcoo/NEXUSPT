<?php
ob_start(); //Do not delete this line
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
if ($showextinfo['imdb'] == 'yes')
	require_once("imdb/imdb.class.php");
loggedinorreturn();
parked();




$id = 0 + $_GET["id"];

int_check($id);
if (!isset($id) || !$id)
die();

$res = sql_query("SELECT torrents.cache_stamp,torrents.promotion_time_type, torrents.urltype, torrents.sp_state, torrents.url, torrents.small_descr, torrents.seeders, torrents.banned,torrents.promotion_until, torrents.leechers, torrents.info_hash, torrents.filename, nfo, LENGTH(torrents.nfo) AS nfosz, torrents.last_action, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, torrents.anonymous, torrents.picktype, categories.name AS cat_name, sources.name AS source_name, media.name AS medium_name, codecs.name AS codec_name, standards.name AS standard_name, processings.name AS processing_name, teams.name AS team_name, audiocodecs.name AS audiocodec_name ,torrents.audiocodec, finished FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN sources ON torrents.source = sources.id LEFT JOIN media ON torrents.medium = media.id LEFT JOIN codecs ON torrents.codec = codecs.id LEFT JOIN standards ON torrents.standard = standards.id LEFT JOIN processings ON torrents.processing = processings.id LEFT JOIN teams ON torrents.team = teams.id LEFT JOIN audiocodecs ON torrents.audiocodec = audiocodecs.id LEFT JOIN  snatched ON torrentid = torrents.id and userid={$CURUSER['id']} WHERE torrents.id = $id LIMIT 1")
or sqlerr();
$row = mysql_fetch_array($res);

if (get_user_class() >= $torrentmanage_class || $CURUSER["id"] == $row["owner"]||$CURUSER["picker"] == 'yes')
$owned = 1;
else $owned = 0;

if (!$row)
	stderr($lang_details['std_error'], $lang_details['std_no_torrent_id']);
elseif ($row['banned'] == 'yes' && get_user_class() < $seebanned_class&&$CURUSER["picker"] != 'yes')
	permissiondenied();
else {
	if ($_GET["hit"]) {
		sql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
	}

	if (!isset($_GET["cmtpage"])) {
		//stdhead($lang_details['head_details_for_torrent']. "\"" . $row["name"] . "\"");
	stdhead($row["name"]);
		if ($_GET["uploaded"])
		{
			print("<h1 align=\"center\">".$lang_details['text_successfully_uploaded']."</h1>");
			print("<p>".$lang_details['text_redownload_torrent_note']."</p>");
			
			print("<script>jQuery.get('retriver.php?',{id:".$id.",type:2,isjq:1});</script>");


			
			header("refresh: 1; url=download.php?id=$id");
			//header("refresh: 1; url=getimdb.php?id=$id&type=1");
			//http://pt.antsoul.com/retriver.php?id=1377&type=2
		}
		elseif ($_GET["edited"]) {
			print("<h1 align=\"center\">".$lang_details['text_successfully_edited']."</h1>");
			if (isset($_GET["returnto"]))
				print("<p><b>".$lang_details['text_go_back'] . "<a href=\"".htmlspecialchars($_GET["returnto"])."\">" . $lang_details['text_whence_you_came']."</a></b></p>");
				print("<script>jQuery.get('retriver.php?',{id:".$id.",type:2,isjq:1});</script>");
		}
		$sp_torrent =get_torrent_promotion_append($row['sp_state'],$row['audiocodec'],"word",true,$row["added"], $row['promotion_time_type'], $row['promotion_until']);
		

	$picked_torrent = sp_torrent_type($row['picktype']);
	
		$s=$picked_torrent.htmlspecialchars($row["name"]).$sp_torrent;
		print("<h1 align=\"center\" id=\"top\">".$s."</h1>\n");
		print("<table width=\"98%\" cellspacing=\"0\" cellpadding=\"5\">\n");

		$url = "edit.php?id=" . $row["id"];
		if (isset($_GET["returnto"])) {
			$url .= "&returnto=" . rawurlencode($_GET["returnto"]);
		}
		$editlink = "a title=\"".$lang_details['title_edit_torrent']."\" href=\"$url\"";

		// ------------- start upped by block ------------------//

		if($row['anonymous'] == 'yes') {
			if (get_user_class() < $viewanonymous_class)
			$uprow = "<i>".$lang_details['text_anonymous']."</i>";
			else
			$uprow = "<i>".$lang_details['text_anonymous']."</i> (" . get_username($row['owner'], false, true, true, false, false, true) . ")";
		}
		else {
			$uprow = (isset($row['owner']) ? get_username($row['owner'], false, true, true, false, false, true) : "<i>".$lang_details['text_unknown']."</i>");
		}
		
		
	if ($CURUSER["downloadpos"] != "no")
		{
			
			if ($CURUSER['timetype'] != 'timealive')
				$uploadtime = $lang_details['text_at'].$row['added'];
			else $uploadtime = $lang_details['text_blank'].gettime($row['added'],true,false);
		}
		else
			tr($lang_details['row_download'], $lang_details['text_downloading_not_allowed']);

		


		if ($CURUSER["id"] == $row["owner"])$CURUSER["downloadpos"] = "yes";
		
	
		if (($smalldescription_main == 'yes')&&(trim($row["small_descr"])!=""))tr($lang_details['row_small_description'],htmlspecialchars(trim($row["small_descr"])),true);
		
		
				$size_info =  "&nbsp;&nbsp;&nbsp;<b>".$lang_details['text_size']."</b>" . mksize($row["size"]);
		$type_info = "<b>".$lang_details['row_type'].":</b>&nbsp;".$row["cat_name"];
		if (isset($row["source_name"]))
			$source_info = "&nbsp;&nbsp;&nbsp;<b>".$lang_details['text_source']."&nbsp;</b>".$row[source_name];
		if (isset($row["medium_name"]))
			$medium_info = "&nbsp;&nbsp;&nbsp;<b>".$lang_details['text_medium']."&nbsp;</b>".$row[medium_name];
		if (isset($row["codec_name"]))
			$codec_info = "&nbsp;&nbsp;&nbsp;<b>".$lang_details['text_codec']."&nbsp;</b>".$row[codec_name];
		if (isset($row["standard_name"]))
			$standard_info = "&nbsp;&nbsp;&nbsp;<b>".$lang_details['text_stardard']."&nbsp;</b>".$row[standard_name];
		if (isset($row["processing_name"]))
			$processing_info = "&nbsp;&nbsp;&nbsp;<b>".$lang_details['text_processing']."&nbsp;</b>".$row[processing_name];
		if (isset($row["team_name"]))
			$team_info = "&nbsp;&nbsp;&nbsp;<b>".$lang_details['text_team']."&nbsp;</b>".$row[team_name];
		if (isset($row["audiocodec_name"]))
			$audiocodec_info = "-".$row[audiocodec_name];
tr_small("种子信息", $lang_details['row_upped_by'] . "&nbsp;".$uprow. "&nbsp;".$uploadtime, 1);
		tr_small($lang_details['row_basic_info'], $type_info.$audiocodec_info.$size_info.$source_info . $medium_info. $codec_info .  $standard_info . $processing_info . $team_info, 1);
		
		
$snatchedbgcolour=array("yes"=>"style='background-color: Green;'");
$unsnatchedbgcolour=array("no"=>"style='background-color: Red;'");
$seederbgcolour=array("yes"=>"style='background-color: Green;'");
$leecherbgcolour=array("no"=>"style='background-color: Red;'");		
$snatchedfont=array("no"=>"<font color='red'>[未完成]</font>","yes"=>"<font color='green'>[已完成]</font>",""=>"[未下载]");	
		
if ($CURUSER["downloadpos"] == "no"||$row['added']==$row['last_action']&&$CURUSER["id"] != $row["owner"])
$download = "";
else 
$download = "<a title=\"".$lang_details['title_download_torrent']."\" href=\"download.php?id=".$id."\"><img class=\"dt_download\" src=\"pic/trans.gif\" alt=\"download\" />&nbsp;<b><font class=\"small\">".$lang_details['text_download_torrent'].($snatchedfont[$row['finished']])."</font></b></a>&nbsp;|&nbsp;";


		//tr($lang_details['row_action'], $download. ($owned == 1 ? "<$editlink><img class=\"dt_edit\" src=\"pic/trans.gif\" alt=\"edit\" />&nbsp;<b><font class=\"small\">".$lang_details['text_edit_torrent'] . "</font></b></a>&nbsp;|&nbsp;" : "").  (get_user_class() >= $askreseed_class && $row[seeders] == 0 ? "<a title=\"".$lang_details['title_ask_for_reseed']."\" href=\"takereseed.php?reseedid=$id\"><img class=\"dt_reseed\" src=\"pic/trans.gif\" alt=\"reseed\">&nbsp;<b><font class=\"small\">".$lang_details['text_ask_for_reseed'] ."</font></b></a>&nbsp;|&nbsp;" : "") . "<a title=\"".$lang_details['title_report_torrent']."\" href=\"report.php?torrent=$id\"><img class=\"dt_report\" src=\"pic/trans.gif\" alt=\"report\" />&nbsp;<b><font class=\"small\">".$lang_details['text_report_torrent']."</font></b></a>", 1);

		// ---------------- start subtitle block -------------------//

		$moviename = "";
		$imdb_id = parse_imdb_id($row["url"]);
		if ($imdb_id && $showextinfo['imdb'] == 'yes')
		{
			$thenumbers = $imdb_id;
			if (!$moviename = $Cache->get_value('imdb_id_'.$thenumbers.'_movie_name')){
				$movie = new imdb ($thenumbers);
				$target = array('Title');
				switch ($movie->cachestate($target)){
					case "1":{
						$moviename = $movie->title (); break;
						$Cache->cache_value('imdb_id_'.$thenumbers.'_movie_name', $moviename, 1296000);
					}
					default: break;
				}
			}
		}

		// ---------------- end subtitle block -------------------//
		$torrentdetailad=$Advertisement->get_ad('torrentdetail');

		if ($CURUSER['showdescription'] != 'no' && !empty($row["descr"])){
		tr("<a href=\"javascript: klappe_news('descr')\"><img class=\"minus\" src=\"pic/trans.gif\" alt=\"Show/Hide\" id=\"picdescr\" title=\"".$lang_detail['title_show_or_hide']."\" /> ".$lang_details['row_description']."</a>","<div id='kdescr'>".format_comment($row["descr"])."</div>". ($Advertisement->enable_ad() && $torrentdetailad ? "<div align=\"left\" style=\"margin-bottom: 10px\" id=\"ad_torrentdetail\">".$torrentdetailad[0]."</div>" : ""), 1);

		//tr($lang_details['row_description']."</span>", "<div id='kdescr'>".($Advertisement->enable_ad() && $torrentdetailad ? "<div align=\"left\" style=\"margin-bottom: 10px\" id=\"ad_torrentdetail\">".$torrentdetailad[0]."</div>" : "").format_comment($row["descr"])."</div>", 1);
		}else
				tr("<a href=\"javascript: klappe_news('descr')\"><img class=\"plus\" src=\"pic/trans.gif\" alt=\"Show/Hide\" id=\"picdescr\" title=\"".$lang_detail['title_show_or_hide']."\" /> ".$lang_details['row_description']."</a>","<div id='kdescr' style=\"display: none;\" >".format_comment($row["descr"])."</div>". ($Advertisement->enable_ad() && $torrentdetailad ? "<div align=\"left\" style=\"margin-bottom: 10px\" id=\"ad_torrentdetail\" >".$torrentdetailad[0]."</div>" : ""), 1);


		if (get_user_class() >= $viewnfo_class && $CURUSER['shownfo'] != 'no' && $row["nfosz"] > 0 && $enablenfo_main=='yes'){
			if (!$nfo = $Cache->get_value('nfo_block_torrent_id_'.$id)){
				$nfo = code($row["nfo"], $view == "magic");
				$Cache->cache_value('nfo_block_torrent_id_'.$id, $nfo, 3467);
			}
			tr("<a href=\"javascript: klappe_news('nfo')\"><img class=\"plus\" src=\"pic/trans.gif\" alt=\"Show/Hide\" id=\"picnfo\" title=\"".$lang_detail['title_show_or_hide']."\" /> ".$lang_details['text_nfo']."</a>", "<a href=\"viewnfo.php?id=".$row[id]."\" class=\"sublink\">". $lang_details['text_view_nfo']. "</a><br /><div id='knfo' style=\"display: none;\"><pre style=\"font-size:10pt; font-family: 'Courier New', monospace;\">".$nfo."</pre></div>\n", 1);
		}

	if ($imdb_id && $showextinfo['imdb'] == 'yes' && $CURUSER['showimdb'] != 'no')
	{
		$thenumbers = $imdb_id;

		$Cache->new_page('imdb_id_'.$thenumbers.'_large', 3630 , true);
		if (!$Cache->get_page()){
			$movie = new imdb ($thenumbers);
			$movieid = $thenumbers;
			$movie->setid ($movieid);
			$movie->settypt ($row['urltype']);
			$target = array('Title', 'Credits', 'Plot');
			switch ($movie->cachestate($target))
			{
				case "0" : //cache is not ready, try to
				{
					if($row['cache_stamp']==0 || ($row['cache_stamp'] != 0 && (time()-$row['cache_stamp']) > $movie->timeout))	//not exist or timed out
						tr($lang_details['text_imdb'] . $lang_details['row_info'] , $lang_details['text_imdb'] . $lang_details['text_not_ready']."<a href=\"retriver.php?id=". $id ."&amp;type=1&amp;siteid=".$row["urltype"]."\">".$lang_details['text_here_to_retrieve'] . $lang_details['text_imdb'],1);
					else
						tr($lang_details['text_imdb'] . $lang_details['row_info'] , "<img src=\"pic/progressbar.gif\" alt=\"\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $lang_details['text_someone_has_requested'].min(max(time()-$row['cache_stamp'],1),$movie->timeout) . $lang_details['text_please_be_patient'],1);
					break;
				}
				case "1" :
					{
						reset_cachetimestamp($row['id']);
						$country = $movie->country ();
						$director = $movie->director();
						$creator = $movie->creator(); // For TV series
						$write = $movie->writing();
						$link = $movie->link();
						$produce = $movie->producer();
						$cast = $movie->cast();
						$plot = $movie->plot ();
						//$plot_outline = $movie->plotoutline();
						//$compose = $movie->composer();
						$gen = $movie->genres();
						$episodes = $movie->episodes();
						//$comment = $movie->comment();
						//$similiar_movies = $movie->similiar_movies();

						if (($photo_url = $movie->photo_localurl() ) != FALSE)
							$smallth = "<img src=\"".$photo_url. "\" width=\"102\" height=\"145\"  alt=\"poster\" 
							onmouseover=\"domTT_activate(this, event, 'content', '".htmlspecialchars("<img src=\'$photo_url\' />")."', 'trail', true, 'delay', 0,'lifetime',10000,'styleClass','smilies');\"	
 />";
						else
							$smallth = "<img src=\"pic/imdb_pic/nophoto.gif\" alt=\"no poster\" />";

						$autodata3 = "<a target=\"_blank\"  href=\"".$link.'">'.($row['urltype']==2?"http://movie.douban.com/subject/".$thenumbers:"http://www.imdb.com/title/tt".$thenumbers) ."</a><br /><strong><font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font><br />\n";
						$autodata3 .= "<font color=\"darkred\" size=\"3\">".$lang_details['text_information']."</font><br />\n";
						$autodata3 .= "<font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font></strong><br />\n";
						$autodata ="";
						$autodata2="";
						$autodata2 .= "<strong><font color=\"DarkRed\">". $lang_details['text_title']."</font></strong>" . "".$movie->title ()."<br />\n";
						$autodata2 .= "<strong><font color=\"DarkRed\">".$lang_details['text_also_known_as']."</font></strong>";

						$temp = "";
						//foreach ($movie->alsoknow() as $ak)
						//{
						//	$temp .= $ak["title"].$ak["year"]. ($ak["country"] != "" ? " (".$ak["country"].")" : "") . ($ak["comment"] != "" ? " (" . $ak["comment"] . ")" : "") . ", ";
						//}
						//$autodata .= rtrim(trim($temp), ",");
						$autodata2 .= $movie->alsoknow();
						$runtimes = str_replace(" min",$lang_details['text_mins'], $movie->runtime_all());
						//$runtimes = $movie->runtime_all().$lang_details['text_mins'];
						$autodata2 .= "<br />\n<strong><font color=\"DarkRed\">".$lang_details['text_year']."</font></strong>" . "".$movie->year ()."<br />\n";
				if($movie->runtime_all())	$autodata2 .= "<strong><font color=\"DarkRed\">".$lang_details['text_runtime']."</font></strong>".$runtimes."<br />\n";
				if($episodes)	$autodata2 .= "<strong><font color=\"DarkRed\">集数:&nbsp;</font></strong>".$episodes."<br />\n";
						$autodata2 .= "<strong><font color=\"DarkRed\">".$lang_details['text_votes']."</font></strong>" . "".$movie->votes ()."<br />\n";
						$autodata2 .= "<strong><font color=\"DarkRed\">".$lang_details['text_rating']."</font></strong>" . "".$movie->rating ()."<br />\n";
				if($movie->language ())	$autodata2 .= "<strong><font color=\"DarkRed\">".$lang_details['text_language']."</font></strong>" . "".$movie->language ()."<br />\n";
				if($country)	{$autodata2 .= "<strong><font color=\"DarkRed\">".$lang_details['text_country']."</font></strong>";

						$temp = "";
						//for ($i = 0; $i < count ($country); $i++)
						//{
						//	$temp .="$country[$i], ";
						//}
						//$autodata .= rtrim(trim($temp), ",");
						$autodata2 .= $country."<br />\n";}
						IF($gen){
						$autodata2 .= "<strong><font color=\"DarkRed\">".$lang_details['text_all_genres']."</font></strong>";
						$temp = "";
						//for ($i = 0; $i < count($gen); $i++)
						//{
						//	$temp .= "$gen[$i], ";
						//}
						//$autodata .= rtrim(trim($temp), ",");
						$autodata2 .= $gen;
						$autodata2 .= "<br />\n";
						}
						if($movie->tagline())
						$autodata .= "<br />\n<strong><font color=\"DarkRed\">".$lang_details['text_tagline']."</font></strong>" .$movie->tagline()."<br />\n";
						if ($director)
						{
							$autodata .= "<strong><font color=\"DarkRed\">".$lang_details['text_director']."</font></strong>";
							$temp = "";
							//for ($i = 0; $i < count ($director); $i++)
							//{
								//$temp .= "<a target=\"_blank\" href=\"http://www.imdb.com/Name?" . "".$director[$i]["imdb"]."" ."\">" . $director[$i]["name"] . "</a>, ";
							//}
							//$autodata .= rtrim(trim($temp), ",");
							$autodata .= $director."<br />";
							if($write)$autodata .= "<strong><font color=\"DarkRed\">".$lang_details['text_written_by']."</font></strong>".$write."<br />\n";
						}
					elseif ($creator)
							$autodata .= "<strong><font color=\"DarkRed\">".$lang_details['text_creator']."</font></strong>".$creator."<br />\n";

						
						$temp = "";
						//for ($i = 0; $i < count ($write); $i++)
						//{
						//	$temp .= "<a target=\"_blank\" href=\"http://www.imdb.com/Name?" . "".$write[$i]["imdb"]."" ."\">" . "".$write[$i]["name"]."" . "</a>, ";
						//}
						//$autodata .= rtrim(trim($temp), ",");

						$autodata .= "";
						//$temp = "";
						//for ($i = 0; $i < count ($produce); $i++)
						//{
						//	$temp .= "<a target=\"_blank\" href=\"http://www.imdb.com/Name?" . "".$produce[$i]["imdb"]."" ." \">" . "".$produce[$i]["name"]."" . "</a>, ";
						//}
					//	$autodata .= rtrim(trim($temp), ",");
						if ($produce)$autodata .= "<strong><font color=\"DarkRed\">".$lang_details['text_produced_by']."</font></strong>".$produce;
						//$autodata .= "<br />\n<strong><font color=\"DarkRed\">".$lang_details['text_music']."</font></strong>";
						//$temp = "";
						//for ($i = 0; $i < count($compose); $i++)
						//{
						//	$temp .= "<a target=\"_blank\" href=\"http://www.imdb.com/Name?" . "".$compose[$i]["imdb"]."" ." \">" . "".$compose[$i]["name"]."" . "</a>, ";
						//}
						//$autodata .= rtrim(trim($temp), ",");
						//if($compose)$autodata .= $compose;
						$autodata .= "<br /><br />\n\n<strong><font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font><br />\n";
						$autodata .= "<font color=\"darkred\" size=\"3\">".$lang_details['text_plot_outline']."</font><br />\n";
						$autodata .= "<font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font></strong>";

						if(count($plot) == 0)
						{
							$autodata .= "<br />\n".$plot_outline;
						}
						else
						{
							//for ($i = 0; $i < count ($plot); $i++)
							//{
							//	$autodata .= "<br />\n<font color=\"DarkRed\">.</font> ";
							//	$autodata .= $plot[$i];
							//}
							$autodata .= "<br />\n".$plot;
						}
						
						if($cast){
						$autodata .= "<br /><br />\n\n<strong><font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font><br />\n";
						$autodata .= "<font color=\"darkred\" size=\"3\">".$lang_details['text_cast']."</font><br />\n";
						$autodata .= "<font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font></strong><br />\n";

						$autodata .=$cast;}
						/*for ($i = 0; $i < count ($cast); $i++)
						{
							if ($i > 9)
							{
								break;
							}
							$autodata .= "<font color=\"DarkRed\">.</font> " . "<a target=\"_blank\" href=\"http://www.imdb.com/Name?" . "".$cast[$i]["imdb"]."" ."\">" . $cast[$i]["name"] . "</a> " .$lang_details['text_as']."<strong><font color=\"DarkRed\">" . "".$cast[$i]["role"]."" . " </font></strong><br />\n";
						}


						/*$autodata .= "<br /><strong><font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font><br />\n";
						$autodata .= "<font color=\"darkred\" size=\"3\">".$lang_details['text_may_also_like']."</font><br />\n";
						$autodata .= "<font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font></strong><br />\n";

						$autodata .=  "<table cellpadding=\"10\"><tr>";
						if($similiar_movies)
						{
							$counter = 0;
							foreach($similiar_movies as $similiar_movies_each)
							{
								$on_site = "";
								$imdb_config_inst = new imdb_config();
								if($imdb_id_new = parse_imdb_id($imdb_config_inst->imdbsite . $similiar_movies_each['Link']))
								{
									$similiar_res = sql_query("SELECT id FROM torrents WHERE url = " . sqlesc((int)$imdb_id_new) . " AND id != ".sqlesc($id)." ORDER BY RAND() LIMIT 1") or sqlerr(__FILE__, __LINE__);
									while($similiar_arr = mysql_fetch_array($similiar_res)) {
										$on_site = "<strong><a href=\"" .htmlspecialchars(get_protocol_prefix() . $BASEURL . "/details.php?id=" . $similiar_arr['id'] . "&hit=1")."\">" . $lang_details['text_local_link'] . "</a></strong>";
									}
								}

								$autodata .=  ($counter == 5 ? "</tr><tr>" : "" ) . "<td align=\"center\" style=\"border: 0px; padding-left: 20px; padding-right: 20px; padding-bottom: 10px\"><a href=\"" . $movie->protocol_prefix . $movie->imdbsite . $similiar_movies_each['Link'] . "\" title=\"\"><img style=\"border:0px;\" src=\"" . $similiar_movies_each['Local'] . "\" alt=\"" . $similiar_movies_each['Name'] . "\" /><br />" . $similiar_movies_each['Name'] . "</a><br />" . ($on_site != "" ? $on_site : "&nbsp;") .  "</td>";
								$counter++;
							}
						}
						$autodata .=  "</tr></table>";*/

						//$autodata .= "<br />\n\n<strong><font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font><br />\n";
						//$autodata .= "<font color=\"darkred\" size=\"3\">".$lang_details['text_recommended_comment']."</font><br />\n";
						//$autodata .= "<font color=\"navy\">------------------------------------------------------------------------------------------------------------------------------------</font></strong>";

						//$autodata .= "<br />".$comment;
						$cache_time = $movie->getcachetime();

						$Cache->add_whole_row();
						print("<tr>");
						print("<td class=\"rowhead\" style='padding: 0px;'><a href=\"javascript: klappe_ext('imdb')\"  >".  $smallth."</a></td>");
						//print("<td class=\"rowhead\"><a href=\"javascript: klappe_ext('imdb')\"><span class=\"nowrap\"><img class=\"minus\" src=\"pic/trans.gif\" alt=\"Show/Hide\" id=\"picimdb\" title=\"".$lang_detail['title_show_or_hide']."\" /> ".$lang_details['text_imdb'] . $lang_details['row_info'] ."</span><br />".  $smallth."</a></td>");
						$Cache->end_whole_row();
						$Cache->add_row();
						$Cache->add_part();
						print("<td class=\"rowfollow\"  style='padding-top: 2px;padding-bottom: 2px;' align=\"left\"><div id='kimdb2' style='display:none'>$autodata3</div>$autodata2<div id='kimdb' style=\"display:none\">".$autodata);
						$Cache->end_part();
						$Cache->add_part();
						print($lang_details['text_information_updated_at'] . date("Y-m-d", $cache_time) . $lang_details['text_might_be_outdated']);
						$Cache->end_part();
						$Cache->end_row();
						$Cache->add_whole_row();
						print("</div></td></tr>");
						$Cache->end_whole_row();
						$Cache->cache_page();
						echo $Cache->next_row();
						$Cache->next_row();
						echo $Cache->next_part();
						if (get_user_class() >= $updateextinfo_class)
							echo $Cache->next_part() ."<a href=\"".htmlspecialchars("retriver.php?id=". $id ."&type=2&siteid=".$row["urltype"]."")."\">".$lang_details['text_here_to_update'];
						echo $Cache->next_row();
						break;
					}
				case "2" :
					{
						tr($lang_details['text_imdb'] . $lang_details['row_info'] ,$lang_details['text_network_error'],1);
						break;
					}
				case "3" :// not a valid imdb url
				{
					break;
				}
			}
		}
		else{
				echo $Cache->next_row();
				$Cache->next_row();
				echo $Cache->next_part();
				if (get_user_class() >= $updateextinfo_class){
					echo $Cache->next_part()."<a href=\"".htmlspecialchars("retriver.php?id=". $id ."&type=2&siteid=".$row["urltype"]."")."\">".$lang_details['text_here_to_update'];
				}
				echo $Cache->next_row();
		}
	}

		if ($imdb_id)
		{
			$where_area = " url = " . sqlesc((int)$imdb_id) ." AND visible = 'yes' and torrents.id != ".sqlesc($id);
			//$copies_res = sql_query("SELECT torrents.id, torrents.small_descr , torrents.name, torrents.sp_state, torrents.size, torrents.added, torrents.seeders, torrents.leechers, categories.id AS catid, categories.name AS catname, categories.image AS catimage, sources.name AS source_name, media.name AS medium_name, codecs.name AS codec_name, standards.name AS standard_name, processings.name AS processing_name FROM torrents LEFT JOIN categories ON torrents.category=categories.id LEFT JOIN sources ON torrents.source = sources.id LEFT JOIN media ON torrents.medium = media.id  LEFT JOIN codecs ON torrents.codec = codecs.id LEFT JOIN standards ON torrents.standard = standards.id LEFT JOIN processings ON torrents.processing = processings.id WHERE " . $where_area . " ORDER BY torrents.id DESC") or sqlerr(__FILE__, __LINE__);
			//SELECT torrents.id, small_descr , name, sp_state, size, added, seeders, leechers,times_completed,category AS catid,source AS source_name,medium AS medium_name,codec AS codec_name,team AS team_name,standard AS standard_name,processing AS processing_name,audiocodec AS audiocodec_name , finished  FROM torrents  left JOIN  snatched ON torrentid = torrents.id and userid={$CURUSER['id']} WHERE " . $where_area . " ORDER BY torrents.id DESC limit 20 
			$copies_res = sql_query("SELECT DISTINCT (torrents.id), small_descr, torrents.name, sp_state, size, added, seeders, leechers, times_completed,times_uncompleted, category AS catid, source AS source_name, medium AS medium_name, codec AS codec_name, team AS team_name, standard AS standard_name, processing AS processing_name, audiocodec AS audiocodec_name, finished, peers.seeder FROM torrents LEFT JOIN snatched ON torrentid = torrents.id AND userid={$CURUSER['id']} LEFT JOIN peers ON peers.userid={$CURUSER['id']} AND torrent = torrents.id WHERE " . $where_area . "  GROUP BY torrents.id ORDER BY torrents.id DESC LIMIT 20
			
			") or sqlerr(__FILE__, __LINE__);

			$copies_count = mysql_num_rows($copies_res);
			if($copies_count > 0)
			{
				$s = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n";
				$s.="<tr><td class=\"colhead\" style=\"padding: 0px; text-align:center;\">".$lang_details['col_type']."</td><td class=\"colhead\" align=\"left\">".$lang_details['col_name']."</td><td class=\"colhead\" align=\"center\">".$lang_details['col_quality']."</td><td class=\"colhead\" align=\"center\"><img class=\"size\" src=\"pic/trans.gif\" alt=\"size\" title=\"".$lang_details['title_size']."\" /></td><td class=\"colhead\" align=\"center\"><img class=\"time\" src=\"pic/trans.gif\" alt=\"time added\" title=\"".$lang_details['title_time_added']."\" /></td><td class=\"colhead\" align=\"center\"><img class=\"seeders\" src=\"pic/trans.gif\" alt=\"seeders\" title=\"".$lang_details['title_seeders']."\" /></td><td class=\"colhead\" align=\"center\"><img class=\"leechers\" src=\"pic/trans.gif\" alt=\"leechers\" title=\"".$lang_details['title_leechers']."\" /></td><td class=\"colhead\" align=\"center\"><img class=\"snatched\" src=\"pic/trans.gif\" alt=\"snatched\" title=\"完成数\"/></td><td class=\"colhead\" align=\"center\"><img class=\"unsnatched\" src=\"pic/trans.gif\" alt=\"unsnatched\" title=\"未完成数\"/></td><td class=\"colhead\" align=\"center\"><img class=\"bookmark\" src=\"pic/trans.gif\" alt=\"bookmark\" title=\"收藏\"/></td></tr>\n";
				$get_second_name=get_second_name();
				
				while ($copy_row = mysql_fetch_assoc($copies_res))
				{
					//$dispname = htmlspecialchars(trim($copy_row["name"].$copy_row["small_descr"]));
					$dispname = htmlspecialchars(trim($copy_row["name"])).'<br />'.htmlspecialchars(trim($copy_row["small_descr"]));
					$count_dispname=strlen($dispname);
					$max_lenght_of_torrent_name="80"; // maximum lenght
					if($count_dispname > $max_lenght_of_torrent_name)
					{;
						//$dispname=substr($dispname, 0, $max_lenght_of_torrent_name) . "..";
					}
					
									
						$other_source_info ="";
					
						$other_medium_info ="";
					
						$other_codec_info ="";
					
						$other_standard_info = "";
					
						$other_processing_info = "";
						
						

					//if (isset($copy_row["source_name"]))
						$other_source_info = $get_second_name['source'][$copy_row[source_name]];
					//if (isset($copy_row["medium_name"]))
						$other_medium_info = $get_second_name['medium'][$copy_row[medium_name]];
					//if (isset($copy_row["codec_name"]))
						$other_codec_info = $get_second_name['codec'][$copy_row[codec_name]];
					//if (isset($copy_row["standard_name"]))
						$other_standard_info = $get_second_name['standard'][$copy_row[standard_name]];
					//if (isset($copy_row["processing_name"]))
						$other_processing_info = $get_second_name['processing'][$copy_row[processing_name]];
						
						$other_team_info = $get_second_name['team'][$copy_row[team_name]];
						$other_audiocodec_info = "<b>".$get_second_name['audiocodec'][$copy_row[audiocodec_name]]."</b>";
						

					$sphighlight = get_torrent_bg_color($copy_row['sp_state'],$copy_row['audiocodec_name']);
					$sp_info = get_torrent_promotion_append($copy_row['sp_state'],$copy_row['audiocodec_name']);

					$s .= "<tr". $sphighlight."><td class=\"rowfollow nowrap\" valign=\"middle\" style='padding: 0px'>".return_category_image($copy_row["catid"], "download.php?id={$copy_row["id"]}&amp;")."</td><td class=\"rowfollow\" align=\"left\"><a href=\"" . htmlspecialchars(get_protocol_prefix() . $BASEURL . "/details.php?id=" . $copy_row["id"]. "&hit=1")."\">" . $other_audiocodec_info.$dispname ."</a>". $sp_info."</td>" .
					"<td class=\"rowfollow nowrap\" align=\"left\">" . rtrim(trim($other_source_info .$other_processing_info. $other_medium_info .$other_codec_info ."<br> ".$other_team_info. $other_standard_info ), ","). "</td>" .
					"<td class=\"rowfollow\" align=\"center\">" . mksize($copy_row["size"]) . "</td>" .
					"<td class=\"rowfollow nowrap\" align=\"center\">" . str_replace("&nbsp;", "<br />", gettime($copy_row["added"],false)). "</td>" .
					"<td class=\"rowfollow\" align=\"center\" {$seederbgcolour[$copy_row["seeder"]]}>" . $copy_row["seeders"] . "</td>" .
					"<td class=\"rowfollow\" align=\"center\" {$copy_row["seeder"]} {$leecherbgcolour[$copy_row["seeder"]]}>" . $copy_row["leechers"] . "</td>" .
					"<td class=\"rowfollow\" align=\"center\" {$snatchedbgcolour[$copy_row["finished"]]} >" . $copy_row["times_completed"] . "</td>" .
					"<td class=\"rowfollow\" align=\"center\" {$unsnatchedbgcolour[$copy_row["finished"]]} >" . $copy_row["times_uncompleted"] . "</td>" .
					"<td class=\"rowfollow\" align=\"center\"><a id=\"bookmark{$copy_row["id"]}\"  href=\"javascript: bookmark({$copy_row["id"]},{$copy_row["id"]});\" >".get_torrent_bookmark_state($CURUSER['id'], $copy_row["id"])."</a></td>" .
					
					"</tr>\n";
				}
				$s .= "</table>\n";
				if($copies_count)tr("<a href=\"javascript: klappe_news('othercopy')\"><span class=\"nowrap\"><img class=\"plus\" src=\"pic/trans.gif\" alt=\"Show/Hide\" id=\"picothercopy\" title=\"".$lang_detail['title_show_or_hide']."\" /> ".$lang_details['row_other_copies']."</span></a>", "<b><a href=\"/torrents.php?search={$imdb_id}&search_area=5\"><span class=\"nowrap\">".$copies_count.$lang_details['text_other_copies']." </span></a></b><br /><div id='kothercopy' style=\"display: none\">".$s."</div>",1);
			}
		}

		//if ($row["type"] == "multi")
		{
			$files_info = "<b>".$lang_details['text_num_files']."</b>". $row["numfiles"] . $lang_details['text_files']."<br />" ;
		}
		
		$row_torrent_info = "<span id=\"showfl\"><a href=\"javascript: viewfilelist(".$id.")\" ><img class=\"plus\" src=\"pic/trans.gif\" alt=\"Show/Hide\" id=\"showflpicothercopy\" title=\"".$lang_detail['title_show_or_hide']."\" /> ".$lang_details['row_torrent_info']."</a>\n</span><span id=\"hidefl\" style=\"display: none;\"><a href=\"javascript: hidefilelist()\"><img class=\"minus\" src=\"pic/trans.gif\" alt=\"Show/Hide\" id=\"showflpicothercopy\" title=\"".$lang_detail['title_show_or_hide']."\" /> ".$lang_details['row_torrent_info']."</a>\n</span>";

		
		
		function hex_esc($matches) {
			return sprintf("%02x", ord($matches[0]));
		}
		//if ($enablenfo_main=='yes')
			//tr($lang_details['row_torrent_info'], "<table><tr>" . ($files_info != "" ? "<td class=\"no_border_wide\">" . $files_info . "</td>" : ""). "<td class=\"no_border_wide\"><b></td></tr></table><span id='filelist'></span>",1);
			//tr($lang_details['row_torrent_info'], "<table><tr>" . ($files_info != "" ? "<td class=\"no_border_wide\">" . $files_info . "</td>" : ""). "<td class=\"no_border_wide\"><b>".$lang_details['row_info_hash']."</b>:&nbsp;".preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"]))."<b></td></tr></table><span id='filelist'></span>",1);
			tr($row_torrent_info, "<table><tr>" . ($files_info != "" ? "<td class=\"no_border_wide\">" . $files_info . "</td>" : ""). "<td class=\"no_border_wide\"><b>".$lang_details['row_info_hash']."</b>:&nbsp;".preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"]))."</td>". (get_user_class() >= $torrentstructure_class ? "<td class=\"no_border_wide\"><b>" . $lang_details['text_torrent_structure'] . "</b><a href=\"torrent_info.php?id=".$id."\">".$lang_details['text_torrent_info_note']."</a></td>" : "") . "</tr></table><div style=\"display: none;\" id='filelist' ></div>",1);
	//	if ($enablenfo_main=='yes')
			//tr($lang_details['row_torrent_info'], "<table><tr>" . ($files_info != "" ? "<td class=\"no_border_wide\">" . $files_info . "</td>" : "") . "<td class=\"no_border_wide\"><b>".$lang_details['row_info_hash'].":</b>&nbsp;".preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"]))."</td>". (get_user_class() >= $torrentstructure_class ? "<td class=\"no_border_wide\"><b>" . $lang_details['text_torrent_structure'] . "</b><a href=\"torrent_info.php?id=".$id."\">".$lang_details['text_torrent_info_note']."</a></td>" : "") . "</tr></table><span id='filelist'></span>",1);

			// ---------------- start subtitle block -------------------//
			
		$r = sql_query("SELECT subs.*, language.flagpic, language.lang_name FROM subs LEFT JOIN language ON subs.lang_id=language.id WHERE torrent_id = " . sqlesc($row["id"]). " ORDER BY subs.lang_id ASC") or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($r) > 0)
		{		print("<tr><td class=\"rowhead\" valign=\"top\">".$lang_details['row_subtitles']."</td>");
				print("<td class=\"rowfollow\" align=\"left\" valign=\"top\">");
				print("<table border=\"0\" cellspacing=\"0\">");
			while($a = mysql_fetch_assoc($r))
			{
				$lang = "<tr><td class=\"embedded\"><img border=\"0\" src=\"pic/flag/". $a["flagpic"] . "\" alt=\"" . $a["lang_name"] . "\" title=\"" . $a["lang_name"] . "\" style=\"padding-bottom: 4px\" /></td>";
				$lang .= "<td class=\"embedded\">&nbsp;&nbsp;<a href=\"downloadsubs.php?torrentid=".$a[torrent_id]."&subid=".$a[id]."\" title=\"下载次数:".$a["hits"]."\"><u>". $a["title"].'.'.$a["ext"]."(".mksize($a["size"]).")</u></a>".(get_user_class() >= $submanage_class || (get_user_class() >= $delownsub_class && $a["uppedby"] == $CURUSER["id"]) ? " <font class=\"small\"><a href=\"subtitles.php?delete=".$a[id]."\">[".$lang_details['text_delete']."</a>]</font>" : "")."</td><td class=\"embedded\">&nbsp;&nbsp;".($a["anonymous"] == 'yes' ? $lang_details['text_anonymous'] . (get_user_class() >= $viewanonymous_class ? get_username($a['uppedby'],false,true,true,false,true) : "") : get_username($a['uppedby']))."</td></tr>";
				print($lang);
			}
				print("</table>");
				print("<table border=\"0\" cellspacing=\"0\"><tr>");
				

		
		
				print("</tr></table>");
				print("</td></tr>\n");
		}					
		$moviename = "";
		$imdb_id = parse_imdb_id($row["url"]);
		if ($imdb_id && $showextinfo['imdb'] == 'yes')
		{
			$thenumbers = $imdb_id;
			if (!$moviename = $Cache->get_value('imdb_id_'.$thenumbers.'_movie_name')){
				$movie = new imdb ($thenumbers);
				$target = array('Title');
				switch ($movie->cachestate($target)){
					case "1":{
						$moviename = $movie->title (); break;
						$Cache->cache_value('imdb_id_'.$thenumbers.'_movie_name', $moviename, 1296000);
					}
					default: break;
				}
		}}


		if($CURUSER['id']==$row['owner']  ||  get_user_class() >= $uploadsub_class)
		{
			$upload_subtitles_table=("<span style=\"display: none\"><form method=\"post\" action=\"subtitles.php\" name=\"subtitles\"><input type=\"hidden\" name=\"torrent_name\" value=\"" . $row["name"]. "\" /><input type=\"hidden\" name=\"detail_torrent_id\" value=\"" . $row["id"]. "\" /><input type=\"hidden\" name=\"in_detail\" value=\"in_detail\" /></form></span>");
			$upload_subtitles="<a title=\"".$lang_details['submit_upload_subtitles']."\"  href=\"javascript:document.subtitles.submit()\" ><img class=\"sticky\" src=\"pic/trans.gif\" alt=\"upload\" />&nbsp;<b><font class=\"small\">".$lang_details['submit_upload_subtitles']."</font></b></a>&nbsp;|&nbsp;";

			}
		





		// ---------------- end subtitle block -------------------//

		//$bwres = sql_query("SELECT uploadspeed.name AS upname, downloadspeed.name AS downname, isp.name AS ispname FROM users LEFT JOIN uploadspeed ON users.upload = uploadspeed.id LEFT JOIN downloadspeed ON users.download = downloadspeed.id LEFT JOIN isp ON users.isp = isp.id WHERE users.id=".$row['owner']);
		//$bwrow = mysql_fetch_array($bwres);
		//if ($bwrow['upname'] && $bwrow['downname'])
			//tr($lang_details['row_uploader_bandwidth'], "<img class=\"speed_down\" src=\"pic/trans.gif\" alt=\"Downstream Rate\" /> ".$bwrow['downname']."&nbsp;&nbsp;&nbsp;&nbsp;<img class=\"speed_up\" src=\"pic/trans.gif\" alt=\"Upstream Rate\" /> ".$bwrow['upname']."&nbsp;&nbsp;&nbsp;&nbsp;".$bwrow['ispname'],1);

		
		// Health
	$Cache->new_page('torrent_details_traffic_'.$row['id'], 300, true);
	if (!$Cache->get_page()){
		$Cache->add_whole_row();	
		$seedersTmp = $row['seeders'];
		$leechersTmp = $row['leechers'];
		$seedersTmp = 0;
		$leechersTmp = 0;
		$subres = mysql_fetch_array(sql_query("SELECT sum(uploaded) as progressTotaluploaded,sum(downloaded) as progressTotaldownloaded FROM snatched WHERE torrentid = $row[id]"));
		$progressTotaluploaded=$subres[progressTotaluploaded];
		$progressTotaldownloaded=$subres[progressTotaldownloaded];
		if ( $row['leechers'] >= 1)	// it is possible that there's traffic while have no seeders
		{
			$progressPerTorrent = 0;
			$i = 0;
			$subres = sql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, ip, port, uploaded, downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, peer_id, UNIX_TIMESTAMP(last_action) AS la, userid FROM peers WHERE torrent = $row[id] AND connectable = 'yes' AND userid > 0 ") or sqlerr();

			while ($subrow = mysql_fetch_array($subres)) {
				$progressPerTorrent += sprintf("%.2f", 100 * (1 - ($subrow["to_go"] / $row["size"])));
				$i++;
				$totaluploaded +=$subrow["uploaded"];
				$totaldownloaded +=$subrow["downloaded"];
				if ($subrow["seeder"] == "yes"){
				$seedersTmp ++;
				$seeders[] = $subrow;}
				else{
				$leechersTmp ++;
				$downloaders[] = $subrow;}
			}
			if ($i == 0)
				$i = 1;
			$progressTotal = sprintf("%.2f", $progressPerTorrent / $i);

			$totalspeed = 0;

			if($seedersTmp >=1)
			{
				if ($seeders) {
					foreach($seeders as $e) {
						$totalspeed = $totalspeed + max(1,($e["uploaded"] - $e["uploadoffset"])) / max(1, ($e["la"] - $e["st"]));
						//$totalspeed = $totalspeed + ($e["downloaded"] - $e["downloadoffset"]) / max(1, $e["finishedat"] - $e[st]);
					}
				}
			}

			

			
			if ($downloaders) {
				foreach($downloaders as $e) {
					$totalspeed = $totalspeed + max(1,($e["uploaded"] - $e["uploadoffset"])) / max(1, ($e["la"] - $e["st"]));
					//$totalspeed = $totalspeed + ($e["downloaded"] - $e["downloadoffset"]) / max(1, ($e["la"] - $e["st"]));
				}
			}

			//$avgspeed =  mksize($totalspeed/($seedersTmp+$leechersTmp)) . "/s";
			$avgspeed =  mksize($totalspeed/(max(1,$leechersTmp))) . "/s";
			$total_speed = "<b>".$lang_details['text_total_speed']."</b>" . mksize($totalspeed) . "/s ";
			$average_speed= "<b>".$lang_details['text_average_speed']."</b>" . $avgspeed ;
			$avprogress="<b>". $lang_details['text_avprogress'] ."</b>". get_percent_completed_image(floor($progressTotal))." (".round($progressTotal)."%)";
			tr($lang_details['row_hot_meter'],"<table style=\"width: 100%;\"><tr><td class=\"no_border_wide\"><b>" . $lang_details['text_views']."</b>". $row["views"] . "</td><td class=\"no_border_wide\"><b>" . $lang_details['text_hits']. "</b>" . $row["hits"] . "</td><td class=\"no_border_wide\"><b>" .$lang_details['text_snatched'] . "</b><a href=\"viewsnatches.php?id=".$id."\"><b>" . $row["times_completed"]. $lang_details['text_view_snatches'] . "</td><td class=\"no_border_wide\"><b>" . $lang_details['row_last_seeder']. "</b>" . gettime($row["last_action"]). "</td><td class=\"no_border_wide\" ><b>上传量：</b>".mksize($totaluploaded)."</td >
			<td class=\"no_border_wide\" ><b>总上传量：</b>".mksize($progressTotaluploaded)."</td ></tr>
			<tr>
			<td class=\"no_border_wide\" colspan=\"2\">".$avprogress."</td >
			<td class=\"no_border_wide\">".$average_speed."</td >
			<td class=\"no_border_wide\">".$total_speed."</td ><td class=\"no_border_wide\"><b>下载量：</b>".mksize($totaldownloaded)."</td >
			<td class=\"no_border_wide\"><b>总下载量：</b>".mksize($progressTotaldownloaded)."</td >
			</tr>

			
			
			</table>",1);
		}
		else{
			$health = "<td class=\"no_border_wide\"><b>".$lang_details['text_traffic']. "</b>&nbsp;" . $lang_details['text_no_traffic']."</td>";
			if ($row["visible"] == "no")$health .= "<td class=\"no_border_wide\"><b>".$lang_details['text_status']."</b>&nbsp;" . $lang_details['text_dead'] ."</td>";
			else $health .="<td class=\"no_border_wide\" ><b>总上传量：</b>".mksize($progressTotaluploaded)."</td ><td class=\"no_border_wide\"><b>总下载量：</b>".mksize($progressTotaldownloaded)."</td >";
			
			tr($lang_details['row_hot_meter'],"<table><tr><td class=\"no_border_wide\"><b>" . $lang_details['text_views']."</b>". $row["views"] . "</td><td class=\"no_border_wide\"><b>" . $lang_details['text_hits']. "</b>" . $row["hits"] . "</td><td class=\"no_border_wide\"><b>" .$lang_details['text_snatched'] . "</b><a href=\"viewsnatches.php?id=".$id."\"><b>" . $row["times_completed"]. $lang_details['text_view_snatches'] . "</td><td class=\"no_border_wide\"><b>" . $lang_details['row_last_seeder']. "</b>" . gettime($row["last_action"]). "</td>".$health."</tr></table>",1);
		}
	$Cache->end_whole_row();
	$Cache->cache_page();
	}
	echo $Cache->next_row();
		
		
		
		$subrow = mysql_fetch_array(sql_query("SELECT seeder FROM peers WHERE torrent = $row[id] AND connectable = 'yes' AND userid ={$CURUSER['id']}"));
		if($subrow["seeder"] == 'yes')$nowuserisseedfont="<font color='green'>[做种中]</font>";
			elseif($subrow["seeder"] == 'no')$nowuserisleechfont="<font color='red'>[下载中]</font>";
		
		
		
		tr("<span id=\"showpeer\"><a href=\"javascript: viewpeerlist(".$row['id'].");\"><img class=\"plus\" src=\"pic/trans.gif\" alt=\"Show/Hide\" id=\"viewpeerlistpicothercopy\" title=\"".$lang_detail['title_show_or_hide']."\" /> ".$lang_details['row_peers']."</a>\n</span><span id=\"hidepeer\" style=\"display: none;\"><a href=\"javascript: hidepeerlist();\"><img class=\"minus\" src=\"pic/trans.gif\" alt=\"Show/Hide\" id=\"viewpeerlistpicothercopy\" title=\"".$lang_detail['title_show_or_hide']."\" /> ".$lang_details['row_peers']."<br /></a></span>", "<div id=\"peercount\"><a href=\"javascript: viewpeerlist(".$row['id'].");\"><b>".$row['seeders'].$lang_details['text_seeders'].add_s($row['seeders']).$nowuserisseedfont."</b> | <b>".$row['leechers'].$lang_details['text_leechers'].add_s($row['leechers']).$nowuserisleechfont."</b></a> | <a href=\"javascript: viewsnatchslist(".$row['id'].");\"><b>".$row['times_completed']."个完成者".add_s($row['times_completed']).$snatchedfont[$row['finished']]."</b></a></div>\n<div id=\"peerlist\" style=\"display: none;\">\n</div>" , 1);
		if ($_GET['dllist'] == 1)
		{
			$scronload = "viewpeerlist(".$row['id'].")";

echo "<script type=\"text/javascript\">\n";
echo $scronload;
echo "</script>";
		}

		// ------------- start thanked-by block--------------//
		
		

			
			

		$torrentid = $id;
		
		
				$totalrated= 0;
		$subres = sql_query("SELECT rated FROM thanks WHERE torrentid=".sqlesc($torrentid)." ORDER BY id") or sqlerr();;

			while ($subrow = mysql_fetch_array($subres)) {
				$totalrated += $subrow['rated'];
			}
			
			
			
		$thanksby = "";
		$nothanks = "";
		$thanks_said = 0;
		$thanks_sql = sql_query("SELECT userid , rated FROM thanks WHERE torrentid=".sqlesc($torrentid)." ORDER BY id DESC LIMIT 20");
		$thanksCount = get_row_count("thanks", "WHERE torrentid=".sqlesc($torrentid));
		$thanks_all = mysql_num_rows($thanks_sql);
		if ($thanks_all) {
			while($rows_t = mysql_fetch_array($thanks_sql)) {
				$thanks_userid = $rows_t["userid"];
				$thanks_rated = $rows_t["rated"];
				if ($rows_t["userid"] == $CURUSER['id']) {
					$thanks_said = 1;
					$thanks_said_rated=($thanks_rated ? $thanks_rated: "");
				} else {
					$thanksby .= get_username($thanks_userid).($thanks_rated ? "(+".$thanks_rated.")" : "") ." ";
				}
			}
		}
		else $nothanks = $lang_details['text_no_thanks_added'];

		if (!$thanks_said) {
		
			$res = sql_query("SELECT * FROM thanks WHERE torrentid= $torrentid  AND userid=".sqlesc($CURUSER['id']));
			$rows_t = mysql_fetch_array($res);
			if ($rows_t['rated'])
			{
					$thanks_said_rated =$rows_t['rated'];
					$thanksby = get_username($CURUSER['id']).($thanks_said_rated ? "(+".$thanks_said_rated.")" : "") ." ".$thanksby;
					$thanks_said=1;
								}
		
			//$thanks_said = get_row_count("thanks", "WHERE torrentid=$torrentid AND userid=".sqlesc($CURUSER['id']));
		}
		
		else {
			
			$thanksby = get_username($CURUSER['id']).($thanks_said_rated ? "(+".$thanks_said_rated.")" : "") ." ".$thanksby;
			}
		
$thanks_said_rated =($thanks_said_rated?$lang_details['text_given3_points'].$thanks_said_rated.$lang_details['text_karma2_points']:$lang_details['submit_you_said_thanks']);

$torrenthash="'".bin2hex($row["info_hash"])."'";
if($CURUSER['id']==$row['owner'])$thanksbutton="";
		else if ($thanks_said == 0) {
$thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"saythanks\"  onclick=\"saythanks_as(".$torrenthash.",0);\" value=\"".$lang_details['submit_say_thanks']."\" />";
if($CURUSER['seedbonus']>=10000&&$CURUSER["class"]>UC_USER){
$thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"ratedthanks\"  onclick=\"saythanks_as(".$torrenthash.",50);\" value=\"".$lang_details['text_given_points']."50".$lang_details['text_karma_points']."\" />";
$thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"ratedthanks\"  onclick=\"saythanks_as(".$torrenthash.",100);\" value=\"".$lang_details['text_given_points']."100".$lang_details['text_karma_points']."\" />";
$thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"ratedthanks\"  onclick=\"saythanks_as(".$torrenthash.",200);\" value=\"".$lang_details['text_given_points']."200".$lang_details['text_karma_points']."\" />";
$thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"ratedthanks\"  onclick=\"saythanks_as(".$torrenthash.",500);\" value=\"".$lang_details['text_given_points']."500".$lang_details['text_karma_points']."\" />";
///权限修改thanks.php
if($CURUSER["class"]>=$userbar_class) $thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"ratedthanks\"  onclick=\"saythanks_as(".$torrenthash.",1000);\" value=\"".$lang_details['text_given2_points']."1000".$lang_details['text_karma_points']."\" />";
}
$thanksbutton .="<br />";
		} else {
			$buttonvalue = " value=\"".$thanks_said_rated."\" disabled=\"disabled\"";
			$thanksby = $thanksby;
			$thanksbutton = "<input class=\"btnbak\" type=\"button\" id=\"saythanks\"  onclick=\"saythanks_as(".$torrenthash.",0);\" ".$buttonvalue." />";
			if($CURUSER['seedbonus']>=10000&&$CURUSER["class"]>UC_USER){
$thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"ratedthanks\"  onclick=\"saythanks_as(".$torrenthash.",50);\" value=\"".$lang_details['text_given_points']."50".$lang_details['text_karma_points']."\" />";
$thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"ratedthanks\"  onclick=\"saythanks_as(".$torrenthash.",100);\" value=\"".$lang_details['text_given_points']."100".$lang_details['text_karma_points']."\" />";
$thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"ratedthanks\"  onclick=\"saythanks_as(".$torrenthash.",200);\" value=\"".$lang_details['text_given_points']."200".$lang_details['text_karma_points']."\" />";
$thanksbutton .= "<input class=\"btnbak\" type=\"button\" id=\"ratedthanks\"  onclick=\"saythanks_as(".$torrenthash.",500);\" value=\"".$lang_details['text_given_points']."500".$lang_details['text_karma_points']."\" />";
///权限修改thanks.php
$thanksbutton .="<br />";
}
		}
$buy_torrent="<a title=\"".$lang_details['title_buy_torrent']."\" href=\"bysppos.php?id=$id\"><img class=\"buddylist\" src=\"pic/trans.gif\" alt=\"buy\" />&nbsp;<b><font class=\"small\">".$lang_details['text_buy_torrent']."</font></b></a>&nbsp;|&nbsp;";

		tr($lang_details['row_thanks_by']."<br /><span style=\"font-weight: lighter;\"> ".$lang_details['text_total_points'] .$totalrated.$lang_details['text_karma_points'] ."</span>","<span id=\"thanksadded\" style=\"display: none;\"><input class=\"btnbak\" type=\"button\" value=\"".$lang_details['text_thanks_added']."\" disabled=\"disabled\" /></span><span id=\"curuser\" style=\"display: none;\">".get_username($CURUSER['id'])." </span><span id=\"thanksbutton\">".$thanksbutton."</span>&nbsp;&nbsp;<span id=\"nothanks\">".$nothanks."</span><span id=\"addcuruser\"></span>".$thanksby.($thanks_all < $thanksCount ? $lang_details['text_and_more'].$thanksCount.$lang_details['text_users_in_total'] : ""),1);
		tr($lang_details['row_action'], $download.
		
		"<a id=\"bookmark0\" href=\"javascript: bookmarktext(" .$row['id'].",0);\"><SPAN id=\"unbookmarked0\" ".get_torrent_bookmark_state_in_details($CURUSER['id'], $row['id'], 'unbookmarked')."><img class=\"delbookmark\" src=\"pic/trans.gif\" alt=\"Unbookmarked\" title=\"".$lang_functions['title_bookmark_torrent']."\" /><b><font class=\"small\">&nbsp;".$lang_functions['title_bookmark_torrent'] ."</font></b></SPAN><SPAN id=\"bookmarked0\"  ".get_torrent_bookmark_state_in_details($CURUSER['id'], $row['id'], 'bookmarked')."><img class=\"bookmark\" src=\"pic/trans.gif\" alt=\"Bookmarked\" title=\"".$lang_functions['title_delbookmark_torrent']."\" /><b><font class=\"small\">&nbsp;".$lang_functions['title_delbookmark_torrent']."</font></b></SPAN></a>&nbsp;|&nbsp;"
		
		//"<a id=\"bookmark0\" href=\"javascript: bookmarktext(" . $row['id'].",0);\">".get_torrent_bookmark_state($CURUSER['id'], $row['id'], ture) ."</a>&nbsp;|&nbsp;"
.$upload_subtitles.$buy_torrent.($owned == 1 ? "<$editlink><img class=\"dt_edit\" src=\"pic/trans.gif\" alt=\"edit\" />&nbsp;<b><font class=\"small\">".$lang_details['text_edit_torrent'] . "</font></b></a>&nbsp;|&nbsp;" : "").  (get_user_class() >= $askreseed_class && $row[seeders] == 0 ? "<a title=\"".$lang_details['title_ask_for_reseed']."\" href=\"takereseed.php?reseedid=$id\"><img class=\"dt_reseed\" src=\"pic/trans.gif\" alt=\"reseed\">&nbsp;<b><font class=\"small\">".$lang_details['text_ask_for_reseed'] ."</font></b></a>&nbsp;|&nbsp;" : "") . "<a title=\"".$lang_details['title_report_torrent']."\" href=\"report.php?torrent=$id\"><img class=\"dt_report\" src=\"pic/trans.gif\" alt=\"report\" />&nbsp;<b><font class=\"small\">".$lang_details['text_report_torrent']."</font></b></a>".$upload_subtitles_table, 1);

		// ------------- end thanked-by block--------------//
		print("</table>\n");
	}
	else {
		stdhead($lang_details['head_comments_for_torrent']."\"" . $row["name"] . "\"");
		print("<h1 id=\"top\">".$lang_details['text_comments_for']."<a href=\"details.php?id=".$id."\">" . htmlspecialchars($row["name"]) . "</a></h1>\n");
	}

	// -----------------COMMENT SECTION ---------------------//
if ($CURUSER['showcomment'] != 'no'){
	$count = get_row_count("comments","WHERE torrent=".sqlesc($id));
	if ($count)
	{
		print("<br /><br />");
		print("<h1 align=\"center\" id=\"startcomments\" width=100%>" .$lang_details['h1_user_comments'] . "</h1>\n");
		list($pagertop, $pagerbottom, $limit) = pager(10, $count, "details.php?id=$id&cmtpage=1&", array(lastpagedefault => 1), "page");

		$subres = sql_query("SELECT id, text, user, added, editedby, anonymous, torrent , editdate FROM comments WHERE torrent = $id ORDER BY id $limit") or sqlerr(__FILE__, __LINE__);
		$allrows = array();
		while ($subrow = mysql_fetch_array($subres)) {
			$allrows[] = $subrow;
		}
		print($pagertop);
		commenttable($allrows,"torrent",$id);
		print($pagerbottom);
	}
}
print("<br /><br />");
print ("<table style='border:1px solid #000000;'><tr><td class=\"text\" align=\"center\"><b>".$lang_details['text_quick_comment']."</b><br /><br /><form id=\"compose\" name=\"comment\" method=\"post\" action=\"".htmlspecialchars("comment.php?action=add&type=torrent&pid=".$id."")."\" onsubmit=\"return postvalid(this);\"><input type=\"hidden\" name=\"pid\" value=\"".$id."\" /><br />");
quickreply('comment', 'body', $lang_details['submit_add_comment']);
print("</form></td></tr></table>");
print("<p align=\"center\"><a class=\"index\" href=\"".htmlspecialchars("comment.php?action=add&pid=".$id."&type=torrent")."\">".$lang_details['text_add_a_comment']."</a></p>\n");
}
stdfoot();
