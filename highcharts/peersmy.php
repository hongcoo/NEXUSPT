<?
if(!defined('IN_highcharts'))die;
$userid=0+$CURUSER['id'];
if (get_user_class() >= UC_ADMINISTRATOR&&$_GET['userid'])$userid=0+$_GET['userid'];
$Cache->new_page('highcharts_peermy.php_'.$wheretype.'_'.$userid,3560*3);
if (!$Cache->get_page()){
$Cache->add_whole_row();
		$res=sql_query("SELECT date_format(timenow,'%Y,%m-1,%d') as timenowdate, count(*) AS usersum, SUM(truploaded) AS truploaded, SUM(imuploaded) AS imuploaded, SUM(trdownloaded) AS trdownloaded, SUM(imdownloaded) AS imdownloaded FROM peershis where userid = ".sqlesc(0+$userid)." and timenow > $searchwheredate GROUP BY timenowdate");


		while ($row = mysql_fetch_array($res)){
		$usersum[] ='[Date.UTC('.$row['timenowdate'].'),'.$row['usersum'].']';
		$truploaded[] ='[Date.UTC('.$row['timenowdate'].'),'.number_format($row['truploaded']/1024/1024/1024,2,'.','').']';
		$imuploaded[] ='[Date.UTC('.$row['timenowdate'].'),'.number_format($row['imuploaded']/1024/1024/1024,2,'.','').']';
		$trdownloaded[] ='[Date.UTC('.$row['timenowdate'].'),'.number_format($row['trdownloaded']/1024/1024/1024,2,'.','').']';
		$imdownloaded[] ='[Date.UTC('.$row['timenowdate'].'),'.number_format($row['imdownloaded']/1024/1024/1024,2,'.','').']';
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
                text: '<?echo get_plain_username($userid);?>的流量统计'
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
                    text: '上传量(GB)'
                }, min: 0
            },{
                title: {
                    text: '下载量(GB)'
                }, min: 0,
				opposite: true,
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

                name: '实际上传',
                data: [<?echo @implode(',', $truploaded)?>],
				yAxis: 0,
				tooltip: {valueSuffix: ' GB',}
            },{

                  name: '实际下载',
                data: [<?echo @implode(',', $trdownloaded)?>],
				yAxis: 1,
				tooltip: {valueSuffix: ' GB',}
            },{

                  name: '虚拟上传',
                data: [<?echo @implode(',', $imuploaded)?>],
				yAxis: 0,
				tooltip: {valueSuffix: ' GB',}
            },{

                  name: '虚拟下载',
                data: [<?echo @implode(',', $imdownloaded)?>],
				yAxis: 1,
				tooltip: {valueSuffix: ' GB',}
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