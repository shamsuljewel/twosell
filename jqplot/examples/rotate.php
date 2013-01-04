<!DOCTYPE html>

<html>
<head>
	
	<title>Rotated Labels and Font Styling</title>

    <link class="include" rel="stylesheet" type="text/css" href="../jquery.jqplot.min.css" />
    <link rel="stylesheet" type="text/css" href="examples.min.css" />
    <link type="text/css" rel="stylesheet" href="syntaxhighlighter/styles/shCoreDefault.min.css" />
    <link type="text/css" rel="stylesheet" href="syntaxhighlighter/styles/shThemejqPlot.min.css" />
  
  <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="../excanvas.js"></script><![endif]-->
    <script class="include" type="text/javascript" src="../jquery.min.js"></script>
    
   
</head>
<body>
   
<div id="chart2" style="height:300px; width:500px;"></div>



<script type="text/javascript">
$(document).ready(function(){
  var line1 = [['2012-10-27',2116],['2012-11-04',3568],['2012-11-03',1733],['2012-11-02',4568],['2012-11-01',1411],['2012-10-31',42],['2012-10-30',10],['2012-10-29',10],['2012-10-28',4],['2012-11-05',10]];
  var line2 = [2116,3568,1733,4568,1411,42,10,10,4,10];
  var plot2 = $.jqplot('chart2', [line1, line1], {
    series:[
        {
            renderer:$.jqplot.BarRenderer},
        {
            rendererOptions: {
                forceTickAt0: true
            }
        }
    ],
    axesDefaults: {
        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
        tickOptions: {
          angle: -30
        }
    },
    axes: {
      xaxis: {
        renderer: $.jqplot.CategoryAxisRenderer
      },
      yaxis: {
            min:0,
            tickOptions: {
                formatString: "%.0f" 
            }
        }  
    },
    highlighter: {
        show: true, 
        showLabel: true, 
        tooltipAxes: 'y',
        sizeAdjust: 7.5 , tooltipLocation : 'ne'
    },
    series:[
        {
            pointLabels: {
                    show: false
            },
            lineWidth:1, 
            markerOptions:{style:'circle'}
        }]
  });
});
</script>



<!-- End example scripts -->

<!-- Don't touch this! -->


    <script class="include" type="text/javascript" src="../jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="syntaxhighlighter/scripts/shCore.min.js"></script>
    <script type="text/javascript" src="syntaxhighlighter/scripts/shBrushJScript.min.js"></script>
    <script type="text/javascript" src="syntaxhighlighter/scripts/shBrushXml.min.js"></script>
<!-- End Don't touch this! -->

<!-- Additional plugins go here -->

  <script class="include" language="javascript" type="text/javascript" src="../plugins/jqplot.dateAxisRenderer.min.js"></script>
  <script class="include" language="javascript" type="text/javascript" src="../plugins/jqplot.canvasTextRenderer.min.js"></script>
  <script class="include" language="javascript" type="text/javascript" src="../plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
  <script class="include" language="javascript" type="text/javascript" src="../plugins/jqplot.categoryAxisRenderer.min.js"></script>
  <script class="include" language="javascript" type="text/javascript" src="../plugins/jqplot.barRenderer.min.js"></script>
  <script class="include" type="text/javascript" src="../jqplot/plugins/jqplot.pointLabels.min.js"></script>
  <script class="include" type="text/javascript" src="../jqplot/plugins/jqplot.highlighter.min.js"></script>
<script class="include" type="text/javascript" src="../jqplot/plugins/jqplot.cursor.min.js"></script> 
<!-- End additional plugins -->


	</div>	
	<script type="text/javascript" src="example.min.js"></script>

</body>


</html>

