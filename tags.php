<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());

function insert_tag($name, $description, $syntax, $example, $remarks="")
{
	global $lang_tags;
	$result = format_comment($example);
	print("<p class=sub><b>$name</b></p>\n");
	print("<table class=main width=100% border=1 cellspacing=0 cellpadding=5>\n");
	print("<tr valign=top><td width=25%>".$lang_tags['text_description']."</td><td>$description\n");
	print("<tr valign=top><td>".$lang_tags['text_syntax']."</td><td><tt>$syntax</tt>\n");
	print("<tr valign=top><td>".$lang_tags['text_example']."</td><td><tt>$example</tt>\n");
	print("<tr valign=top><td>".$lang_tags['text_result']."</td><td>$result\n");
	if ($remarks != "")
		print("<tr><td>".$lang_tags['text_remarks']."</td><td>$remarks\n");
	print("</table>\n");
}

stdhead($lang_tags['head_tags']);
begin_main_frame();
begin_frame($lang_tags['text_tags']);
$test = $_POST["test"];
?>
<p><?php echo $lang_tags['text_bb_tags_note'] ?></p>

<form method=post action=?>
<textarea name=test cols=60 rows=3><?php print($test ? htmlspecialchars($test) : "")?></textarea>
<input type=submit style='height: 23px; margin-left: 5px' value=<?php echo $lang_tags['submit_test_this_code'] ?>>
</form>
<?php

if ($test != "")
  print("<p><hr>" . format_comment($test) . "</hr></p>\n");

  
 $Cache->new_page('tags_page', 3600, true);
if (!$Cache->get_page()){
$Cache->add_whole_row(); 
insert_tag(
	$lang_tags['text_bold'],
	$lang_tags['text_bold_description'],
	$lang_tags['text_bold_syntax'],
	$lang_tags['text_bold_example'],
	""
);

insert_tag(
	$lang_tags['text_italic'],
	$lang_tags['text_italic_description'],
	$lang_tags['text_italic_syntax'],
	$lang_tags['text_italic_example'],
	""
);

insert_tag(
	$lang_tags['text_underline'],
	$lang_tags['text_underline_description'],
	$lang_tags['text_underline_syntax'],
	$lang_tags['text_underline_example'],
	""
);

insert_tag(
	$lang_tags['text_color_one'],
	$lang_tags['text_color_one_description'],
	$lang_tags['text_color_one_syntax'],
	$lang_tags['text_color_one_example'],
	$lang_tags['text_color_one_remarks']
);

insert_tag(
	$lang_tags['text_color_two'],
	$lang_tags['text_color_two_description'],
	$lang_tags['text_color_two_syntax'],
	$lang_tags['text_color_two_example'],
	$lang_tags['text_color_two_remarks']
);

insert_tag(
	$lang_tags['text_size'],
	$lang_tags['text_size_description'],
	$lang_tags['text_size_syntax'],
	$lang_tags['text_size_example'],
	$lang_tags['text_size_remarks']
);

insert_tag(
	$lang_tags['text_font'],
	$lang_tags['text_font_description'],
	$lang_tags['text_font_syntax'],
	$lang_tags['text_font_example'],
	$lang_tags['text_font_remarks']
);

insert_tag(
	"文字添加删除线",
	"文字特殊装饰显示",
	"[s]代码[/s]",
	"[s]放开那只真红酱[/s]"
);

insert_tag(
	"分界线",
	"文字特殊装饰显示",
	"[hr] [hr=标题]",
	"[hr] [hr=分界线]"
);

insert_tag(
	"文字滚动",
	"文字特殊装饰显示, 来回滚动",
	"[f]代码[/f]",
	"[f]交大男女七比一,一对情侣三对基![/f]"	
);
insert_tag(
	"文字向左飞行",
	"文字特殊装饰显示",
	"[fl]代码[/f]",
	"[fl]金发双马尾绝对领域[/f]"
);

insert_tag(
	"文字向右飞行",
	"文字特殊装饰显示",
	"[fr]代码[/f]",
	"[fr]金发双马尾绝对领域[/f]"
);

insert_tag(
	$lang_tags['text_hyperlink_one'],
	$lang_tags['text_hyperlink_one_description'],
	$lang_tags['text_hyperlink_one_syntax'],
	$lang_tags['text_hyperlink_one_example'],
	$lang_tags['text_hyperlink_one_remarks']
);

insert_tag(
	$lang_tags['text_hyperlink_two'],
	$lang_tags['text_hyperlink_two_description'],
	$lang_tags['text_hyperlink_two_syntax'],
	$lang_tags['text_hyperlink_two_example'],
	$lang_tags['text_hyperlink_two_remarks']
);

insert_tag(
	$lang_tags['text_image_one'],
	$lang_tags['text_image_one_description'],
	$lang_tags['text_image_one_syntax'],
	$lang_tags['text_image_one_example'],
	$lang_tags['text_image_one_remarks']
);

insert_tag(
	$lang_tags['text_image_two'],
	$lang_tags['text_image_two_description'],
	$lang_tags['text_image_two_syntax'],
	$lang_tags['text_image_two_example'],
	$lang_tags['text_image_two_remarks']
);

insert_tag(
	$lang_tags['text_quote_one'],
	$lang_tags['text_quote_one_description'],
	$lang_tags['text_quote_one_syntax'],
	$lang_tags['text_quote_one_example'],
	""
);

insert_tag(
	$lang_tags['text_quote_two'],
	$lang_tags['text_quote_two_description'],
	$lang_tags['text_quote_two_syntax'],
	$lang_tags['text_quote_two_example'],
	""
);

insert_tag(
	$lang_tags['text_list'],
	$lang_tags['text_description'],
	$lang_tags['text_list_syntax'],
	$lang_tags['text_list_example'],
	""
);

insert_tag(
	$lang_tags['text_preformat'],
	$lang_tags['text_preformat_description'],
	$lang_tags['text_preformat_syntax'],
	$lang_tags['text_preformat_example'],
	""
);

insert_tag(
	$lang_tags['text_code'],
	$lang_tags['text_code_description'],
	$lang_tags['text_code_syntax'],
	$lang_tags['text_code_example'],
	""
);

insert_tag(
	"代码2",
	"文字特殊装饰显示,里面内容不会当作标签",
	"[c]代码[/c]",
	"[c][b][i]我的地盘,我做主[/i][/b][/c]"
	
);

/*insert_tag(
	$lang_tags['text_you'],
	$lang_tags['text_you_description'],
	$lang_tags['text_you_syntax'],
	$lang_tags['text_you_example'],
	$lang_tags['text_you_remarks']
);*/

insert_tag(
	"种子引用",
	"显示种子名称并且连接到种子页面",
	"[sid种子编号]",
	"[sid5]",
	"ID不正确会报错"
);
insert_tag(
	"帖子引用",
	"显示帖子名称并且连接到帖子页面",
	"[tid帖子编号]",
	"[tid1]",
	"ID不正确会报错"
);
insert_tag(
	"求种引用",
	"显示求种名称并且连接到求种页面",
	"[rid求种编号]",
	"[rid1]",
	"ID不正确会报错"
);
insert_tag(
	"剧透码",
	"剧透(spoiler)代码可以显示一段黑色背景颜色的文字，只有当用户选中该文字时，文字才可见，以提供给用户一个是否查看该内容的选择。",
	"[mask]隐藏内容[/mask]",
	"[mask]大家都[s]不[/s]是萝莉控[/mask]"
);
insert_tag(
	"用户引用",
	"显示用户名称并且连接到用户页面",
	"[@用户编号]或[uid用户编号]或[reply:用户编号]",
	"[@0]         [uid0]        [@1]       [uid1]       [reply:1]",
	"@,reply到自己颜色为彩色(前提是你的名字本身就有颜色)"
);
insert_tag(
	"IMDB引用",
	"显示IMDB/豆瓣编号对应的种子名称并且连接到种子页面",
	"[imdb编号]",
	"[imdb0111161]",
	"1:最多显示5个活种种子,按照做种数排序,显示编号类型(IMDB/豆瓣)<br>2:编号依据豆瓣网,因此可能有些链接对应不了<br>"
);
insert_tag(
	"竞猜引用",
	"显示竞猜名称并且连接到竞猜页面",
	"[bid竞猜编号]",
	"[bid5]",
	"ID不正确会报错"
);

insert_tag(
	$lang_tags['text_site'],
	$lang_tags['text_site_description'],
	$lang_tags['text_site_syntax'],
	$lang_tags['text_site_example'],
	""
);

insert_tag(
	$lang_tags['text_siteurl'],
	$lang_tags['text_siteurl_description'],
	$lang_tags['text_siteurl_syntax'],
	$lang_tags['text_siteurl_example'],
	""
);

insert_tag(
	$lang_tags['text_flash'],
	$lang_tags['text_flash_description'],
	$lang_tags['text_flash_syntax'],
	$lang_tags['text_flash_example'],
	""
);

insert_tag(
	$lang_tags['text_flash_two'],
	$lang_tags['text_flash_two_description'],
	$lang_tags['text_flash_two_syntax'],
	$lang_tags['text_flash_two_example'],
	""
);

insert_tag(
	$lang_tags['text_flv_one'],
	$lang_tags['text_flv_one_description'],
	$lang_tags['text_flv_one_syntax'],
	$lang_tags['text_flv_one_example'],
	""
);

insert_tag(
	$lang_tags['text_flv_two'],
	$lang_tags['text_flv_two_description'],
	$lang_tags['text_flv_two_syntax'],
	$lang_tags['text_flv_two_example'],
	""
);

/*
insert_tag(
	$lang_tags['text_youtube'],
	$lang_tags['text_youtube_description'],
	$lang_tags['text_youtube_syntax'],
	$lang_tags['text_youtube_example'],
	""
);

insert_tag(
	$lang_tags['text_youku'],
	$lang_tags['text_youku_description'],
	$lang_tags['text_youku_syntax'],
	$lang_tags['text_youku_example'],
	""
);

insert_tag(
	$lang_tags['text_tudou'],
	$lang_tags['text_tudou_description'],
	$lang_tags['text_tudou_syntax'],
	$lang_tags['text_tudou_example'],
	""
);
if ($cc98holder == 'yes')
insert_tag(
	$lang_tags['text_ninety_eight_image'],
	$lang_tags['text_ninety_eight_image_description'],
	$lang_tags['text_ninety_eight_image_syntax'],
	$lang_tags['text_ninety_eight_image_example'],
	$lang_tags['text_ninety_eight_image_remarks']
);*/
$Cache->end_whole_row();
$Cache->cache_page();
}
echo $Cache->next_row();
end_frame();
end_main_frame();
stdfoot();
?>
