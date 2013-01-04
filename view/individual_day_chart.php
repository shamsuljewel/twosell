<?php
/*
 * this script shows the individual cashier statistics and chart for the selection of 
 * a cashier, this script gets the data from $_post[id] then it explode the data
 */
session_start();
include '../dbconnect.php';
include '../functions/commonFunction.php';
include '../admin_config.php';
    
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
//echo $_POST['id'];
//$_POST['id'] = '26124,,14,,store7days,,{"2012-11-05":{"total_receipt":57,"total_twosell":"1","per":0,"total_cost":2522,"twosell_cost":51,"twosell_per":2.02,"avg_rec_value_after":44.2,"avg_rec_value_before":43.4,"avg_rec_increase":0.8},"all_total_receipt":57,"all_twosell_receipt":1,"all_total_cost":2522,"all_twosell_cost":51}';
if($_POST['id']){
    
//    mysql_select_db($dbLocal);
    /*
     * config tables
     */
    //$cashier_stat_tbl = 'stat_cashier';
    // expplode the information from the post id
    $ids = explode(",,", $_POST['id']);
    $store_id = $ids[0];
    $cashier_id = $ids[1];
//    $option = $ids[2];
    $dataArray = json_decode($ids[3], true);
    
    $dateOf = $_SESSION['dateOf'];
    echo "<div><h1>Cashier: ".$cashier_id." of Store: ".$store_id."</h1></div>";
//    print_r($dataArray);
    
    include '../include/jqplot-css.php';
    // two y chart and two bars shows into each bar
    include '../class/barchart_3_3.php';
    include '../class/barchart_3.php';
    
    $chart4_config = array();
    $chart_data = array();
    $chart4_s1 = '[';
    $chart4_s2 = '[';
    $chart4_s3 = '[';
    $chart4_config['div_id'] = 'chart4_individual';
    $chart4_config['format-y-left'] = '%';
    $chart4_config['format-y-right'] = '';
    $chart4_config['div-width'] = '900';
    
    $chart4_config['bar-width'] = $bar_width;
    $chart4_config['yformatString'] = '%.1f';
    $total_days = count($dateOf);
    $bar_width = 170 / $total_days;
    $all_total_receipt = 0;
    $all_twosell_receipt = 0;
    $all_total_cost = 0;
    $all_twosell_cost = 0;
//    echo $total_days;
    for($i=$total_days-1; $i >= 0; $i--){
        $curr_date =  $dateOf[$i];
        if($dataArray[$curr_date]){
//            echo $dataArray[$curr_date]['total_receipt'].", ";
            $chart_data[$curr_date]['total'] = $dataArray[$curr_date]['total_receipt'];
            $chart_data[$curr_date]['twosell'] = $dataArray[$curr_date]['total_twosell'];
            if($chart_data[$curr_date]['total'] != 0) $chart_data[$curr_date]['per'] = round(($chart_data[$curr_date]['twosell'] * 100) / $chart_data[$curr_date]['total'],1);
            else $chart_data[$curr_date]['per'] = 0;

            $value = $chart_data[$curr_date]['twosell'];
            $s2_value = $chart_data[$curr_date]['per'];
            $s3_value = $chart_data[$curr_date]['total'];
            //$dateIs = date('d',strtotime($dateOf[$i]));
            $chart4_s1 .= "['".$curr_date."',".$value."]";
            $chart4_s2 .= "['".$curr_date."',".$s2_value."]";
            $chart4_s3 .= "['".$curr_date."',".$s3_value."]";
            // all time add a , last we delete the last , after finished the string generation
            $chart4_s1 .= ","; $chart4_s2 .= ","; $chart4_s3 .= ","; 
            //echo $dateOf[$i-1];
            // calculate all total all the dates
            $all_total_receipt += $dataArray[$curr_date]['total_receipt'];
            $all_twosell_receipt += $dataArray[$curr_date]['total_twosell'];
            
            $all_total_cost += $dataArray[$curr_date]['total_cost'];
            $all_twosell_cost += $dataArray[$curr_date]['twosell_cost'];
        }
    }
    $chart4_s1 .= ']';
    $chart4_s2 .= "]";
    $chart4_s3 .= "]";
    
    $s1_len = strlen($chart4_s1)-2;
    $s2_len = strlen($chart4_s2)-2;
    $s3_len = strlen($chart4_s3)-2;
    
    // delete the last , from the string
    $chart4_s1 = substr_replace($chart4_s1, '', $s1_len, -1);
    $chart4_s2 = substr_replace($chart4_s2, '', $s2_len, -1);
    $chart4_s3 = substr_replace($chart4_s3, '', $s3_len, -1);
    //echo $chart4_s1;
    if($all_total_receipt != 0){
        $avg_receipt_value_before = round(($all_total_cost - $all_twosell_cost) / $all_total_receipt, 1);
        $avg_receipt_value_after = round($all_total_cost / $all_total_receipt, 1);

    }else{
        $avg_receipt_value_before = 0;
        $avg_receipt_value_after = 0;
    }
    $avg_increase_per_receipt = $avg_receipt_value_after - $avg_receipt_value_before;
    if($all_total_cost != 0) $revenew_increase_per = round((($all_twosell_cost * 100) / $all_total_cost), 2);
    else $revenew_increase_per = 0;
    $avg_twosell_receipt_per = round((($all_twosell_receipt * 100 ) / $all_total_receipt), 1); 
//    echo $chart4_s1;
//    echo "<br />";
//    echo $chart4_s2;
//    echo "<br />";
//    echo $chart4_s3;
//    echo "<table>";
//    echo "<tr><td>";
//    echo "<div style='float: left;border:0px solid #CCC; width: 500px; padding: 5px'>";
    echo "<h2>Summary:</h2>";
    echo "<table style='width: 902px;' class='report_tbl'>
        <tr><td class='field'>TWOSELL receipts (%)</td><td class='value'>".number_format($avg_twosell_receipt_per, 1,',',' ')."</td><td class='field'>Total TWOSELL receipts</td><td class='value'>".number_format($all_twosell_receipt, 0, ',', ' ')."</td><td class='field'>Total no. of receipts<br />excl. TWOSELL</td><td class='value'>".number_format($all_total_receipt, 0, ',', ' ')."</td></tr>
        <tr><td class='field'>Revenue increase by <br />TWOSELL (%)</td><td class='value'>".number_format($revenew_increase_per, 2, ',',' ')."</td><td class='field'>TWOSELL revenue increase (kr)</td><td class='value'>".number_format($all_twosell_cost, 0, ',', ' ')."</td><td class='field'>Total revenue (kr)</td><td class='value'>".number_format($all_total_cost, 0, ',', ' ')."</td></tr>
        <tr><td class='field'>Revenue increase per receipt (kr)</td><td class='value'>".number_format($avg_increase_per_receipt, 2, ',', ' ')."</td><td class='field'>Average receipt <br />after TWOSELL(kr)</td><td class='value'>".number_format($avg_receipt_value_after, 2, ',', ' ')."</td><td class='field'>Average receipt before TWOSELL (kr)</td><td class='value'>".number_format($avg_receipt_value_before, 2, ',', ' ')."</td></tr>
    </table>";
    
    echo "<h2>Total / Twosell count / percent(%) of total receipt:</h2>";
    $barChart4 = new barchart_3_3($chart4_config, $chart4_s1, $chart4_s2, $chart4_s3);
    $barChart4->drawChart();
    
//    echo "</div>";
//    echo "</td></tr></table>";

    include '../include/jqplot-js.php';
}
// if no post id found then shows nothing
?>
