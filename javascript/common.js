
function postvalid(form){     //错误已修复
	$('#qr').attr({'disabled':'disabled'});
	return true;
}
function dropmenu(obj){   //错误已修复
	var listid = '#' + obj.id + 'list';
	$(listid).css({"left":findPosition(obj)[0]});
	$(listid).toggle("normal");
}

function confirm_delete(id, note, addon)
{
   if(confirm(note))
   {
      self.location.href='?action=del'+(addon ? '&'+addon : '')+'&id='+id;
   }
}

//viewfilelist.js
/*
function viewfilelist(torrentid)
{
document.getElementById("filelist").innerHTML='载入中...';
document.getElementById("showfl").style.display = 'none';
document.getElementById("hidefl").style.display = 'block';
var result=ajax.gets('viewfilelist.php?id='+torrentid);
showlist(result);
}
function showlist(filelist)
{
document.getElementById("filelist").innerHTML=filelist;
}
*/

function viewfilelist(torrentid)
{
//document.getElementById("filelist").innerHTML='载入中...';
document.getElementById("showfl").style.display = 'none';
document.getElementById("hidefl").style.display = 'block';
jQuery.get("viewfilelist.php?",{id:torrentid}, function(data){document.getElementById("filelist").innerHTML=data;jQuery(document.getElementById("filelist")).slideDown();
$('#filelist table').tablesorter();
},"html"); 
}






function hidefilelist()
{
document.getElementById("hidefl").style.display = 'none';
document.getElementById("showfl").style.display = 'block';
jQuery(document.getElementById("filelist")).slideUp("normal");


}

//viewpeerlist.js

/*function viewpeerlist(torrentid)
{
document.getElementById("peerlist").innerHTML='载入中...';
document.getElementById("showpeer").style.display = 'none';
document.getElementById("hidepeer").style.display = 'block';
document.getElementById("peercount").style.display = 'none';
var list=ajax.gets('viewpeerlist.php?id='+torrentid);
document.getElementById("peerlist").innerHTML=list;
}*/

function viewpeerlist(torrentid)
{
//document.getElementById("peerlist").innerHTML="载入中...";
document.getElementById("showpeer").style.display = 'none';
document.getElementById("hidepeer").style.display = 'block';

jQuery.get("viewpeerlist.php?",{id:torrentid}, function(data){
document.getElementById("peerlist").innerHTML=data;
jQuery(document.getElementById("peercount")).slideUp();
jQuery(document.getElementById("peerlist")).slideDown();
$('#peerlist table').tablesorter();
},"html"); 
}

function viewsnatchslist(torrentid)
{
//document.getElementById("peerlist").innerHTML="载入中...";
document.getElementById("showpeer").style.display = 'none';
document.getElementById("hidepeer").style.display = 'block';

jQuery.get("viewsnatchelist.php?",{id:torrentid}, function(data){
document.getElementById("peerlist").innerHTML=data;
jQuery(document.getElementById("peercount")).slideUp();
jQuery(document.getElementById("peerlist")).slideDown();
$('#viewsnatcheslist table').tablesorter();
},"html"); 
}










function hidepeerlist()
{
document.getElementById("hidepeer").style.display = 'none';
jQuery(document.getElementById("peerlist")).slideUp();
jQuery(document.getElementById("peercount")).slideDown();
document.getElementById("showpeer").style.display = 'block';

}

// smileit.js

function SmileIT(smile,form,text){
if (typeof(doInsert) == "function") { 
   doInsert(smile, "", false);
} else{ 
   document.forms[form].elements[text].value = document.forms[form].elements[text].value+" "+smile+" ";
   document.forms[form].elements[text].focus();}
}

// saythanks.js

function saythanks_as(torrentid,ratednum)
{
//var list=ajax.posts('thanks.php','id='+torrentid+'&ratednum='+ratednum);
jQuery.post("thanks.php?",{id:torrentid,ratednum:ratednum}); 
document.getElementById("thanksbutton").innerHTML = document.getElementById("thanksadded").innerHTML;
document.getElementById("nothanks").innerHTML = "";
document.getElementById("addcuruser").innerHTML = document.getElementById("curuser").innerHTML;
//document.getElementById("ratedthanks").style.display = 'none';
}

// preview.js

function preview(obj) {
	var poststr = encodeURIComponent( document.getElementById("body").value );
	var result=ajax.posts('preview.php','body='+poststr);
	document.getElementById("previewouter").innerHTML=result;
	document.getElementById("previewouter").style.display = 'block';
	document.getElementById("editorouter").style.display = 'none';
	document.getElementById("unpreviewbutton").style.display = 'block';
	document.getElementById("previewbutton").style.display = 'none';
}

function unpreview(obj){
	document.getElementById("previewouter").style.display = 'none';
	document.getElementById("editorouter").style.display = 'block';
	document.getElementById("unpreviewbutton").style.display = 'none';
	document.getElementById("previewbutton").style.display = 'block';
}

// java_klappe.js
/*
function klappe(id)
{
var klappText = document.getElementById('k' + id);
var klappBild = document.getElementById('pic' + id);

if (klappText.style.display == 'none') {
 klappText.style.display = 'block';
 // klappBild.src = 'pic/blank.gif';
}
else {
 klappText.style.display = 'none';
 // klappBild.src = 'pic/blank.gif';
}
}

function klappe_news(id)
{
var klappText = document.getElementById('k' + id);
var klappText2 = document.getElementById('k2' + id);
var klappBild = document.getElementById('pic' + id);
var klappBild2 = document.getElementById('pic2' + id);

if (klappText.style.display == 'none') {
 klappText.style.display = '';
  if (klappText2)klappText2.style.display = 'none';
 klappBild.className = 'minus';
 if (klappBild2)klappBild2.className = 'minus';
}
else {
 klappText.style.display = 'none';
 if (klappText2)klappText2.style.display = '';
 klappBild.className = 'plus';
if (klappBild2)klappBild2.className = 'plus';
}
}
function klappe_ext(id)
{
var klappText = document.getElementById('k' + id);
var klappText2 = document.getElementById('k' + id +'2');
//var klappBild = document.getElementById('pic' + id);
//var klappPoster = document.getElementById('poster' + id);
if (klappText.style.display == 'none') {
 if (klappText2)klappText2.style.display = 'block';
 klappText.style.display = 'block';
 //klappPoster.style.display = 'block';
 //klappBild.className = 'minus';
}
else {
 if (klappText2)klappText2.style.display = 'none';
  klappText.style.display = 'none';
 //klappPoster.style.display = 'none';
 //klappBild.className = 'plus';
}
}
*/


function klappe(id)
{
var klappText = document.getElementById('k' + id);
var klappBild = document.getElementById('pic' + id);

if (klappText.style.display == 'none') {
 jQuery(klappText).slideDown();
 // klappBild.src = 'pic/blank.gif';
}
else {
 jQuery(klappText).slideUp();
 // klappBild.src = 'pic/blank.gif';
}
}

function klappe_news(id)
{
var klappText = document.getElementById('k' + id);
var klappText2 = document.getElementById('k2' + id);
var klappBild = document.getElementById('pic' + id);
var klappBild2 = document.getElementById('pic2' + id);



if (klappText.style.display == 'none') {

 jQuery(klappText).slideDown();
  if (klappText2)jQuery(klappText2).slideUp();
 klappBild.className = 'minus';
 if (klappBild2)klappBild2.className = 'minus';
}
else {
 jQuery(klappText).slideUp();
 if (klappText2)jQuery(klappText2).slideDown();
 klappBild.className = 'plus';
if (klappBild2)klappBild2.className = 'plus';
}
}


function klappe_search(id)
{
var klappText = document.getElementById('k' + id);
var klappText2 = document.getElementById('k2' + id);
var klappBild = document.getElementById('pic' + id);
var klappBild2 = document.getElementById('pic2' + id);

if (klappText.style.display == 'none') {
jQuery(klappText).show();
  if (klappText2)klappText2.style.display = 'none';
 klappBild.className = 'minus';
 if (klappBild2)klappBild2.className = 'minus';
}
else {
 jQuery(klappText).hide();
 if (klappText2)klappText2.style.display = '';
 klappBild.className = 'plus';
if (klappBild2)klappBild2.className = 'plus';
}
}



function klappe_ext(id)
{
var klappText = document.getElementById('k' + id);
var klappText2 = document.getElementById('k' + id +'2');
if (klappText.style.display == 'none') {
 if (klappText2)jQuery(klappText2).slideDown();
 jQuery(klappText).slideDown();
}
else {
 if (klappText2)jQuery(klappText2).slideUp();
  jQuery(klappText).slideUp();
}
}

// disableother.js

function disableother(select,target)
{
	if (document.getElementById(select).value == 0)
		document.getElementById(target).disabled = false;
	else {
	document.getElementById(target).disabled = true;
	document.getElementById(select).disabled = false;
	}
}

function disableother2(oricat,newcat)
{
	if (document.getElementById("movecheck").checked == true){
		document.getElementById(oricat).disabled = true;
		document.getElementById(newcat).disabled = false;
	}
	else {
		document.getElementById(oricat).disabled = false;
		document.getElementById(newcat).disabled = true;
	}
}

// ctrlenter.js
var submitted = false;
function ctrlenter(event,formname,submitname){
	if (submitted == false){
	var keynum;
	if (event.keyCode){
		keynum = event.keyCode;
	}
	else if (event.which){
		keynum = event.which;
	}
	if (event.ctrlKey && keynum == 13){
		submitted = true;
		document.getElementById(formname).submit();
		}
	}
}
function gotothepage(page){
var url=window.location.href;
var end=url.lastIndexOf("page");
url = url.replace(/#[0-9]+/g,"");
if (end == -1){
if (url.lastIndexOf("?") == -1)
window.location.href=url+"?page="+page;
else
window.location.href=url+"&page="+page;
}
else{
url = url.replace(/page=.+/g,"");
window.location.href=url+"page="+page;
}
}
function changepage(event){
var gotopage;
var keynum;
var altkey;
if (navigator.userAgent.toLowerCase().indexOf('presto') != -1)
altkey = event.shiftKey;
else altkey = event.altKey||event.ctrlKey||event.shiftKey;
if (event.keyCode){
	keynum = event.keyCode;
}
else if (event.which){
	keynum = event.which;
}
if(altkey && (keynum==33||keynum==37)){
if(currentpage<=0) return;
gotopage=currentpage-1;
gotothepage(gotopage);
}
else if (altkey && (keynum == 34||keynum==39)){
if(currentpage>=maxpage) return;
gotopage=currentpage+1;
gotothepage(gotopage);
}
}
if(window.document.addEventListener){
window.addEventListener("keydown",changepage,false);
}
else{
window.attachEvent("onkeydown",changepage,false);
}

// bookmark.js
function bookmark_topic(topicid,counter)
{
var result=ajax.posts('bookmark_topic.php','topicid='+topicid);
bmicon(result,counter);
}

function bookmark(torrentid,counter)
{
var result=ajax.posts('bookmark.php','torrentid='+torrentid);
bmicon(result,counter);
}
function bmicon(status,counter)
{
	if (status=="added")
		document.getElementById("bookmark"+counter).innerHTML="<img class=\"bookmark\" src=\"pic/trans.gif\" alt=\"Bookmarked\" />";
	else if (status=="deleted")
		document.getElementById("bookmark"+counter).innerHTML="<img class=\"delbookmark\" src=\"pic/trans.gif\" src=\"pic/trans.gif\" alt=\"Unbookmarked\" />";
}


function bookmarktext(torrentid,counter)
{
var result=ajax.posts('bookmark.php','torrentid='+torrentid);
bmicontext(result,counter);
}

function bmicontext(status,counter)
{
	if (status=="added"){
	document.getElementById("unbookmarked"+counter).style.display = 'none';
	document.getElementById("bookmarked"+counter).style.display = '';
}
else if (status=="deleted"){
	document.getElementById("bookmarked"+counter).style.display = 'none';
	document.getElementById("unbookmarked"+counter).style.display = '';}

}

// check.js
var checkflag = "false";
function check(field,checkall_name,uncheckall_name) {
	if (checkflag == "false") {
		for (i = 0; i < field.length; i++) {
			field[i].checked = true;}
			checkflag = "true";
			return uncheckall_name; }
			else {
				for (i = 0; i < field.length; i++) {
					field[i].checked = false; }
					checkflag = "false";
					return checkall_name; }
}

function checktocheck(field) {
	for (i = 0; i < field.length; i++) {
				if(field[i].checked)
					field[i].checked = false;
				else field[i].checked = true; }
}

// in torrents.php
var form='searchbox';
function SetChecked(chkName,ctrlName,checkall_name,uncheckall_name,start,count) {
	dml=document.forms[form];
	len = dml.elements.length;
	var begin;
	var end;
	if (start == -1){
	begin = 0;
	end = len;
	}
	else{
	begin = start;
	end = start + count;
	}
	var check_state;
	for( i=0 ; i<len ; i++) {
		if(dml.elements[i].name==ctrlName)
		{
			if(dml.elements[i].value == checkall_name)
			{
				dml.elements[i].value = uncheckall_name;
				check_state=1;
			}
			else
			{
				dml.elements[i].value = checkall_name;
				check_state=0;
			}
		}

	}
	for( i=begin ; i<end ; i++) {
		if (dml.elements[i].name.indexOf(chkName) != -1) {
			dml.elements[i].checked=check_state;
		}
	}
}

// funvote.js
function funvote(funid,yourvote)
{
var result=ajax.gets('fun.php?action=vote&id='+funid+"&yourvote="+yourvote);
if(result=="ok")voteaccept(yourvote);
}
function voteaccept(yourvote)
{
	if (yourvote=="fun" || yourvote=="dull"){
		document.getElementById("funvote").style.display = 'none';
		document.getElementById("voteaccept").style.display = 'block';
	}
}

// in upload.php
function getname()
{
var filename = document.getElementById("torrent").value;
var filename = filename.toString();
var lowcase = filename.toLowerCase();
var start = lowcase.lastIndexOf("\\"); //for Google Chrome on windows
if (start == -1){
start = lowcase.lastIndexOf("\/"); // for Google Chrome on linux
if (start == -1)
start == 0;
else start = start + 1;
}
else start = start + 1;
var end = lowcase.lastIndexOf("torrent");
var noext = filename.substring(start,end-1);
noext = noext.replace(/H\.264/ig,"H_264");
noext = noext.replace(/5\.1/g,"5_1");
noext = noext.replace(/2\.1/g,"2_1");
noext = noext.replace(/\./g," ");
noext = noext.replace(/H_264/g,"H.264");
noext = noext.replace(/5_1/g,"5.1");
noext = noext.replace(/2_1/g,"2.1");
document.getElementById("name").value=noext;
}

// in userdetails.php
/*function getusertorrentlistajax(userid, type, blockid)
{
if (document.getElementById(blockid).innerHTML==""){
document.getElementById(blockid).innerHTML='载入中...';
var infoblock=ajax.gets('getusertorrentlistajax.php?userid='+userid+'&type='+type);
document.getElementById(blockid).innerHTML=infoblock;
}
//return true;
}*/



function getusertorrentlistajax(userid,show,id)
{
var klappText = document.getElementById('k' + id);
var klappBild = document.getElementById('pic' + id);

if (klappText.style.display == 'none') {
 //klappText.style.display = '';
jQuery.get("getusertorrentlistajax.php?",{userid:userid,type:show}, function(data){klappText.innerHTML=data ;jQuery(klappText).slideDown(1000);},"html"); 
klappBild.className = 'minus';
}
else {
 //klappText.style.display = 'none';
  jQuery(klappText).slideUp(1000);
klappBild.className = 'plus';
}
}




// in functions.php
function get_ext_info_ajax(blockid,url,cache,type)
{
if (document.getElementById(blockid).innerHTML==""){
var infoblock=ajax.gets('getextinfoajax.php?url='+url+'&cache='+cache+'&type='+type);
document.getElementById(blockid).innerHTML=infoblock;
}
//return true;
}

// in userdetails.php
function enabledel(msg){
document.deluser.submit.disabled=document.deluser.submit.checked;
alert (msg);
}

function disabledel(){
document.deluser.submit.disabled=!document.deluser.submit.checked;
}

// in mybonus.php
function customgift()
{
if (document.getElementById("giftselect").value == '0'){
document.getElementById("giftselect").disabled = true;
document.getElementById("giftcustom").disabled = false;
}
}

function  notechangedis(name,hide){
var id=document.getElementById("id"+name+"_sel");
var dis=document.getElementById("dispid"+name+"_sel");
if(id){
if(hide){id.disabled =true;dis.style.display ='none';}
else{id.disabled =false;dis.style.display ='';}
}
}
function  notechangenote(name,html){
var id=document.getElementById(name);
if(id)id.innerHTML=html;
}

function notechange(){
var catid=document.getElementById("browsecat").value;
var secondvalue=document.getElementById("idaudiocodec_sel").value;
json = eval('(' +ajax.gets("upnote.php?id="+catid+"&secondvalue="+secondvalue) + ')');
notechangedis('source',json.sourcehide);
notechangedis('medium',json.mediumhide);
notechangedis('standard',json.standardhide);
notechangedis('processing',json.processinghide);
notechangedis('team',json.teamhide)
notechangenote('texttorrentnamenote',json.printcat1);
notechangenote('texttorrentsmaillnamenote',json.printcat2);
notechangenote('texttorrentsecondnote',json.printcat3);
document.getElementById('idaudiocodec_sel').value=json.secondvalue;
}




function IPV6mark()
{

if (confirm("重铸?"))
{
var result=ajax.gets('TRACKERSET.php');
if(result=='ok')document.getElementById("trackerstate").innerHTML= "检测中..." ;
}
}

function game()
{
	var listid = '#gamelist';
	$(listid).css({"left":$('#game').position().left,zIndex:10});
	$(listid).toggle(100);
}

function offersvoteplus(id,vote)
{
var result=ajax.posts("offersvote.php","id="+id+"&vote="+vote);
document.getElementById("offervotesresult"+id).innerHTML=result;
}

function customschool()
{
if (document.getElementById("schoolselect").value == '999'){
document.getElementById("schoolselect").disabled = true;
document.getElementById("schoolname").disabled = false;
}
}

var shoutboxstopreflag=1;

function shoutboxstopre(){ 

if(shoutboxstopreflag==1){
shoutboxstopreflag=0;
$('.shoutboxstopre').empty().append('刷新');
}
else{
shoutboxstopreflag=1;
$('.shoutboxstopre').empty().append('暂停');
}
$("#shoutboxwindows")[0].contentWindow.shoutboxstop(shoutboxstopreflag);

}


var shoutboxheightstatus=1;
function shoutboxheight(){

shoutboxstopreflag=1;
$('.shoutboxstopre').empty().append('暂停');

if (shoutboxheightstatus==1){
document.getElementById("shoutboxwindows").src='shoutbox.php?type=shoutbox&long=1';
document.getElementById("shbox").action='shoutbox.php?long=1';
$('#shoutboxwindows').stop(true,true).animate({height:'600'},200);
$('.shoutboxheight').empty().append('收起');
shoutboxheightstatus=0;
}else{
document.getElementById("shoutboxwindows").src='shoutbox.php?type=shoutbox&long=0';
document.getElementById("shbox").action='shoutbox.php?long=0';
$('#shoutboxwindows').stop(true,true).animate({height:'300'},200);
$('.shoutboxheight').empty().append('展开');
shoutboxheightstatus=1;
}

}

function quick_reply_to(userid)
{
    parent.document.getElementById("qrbody").value =  userid + " : "+parent.document.getElementById("qrbody").value;
}

function linestate(value){
document.cookie = "c_secure_user_link_online="+ escape (value) + ";";
}

$(function () {
//$('#mainmenu li .normalMenu').each(function(){ $(this).before($(this).clone().removeClass().addClass('active')); });
$('#mainmenu li').hover(
function(){$(this).find('.active').stop(true,true).animate({marginTop:'0px'},200);$(this).find('.active2').stop(true,true).slideDown(200); },
function(){$(this).find('.active').animate({marginTop:'-24px'},200);  $(this).find('.active2').slideUp(200);}
)
});

$(function () {
if($('#nav').length){
var _defautlTop = $('#nav').offset().top ;
var _defautlLeft = $('#nav').offset().left ;
var navfix=false;

$(window).scroll(function () { 
      if($(this).scrollTop() > _defautlTop){
      	if(!navfix){
      	$('#nav').css({ '-webkit-box-shadow': '0 0 20px #' + LinkEndColor ,'position': 'fixed', 'top': 0, 'left': (_defautlLeft- $(window).scrollLeft()), 'z-index': 99999 });
      	navfix=true;
      	}
      }else if(navfix){
      	$('#nav').css({ '-webkit-box-shadow': '','position': '', 'top': '', 'left': '', 'z-index': '' });
      	navfix=false;
      }
})
}
})

$(function () {
$('.transitionpic').hover(
function(){$(this).css({'-webkit-box-shadow':'0 0 20px #' + LinkEndColor})},
function(){$(this).css({'-webkit-box-shadow':''})})
})