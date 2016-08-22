<?php
require "include/bittorrent.php";
 
 
dbconn();
loggedinorreturn();

$data=array();

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
if( $CURUSER['class'] >= UC_POWER_USER)//if( $CURUSER['class'] >= UC_MODERATOR||get_bet_moderators_is() )
{
$HTMLOUT .= "<td align='center' class='navigation'><a href='/bet_admin.php'>管理竞猜</a></td>";
}
$HTMLOUT .="</tr></table><br />";

if($_GET['history']==1)$where=' where fix = 1 ';
else $where=' where fix = 0 ';

$a = sql_query("SELECT  heading,count(*) AS tnum,SUM(bonus) AS bsum,fix,active,betgames.id as gameid,endtime FROM  betgames LEFT JOIN bets ON gameid = betgames.id $where GROUP BY betgames.id ORDER BY fix,endtime") or sqlerr(__FILE__, __LINE__);

while($b = mysql_fetch_array($a)){
$data['text'][] ="'".$b['heading']."'";
$data['tnum'][] =0+$b['tnum'];
$data['bsum'][]=0+$b['bsum'];

if($b["fix"])$thistype='★';
elseif($b["endtime"]<time())$thistype='○';
elseif(!$b["active"])$thistype='●';
else $thistype='+';
$data['link'][]="'{$b['heading']}': '<a href=\"bet_gameinfo.php?showgames={$b['gameid']}\">$thistype</a>'";
}

stdhead('Betting');print  $HTMLOUT ;
if($a){ 
?>
<script src="javascript/highcharts.js"></script>
<script type="text/javascript">
$(function () {
    var chart;
	var ategoryLinks = {
	<?echo implode(',', $data['link'])?> 
    };
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'column',
				zoomType: 'x',
            },
            title: {
                text: '系统当前运行情况'
            },
			subtitle: {
                text: '左右拖动可以放大'
            },
            xAxis: {
                categories: [<?echo implode(',', $data['text'])?>],
				labels: {
                formatter: function() {
				return ategoryLinks[this.value];
            }
				}
            },        plotOptions: {
            column: {
                pointPadding: 0,
                //groupPadding: 0.1,
                borderWidth: 0, 
                shadow: false
            }
        },
            yAxis: [{
                title: {
                    text: '注数(<?echo array_sum($data['tnum'])?>)',
                },
            }, {
                title: {
                    text: '总额(<?echo array_sum($data['bsum'])?>)',
                },
                opposite: true
            }],
			tooltip: {
			shared: true,
             
            },
			exporting: {
				enabled: false
			},
            series: [ {
                name: '拥有注数',
                data: [<?echo implode(',', $data['tnum'])?>],
				yAxis: 0,
    
            },{
                name: '投注总额',
                data: [<?echo implode(',', $data['bsum'])?>],
				yAxis: 1,
    
            }]
        });
    });
    
});
		</script>
<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<?}

stdfoot();
?>