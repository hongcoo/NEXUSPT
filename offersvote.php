<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path("offers.php"));
require_once(get_langfile_path("offers.php",true));
//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
//header("Content-Type: text/xml; charset=utf-8"); 

 
$id = 0 + $_POST['id'];
$userid = 0 + $CURUSER["id"];

if ($_POST["vote"]&&$userid){
	$offerid = 0 + htmlspecialchars($_POST["id"]);
	$vote = htmlspecialchars($_POST["vote"]);
	if ($vote =='yeah' || $vote =='against'&&get_user_class() >= $againstoffer_class)
	{
		$userid = 0+$CURUSER["id"];
		$res = sql_query("SELECT * FROM offervotes WHERE offerid=".sqlesc($offerid)." AND userid=".sqlesc($userid)) or sqlerr(__FILE__,__LINE__);
		$arr = mysql_fetch_assoc($res);
		$voted = $arr;
		$offer_userid = get_single_value("offers", "userid", "WHERE id=".sqlesc($offerid));

		if (!$voted&&$offer_userid != $CURUSER['id'])

		{
			sql_query("UPDATE offers SET $vote = $vote + 1 WHERE id=".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);

			$res = sql_query("SELECT users.username, offers.userid, offers.name FROM offers LEFT JOIN users ON offers.userid = users.id WHERE offers.id = ".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);
			$arr = mysql_fetch_assoc($res);

			$rs = sql_query("SELECT yeah, against, allowed FROM offers WHERE id=".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);
			$ya_arr = mysql_fetch_assoc($rs);
			$yeah = $ya_arr["yeah"];
			$against = $ya_arr["against"];
			$finishtime = date("Y-m-d H:i:s");
			//allowed and send offer voted on message
			if(($yeah-$against)>=$minoffervotes && $ya_arr['allowed'] == "pending")
			{
				if ($offeruptimeout_main){
					$timeouthour = floor($offeruptimeout_main/3600);
					$timeoutnote = $lang_offers_target[get_user_lang($arr["userid"])]['msg_you_must_upload_in'].$timeouthour.$lang_offers_target[get_user_lang($arr["userid"])]['msg_hours_otherwise'];
				}
				else $timeoutnote = "";
				sql_query("UPDATE offers SET allowed='allowed', allowedtime=".sqlesc($finishtime)." WHERE id=".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);
				$msg = $lang_offers_target[get_user_lang($arr['userid'])]['msg_offer_voted_on']."[b][url=offers.php?id=$offerid&off_details=1]" . $arr[name] . "[/url][/b].". $lang_offers_target[get_user_lang($arr['userid'])]['msg_find_offer_option'].$timeoutnote;
				$subject = $lang_offers_target[get_user_lang($arr['userid'])]['msg_your_offer_allowed']."(". $arr[name].")";
				sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, $arr[userid], " . sqlesc(date("Y-m-d H:i:s")) . ", " . sqlesc($msg) . ", ".sqlesc($subject).")") or sqlerr(__FILE__, __LINE__);
				write_log("System allowed offer $arr[name]",'normal');
			}
			//denied and send offer voted off message
			if(($against-$yeah)>=$minoffervotes && $ya_arr['allowed'] == "pending")
			{
				sql_query("UPDATE offers SET allowed='denied' WHERE id=".sqlesc($offerid)) or sqlerr(__FILE__,__LINE__);
				$msg = $lang_offers_target[get_user_lang($arr['userid'])]['msg_offer_voted_off']."[b][url=offers.php?id=$offid&off_details=1]" . $arr[name] . "[/url][/b].";
				$subject = $lang_offers_target[get_user_lang($arr['userid'])]['msg_offer_deleted']."(". $arr[name].")";
				sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, $arr[userid], " . sqlesc(date("Y-m-d H:i:s")) . ", " . sqlesc($msg) . ", ".sqlesc($subject).")") or sqlerr(__FILE__, __LINE__);
				write_log("System denied offer $arr[name]",'normal');
			}


			sql_query("INSERT INTO offervotes (offerid, userid, vote) VALUES($offerid, $userid, ".sqlesc($vote).")") or sqlerr(__FILE__,__LINE__);
			KPS("+",$offervote_bonus,$CURUSER["id"]);		

		}
	}ELSE ECHO "ERR";
}





$res2 =  mysql_fetch_array(sql_query("SELECT id , offers.id, yeah , against  FROM offers  where id =".$id ));


	if ($res2["yeah"] == 0 &&$res2["against"] == 0)
	{
		$v_res = "0";
	}
	else
	{

		$v_res = "<b><a href=\"?id=".$res2[id]."&amp;offer_vote=1\" title=\"".$lang_offers['title_show_vote_details']."\"><font color=\"green\">" .$res2[yeah]."</font> - <font color=\"red\">".$res2[against]."</font> = ".($res2[yeah] - $res2[against]). "</a></b>";
	}

	echo $v_res;
	

?>
