<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
if(!(0+$CURUSER["id"]))die();//loggedinorreturn();

$body = $_POST['body'];
if($_POST['action']=='light'){
print "<style type='text/css'>#footer a.faqlink {color:#A83838;}</style>";
print "<div class='bbcode' style='color: black;text-align:left;font-size: 10pt;'><table align=\"center\"><tr><td><br>".format_comment($body)."<br><br></td></tr></table></div>";
}else{
print ("<table width=100% border=1 cellspacing=0 cellpadding=10 align=left>\n");
print ("<tr><td align=left>".format_comment($body)."<br /><br /></td></tr></table>");
}
?>
