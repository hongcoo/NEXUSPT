<?php
//自行修改第177行代码
class WinCache{

		function pconnect($host, $port){
		if(extension_loaded('wincache'))
		return true;
		}

		function delete($Key){
		wincache_ucache_delete($Key);
		}
		
		function set($Key,$Value,$What,$Duration){
		wincache_ucache_set($Key,$Value,$Duration);
		}
		
		function get($Key){
		return wincache_ucache_get($Key);
		
		if(wincache_ucache_exists($Key))
			return wincache_ucache_get($Key);
		else
			return false;
		}
		
}

class APCCache{
		
		function pconnect($host, $port){
		if(extension_loaded('apc'))
		return true;
		}
		
		function delete($Key){
		apc_delete($Key);
		}
		
		function set($Key,$Value,$What,$Duration){
		apc_store($Key,$Value,$Duration);
		}
		
		function get($Key){
		if(apc_exists($Key))
			return apc_fetch($Key);
		else 
			return false;
		}
		
}

class XCache{//待测试

		function pconnect($host, $port){
		if(extension_loaded('xcache'))
		return true;
		}
		
		function delete($Key){
		xcache_unset($Key);
		}
		
		function set($Key,$Value,$What,$Duration){
		xcache_set($Key,$Value,$Duration);
		}
		
		function get($Key){
		if (xcache_isset($Key))
			return xcache_get($Key);
		else 
			return false;
		}
		
}

class eAccelerator{//待测试

		function pconnect($host, $port){
		if(extension_loaded('eaccelerator'))
		return true;
		}

		
		function delete($Key){
		eaccelerator_rm($Key);
		}
		
		function set($Key,$Value,$What,$Duration){
		eaccelerator_put($Key,$Value,$Duration);
		}
		
		function get($Key){
		return eaccelerator_get($Key);
		}
		
}

class FileCache{ 
/*
cleanup.php

$dp = @opendir('cache');
while (($file = readdir($dp)) !== false){
if(time() - filemtime('cache/'.$file) > 24*3600)@unlink('cache/'.$file);
}
closedir($dp);
*/  
    private $lifetime = 3600;
    private $path = 'cache';
	
    function set($name,$value,$What,$time=0){
	    if($time) $this->lifetime = $time;
        $filename = $this->path.'/'.$name.'.cache';
        //@unlink($filename);
        $valuecache['cache'] = $value;
		$valuecache['cachetimeuntil']=time()+$this->lifetime;
        $array = "<?php\n\$filecache['".$name."']=".var_export($valuecache, true).";\n?>";
        $strlen = file_put_contents($filename, $array);
        @chmod($filename, 0777);
        return $strlen;
    } 

    function get($name){
			$filename = $this->path.'/'.($name).'.cache';
			if(!file_exists($filename))return false;
            include_once $filename;
			if($filecache[$name]['cachetimeuntil']>time())
            return $filecache[$name]['cache'];
			else
			return false;
   } 
	
    function delete($name){
        $filename = $this->path.'/'.($name).'.cache';
        @unlink($filename);
	} 
 	
    function pconnect(){
        return true;
    }
}

class SqlCache {
/*
CREATE TABLE `sqlcache` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `keyname` char(128) NOT NULL,
  `keyvalue` text NOT NULL,
  `deadtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyname` (`keyname`),
  KEY `deadtime` (`deadtime`)
) ENGINE=MyISAM AUTO_INCREMENT=5437 DEFAULT CHARSET=utf8;

//////////////////////////////////////////////////////////////
cleanup.php
sql_query("DELETE from sqlcache where deadtime <".time());
*/
	function pconnect(){
        return true;
    }

	function get($key) {
			$cache = sql_query("SELECT * FROM  sqlcache WHERE keyname=".sqlesc($key));
			if(!$cache)return false;
			$data = mysql_fetch_assoc($cache);
			
			if($data['deadtime'] < time())
				return false;
				
		return $data['keyvalue'];
	}

	function set($key, $value,$What, $life) {
	$key=sqlesc($key);
	$value=sqlesc($value);
	$life=sqlesc($life+time());
	return sql_query("INSERT INTO sqlcache (keyname,keyvalue,deadtime) VALUES ( $key , $value , $life ) ON DUPLICATE KEY update keyvalue=values(keyvalue) ,deadtime=values(deadtime) ");
	}

	function delete($key) {
		$key=sqlesc($key);
		return sql_query("DELETE from sqlcache where keyname = $key");
	}
}

class NoCache{//待测试
		function pconnect($host, $port){return true;}
		function delete($Key){}
		function set($Key,$Value,$What,$Duration){}
		function get($Key){}
}

class CACHE extends Memcache{
//WinCache{//Memcache{//APCCache{//XCache{//eAccelerator{//FileCache{//SqlCache{//NoCache
	var $isEnabled;
	var $clearCache = 0;
	var $language = 'chs';
	var $Page = array();
	var $Row = 1;
	var $Part = 0;
	var $MemKey = "";
	var $Duration = 0;
	var $cacheReadTimes = 0;
	var $cacheWriteTimes = 0;
	var $keyHits = array();
	var $languageFolderArray = array();
	
	var $keyPre="ANTSOUL_";	
	function _feaKey($key) {
		//return $key;
		return hash('sha256',$this->keyPre . $key);
	} 
	
	
	function connect($host, $port){
		return $this->pconnect($host, $port);
	}	

	function __construct($host = '127.0.0.1', $port = 11211) {
		$success = $this->pconnect($host, $port); // Connect to memcache
		if ($success) {
			$this->isEnabled = 1;
		} else {
			die("Fatal error: Class 'Memcache' not found in class_cache.php on line 11");
			$this->isEnabled = 0;
		}
		//$this->keyPre=$_SERVER["HTTP_HOST"];
	}
	
	function getIsEnabled() {
		return $this->isEnabled;
	}

	function setClearCache($isEnabled) {
		$this->clearCache = $isEnabled;
	}
	
	function getLanguageFolderArray() {
		return $this->languageFolderArray;
	}

	function setLanguageFolderArray($languageFolderArray) {
		$this->languageFolderArray = $languageFolderArray;
	}
	
	function setkeyPre($keyPre) {
		$this->keyPre = $keyPre;
	}

	function getClearCache() {
		return $this->clearCache;
	}

	function setLanguage($language) {
		$this->language = $language;
	}

	function getLanguage() {
		return $this->language;
	}

	function new_page($MemKey = '', $Duration = 3600, $Lang = true) {
		if ($Lang) {
			$language = $this->getLanguage();
			$this->MemKey = $language."_".$MemKey;
		} else {
			$this->MemKey = $MemKey;
		}
		$this->Duration = $Duration;
		$this->Row = 1;
		$this->Part = 0;
		$this->Page = array();
	}

	function set_key(){

	}

	//---------- Adding functions ----------//

	function add_row(){
		$this->Part = 0;
		$this->Page[$this->Row] = array();
	}

	function end_row(){
		$this->Row++;
	}

	function add_part(){
		ob_start();
	}

	function end_part(){
		$this->Page[$this->Row][$this->Part]=ob_get_clean();
		$this->Part++;
	}

	// Shorthand for:
	// add_row();
	// add_part();
	// You should only use this function if the row is only going to have one part in it (convention),
	// although it will theoretically work with multiple parts.
	function add_whole_row(){
		$this->Part = 0;
		$this->Page[$this->Row] = array();
		ob_start();
	}

	// Shorthand for:
	// end_part();
	// end_row();
	// You should only use this function if the row is only going to have one part in it (convention),
	// although it will theoretically work with multiple parts.
	function end_whole_row(){
		$this->Page[$this->Row][$this->Part]=ob_get_clean();
		$this->Row++;
	}

	// Set a variable that will only be availabe when the system is on its row
	// This variable is stored in the same way as pages, so don't use an integer for the $Key.
	function set_row_value($Key, $Value){
		$this->Page[$this->Row][$Key] = $Value;
	}

	// Set a variable that will always be available, no matter what row the system is on.
	// This variable is stored in the same way as rows, so don't use an integer for the $Key.
	function set_constant_value($Key, $Value){
		$this->Page[$Key] = $Value;
	}

	// Inserts a 'false' value into a row, which breaks out of while loops.
	// This is not necessary if the end of $this->Page is also the end of the while loop.
	function break_loop(){
		if(count($this->Page)>0){
			$this->Page[$this->Row] = FALSE;
			$this->Row++;
		}
	}
	
	//---------- Locking functions ----------//
	
	// These functions 'lock' a key.
	// Users cannot proceed until it is unlocked.
	
	function lock($Key){
		$this->cache_value('lock_'.$Key, 'true', 3600);
	}
	
	function unlock($Key) {
		$this->delete($this->_feaKey('lock_'.$Key));
	}
	
	//---------- Caching functions ----------//

	// Cache $this->Page and resets $this->Row and $this->Part
	function cache_page(){
		$this->cache_value($this->MemKey,$this->Page, $this->Duration);
		$this->Row = 0;
		$this->Part = 0;
	}

	// Exact same as cache_page, but does not store the page in cache
	// This is so that we can use classes that normally cache values in
	// situations where caching is not required
	function setup_page(){
		$this->Row = 0;
		$this->Part = 0;
	}

	// Wrapper for Memcache::set, with the zlib option removed and default duration of 1 hour
	function cache_value($Key, $Value, $Duration = 3600){
		//$this->set($this->_feaKey($Key),($Value), 0, $Duration);
		$this->set($this->_feaKey($Key),serialize($Value), 0, $Duration);
		$this->cacheWriteTimes++;
		$this->keyHits['write'][$Key] = !$this->keyHits['write'][$Key] ? 1 : $this->keyHits['write'][$Key]+1;
	}

	//---------- Getting functions ----------//

	// Returns the next row in the page
	// If there's only one part in the row, return that part.
	function next_row(){
		$this->Row++;
		$this->Part = 0;
		if($this->Page[$this->Row] == false){
			return false;
		}
		elseif(count($this->Page[$this->Row]) == 1){
			return $this->Page[$this->Row][0];
		}
		else {
			return $this->Page[$this->Row];
		}
	}

	// Returns the next part in the row
	function next_part(){
		$Return = $this->Page[$this->Row][$this->Part];
		$this->Part++;
		return $Return;
	}

	// Returns a 'row value' (a variable that changes for each row - see above).
	function get_row_value($Key){
		return $this->Page[$this->Row][$Key];
	}

	// Returns a 'constant value' (a variable that doesn't change with the rows - see above)
	function get_constant_value($Key){
		return $this->Page[$Key];
	}

	// If a cached version of the page exists, set $this->Page to it and return true.
	// Otherwise, return false.
	function get_page($mustreflush=false){
		$Result = $this->get_value($this->MemKey);
		if($Result&&!$mustreflush){
			$this->Row = 0;
			$this->Part = 0;
			$this->Page = $Result;
			return true;
		} else {
			return false;
		}
	}

	// Wrapper for Memcache::get. Why? Because wrappers are cool.
	function get_value($Key) {
		if($this->getClearCache()){
			$this->delete_value($this->_feaKey($Key));
			return false;
		}
		// If we've locked it
		// Xia Zuojie: we disable the following lock feature 'cause we don't need it and it doubles the time to fetch a value from a key
		/*while($Lock = $this->get('lock_'.$Key)){
			sleep(2);
		}*/

		//$Return = ($this->get($this->_feaKey($Key)));
		$Return = unserialize($this->get($this->_feaKey($Key)));
		$this->cacheReadTimes++;
		$this->keyHits['read'][$Key] = !$this->keyHits['read'][$Key] ? 1 : $this->keyHits['read'][$Key]+1;
		return $Return;
	}

	// Wrapper for Memcache::delete. For a reason, see above.
	function delete_value($Key, $AllLang = false){
		if ($AllLang){
			$langfolder_array = $this->getLanguageFolderArray();
			foreach($langfolder_array as $lf)
				$this->delete($this->_feaKey($lf."_".$Key));
		}
		else {
			$this->delete($this->_feaKey($Key));
		}
	}

	function getCacheReadTimes() {
		return $this->cacheReadTimes;
	}

	function getCacheWriteTimes() {
		return $this->cacheWriteTimes;
	}
	
	function getKeyHits ($type='read') {
		return (array)$this->keyHits[$type];
	}
}
