<?
if(!defined('IN_highcharts'))die;
$Cache->new_page('highcharts_signup.php_'.$wheretype, 3560*3);
if (!$Cache->get_page()){
$Cache->add_whole_row();
	$rowinvited_by = get_row_count("users","WHERE invited_by != 0 AND added > $searchwheredate");
	$rowdeancheck = get_row_count("users","WHERE deancheck != 0 AND added > $searchwheredate");
	$rowsignupfree = get_row_count("users","WHERE invited_by = 0 and deancheck = 0 AND added > $searchwheredate");
	
	
		$res=sql_query("SELECT count(*) as num ,date_format(added,'%Y,%m-1,%d')as addedf FROM users where invited_by = 0 and deancheck = 0 AND added > $searchwheredate GROUP BY  addedf");
while ($row = mysql_fetch_array($res))
			$retfree[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			
$res=sql_query("SELECT count(*) as num ,date_format(added,'%Y,%m-1,%d')as addedf FROM users where invited_by != 0 AND added > $searchwheredate GROUP BY  addedf");
while ($row = mysql_fetch_array($res))
			$retinv[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			
$res=sql_query("SELECT count(*) as num ,date_format(added,'%Y,%m-1,%d')as addedf FROM users where deancheck != 0 AND added > $searchwheredate GROUP BY  addedf");
while ($row = mysql_fetch_array($res))
			$retdean[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			
$res=sql_query("SELECT count(*) as num ,date_format(added,'%Y,%m-1,%d')as addedf FROM users WHERE added > $searchwheredate GROUP BY  addedf");
while ($row = mysql_fetch_array($res)){
			$retall[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			$retallcount[] ='[Date.UTC('.$row['addedf'].'),'.($retallcountnum=$retallcountnum+$row['num']).']';
			
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
                text: '站点注册情况(不含被删除用户)'
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
            yAxis: {
                title: {
                    text: '注 册 人 数'
                }, min: 0
            },
            tooltip: {
                shared: true,
				crosshairs: true,
				xDateFormat :'%Y-%m-%d',
            },
			exporting: {
				enabled: false
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
                },
            },

    
            series: [			
			{
                name: '累计注册用户',
                data: [<?echo @implode(',', $retallcount)?>]
            },{	
                name: '自由注册',
                data: [<?echo @implode(',', $retfree)?>]
            },{
                name: '邀请注册',
                data: [<?echo @implode(',', $retinv)?>]
            },{
                name: '教网注册',
                data: [<?echo @implode(',', $retdean)?>]
            },{
                name: '总共注册',
                data: [<?echo @implode(',', $retall)?>]
            },{
                type: 'pie',
				name: '数目',
                data: [{
                    name: '自由注册',
                    y: <?echo $rowsignupfree?>,

                }, {
                    name: '邀请注册',
                    y: <?echo $rowinvited_by?>,
                }, {
                    name: '教务/网号注册',
                    y: <?echo $rowdeancheck?>,
                }],
                center: [150, 80],
                size: 100,
				}
            ]
        });
		chart.series[0].hide();  
    });
  
});


		</script>
		<?
$Cache->end_whole_row();
$Cache->cache_page();
}
echo $Cache->next_row();