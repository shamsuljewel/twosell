<?php

/**
 * Description of barchart
 *
 * @author Shamsul
 */
class barchart_2y {
    private $config = array();
    private $s1 = '';
    private $s2 = '';
    private $ticks = '';
    public function __construct($config, $s1, $s2, $ticks) {
        $this->config = $config;
        $this->s1 = $s1;
        $this->s2 = $s2;
        $this->ticks = $ticks;
        print_r($ticks);
    }
    public function drawChart(){
 //       print_r($this->config);
         echo "<div id='".$this->config['div_id']."' style='width:".$this->config['div-width']."px; height:300px'></div>";
  ?>
<script class='code' type='text/javascript'>
$(document).ready(function () {
    var s1 = <?php echo $this->s1; ?>;
    var s2 = <?php echo $this->s2; ?>;
    var ticks = <?php echo $this->ticks; ?>;
//    var s1 = [2, 6, 7, 10];
//    var s2 = [100, 500, 300, 200];
//    var ticks = ['a', 'b', 'c', 'd'];
    
    plot1 = $.jqplot('<?php echo $this->config['div_id']; ?>', [s1, s2], {
        // Turns on animatino for all series in this plot.
        animate: true,
        // Will animate plot on calls to plot1.replot({resetAxes:true})
        animateReplot: true,
        series:[
            {
                pointLabels: {
                    show: true
                },
                renderer: $.jqplot.BarRenderer,
                showHighlight: false,
                yaxis: 'yaxis',
                rendererOptions: {
                    // Speed up the animation a little bit.
                    // This is a number of milliseconds.  
                    // Default for bar series is 3000.  
                    animation: {
                        speed: 2500
                    },
                    barWidth: <?php echo $this->config['bar-width']; ?>,
                    barPadding: -<?php echo $this->config['bar-width']; ?>,
                    barMargin: 0,
                    highlightMouseOver: false
                }
            }, 
            {
                yaxis: 'y2axis',
                pointLabels: {
                    show: true
                },
                renderer: $.jqplot.BarRenderer,
                rendererOptions: {
                    // speed up the animation a little bit.
                    // This is a number of milliseconds.
                    // Default for a line series is 2500.
                    animation: {
                        speed: 2000
                    },
                    barWidth: <?php echo $this->config['bar-width']; ?>
                }
            }
        ],
        axesDefaults: {
            pad: 0,
            tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
            tickOptions: {
                angle: -30
            }
        },
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                tickRenderer: $.jqplot.AxisTickRenderer,
                ticks: ticks,
                tickOptions: {
                    formatString: function(){return '%s';}()
                }
            },
            yaxis: {
                tickOptions: {
                    formatString: "%.0f <?php echo $this->config['format-y-right']; ?>" 
                },
                rendererOptions: {
                    forceTickAt0: true
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
        }
    });
//    plot1.series[1].show = false;
//    plot1.replot(); 
});
</script>
        
<?php
    }
}

?>
