<?php
require_once("include/bittorrent.php");
header("Content-Type: text/html; charset=utf-8");
dbconn();
require_once(get_langfile_path("", false, get_langfolder_cookie()));
failedloginscheck ();
cur_user_check () ;
if (!mkglobal("username:password"))
	die();

if($_COOKIE["c_secure_AssWeCan"]!= 'Yes')
stderr("错误",'当前浏览器不支持COOKIE<br />请更改浏览器设置或清空浏览器缓存',false);

if(!($username&&$password))
stderr("错误",'请输入用户名和密码');

function bark($text = "")
{
  global $lang_takelogin;
  $text =  ($text == "" ? $lang_takelogin['std_login_fail_note'] : $text);
  stderr($lang_takelogin['std_login_fail'], $text,false);
}
//if ($iv == "yes"||$iv == "op")
	//check_code ($_POST['imagehash'], $_POST['imagestring'],'login.php',true);



if($_POST['logintype']=='uid')	
$res = sql_query("SELECT id, passhash, secret, enabled, status , logouttime, passkey FROM users WHERE id = " . sqlesc(0+$username));
elseif($_POST['logintype']=='email')	
$res = sql_query("SELECT id, passhash, secret, enabled, status , logouttime, passkey FROM users WHERE email='".mysql_real_escape_string($username)."'");
else
$res = sql_query("SELECT id, passhash, secret, enabled, status , logouttime, passkey FROM users WHERE username = " . sqlesc($username));




$row = mysql_fetch_array($res);

/* echo "<pre>";
print_r($row);
echo $row["secret"] . $password . $row["secret"];
exit(); */
if (!$row)
	failedlogins($lang_takelogin['std_account_invalid']);
if ($row['status'] == 'pending')
	failedlogins($lang_takelogin['std_user_account_unconfirmed']);

if ($row["passhash"] != md5($row["secret"] . $password . $row["secret"]))
	//login_failedlogins($lang_takelogin['std_password_invalid']);
	failedlogins($lang_takelogin['std_password_invalid']);

if ($row["enabled"] == "no")
	bark($lang_takelogin['std_account_disabled']);

if( TIMENOW <= $row['logouttime'] ){
sql_query("UPDATE users SET logouttime = ".sqlesc(TIMENOW)." WHERE id=" . sqlesc($row["id"]));
$row['logouttime']=TIMENOW;
}
	
if ($_POST["securelogin"] == "yes")
{
	$securelogin_indentity_cookie = true;
	$passh = md5($row["logouttime"].$row["passhash"].$_SERVER["REMOTE_ADDR"]);
}
else
{
	$securelogin_indentity_cookie = false;
	$passh = md5($row["logouttime"].$row["passhash"]);
}

if ($securelogin=='yes' || $_POST["ssl"] == "yes")
{
	$pprefix = "https://";
	$ssl = true;
}
else
{
	$pprefix = "http://";
	$ssl = false;
}
if ($securetracker=='yes' || $_POST["trackerssl"] == "yes")
{
	$trackerssl = true;
}
else
{
	$trackerssl = false;
}

if ($_POST["thispagewidth"] == "yes")$thispagewidth=true;
else $thispagewidth=false;

if ($_POST["logout"] == "yes")
{
	logincookie($row["id"], $passh,1,0x2A30,$securelogin_indentity_cookie, $ssl, $trackerssl,$thispagewidth);
	//sessioncookie($row["id"], $passh,true);
}
else 
{
	logincookie($row["id"], $passh,1,0x7fffffff,$securelogin_indentity_cookie, $ssl, $trackerssl,$thispagewidth);
	//sessioncookie($row["id"], $passh,false);
}

//if( TIMENOW <= $row['logouttime'] )sql_query("UPDATE users SET logouttime = ".sqlesc(TIMENOW-10)." WHERE id=" . sqlesc($row["id"]));

//print("<iframe src=" . $pprefix . "$BASEURLV6/ipv6record.php?passkey=".$row["passkey"]." width=\"1\" height=\"1\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\"></iframe>");

setcookie("AssWeCan",'');

if (!empty($_POST["returnto"]))
	redirect("$_POST[returnto]");
else
	redirect("index.php");
?>
