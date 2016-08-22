<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
if (get_user_class() < UC_ADMINISTRATOR)
stderr("Sorry", "Access denied.");
stdhead("Mass PM", false);
?>
<table class=main width=737 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<div align=center>
<h1>Mass PM to all Staff members and users:</a></h1>
<form method=post action=takestaffmess.php>
<?php

if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"])
{
?>
<input type=hidden name=returnto value="<?php echo htmlspecialchars($_GET["returnto"]) ? htmlspecialchars($_GET["returnto"]) : htmlspecialchars($_SERVER["HTTP_REFERER"])?>">
<?php
}
?>
<table cellspacing=0 cellpadding=5>
<?php
if ($_GET["sent"] == 1) {
?>
<tr><td colspan=2><font color=red><b>The message has ben sent.</font></b></tr></td>
<?php
}
?>
<tr>
<td><b>Send to:</b><br />
  <table style="border: 0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
             

	   <?
	   for($i=UC_USER;$i<=UC_SYSOP;$i++)
	   
    print  "<td style=\"border: 0\"><label for='c{$i}'><input checked=\'checked\' type='checkbox' value='{$i}' id='c{$i}' name='clases[]'/> ".get_user_class_name($i,0,0,1)."</label></td>".($i%4==0?"<tr>":"");
	   
	   
	   ?>
      </tr>
    </table>
  </td>
</tr>
<tr><td>Subject <input type=text name=subject size=75></tr></td>
<tr><td><textarea name=msg cols=80 rows=15><?php echo $body?></textarea></td></tr>
<tr>
<td colspan=1><div align="center"><b>Sender:&nbsp;&nbsp;</b>
<?php echo $CURUSER['username']?>
<input name="sender" type="radio" value="self" checked>
&nbsp; System
<input name="sender" type="radio" value="system">
</div></td></tr>
<tr><td colspan=1 align=center><input type=submit value="Send!" class=btn></td></tr>
</table>
<input type=hidden name=receiver value=<?php echo $receiver?>>
</form>

 </div></td></tr></table>
<br />
NOTE: Do not user BB codes. (NO HTML)
<?php
stdfoot();
