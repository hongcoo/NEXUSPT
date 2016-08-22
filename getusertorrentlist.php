<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path("getusertorrentlistajax.php"));
//if(Extension_Loaded('zlib')) Ob_Start('ob_gzhandler'); //gzipchenzhuyu
//Send some headers to keep the user's browser from caching the response.

loggedinorreturn();
if ($CURUSER["torrentsperpage"])
$torrentsperpage = (int)$CURUSER["torrentsperpage"];
elseif ($torrentsperpage_main)
	$torrentsperpage = $torrentsperpage_main;
else $torrentsperpage = 50;

$staffmem_class=99;
function maketable($res, $mode = 'seeding')
{
	global $lang_getusertorrentlistajax,$CURUSER,$smalldescription_main,$staffmem_class;
	switch ($mode)
	{
		case 'uploaded': {
		$showsize = true;
		$showsenum = true;
		$showlenum = true;
		$showcomnum=true;
		$showuploaded = true;
		$showdownloaded = false;
		$showratio = false;
		$showsetime = true;
		$showletime = false;
		$showcotime = false;
		$showanonymous = true;
		$columncount = 8;
		break;
		}
		case 'seeding': {
		$showsize = true;
		$showsenum = true;
		$showlenum = true;
		$showcomnum=true;
		$showuploaded = true;
		$showdownloaded = true;
		$showratio = true;
		$showsetime = true;
		$showletime = false;
		$showcotime = false;
		$showanonymous = false;
		$columncount = 8;
		break;
		}
		case 'leeching': {
		$showsize = true;
		$showsenum = true;
		$showlenum = true;
		$showcomnum=true;
		$showuploaded = true;
		$showdownloaded = true;
		$showratio = true;
		$showsetime = false;
		$showletime = true;
		$showcotime = false;
		$showanonymous = false;
		$columncount = 8;
		break;
		}
		case 'completed': {
		$showsize = true;
		$showsenum = false;
		$showlenum = false;
		$showcomnum=false;
		$showuploaded = true;
		$showdownloaded = true;
		$showratio = true;
		$showsetime = true;
		$showletime = true;
		$showcotime = true;
		$showanonymous = false;
		$columncount = 8;
		break;
		}
		case 'incomplete': {
		$showsize = true;
		$showsenum = false;
		$showlenum = false;
		$showcomnum=false;
		$showuploaded = true;
		$showdownloaded = true;
		$showratio = true;
		$showsetime = false;
		$showletime = true;
		$showcotime = false;
		$showanonymous = false;
		$columncount = 7;
		break;
		}			
		
		default: break;
	}
	$ret = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" width=\"98%\"><tr>".("<td class=\"colhead\" style=\"padding: 0px\">".$lang_getusertorrentlistajax['col_type']."</td><td class=\"colhead\" align=\"center\">".$lang_getusertorrentlistajax['col_name']."</td>").
	($showsize ? "<td class=\"colhead\" align=\"center\"><img class=\"size\" src=\"pic/trans.gif\" alt=\"size\" title=\"".$lang_getusertorrentlistajax['title_size']."\" /></td>" : "").($showsenum ? "<td class=\"colhead\" align=\"center\"><img class=\"seeders\" src=\"pic/trans.gif\" alt=\"seeders\" title=\"".$lang_getusertorrentlistajax['title_seeders']."\" /></td>" : "").($showlenum ? "<td class=\"colhead\" align=\"center\"><img class=\"leechers\" src=\"pic/trans.gif\" alt=\"leechers\" title=\"".$lang_getusertorrentlistajax['title_leechers']."\" /></td>" : "").($showcomnum ? "<td class=\"colhead\" align=\"center\"><img class=\"snatched\" src=\"pic/trans.gif\" alt=\"snatched\" title=\"完成数\" /></td>" : "").($showuploaded ? "<td class=\"colhead\" align=\"center\">".$lang_getusertorrentlistajax['col_uploaded']."</td>" : "") . ($showdownloaded ? "<td class=\"colhead\" align=\"center\">".$lang_getusertorrentlistajax['col_downloaded']."</td>" : "").($showratio ? "<td class=\"colhead\" align=\"center\">".$lang_getusertorrentlistajax['col_ratio']."</td>" : "").($showsetime ? "<td class=\"colhead\" align=\"center\">".$lang_getusertorrentlistajax['col_se_time']."</td>" : "").($showletime ? "<td class=\"colhead\" align=\"center\">".$lang_getusertorrentlistajax['col_le_time']."</td>" : "").($showcotime ? "<td class=\"colhead\" align=\"center\">".$lang_getusertorrentlistajax['col_time_completed']."</td>" : "").($showanonymous ? "<td class=\"colhead\" align=\"center\">".$lang_getusertorrentlistajax['col_anonymous']."</td>" : "").((get_user_class() >= $staffmem_class) ? "<td class=\"colhead\" align=\"center\">数据总量</td>" : "")."</tr>\n";
	while ($arr = mysql_fetch_assoc($res))
	{
		$catimage = htmlspecialchars($arr["image"]);
		$catname = htmlspecialchars($arr["catname"]);

		$sphighlight = get_torrent_bg_color($arr['sp_state']);
		$sp_torrent = get_torrent_promotion_append($arr['sp_state']);

		//torrent name
		$dispname = $nametitle = htmlspecialchars($arr["torrentname"]);
		$count_dispname=mb_strlen($dispname,"UTF-8");
		$max_lenght_of_torrent_name=($CURUSER['fontsize'] == 'large' ? 70 : 80);
		if($count_dispname > $max_lenght_of_torrent_name)
			$dispname=mb_substr($dispname, 0, $max_lenght_of_torrent_name,"UTF-8") . "..";
		if ($smalldescription_main == 'yes'){
			//small description
			$dissmall_descr = htmlspecialchars(trim($arr["small_descr"]));
			$count_dissmall_descr=mb_strlen($dissmall_descr,"UTF-8");
			$max_lenght_of_small_descr=80; // maximum length
			if($count_dissmall_descr > $max_lenght_of_small_descr)
			{
				$dissmall_descr=mb_substr($dissmall_descr, 0, $max_lenght_of_small_descr,"UTF-8") . "..";
			}
		}
		else $dissmall_descr == "";
		$ret .= "<tr" .  $sphighlight  . ">".("<td class=\"rowfollow nowrap\" valign=\"middle\" style='padding: 0px'><a href=\"download.php?id=" . $arr[torrent] . "\">".return_category_image($arr['category'])."</a></td>\n" .
		"<td class=\"rowfollow\" width=\"100%\" align=\"left\"  style='padding-top:0;padding-bottom: 0px;'><a href=\"".htmlspecialchars("details.php?id=".$arr[torrent]."&hit=1")."\" title=\"".$nametitle."\"><b>" . $dispname . "</b></a>". $sp_torrent .($dissmall_descr == "" ? "" : "<br />" . $dissmall_descr) . "</td>");
		//size
		if ($showsize)
			$ret .= "<td class=\"rowfollow\" align=\"center\">". mksize($arr['size'])."</td>";
		//number of seeders
		if ($showsenum)
			$ret .= "<td class=\"rowfollow\" align=\"center\">".$arr['seeders']."</td>";
		//number of leechers
		if ($showlenum)
			$ret .= "<td class=\"rowfollow\" align=\"center\">".$arr['leechers']."</td>";
			if ($showcomnum)
			$ret .= "<td class=\"rowfollow\" align=\"center\">".$arr['times_completed']."</td>";	
			
			
			
			
		//uploaded amount
		if ($showuploaded){
			$uploaded = mksize($arr["uploaded"]);
			$imuploaded = mksize($arr["imuploaded"]);
			$ret .= "<td class=\"rowfollow\" align=\"center\">".$imuploaded.'<br />'.$uploaded."</td>";
		}
		//downloaded amount
		if ($showdownloaded){
			$downloaded = mksize($arr["downloaded"]);
			$imdownloaded = mksize($arr["imdownloaded"]);
			$ret .= "<td class=\"rowfollow\" align=\"center\">".$imdownloaded.'<br />'.$downloaded."</td>";
		}
		//ratio
		if ($showratio){
			if ($arr['downloaded'] > 0)
			{
				$ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
				$ratio = "<font color=\"" . get_ratio_color($ratio) . "\">".$ratio."</font>";
			}
			elseif ($arr['uploaded'] > 0) $ratio = "Inf.";
			else $ratio = "---";
			$ret .= "<td class=\"rowfollow\" align=\"center\">".$ratio."</td>";
		}
		if ($showsetime){
			$ret .= "<td class=\"rowfollow\" align=\"center\">".mkprettytime($arr['seedtime'])."</td>";
		}
		if ($showletime){
			$ret .= "<td class=\"rowfollow\" align=\"center\">".mkprettytime($arr['leechtime'])."</td>";
		}
		if ($showcotime)
			$ret .= "<td class=\"rowfollow\" align=\"center\">"."". str_replace("&nbsp;", "<br />", gettime($arr['completedat'],false)). "</td>";
		if ($showanonymous)
			$ret .= "<td class=\"rowfollow\" align=\"center\">".($arr['anonymous']=='yes'?"活种":"<b>断种</b>")."</td>";
		if (get_user_class() >= $staffmem_class)
			$ret .= "<td class=\"rowfollow\" align=\"center\">".mksize($arr["progressTotaluploaded"])."<br />".mksize($arr["progressTotaldownloaded"])."</td>";	
			
		$ret .="</tr>\n";
		
	}
	$ret .= "</table>\n";
	return $ret;
}

$id = 0+$_GET['userid'];
if(!$id)$id =$CURUSER[id];
$type = $_GET['type'];
if (!in_array($type,array('uploaded','seeding','leeching','completed','incomplete','noseeding','completednoseeding')))
$type='uploaded';
if(get_user_class() < $torrenthistory_class && $id != $CURUSER["id"])
permissiondenied();

switch ($type)
{
	case 'uploaded':
	{
		$res = sql_query("SELECT COUNT(*) FROM torrents LEFT JOIN snatched ON (torrents.id = snatched.torrentid AND snatched.userid=$id) WHERE torrents.owner=$id  " . (($CURUSER["id"] != $id)?((get_user_class() < $viewanonymous_class) ? " AND anonymous = 'no'":""):"")) or sqlerr(__FILE__, __LINE__);
		$arr3 = mysql_fetch_row($res);
		$count = $arr3[0];
		list($pagertop, $pagerbottom, $limit2) = pager($torrentsperpage, $count, "?userid=".$id."&type=".$type."&");
		
		$res = sql_query("SELECT  ".((get_user_class() >= $staffmem_class) ? "(SELECT sum(uploaded) FROM snatched  where torrentid=torrents.id ) as progressTotaluploaded, (SELECT sum(downloaded) FROM snatched  where torrentid=torrents.id ) as progressTotaldownloaded," : "")."torrents.id AS torrent, torrents.name as torrentname,torrents.times_completed,  small_descr, seeders, leechers, visible  as anonymous, categories.name AS catname, categories.image, category, sp_state, size, snatched.seedtime, snatched.uploaded ,snatched.imuploaded,snatched.imdownloaded FROM torrents LEFT JOIN snatched ON (torrents.id = snatched.torrentid AND snatched.userid=$id) LEFT JOIN categories ON torrents.category = categories.id WHERE torrents.owner=$id  " . (($CURUSER["id"] != $id)?((get_user_class() < $viewanonymous_class) ? " AND anonymous = 'no'":""):"") ." ORDER BY  visible ASC , torrents.added DESC $limit2") or sqlerr(__FILE__, __LINE__);
		if ($count > 0)
		{
			$torrentlist = maketable($res, 'uploaded');
		}
		break;
	}
	
		case 'noseeding':
	{
		$res = sql_query("SELECT COUNT(*) FROM torrents LEFT JOIN snatched ON (torrents.id = snatched.torrentid AND snatched.userid=$id) WHERE torrents.owner=$id and torrents.id not in (SELECT DISTINCT(torrent) FROM peers WHERE peers.userid=$id ) " . (($CURUSER["id"] != $id)?((get_user_class() < $viewanonymous_class) ? " AND anonymous = 'no'":""):"")) or sqlerr(__FILE__, __LINE__);
		$arr3 = mysql_fetch_row($res);
		$count = $arr3[0];
		list($pagertop, $pagerbottom, $limit2) = pager($torrentsperpage, $count, "?userid=".$id."&type=".$type."&");
		
		$res = sql_query("SELECT  ".((get_user_class() >= $staffmem_class) ? "(SELECT sum(uploaded) FROM snatched  where torrentid=torrents.id ) as progressTotaluploaded, (SELECT sum(downloaded) FROM snatched  where torrentid=torrents.id ) as progressTotaldownloaded," : "")."torrents.id AS torrent, torrents.name as torrentname,torrents.times_completed,  small_descr, seeders, leechers, visible  as anonymous, categories.name AS catname, categories.image, category, sp_state, size, snatched.seedtime, snatched.uploaded ,snatched.imuploaded,snatched.imdownloaded FROM torrents LEFT JOIN snatched ON (torrents.id = snatched.torrentid AND snatched.userid=$id) LEFT JOIN categories ON torrents.category = categories.id WHERE torrents.id not in (SELECT DISTINCT(torrent) FROM peers WHERE peers.userid=$id )  and torrents.owner=$id  " . (($CURUSER["id"] != $id)?((get_user_class() < $viewanonymous_class) ? " AND anonymous = 'no'":""):"") ." ORDER BY  visible ASC , torrents.added DESC $limit2") or sqlerr(__FILE__, __LINE__);
		if ($count > 0)
		{
			$torrentlist = maketable($res, 'uploaded');
		}
		break;
	}

	// Current Seeding
	case 'seeding':
	{
		$res = sql_query("SELECT COUNT(DISTINCT(torrent)) FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id  LEFT JOIN snatched ON torrents.id = snatched.torrentid WHERE peers.userid=$id AND snatched.userid = $id AND peers.seeder='yes' ") or sqlerr();
		$arr3 = mysql_fetch_row($res);
		$count = $arr3[0];
		list($pagertop, $pagerbottom, $limit2) = pager($torrentsperpage, $count, "?userid=".$id."&type=".$type."&");
		$res = sql_query("SELECT DISTINCT(torrent),  ".((get_user_class() >= $staffmem_class) ? "(SELECT sum(uploaded) FROM snatched  where torrentid=peers.torrent ) as progressTotaluploaded, (SELECT sum(downloaded) FROM snatched  where torrentid=peers.torrent ) as progressTotaldownloaded," : "")."added,snatched.uploaded,snatched.downloaded,snatched.imuploaded,snatched.imdownloaded,snatched.seedtime,torrents.name as torrentname,torrents.times_completed,  torrents.small_descr, torrents.sp_state, categories.name as catname,size,image,category,seeders,leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN snatched ON torrents.id = snatched.torrentid WHERE peers.userid=$id AND snatched.userid = $id AND peers.seeder='yes'  ORDER BY torrents.added DESC $limit2") or sqlerr();
		
		if ($count > 0){
			$torrentlist = maketable($res, 'seeding');
		}
		break;
	}
	
	

	// Current Leeching
	case 'leeching':
	{
		$res = sql_query("SELECT COUNT(DISTINCT(torrent)) FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id  LEFT JOIN snatched ON torrents.id = snatched.torrentid WHERE peers.userid=$id AND snatched.userid = $id AND peers.seeder='no'  ") or sqlerr();
		$arr3 = mysql_fetch_row($res);
		$count = $arr3[0];
		list($pagertop, $pagerbottom, $limit2) = pager($torrentsperpage, $count, "?userid=".$id."&type=".$type."&");
		$res = sql_query("SELECT DISTINCT(torrent),  ".((get_user_class() >= $staffmem_class) ? "(SELECT sum(uploaded) FROM snatched  where torrentid=peers.torrent ) as progressTotaluploaded, (SELECT sum(downloaded) FROM snatched  where torrentid=peers.torrent ) as progressTotaldownloaded," : "")."snatched.uploaded,snatched.downloaded,snatched.imuploaded,snatched.imdownloaded,snatched.leechtime,torrents.name as torrentname,torrents.times_completed,  torrents.small_descr, torrents.sp_state, categories.name as catname,size,image,category,seeders,leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN snatched ON torrents.id = snatched.torrentid WHERE peers.userid=$id AND snatched.userid = $id AND peers.seeder='no'  ORDER BY torrents.added DESC $limit2") or sqlerr();
		
		if ($count > 0){
			$torrentlist = maketable($res, 'leeching');
		}
		break;
	}

	// Completed torrents
	case 'completed':
	{
		$res = sql_query("SELECT COUNT(*) FROM torrents LEFT JOIN snatched ON torrents.id = snatched.torrentid  WHERE snatched.finished='yes' AND torrents.owner != $id AND userid=$id ") or sqlerr();
		$arr3 = mysql_fetch_row($res);
		$count = $arr3[0];
		list($pagertop, $pagerbottom, $limit2) = pager($torrentsperpage, $count, "?userid=".$id."&type=".$type."&");
		$res = sql_query("SELECT  ".((get_user_class() >= $staffmem_class) ? "(SELECT sum(uploaded) FROM snatched  where torrentid=torrents.id ) as progressTotaluploaded, (SELECT sum(downloaded) FROM snatched  where torrentid=torrents.id ) as progressTotaldownloaded," : "")."torrents.id AS torrent, torrents.name AS torrentname,torrents.times_completed,  small_descr, categories.name AS catname, categories.image, category, sp_state, size, snatched.uploaded,snatched.downloaded,snatched.imuploaded,snatched.imdownloaded, snatched.seedtime, snatched.leechtime, snatched.completedat FROM torrents LEFT JOIN snatched ON torrents.id = snatched.torrentid LEFT JOIN categories on torrents.category = categories.id WHERE snatched.finished='yes' AND torrents.owner != $id AND userid=$id ORDER BY snatched.completedat DESC $limit2") or sqlerr();
		
		if ($count > 0)
		{
			$torrentlist = maketable($res, 'completed');
		}
		break;
	}
	
		case 'completednoseeding':
	{
		$res = sql_query("SELECT COUNT(*) FROM torrents LEFT JOIN snatched ON torrents.id = snatched.torrentid  WHERE  torrents.id not in (SELECT DISTINCT(torrent) FROM peers WHERE peers.userid=$id ) and 
		 snatched.finished='yes' AND torrents.owner != $id AND userid=$id ") or sqlerr();
		$arr3 = mysql_fetch_row($res);
		$count = $arr3[0];
		list($pagertop, $pagerbottom, $limit2) = pager($torrentsperpage, $count, "?userid=".$id."&type=".$type."&");
		$res = sql_query("SELECT  ".((get_user_class() >= $staffmem_class) ? "(SELECT sum(uploaded) FROM snatched  where torrentid=torrents.id ) as progressTotaluploaded, (SELECT sum(downloaded) FROM snatched  where torrentid=torrents.id ) as progressTotaldownloaded," : "")."torrents.id AS torrent, torrents.name AS torrentname,torrents.times_completed,  small_descr, categories.name AS catname, categories.image, category, sp_state, size, snatched.uploaded,snatched.downloaded,snatched.imuploaded,snatched.imdownloaded, snatched.seedtime, snatched.leechtime, snatched.completedat FROM torrents LEFT JOIN snatched ON torrents.id = snatched.torrentid LEFT JOIN categories on torrents.category = categories.id WHERE torrents.id not in (SELECT DISTINCT(torrent) FROM peers WHERE peers.userid=$id )  and snatched.finished='yes' AND torrents.owner != $id AND userid=$id ORDER BY snatched.completedat DESC $limit2") or sqlerr();
		
		if ($count > 0)
		{
			$torrentlist = maketable($res, 'completed');
		}
		break;
	}

	// Incomplete torrents
	case 'incomplete':
	{
		$res = sql_query("SELECT COUNT(*) FROM torrents LEFT JOIN snatched ON torrents.id = snatched.torrentid WHERE snatched.finished='no' AND userid=$id AND torrents.owner != $id ") or sqlerr();
		$arr3 = mysql_fetch_row($res);
		$count = $arr3[0];
		list($pagertop, $pagerbottom, $limit2) = pager($torrentsperpage, $count, "?userid=".$id."&type=".$type."&");
		$res = sql_query("SELECT  ".((get_user_class() >= $staffmem_class) ? "(SELECT sum(uploaded) FROM snatched  where torrentid=torrents.id ) as progressTotaluploaded, (SELECT sum(downloaded) FROM snatched  where torrentid=torrents.id ) as progressTotaldownloaded," : "")."torrents.id AS torrent, torrents.name AS torrentname,torrents.times_completed,  small_descr, categories.name AS catname, categories.image, category, sp_state, size, snatched.uploaded, snatched.downloaded,snatched.imuploaded,snatched.imdownloaded, snatched.leechtime FROM torrents LEFT JOIN snatched ON torrents.id = snatched.torrentid LEFT JOIN categories on torrents.category = categories.id WHERE snatched.finished='no' AND userid=$id AND torrents.owner != $id ORDER BY snatched.startdat DESC $limit2") or sqlerr();
		if ($count > 0)
		{
			$torrentlist = maketable($res, 'incomplete');
		}
		break;
	}
	default: 
	{
		$count = 0;
		$torrentlist = "";
		break;
	}
}
stdhead("种子历史",true, "", "",true);
switch ($type)
{
	case 'uploaded':{$typestate	="发布的种子";break;}
	case 'seeding':{$typestate	="做种中的种子";break;}
	case 'leeching':{$typestate="正在下载的种子";	break;}
	case 'completed':{$typestate="完成的种子";	break;}
	case 'incomplete':{$typestate="未完成的种子";	break;}
	case 'noseeding':{$typestate="撤种的种子";	break;}
	case 'completednoseeding':{$typestate="未保种的种子";	break;}	
}
print("<h1 align=center>".get_plain_username($id)."  $typestate (共 $count)</h1>");
print("<br><b><a href=getusertorrentlist.php?userid=".$id."&type=uploaded>查看所发布</a> | 
<a href=getusertorrentlist.php?userid=".$id."&type=noseeding>查看已撤种</a> | 
<a href=getusertorrentlist.php?userid=".$id."&type=seeding>查看做种中</a> | 
<a href=getusertorrentlist.php?userid=".$id."&type=leeching>查看下载中</a> | 
<a href=getusertorrentlist.php?userid=".$id."&type=completed>查看已完成</a> | 
<a href=getusertorrentlist.php?userid=".$id."&type=completednoseeding>查看未保种</a> | 
<a href=getusertorrentlist.php?userid=".$id."&type=incomplete>查看未完成</a>
</b><p>\n");
if ($count)
echo $torrentlist;
else
echo $lang_getusertorrentlistajax['text_no_record'];
print($pagerbottom);
stdfoot();
//if(Extension_Loaded('zlib')) {Ob_End_Flush();} //gzipchenzhuyu
?>
