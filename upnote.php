<?php
require_once("include/bittorrent.php");
dbconn();
//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

$id = 0 + (int)$_GET["id"];
$cat =0 + (int)$_GET["cat"];
$secondvalue =0 + (int)$_GET["secondvalue"];

$secondtype = searchbox_item_list("audiocodecs",$cat);
$secondsize = count($secondtype,0);
for($i=0; $i<$secondsize; $i++)	$cachearray[] = $secondtype[$i]['id'];
if ($secondsize>0&&!in_array($secondvalue, $cachearray))
$secondvalue=0;


$sourcehide=false;//地区
$mediumhide=false;//运行平台
$standardhide=false;//分辨率
$processinghide=false;//连载状况
$teamhide=false;//编码


switch ($id){
				case 401: //电影
					$mediumhide=true;$processinghide=true;
					$printcat1=("[名称][上映时间]");
					$printcat2=("[电影类型][字幕/语言]");
					break;
				case 410 : //游戏
					$standardhide=true;$processinghide=true;$teamhide=true;
					$printcat1=("[名称][游戏类型(英文)][游戏语言]");
					$printcat2=("[制作公司][其它信息][格式]");
					switch ($secondvalue){
						case"30":$printcat3= "";break;//网络游戏
						case"32":$printcat3= "";break;//单机游戏
						case"31":$printcat3= "";break;//掌机游戏
						case"11":$printcat3= "";break;//视频解说
						case"53":$printcat3= "游戏CG,图片,原声等资源";break;//游戏视听
						case"55":$printcat3= "游戏攻略也可以，也可以上传到对应资源的附件中";break;//补丁攻略
							}
					break;
				case 402: //电视剧
					$mediumhide=true;
					$printcat1=("[名称][季度集数S.E.]");
					$printcat2=("[语言字幕][其他信息]");
					break;					
				case 403 : //综艺
					$mediumhide=true;$processinghide=true;
					$printcat1=("[节目日期][名称]");
					$printcat2=("[字幕/语言]");
						switch ($secondvalue){
						case"19":$printcat3= "";break;//综艺娱乐
						case"20":$printcat3= "";break;//新闻综合
						case"21":$printcat3= "";break;//晚会典礼
						case"22":$printcat3= "";break;//科教节目
						case"23":$printcat3= "";break;//艺人合集
						}
					break;					
				case 405: //动漫
					$mediumhide=true;
					$printcat1=("[名称][集数][字幕组][其他信息] ");
					$printcat2=("[罗马音或英文名][日文名]");
					switch ($secondvalue){
						case"12":$printcat3= "";break;//连载动画
						case"13":$printcat3= "";break;//完结动画
						case"14":$printcat3= "";break;//剧场OVA
						case"15":$mediumhide=false;$printcat3= "仅限ACG资源";break;//游戏相关
						case"16":$printcat3= "";break;//音乐声优
						case"17":$printcat3= "仅限ACG资源";break;//漫画画集
						case"18":$printcat3= "";break;//其他资源
					}
					break;
				case 404:case 419: //纪录片
					$mediumhide=true;
					$printcat1=("[名称][季度集数S.E.] ");
					$printcat2=("[格式]");
					switch ($secondvalue){					
						case"34":$printcat3= "Discovery";break;//探索频道
						case"33":$printcat3= "National Geographic";break;//国家地理
						case"35":$printcat3= "";break;//CCTV
						case"36":$printcat3= "";break;//BBC
						case"37":$printcat3= "";break;//其他
						}
						break;
				case 408 : //音乐
					$mediumhide=true;$processinghide=true;$teamhide=true;
					$printcat1=("[名称][艺术家][资源名称][发行时间]");
					$printcat2=("[BK][格式]");
					break;
				case 411 : //软件
					$standardhide=true;$processinghide=true;$teamhide=true;
					$printcat1=("[名称][软件版本][软件语言]");
					$printcat2=("[授权信息][安装信息][其它信息][格式]");
					break;					
				case 407 : //体育
					$mediumhide=true;
					$printcat1=("[日期][名称][类别]");
					switch ($secondvalue){					
							case"25":$printcat3= "";break;//篮球
							case"26":$printcat3= "";break;//足球
							case"27":$printcat3= "";break;//网球
							case"28":$printcat3= "";break;//台球
							case"29":$printcat3= "";break;//其他
							}
					break;
				case 406 : //MV
					$mediumhide=true;$processinghide=true;
					$printcat1=("[名称][艺术家][发行时间]");
					break;
				case 412 : //学习
					$processinghide=true;
					$printcat1=("[名称][类型][学科分类]");
					switch ($secondvalue){					
								case"38":$printcat3= "";break;//自然科学
								case"39":$printcat3= "";break;//人文社科
								case"40":$printcat3= "";break;//外语相关
								case"41":$printcat3= "";break;//工程科学
								case"42":$printcat3= "";break;//电子杂志
								case"43":$printcat3= "";break;//讲座演讲
								case"68":$printcat3= "";break;//公开课视频
								case"44":$printcat3= "";break;//考研相关
								case"45":$printcat3= "";break;//计算机类
								case"47":$printcat3= "";break;//经济管理
								case"54":$printcat3= "自拍电影之类的";break;//学生原创
								case"46":$printcat3= "";break;//其他资料
								}
					break;
				case 409 : //其他
					$printcat1=("[名称][类型]");
					$printcat2=("[格式]");
					switch ($secondvalue){					
									case"48":$printcat3= "";break;//图片收藏
									case"49":$printcat3= "";break;//视频拾遗
									case"50":$printcat3= "";break;//音频集萃
									case"51":$printcat3= "";break;//文本珍存
									case"52":$printcat3= "";break;//其他资源
									}
					break;
				case 415 : //不规范
					$printcat1=("<b><font color=\"red\">分类为不规范的种子将不会移动到种子区</font></b> ");
					$printcat2=("<b><font color=\"red\">请规范种子名称,介绍后,修改分类为新手试种</font></b>");
					$printcat3= ("<b>请规范种子名称,介绍后,修改分类为<font color=\"red\">新手试种</font></b>");
					break;
				case 416 : //新手试种
					$printcat1=("<b><font color=\"red\">请按要求规范填写,不规范的种子将被删除</font></b>");
					switch ($secondvalue){					
										case "56":
										$printcat1=("[名称][上映时间]");
										$printcat2=("[电影类型][字幕/语言]");
										$mediumhide=true;$processinghide=true;
										break;
										case "65":
										$printcat1=("[名称][游戏类型(英文)][游戏语言]");
										$printcat2=("[制作公司][其它信息][格式]");
										$standardhide=true;$processinghide=true;$teamhide=true;
										break; 
										case "57":
										$printcat1=("[名称][季度集数S.E.]");
										$printcat2=("[语言字幕][其他信息]");
										$mediumhide=true;
										break;
										case "58":
										$printcat1=("[节目日期][名称]");
										$printcat2=("[字幕/语言]");
										$mediumhide=true;$processinghide=true;
										break;
										case "60":
										$printcat1=("[名称][集数][字幕组][其他信息] ");
										$printcat2=("[罗马音或英文名][日文名]");
										$mediumhide=true;
										break;
										case "59":
										$printcat1=("[名称][季度集数S.E.] ");
										$printcat2=("[格式]");
										$mediumhide=true;
										break; 
										case "63":
										$printcat1=("[名称][艺术家][资源名称][发行时间]");
										$printcat2=("[BK][格式]");
										$mediumhide=true;$processinghide=true;$teamhide=true;
										break;
										case "66":
										$printcat1=("[名称][软件版本][软件语言]");
										$printcat2=("[授权信息][安装信息][其它信息][格式]");
										$standardhide=true;$processinghide=true;$teamhide=true;
										break;
										case "62":
										$printcat1=("[日期][名称][类别]");
										$mediumhide=true;
										break;
										case "61":
										$printcat1=("[名称][艺术家][发行时间]");
										$mediumhide=true;$processinghide=true;
										break;
										case "67":
										$printcat1=("[名称][类型][学科分类]");
										$processinghide=true;
										break;
										case "64":
										$printcat1=("[名称][类型]");
										$printcat2=("[格式]");
										break;
										default:$sourcehide=true;$mediumhide=true;$standardhide=true;$processinghide=true;$teamhide=true;break;
										}
					break;
				default:
					$sourcehide=true;$mediumhide=true;$standardhide=true;$processinghide=true;$teamhide=true;
					$printcat1=("<b><font color=\"red\">请按要求规范填写,不规范的种子将被删除</font></b>");
					$printcat2=("标题过长的部分可以添加到这里");
					$printcat3= ("<b>若种子包含文件数太多,建议<font color=\"red\">打包以后再上传</font></b>");
					break;
				}
	

	
print "{printcat1:'$printcat1',printcat2:'$printcat2',printcat3:'$printcat3',secondvalue:'$secondvalue',sourcehide:'$sourcehide',mediumhide:'$mediumhide',standardhide:'$standardhide',processinghide:'$processinghide',teamhide:'$teamhide'}";
?>
