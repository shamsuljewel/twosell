<?php
// store stat
session_start();
include '../functions/commonFunction.php';
include '../admin_config.php';

?>
<script>
    $(document).ready(function(){
        $('#nav_div').load('controller/navigation_store_day_stat.php').fadeIn("slow");    
        $('.a_li').click(function(){
            var a = $(this).attr('id');
            $("#nav_div").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/navigation_store_day_stat.php",{z:a},function(data){
                $("#nav_div").html(data).fadeIn(100);
            });
            return false;
        });
    });
</script>

<?php
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
$dateTime = date("Y-m-d");
//$dateTime = "2012-09-06";
include '../dbconnect.php';
$dataArray = array();

//if(isset($_POST['store7days'])){
        
if(isset($_POST['store7days'])) $store7days = $_POST['store7days']; // if submit button clicked
else $store7days = 'store7days'; // default last 7 days
        
$fromDate = $_POST['fromDate'];
$toDate =  $_POST['toDate'];
if(isset($_POST['storeList'])) $select_stores = $_POST['storeList'];
else{
    include '../class/user.php';
    $user = new user($_SESSION['user']['name']);
    $select_stores = $user->getStoreIdByChain(1);
}
//print_r($select_stores);
if($store7days == 'store7days'){
    $q = "SELECT s_id, total_receipt, total_twosell, per, date FROM stat_store WHERE date='$dateTime'";
    $date_diff = 10;
    $dateTime = '2012-09-26 00:00:00';
}  
else if($store7days == 'dateRange'){
    if($fromDate != "" && $toDate != ""){
        $q = "SELECT s_id, total_receipt, total_twosell, per, date FROM stat_store WHERE date BETWEEN '$fromDate' AND '$toDate'";
        $date_diff = dateDiff($fromDate, $toDate);
        $dateTime = $toDate;
    }
    else{
        exit('Fields are null');
    }
}
//        echo $q;
    for($i=0; $i < $date_diff; $i++){
        $dateOf[$i] = date('Y-m-d', strtotime('-'.$i.' days', strtotime($dateTime)));
    }
    $start = $dateOf[$date_diff-1];
    $last = $dateOf[0];
    echo "<div style='width: 900px; border: 0px solid #CCC' align='right'><b>Duration:</b> $start - $last</div>";
    $totalArray = array();
    $len = count($dateOf);
    $count_stores = 0;
    for($i=0; $i< $len; $i++){
        $dateIs = $dateOf[$i];
        $q = "SELECT s_id, total_receipt, total_twosell, per, total_cost, twosell_cost, date FROM stat_store WHERE date='$dateIs'";
        $q1 = mysql_query($q);
        if($q1 != FALSE && mysql_num_rows($q1) > 0){
            while($q2 = mysql_fetch_array($q1)){
                if(in_array($q2[s_id], $select_stores)){ 

                $count_stores++;
                $dataArray[$q2[s_id]][$dateIs]['total_receipt'] = $q2['total_receipt'];
                $dataArray[$q2[s_id]][$dateIs]['total_twosell'] = $q2['total_twosell'];
                $dataArray[$q2[s_id]][$dateIs]['per'] = round($q2['per'],1);
                $dataArray[$q2[s_id]][$dateIs]['total_cost'] = round($q2['total_cost'],0);
                $dataArray[$q2[s_id]][$dateIs]['twosell_cost'] = round($q2['twosell_cost'],0);
                if($dataArray[$q2[s_id]][$dateIs]['total_cost'] > 0){
                    $dataArray[$q2[s_id]][$dateIs]['twosell_per'] = round(($dataArray[$q2[s_id]][$dateIs]['twosell_cost'] * 100) / $dataArray[$q2[s_id]][$dateIs]['total_cost'],2);
                    $dataArray[$q2[s_id]][$dateIs]['avg_rec_value_after'] = round( $dataArray[$q2[s_id]][$dateIs]['total_cost'] / $dataArray[$q2[s_id]][$dateIs]['total_receipt'] , 1);
                    $dataArray[$q2[s_id]][$dateIs]['avg_rec_value_before'] = round( ($dataArray[$q2[s_id]][$dateIs]['total_cost'] - $dataArray[$q2[s_id]][$dateIs]['twosell_cost'])  / $dataArray[$q2[s_id]][$dateIs]['total_receipt'] , 1);
                    if($dataArray[$q2[s_id]][$dateIs]['avg_rec_value_before'] != 0) $dataArray[$q2[s_id]][$dateIs]['avg_rec_increase'] = round($dataArray[$q2[s_id]][$dateIs]['avg_rec_value_after'] - $dataArray[$q2[s_id]][$dateIs]['avg_rec_value_before'],1);
                    else $dataArray[$q2[s_id]][$dateIs]['avg_rec_increase'] = 0;
                }
                else{
                    $dataArray[$q2[s_id]][$dateIs]['twosell_per'] = 0;
                    $dataArray[$q2[s_id]][$dateIs]['avg_rec_value_after'] = 0;
                    $dataArray[$q2[s_id]][$dateIs]['avg_rec_value_before'] = 0;
                    $dataArray[$q2[s_id]][$dateIs]['avg_rec_increase'] = 0;
                }
                $dataArray[$q2[s_id]]['all_total_receipt'] += $dataArray[$q2[s_id]][$dateIs]['total_receipt'];
                $dataArray[$q2[s_id]]['all_twosell_receipt'] += $dataArray[$q2[s_id]][$dateIs]['total_twosell'];
                $dataArray[$q2[s_id]]['all_total_cost'] += $dataArray[$q2[s_id]][$dateIs]['total_cost'];
                $dataArray[$q2[s_id]]['all_twosell_cost'] += $dataArray[$q2[s_id]][$dateIs]['twosell_cost'];

                }
            }
        }
        // total calculation should be here
//           $total_stores = count($dataArray);
        $index = array_keys($dataArray);
//           //print_r($index);
        for($j=0;$j < count($index); $j++){
            $totalArray[$dateIs]['all_store_total_receipt'] +=  $dataArray[$index[$j]][$dateIs]['total_receipt'];
            $totalArray[$dateIs]['all_store_twosell_receipt'] += $dataArray[$index[$j]][$dateIs]['total_twosell'];
            $totalArray[$dateIs]['all_store_total_cost'] += $dataArray[$index[$j]][$dateIs]['total_cost'];
            $totalArray[$dateIs]['all_store_twosell_cost'] += $dataArray[$index[$j]][$dateIs]['twosell_cost'];
        }
        $totalArray['all_total_receipt'] += $totalArray[$dateIs]['all_store_total_receipt'];
        $totalArray['all_twosell_receipt'] += $totalArray[$dateIs]['all_store_twosell_receipt'];
        $totalArray['all_total_cost'] += $totalArray[$dateIs]['all_store_total_cost'];
        $totalArray['all_twosell_cost'] += $totalArray[$dateIs]['all_store_twosell_cost'];
    }
    $totalArray['all_avg_value_before_twosell'] = avg(($totalArray['all_total_cost'] - $totalArray['all_twosell_cost']), $totalArray['all_total_receipt']);
    $totalArray['all_avg_value_after_twosell'] = avg($totalArray['all_total_cost'], $totalArray['all_total_receipt']);
    $totalArray['all_avg_value_increase'] = round($totalArray['all_avg_value_after_twosell'] - $totalArray['all_avg_value_before_twosell'], 2);
    echo "<h2>Summary:</h2>";
    echo "<table style='width: 902px;' class='report_tbl'>
        <tr><td class='field'>Total count (receipt)</td><td class='value'>".number_format($totalArray['all_total_receipt'], 0, ',', ' ')."</td><td class='field'>Twosell count (receipts)</td><td class='value'>".number_format($totalArray['all_twosell_receipt'],0,',',' ')."</td><td class='field'>Percentage <br />( % )</td><td class='value'>". number_format(calPercent($totalArray['all_total_receipt'], $totalArray['all_twosell_receipt']),2,',',' ')."</td></tr>
        <tr><td class='field'>Total value (kr) </td><td class='value'>".number_format($totalArray['all_total_cost'], 0, ',',' ')."</td><td class='field'>TWOSELL value (kr)</td><td class='value'>".number_format($totalArray['all_twosell_cost'],0,',',' ')."</td><td class='field'>Percentage<br />( % )</td><td class='value'>". number_format(calPercent($totalArray['all_total_cost'], $totalArray['all_twosell_cost']),2,',',' ')."</td></tr>
        <tr><td class='field'>Average receipt value<br/>before (kr)</td><td class='value'>".number_format($totalArray['all_avg_value_before_twosell'], '2', ',',' ') ."</td><td class='field'>Average receipt value<br />after (kr)</td><td class='value'>".number_format($totalArray['all_avg_value_after_twosell'],'2',',',' ')."</td><td class='field'>Increased Value <br />per receipt (kr)</td><td class='value'>".number_format($totalArray['all_avg_value_increase'],'2', ',', ' ')."</td></tr>
    </table>";
    //print_table($dataArray, $dateOf, $totalArray);
    //<tr><td class='field'>Average items on<br/>receipt before Twosell </td><td class='value'></td><td class='field'>Average items on<br />receipt incl. Twosell</td><td class='value'></td><td class='field'>Product Increased<br />by Twosell %</td><td class='value'></td></tr>
    // drawing charts
    include '../include/jqplot-css.php';
    include '../class/barchart.php';
    include '../class/barchart_2y.php';
    /* config for chart1 */
    // for showing the twosell values
    $chart1_config = array();
    $chart1_data = '[';
    $chart1_config['div_id'] = 'chart1';
    $chart1_config['format-y'] = 'kr';
    $chart1_config['div-width'] = '500';
    for($i=0; $i<$date_diff;$i++){
        $dateIs = $dateOf[$i];
        $value = $totalArray[$dateIs]['all_store_twosell_cost'];
        $dateIs = date('d',strtotime($dateOf[$i]));
        $chart1_data .= "[".$dateIs.",".$value."]";
        if($i != $date_diff-1) $chart1_data .= ",";
    }
    $chart1_data .= "]";
    
    /*chart1 config end*/
    // showing the Average receipt increase when TWOSELL is used
    $chart2_config = array();
    $chart2_data = '[';
    $chart2_config['div_id'] = 'chart2';
    $chart2_config['format-y'] = 'kr';
    $chart2_config['div-width'] = '500';
    for($i=0; $i<$date_diff;$i++){
        $dateIs = $dateOf[$i];
        //$value = calPercent($totalArray[$dateIs]['all_store_total_receipt'], $totalArray[$dateIs]['all_store_twosell_receipt']);
        if($totalArray[$dateIs]['all_store_twosell_receipt'] > 0) $value = round($totalArray[$dateIs]['all_store_twosell_cost'] / $totalArray[$dateIs]['all_store_twosell_receipt'],0);
        else $value = 0;
        $dateIs = date('d',strtotime($dateOf[$i]));
        $chart2_data .= "[".$dateIs.",".$value."]";
        if($i != $date_diff-1) $chart2_data .= ",";
    }
    $chart2_data .= ']';
    
    // showing the continuous sum up values for twosell
    $chart3_config = array();
    $chart3_data = '[';
    $chart3_config['div_id'] = 'chart3';
    $chart3_config['format-y'] = 'kr';
    $chart3_config['div-width'] = '500';
    $value = 0;
    for($i=$date_diff-1; $i >= 0; $i--){
        $dateIs = $dateOf[$i];
        //$value = calPercent($totalArray[$dateIs]['all_store_total_receipt'], $totalArray[$dateIs]['all_store_twosell_receipt']);
        $value += $totalArray[$dateIs]['all_store_twosell_cost'];
        $dateIs = date('d',strtotime($dateOf[$i]));
        $chart3_data .= "[".$dateIs.",".$value."]";
        if($i != 0) $chart3_data .= ",";
    }
    $chart3_data .= ']';

    
    $chart4_config = array();
    $chart4_s1 = '[';
    $chart4_s2 = '[';
    $chart4_config['div_id'] = 'chart4';
    $chart4_config['format-y-left'] = '%';
    $chart4_config['format-y-right'] = '';
    $chart4_config['div-width'] = '500';
    $chart4_ticks  = "[";
    for($i=$date_diff-1; $i >= 0; $i--){
        $dateIs = $dateOf[$i];
        $value = $totalArray[$dateIs]['all_store_twosell_receipt'];
        $s2_value = round(calPercent($totalArray[$dateIs]['all_store_total_receipt'], $totalArray[$dateIs]['all_store_twosell_receipt']),0);
        $dateIs = date('d',strtotime($dateOf[$i]));
        $chart4_ticks .= $dateIs;
        $chart4_s1 .= $value;
        $chart4_s2 .= $s2_value;
        if($i != 0) { $chart4_s1 .= ","; $chart4_ticks .= ","; $chart4_s2 .= ","; }
    }
    
    $chart4_s1 .= ']';
    $chart4_s2 .= "]";
    $chart4_ticks .= "]";
    //echo $chart4_s1;
    //$data = '[[20,64696],[21,66261],[22,55464],[23,53560],[24,55062],[25,62510],[26,15491],[27,15591],[28,15791],[29,18491]]';
    echo "<div style='width: 500px; float: left; border:0px solid #CCC; padding:5px;'>";
    echo "<h2>Total Twosell Value:</h2>";
    $barChart1 = new barchart($chart1_config, $chart1_data);
    $barChart1->drawChart();
    echo "</div>";
    //echo $chart2_data;
    echo "<div style='float: left;border:0px solid #CCC; width: 500px; padding: 5px'>";
    echo "<h2>TWOSELL total revenue (sum up the staples from fig 1):</h2>";
    $barChart3 = new barchart($chart3_config, $chart3_data);
    $barChart3->drawChart();
    echo "</div>";
    
    echo "<div style='width: 500px;float: left; border:0px solid #CCC; padding:5px;'>";
    echo "<h2>Average receipt increase when TWOSELL is used:</h2>";
    $barChart2 = new barchart($chart2_config, $chart2_data);
    $barChart2->drawChart();
    echo "</div>";
    
    echo "<div style='float: left;border:0px solid #CCC; width: 500px; padding: 5px'>";
    echo "<h2>Twosell count and percent(%) of total receipt:</h2>";
    $barChart4 = new barchart_2y($chart4_config, $chart4_s1, $chart4_s2, $chart4_ticks);
    $barChart4->drawChart();
    
    echo "</div>";
    include '../include/jqplot-js.php';
    
 ?>
