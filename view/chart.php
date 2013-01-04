<?php
    require_once '../admin_config.php';
    require_once '../include/jqplot-css.php';
?>
<div id="chart1" style="width:600px; height:300px"></div>
<div id="chart2" style="width:600px; height:300px"></div>
<script type="text/javascript">
//$(document).ready(function () {
//    var s1 = [[2002, 112000], [2003, 122000], [2004, 104000], [2005, 99000], [2006, 121000], 
//    [2007, 148000], [2008, 114000], [2009, 133000], [2010, 161000]];
//    var s2 = [[2002, 10200], [2003, 10800], [2004, 11200], [2005, 11800], [2006, 12400], 
//    [2007, 12800], [2008, 13200], [2009, 12600], [2010, 13100]];
// 
//    plot1 = $.jqplot("chart1", [s2, s1], {
//        // Turns on animatino for all series in this plot.
//        animate: true,
//        // Will animate plot on calls to plot1.replot({resetAxes:true})
//        animateReplot: true,
//        cursor: {
//            show: true,
//            zoom: true,
//            looseZoom: true,
//            showTooltip: false
//        },
//        series:[
//            {
//                pointLabels: {
//                    show: true
//                },
//                renderer: $.jqplot.BarRenderer,
//                showHighlight: false,
//                yaxis: 'y2axis',
//                rendererOptions: {
//                    // Speed up the animation a little bit.
//                    // This is a number of milliseconds.  
//                    // Default for bar series is 3000.  
//                    animation: {
//                        speed: 2500
//                    },
//                    barWidth: 15,
//                    barPadding: 0,
//                    barMargin: 0,
//                    highlightMouseOver: false
//                }
//            }, 
//            {
//                rendererOptions: {
//                    // speed up the animation a little bit.
//                    // This is a number of milliseconds.
//                    // Default for a line series is 2500.
//                    animation: {
//                        speed: 2000
//                    }
//                }
//            }
//        ],
//        axesDefaults: {
//            pad: 0
//        },
//        axes: {
//            // These options will set up the x axis like a category axis.
//            xaxis: {
//                renderer: $.jqplot.DateAxisRenderer
//                
//              }
//            },
//            yaxis: {
//                tickOptions: {
//                    formatString: "$%'d"
//                },
//                rendererOptions: {
//                    forceTickAt0: true
//                }
//            },
//            y2axis: {
//                tickOptions: {
//                    formatString: "$%'d"
//                },
//                rendererOptions: {
//                    // align the ticks on the y2 axis with the y axis.
//                    alignTicks: true,
//                    forceTickAt0: true
//                }
//            }
//        },
//        highlighter: {
//            show: true, 
//            showLabel: true, 
//            tooltipAxes: 'y',
//            sizeAdjust: 7.5 , tooltipLocation : 'ne'
//        }
//    });
//});

$(document).ready(function(){
        var s1 = [2116,3568,1733,4568,1411,42,0,0,4,0];
        var s2 = [100, 500, 300, 200];
        var ticks = ['2012-11-05', '2012-11-04', '2012-11-03', '2012-11-02','2012-11-01', '2012-10-31', '2012-10-30', '2012-10-29','2012-10-28','2012-10-27'];
        
        plot2 = $.jqplot('chart2', [s1,s1], {
            seriesDefaults: {
                renderer:$.jqplot.BarRenderer,
                pointLabels: { show: true }
            },
            series:[
                
                {
                    yaxis: 'y2axis'
                }
            ],
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    //tickRenderer: $.jqplot.DateAxisRenderer,
                    ticks: ticks,
                    tickOptions: {
                       angle: -45,  
                        formatString: "%d/%m"
                    }
                    //renderer: $.jqplot.DateAxisRenderer
                },
                yaxis: {
                    tickOptions: {
                        formatString: "%s"
                    },
                    rendererOptions: {
                        forceTickAt0: true
                    }
                },
                y2axis: {
                    tickOptions: {
                        formatString: "$%'d"
                    },
                    rendererOptions: {
                        // align the ticks on the y2 axis with the y axis.
                        alignTicks: true,
                        forceTickAt0: true
                    }
                }
            }
        });
    
    });
</script>
<?php
include '../include/jqplot-js.php';
?>