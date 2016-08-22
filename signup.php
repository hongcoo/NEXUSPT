<?php
require_once("include/bittorrent.php");
dbconn();

$langid = 0 + $_GET['sitelanguage'];
if ($langid)
{
	$lang_folder = validlang($langid);
	if(get_langfolder_cookie() != $lang_folder)
	{
		set_langfolder_cookie($lang_folder);
		header("Location: " . $_SERVER['REQUEST_URI']);
	}
}
require_once(get_langfile_path("", false, $CURLANGDIR));
cur_user_check ();
$type = $_GET['type'];
//if(cookietureuserid(true))stderr('错误','您已有帐号: <b>'.cookietureuserid(true).'</b> ,请不要重复注册,如忘记密码请使用密码找回功能',false);
if ($type == 'invite'){
	registration_check("invitesystem", true, true);
	failedloginscheck ("Invite signup");
	$code = str_replace(" ", "",$_POST["invitenumber"]);
	$nuIP = getip();

	/*$dom = @gethostbyaddr($nuIP);
	if ($dom == $nuIP || @gethostbyname($dom) != $nuIP)
	$dom = "";
	else
	{
	$dom = strtoupper($dom);
	preg_match('/^(.+)\.([A-Z]{2,3})$/', $dom, $tldm);
	$dom = $tldm[2];
	}*/
	$dom = "";

	$sq = sprintf("SELECT inviter ,invitee  FROM invites LEFT JOIN users ON users.id = invites.inviter WHERE users.enabled =  'yes' and invites.hash ='%s'",mysql_real_escape_string($code));
	$res = sql_query($sq) or sqlerr(__FILE__, __LINE__);
	$inv = mysql_fetch_assoc($res);
	$inviter = htmlspecialchars($inv["inviter"]);
	$inviteremail = htmlspecialchars($inv["invitee"]);
	
	/*if(!$code){
	stdhead($lang_signup['head_invite_signup']);
	
	
	$czyhash=get_single_value("invites", "hash", "WHERE  inviter = $MASTERUSERID and sent = 0 ORDER BY time_invited ASC ");
	if(!$czyhash)$czyhash="请输入邀请码";
	
	
	?>	
	
	<p>
<table border="1" cellspacing="0" cellpadding="10"> 

<tr><td>正常注册</td> 
<td><form method="get" action="signup.php"> 
可能会有邮箱限制或者无法自由注册</td><td><input type=submit value=<?php	echo $lang_signup['submit_sign_up'] ?>></td></form></tr> 

<tr><td>邀请码注册</td> 
<td><form method="get" action="signup.php"> 
<input type="hidden" name="type" value="invite"> <input type="text" name="invitenumber" size="67" value="<?php echo $czyhash?>" onfocus="this.value=''"></td><td><input type=submit value=<?php	echo $lang_signup['submit_sign_up'] ?>></td></form></tr> 

<tr><td>教务注册</td> 
<td><form method="get" action="signup.php"> 
<input type="hidden" name="type" value="dean"> <input type="text" name="deannumber" size="30" value="教务账号" onfocus="this.value=''">
<input type="password" name="deanpassword" size="30" value="教务密码" onfocus="this.value=''"></td><td><input type=submit value=<?php	echo $lang_signup['submit_sign_up'] ?>></td></form></tr> 


</table> 

	



		
		
<?php			//stderr($lang_signup['std_error'], $lang_signup['std_uninvited'], 0);
		stdfoot();
		die;
		}else*/if (!$inv) stderr($lang_signup['std_error'], $lang_signup['std_uninvited'], 0);
		
	stdhead($lang_signup['head_invite_signup']);
	
}elseif($type == 'dean'){
	failedloginscheck ("Signup");
	registration_check("dean", true, true);
	$deannumber  =  $_POST["deannumber"];
	$deanpassword  = $_POST["deanpassword"];
	$deantype  =0+$_GET["deantype"];
	
	
dean_check($deannumber,$deanpassword,$deantype);

$deanusernameid = get_single_value("users","id","WHERE deancheck=".sqlesc($deannumber));
if ($deanusernameid)
  stderr($lang_signup['std_error'],$deannumber." 已被用户 ".get_username($deanusernameid)." 验证",false);
  
  
	stdhead("教务注册");
	
}elseif($type == 'normal'){
	registration_check("normal", true, true);
	failedloginscheck ("Signup");
	stdhead($lang_signup['head_signup']);
	
}else{
	stdhead("注册方式选择");
	
	?>	
	
	<p>
<table border="1" cellspacing="0" cellpadding="10"> 

<?if($registration != "no"){?>
<tr><form method="post" action="signup.php?type=normal">
<td>正常注册</td> 
<td> 可能会有邮箱限制或者无法自由注册</td>
<td><input type=submit value=<?php	echo $lang_signup['submit_sign_up'] ?>></td>
</form></tr>
<?}?>

<?if($invitesystem != "no"){?>
<tr><form method="post" action="signup.php?type=invite"> 
<td>邀请码注册</td> 
<td><input type="text" name="invitenumber" size="67" value="请输入邀请码" onfocus="this.value=''"></td>
<td><input type=submit value=<?php	echo $lang_signup['submit_sign_up'] ?>></td>
</form></tr>
<?}?>

<?if($registration != "no"||$invitesystem != "no"){?>
<tr><td colspan="3" align="center">你还有 <b><?php echo remaining ();?></b> 次机会尝试<b>下列方式</b>进行注册,连续注册将导致你的IP地址被禁用!</p></td></tr>
<tr><form method="post" action="signup.php?type=dean&deantype=1"> 
<td>本科生教务注册</td> 
<td><input type="text" name="deannumber" size="30" value="学生学号" onfocus="this.value=''"><input type="text" name="deanpassword" size="30" value="教务密码" onfocus="this.value=''"></td>
<td><input type=submit value=<?php	echo $lang_signup['submit_sign_up'] ?>></td>
</form></tr>
<!--
<tr><form method="post" action="signup.php?type=dean&deantype=2"> 
<td>研究生教务注册</td> 
<td><input type="text" name="deannumber" size="30" value="学生学号" onfocus="this.value=''"><input type="text" name="deanpassword" size="30" value="教务密码" onfocus="this.value=''"></td>
<td><input type=submit disabled value='敬请期待'></td>
</form></tr>
-->

<tr><form method="post" action="signup.php?type=dean&deantype=3"> 
<td>上网帐号注册</td> 
<td><input type="text" name="deannumber" size="30" value="上网帐号(无后缀)" onfocus="this.value=''"><input type="text" name="deanpassword" size="30" value="上网密码" onfocus="this.value=''"></td>
<td><input type=submit value=<?php	echo $lang_signup['submit_sign_up'] ?>></td>
</form></tr>
<?}?>

</table> 

	



		
		
<?php			//stderr($lang_signup['std_error'], $lang_signup['std_uninvited'], 0);
		stdfoot();
		die;
}
/*
$s = "<select name=\"sitelanguage\" onchange='submit()'>\n";

$langs = langlist("site_lang");

foreach ($langs as $row)
{
	if ($row["site_lang_folder"] == get_langfolder_cookie()) $se = " selected"; else $se = "";
	$s .= "<option value=". $row["id"] . $se. ">" . htmlspecialchars($row["lang_name"]) . "</option>\n";
}
$s .= "\n</select>";
?>
<form method="get" action=<?php echo $_SERVER['PHP_SELF'] ?>>
<?php
if ($type == 'invite')
print("<input type=hidden name=type value='invite'><input type=hidden name=invitenumber value='".$code."'>");
elseif ($type == 'dean')
print("<input type=hidden name=type value='dean'><input type=hidden name=deannumber value='".$_GET["deannumber"]."'><input type=hidden name=deanpassword value='".$_GET["deanpassword"]."'>");
print("<div align=right valign=top>".$lang_signup['text_select_lang']. $s . "</div>");
?>
</form>

<? */ ?>
<p>
<form method="post" action="takesignup.php">

<table border="1" cellspacing="0" cellpadding="10">
<?php if ($type == 'invite'){
 print("<input type=\"hidden\" name=\"inviter\" value=\"".$inviter."\"><input type=hidden name=type value='invite'><input type=hidden name=hash value=$code>");
tr("邀请者:", get_plain_username($inviter)
);
}elseif($type == 'dean')
print("<input type=hidden name=type value='dean'><input type=hidden name=deannumber value='".$deannumber."'><input type=hidden name=deanpassword value='".$deanpassword."'><input type=hidden name=deantype value='".$deantype."'>");

?>
<?php
//print("<tr><td class=text align=center colspan=2>".$lang_signup['text_cookies_note']."</td></tr>");
?>
<tr><td class=rowhead><?php echo $lang_signup['row_desired_username'] ?></td><td class=rowfollow align=left><input type="text" style="width: 200px" name="wantusername" value="<? echo cookietureuserid(true);?>"/><br />
<font class=small><?php echo $lang_signup['text_allowed_characters'] ?></font></td></tr>
<tr><td class=rowhead><?php echo $lang_signup['row_pick_a_password'] ?></td><td class=rowfollow align=left><input type="password" style="width: 200px" name="wantpassword" /><br />
	<font class=small><?php echo $lang_signup['text_minimum_six_characters'] ?></font></td></tr>
<tr><td class=rowhead><?php echo $lang_signup['row_enter_password_again'] ?></td><td class=rowfollow align=left><input type="password" style="width: 200px" name="passagain" /></td></tr>

<tr><td class=rowhead><?php echo $lang_signup['row_email_address'] ?></td><td class=rowfollow align=left><input type="text" style="width: 200px" name="email" value='<?echo $inviteremail;?>' />
<table width=250 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font class=small><?php echo ($restrictemaildomain == 'yes'&&($type != 'invite'&&$type != 'dean') ? $lang_signup['text_email_note'].allowedemails(): "") ?></td></tr>
</font></td></tr></table>
</td></tr>

<?php 
/*$countries = "<option value=\"8\">---- ".$lang_signup['select_none_selected']." ----</option>n";
$ct_r = sql_query("SELECT id,name FROM countries ORDER BY name") or die;
while ($ct_a = mysql_fetch_array($ct_r))
$countries .= "<option value=$ct_a[id]" . ($ct_a['id'] == 8 ? " selected" : "") . ">$ct_a[name]</option>n";
tr($lang_signup['row_country'], "<select name=country>n$countries</select>", 1); 
*/
print("<input type=\"hidden\" name=\"country\" value=\"8\" />");

//School select
if ($showschool == 'yes'){
$schools = "<option value=0  selected>---- 可选 ----</option>n";
$sc_r = sql_query("SELECT id,name FROM schools ORDER BY name") or die;
while ($sc_a = mysql_fetch_array($sc_r))
$schools .= "<option value=$sc_a[id]" . ($sc_a['id'] == 75 ? " selected" : "") . ">$sc_a[name]</option>n";
tr($lang_signup['row_school'], "<select name=school>$schools</select>", 1);
}
?>
<tr><td class=rowhead><?php echo $lang_signup['row_gender'] ?></td><td class=rowfollow align=left>
<input type=radio name=gender value=Male><?php echo $lang_signup['radio_male'] ?><input type=radio name=gender value=Female><?php echo $lang_signup['radio_female'] ?><input type=radio name=gender value='N/A' checked><?php echo $lang_signup['radio_na'] ?></td></tr>

<?php
show_image_code ();
?>
<tr>

<td class=rowhead><?php echo $lang_signup['row_verification'] ?></td>

<td class=rowfollow align=left>
<input type=checkbox name=rulesverify value=yes><?php echo $lang_signup['checkbox_read_rules'] ?><br />
<input type=checkbox name=faqverify value=yes><?php echo $lang_signup['checkbox_read_faq'] ?> <br />
<input type=checkbox name=ageverify value=yes><?php echo $lang_signup['checkbox_age'] ?>
</td></tr>
<tr><td class=toolbox colspan="2" align="center"><font color=red><b>
<?php //echo $lang_signup['text_all_fields_required'] ?></b></font><input type="button" onclick="javascript:{this.disabled=true;this.form.submit()}"  value=<?php echo $lang_signup['submit_sign_up'] ?> style='height: 25px'></td></tr>
</table>
</form>
<?php
stdfoot();
?>