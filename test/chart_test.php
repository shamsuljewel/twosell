<?php
include '../admin_config.php';
?>
        <?php
            include '../include/jqplot-css.php';
        ?>

<?php


include '../class/barchart.php';
$chart3_config = array();
    $chart3_config['div_id'] = 'chart3';
    $chart3_config['format-y'] = 'kr';
    $chart3_config['div-width'] = '500';
    $chart3_config['bar-width'] = $bar_width;
    $chart3_config['yformatString'] = '%.0f';
    
$chart3_data = "[['2012-11-21',5392],['2012-11-22',1227],['2012-11-23',1410],['2012-11-24',2026],['2012-11-25',1650],['2012-11-26',5219],['2012-11-27',6142],['2012-11-28',1686],['2012-11-29',0],['2012-11-30',0]]";    
$barChart3 = new barchart($chart3_config, $chart3_data);
$barChart3->drawChart();
include '../include/jqplot-js.php';

?>
