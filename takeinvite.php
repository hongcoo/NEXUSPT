<?php
require_once("include/bittorrent.php");
dbconn();
//checkloggedinorreturn();
require_once(get_langfile_path());
registration_check('invitesystem', true, false);


function bark($msg) {
  stdhead();
	stdmsg($lang_takeinvite['head_invitation_failed'], $msg);
  stdfoot();
  exit;
}

$id = $CURUSER[id];

if($_POST["hash"])$sendthis=safe_email(unesc(htmlspecialchars(trim($_POST["hash"]))));
	else $sendthis=0;

$email = unesc(htmlspecialchars(trim($_POST["email"])));
$email = safe_email($email);



if($sendthis){
if (get_user_class() < $sendinvite_class)
stderr($lang_takeinvite['std_error'],$lang_takeinvite['std_invite_denied']);

if (!$email)
    bark($lang_takeinvite['std_must_enter_email']);
if (!check_email($email))
	bark($lang_takeinvite['std_invalid_email_address']);
if(EmailBanned($email))
    bark($lang_takeinvite['std_email_address_banned']);

//if(!EmailAllowed($email))
  //  bark($lang_takeinvite['std_wrong_email_address_domains'].allowedemails());

$body = str_replace("<br />", "<br />", nl2br(trim(strip_tags($_POST["body"]))));
if(!$body)
	bark($lang_takeinvite['std_must_enter_personal_message']);


// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("select count(*) from users where email=".sqlesc($email)))) or die(mysql_error());
if ($a[0] != 0)
  bark($lang_takeinvite['std_email_address'].htmlspecialchars($email).$lang_takeinvite['std_is_in_use']);
$b = (@mysql_fetch_row(@sql_query("select count(*) from invites where invitee=".sqlesc($email)))) or die(mysql_error());
if ($b[0] != 0)
  bark($lang_takeinvite['std_invitation_already_sent_to'].htmlspecialchars($email).$lang_takeinvite['std_await_user_registeration']);
  

 
$d = (@mysql_fetch_row(@sql_query("select count(*) from invites where HASH=".sqlesc($sendthis)))) or die(mysql_error());
if ($d[0] != 1)bark("ERROR HASH");
 
$c = mysql_fetch_assoc(sql_query("select sent,time_invited from invites where HASH=".sqlesc($sendthis))) or die(mysql_error());
if ($c['sent'])bark($sendthis.$lang_takeinvite['std_await_user_registeration']);





////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$ret = sql_query("SELECT username FROM users WHERE id = ".sqlesc($id)) or sqlerr();
$arr = mysql_fetch_assoc($ret); 

$title = $SITENAME.$lang_takeinvite['mail_tilte'];
$invite_timeout=mkprettytime(strtotime($c[time_invited])+$invite_timeout*24*60*60-TIMENOW);
$message = <<<EOD
{$lang_takeinvite['mail_one']}{$arr[username]}{$lang_takeinvite['mail_two']}<b>$sendthis</b><br />
{$lang_takeinvite['mail_three']}$invite_timeout{$lang_takeinvite['mail_four']}{$arr[username]}{$lang_takeinvite['mail_five']}<br />
$body
<br /><br />{$lang_takeinvite['mail_six']}
EOD;


sent_mail($email,$SITENAME,$SITEEMAIL,change_email_encode(get_langfolder_cookie(), $title),change_email_encode(get_langfolder_cookie(),$message),"invitesignup",false,false,'',get_email_encode(get_langfolder_cookie()));
//this email is sent only when someone give out an invitation


sql_query("UPDATE invites SET invitee = ".sqlesc($email)." ,SENT = 1  WHERE hash=".sqlesc($sendthis)) or sqlerr(__FILE__, __LINE__);

if($email)sql_query("delete from shoutbox  where type='hb' and userid=0 and text like  ".sqlesc('%'.$email.'%'));

header("Refresh: 0; url=invite.php?id=".htmlspecialchars($id)."&sent=$sendthis");

  
  
}ELSE {
if (get_user_class() < $exchangeinvite_class)
stderr($lang_takeinvite['std_error'],$lang_takeinvite['std_invite_denied']);

if ($CURUSER['invites'] < 1)
	stderr($lang_takeinvite['std_error'],$lang_takeinvite['std_no_invite']);  

$hash  = md5(mt_rand(1,10000).$CURUSER['username'].TIMENOW.$CURUSER['passhash']);
sql_query("INSERT INTO invites (inviter, invitee, hash, time_invited) VALUES ('".mysql_real_escape_string($id)."', '".mysql_real_escape_string($email)."', '".mysql_real_escape_string($hash)."', " . sqlesc(date("Y-m-d H:i:s")) . ")");
sql_query("UPDATE users SET invites = invites - 1 WHERE id = ".mysql_real_escape_string($id)."") or sqlerr(__FILE__, __LINE__);
header("Refresh: 0; url=invite.php?id=".htmlspecialchars($id));
}

?> 
  
    

