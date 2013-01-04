<?php

/**
 * Description of barchart
 *
 * @author Shamsul
 */
class barchart {
    private $config = array();
    private $data = '';
    public function __construct($config, $data) {
        $this->config = $config;
        $this->data = $data;
        print_r($this->data);
    }
    public function drawChart(){
        //print_r($this->config);
         echo "<div id='".$this->config['div_id']."' style='width:".$this->config['div-width']."px; height:300px'></div>";
  ?>
<script class='code' type='text/javascript'>
$(document).ready(function () {
    var s1 = <?php echo $this->data; ?>;
    var s2 = <?php echo $this->data; ?>;
    
    plot1 = $.jqplot('<?php echo $this->config['div_id']; ?>', [s1, s2], {
        // Turns on animatino for all series in this plot.
        animate: true,
        // Will animate plot on calls to plot1.replot({resetAxes:true})
        animateReplot: true,
        cursor: {
            show: true,
            zoom: true,
            looseZoom: true,
            showTooltip: false
        },
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
                    barWidth: 35,
                    barPadding: -15,
                    barMargin: 0,
                    highlightMouseOver: false
                }
            }, 
            {
                rendererOptions: {
                    // speed up the animation a little bit.
                    // This is a number of milliseconds.
                    // Default for a line series is 2500.
                    animation: {
                        speed: 2000
                    }
                }
            }
        ],
        axesDefaults: {
            pad: 0
        },
        axes: {
            // These options will set up the x axis like a category axis.
            xaxis: {
                tickInterval: 1,
                drawMajorGridlines: false,
                drawMinorGridlines: true,
                drawMajorTickMarks: false,
                rendererOptions: {
                    tickInset: 0.5,
                    minorTicks: 1
                },
                tickOptions: {
                    formatString: "%s" 
                        //function(){return '%s';}()
                }
            },
            yaxis: {
                tickOptions: {
                    formatString: "%s <?php echo $this->config['format-y']; ?>" 
                        //function(){return '%s';}()
                },
                rendererOptions: {
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
    });
//    plot1.series[1].show = false;
//    plot1.replot(); 
});
</script>
        
<?php
    }
}

?>
