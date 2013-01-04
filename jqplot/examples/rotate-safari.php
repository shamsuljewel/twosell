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
    

      
<!-- Example scripts go here -->

 
<div id="chart2" style="height:300px; width:500px;"></div>

<script type="text/javascript">
$(document).ready(function(){
  var line1 = [['Cup Holder Pinion Bob', 7], ['Generic Fog Lamp', 9], ['HDTV Receiver', 15], 
  ['8 Track Control Module', 12], [' Sludge Pump Fourier Modulator', 3], 
  ['Transcender/Spice Rack', 6], ['Hair Spray Danger Indicator', 18]];
  var line2 = [['Nickle', 28], ['Aluminum', 13], ['Xenon', 54], ['Silver', 47], 
  ['Sulfer', 16], ['Silicon', 14], ['Vanadium', 23]];

  var plot2 = $.jqplot('chart2', [line1, line2], {
    series:[{renderer:$.jqplot.BarRenderer}, {xaxis:'x2axis', yaxis:'y2axis'}],
    axesDefaults: {
        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
        tickOptions: {
          angle: 30
        }
    },
    axes: {
      xaxis: {
        renderer: $.jqplot.CategoryAxisRenderer
      },
      x2axis: {
        renderer: $.jqplot.CategoryAxisRenderer
      },
      yaxis: {
        autoscale:true
      },
      y2axis: {
        autoscale:true
      }
    }
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

<!-- End additional plugins -->


	<script type="text/javascript" src="example.min.js"></script>

</body>


</html>
