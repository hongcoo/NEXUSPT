<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path("upload.php"));
loggedinorreturn();
parked();

$brsectiontype = $browsecatmode;


print("
function uplist(name,list) {

	var childRet = document.getElementById(name);
	for (var i = childRet.childNodes.length-1; i >= 0; i--) { 
		childRet.removeChild(childRet.childNodes.item(i)); 
	} 
	for (var j=0; j < list.length; j++) {
		var ret = document.createDocumentFragment();
		var newop = document.createElement('option');
		newop.id = list[j][0];
		newop.value = list[j][0]; 
		newop.appendChild(document.createTextNode(list[j][1])); 
		ret.appendChild(newop); 
		document.getElementById(name).appendChild(ret); 

	}

}
function secondtype() {
var value=document.getElementById('browsecat').value;
var secondvalue=document.getElementById('idaudiocodec_sel').value;
var idaudiocodec=document.getElementById('idaudiocodec_sel');//编码
var dispidaudiocodec=document.getElementById('dispidaudiocodec_sel');//编码
var idaudiocodecdl=false;//编码
var idaudiocodecdp='';//编码
if(!secondvalue)secondvalue=0;
switch(value){");

	$cats = genrelist($browsecatmode);
        foreach ($cats as $row){
	$catsid = $row['id'];
	$secondtype = searchbox_item_list("audiocodecs",$catsid);
	$secondsize = count($secondtype,0);
	if($secondsize>0){
	$cachearray = $cachearray."case \"".$catsid."\": uplist(\"idaudiocodec_sel\", new Array(['0','请选择子类型']";
	for($i=0; $i<$secondsize; $i++){
		$cachearray = $cachearray.",['".$secondtype[$i]['id']."','".$secondtype[$i]['name']."']";
	}
	$cachearray = $cachearray."));break;\n";}
	}
	
		$cats = genrelist($specialcatmode);
        foreach ($cats as $row){
	$catsid = $row['id'];
	$secondtype = searchbox_item_list("audiocodecs",$catsid);
	$secondsize = count($secondtype,0);
	if($secondsize>0){
	$cachearray = $cachearray."case \"".$catsid."\": uplist(\"idaudiocodec_sel\", new Array(['0','请选择子类型']";
	for($i=0; $i<$secondsize; $i++){
		$cachearray = $cachearray.",['".$secondtype[$i]['id']."','".$secondtype[$i]['name']."']";
	}
	$cachearray = $cachearray."));break;\n";}
	}


	print($cachearray);
print("
	default: idaudiocodecdl=true;break;
	}
if(idaudiocodecdl)idaudiocodecdp='none';
if(idaudiocodec){idaudiocodec.disabled=idaudiocodecdl;dispidaudiocodec.style.display=idaudiocodecdp;idaudiocodec.value=secondvalue}

}");



