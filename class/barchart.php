<?php

/**
 * Description of barchart
 *
 * @author Shamsul
 */
class barchart {
    private $config = array();
    private $s1 = '';
//    private $thicks = '';
    public function __construct($config, $s1) {
        $this->config = $config;
        $this->s1 = $s1;
//        $this->thicks = $ticks;
        //print_r($s1);
    }
    public function drawChart(){
 //       print_r($this->config);
         echo "<div id='".$this->config['div_id']."' style='width:".$this->config['div-width']."px; height:300px'></div>";
  ?>
<script type="text/javascript">
$(document).ready(function(){
  var line1 = <?php  echo $this->s1; ?>;
  var plot2 = $.jqplot('<?php echo $this->config['div_id']; ?>', [line1, line1], {
    series:[
        {
            renderer:$.jqplot.BarRenderer,
             pointLabels: {
                show: true
             }
        },
        {xaxis:'xaxis', yaxis:'yaxis'}
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
                formatString: "<?php echo $this->config['yformatSrting']; ?>" 
            }
        }  
    },
    highlighter: {
        show: true, 
        showLabel: true, 
        tooltipAxes: 'y',
        sizeAdjust: 7.5 , tooltipLocation : 'ne'
    }
//    ,
//    series:[
//        {
//            pointLabels: {
//               show: true
//            },
//            lineWidth:1, 
//            markerOptions:{style:'circle'}
//        }]
  });
});
</script>      
<?php
    }
}

?>
