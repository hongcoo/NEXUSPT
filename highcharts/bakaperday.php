<?
if(!defined('IN_highcharts'))die;
$Cache->new_page('highcharts_bakaperday.php_'.$wheretype, 3560*3);
if (!$Cache->get_page()){
$Cache->add_whole_row();
		$res=sql_query("SELECT count(*) AS num, FROM_UNIXTIME(daytime, '%Y,%m-1,%d') AS addedf FROM bakaperday where daytime > $searchwheretime GROUP BY addedf");
while ($row = mysql_fetch_array($res)){
			$retu[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
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
                text: '每日签到统计'
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
                    text: '人数',
                },
				min:0
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

                name: '签到人数',
                data: [<?echo @implode(',', $retu)?>],
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