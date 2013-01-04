<?php
// store stat
session_start();
include '../functions/commonFunction.php';
include '../admin_config.php';

?>

<!--<script type="text/javascript" src="js/tinybox.js"></script>
<link rel="stylesheet" href="css/style.css" />-->
<script>
    $(document).ready(function(){
        
        $('#nav_div').load('controller/navigation_cashier_day_stat.php').fadeIn("slow");    
        $('.a_li').click(function(){
            var a = $(this).attr('id');
            $("#nav_div").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/navigation_cashier_day_stat.php",{z:a},function(data){
                $("#nav_div").html(data).fadeIn(100);
            });
            return false;
        });
//        $("#individual-chart").dialog({
//            title:'Are you sure you don\'t want to save?',
//            resizable: false,
//            height:140,
//            autoOpen: false,
//
//            modal: true,
//            buttons: {
//                Ok: function() {
//                    window.location.href = "findUsers";
//                    $( this ).dialog( "close" );
//                },
//                Cancel: function() {
//                    $( this ).dialog( "close" );
//                }
//            }
//        });
        $("#individual-chart").hide();
        $('.showIndividualCashier').click(function(){
            var id = $(this).attr('id');
            $("#individual-chart").show();
            $("#individual-chart").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            // use to place the focus on the new chart
            $('#individual-chart').focus();
            $('html,body').animate({scrollTop: $('#individual-chart').offset().top}, 'slow');
            
//            alert(id);
//            $('#jqchart').hide();
            $.post("view/individual_day_chart.php",{id: id} ,function(data){
                $("#individual-chart").html(data);
//                $('#individual-chart').hide();
            });
            
//            $('#individual-chart').dialog('open');    
            
/*
 * used for tiny box but it does not working for the jqplot chart, don't know why?
 */
//            TINY.box.show({url:'view/individual_day_chart.php',post:'id='+id ,width:900,opacity:20,topsplit:3, fixed:false});
//            $('#dialog').load('http://google.com');
            return false;
        });
        
    });
</script>

<?php
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
$dateTime = date("Y-m-d");
$curr_time = date('Y-m-d H:i:s');
//$dateTime = "2012-09-06";
include '../dbconnect.php';
$dataArray = array();

//if(isset($_POST['store7days'])){
        
if(isset($_POST['store7days'])) $store7days = $_POST['store7days']; // if submit button clicked
else $store7days = 'store7days'; // default last 7 days
        
$fromDate = $_POST['fromDate'];
$toDate =  $_POST['toDate'];
include '../class/user.php';
$chainId = 1;
$user = new user($_SESSION['user']['name']);
if(isset($_POST['storeList'])) $select_store = $_POST['storeList'];
else{
    $select_store = $user->getStoreIdByChain($chainId);
    $select_store = $select_store[0];
}
if($store7days == 'store7days'){
//    $q = "SELECT s_id, total_receipt, total_twosell, per, date FROM stat_cashier WHERE date='$dateTime' AND s_id='$select_store'";
    $date_diff = 10;
    
//    $dateTime = '2012-11-05 00:00:00';
    //$dateTime = date('Y-m-d');
}  
else if($store7days == 'dateRange'){
    if($fromDate != "" && $toDate != ""){
//        $q = "SELECT s_id, total_receipt, total_twosell, per, date FROM stat_cashier WHERE date BETWEEN '$fromDate' AND '$toDate' AND s_id = '$select_store'";
        $date_diff = dateDiff($fromDate, $toDate);
        $dateTime = $toDate;
    }
    else{
        exit('Fields are null');
    }
}
    //$dateTime = date('Y-m-d', strtotime('+1 days', strtotime($dateTime)));    
    for($i=0; $i < $date_diff; $i++){
        $dateOf[$i] = date('Y-m-d', strtotime('-'.$i.' days', strtotime($dateTime)));
    }
//    print_r($dateOf);
    $start = $dateOf[$date_diff-1];
    $last = $dateOf[0];
    $_SESSION['dateOf'] = $dateOf;
    
    echo "<div style='width: 900px; border: 0px solid #CCC' align='right'><b>Duration:</b> $start - $last<br />".date('M d, H:i:s',strtotime($curr_time))."<br />";
    $q_last_twosell = mysql_query("SELECT * FROM `twosell_purchase_last` WHERE `direct_gross_incl_vat`>0 ORDER BY `twosell_purchase_last`.`id`  DESC LIMIT 1");
    $q_last_twosell2 = mysql_fetch_object($q_last_twosell);
    echo "Last Twosell at: ".$q_last_twosell2->time_of_purchase." from <b>Store:</b>".$q_last_twosell2->store." </div>";
    
    $totalArray = array();
    $len = count($dateOf);
    for($i=0; $i < $len; $i++){
        $dateIs = $dateOf[$i];
        $q = "SELECT s_id, c_id, total_receipt, total_twosell, per,total_cost, twosell_cost, date FROM stat_cashier WHERE date='$dateIs' AND s_id='$select_store' ORDER BY c_id";
//        echo $q;
        $q1 = mysql_query($q);
        if($q1 != FALSE && mysql_num_rows($q1) > 0){
            while($q2 = mysql_fetch_array($q1)){

                $dataArray[$q2[c_id]][$dateIs]['total_receipt'] = $q2['total_receipt'];
                $dataArray[$q2[c_id]][$dateIs]['total_twosell'] = $q2['total_twosell'];
                $dataArray[$q2[c_id]][$dateIs]['per'] = round($q2['per'],1);
                $dataArray[$q2[c_id]][$dateIs]['total_cost'] = round($q2['total_cost'] - $q2['twosell_cost'],0);
                $dataArray[$q2[c_id]][$dateIs]['twosell_cost'] = round($q2['twosell_cost'],0);
                if($dataArray[$q2[c_id]][$dateIs]['total_cost'] > 0){
                    $dataArray[$q2[c_id]][$dateIs]['twosell_per'] = round(($dataArray[$q2[c_id]][$dateIs]['twosell_cost'] * 100) / $dataArray[$q2[c_id]][$dateIs]['total_cost'],2);
                    $dataArray[$q2[c_id]][$dateIs]['avg_rec_value_after'] = round( $dataArray[$q2[c_id]][$dateIs]['total_cost'] / $dataArray[$q2[c_id]][$dateIs]['total_receipt'] , 1);
                    $dataArray[$q2[c_id]][$dateIs]['avg_rec_value_before'] = round( ($dataArray[$q2[c_id]][$dateIs]['total_cost'] - $dataArray[$q2[c_id]][$dateIs]['twosell_cost'])  / $dataArray[$q2[c_id]][$dateIs]['total_receipt'] , 1);
                    if($dataArray[$q2[c_id]][$dateIs]['avg_rec_value_before'] != 0) $dataArray[$q2[c_id]][$dateIs]['avg_rec_increase'] = round($dataArray[$q2[c_id]][$dateIs]['avg_rec_value_after'] - $dataArray[$q2[c_id]][$dateIs]['avg_rec_value_before'],1);
                    else $dataArray[$q2[c_id]][$dateIs]['avg_rec_increase'] = 0;
                }
                else{
                    $dataArray[$q2[c_id]][$dateIs]['twosell_per'] = 0;
                    $dataArray[$q2[c_id]][$dateIs]['avg_rec_value_after'] = 0;
                    $dataArray[$q2[c_id]][$dateIs]['avg_rec_value_before'] = 0;
                    $dataArray[$q2[c_id]][$dateIs]['avg_rec_increase'] = 0;
                }
                /*
                 * this array saves the individual cashiers total information
                 */
                $dataArray[$q2[c_id]]['all_total_receipt'] += $dataArray[$q2[c_id]][$dateIs]['total_receipt'];
                $dataArray[$q2[c_id]]['all_twosell_receipt'] += $dataArray[$q2[c_id]][$dateIs]['total_twosell'];
                $dataArray[$q2[c_id]]['all_total_cost'] += $dataArray[$q2[c_id]][$dateIs]['total_cost'];
                $dataArray[$q2[c_id]]['all_twosell_cost'] += $dataArray[$q2[c_id]][$dateIs]['twosell_cost'];
            }
        }
//        print_r($dataArray);
//        echo "<hr/>";
        // total calculation should be here
//           $total_stores = count($dataArray);
        $index = array_keys($dataArray);
//        print_r($index);
        for($j=0;$j < count($index); $j++){
            $totalArray[$dateIs]['all_store_total_receipt'] +=  $dataArray[$index[$j]][$dateIs]['total_receipt'];
            $totalArray[$dateIs]['all_store_twosell_receipt'] += $dataArray[$index[$j]][$dateIs]['total_twosell'];
            $totalArray[$dateIs]['all_store_total_cost'] += $dataArray[$index[$j]][$dateIs]['total_cost'];
            $totalArray[$dateIs]['all_store_twosell_cost'] += $dataArray[$index[$j]][$dateIs]['twosell_cost'];
        }
        /*
         * this array saves the all cashiers total of total 
         */
        $totalArray['all_total_receipt'] += $totalArray[$dateIs]['all_store_total_receipt'];
        $totalArray['all_twosell_receipt'] += $totalArray[$dateIs]['all_store_twosell_receipt'];
        $totalArray['all_total_cost'] += $totalArray[$dateIs]['all_store_total_cost'];
        $totalArray['all_twosell_cost'] += $totalArray[$dateIs]['all_store_twosell_cost'];
    }
//    print_r($dataArray);
//    echo $totalArray['all_total_receipt'].", ".$totalArray['all_total_cost'];
    
//    $totalArray['all_total_receipt'] -=  $totalArray['all_twosell_receipt'];
//    $totalArray['all_total_cost'] -= $totalArray['all_twosell_cost'];
    
    $totalArray['all_avg_value_before_twosell'] = avg(($totalArray['all_total_cost'] - $totalArray['all_twosell_cost']), $totalArray['all_total_receipt']);
    $totalArray['all_avg_value_after_twosell'] = avg($totalArray['all_total_cost'], $totalArray['all_total_receipt']);
    $totalArray['all_avg_value_increase'] = round($totalArray['all_avg_value_after_twosell'] - $totalArray['all_avg_value_before_twosell'], 2);
    
//    print_r($totalArray);
    echo "<h2>Summary:</h2>";
    echo "<table style='width: 902px;' class='report_tbl'>
        <tr><td class='field'>TWOSELL receipts (%)</td><td class='value'>". number_format(calPercent($totalArray['all_total_receipt'], $totalArray['all_twosell_receipt']),2,',',' ')."</td><td class='field'>Total TWOSELL receipts</td><td class='value'>".number_format($totalArray['all_twosell_receipt'],0,',',' ')."</td><td class='field'>Total no. of receipts<br />excl. TWOSELL</td><td class='value'>". number_format($totalArray['all_total_receipt'], 0, ',', ' ')."</td></tr>
        <tr><td class='field'>Revenue increase by <br />TWOSELL (%)</td><td class='value'>".number_format(calPercent($totalArray['all_total_cost'], $totalArray['all_twosell_cost']),2,',',' ')."</td><td class='field'>TWOSELL revenue increase (kr)</td><td class='value'>".number_format($totalArray['all_twosell_cost'],0,',',' ')."</td><td class='field'>Total revenue (kr)</td><td class='value'>".number_format($totalArray['all_total_cost'], 0, ',',' ')."</td></tr>
        <tr><td class='field'>Revenue increase per receipt (kr)</td><td class='value'>".number_format($totalArray['all_avg_value_increase'],'2', ',', ' ') ."</td><td class='field'>Average receipt <br />after TWOSELL(kr)</td><td class='value'>".number_format($totalArray['all_avg_value_after_twosell'],'2',',',' ')."</td><td class='field'>Average receipt before TWOSELL (kr)</td><td class='value'>".number_format($totalArray['all_avg_value_before_twosell'], '2', ',',' ')."</td></tr>
    </table>";
//    print_table_cashier($dataArray, $dateOf);
    //print_table($dataArray, $dateOf, $totalArray);
    //<tr><td class='field'>Average items on<br/>receipt before Twosell </td><td class='value'></td><td class='field'>Average items on<br />receipt incl. Twosell</td><td class='value'></td><td class='field'>Product Increased<br />by Twosell %</td><td class='value'></td></tr>
    $cashierList = array_keys($dataArray);
//    print_r($cashierList);
    sort($cashierList);
//    print_r($cashierList);
    // drawing charts
    include '../include/jqplot-css.php';
    include '../class/barchart_3.php';
    include '../class/barchart.php';
    
    $chart4_config = array();
    $chart4_s1 = '[';
    $chart4_s2 = '[';
    $chart4_config['div_id'] = 'chart4';
    $chart4_config['format-y-left'] = '%';
    $chart4_config['format-y-right'] = '';
    $chart4_config['div-width'] = '900';
    $bar_width = 170 / $date_diff;
    $chart4_config['bar-width'] = $bar_width;
    $total_cashier = count($cashierList);
    
    $chart2_config = array();
    $chart2_data = '[';
    $chart2_config['div_id'] = 'chart2';
    $chart2_config['format-y'] = 'kr';
    $chart2_config['div-width'] = '860';
    $bar_width = 170 / $date_diff;
    $chart2_config['bar-width'] = $bar_width;
    $chart2_config['yformatSrting'] = '%.1f';
    for($i=0; $i < $total_cashier; $i++){
        if($cashierList[$i] != ''){
            $cashier = $cashierList[$i];
            $chart_data[$cashier]['total'] = $dataArray[$cashier]['all_total_receipt'];
            $chart_data[$cashier]['twosell'] = $dataArray[$cashier]['all_twosell_receipt'];
            if($chart_data[$cashier]['total'] != 0) $chart_data[$cashier]['per'] = round(($chart_data[$cashierList[$i]]['twosell'] * 100) / $chart_data[$cashierList[$i]]['total'],1);
            else $chart_data[$cashier]['per'] = 0;
            
            $value = $chart_data[$cashier]['twosell'];
            $s2_value = $chart_data[$cashier]['per'];
            //$dateIs = date('d',strtotime($dateOf[$i]));
            $chart4_s1 .= "['".$cashier."',".$value."]";
            $chart4_s2 .= "['".$cashier."',".$s2_value."]";
            if($i != $total_cashier-1){ $chart4_s1 .= ","; $chart4_s2 .= ","; }
            
            if($dataArray[$cashier]['all_total_receipt'] != 0){
                $avg_receipt_value_before = round(($dataArray[$cashier]['all_total_cost'] - $dataArray[$cashier]['all_twosell_cost']) / $dataArray[$cashier]['all_total_receipt'], 1);
                $avg_receipt_value_after = round($dataArray[$cashier]['all_total_cost'] / $dataArray[$cashier]['all_total_receipt'], 1);

            }else{
                $avg_receipt_value_before = 0;
                $avg_receipt_value_after = 0;
            }
            $value_increase = $avg_receipt_value_after - $avg_receipt_value_before;
            $chart2_data .= "['".$cashier."',".$value_increase."]";
            if($i != $total_cashier-1){ 
                $chart2_data .= ",";
            }
        }
    }
    
    $chart4_s1 .= ']';
    $chart4_s2 .= "]";
    $chart2_data .= "]";

//    echo $chart4_s1;
//    echo "<br />";
//    echo $chart4_s2;
//    //$data = '[[20,64696],[21,66261],[22,55464],[23,53560],[24,55062],[25,62510],[26,15491],[27,15591],[28,15791],[29,18491]]';
    echo "<table>";
    
    echo "<tr><td>";
    echo "<div id='jqchart' style='float: left;border:0px solid #CCC; width: 500px; padding: 5px'>";
    echo "<h2>Twosell count and percent(%) of total receipt:</h2>";
    /*
     * Cashiers chart during the dates / 1st chart
     */
    $barChart4 = new barchart_3($chart4_config, $chart4_s1, $chart4_s2);
    $barChart4->drawChart();
    
    echo "</div>";
    echo "</td></tr>";
    echo "<tr><td>";
    echo "<div id='jqchart1' style='float: left;border:0px solid #CCC; width: 500px; padding: 5px'>";
//    echo $chart2_data;
    echo "<h2>Average Receipt Value(kr) Increased:</h2>";
    /*
     * Cashiers chart during the dates / 2nd chart
     */
    
    $barChart2 = new barchart($chart2_config, $chart2_data);
    $barChart2->drawChart();
    
    echo "</div>";
    echo "</td></tr>";
    echo "</table>";
    
    
    include '../include/jqplot-js.php';
    
    echo "<div id='individual-chart'><img src='images/loader.gif' />";
        
    echo "</div>";
//    
    $group_name = $user->getGroupName();
//    //echo $group_name;
    if($group_name == 'twosell_super' || $group_name == 'twosell' || $group_name == 'chain'){
        echo "<div>";
        echo "<p><b>Cashier Individual Statistics:</b></p>";
        foreach($cashierList as $cashier){
            $cashier_data = json_encode($dataArray[$cashier]);
            $a_id = $select_store.",,".$cashier.",,".$store7days.",,".$cashier_data;
//            print_r($dataArray[$cashier]);
            echo "<a id='".$a_id."' class='showIndividualCashier' href='#' >".$cashier."</a>, ";
        }
        echo "</div>";
    }
//    echo "<div id='nav_div'></div>";
 ?>
