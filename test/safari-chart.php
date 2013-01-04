<?php
include '../admin_config.php';
include '../include/jqplot-css.php';
include '../class/barChart_final.php';
$chart1_config = array();
    $chart1_data = '[';
    $chart1_config['div_id'] = 'chart1';
    $chart1_config['format-y'] = 'kr';
    $chart1_config['div-width'] = '500';
    if($date_diff!=0) $bar_width = 300 / $date_diff;
    else $bar_width = 300;
    $chart1_config['bar-width'] = $bar_width;
    
    $chart1_data = "[['2012-10-29',0],['2012-10-30',0],['2012-10-31',42],['2012-11-01',1411],['2012-11-02',4568],['2012-11-03',1733],['2012-11-04',3568],['2012-11-05',2116],['2012-11-06',0],['2012-11-07',0]]";
    $chart1_data = "[['2012-10-28', 7], ['Generic Fog Lamp', 9], ['HDTV Receiver', 15], 
  ['8 Track Control Module', 12], [' Sludge Pump Fourier Modulator', 3], 
  ['Transcender/Spice Rack', 6], ['Hair Spray Danger Indicator', 18]]";
    $chart = new barChart_final($chart1_config,$chart1_data);
    $chart->drawChart();
    
    include '../include/jqplot-js.php';

?>
