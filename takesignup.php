<?php
require_once("include/bittorrent.php");
dbconn();
cur_user_check ();
require_once(get_langfile_path("",true));
require_once(get_langfile_path("", false, get_langfolder_cookie()));

function bark($msg) {
	global $lang_takesignup;
	stdhead();
	stdmsg($lang_takesignup['std_signup_failed'], $msg);
	stdfoot();
	exit;
}


$agent = $_SERVER["HTTP_USER_AGENT"];
	if (!(preg_match("/^Mozilla/", $agent) || preg_match("/^Opera/", $agent) || preg_match("/^Links/", $agent) || preg_match("/^Lynx/", $agent) ))die;
	
	
$type = $_POST['type'];
if ($type == 'dean'){
registration_check("dean", true, false);
failedloginscheck ("Dean Signup");
if ($iv == "yes"||$iv == "op")
	check_code ($_POST['imagehash'], $_POST['imagestring']);
}
elseif ($type == 'invite'){
registration_check("invitesystem", true, false);
failedloginscheck ("Invite Signup");
if ($iv == "yes"||$iv == "op")
	check_code ($_POST['imagehash'], $_POST['imagestring']);
}
else{
registration_check("normal", true, true);
failedloginscheck ("Signup");
if ($iv == "yes"||$iv == "op")
	check_code ($_POST['imagehash'], $_POST['imagestring']);
}

function isportopen($port)
{
	$sd = @fsockopen($_SERVER["REMOTE_ADDR"], $port, $errno, $errstr, 1);
	if ($sd)
	{
		fclose($sd);
		return true;
	}
	else
		return false;
}

function isproxy()
{
	$ports = array(80, 88, 1075, 1080, 1180, 1182, 2282, 3128, 3332, 5490, 6588, 7033, 7441, 8000, 8080, 8085, 8090, 8095, 8100, 8105, 8110, 8888, 22788);
	for ($i = 0; $i < count($ports); ++$i)
		if (isportopen($ports[$i])) return true;
	return false;
}
if ($type=='invite')
{
$inviter =  $_POST["inviter"];
	int_check($inviter);
$code = unesc($_POST["hash"]);
$ip = getip();

$res = sql_query("SELECT username FROM users WHERE id = $inviter") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);
$invusername = $arr[username];


$sq = sprintf("SELECT inviter FROM invites LEFT JOIN users ON users.id = invites.inviter WHERE users.enabled =  'yes' and invites.hash ='%s'",mysql_real_escape_string($code));
$res = sql_query($sq) or sqlerr(__FILE__, __LINE__);
$inv = mysql_fetch_assoc($res);

if($inviter!=$inv['inviter']||!$inv)
bark("邀请码错误");

if($inviter==$MASTERUSERID)$inviter=$ROBOTUSERID;
}elseif ($type=='dean')
{
$deannumber =  $_POST["deannumber"];
$deanpassword =  $_POST["deanpassword"];
$deantype = 0+$_POST["deantype"];

dean_check($deannumber,$deanpassword,$deantype);

$a = (@mysql_fetch_row(@sql_query("select count(*) from users where deancheck='".mysql_real_escape_string($deannumber)."'"))) or sqlerr(__FILE__, __LINE__);
if ($a[0] != 0){
$emailusername = get_single_value("users","username","WHERE deancheck='".mysql_real_escape_string($deannumber)."'");
  bark($deannumber." 已被用户 ".$emailusername." 验证");
  
 } 

 
//bark("TEST OK");
}

if (!mkglobal("wantusername:wantpassword:passagain:email"))
	die();

$email = htmlspecialchars(trim($email));
$email = safe_email($email);
if (!check_email($email))
	bark($lang_takesignup['std_invalid_email_address']);
	
if(EmailBanned($email))
    bark($lang_takesignup['std_email_address_banned']);

if(!EmailAllowed($email)&& ($type != 'invite'&&$type != 'dean'))
    bark($lang_takesignup['std_wrong_email_address_domains'].allowedemails());

$country = $_POST["country"];
	int_check($country);

if ($showschool == 'yes'){
$school =0+ $_POST["school"];
//int_check($school);
}

$gender =  htmlspecialchars(trim($_POST["gender"])); 
$allowed_genders = array("Male","Female","male","female","N/A","n/a");
if (!in_array($gender, $allowed_genders, true))
	bark($lang_takesignup['std_invalid_gender']);
	
if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($country) || empty($gender))
	bark($lang_takesignup['std_blank_field']);

	
//if (strlen($wantusername) > 12)
if (mb_strlen($wantusername,'gbk')> 12)
	bark($lang_takesignup['std_username_too_long']);
	
	if (mb_strlen($wantusername,'gbk')< 4)
	bark("用户名太短");

if ($wantpassword != $passagain)
	bark($lang_takesignup['std_passwords_unmatched']);

if (strlen($wantpassword) < 6)
	bark($lang_takesignup['std_password_too_short']);

if (strlen($wantpassword) > 40)
	bark($lang_takesignup['std_password_too_long']);

if ($wantpassword == $wantusername)
	bark($lang_takesignup['std_password_equals_username']);

if (!validemail($email))
	bark($lang_takesignup['std_wrong_email_address_format']);

if (!check_username($wantusername))
	bark($lang_takesignup['std_invalid_username']);
	
// make sure user agrees to everything...
if ($_POST["rulesverify"] != "yes" || $_POST["faqverify"] != "yes" || $_POST["ageverify"] != "yes")
	stderr($lang_takesignup['std_signup_failed'], $lang_takesignup['std_unqualified']);

// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("select count(*) from users where email='".mysql_real_escape_string($email)."'"))) or sqlerr(__FILE__, __LINE__);
if ($a[0] != 0){
$emailusername = get_single_value("users","username","WHERE email='".mysql_real_escape_string($email)."'");
  bark($lang_takesignup['std_email_address'].$email.$lang_takesignup['std_in_use']."(".$emailusername.")");
 } 
/*
// do simple proxy check
if (isproxy())
	bark("You appear to be connecting through a proxy server. Your organization or ISP may use a transparent caching HTTP proxy. Please try and access the site on <a href="." . get_protocol_prefix() . "$BASEURL.":81/signup.php>port 81</a> (this should bypass the proxy server). <p><b>Note:</b> if you run an Internet-accessible web server on the local machine you need to shut it down until the sign-up is complete.");

$res = sql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
*/

$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);
$editsecret = ($verification == 'admin' ? '' : $secret);
$invite_count = (int) $invite_count;

$wantusername = sqlesc($wantusername);
$wantpasshash = sqlesc($wantpasshash);
$secret = sqlesc($secret);
$editsecret = sqlesc($editsecret);
$send_email = $email;
$email = sqlesc($email);
$country = sqlesc($country);
$gender = sqlesc($gender);
$sitelangid = sqlesc(get_langid_from_langcookie());
$ip = sqlesc(getip());
$deannumber = sqlesc($deannumber);
$res_check_user = sql_query("SELECT * FROM users WHERE username = " . $wantusername);
 
if(mysql_num_rows($res_check_user) == 1)
  bark($lang_takesignup['std_username_exists']);

$ret = sql_query("INSERT INTO users (username, passhash, secret, editsecret, email, country, gender, status, class, invites, ".($type == 'invite' ? "invited_by," : "")." added, last_access, lang, stylesheet".($showschool == 'yes' ? ", school" : "").", uploaded , ".($type == 'dean' ? "deancheck," : "")."ip) VALUES (" . $wantusername . "," . $wantpasshash . "," . $secret . "," . $editsecret . "," . $email . "," . $country . "," . $gender . ", 'pending', ".$defaultclass_class.",". $invite_count .", ".($type == 'invite' ? "'$inviter'," : "") ." '". date("Y-m-d H:i:s") ."' , " . " '". date("Y-m-d H:i:s") ."' , ".$sitelangid . ",".$defcss.($showschool == 'yes' ? ",".$school : "").",".($iniupload_main > 0 ? $iniupload_main : 0).",".($type == 'dean' ? "$deannumber," : "")."".$ip.")") or sqlerr(__FILE__, __LINE__);
$id = mysql_insert_id();
$dt = sqlesc(date("Y-m-d H:i:s"));
$subject = sqlesc($lang_takesignup['msg_subject'].$SITENAME."!");
$msg = sqlesc($lang_takesignup['msg_congratulations'].htmlspecialchars($wantusername).$lang_takesignup['msg_you_are_a_member']);
sql_query("INSERT INTO messages (sender, receiver, subject, added, msg) VALUES(0, $id, $subject, $dt, $msg)") or sqlerr(__FILE__, __LINE__);

//write_log("User account $id ($wantusername) was created");
$res = sql_query("SELECT passhash, secret, editsecret, status FROM users WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);
$psecret = md5($row['secret']);
$ip = getip();
$usern = htmlspecialchars($wantusername);
$title = $SITENAME.$lang_takesignup['mail_title'];
$body = <<<EOD
{$lang_takesignup['mail_one']}$usern{$lang_takesignup['mail_two']}($email){$lang_takesignup['mail_three']}{$lang_takesignup['mail_four']}
<br />http://$BASEURL/confirm.php?id=$id&secret=$psecret
{$lang_takesignup['mail_four_1']}
<br />
http://$BASEURL/confirm_resend.php
<br />
{$lang_takesignup['mail_five']}
<br />
IP:{$ip}
EOD;


if ($type == 'invite')
{
//don't forget to delete confirmed invitee's hash code from table invites
sql_query("DELETE FROM invites WHERE hash = '".mysql_real_escape_string($code)."'");
$dt = sqlesc(date("Y-m-d H:i:s"));
$subject = sqlesc($lang_takesignup_target[get_user_lang($inviter)]['msg_invited_user_has_registered']);
$msg = sqlesc($lang_takesignup_target[get_user_lang($inviter)]['msg_user_you_invited'].$usern.$lang_takesignup_target[get_user_lang($inviter)]['msg_has_registered']);
//sql_query("UPDATE users SET uploaded = uploaded + 10737418240 WHERE id = $inviter"); //add 10GB to invitor's uploading credit
sql_query("INSERT INTO messages (sender, receiver, subject, added, msg) VALUES(0, $inviter, $subject, $dt, $msg)") or sqlerr(__FILE__, __LINE__);
$Cache->delete_value('user_'.$inviter.'_unread_message_count');
$Cache->delete_value('user_'.$inviter.'_inbox_count');
}


if ($verification == 'admin'){
	if ($type == 'invite')
	header("Location: " . get_protocol_prefix() . "$BASEURL/ok.php?type=inviter");
	else
	header("Location: " . get_protocol_prefix() . "$BASEURL/ok.php?type=adminactivate");
}
elseif ($verification == 'automatic' || $smtptype == 'none'){
	header("Location: " . get_protocol_prefix() . "$BASEURL/confirm.php?id=$id&secret=$psecret");
}
else{
	
	sent_mail($send_email,$SITENAME,$SITEEMAIL,change_email_encode(get_langfolder_cookie(), $title),change_email_encode(get_langfolder_cookie(),$body),"signup",false,false,'',get_email_encode(get_langfolder_cookie()));
	
	header("Location: " . get_protocol_prefix() . "$BASEURL/ok.php?type=signup&email=" . rawurlencode($send_email));
}

if($code)sql_query("delete from shoutbox  where type='hb' and text like  '%".mysql_real_escape_string($code)."%' ");
if($send_email)sql_query("delete from shoutbox  where type='hb' and text like  ".sqlesc('%'.$send_email.'%'));
?>
