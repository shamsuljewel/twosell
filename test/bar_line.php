<?php 
require_once '../include/jqplot-css_1.php';
?>
<div id="chart1" style="margin-top:20px; margin-left:20px; width:800px; height:600px;"></div>
<script>
$(document).ready(function() {
    $.jqplot.config.enablePlugins = true;
//    var s1 = [ ['19/5',93219],['20/5',93219],['21/5',209742],['22/5',318392],['23/5',434915],['24/5',551438],['25/5',669477],['26/5',763907],['27/5',763907],['28/5',876835],['29/5',1040798],['30/5',1209873],['31/5',1388262],['1/6',1558778],['2/6',1711314],['3/6',1711314],['4/6',1891763],['5/6',2072211],['6/6',2252660],['7/6',2433109],['8/6',2584770],['9/6',2737306],['10/6',2737306],['11/6',2927976],['12/6',3113536],['13/6',3304206],['14/6',3510550],['15/6',3688129],['16/6',3853204],['17/6',3853204],['18/6',4059548],['19/6',4265892],['20/6',4457841],['21/6',4653964],['22/6',4850086] ]
//    var s2 = [ ['19/5',0],['20/5',0],['21/5',488814],['22/5',660642],['23/5',660642],['24/5',743570],['25/5',912585],['26/5',912585],['27/5',912585],['28/5',1134049],['29/5',1979167],['30/5',2285510],['31/5',2373220],['1/6',2408507],['2/6',2408507],['3/6',2408507],['4/6',2657460],['5/6',2958574],['6/6',3242003],['7/6',3242003],['8/6',3242003],['9/6',3242003],['10/6',3242003],['11/6',3242003],['12/6',3242003],['13/6',3242003],['14/6',3242003],['15/6',3242003],['16/6',3242003],['17/6',3242003],['18/6',3242003],['19/6',3242003],['20/6',3242003],['21/6',3242003],['22/6',3242003] ]
    var s3 = [ ['19',168960],['20/5',236868],['21/5',445012],['22/5',665070],['23/5',883847],['24/5',1084399],['25/5',1132923],['26/5',1149534],['27/5',1166236],['28/5',1179926],['29/5',1368435],['30/5',1560731],['31/5',1757273],['1/6',1957300],['2/6',2128185],['3/6',2196829],['4/6',2381927],['5/6',2562779],['6/6',2727602],['7/6',2919665],['8/6',3109542],['9/6',3273358],['10/6',3347347],['11/6',3529932],['12/6',3719408],['13/6',3906757],['14/6',4098234],['15/6',4243548],['16/6',4322222],['17/6',4384651],['18/6',4552196],['19/6',4552196],['20/6',4552196],['21/6',4552196],['22/6',4552196] ];
    var s4 = [ ['19',168960],['20/5',236868],['21/5',445012],['22/5',665070],['23/5',883847],['24/5',1084399],['25/5',1132923],['26/5',1149534],['27/5',1166236],['28/5',1179926],['29/5',1368435],['30/5',1560731],['31/5',1757273],['1/6',1957300],['2/6',2128185],['3/6',2196829],['4/6',2381927],['5/6',2562779],['6/6',2727602],['7/6',2919665],['8/6',3109542],['9/6',3273358],['10/6',3347347],['11/6',3529932],['12/6',3719408],['13/6',3906757],['14/6',4098234],['15/6',4243548],['16/6',4322222],['17/6',4384651],['18/6',4552196],['19/6',4552196],['20/6',4552196],['21/6',4552196],['22/6',4552196] ];
    var ticks = [ {label:'Call',pointLabels: { show: false },color:'#FF0000',renderer:$.jqplot.LineRenderer},{label:'Tally Cubes',pointLabels: { show: false },color:'#808080',renderer:$.jqplot.BarRenderer} ];
    var plot1 = $.jqplot('chart1', [s3,s4], {
        animate: !$.jqplot.use_excanvas,
        seriesDefaults:{
            renderer:eval($.jqplot.BarRenderer),
            rendererOptions: { 
                barMargin: 5 
            } 
        },
        highlighter: {
            lineWidthAdjust: 0,  
            show: true,
            fadeTooltip: false,
            sizeAdjust: 0,
            tooltipOffset: 0,
            tooltipAxes: 'y'
        },
        cursor: {
            show: true
        },
        series: ticks,
        legend: {
            show: true,
            placement: 'insideGrid',
            location: 'ne',
            pointLabels: { show:true }
        },
        axesDefaults: {        
            tickRenderer: $.jqplot.CanvasAxisTickRenderer,        
            tickOptions: {          
                fontSize: '11pt'  
            }    
        },
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                tickOptions: {          
                    angle: -50
                }    
            },
            yaxis: {
                pad: 1.05,
                min: 0,
                max: 5820103.2
            }
        }
    });
})
</script>
<?php
include '../include/jqplot-js_1.php';
?>