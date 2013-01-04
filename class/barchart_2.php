<?php

/**
 * Description of barchart
 *
 * @author Shamsul
 */
class barchart {
    private $config = array();
    private $data = '';
    private $ticks = '';
    public function __construct($config, $data) {
        $this->config = $config;
        $this->data = $data;
        $this->ticks = $ticks;
        print_r($this->data);
    }
    public function drawChart(){
       // print_r($this->config);
         echo "<div id='".$this->config['div_id']."' style='width:".$this->config['div-width']."px; height:300px'></div>";
  ?>
<script class='code' type='text/javascript'>
$(document).ready(function () {
    //document.write(data1);
    $.jqplot.config.enablePlugins = true;
    var s1 = <?php echo $this->data; ?>;
    var line = <?php echo $this->data; ?>;
    plot1 = $.jqplot('<?php echo $this->config['div_id']; ?>', [s1,line], {
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
                
                renderer: $.jqplot.BarRenderer,
                showHighlight: false,
                yaxis: 'yaxis',
                animation: {
                   speed: 2500
                },
                rendererOptions: {
                    barWidth: '<?php echo $this->config['bar-width']; ?>',
                    barPadding: 0,
                    barMargin: 0,
                    highlightMouseOver: false
                }
            }, 
            {
                pointLabels: {
                    show: true
                },
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
        //title:'Default Date Axis',
        axesDefaults: {
            pad: 0
        },
        axes:{
            xaxis:{
                renderer: $.jqplot.DateAxisRenderer,
                tickOptions: {
                    formatString: "%d/%m" 
                }
                
            },
            yaxis: {
                
                tickOptions: {
                    formatString: "%.0f <?php echo $this->config['format-y']; ?>" 
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
//    plot1.series[1].show = false;
//    plot1.replot(); 
});
</script>
        
<?php
    }
}

?>
