<?php
require_once('include/bittorrent.php');
dbconn();
require_once(get_langfile_path());
require(get_langfile_path("",true));
loggedinorreturn();
parked();

if(get_user_class() <= 1)$bonusgift_bonus='no';

function bonusarray($option){
	global $onegbupload_bonus,$fivegbupload_bonus,$tengbupload_bonus,$oneinvite_bonus,$customtitle_bonus,$vipstatus_bonus, $basictax_bonus, $taxpercentage_bonus, $bonusnoadpoint_advertisement, $bonusnoadtime_advertisement,$namecolour_bonus,$namechange_bonus,$nohr2d_bonus;
	global $lang_mybonus,$CURUSER,$invite_timeout;
	$bonus = array();
	switch ($option)
	{
		case 1: {//1.0 GB Uploaded
			$bonus['points'] = $onegbupload_bonus;
			$bonus['art'] = 'traffic';
			$bonus['menge'] = 10737418240;
			$bonus['name'] = $lang_mybonus['text_uploaded_one']."(10.0 GB)";
			$bonus['description'] = $lang_mybonus['text_uploaded_note'];
			break;
			}
		case 2: {//5.0 GB Uploaded
			$bonus['points'] = $fivegbupload_bonus;
			$bonus['art'] = 'traffic';
			$bonus['menge'] = 53687091200;
			$bonus['name'] = $lang_mybonus['text_uploaded_two']."(50.0 GB)";
			$bonus['description'] = $lang_mybonus['text_uploaded_note'];
			break;
			}
		case 3: {//10.0 GB Uploaded
			$bonus['points'] = $tengbupload_bonus;
			$bonus['art'] = 'traffic';
			$bonus['menge'] = 107374182400;
			$bonus['name'] = $lang_mybonus['text_uploaded_three']."(100.0 GB)";
			$bonus['description'] = $lang_mybonus['text_uploaded_note'];
			break;
			}
		case 4: {//Invite
			$bonus['points'] = $oneinvite_bonus;
			$bonus['art'] = 'invite';
			$bonus['menge'] = 1;
			$bonus['name'] = $lang_mybonus['text_buy_invite'];
			
			$invitesall=get_row_count("invites","WHERE 1");
			$invitespast24h=get_row_count("invites","WHERE time_invited >" . sqlesc(date("Y-m-d H:i:s",(TIMENOW -24*60*60))));
			$inviterpast24h=get_row_count("users","WHERE invited_by !='' AND added >" . sqlesc(date("Y-m-d H:i:s",(TIMENOW -24*60*60))));
			$inviterpastcanuse=get_row_count("invites","WHERE time_invited >" . sqlesc(date("Y-m-d H:i:s",(TIMENOW+$invite_timeout*60*60))));
			$bonus['description'] = $lang_mybonus['text_buy_invite_note']."<br />当前共有契约书".($invitesall-$inviterpastcanuse)."份，24小时内生成的契约书".($invitespast24h-$inviterpastcanuse)."份，召唤出魔法少女".$inviterpast24h."名。";
			break;
			}
		case 5: {//Custom Title
			if($CURUSER['title']=='')
			$bonus['points'] = $customtitle_bonus/2;
			else
			$bonus['points'] = $customtitle_bonus;
			$bonus['art'] = 'title';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_custom_title'];
			$bonus['description'] = $lang_mybonus['text_custom_title_note'];
			break;
			}
		case 6: {//VIP Status
		
		if($CURUSER['vip_until']=='0000-00-00 00:00:00')
			$bonus['points'] = $vipstatus_bonus/2;
			else
			$bonus['points'] = $vipstatus_bonus;
			$bonus['art'] = 'class';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_vip_status'];
			$bonus['description'] = $lang_mybonus['text_vip_status_note'];
			break;
			}
		case 7: {//Bonus Gift
			$bonus['points'] = 100;
			$bonus['art'] = 'gift_1';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_bonus_gift'];
			$bonus['description'] = $lang_mybonus['text_bonus_gift_note'];
			if ($basictax_bonus || $taxpercentage_bonus){
				$onehundredaftertax = 100 - $taxpercentage_bonus - $basictax_bonus;
				$bonus['description'] .= "<br /><br />".$lang_mybonus['text_system_charges_receiver']."<b>".($basictax_bonus ? $basictax_bonus.$lang_mybonus['text_tax_bonus_point'].add_s($basictax_bonus).($taxpercentage_bonus ? $lang_mybonus['text_tax_plus'] : "") : "").($taxpercentage_bonus ? $taxpercentage_bonus.$lang_mybonus['text_percent_of_transfered_amount'] : "")."</b>".$lang_mybonus['text_as_tax'].$onehundredaftertax.$lang_mybonus['text_tax_example_note'];
				}
			break;
			}
		case 8: {
			$bonus['points'] = $bonusnoadpoint_advertisement;
			$bonus['art'] = 'noad';
			$bonus['menge'] = $bonusnoadtime_advertisement * 86400;
			$bonus['name'] = $bonusnoadtime_advertisement.$lang_mybonus['text_no_advertisements'];
			$bonus['description'] = $lang_mybonus['text_no_advertisements_note'];
			break;
			}
		case 9: {
			$bonus['points'] = 1000;
			$bonus['art'] = 'gift_2';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_charity_giving'];
			$bonus['description'] = $lang_mybonus['text_charity_giving_note'];
			break;
			}
			case 10: {
			$bonus['points'] = $namecolour_bonus;
			$bonus['art'] = 'namecolour';
			$bonus['menge'] = 0;
			$bonus['name'] = "五彩法杖";
			$bonus['description'] = "您的名字配将配以您指定的颜色显示。要改回正常颜色请输入「FFFFFF」(同样当作使用法杖一次)。 <br/>输入你想要的颜色的HEX代码或点击输入框选取颜色：<script type=\"text/javascript\" src=\"javascript/jscolor.js\"></script><input class=\"color\"  value = \"".$CURUSER['namecolour']."\" type=\"text\" name=\"chcol\" style=\"width: 232px\" maxlength=\"6\" />";
			break;
			}
			case 11: {
			$bonus['points'] = $namechange_bonus;
			$bonus['art'] = 'namechange';
			$bonus['menge'] = 0;
			$bonus['name'] = "转生羽毛";
			$bonus['description'] = "你可以用它来换取一个新的帐户名称。交易完成后，您现有的帐户名称将可被其他人使用。<br />
(注意：登入时需要输入用户名称，强烈建议您只使用您能于键盘输入的字符，以免令您无法登入。) <br />
输入一个新的用户名称： <input  value = \"".$CURUSER['username']."\" type=\"text\" name=\"newname\" style=\"width: 200px\" maxlength=\"20\"/>";
			break;
			}
			case 12: {
			$bonus['points'] = 0;
			$bonus['art'] = 'takeflush';
			$bonus['menge'] = 0;
			$bonus['name'] = "圣洁之水";
			$bonus['description'] = "在软件非正常退出时，清理服务器上冗余记录，避免分享率低的用户出现下载多个资源的提示。<br />但是一般都用不到吧";
			break;
			}
		case 13: {//Invite
			$bonus['menge'] = $CURUSER['invites']<5&&$CURUSER['invites']>0?$CURUSER['invites']:5;
			$bonus['points'] = 10;
			$bonus['unpoints'] = ($oneinvite_bonus*3/10)*$bonus['menge'];
			$bonus['art'] = 'uninvite';
			$bonus['name'] = "契约解除*".$bonus['menge'];
			$bonus['description'] = "如果有足够的邀请名额，你可以用它来换取魔力值。交易完成后，你的邀请名额数会减少，魔力值则会增加。";
			break;
			}
			
		case 14: {
			$bonus['points'] = $nohr2d_bonus;
			$bonus['art'] = 'nohr2d';
			$bonus['menge'] = 0;
			$bonus['name'] = "免罪金牌";
			$nohr2d=$_GET['nohr2d']?$_GET['nohr2d']:get_single_value('snatched', 'torrentid', "WHERE userid=".$CURUSER['id']." and hr='C'");
			$bonus['description'] = "对一个<b>未达标</b>的H&R使用该道具将免除该H&R的考核，H&R编号：<input name=\"nohr2d\"  value = \"".$nohr2d."\" type=\"text\" style=\"width: 232px\" maxlength=\"6\" />";
			
			break;
			}
			
		default: break;
	}
	return $bonus;
}

if ($bonus_tweak == "disable" || $bonus_tweak == "disablesave")
	stderr($lang_mybonus['std_sorry'],$lang_mybonus['std_karma_system_disabled'].($bonus_tweak == "disablesave" ? "<b>".$lang_mybonus['std_points_active']."</b>" : ""),false);

$action = htmlspecialchars($_GET['action']);
$do = htmlspecialchars($_GET['do']);
unset($msg);
if (isset($_GET['do'])) {
	if ($do == "upload")
	$msg = $lang_mybonus['text_success_upload'];
	elseif ($do == "invite")
	$msg = $lang_mybonus['text_success_invites'];
	elseif ($do == "vip")
	$msg =  $lang_mybonus['text_success_vip']."<b>".get_user_class_name(UC_VIP,false,false,true)."</b>".$lang_mybonus['text_success_vip_two'];
	elseif ($do == "vipfalse")
	$msg =  $lang_mybonus['text_no_permission'];
	elseif ($do == "title")
	$msg = $lang_mybonus['text_success_custom_title'];
	elseif ($do == "transfer")
	$msg =  $lang_mybonus['text_success_gift'];
	elseif ($do == "noad")
	$msg =  $lang_mybonus['text_success_no_ad'];
	elseif ($do == "charity")
	$msg =  $lang_mybonus['text_success_charity'];
	elseif ($do == "namecolour")
	$msg =  "更换颜色成功";
		elseif ($do == "namechange")
	$msg =  "更换名称成功";
	elseif ($do == "namechangeerr")
	$msg =  "更换名称失败,名称长度5-21半角字符";
	elseif ($do == "namechangeerrsame")
	$msg =  "更换名称失败,用户名重复";
		elseif ($do == "uninvite")
	$msg =  "兑换成功";
	elseif ($do == "nohr2d")
	$msg =  "免罪成功";
	elseif ($do == "nohr2derr")
	$msg =  "免罪失败,该ID不是未达标HR";
	
	
	
	else
	$msg = '兑换失败';
}
	stdhead($CURUSER['username'] . $lang_mybonus['head_karma_page']);

	$bonus = number_format($CURUSER['seedbonus'], 1);
if (!$action) {
	print("<table align=\"center\" width=\"98%\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n");
	print("<tr><td class=\"colhead\" colspan=\"4\" align=\"center\"><font class=\"big\">".$SITENAME.$lang_mybonus['text_karma_system']."</font></td></tr>\n");
	if ($msg)
	print("<tr><td align=\"center\" colspan=\"4\"><font class=\"striking\">". $msg ."</font></td></tr>");
?>
<tr><td class="text" align="center" colspan="4"><?php echo $lang_mybonus['text_exchange_your_karma']?><?php echo $bonus?><?php echo $lang_mybonus['text_for_goodies'] ?>
<br /><b><?php echo $lang_mybonus['text_no_buttons_note'] ?></b></td></tr>
<?php

print("<tr><td class=\"colhead\" align=\"center\">".$lang_mybonus['col_option']."</td>".
"<td class=\"colhead\" align=\"left\">".$lang_mybonus['col_description']."</td>".
"<td class=\"colhead\" align=\"center\">".$lang_mybonus['col_points']."</td>".
"<td class=\"colhead\" align=\"center\">".$lang_mybonus['col_trade']."</td>".
"</tr>");
for ($i=1; $i <=14; $i++)
{
	$bonusarray = bonusarray($i);
	if (($i == 7 && $bonusgift_bonus == 'no') || ($i == 8 && ($enablead_advertisement != 'yes' || $enablebonusnoad_advertisement != 'yes'))||($i == 10 && get_user_class() < 2)||($i == 11 && get_user_class() < 2))//enablead_advertisement启用广告//enablebonusnoad_advertisement魔力交换
		continue;
	print("<tr>");
	print("<form action=\"?action=exchange\"  method=\"post\">");
	print("<td class=\"rowhead_center\"><input type=\"hidden\" name=\"option\" value=\"".$i."\" /><b>".$i."</b></td>");
	if ($i==5){ //for Custom Title!
	$otheroption_title = "<input type=\"text\" name=\"title\" value=\"".$CURUSER['title']."\" style=\"width: 200px\" maxlength=\"30\" />";
	print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."<br /><br />".$lang_mybonus['text_enter_titile'].$otheroption_title.$lang_mybonus['text_click_exchange']."</td><td class=\"rowfollow\" align='center'>".number_format($bonusarray['points'])."</td>");
	}
	elseif ($i==7){  //for Give A Karma Gift
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b>".$lang_mybonus['text_username']."</b><input type=\"text\" name=\"username\" style=\"width: 200px\" maxlength=\"24\" /></td><td class=\"embedded\"><b>".$lang_mybonus['text_to_be_given']."</b><select name=\"bonusgift\" id=\"giftselect\" onchange=\"customgift();\"> <option value=\"100\"> 100</option> <option value=\"200\"> 200</option> <option value=\"300\"> 300</option> <option value=\"400\"> 400</option><option value=\"500\"> 500</option><option value=\"1000\" selected=\"selected\"> 1,000</option><option value=\"5000\"> 5,000</option><option value=\"10000\"> 10,000</option><option value=\"0\">".$lang_mybonus['text_custom']."</option></select><input type=\"text\" name=\"bonusgift\" id=\"giftcustom\" style='width: 80px' disabled=\"disabled\" />".$lang_mybonus['text_karma_points']."</td></tr><tr><td class=\"embedded\" colspan=\"2\"><b>".$lang_mybonus['text_message']."</b><input type=\"text\" name=\"message\" style=\"width: 400px\" maxlength=\"100\" /></td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."<br /><br />".$lang_mybonus['text_enter_receiver_name']."<br />$otheroption</td><td class=\"rowfollow nowrap\" align='center'>".$lang_mybonus['text_min']."100<br />".$lang_mybonus['text_max']."10,000</td>");
	}
	elseif ($i==9){  //charity giving
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\">".$lang_mybonus['text_ratio_below']."<select name=\"ratiocharity\"> <option value=\"0.1\"> 0.1</option><option value=\"0.2\"> 0.2</option><option value=\"0.3\" selected=\"selected\"> 0.3</option> <option value=\"0.4\"> 0.4</option> <option value=\"0.5\"> 0.5</option> <option value=\"0.6\"> 0.6</option><option value=\"0.7\"> 0.7</option><option value=\"0.8\"> 0.8</option></select>".$lang_mybonus['text_and_downloaded_above']." 10 GB</td><td class=\"embedded\"><b>".$lang_mybonus['text_to_be_given']."</b><select name=\"bonuscharity\" id=\"charityselect\" > <option value=\"1000\"> 1,000</option><option value=\"2000\"> 2,000</option><option value=\"3000\" selected=\"selected\"> 3000</option> <option value=\"5000\"> 5,000</option> <option value=\"8000\"> 8,000</option> <option value=\"10000\"> 10,000</option><option value=\"20000\"> 20,000</option><option value=\"50000\"> 50,000</option></select>".$lang_mybonus['text_karma_points']."</td></tr></table>";                                                                
			print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."<br /><br />".$lang_mybonus['text_select_receiver_ratio']."<br />$otheroption</td><td class=\"rowfollow nowrap\" align='center'>".$lang_mybonus['text_min']."1,000<br />".$lang_mybonus['text_max']."50,000</td>");
	}
		elseif ($i==13){  //for VIP or Upload
		print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."</td><td class=\"rowfollow\" align='center'>-".number_format($bonusarray['unpoints'])."</td>");
	}
	else{  //for VIP or Upload
		print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."</td><td class=\"rowfollow\" align='center'>".number_format($bonusarray['points'])."</td>");
	}

	if($CURUSER['seedbonus'] >= $bonusarray['points'] ||!$bonusarray['points'])
	{		if($i==13)
		{
			if($CURUSER['invites']<1)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"邀请码不足\" disabled=\"disabled\" /></td>");
			else
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}elseif ($i==7){
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_karma_gift']."\" /></td>");
		}
		elseif ($i==8){
			if ($enablenoad_advertisement == 'yes' && get_user_class() >= $noad_advertisement)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_class_above_no_ad']."\" disabled=\"disabled\" /></td>");
			elseif (strtotime($CURUSER['noaduntil']) >= TIMENOW)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_already_disabled']."\" disabled=\"disabled\" /></td>");
			elseif (get_user_class() < $bonusnoad_advertisement)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".get_user_class_name($bonusnoad_advertisement,false,false,true).$lang_mybonus['text_plus_only']."\" disabled=\"disabled\" /></td>");
			else
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
		elseif ($i==9){
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_charity_giving']."\" /></td>");
		}
		elseif($i==4)
		{
			if($invitesystem == "no")
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"邀请系统关闭\" disabled=\"disabled\" /></td>");
			elseif(get_user_class() < $buyinvite_class)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".get_user_class_name($buyinvite_class,false,false,true).$lang_mybonus['text_plus_only']."\" disabled=\"disabled\" /></td>");
			else
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
		elseif ($i==6)
		{
			if (get_user_class() >= UC_VIP)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".($CURUSER['vip_added']=='yes'?"有效期至".$until = date("Y-m-d",strtotime($CURUSER['vip_until'])):$lang_mybonus['std_class_above_vip'])."\" disabled=\"disabled\" /></td>");
			else
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
		elseif ($i==5)
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		else
		{
			if ($CURUSER['downloaded'] > 0){
				if ($CURUSER['uploaded'] > $dlamountlimit_bonus * 1073741824)//Uploaded amount reach limit
					$ratio = $CURUSER['uploaded']/$CURUSER['downloaded'];
				else $ratio = 0;
			}
			else $ratio = $ratiolimit_bonus + 1; //Ratio always above limit
			if ($ratiolimit_bonus > 0 && $ratio > $ratiolimit_bonus){
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"  onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['text_ratio_too_high']."\" disabled=\"disabled\" /></td>");
			}
			else print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"  onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
	}
	else
	{
		if ($i==10||$i==11)print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\" onclick=\"javascript:{this.disabled=true;this.form.submit()}\" value=\"".$lang_mybonus['text_more_points_needed']."\" disabled=\"disabled\" /></td>");
		elseif ($i==6&&get_user_class() >= UC_VIP)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\"    onclick=\"javascript:{this.disabled=true;this.form.submit()}\"  value=\"".($CURUSER['vip_added']=='yes'?"有效期至".$until = date("Y-m-d",strtotime($CURUSER['vip_until'])):$lang_mybonus['std_class_above_vip'])."\" disabled=\"disabled\" /></td>");
		else
		print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" name=\"btnsubmit\" onclick=\"javascript:{this.disabled=true;this.form.submit()}\" value=\"".$lang_mybonus['text_more_points_needed']."\" disabled=\"disabled\" /></td>");
	}
	print("</form>");
	print("</tr>");
	
}
print("</table><br />");
?>

<table width="940" cellpadding="3">
<tr><td class="colhead" align="center"><font class="big"><?php echo $lang_mybonus['text_what_is_karma'] ?></font></td></tr>
<tr><td class="text" align="left">
<?php
print($lang_mybonus['text_updown']);
print("<h1>".$lang_mybonus['text_get_by_seeding']."</h1>");
print("<ul>");
if ($perseeding_bonus > 0)
	print("<li>".$perseeding_bonus.$lang_mybonus['text_point'].add_s($perseeding_bonus).$lang_mybonus['text_for_seeding_torrent'].$maxseeding_bonus.$lang_mybonus['text_torrent'].add_s($maxseeding_bonus).")</li>");
print("<li>".$lang_mybonus['text_bonus_formula_one'].$tzero_bonus.$lang_mybonus['text_bonus_formula_two'].$nzero_bonus.$lang_mybonus['text_bonus_formula_three'].$bzero_bonus.$lang_mybonus['text_bonus_formula_four'].$l_bonus.$lang_mybonus['text_bonus_formula_five']."</li>");
if ($donortimes_bonus)
	print("<li>".$lang_mybonus['text_donors_always_get'].$donortimes_bonus.$lang_mybonus['text_times_of_bonus']."</li>");
print("</ul>");

		$sqrtof2 = sqrt(2);
		$logofpointone = log(0.1);
		$valueone = $logofpointone / $tzero_bonus;
		$pi = 3.141592653589793;
		$valuetwo = $bzero_bonus * ( 2 / $pi);
		$valuethree = $logofpointone / ($nzero_bonus - 1);
		$timenow = strtotime(date("Y-m-d H:i:s"));
		$sectoweek = 7*24*60*60;
		$A = 0;
		$count = 0;
		$torrentres = sql_query("select torrents.id, torrents.added, torrents.size, torrents.seeders from torrents LEFT JOIN peers ON peers.torrent = torrents.id WHERE peers.userid = $CURUSER[id] AND peers.seeder ='yes' GROUP BY torrents.id")  or sqlerr(__FILE__, __LINE__);
		while ($torrent = mysql_fetch_array($torrentres))
		{
			$weeks_alive = ($timenow - strtotime($torrent[added])) / $sectoweek;
			$gb_size = $torrent[size] / 1073741824;
			$temp = (1 - exp($valueone * $weeks_alive)) * $gb_size * (1 + $sqrtof2 * exp($valuethree * ($torrent[seeders] - 1)));
			$A += $temp;
			$count++;
		}
		if ($count > $maxseeding_bonus)
			$count = $maxseeding_bonus;
		$all_bonus = $valuetwo * atan($A / $l_bonus) + ($perseeding_bonus * $count);
		$percent = $all_bonus * 100 / ($bzero_bonus + $perseeding_bonus * $maxseeding_bonus);
	print("<div align=\"center\">".$lang_mybonus['text_you_are_currently_getting'].round($all_bonus,3).$lang_mybonus['text_point'].add_s($all_bonus).$lang_mybonus['text_per_hour']." (A = ".round($A,1).")</div><table align=\"center\" border=\"0\" width=\"400\"><tr><td class=\"loadbarbg\" style='border: none; padding: 0px;'>");

	if ($percent <= 30) $loadpic = "loadbarred";
	elseif ($percent <= 60) $loadpic = "loadbaryellow";
	else $loadpic = "loadbargreen";
	$width = $percent * 4;
	print("<img class=\"".$loadpic."\" src=\"pic/trans.gif\" style=\"width: ".$width."px;\" alt=\"".$percent."%\" /></td></tr></table>");

print("<h1>".$lang_mybonus['text_other_things_get_bonus']."</h1>");
print("<ul>");
if ($uploadtorrent_bonus > 0)
	print("<li>".$lang_mybonus['text_upload_torrent'].$uploadtorrent_bonus.$lang_mybonus['text_point'].add_s($uploadtorrent_bonus)."</li>");
if ($uploadsubtitle_bonus > 0)
	print("<li>".$lang_mybonus['text_upload_subtitle'].$uploadsubtitle_bonus.$lang_mybonus['text_point'].add_s($uploadsubtitle_bonus)."</li>");
if ($starttopic_bonus > 0)
	print("<li>".$lang_mybonus['text_start_topic'].$starttopic_bonus.$lang_mybonus['text_point'].add_s($starttopic_bonus)."</li>");
if ($makepost_bonus > 0)
	print("<li>".$lang_mybonus['text_make_post'].$makepost_bonus.$lang_mybonus['text_point'].add_s($makepost_bonus)."</li>");
if ($addcomment_bonus > 0)
	print("<li>".$lang_mybonus['text_add_comment'].$addcomment_bonus.$lang_mybonus['text_point'].add_s($addcomment_bonus)."</li>");
if ($pollvote_bonus > 0)
	print("<li>".$lang_mybonus['text_poll_vote'].$pollvote_bonus.$lang_mybonus['text_point'].add_s($pollvote_bonus)."</li>");
if ($offervote_bonus > 0)
	print("<li>".$lang_mybonus['text_offer_vote'].$offervote_bonus.$lang_mybonus['text_point'].add_s($offervote_bonus)."</li>");
if ($funboxvote_bonus > 0)
	print("<li>".$lang_mybonus['text_funbox_vote'].$funboxvote_bonus.$lang_mybonus['text_point'].add_s($funboxvote_bonus)."</li>");
if ($ratetorrent_bonus > 0)
	print("<li>".$lang_mybonus['text_rate_torrent'].$ratetorrent_bonus.$lang_mybonus['text_point'].add_s($ratetorrent_bonus)."</li>");
if ($saythanks_bonus > 0)
	print("<li>".$lang_mybonus['text_say_thanks'].$saythanks_bonus.$lang_mybonus['text_point'].add_s($saythanks_bonus)."</li>");
if ($receivethanks_bonus > 0)
	print("<li>".$lang_mybonus['text_receive_thanks'].$receivethanks_bonus.$lang_mybonus['text_point'].add_s($receivethanks_bonus)."</li>");
if ($adclickbonus_advertisement > 0)
	print("<li>".$lang_mybonus['text_click_on_ad'].$adclickbonus_advertisement.$lang_mybonus['text_point'].add_s($adclickbonus_advertisement)."</li>");
if ($prolinkpoint_bonus > 0)
	print("<li>".$lang_mybonus['text_promotion_link_clicked'].$prolinkpoint_bonus.$lang_mybonus['text_point'].add_s($prolinkpoint_bonus)."</li>");
if ($funboxreward_bonus > 0)
	print("<li>".$lang_mybonus['text_funbox_reward']."</li>");
print($lang_mybonus['text_howto_get_karma_four']);
if ($ratiolimit_bonus > 0)
	print("<li>".$lang_mybonus['text_user_with_ratio_above'].$ratiolimit_bonus.$lang_mybonus['text_and_uploaded_amount_above'].$dlamountlimit_bonus.$lang_mybonus['text_cannot_exchange_uploading']."</li>");
print($lang_mybonus['text_howto_get_karma_five'].$uploadtorrent_bonus.$lang_mybonus['text_point'].add_s($uploadtorrent_bonus).$lang_mybonus['text_howto_get_karma_six']);
?>
</td></tr></table>
<br />
<table width="940" cellpadding="3">
<tr><td class="colhead" align="center"><font class="big">魔力值历史记录(不包含种子评分)</font></td></tr>
<tr><td class="text" align="center">
<?php

//print("<h1>".$lang_mybonus['text_other_things_get_bonus']."</h1>");
$bonuscomment = ($CURUSER["bonuscomment"]);
print( "<textarea cols=\"175\" rows=\"6\" name=\"bonuscomment\" readonly=\"readonly\">".$bonuscomment."</textarea></td></tr></table>");
}


// Bonus exchange
if ($action == "exchange") {
	if ($_POST["userid"] || $_POST["points"] || $_POST["bonus"] || $_POST["art"]){
		write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is trying to cheat at bonus system",'mod');
		die($lang_mybonus['text_cheat_alert']);
	}
	$option = (int)$_POST["option"];
	$bonusarray = bonusarray($option);

	$points = $bonusarray['points'];
	$userid = $CURUSER['id'];
	$art = $bonusarray['art'];

	$bonuscomment = $CURUSER['bonuscomment'];
	$seedbonus=$CURUSER['seedbonus']-$points;

	if($CURUSER['seedbonus'] >= $points||!$points) {
		//=== trade for upload
		if($art == "traffic") {
			if ($CURUSER['uploaded'] > $dlamountlimit_bonus * 1073741824)//uploaded amount reach limit
			$ratio = $CURUSER['uploaded']/(1+$CURUSER['downloaded']);
			else $ratio = 0;
			if ($ratiolimit_bonus > 0 && $ratio > $ratiolimit_bonus)
				die($lang_mybonus['text_cheat_alert']);
			else {
			$upload = $CURUSER['uploaded'];
			$up = $upload + $bonusarray['menge'];
			$text = "花费 ".$points. " 魔力值并获取了 " .mksize($bonusarray['menge'])." 上传量";
			$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
			sql_query("UPDATE users SET uploaded = ".sqlesc($up).", seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			//header("Refresh: 0; url=mybonus.php?do=upload");
			redirect('mybonus.php?do=upload');
			}
		}
		//=== trade for one month VIP status ***note "SET class = '10'" change "10" to whatever your VIP class number is
		elseif($art == "class") {
			if (get_user_class() >= UC_VIP) {
				stdmsg($lang_mybonus['std_no_permission'],$lang_mybonus['std_class_above_vip'], 0);
				stdfoot();
				die;
			}
			$vip_until = date("Y-m-d H:i:s",(strtotime(date("Y-m-d H:i:s")) + 31*86400));
			//$bonuscomment = date("Y-m-d") . " - " .$points. " Points for 1 month VIP Status.\n" .htmlspecialchars($bonuscomment);
			$text = "花费 ".$points. " 魔力值并获取了一个月的VIP资格";
			$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
			sql_query("UPDATE users SET class = '".UC_VIP."', vip_added = 'yes', vip_until = ".sqlesc($vip_until).", seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			//header("Refresh: 0; url=mybonus.php?do=vip");
			redirect('mybonus.php?do=vip');
		}
		//=== trade for invites
		elseif($art == "invite") {
			if(get_user_class() < $buyinvite_class)
				die(get_user_class_name($buyinvite_class,false,false,true).$lang_mybonus['text_plus_only']);
			if($invitesystem == "no")
				die('邀请系统关闭');
			$invites = $CURUSER['invites'];
			$inv = $invites+$bonusarray['menge'];
			//$bonuscomment = date("Y-m-d") . " - " .$points. " Points for invites.\n" .htmlspecialchars($bonuscomment);
			$text = "花费 ".$points. " 魔力值并获取了一个邀请码";
			$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
			//sql_query("UPDATE users SET invites = ".sqlesc($inv).", seedbonus = seedbonus - $points WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			//header("Refresh: 0; url=mybonus.php?do=invite");
			$hash  = md5(mt_rand(1,10000).$CURUSER['username'].TIMENOW.$CURUSER['passhash']);
			sql_query("INSERT INTO invites (inviter, invitee, hash, time_invited) VALUES ('".mysql_real_escape_string($CURUSER[id])."', '', '".mysql_real_escape_string($hash)."', " . sqlesc(date("Y-m-d H:i:s"))  . ")");
			sql_query("UPDATE users SET   seedbonus = seedbonus - $points , bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);

			redirect('mybonus.php?do=invite');
		}
		elseif($art == "uninvite") {
			if($CURUSER['invites']<=0){
			//header("Refresh: 0; url=mybonus.php");
			redirect('mybonus.php');
				die();}
			$invites = $CURUSER['invites'];
			$inv = $invites-$bonusarray['menge'];
			//$bonuscomment = date("Y-m-d") . " -  ".$bonusarray['menge']." invites for " .$bonusarray['unpoints']. " Points .\n" .htmlspecialchars($bonuscomment);
			$text = "将 ".$bonusarray['menge']. " 邀请码兑换为了 ".$bonusarray['unpoints']." 魔力值";
			$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
			sql_query("UPDATE users SET invites = ".sqlesc($inv).", seedbonus = seedbonus + ".$bonusarray[unpoints]." ,bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			//header("Refresh: 0; url=mybonus.php?do=uninvite");
			redirect('mybonus.php?do=uninvite');
		}elseif($art == "nohr2d") {
			if(!get_row_count("snatched", "WHERE userid=".$userid." and hr='C' and torrentid=".sqlesc(0+$_POST['nohr2d'])))
			redirect('mybonus.php?do=nohr2derr');
			$text = "花费 ".$bonusarray['points']." 魔力值为 ".(0+$_POST['nohr2d']). " HR购买免罪金牌";
			$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
			sql_query("UPDATE users SET seedbonus = seedbonus - ".$bonusarray['points']." ,bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			
			sql_query("UPDATE snatched SET hr='D' WHERE userid=".$userid." and hr='C' and torrentid=".sqlesc(0+$_POST['nohr2d'])) or sqlerr(__FILE__, __LINE__);
			redirect('mybonus.php?do=nohr2d');
		}
		//=== trade for special title
		/**** the $words array are words that you DO NOT want the user to have... use to filter "bad words" & user class...
		the user class is just for show, but what the hell tongue.gif Add more or edit to your liking.
		*note if they try to use a restricted word, they will recieve the special title "I just wasted my karma" *****/
		elseif($art == "title") {
			//===custom title
			$title = $_POST["title"];
			//if($title!="")
			if($title!=$CURUSER["title"])
			{
			//$title = sqlesc($title);
			$words = array("fuck", "shit", "pussy", "cunt", "nigger", "Staff Leader","SysOp", "Administrator","Moderator","Uploader","Retiree","VIP","Nexus Master","Ultimate User","Extreme User","Veteran User","Insane User","Crazy User","Elite User","Power User","User","Peasant","Champion");
			$title = str_replace($words, $lang_mybonus['text_wasted_karma'], $title);
			//$bonuscomment = date("Y-m-d") . " - " .$points. " Points for custom title. Old title is ".htmlspecialchars(trim($CURUSER["title"]))." and new title is $title\n" .htmlspecialchars($bonuscomment);
			
			$text = "花费 ".$points. " 魔力值并将头衔".($CURUSER["title"]?" ".sqlesc(trim($CURUSER["title"]))." ":"").($title?"改为 ".sqlesc($title):"清空");
			$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
			sql_query("UPDATE users SET title = ".sqlesc($title).", seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			//header("Refresh: 0; url=mybonus.php?do=title");
			redirect('mybonus.php?do=title');
			}
			else
			//header("Refresh: 0; url=mybonus.php");
			redirect('mybonus.php');
		}
		elseif($art == "noad" && $enablead_advertisement == 'yes' && $enablebonusnoad_advertisement == 'yes') {
			if (($enablenoad_advertisement == 'yes' && get_user_class() >= $noad_advertisement) || strtotime($CURUSER['noaduntil']) >= TIMENOW || get_user_class() < $bonusnoad_advertisement)
				die($lang_mybonus['text_cheat_alert']);
			else{
				$noaduntil = date("Y-m-d H:i:s",(TIMENOW + $bonusarray['menge']));
				//$bonuscomment = date("Y-m-d") . " - " .$points. " Points for ".$bonusnoadtime_advertisement." days without ads.\n" .htmlspecialchars($bonuscomment);
				$text = "花费 ".$points. " 魔力值关闭广告 $bonusnoadtime_advertisement 天";
				$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
				sql_query("UPDATE users SET noad='yes', noaduntil='".$noaduntil."', seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id=".sqlesc($userid));
				//header("Refresh: 0; url=mybonus.php?do=noad");
				redirect('mybonus.php?do=noad');
			}
		}
		elseif($art == 'gift_2') // charity giving
		{
			$points = 0+$_POST["bonuscharity"];
			if ($points < 1000 || $points > 50000){
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['bonus_amount_not_allowed_two'], 0);
				stdfoot();
				die();
			}
			$ratiocharity = 0.0+$_POST["ratiocharity"];
			if ($ratiocharity < 0.1 || $ratiocharity > 0.8){
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['bonus_ratio_not_allowed']);
				stdfoot();
				die();
			}
			if($CURUSER['seedbonus'] >= $points) {
				$points2= number_format($points,1);
				//$bonuscomment = date("Y-m-d") . " - " .$points2. " Points as charity to users with ratio below ".htmlspecialchars(trim($ratiocharity)).".\n" .htmlspecialchars($bonuscomment);
				$text = "将 ".$points. " 魔力值捐赠给了分享率在 ".$ratiocharity." 以下的用户";
				$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
				$charityReceiverCount = get_row_count("users", "WHERE enabled='yes' AND 10737418240 < downloaded AND $ratiocharity > uploaded/downloaded");
				if ($charityReceiverCount) {
					sql_query("UPDATE users SET seedbonus = seedbonus - $points, charity = charity + $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
					$charityPerUser = $points/$charityReceiverCount;
					sql_query("UPDATE users SET seedbonus = seedbonus + $charityPerUser WHERE enabled='yes' AND 10737418240 < downloaded AND $ratiocharity > uploaded/downloaded") or sqlerr(__FILE__, __LINE__);
					//header("Refresh: 0; url=mybonus.php?do=charity");
					redirect('mybonus.php?do=charity');
				}
				else
				{
					stdmsg($lang_mybonus['std_sorry'], $lang_mybonus['std_no_users_need_charity']);
					stdfoot();
					die;
				}
			}
		}
		elseif($art == "gift_1" && $bonusgift_bonus == 'yes') {
			//=== trade for giving the gift of karma
			$points = 0+$_POST["bonusgift"];
			$message = $_POST["message"];
			//==gift for peeps with no more options
			$usernamegift = sqlesc(trim($_POST["username"]));
			$res = sql_query("SELECT id, bonuscomment FROM users WHERE username=" . $usernamegift);
			$arr = mysql_fetch_assoc($res);
			$useridgift = $arr['id'];
			$userseedbonus = $arr['seedbonus'];
			$receiverbonuscomment = $arr['bonuscomment'];
			if ($points < 100 || $points > 10000) {
				//write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is hacking bonus system",'mod');
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['bonus_amount_not_allowed']);
				stdfoot();
				die();
			}
			if($CURUSER['seedbonus'] >= $points) {
				$points2= number_format($points,1);
			$text = "将 ".$points. " 魔力值作为礼物赠给了 ".$_POST["username"];
			$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
				//$bonuscomment = date("Y-m-d") . " - " .$points2. " Points as gift to ".htmlspecialchars(trim($_POST["username"])).".\n" .htmlspecialchars($bonuscomment);

				$aftertaxpoint = $points;
				if ($taxpercentage_bonus)
					$aftertaxpoint -= $aftertaxpoint * $taxpercentage_bonus * 0.01;
				if ($basictax_bonus)
					$aftertaxpoint -= $basictax_bonus;

				$points2receiver = number_format($aftertaxpoint,1);
				//$newreceiverbonuscomment = date("Y-m-d") . " + " .$points2receiver. " Points (after tax) as a gift from ".($CURUSER["username"]).".\n" .htmlspecialchars($receiverbonuscomment);
				$text = "从 ".$CURUSER["username"]. " 获得了 ".$aftertaxpoint." 魔力值(税后)";
				$newreceiverbonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$receiverbonuscomment;
				if ($userid==$useridgift){
					stdmsg($lang_mybonus['text_huh'], $lang_mybonus['text_karma_self_giving_warning'], 0);
					stdfoot();
					die;
				}
				if ($aftertaxpoint<=0){
					stdmsg($lang_mybonus['text_error'], "税后魔力值小于0", 0);
					stdfoot();
					die;
				}
				
				if (!$useridgift){
					stdmsg($lang_mybonus['text_error'], $lang_mybonus['text_receiver_not_exists'], 0);
					stdfoot();
					die;
				}

				sql_query("UPDATE users SET seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
				sql_query("UPDATE users SET seedbonus = seedbonus + $aftertaxpoint, bonuscomment = ".sqlesc($newreceiverbonuscomment)." WHERE id = ".sqlesc($useridgift));

				//===send message
				$subject = sqlesc($lang_mybonus_target[get_user_lang($useridgift)]['msg_someone_loves_you']);
				$added = sqlesc(date("Y-m-d H:i:s"));
				$msg = $lang_mybonus_target[get_user_lang($useridgift)]['msg_you_have_been_given'].$points2.$lang_mybonus_target[get_user_lang($useridgift)]['msg_after_tax'].$points2receiver.$lang_mybonus_target[get_user_lang($useridgift)]['msg_karma_points_by'].$CURUSER['username'];
				if ($message)
					$msg .= "\n".$lang_mybonus_target[get_user_lang($useridgift)]['msg_personal_message_from'].$CURUSER['username'].$lang_mybonus_target[get_user_lang($useridgift)]['msg_colon'].$message;
				$msg = sqlesc($msg);
				sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES(0, $subject, $useridgift, $msg, $added)") or sqlerr(__FILE__, __LINE__);
				$usernamegift = unesc($_POST["username"]);
				//header("Refresh: 0; url=mybonus.php?do=transfer");
				redirect('mybonus.php?do=transfer');
			}
			else{
				print("<table width=\"940\"><tr><td class=\"colhead\" align=\"left\" colspan=\"2\"><h1>".$lang_mybonus['text_oups']."</h1></td></tr>");
				print("<tr><td align=\"left\"></td><td align=\"left\">".$lang_mybonus['text_not_enough_karma']."<br /><br /></td></tr></table>");
			}
		}
				elseif($art == 'namecolour') // charity giving
		{	if(get_user_class() < 1)
				die(get_user_class_name(1,false,false,true).$lang_mybonus['text_plus_only']);
			$colour = $_POST["chcol"];
			if("#".$colour!=$CURUSER['namecolour']&&!preg_match("/[^0-9a-f]/i",$colour)){
			if($colour=="FFFFFF")$colour=("");
			else $colour = ("#".$colour);
			//$bonuscomment = date("Y-m-d") . " - " .$points. " Points for chcol is $colour\n" .htmlspecialchars($bonuscomment);
			$text = "花费 ".$points. " 魔力值将头衔颜色 ".$CURUSER['namecolour'].($colour?" 改为 $colour":" 清空");
			$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
			sql_query("UPDATE users SET namecolour =". sqlesc($colour).", seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			//header("Refresh: 0; url=mybonus.php?do=namecolour");
			redirect('mybonus.php?do=namecolour');
			}
			else
			//header("Refresh: 0; url=mybonus.php");
			redirect('mybonus.php');
			
		}elseif($art == 'namechange') // charity giving
		{	if(get_user_class() < 2)
				die(get_user_class_name(2,false,false,true).$lang_mybonus['text_plus_only']);
			$newname = trim($_POST["newname"]);
			if($newname!=""){
			 if (!check_username($newname)){//header("Refresh: 0; url=mybonus.php?do=namechangeerr");
			 redirect('mybonus.php?do=namechangeerr');die;}
			 $newname = sqlesc($newname);
			if(get_row_count("users", "WHERE username=".$newname)){
			//header("Refresh: 0; url=mybonus.php?do=namechangeerrsame");
			redirect('mybonus.php?do=namechangeerrsame');die;}

			write_log("UID为".$CURUSER["id"]."的用户[" . $CURUSER["username"] . "]使用转生羽毛将账户名改为[".$_POST["newname"]."]");
			//$bonuscomment = date("Y-m-d") . " - " .$points. " Points change  name from ".sqlesc($CURUSER['username'])." to $newname\n" .htmlspecialchars($bonuscomment);
			$text = "花费 ".$points. " 魔力值将用户名从 ".sqlesc($CURUSER['username'])." 改为 $newname";
			$bonuscomment = date("Y-m-d") . " - BONUS SYSTEM - " .$text. " \n" .$bonuscomment;
			sql_query("UPDATE users SET username = $newname, seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			//header("Refresh: 0; url=mybonus.php?do=namechange");
			redirect('mybonus.php?do=namechange');
			}
			else
			//header("Refresh: 0; url=mybonus.php");
			redirect('mybonus.php');
			
		}elseif($art == 'takeflush') // charity giving
		{
			//KPS("-",$points,$CURUSER['id']);
			//header("Refresh: 0; url=takeflush.php");
			redirect('takeflush.php');
			
			
			
		}
	}
	else //header("Refresh: 0; url=mybonus.php");
	redirect('mybonus.php');
}
//redirect("" . get_protocol_prefix() . "$BASEURL/
//header("Refresh: 0; url=
stdfoot();
?>
