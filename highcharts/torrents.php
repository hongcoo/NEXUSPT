<?
if(!defined('IN_highcharts'))die;
$Cache->new_page('highcharts_torrents.php_'.$wheretype, 3560*3);
if (!$Cache->get_page()){
$Cache->add_whole_row();
			
	$rowvn = get_row_count("torrents","WHERE visible ='no' AND added > $searchwheredate");
	$rowvy = get_row_count("torrents","WHERE visible ='yes' AND added > $searchwheredate");

	
		$res=sql_query("SELECT count(*) as num,SUM(size) AS ss,date_format(added,'%Y,%m-1,%d')as addedf FROM torrents WHERE added > $searchwheredate GROUP BY  addedf");
while ($row = mysql_fetch_array($res))
			{$ret[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			$retss[] ='[Date.UTC('.$row['addedf'].'),'.number_format($row['ss']/1024/1024/1024,2,'.','').']';
			}
			
			$res=sql_query("SELECT count(*) as num ,date_format(added,'%Y,%m-1,%d')as addedf FROM torrents  where visible ='no' AND added > $searchwheredate GROUP BY  addedf");
while ($row = mysql_fetch_array($res))
			$retvisiblen[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			
			$res=sql_query("SELECT count(*) as num ,date_format(added,'%Y,%m-1,%d')as addedf FROM torrents  where visible ='yes' AND added > $searchwheredate  GROUP BY  addedf");
while ($row = mysql_fetch_array($res))
			$retvisibley[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			
			
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
                text: '当日种子发布(不含删除的种子)'
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
                    text: '种 子 数 目'
                }, min: 0
            },{
                title: {
                    text: '种 子 体 积'
                }, min: 0,
				opposite: true,
				tooltip: {valueSuffix: ' GB',}
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

                name: '发种数',
                data: [<?echo @implode(',', $ret)?>]
            },{

                name: '种体积',
                data: [<?echo @implode(',', $retss)?>],
				yAxis: 1,
				tooltip: {valueSuffix: ' GB',}
            },{

                name: '活种数',
                data: [<?echo @implode(',', $retvisibley)?>]
            },{

                name: '断种数',
                data: [<?echo @implode(',', $retvisiblen)?>]
            },{
                type: 'pie',
				name: '数目',
                data: [{
                    name: '活种',
                    y: <?echo $rowvy?>,

                }, {
                    name: '断种',
                    y: <?echo $rowvn?>,
                }],
                center: [150, 80],
                size: 100,
				}
			]
        });
    });
    
});
		</script>
<?
$Cache->end_whole_row();
$Cache->cache_page();
}
echo $Cache->next_row();