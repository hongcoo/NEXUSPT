<?php
 #############################################################################
 # IMDBPHP                              (c) Giorgos Giagas & Itzchak Rehberg #
 # written by Giorgos Giagas                                                 #
 # extended & maintained by Itzchak Rehberg <izzysoft@qumran.org>            #
 # http://www.qumran.org/homes/izzy/                                         #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################
 /*   			 $this->credits_director = strstr ($this->page["Credits"], "导演");
	$this->credits_director = strstr ($this->credits_director, "valign");
	$endpos = stripos ($this->credits_director, "/td>");//85
	$this->credits_director =mb_strcut($this->credits_director, 0, $endpos,"UTF-8");
	
	//$this->credits_director = ereg_replace("<a [^>]*>|<\/a>","",$this->credits_director);
	$this->credits_director =mb_strcut($this->credits_director, 0, $endpos,"UTF-8");	
	$this->credits_director=matcheslink($this->credits_director);
	
	//$this->credits_director = ereg_replace("更多详细拍摄地","",$this->credits_director); 
	
		//$endpos = stripos ($this->alsoknow, "/span");//85
	//$endpos = strpos ($this->alsoknow, "/span");//85
	//$endpos = mb_strpos($this->alsoknow,'/span');//85
	//$this->alsoknow = substr ($this->alsoknow, 14, 70 );//中文两字符
	//$this->alsoknow =mb_strcut($this->alsoknow, 14 ,70 ,"UTF-8");//中文两字符
	//$this->alsoknow =mb_substr($this->alsoknow, 14 ,28 ,"UTF-8");//中文一字符
   // $this->main_title = substr ($this->main_title, 7, $endpos - 7);
   // $year_s = strpos ($this->main_title, "(", 0);
   // $year_e = strpos ($this->main_title, ")", 0);
   // $this->main_title = substr ($this->main_title, 0, $year_s - 1);
	
	*/

 /* $Id: imdb.class.php,v 1.13 2007/10/05 00:07:03 izzy Exp $ */

 require_once ("include/browser/browseremulator.class.php");
 require_once ("include/browser/info_extractor.php");
 require_once (dirname(__FILE__)."/imdb_config.php");

 #===============================================[ The IMDB class itself ]===
 /** Accessing IMDB information
  * @package Api
  * @class imdb
  * @extends imdb_config
  * @author Izzy (izzysoft@qumran.org)
  * @copyright (c) 2002-2004 by Giorgos Giagas and (c) 2004-2007 by Itzchak Rehberg and IzzySoft
  * @version $Revision: 1.13 $ $Date: 2007/10/05 00:07:03 $
  */
 class imdb extends imdb_config {
  var $imdbID = "";
  var $page;

  var $main_title = "";
  var $main_year = "";
  var $main_episodes = "";
  var $main_runtime = "";
  var $main_runtimes;
  var $main_rating = "";
  var $main_votes = "";
  var $main_language = "";
  var $main_languages = "";
  var $main_genre = "";
  var $main_genres = "";
  var $main_tagline = "";
  var $main_plotoutline = "";
  var $main_comment = "";
  var $main_alttitle = "";
  var $main_colors = "";

  var $plot_plot = "";
  var $taglines = "";

  var $credits_cast = "";
  var $credits_director = "";
  var $credits_writing = "";
  var $credits_producer = "";

  var $main_director = "";
  var $main_credits = "";
  var $main_photo = "";
  var $main_country = "";
  var $main_alsoknow = "";
  var $main_sound = "";

  var $info_excer;
  var $imdbtype="1";
  var $imdbtureid = "";
  var $doubantureid = "";
  
  var $similiar_movies = array(array('Name' => '', 'Link' => '', 'Local' => ''));	// no Thumbnail here, since it works different from last.fm, douban
  var $extension = array('Title');
  
  function debug_scalar($scalar) {
    echo "<b><font color='#ff0000'>$scalar</font></b><br>";
  }
  
  
function settypt ($id) {
 $this->imdbtype = $id;
 }


  function debug_object($object) {
    echo "<font color='#ff0000'><pre>";print_r($object);echo "</pre></font>";
  }
  function debug_html($html) {
    echo "<b><font color='#ff0000'>".htmlentities($html)."</font></b><br>";
  }

	/** Get similiar movies
   * @method similiar_movies
   * @return list similiar_movies
   */
	function similiar_movies()
	{
		if (!isset($this->similiar_movies))
		{
			if ($this->page["Title"] == "")
			{
				$this->openpage ("Title");
			}
			$similiar_movies = $this->info_excer->truncate($this->page["Title"], "<h3>Recommendations</h3>", "<tr class=\"rating\">");
			$similiar_movies = $this->info_excer->truncate($similiar_movies, "<tr>", "</tr>");
			$res_where_array = array('Link' => '1', 'Name' => '3');
			if($res_array = $this->info_excer->find_pattern($similiar_movies,"/<td><a href=\"((\s|.)+?)\">((\s|.)+?)<\/a><\/td>/",true,$res_where_array))
			{
				$counter = 0;
				foreach($res_array as $res_array_each)
				{
					$this->similiar_movies[$counter]['Link'] = $res_array_each[0];
					$this->similiar_movies[$counter]['Name'] = $res_array_each[1];
					
					$imdb_id = ltrim(strrchr($res_array_each[0],'tt'),'tt');
					$imdb_id = preg_replace("/[^A-Za-z0-9]/", "", $imdb_id);
					
					//die("ss" . $imdb_id);
					$imdb_sim_movies = new imdb($imdb_id);
					//$imdb_sim_movies->setid($imdb_id);
					$target = array('Title');
					$imdb_sim_movies->preparecache($target,false);
					$this->similiar_movies[$counter]['Local'] = $imdb_sim_movies->photo_localurl();
					$counter++;
				}
			}
		}
		return $this->similiar_movies;
	}
  
  
  /** Test if IMDB url is valid
   * @method urlstate ()
   * @param none
   * @return int state (0-not valid, 1-valid)
   */
  function urlstate () {
   if (strlen($this->imdbID) != 7&&strlen($this->imdbID) != 8)
    return 0;
   else
   	return 1;
  }

  /** Test if caching IMDB page is complete
   * @method cachestate ()
   * @param $target array
   * @return int state (0-not complete, 1-cache complete, 2-cache not enabled, 3-not valid imdb url)
   */
  function cachestate ($target = "",$checkphoto=true) {
   if (strlen($this->imdbID) != 7&&strlen($this->imdbID) != 8){
    //echo "not valid imdbID: ".$this->imdbID."<BR>".strlen($this->imdbID);
    $this->page[$wt] = "cannot open page";
    return 3;
   }
   if ($this->usecache)
   {
   
   

   	$ext_arr =  $this->extension;
	foreach($ext_arr as $ext)
  	{
	   	if(!file_exists($this->cachedir."/".$this->imdbID.".".$ext))
			return 0;
	    @$fp = fopen ($this->cachedir."/".$this->imdbID.".".$ext, "r");
	    if (!$fp)
	    	return 0;
  	}
	
	if ($checkphoto&&(!file_exists($this->photodir."/".$this->imdbID.".jpg")||filesize($this->photodir."/".$this->imdbID.".jpg")<100))return 0;
  	return 1;
   }
   else
   	return 2;
  }
  
   /** prepare IMDB page cache
   * @method preparecache
   * @param $target array
   */
  function preparecache ($target = "", $retrive_similiar = false) {
  	$ext_arr =  $this->extension;
  	foreach($ext_arr as $ext)
  	{
  		$tar_ext = array($ext);
	    if($this->cachestate($tar_ext) == 0) 
	    	$this->openpage($ext);
  	}
  	if($retrive_similiar)
  		$this->similiar_movies(false);
  	return $ext;
  }
  
  /** Open an IMDB page
   * @method openpage
   * @param string wt
   */
  function openpage ($wt) {
   if (strlen($this->imdbID) != 7&&strlen($this->imdbID) != 8){
    echo "not valid imdbID: ".$this->imdbID."<BR>".strlen($this->imdbID);
    $this->page[$wt] = "cannot open page";
    return;
   }
   switch ($wt){
    //case "Title"   : $urlname="/"; break;
   // case "Credits" : $urlname="/fullcredits"; break;
   //case "Plot"    : $urlname="/plotsummary"; break;
    //case "Taglines": $urlname="/taglines"; break;
	case "Title"   : $urlname=""; break;
	//case "Credits" : $urlname=""; break;
   //case "Plot"    : $urlname=""; break;
  // case "Taglines": $urlname=""; break;
   }
   if ($this->usecache) {
    @$fp = fopen ("$this->cachedir/$this->imdbID.$wt", "r");
    if ($fp) {
     $temp="";
     while (!feof ($fp)) {
	$temp .= fread ($fp, 1024);
     }
	if ($temp) {
		$this->page[$wt] = $temp;
		fclose($fp);
     		return;
	}
    }
   } // end cache


   
   
if($this->imdbtype==2)
$url="http://api.douban.com/movie/subject/".$this->imdbID."?apikey=".doubanapikey();
else
$url="http://".$this->imdbsite."/tt".$this->imdbID."?apikey=".doubanapikey();


$this->page[$wt] = file_get_contents_function($url,20);

  /*$req = new IMDB_Request("");
  $req->setURL($url);
  $response = $req->send();
  $this->page[$wt] = $response->getBody();*/
  






   if ($responseBody) {
      // $this->page[$wt] = utf8_encode($responseBody);
	  //$this->page[$wt] = iconv ("gb2312","UTF-8",$responseBody);
   }
   if( $this->page[$wt] ){ //storecache
    if ($this->storecache) {
     $fp = fopen ("$this->cachedir/$this->imdbID.$wt", "w");
     fputs ($fp, $this->page[$wt]);
     fclose ($fp);
    }
    return;
   }
   $this->page[$wt] = "cannot open page";
   //echo "page not found";
  }

  /** Retrieve the IMDB ID
   * @method imdbid
   * @return string id
   */
  function imdbid () {
   return $this->imdbID;
  }

  /** Setup class for a new IMDB id
   * @method setid
   * @param string id
   */
  function setid ($id) {
   $this->imdbID = $id;

   $this->page["Title"] = "";
   $this->page["Credits"] = "";
   $this->page["Amazon"] = "";
   $this->page["Goofs"] = "";
   $this->page["Plot"] = "";
   $this->page["Comments"] = "";
   $this->page["Quotes"] = "";
   $this->page["Taglines"] = "";
   $this->page["Plotoutline"] = "";
   $this->page["Trivia"] = "";
   $this->page["Directed"] = "";

   $this->main_title = "";
   $this->main_year = "";
   $this->main_runtime = "";
   $this->main_rating = "";
   $this->main_comment = "";
   $this->main_votes = "";
   $this->main_language = "";
   $this->main_genre = "";
   $this->main_genres = "";
   $this->main_tagline = "";
   $this->main_plotoutline = "";
   $this->main_alttitle = "";
   $this->main_colors = "";
   $this->credits_cast = "";
   $this->main_director = "";
   $this->main_creator = "";
   
   unset($this->similiar_movies);
   $this->info_excer = new info_extractor();
  }
  
  
   
  

  /** Initialize class
   * @constructor imdb
   * @param string id
   */
  function imdb ($id) {
   $this->imdb_config();
   $this->setid($id);
   //if ($this->storecache && ($this->cache_expire > 0)) $this->purge();
  }

  /** Check cache and purge outdated files
   *  This method looks for files older than the cache_expire set in the
   *  imdb_config and removes them
   * @method purge
   */
  function purge($explicit = false) {
    if (is_dir($this->cachedir))  {
      $thisdir = dir($this->cachedir);
      $now = time();
      while( $file=$thisdir->read() ) {
        if ($file!="." && $file!="..") {
          $fname = $this->cachedir ."/". $file;
	  if (is_dir($fname)) continue;
          $mod = filemtime($fname);
          if ($mod && (($now - $mod > $this->cache_expire) || $explicit == true)) unlink($fname);
        }
      }
    }
  }
  
  /** Check cache and purge outdated single imdb title file
   *  This method looks for files older than the cache_expire set in the
   *  imdb_config and removes them
   * @method purge
   */
  function purge_single($explicit = false,$explicitmust = false,$days=12) {
    if (is_dir($this->cachedir)) 
    {
      $thisdir = dir($this->cachedir);
      foreach($this->extension as $ext)
      {
	      $fname = $this->cachedir ."/". $this->imdbid() . "." . $ext;
		  $pname = $this->photoroot.$this->imdbid().'.jpg';
	      //return $fname;
	      if(file_exists($fname)&&file_exists($pname))
	      {
	      	  $now = time();
	          $mod = filemtime($fname);
	          if ($mod && (($now - $mod > $this->cache_expire) ||filesize($pname)<100 ||$explicitmust || ($explicit == true&&$now - $mod > 24*60*60*($days+mt_rand (1,8))))) 
			  {@unlink($fname);
			   @unlink($pname);
			  }
	      }else{@unlink($pname);@unlink($fname);}
      }
    }
  }
  
function purge_single_jpg($delete=false) {
		  $pname = $this->photoroot.$this->imdbid().'.jpg';
	      if(file_exists($pname)&&!isCompleteJpg($pname)&&$delete)@unlink($pname);
		  }


  /** get the time that cache is stored
   * @method getcachetime
   */
  function getcachetime() {
  	$mod =0;
    if (is_dir($this->cachedir)) 
    {
      $thisdir = dir($this->cachedir);
      foreach($this->extension as $ext)
      {
	      $fname = $this->cachedir ."/". $this->imdbid() . "." . $ext;
	      if(file_exists($fname))
	      {
	      	if($mod > filemtime($fname) || $mod==0)
	          $mod = filemtime($fname);
	      }
      }
    }
     return $mod;
  }
  
  /** Set up the URL to the movie title page
   * @method main_url
   * @return string url
   */
  function main_url(){
   return "http://".$this->imdbsite."/tt".$this->imdbid()."/";
  }

  /** Get movie title
   * @method title
   * @return string title
   */
  function title () {
   if ($this->main_title == "") {
    if ($this->page["Title"] == "") {
     $this->openpage ("Title");
    }
	

   preg_match_all( "/\<db:attribute name=\"title\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN ); 
   $this->main_title = $zh_CN[1][0];
	for($i = 1;$i < count($zh_CN[1]); $i++){$this->main_title .= $zh_CN[1][$i]."&nbsp;&nbsp;";}

   }
   return $this->main_title;
  }
  

  
    function episodes () {
   if ($this->main_episodes == "") {
    if ($this->page["Title"] == "") {
     $this->openpage ("Title");
    }
	

   preg_match_all( "/\<db:attribute name=\"episodes\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN ); 
$this->main_episodes = $zh_CN[1][0];

   }
   return $this->main_episodes;
  }
  
    function composer () {
   if ($this->credits_composer == "") {
    if ($this->page["title"] == "") $this->openpage ("title");
   }
   /*$this->credits_composer = array();
   $composer_rows = $this->get_table_rows($this->page["Credits"], "Original Music by");
   for ( $i = 0; $i < count ($composer_rows); $i++){
	$cels = $this->get_row_cels ($composer_rows[$i]);
	if ( count ( $cels) > 2){
		$wrt["imdb"] = $this->get_imdbname($cels[0]);
		$wrt["name"] = strip_tags($cels[0]);
		$role = strip_tags($cels[2]);
		if ( $role == ""){
			$wrt["role"] = NULL;
		}else{
			$wrt["role"] = $role;
		}
		$this->credits_composer[$i] = $wrt;
	}
   }*/
   
   $this->credits_composer = strstr ($this->page["title"], "音乐");
	$endpos = stripos ($this->credits_composer, "/table>");//85

	$this->credits_composer =mb_strcut($this->credits_composer, 0, $endpos,"UTF-8");	
	$this->credits_composer=matcheslink($this->credits_composer);
	
	
   return $this->credits_composer;
  }

  /** Get year
   * @method year
   * @return string year
   */
  function year () {
   if ($this->main_year == "")
   {
    if ($this->page["Title"] == "") {
     $this->openpage ("Title");
    }

preg_match_all( "/\<db:attribute name=\"pubdate\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN ); 
for($i = 0;$i < count($zh_CN[1]); $i++){$this->main_year .= $zh_CN[1][$i]."&nbsp;&nbsp;";}

   }
   return $this->main_year;
  }

  /** Get general runtime
   * @method runtime_all
   * @return string runtime
   */
  function runtime_all () {
   if ($this->main_runtime == "") {
    if ($this->page["Title"] == "") {
	$this->openpage ("Title");
    }
   preg_match_all( "/\<db:attribute name=\"movie_duration\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN ); 
for($i = 0;$i < count($zh_CN[1]); $i++){$this->main_runtime .= $zh_CN[1][$i]."&nbsp;&nbsp;";}
    }
    return $this->main_runtime;
  }

  /** Get overall runtime
   * @method runtime
   * @return mixed string runtime (if set), NULL otherwise
   */
  function runtime(){
   $runarr = $this->runtimes();
   if (isset($runarr[0]["time"])){
	return $runarr[0]["time"];
   }else{
	return NULL;
   }
  }

  /** Retrieve language specific runtimes
   * @method runtimes
   * @return array runtimes (array[0..n] of array[time,country,comment])
   */
  function runtimes(){
   if ($this->main_runtimes == "") {
    if ($this->runtime_all() == ""){
	return array();
    }
#echo $this->runtime_all();
    $run_arr= explode( "|" , $this->runtime_all());
    $max = count($run_arr);
    for ( $i=0; $i < $max ; $i++){
	$time_e = strpos( $run_arr[$i], " min");
	$country_e = strpos($run_arr[$i], ":");
	if ( $country_e == 0){
	 $time_s = 0;
	}else{
	 $time_s = $country_e+1;
	}
	$comment_s = strpos( $run_arr[$i], '(');
	$comment_e = strpos( $run_arr[$i], ')');
	$runtemp["time"]= substr( $run_arr[$i], $time_s, $time_e - $time_s);
	$country_s = 0;
	if ($country_s != $country_e){
	 $runtemp["country"]= substr( $run_arr[$i], $country_s, $country_e - $country_s);
	}else{
	 $runtemp["country"]=NULL;
	}
	if ($comment_s != $comment_e){
	 $runtemp["comment"]= substr( $run_arr[$i], $comment_s + 1, $comment_e - $comment_s - 1);
	}else{
	 $runtemp["comment"]=NULL;
	}
	$this->main_runtimes[$i] = $runtemp;
    }
   }
   return $this->main_runtimes;
  }

  /** Get movie rating
   * @method rating
   * @return string rating
   */
  function rating () {
   if ($this->main_rating == "")
   {
    if ($this->page["Title"] == "") {
	$this->openpage ("Title");
    }

	
	   preg_match_all( "/average=\"([0-9.]*?)\"/sim", $this->page["Title"], $zh_CN ); 

$this->main_rating .= $zh_CN[1][0];
$this->main_rating=number_format($this->main_rating,1, '.', '');
	
    //if ($rate_e - $rate_s > 7) $this->main_rating = "";
   
   } 
   return $this->main_rating;
  }

  /** Get movie comment
   * @method comment
   * @return string comment
   */
  function comment () {
     if ($this->main_comment == "") {
      if ($this->page["Title"] == "") $this->openpage ("Title");
      $comment_s = strpos ($this->page["Title"], "people found the following comment useful:-");
      if ( $comment_s == 0) return false;
      $comment_e = strpos ($this->page["Title"], "Was the above comment useful to you?", $comment_s);
      $forward_safeval = 50;
      $comment_s_fix = $forward_safeval - strpos(substr($this->page["Title"], $comment_s - $forward_safeval, $comment_e - $comment_s + $forward_safeval),"<div class=\"small\">") - strlen("<div class=\"small\">");
      
      $this->main_comment = substr ($this->page["Title"], $comment_s - $comment_s_fix, $comment_e - $comment_s + $comment_s_fix);
      $this->main_comment = preg_replace("/a href\=\"\//i","a href=\"http://".$this->imdbsite."/",$this->main_comment);
      $this->main_comment = preg_replace("/http:\/\/[a-zA-Z.-]+\/images\/showtimes\//i","pic/imdb_pic/",$this->main_comment);
      $this->main_comment = preg_replace("/<\/?div.*>/i","",$this->main_comment);
      $this->main_comment = preg_replace("/<form.*>/i","",$this->main_comment);
     }
     return $this->main_comment;
  }

  /** Return votes for this movie
   * @method votes
   * @return string votes
   */
  function votes () {
   if ($this->main_votes == "") {
   if ($this->page["Title"] == "") $this->openpage ("Title");

	
	 preg_match_all( "/numRaters=\"([0-9.]*?)\"/sim", $this->page["Title"], $zh_CN ); 

$this->main_votes .= $zh_CN[1][0];



   }
   return $this->main_votes;
  }

  /** Get movies original language
   * @method language
   * @return string language
   */
  function language () {
   if ($this->main_language == "") {
    if ($this->page["Title"] == "") $this->openpage ("Title");

		
preg_match_all( "/\<db:attribute name=\"language\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN);
for($i = 0;$i < count($zh_CN[1]); $i++){$this->main_language.= $zh_CN[1][$i]."&nbsp;&nbsp;";}
   }
   return $this->main_language;
  }

  /** Get all langauges this movie is available in
   * @method languages
   * @return array languages (array[0..n] of strings)
   */
  function languages () {
   if ($this->main_languages == "") {
    if ($this->page["Title"] == "") $this->openpage ("Title");
    $lang_s = 0;
    $lang_e = 0;
    $i = 0;
    $this->main_languages = array();
    while (strpos($this->page["Title"], "/Sections/Languages/", $lang_e) > $lang_s) {
	$lang_s = strpos ($this->page["Title"], "/Sections/Languages/", $lang_s);
	$lang_s = strpos ($this->page["Title"], ">", $lang_s);
	$lang_e = strpos ($this->page["Title"], "<", $lang_s);
	$this->main_languages[$i] = substr ($this->page["Title"], $lang_s + 1, $lang_e - $lang_s - 1);
	$i++;
    }
   }
   return $this->main_languages;
  }

  /** Get the movies main genre
   * @method genre
   * @return string genre
   */
  function genre () {
   if ($this->main_genre == "") {
    if ($this->page["Title"] == "") $this->openpage ("Title");
    $genre_s = strpos ($this->page["Title"], "/Sections/Genres/");
    if ( $genre_s === FALSE )	return FALSE;
    $genre_s = strpos ($this->page["Title"], ">", $genre_s);
    $genre_e = strpos ($this->page["Title"], "<", $genre_s);
    $this->main_genre = substr ($this->page["Title"], $genre_s + 1, $genre_e - $genre_s - 1);
   }
   return $this->main_genre;
  }

  /** Get all genres the movie is registered for
   * @method genres
   * @return array genres (array[0..n] of strings)
   */
  function genres () {
   if ($this->main_genres == "") {
    if ($this->page["Title"] == "") $this->openpage ("Title");

		preg_match_all( "/\<db:attribute name=\"movie_type\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN);
for($i = 0;$i < count($zh_CN[1]); $i++){ $this->main_genres .= $zh_CN[1][$i]."&nbsp;&nbsp;";}

   }
   return $this->main_genres;
  }

  /** Get colors
   * @method colors
   * @return array colors (array[0..1] of strings)
   */
  function colors () {
   if ($this->main_colors == "") {
    if ($this->page["Title"] == "") $this->openpage ("Title");
    $color_s = 0;
    $color_e = 0;
    $i = 0;
    while (strpos ($this->page["Title"], "/List?color-info", $color_e) > $color_s) {
	$color_s = strpos ($this->page["Title"], "/List?color-info", $color_s);
	$color_s = strpos ($this->page["Title"], ">", $color_s);
	$color_e = strpos ($this->page["Title"], "<", $color_s);
	$this->main_colors[$i] = substr ($this->page["Title"], $color_s + 1, $color_e - $color_s - 1);
	$i++;
    }
   }
   return $this->main_colors;
  }

  /** Get the main tagline for the movie
   * @method tagline
   * @return string tagline
   */
  function tagline () {
   if ($this->main_tagline == "") {
    if ($this->page["Title"] == "") $this->openpage ("Title");

	
preg_match_all( "<db:tag count=\"[0-9]*\" name=\"([^\"]*?)\"/>", $this->page["Title"], $zh_CN);
for($i = 0;$i < count($zh_CN[1]); $i++){ $this->main_tagline .= $zh_CN[1][$i]."&nbsp;&nbsp;";}
   }
   return $this->main_tagline;
  }

  /** Get the main Plot outline for the movie
   * @method plotoutline
   * @return string plotoutline
   */
  function plotoutline () {
    if ($this->main_plotoutline == "") {
      if ($this->page["Title"] == "") $this->openpage ("Title");
      $plotoutline_s = strpos ($this->page["Title"], "Plot:");
      if ( $plotoutline_s == 0) return FALSE;
      $plotoutline_s = strpos ($this->page["Title"], ">", $plotoutline_s);
      $plotoutline_e = strpos ($this->page["Title"], "<", $plotoutline_s);
      $this->main_plotoutline = substr ($this->page["Title"], $plotoutline_s + 1, $plotoutline_e - $plotoutline_s - 1);
    }
    return $this->main_plotoutline;
  }

  /** Get the movies plot(s)
   * @method plot
   * @return array plot (array[0..n] of strings)
   */
  function plot () {
   if ($this->plot_plot == "") {
    if ($this->page["Title"] == "") $this->openpage ("Title");

	$temp=nl2br($this->page["Title"] );
	preg_match_all( "/\<summary>(.*?)\<\/summary>/sim", $temp, $zh_CN);
for($i = 0;$i < count($zh_CN[1]); $i++){$this->plot_plot.= $zh_CN[1][$i]."&nbsp;&nbsp;";}
   }
   return $this->plot_plot;
  }

  /** Get all available taglines for the movie
   * @method taglines
   * @return array taglines (array[0..n] of strings)
   */
  function taglines () {
   if ($this->taglines == "") {
    if ($this->page["Taglines"] == "") $this->openpage ("Taglines");
    $tags_e = 0;
    $i = 0;
    $tags_s = strpos ($this->page["Taglines"], "<td width=\"90%\" valign=\"top\" >", $tags_e);
    $tagend = strpos ($this->page["Taglines"], "<form method=\"post\" action=\"/updates\">", $tags_s);
    $this->taglines = array();
    while (($tags_s = strpos ($this->page["Taglines"], "<p>", $tags_e)) < $tagend) {
	$tags_e = strpos ($this->page["Taglines"], "</p>", $tags_s);
	$tmptag = substr ($this->page["Taglines"], $tags_s + 3, $tags_e - $tags_s - 3);
	if (preg_match("/action\=\"\//i",$tmptag)) continue;
	$this->taglines[$i] = $tmptag;
	$i++;
    }
   }
   return $this->taglines;
  }

  /** Get rows for a given table on the page
   * @method get_table_rows
   * @param string html
   * @param string table_start
   * @return mixed rows (FALSE if table not found, array[0..n] of strings otherwise)
   */
  function get_table_rows ( $html, $table_start ){
   $row_s = strpos ( $html, ">".$table_start."<");
   $row_e = $row_s;
   if ( $row_s == 0 )  return FALSE;
   $endtable = strpos($html, "</table>", $row_s);
   $i=0;
   while ( ($row_e + 5 < $endtable) && ($row_s != 0) ){
     $row_s = strpos ( $html, "<tr>", $row_s);
     $row_e = strpos ($html, "</tr>", $row_s);
     $temp = trim(substr ($html, $row_s + 4 , $row_e - $row_s - 4));
     if ( strncmp( $temp, "<td valign=",10) == 0 ){
       $rows[$i] = $temp;
       $i++;
     }
     $row_s = $row_e;
   }
   return $rows;
  }

  /** Get rows for the cast table on the page
   * @method get_table_rows_cast
   * @param string html
   * @param string table_start
   * @return mixed rows (FALSE if table not found, array[0..n] of strings otherwise)
   */
  function get_table_rows_cast ( $html, $table_start ){
   $row_s = strpos ( $html, '<table class="cast">');
   $row_e = $row_s;
   if ( $row_s == 0 )  return FALSE;
   $endtable = strpos($html, "</table>", $row_s);
   $i=0;
   while ( ($row_e + 5 < $endtable) && ($row_s != 0) ){
     $row_s = strpos ( $html, "<tr", $row_s);
     $row_e = strpos ($html, "</tr>", $row_s);
     $temp = trim(substr ($html, $row_s , $row_e - $row_s));
#     $row_x = strpos( $temp, '<td valign="middle"' );
     $row_x = strpos( $temp, '<td class="nm">' );
     $temp = trim(substr($temp,$row_x));
     if ( strncmp( $temp, "<td class=",10) == 0 ){
       $rows[$i] = $temp;
       $i++;
     }
     $row_s = $row_e;
   }
   return $rows;
  }

  /** Get content of table row cells
   * @method get_row_cels
   * @param string row (as returned by imdb::get_table_rows)
   * @return array cells (array[0..n] of strings)
   */
  function get_row_cels ( $row ){
   $cel_s = 0;
   $cel_e = 0;
   $endrow = strlen($row);
   $i = 0;
   $cels = array();
   while ( $cel_e + 5 < $endrow ){
	$cel_s = strpos( $row, "<td",$cel_s);
	$cel_s = strpos( $row, ">" , $cel_s);
	$cel_e = strpos( $row, "</td>", $cel_s);
	$cels[$i] = substr( $row, $cel_s + 1 , $cel_e - $cel_s - 1);
	$i++;
   }
   return $cels;
  }

  /** Get the IMDB name (?)
   * @method get_imdbname
   * @param string href
   * @return string
   */
  function get_imdbname( $href){
   if ( strlen( $href) == 0) return $href;
   $name_s = 15;
   $name_e = strpos ( $href, '"', $name_s);
   if ( $name_e != 0){
	return substr( $href, $name_s, $name_e -1 - $name_s);
   }else{
	return $href;
   }
  }

  /** Get the director(s) of the movie
   * @method director
   * @return array director (array[0..n] of strings)
   */
  function director () {
   if ($this->credits_director == ""){
    if ($this->page["Title"] == "") $this->openpage ("Title");
   
	
preg_match_all( "/\<db:attribute name=\"director\"\>(.*?)\<\/db:attribute\>/sim", $this->page["Title"], $zh_CN);
for($i = 0;$i < count($zh_CN[1]); $i++){ $this->credits_director .= $zh_CN[1][$i]."&nbsp;&nbsp;";}
	

	
	}
   return $this->credits_director;
  }
  
  
    function imdbtureid () {//chenzhuyu
   if ($this->imdbtureid == ""){
    if ($this->page["Title"] == "") $this->openpage ("Title");
   
	
preg_match_all( "/\<db:attribute name=\"imdb\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN);
if($zh_CN[1][0])$this->imdbtureid = parse_imdb_id($zh_CN[1][0]);
else $this->imdbtureid=false;
}
return $this->imdbtureid;
  }
      function doubantureid () {//chenzhuyu
   if ($this->doubantureid == ""){
    if ($this->page["Title"] == "") $this->openpage ("Title");
   
	
preg_match_all( "/(http:\/\/movie\.douban\.com\/subject\/[0-9]*)/", $this->page["Title"], $zh_CN);
if($zh_CN[1][0])$this->doubantureid = parse_imdb_id($zh_CN[1][0]);
else $this->doubantureid=false;
}
return $this->doubantureid;
  }


  /** Get the creator of the tv series
   * @method creator
   * @return string
   */
  function creator(){
   if ($this->main_creator == "") {
    if ($this->page["Title"] == "") $this->openpage ("Title");

	
	preg_match_all( "/\<name\>(.*?)\<\/name\>/sim", $this->page["Title"], $zh_CN);
for($i = 0;$i < count($zh_CN[1]); $i++){ $this->main_creator .= $zh_CN[1][$i]."&nbsp;&nbsp;";}


   }
   return $this->main_creator;
  }

  /** Get the actors
   * @method cast
   * @return array cast (array[0..n] of strings)
   */
  function cast () {

	if ($this->credits_cast == "")
	  {
    if ($this->page["Title"] == "") $this->openpage ("Title");
   
   

if(preg_match_all( "/\<db:attribute name=\"cast\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN)){;}
ELSEIF(preg_match_all( "/\<db:attribute name=\"dubbing\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN)){;}
for($i = 0;$i < count($zh_CN[1]); $i++){$this->credits_cast.= $zh_CN[1][$i]."&nbsp;&nbsp;";}
	}
   //}
   return $this->credits_cast;
  }

  /** Get the writer(s)
   * @method writing
   * @return array writers (array[0..n] of strings)
   */
  function writing () {
   if ($this->credits_writing == "") {
    if ($this->page["title"] == "") $this->openpage ("title");
   

preg_match_all( "/\<name\>(.*?)\<\/name\>/sim", $this->page["Title"], $zh_CN);
for($i = 0;$i < count($zh_CN[1]); $i++){$this->credits_writing.= $zh_CN[1][$i]."&nbsp;&nbsp;";}
	}
   return $this->credits_writing;
  }

  /** Obtain the producer(s)
   * @method producer
   * @return array producer (array[0..n] of strings)
   */
  function producer () {
   if ($this->credits_producer == "") {
    if ($this->page["title"] == "") $this->openpage ("title");
   

	
	preg_match_all( "/\<db:attribute name=\"writer\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN);
for($i = 0;$i < count($zh_CN[1]); $i++){$this->credits_producer.= $zh_CN[1][$i]."&nbsp;&nbsp;";}
	
	
	}
   return $this->credits_producer;
  }

  /** Obtain the composer(s) ("Original Music by...")
   * @method composer
   * @return array composer (array[0..n] of strings)
   */

  
  
  
  

  function link () {
   if ($this->main_link == "")
   {
    if ($this->page["Title"] == "") $this->openpage ("Title");

	 $this->main_link = strstr ($this->page["Title"], "rel=\"self\"");
		$this->main_link = strstr ($this->main_link, "href=");
	$endpos = stripos ($this->main_link, "rel=");//85
	$URL=mb_strcut($this->main_link, 6, $endpos-8,"UTF-8");



	IF($URL)
	$this->main_link = $URL;
	

	
	else return FALSE;
   }
   return $this->main_link;
  }

  
  /** Get cover photo
   * @method photo
   * @return mixed photo (string url if found, FALSE otherwise)
   */
  function photo () {
   if ($this->main_photo == "")
   {
    if ($this->page["Title"] == "") $this->openpage ("Title");

	
	
	 $this->main_photo = $this->page["Title"];//strstr ($this->page["Title"], "alternate");

		
		
		preg_match_all( "/(http[^\<\r\n\"']+?(jpg|png|gif|bmp))/", $this->main_photo, $zh_CN);
		$URL= $zh_CN[1][0];
	
	
	

	$URL= str_replace("spic","lpic",$URL);
	if (preg_match("/default/i", $URL))$URL='';
	//$URL= str_replace("movie-default","",$URL);
	
	//print( $URL);


	IF($URL)
	$this->main_photo = $URL;
	

	
	else $this->main_photo = FALSE;
   }
   return $this->main_photo;
  }

  /** Save the photo to disk
   * @method savephoto
   * @param string path
   * @return boolean success
   */
  function savephoto ($path) {  
   $photo_url = $this->photo ();
   if (!$photo_url) return FALSE;
   /*$req = new IMDB_Request("");
   $req->setUrl($photo_url);
   $response = $req->send();
   if (strpos($response->getHeader("Content-Type"),'image/jpeg') === 0
     || strpos($response->getHeader("Content-Type"),'image/gif') === 0
     || strpos($response->getHeader("Content-Type"), 'image/bmp') === 0 ){
	$fp = $response->getBody();
   }else{
	//echo "<BR>*photoerror* ".$photo_url.": Content Type is '".$req->getResponseHeader("Content-Type")."'<BR>";
	return false;
   }*/


	$fp=file_get_contents_function($photo_url,40);

   $fp2 = fopen ($path, "w");
   if ((!$fp) || (!$fp2)){
     echo "image error...<BR>";
     return false;
   }

   fputs ($fp2, $fp);
   fclose ($fp2);
   return TRUE;
  }

  /** Get the URL for the movies cover photo
   * @method photo_localurl
   * @return mixed url (string URL or FALSE if none)
   */
  function photo_localurl(){
   $path = $this->photodir.$this->imdbid().".jpg";
   if ( @fopen($path,"r")&&filesize($path)>100) return $this->photoroot.$this->imdbid().'.jpg';
   if ($this->savephoto($path))	return $this->photoroot.$this->imdbid().'.jpg';
   return false;
  }

  /** Get country of production
   * @method country
   * @return array country (array[0..n] of string)
   */
  function country () 
  {
   if ($this->main_country == "") 
   {
    if ($this->page["Title"] == "") $this->openpage ("Title");
	
	preg_match_all( "/\<db:attribute name=\"country\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN);
for($i = 0;$i < count($zh_CN[1]); $i++){$this->main_country.= $zh_CN[1][$i]."&nbsp;&nbsp;";}
	
	
   }
   return $this->main_country;
  }


  
  
 function alsoknow  () {
   if ($this->alsoknow == "") {
    if ($this->page["Title"] == "") {
     $this->openpage ("Title");
    }
	preg_match_all( "/\<db:attribute name=\"aka\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN);
	preg_match( "/\<db:attribute lang=\"zh_CN\" name=\"aka\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN2);
if($zh_CN2[1])$this->alsoknow.=$zh_CN2[1]."&nbsp;&nbsp;";
for($i = 0;$i < count($zh_CN[1]); $i++){
if($zh_CN[1][$i]!=$zh_CN2[1])
$this->alsoknow.= $zh_CN[1][$i]."&nbsp;&nbsp;";
}



   }
   return $this->alsoknow;
  }

   function alsoknowcnname  () {
   if ($this->alsoknowcnname == "") {
    if ($this->page["Title"] == "") {
     $this->openpage ("Title");
    }
	preg_match( "/\<db:attribute lang=\"zh_CN\" name=\"aka\"\>(.*?)\<\/db:attribute\>/", $this->page["Title"], $zh_CN2);
	if($zh_CN2[1])$this->alsoknowcnname=$zh_CN2[1];
	else $this->alsoknowcnname=$this->title ();
   }
   return $this->alsoknowcnname;
  }
 
function movieallinfo  (){
return $this->rating ()." imdb".$this->imdbtureid() ." douban".$this->doubantureid()." ".$this->title ().$this->alsoknow().$this->country ().$this->genres().$this->tagline().$this->director().$this->creator().$this->writing().$this->producer().$this->cast();
}

 } 


?>
