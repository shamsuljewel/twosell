<?php

/**
 * Description of barchart
 *
 * @author Shamsul
 */
class barchart_3 {
    private $config = array();
    private $s1 = '';
    private $s2 = '';
//    private $thicks = '';
    public function __construct($config, $s1,$s2) {
        $this->config = $config;
        $this->s1 = $s1;
        $this->s2 = $s2;
//        $this->thicks = $ticks;
//        print_r($s1);
//        print_r($s2);
    }
    public function drawChart(){
 //       print_r($this->config);
         echo "<div id='".$this->config['div_id']."' style='width:".$this->config['div-width']."px; height:300px'></div>";
  ?>
<script type="text/javascript">
$(document).ready(function(){
  var bar1 = <?php  echo $this->s1; ?>;
  var bar2 = <?php echo $this->s2; ?>;
  var plot2 = $.jqplot('<?php echo $this->config['div_id']; ?>', [bar1, bar2], {
    series:[
        {
            renderer:$.jqplot.BarRenderer,
             pointLabels: {
                show: true
             }
        },
        {
            yaxis: 'y2axis',
            renderer:$.jqplot.BarRenderer,
             pointLabels: {
                show: true
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
                formatString: "<?php echo $this->config['yformatSrting']; ?>" 
            }
      },
      y2axis: {
            tickOptions: {
                    formatString: "%.0f <?php echo $this->config['format-y-left']; ?>" 
            },
            rendererOptions: {
                    // align the ticks on the y2 axis with the y axis.
                    alignTicks: true,
                    forceTickAt0: true
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
