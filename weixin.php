<?php
require_once("include/bittorrent.php");
dbconn();

function resimsimi($keyword){
global $CURUSER;
if (!$keyword)return;
$header = array();
$header[]= 'Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, application/x-shockwave-flash, text/html, * '. '/* '; 
$header[]= 'Accept-Language: zh-cn '; 
$header[]= 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:13.0) Gecko/20100101 Firefox/13.0.1'; 
$header[]= 'Host: www.simsimi.com'; 
$header[]= 'Connection: Keep-Alive '; 
$header[]= 'Cookie: JSESSIONID='.hash('crc32',date("G",time()).$CURUSER['username']);

$Ref="http://www.simsimi.com/talk.htm?lc=ch";
$Ch = curl_init();
$Options = array(
CURLOPT_HTTPHEADER => $header,
CURLOPT_URL => 'http://www.simsimi.com/func/req?msg='.urlencode($keyword).'&lc=ch', 
CURLOPT_RETURNTRANSFER => true,
CURLOPT_REFERER => $Ref,
CURLOPT_CONNECTTIMEOUT_MS=>10*1000,
CURLOPT_TIMEOUT_MS=>50*1000,
);
curl_setopt_array($Ch, $Options);
$Message = json_decode(curl_exec($Ch),true);
curl_close($Ch);
if($Message['result']=='100')return $Message['response'];
else return "......";
}

define("TOKEN", "chenzhuyu"); 
$wechatObj = new wechatCallbackapiTest(); 
//$wechatObj->valid();//验证完成后可将此行代码注释掉 
$wechatObj->responseMsg(); 
   
class wechatCallbackapiTest 
{ 
    public function valid() 
    { 
        $echoStr = $_GET["echostr"]; 
   
        //valid signature , option 
        if($this->checkSignature()){ 
            echo $echoStr; 
            exit; 
        } 
    } 
   
    public function responseMsg() 
    { 
        //get post data, May be due to the different environments 
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"]; 
   
        //extract post data 
        if (!empty($postStr)){ 
                   
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA); 
                $fromUsername = $postObj->FromUserName; 
                $toUsername = $postObj->ToUserName; 
                $keyword = trim($postObj->Content); 
                $time = time(); 
                $textTpl = "<xml> 
                            <ToUserName><![CDATA[%s]]></ToUserName> 
                            <FromUserName><![CDATA[%s]]></FromUserName> 
                            <CreateTime>%s</CreateTime> 
                            <MsgType><![CDATA[%s]]></MsgType> 
                            <Content><![CDATA[%s]]></Content> 
                            <FuncFlag>0</FuncFlag> 
                            </xml>";              
                if(!empty( $keyword )) 
                { 
                    $msgType = "text"; 
                    $contentStr = resimsimi($keyword); 
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr); 
                    echo $resultStr; 
                }else{ 
                    echo "Input something..."; 
                } 
   
        }else { 
            echo ""; 
            exit; 
        } 
    } 
           
    private function checkSignature() 
    { 
        $signature = $_GET["signature"]; 
        $timestamp = $_GET["timestamp"]; 
        $nonce = $_GET["nonce"];     
                   
        $token = TOKEN; 
        $tmpArr = array($token, $timestamp, $nonce); 
        sort($tmpArr); 
        $tmpStr = implode( $tmpArr ); 
        $tmpStr = sha1( $tmpStr ); 
           
        if( $tmpStr == $signature ){ 
            return true; 
        }else{ 
            return false; 
        } 
    } 
} 
   
 
