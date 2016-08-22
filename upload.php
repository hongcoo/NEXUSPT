<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
parked();

if ($CURUSER["uploadpos"] == 'no')
	stderr($lang_upload['std_sorry'], $lang_upload['std_unauthorized_to_upload'],false);

if ($enableoffer == 'yes')
	$has_allowed_offer = get_row_count("offers","WHERE allowed='allowed' AND userid = ". sqlesc($CURUSER["id"]));
else $has_allowed_offer = 0;
$uploadfreely = user_can_upload("torrents");
$allowtorrents = ($has_allowed_offer || $uploadfreely);
$allowspecial = user_can_upload("music");

if (!$allowtorrents && !$allowspecial)
	stderr($lang_upload['std_sorry'],$lang_upload['std_please_offer'],false);
$allowtwosec = ($allowtorrents && $allowspecial);

$brsectiontype = $browsecatmode;
$spsectiontype = $specialcatmode;
$spsectiontype2 = (user_can_upload("music2")? $specialcatmode2:0);
$showsource = (($allowtorrents && get_searchbox_value($brsectiontype, 'showsource')) || ($allowspecial && (get_searchbox_value($spsectiontype, 'showsource')||get_searchbox_value($spsectiontype2, 'showsource')))); //whether show sources or not
$showmedium = (($allowtorrents && get_searchbox_value($brsectiontype, 'showmedium')) || ($allowspecial && (get_searchbox_value($spsectiontype, 'showmedium')||get_searchbox_value($spsectiontype2, 'showmedium')))); //whether show media or not
$showcodec = (($allowtorrents && get_searchbox_value($brsectiontype, 'showcodec')) || ($allowspecial && (get_searchbox_value($spsectiontype, 'showcodec')||get_searchbox_value($spsectiontype2, 'showcodec')))); //whether show codecs or not
$showstandard = (($allowtorrents && get_searchbox_value($brsectiontype, 'showstandard')) || ($allowspecial && (get_searchbox_value($spsectiontype, 'showstandard')||get_searchbox_value($spsectiontype2, 'showstandard')))); //whether show standards or not
$showprocessing = (($allowtorrents && get_searchbox_value($brsectiontype, 'showprocessing')) || ($allowspecial && (get_searchbox_value($spsectiontype, 'showprocessing')||get_searchbox_value($spsectiontype2, 'showprocessing')))); //whether show processings or not
$showteam = (($allowtorrents && get_searchbox_value($brsectiontype, 'showteam')) || ($allowspecial && (get_searchbox_value($spsectiontype, 'showteam')||get_searchbox_value($spsectiontype2, 'showteam')))); //whether show teams or not
$showaudiocodec = (($allowtorrents && get_searchbox_value($brsectiontype, 'showaudiocodec')) || ($allowspecial && (get_searchbox_value($spsectiontype, 'showaudiocodec')||get_searchbox_value($spsectiontype2, 'showaudiocodec')))); //whether show languages or not

stdhead($lang_upload['head_upload']);
?>

<script type="text/javascript" src="common.php<?php $cssupdatedate=($cssdate_tweak ? "?".htmlspecialchars($cssdate_tweak) : "");echo $cssupdatedate?>"></script>
	<form id="compose" enctype="multipart/form-data" action="takeupload.php" method="post" name="upload">
			<?php
			//print("<p align=\"center\">".$lang_upload['text_red_star_required']."</p>");
			?>
			<table border="1" cellspacing="0" cellpadding="5" width="98%">
				<!--<tr>
					<td class='colhead' colspan='2' align='left'>
						<?php echo $lang_upload['text_tracker_url'] ?><p>
						<b><?php echo  get_protocol_prefix() . $announce_urls[0] ."?passkey=".$CURUSER[passkey] ?></b><br /><br />
						<b><?php echo  get_protocol_prefix() . $announce_urls[1] ."?passkey=".$CURUSER[passkey] ?></b>
						<?php
						if(!is_writable($torrent_dir))
						print("<br /><br /><b>ATTENTION</b>: Torrent directory isn't writable. Please contact the administrator about this problem!");
						if(!$max_torrent_size)
						print("<br /><br /><b>ATTENTION</b>: Max. Torrent Size not set. Please contact the administrator about this problem!");
						?>
					</td>
				</tr>-->
				
				<tr>
					<td class='colhead' colspan='2' align='center'>
					<font color="red">请按照规则要求发布资源</font>
					</td>
				</tr>
				<tr>
					<td class='colhead' colspan='2' align='left'>
						<?php echo $lang_upload['text_tracker_url'] ?>&nbsp;&nbsp;<b><?php echo  get_protocol_prefix() . $announce_urls[0]?></b>
					</td>
				</tr
				

				><?php
				
					//tr($lang_upload['row_torrent_file']."<font color=\"red\">*</font>", "<input type=\"file\" class=\"file\" id=\"torrent\" name=\"file\" onchange=\"getname()\" />", 1);
					tr($lang_upload['row_torrent_file']."<font color=\"red\">*</font>", "<input type=\"file\" class=\"file\" id=\"torrent\" name=\"file\" accept=\"application/x-bittorrent\" /><br />转载的种子请直接上传原种子,以方便辅种", 1);
						
					$notechange = " onchange=\"javascript:secondtype();notechange();\"";
					$s = "<select name=\"type\" id=\"browsecat\" ". $notechange.">\n<option value=\"0\">".$lang_upload['select_choose_one']."</option>\n";
					
					if($allowtorrents){$cats = genrelist($browsecatmode);
					foreach ($cats as $row)
						$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";}
						
					//if ($allowspecial&&!$allowtorrents)
					if ($allowspecial&&!$allowtorrents){
					$cats2 = genrelist($specialcatmode);
					foreach ($cats2 as $row)
					$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
				}	
					
					
					if ($spsectiontype2){
					$cats3 = genrelist($spsectiontype2);
					foreach ($cats3 as $row)
					$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
				}	
					$s .= "</select>\n";
					
					
					
				
				
				
				
				
					//tr($lang_upload['row_type']."<font color=\"red\">*</font>",$s,1);

				
				
					
					
					if ($showsource){
						$source_select = torrent_selection($lang_upload['text_source'],"source_sel","sources");
					}
					else $source_select = "";

					if ($showmedium){
						$medium_select = torrent_selection($lang_upload['text_medium'],"medium_sel","media");
					}
					else $medium_select = "";

					if ($showcodec){
						$codec_select = torrent_selection($lang_upload['text_codec'],"codec_sel","codecs");
					}
					else $codec_select = "";

					if ($showaudiocodec)
					{
						//$audiocodec_select = torrent_selection($lang_upload['text_audio_codec'],"audiocodec_sel","audiocodecs");
						$audiocodec_select = torrent_selection("","audiocodec_sel","audiocodecs",null,true);

						
					}
					else $audiocodec_select = "";

					if ($showstandard){
						$standard_select = torrent_selection($lang_upload['text_standard'],"standard_sel","standards");
					}
					else $standard_select = "";

					if ($showprocessing){
						$processing_select = torrent_selection($lang_upload['text_processing'],"processing_sel","processings");
					}
					else $processing_select = "";
				if ($showteam){
						$team_select = torrent_selection($lang_upload['text_team'],"team_sel","teams");
					}
					else $showteam = "";
				
					tr($lang_upload['row_quality']."<font color=\"red\">*</font>","<b>".$lang_upload['row_type'].":&nbsp;<b>".$s.$audiocodec_select."<b><font class=\"medium\" id=\"texttorrentsecondnote\" ></font></b><br />".$source_select . $medium_select. $codec_select .  $standard_select .$team_select. $processing_select,1);
					//tr($lang_upload['row_quality'], $source_select . $medium_select. $codec_select . $audiocodec_select. $standard_select . $processing_select, 1 );
				
				
				
				
				

				
				
				
				if ($altname_main == 'yes')
				{
					tr($lang_upload['row_torrent_name']."<font color=\"red\">*</font>",
"<b>".$lang_upload['text_chinese_title']."</b>&nbsp;<input type=\"text\" style=\"width: 250px\" name=\"cnname\">					
<b>".$lang_upload['text_english_title']."</b>&nbsp;<input type=\"text\" style=\"width: 250px;\" name=\"name\" />	
<b>文件格式:</b>&nbsp;<input type=\"text\" style=\"width: 50px;\" name=\"typeform\" /><br />	


<b>其他信息或已规范的标题:</b>&nbsp;<input type=\"text\" style=\"width: 350px;\" id=\"nameadd\" name=\"nameadd\"   />
				&nbsp;	<b><font class=\"medium\" id=\"texttorrentnamenote\">".$lang_upload['text_torrent_name_note']."</font></b>
					", 1);
				}
				else
				
					tr($lang_upload['row_torrent_name']."<font color=\"red\">*</font>", "
					<input type=\"text\" style=\"width: 650px;\" id=\"name\" name=\"name\"   /><br />
					<b><font class=\"medium\" id=\"texttorrentnamenote\">".$lang_upload['text_torrent_name_note']."</font></b>", 1);
				
				if ($smalldescription_main == 'yes')
				tr($lang_upload['row_small_description'], "<input type=\"text\" style=\"width: 650px;\" name=\"small_descr\" /><br /><b><font class=\"medium\" id=\"texttorrentsmaillnamenote\" >".$lang_upload['text_small_description_note']."</font></b>", 1);
				
				get_external_tr();
				if ($enablenfo_main=='yes')
					tr($lang_upload['row_nfo_file'], "<input type=\"file\" class=\"file\" name=\"nfo\" /><font class=\"medium\">".$lang_upload['text_only_viewed_by'].get_user_class_name($viewnfo_class,false,true,true).$lang_upload['text_or_above']."</font>", 1);
				print("<tr><td class=\"rowhead\" style='padding: 3px' valign=\"top\">".$lang_upload['row_description']."<font color=\"red\">*</font></td><td class=\"rowfollow\">");
				textbbcode("upload","descr","",false);
				print("</td></tr>\n");

				/*if ($allowtorrents){
					$disablespecial = " onchange=\"disableother('browsecat','specialcat')\"";
					$s = "<select name=\"type\" id=\"browsecat\" ".($allowtwosec ? $disablespecial : "").">\n<option value=\"0\">".$lang_upload['select_choose_one']."</option>\n";
					$cats = genrelist($browsecatmode);
					foreach ($cats as $row)
						$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
					$s .= "</select>\n";
				}
				else $s = "";
				if ($allowspecial){
					$disablebrowse = " onchange=\"disableother('specialcat','browsecat')\"";
					$s2 = "<select name=\"type\" id=\"specialcat\" ".$disablebrowse.">\n<option value=\"0\">".$lang_upload['select_choose_one']."</option>\n";
					$cats2 = genrelist($specialcatmode);
					foreach ($cats2 as $row)
						$s2 .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
					$s2 .= "</select>\n";
				}
				else $s2 = "";
				tr($lang_upload['row_type']."<font color=\"red\">*</font>", ($allowtwosec ? $lang_upload['text_to_browse_section'] : "").$s.($allowtwosec ? $lang_upload['text_to_special_section'] : "").$s2.($allowtwosec ? $lang_upload['text_type_note'] : ""),1);

				if ($showsource || $showmedium || $showcodec || $showaudiocodec || $showstandard || $showprocessing){
					if ($showsource){
						$source_select = torrent_selection($lang_upload['text_source'],"source_sel","sources");
					}
					else $source_select = "";

					if ($showmedium){
						$medium_select = torrent_selection($lang_upload['text_medium'],"medium_sel","media");
					}
					else $medium_select = "";

					if ($showcodec){
						$codec_select = torrent_selection($lang_upload['text_codec'],"codec_sel","codecs");
					}
					else $codec_select = "";

					if ($showaudiocodec){
						$audiocodec_select = torrent_selection($lang_upload['text_audio_codec'],"audiocodec_sel","audiocodecs");
					}
					else $audiocodec_select = "";

					if ($showstandard){
						$standard_select = torrent_selection($lang_upload['text_standard'],"standard_sel","standards");
					}
					else $standard_select = "";

					if ($showprocessing){
						$processing_select = torrent_selection($lang_upload['text_processing'],"processing_sel","processings");
					}
					else $processing_select = "";
				
					tr($lang_upload['row_quality'], $source_select . $medium_select. $codec_select . $audiocodec_select. $standard_select . $processing_select, 1 );
				}*/

				/*if ($showteam){
					if ($showteam){
						$team_select = torrent_selection($lang_upload['text_team'],"team_sel","teams");
					}
					else $showteam = "";

					tr($lang_upload['row_content'],$team_select,1);
				}
*/
				//==== offer dropdown for offer mod  from code by S4NE
				$offerres = sql_query("SELECT id, name FROM offers WHERE userid = ".sqlesc($CURUSER[id])." AND allowed = 'allowed' ORDER BY name ASC") or sqlerr(__FILE__, __LINE__);
				if (mysql_num_rows($offerres) > 0)
				{
					$offer = "<select name=\"offer\"><option value=\"0\">".$lang_upload['select_choose_one']."</option>";
					while($offerrow = mysql_fetch_array($offerres))
						$offer .= "<option value=\"" . $offerrow["id"] . "\">" . htmlspecialchars($offerrow["name"]) . "</option>";
					$offer .= "</select>";
					tr($lang_upload['row_your_offer']. (!$uploadfreely && !$allowspecial ? "<font color=red>*</font>" : ""), $offer.$lang_upload['text_please_select_offer'] , 1);
				}
				//===end

				/*if(get_user_class()>=$beanonymous_class)
				{
					tr($lang_upload['row_show_uploader'], "<input type=\"checkbox\" name=\"uplver\" value=\"yes\" />".$lang_upload['checkbox_hide_uploader_note'], 1);
				}*/
								tr($lang_upload['row_show_other'],"<input type=\"checkbox\" name=\"source\" value=\"yes\" checked ".(get_user_class()<= UC_VIP?"disabled":"")." >".$lang_upload['text_addsource']."\n".(get_user_class()>=$beanonymous_class? "<br/><input type=\"checkbox\" name=\"uplver\" value=\"yes\" />".$lang_upload['checkbox_hide_uploader_note']:""),1)
			
				?>
				<tr><td class="toolbox" align="center" colspan="2"><b><?php /*echo $lang_upload['text_read_rules']*/?></b> <input id="qr" type="button" onclick="javascript:{closealltags();this.form.submit();this.disabled=true;}"  value="<?php echo "&nbsp;&nbsp;".$lang_upload['submit_upload']."&nbsp;&nbsp;"?>" /></td></tr>
		</table>
	</form>
	
<?php
print("<script>secondtype();notechange();</script> ");
stdfoot();
