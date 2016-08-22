<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

 

$HTMLOUT ="";

 
$HTMLOUT .="<a href='./bet.php'><img src='betting.png' alt='Bet' title='Betting' width='400' height='125' /></a>
<h1>博彩频道</h1>
<table class='main' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>当前竞猜</a></td>
<td align='center' class='navigation'><a href='/bet_history.php'>历史竞猜</a></td>
<td align='center' class='navigation'><a href='./bet_coupon.php'>我的押注</a></td>
<td align='center' class='navigation'><a href='./bet_bonustop.php'>用户排名</font></a></td>
<td align='center' class='navigation'><a href='./bet_info.php'>系统帮助</a></td>";
if( $CURUSER['class'] >= UC_POWER_USER)//if( $CURUSER['class'] >= UC_MODERATOR ||get_bet_moderators_is())
{
$HTMLOUT .= "<td align='center' class='navigation'><a href='/bet_admin.php'>管理竞猜</a></td>";
}
$HTMLOUT .="</tr></table><br />";


 

if(isset($_GET['showgames'])){
$gameid = 0+$_GET['showgames'];

$c=@mysql_fetch_assoc(sql_query("SELECT betgames.*, text, thisright FROM betgames LEFT JOIN betoptions ON gameid = betgames.id WHERE betgames.id =".sqlesc($gameid)." ORDER BY thisright DESC LIMIT 1"));

if($c['sort']==1)
$sort = " order by betoptions.id ASC";
else
$sort = " order by odds desc";

$a = sql_query("SELECT text, count(*) AS num, SUM(bonus) AS sum,odds FROM bets LEFT JOIN betoptions ON optionid = betoptions.id WHERE bets.gameid = ".sqlesc($gameid)." GROUP BY text $sort") or sqlerr(__FILE__, __LINE__);

while($b = mysql_fetch_array($a)){
$data['text'][] ="'".$b['text']."'";
$data['asum'] += $data['sum'][] =$b['sum'];
$data['anum'] += $data['num'][] =$b['num'];
$data['odds'][]=$b['odds'];
}
}

stdhead('Betting');print  $HTMLOUT ;
if($c){

if($c['fix'])$thisstat= '已结盘,正确选项是'.$c['text'];
elseif($c["endtime"]<time())$thisstat= '等待结盘';
elseif(!$c['active'])$thisstat= '等待开盘';
else $thisstat= '剩余时间:'.mkprettytime(($c['endtime']) - time());
?>
<script src="javascript/highcharts.js"></script>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'column'
            },
            title: {
                text: '<?echo $c['heading']?>'
            },
            subtitle: {
                text: '<?echo $c['undertext'].'(当前状态:'.$thisstat.')'?>'
            },
            xAxis: {
                categories: [<?echo $data['text']?implode(',', $data['text']):''?>]
            },
            yAxis: [{ 
                title: {
                    text: '注数(<?echo $data['anum']?>)',
                }
            }, {
                title: {
                    text: '魔力值(<?echo 0+$data['asum']?>)',
                },
                opposite: true
            }, {
                title: {
                    text: '赔率比例',
                },
                opposite: true
            }],
			tooltip: {
                shared: true,
            },
			exporting: {
				enabled: false
			},
            series: [{
                name: '总下注数',
                data: [<?echo $data['num']?implode(',', $data['num']):''?>],
				yAxis: 0,
    
            }, {
                name: '投注总额',
                data: [<?echo $data['sum']?implode(',', $data['sum']):''?>],
				yAxis: 1,
    
            },{
                name: '赔率比例',
                data: [<?echo $data['odds']?implode(',', $data['odds']):''?>],
				yAxis: 2,
    
            }]
        });
    });
    
});
		</script>
<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<?}
print "<h1><a href='/bet_coupong.php?id=".$c["id"]."'>".htmlspecialchars(" < 下注情况 > ")."</a>".($c['active']?"<a href='/bet_odds.php?id=".$c["id"]."'>".htmlspecialchars(" < 点击下注 > ")."</a>":"").($c['fix']?"<a href='/forums.php?action=viewtopic&topicid=".$c["topicid"]."'>".htmlspecialchars(" < 论坛讨论 > ")."</a>":"")."</h1>";


stdfoot();
?>