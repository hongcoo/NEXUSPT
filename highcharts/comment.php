<?
if(!defined('IN_highcharts'))die;
$Cache->new_page('highcharts_comment.php_'.$wheretype, 3560*3);
if (!$Cache->get_page()){
$Cache->add_whole_row();
$retopicsum =0;
		$res=sql_query("SELECT count(*) AS num, date_format(added,'%Y,%m-1,%d') AS addedf FROM comments where added > $searchwheredate GROUP BY addedf");
while ($row = mysql_fetch_array($res))$retorrent[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			$retorrentsum = get_row_count("comments","where added > $searchwheredate");
			
			$res=sql_query("SELECT count(*) as num ,date_format(added,'%Y,%m-1,%d')as addedf FROM posts  where added > $searchwheredate GROUP BY  addedf");
while ($row = mysql_fetch_array($res))$repost[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			$repostsum = get_row_count("posts","where added > $searchwheredate");
			
			$res=sql_query("SELECT count(*) AS num, date_format(added,'%Y,%m-1,%d') AS addedf FROM topics LEFT JOIN posts ON firstpost=posts.id where added > $searchwheredate GROUP BY addedf");
while ($row = mysql_fetch_array($res)){$retopic[] ='[Date.UTC('.$row['addedf'].'),'.$row['num'].']';
			$retopicsum += $row['num'];
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
                text: '当日发帖回帖(不含删除帖子)'
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
                    text: '发帖'
                }, min: 0
            },{
                title: {
                    text: '回帖',
                }, min: 0,
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

                name: '主题数目',
                data: [<?echo @implode(',', $retopic)?>],
				yAxis: 0,
            },{

                name: '论坛回帖',
                data: [<?echo @implode(',', $repost)?>],
				yAxis: 1,
            },{

                name: '种子回复',
                data: [<?echo @implode(',', $retorrent)?>],
				yAxis: 1,
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