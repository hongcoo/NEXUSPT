<?
if(!defined('IN_highcharts'))die;
$Cache->new_page('highcharts_bets.php_'.$wheretype, 3560*3);
if (!$Cache->get_page()){
$Cache->add_whole_row();
		$unum=0;
		$bsum=0;
		$res=sql_query("SELECT count(*) AS num, count(DISTINCT (userid)) as dunum , sum(bonus) as sum, FROM_UNIXTIME(date, '%Y,%m-1,%d' ) AS addedf FROM bets where date > $searchwheretime GROUP BY addedf");
while ($row = mysql_fetch_array($res)){
			$retu[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			$retb[] ='[Date.UTC('.$row['addedf'].'),'.$row['sum'].']';
			$retd[] ='[Date.UTC('.$row['addedf'].'),'.$row['dunum'].']';
			$unum += $row['num'];
			$bsum += $row['sum'];
			}
			
			?>
		<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
				type: 'spline',
                zoomType: 'x',
                spacingRight: 20
            },
            title: {
                text: '博彩每日统计'
            },
            subtitle: {
				text: '更新: <?echo date("Y-m-d H:i");?>',
            },
            xAxis: {
				 type: 'datetime',
               labels: {
			   formatter: function() {
                 return  Highcharts.dateFormat('%Y%m%d', this.value);
                }
				}
            },
            yAxis: [{
                title: {
                    text: '注数(<?echo $unum?>)',
                },
            },{
                title: {
                    text: '人数',
                },
            }, {
                title: {
                    text: '总额(<?echo $bsum?>)',
                },
                opposite: true
            }],
			exporting: {
				enabled: false
			},
            tooltip: {
                shared: true,
				crosshairs: true,
				xDateFormat :'%Y-%m-%d',
            },
            legend: {
                enabled: true
            },plotOptions: {
                spline: {
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 2
                        }
                    },
                    marker: {
                        enabled: true,
						radius: 3,
                        states: {
                            hover: {
                                enabled: true,
                                symbol: 'circle',
                                radius: 5,
                                lineWidth: 2
                            }
                        }
                    },
                }
            },

    
            series: [{

                name: '下注总数',
                data: [<?echo @implode(',', $retu)?>],
				yAxis: 0,
            },{

                name: '参与用户',
                data: [<?echo @implode(',', $retd)?>],
				yAxis: 1,
            },{

                name: '下注总额',
                data: [<?echo @implode(',', $retb)?>],
				yAxis: 2,
            }
			]
        });
    });
    
});
		</script><?
$Cache->end_whole_row();
$Cache->cache_page();
}
echo $Cache->next_row();