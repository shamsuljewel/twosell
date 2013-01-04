<?php
// store stat
session_start();
include '../functions/commonFunction.php';
include '../admin_config.php';
?>
<script>
    $(document).ready(function(){
        $('#nav_div').load('controller/navigation_search_fetch.php').fadeIn("slow");    
        $('.a_li').click(function(){
            var a = $(this).attr('id');
            $("#nav_div").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/navigation_search_fetch.php",{z:a},function(data){
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
if(isset($_POST['store'])){
        include '../dbconnectonline.php';
        $twosell_purchase = "twosell_purchase_last";
        $store = $_POST['store'];
        
        $cashier = $_POST['cashier'];
        $cas_null = "Choose a Cashier »";
        if($cashier == $cas_null)
            $cashier = '';
        $fromDate = $_POST['fromDate'];
        $toDate = $_POST['toDate'];
        $org_to_date = $toDate;
        if($toDate != ""){
            $toDate = strtotime($toDate);
            $toDate = date("Y-m-d", strtotime('+24 hours', $toDate));
            //echo $toDate;
        }
        $fromTime = $_POST['fromTime'];
        $toTime = $_POST['toTime'];
        if($store != ""){
         //   echo "store=".$store."-cashier=".$cashier."-fromDate=".$fromDate."-Todate=".$toDate."-Fromtime".$fromTime."-Totime=".$toTime."<br />";
        // only Store    / q: 1
        if($fromDate=="" && $toDate == "" && $cashier == "" && $fromTime == "" ){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' ORDER BY id";
           
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 1;
        }
        // Store and Cashier / q: 2
        else if( $cashier != "" && $fromDate=="" && $toDate == "" && $fromTime == "" ){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id='$cashier' ORDER BY id";
           
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id='$cashier' AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 2;
        }
        // store and cashier and fromdate query No = 3
        else if($fromDate!="" && $toDate == "" && $cashier!="" && $fromTime == "" ){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id='$cashier' AND date(time_of_purchase) = '$fromDate' ORDER BY id";
           
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id='$cashier' AND date(time_of_purchase) = '$fromDate' AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 3;
        }
        // store and cashier and fromdate and todate / q: 8
        else if($fromDate != "" && $toDate != "" && $cashier != "" && $fromTime == ""){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id='$cashier' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate')";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id='$cashier' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate') AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 8;
        }
        // store and fromdate query No : 4 
        else if($fromDate != "" && $toDate == "" && $cashier == "" && $fromTime == ""){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND DATE(time_of_purchase) = '$fromDate'";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 4;
        }
        // store and fromdate and todate / query NO: 7
        else if($fromDate != "" && $toDate != "" && $cashier == "" && $fromTime == ""){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate')";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate') AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 7;
        }
        // store and cashier and fromdate and todate and fromtime  / q: 9
        else if($fromDate != "" && $toDate != "" && $fromTime != "" && $cashier != "" && $toTime == ""){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate') AND HOUR(time_of_purchase) = '$fromTime'";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate') AND HOUR(time_of_purchase) = '$fromTime' AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 9;
        }
        // store and cashier and fromdate and fromtime / query no: 5
        else if($fromDate != "" && $toDate == "" && $fromTime != "" && $cashier != "" && $toTime == ""){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND date(time_of_purchase) = '$fromDate' AND HOUR(time_of_purchase) = '$fromTime'";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND date(time_of_purchase) = '$fromDate' AND HOUR(time_of_purchase) = '$fromTime' AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 5;
        }
        // store and cashier and fromdate and fromtime and totime / q: 13
        else if($fromDate != "" && $toDate == "" && $fromTime != "" && $toTime != "" && $cashier != ""){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND date(time_of_purchase) = '$fromDate' AND (HOUR(time_of_purchase) >= '$fromTime' AND HOUR(time_of_purchase) <= '$toTime')";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND date(time_of_purchase) = '$fromDate' AND (HOUR(time_of_purchase) >= '$fromTime' AND HOUR(time_of_purchase) <= '$toTime') AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 13;
        }
        // store and fromdate and fromtime / query No: 6
        else if($fromDate != "" && $toDate == "" && $fromTime != "" && $toTime == "" && $cashier == ""){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND HOUR(time_of_purchase) = '$fromTime'";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND HOUR(time_of_purchase) = '$fromTime' AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 6;
        }
        // store and fromdate and fromtime and totime / q: 14
        else if($fromDate != "" && $toDate == "" && $fromTime != "" && $toTime != "" && $cashier == ""){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND (HOUR(time_of_purchase) >= '$fromTime' AND HOUR(time_of_purchase) <= '$toTime')";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND (HOUR(time_of_purchase) >= '$fromTime' AND HOUR(time_of_purchase) <= '$toTime') AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 14;
        }
        // store and cashier and fromdate and todate and from time and totime / q: 12
        else if($fromDate != "" && $toDate != "" && $fromTime != "" && $cashier != "" && $toTime != ""){
            $toDate1 = strtotime($toDate);
            $toDate1 = date("Y-m-d", strtotime('-24 hours', $toDate1));
            $from = $fromDate." ".$fromTime.":0:0";
            $to = $toDate1." ".$toTime.":0:0";
            
//            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase >= '$fromDate' AND HOUR(time_of_purchase) >= '$fromTime') AND (time_of_purchase <= '$toDate' AND HOUR(time_of_purchase) <= '$toTime')";
//            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase >= '$fromDate' AND HOUR(time_of_purchase) >= '$fromTime') AND (time_of_purchase <= '$toDate' AND HOUR(time_of_purchase) <= '$toTime') AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase >= '$from' AND time_of_purchase <= '$to')";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase >= '$from' AND time_of_purchase <= '$to') AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 12;
        }
        // store, fromdate, todate, fromtime, totime / q: 11
        else if($fromDate != "" && $toDate != "" && $fromTime != "" && $cashier == "" && $toTime != ""){
            $toDate1 = strtotime($toDate);
            $toDate1 = date("Y-m-d", strtotime('-24 hours', $toDate1));
            $from = $fromDate." ".$fromTime.":0:0";
            $to = $toDate1." ".$toTime.":0:0";
//            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND (time_of_purchase >= '$fromDate' AND HOUR(time_of_purchase) >= '$fromTime') AND (time_of_purchase <= '$toDate' AND HOUR(time_of_purchase) <= '$toTime') AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND (time_of_purchase >= '$from' AND time_of_purchase <= '$to')";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND (time_of_purchase >= '$from' AND time_of_purchase <= '$to') AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 

            $queryNo = 11;
            
            
        }
        // store, fromdate, todate, fromtime / q: 10
        else if($fromDate != "" && $toDate != "" && $cashier == "" && $fromTime != ""){
            $q = "SELECT * FROM $twosell_purchase WHERE store='$store' AND (date(time_of_purchase) >= '$fromDate' AND HOUR(time_of_purchase) = '$fromTime') AND (date(time_of_purchase) <= '$toDate' AND HOUR(time_of_purchase) = '$fromTime')";
            $q_twosell = "SELECT * FROM $twosell_purchase WHERE store='$store' AND (date(time_of_purchase) >= '$fromDate' AND HOUR(time_of_purchase) = '$fromTime') AND (date(time_of_purchase) <= '$toDate' AND HOUR(time_of_purchase) = '$fromTime') AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
            $queryNo = 10;
        }
//        echo $q."<br />".$queryNo;
        
        include '../dbconnect.php';
        include '../class/all_item_exgas.php';
//        echo $q."<br />";
//        echo $q_twosell;
        $q1 = mysql_query($q) or die(mysql_error());
        if($q1 != FALSE){
            $_SESSION['query'] = $q_twosell;
          //  echo $q;
            $q_wogas = mysql_query($q);
            $total_final = mysql_num_rows($q1);
            $all_final_products = count_items_array($q1, array("n_rows", "total_cost", "total_item_ban", "total_cost_ban", "gas_or_null"));
            //$all_final_products_wogas = new all_item_exgas($q_wogas, array("n_rows", "total_cost"));
            //echo $all_final_products;
            
            
//            echo "<b>Total Final Receipt:</b> ".$total_final;
        
            $q_twosell_3 = mysql_query($q_twosell);
            // calculates the sum of the twosell_table fields
            include '../class/twosell_items.php';
            
            $total_twosell_receipt = mysql_num_rows($q_twosell_3);
            $twosell_items = count_items_array($q_twosell_3, array("twosell_item_count", "direct_gross_incl_vat", "total_item_ban", "total_cost_ban", "n_rows"));
            
           
            if($total_final !=0) $twosell_percent = round((100*$total_twosell_receipt)/$total_final, 2);
            if($all_final_products['total_cost_ban'] != 0) $total_per =  round(($twosell_items['direct_gross_incl_vat'] * 100) / $all_final_products['total_cost_ban'], 2);
            else $total_per = 0;
            if($total_final > 0){
                $avg_pro = round($all_final_products['total_item_ban'] / $total_final, 2);
                $avg_before = round(($all_final_products['total_item_ban'] - $twosell_items['twosell_item_count'])/$total_final,2);
            }
            else{
                $avg_pro = 0;
                $avg_before = 0;
            }
            $q_1st = "SELECT MAX(id) as max, MAX(time_of_purchase) as time_of_purchase FROM $twosell_purchase";
            $q_1st1 = mysql_query($q_1st) or die(mysql_error());
            $q_1st2  =  mysql_fetch_array($q_1st1);
            
            $q_last = "SELECT MIN(id) as min, MIN(time_of_purchase) as time_of_purchase FROM $twosell_purchase";
            $q_last1 = mysql_query($q_last) or die(mysql_error());
            $q_last2  =  mysql_fetch_array($q_last1);
            
            if($fromDate == NULL){
                $show_from_date = $q_1st2['time_of_purchase'];
                $show_to_date = $q_last2['time_of_purchase'];
            }
            else{
                if($fromDate != NULL && $toDate == NULL){
                    $show_from_date = $fromDate;
                    $show_to_date = $fromDate;
                }else{
                    $show_from_date = $fromDate;
                    $show_to_date = $org_to_date;
                }
            }
            
            if($cashier == NULL) $show_cashier = "ALL";
            else $show_cashier = $cashier;
            
            if($all_final_products['total_cost_ban'] > 0 && $total_final > 0){
                $avg_value_before = round(($all_final_products['total_cost_ban'] - $twosell_items['direct_gross_incl_vat']) / $total_final, 2);
                $avg_value_after = round($all_final_products['total_cost_ban'] / $total_final,2);
            }
            else{
                $avg_value_before = 0;
                $avg_value_after = 0;
            }
            $q_update = "SELECT datetime FROM script_tbl WHERE name='calculate_transaction' LIMIT 1";
            $result = sql_query($q_update, $link);
            //print_r($result[0]);
            $dateTime = date("Y-m-d H:i:s");
            if(gettype($result) == "array"){
                echo "<div align='right' style='width: 900px'><b>Statistics Updated at: </b>".$result[0][datetime]."<br />
                <b>Report Time:</b> ".$dateTime."    
                </div>";
//                echo "Last Final ID / Trans: ".$result[0][last_log_id]." / ".$result[0][last_final_receipt];
            }
            else{
                echo $result;
            }
            echo "<table class='view_statistics_tbl'>";
            echo "<tr><th>Report Period</th><td>";
            echo "<table><tr><td class='value'>".$show_from_date."</td><td>-</td><td class='value'>".$show_to_date."</td><td class='field'>Store: </td><td class='value'>".$store."</td><td class='field'>Cashier: </td><td class='value'>".$show_cashier."</td></tr></table>";    
            echo "</td></tr>";
            echo "<tr><th>Results</th><td>";
            echo "<table>
                    <tr><td class='field'>Total Sale</td><td class='field'>TS Contribute</td><td class='field'>Total Receipt</td><td class='field'>TS Receipt</td><td class='field'>Total Products</td><td class='field'>TS Products</td></tr>
                    <tr><td class='value'>".round($all_final_products['total_cost_ban'],2)."</td><td class='value'>".$twosell_items['direct_gross_incl_vat']."(".$total_per."%)</td><td class='value'>".$total_final."</td><td class='value'>".$total_twosell_receipt."(".$twosell_percent."%)</td><td class='value'>".$all_final_products['total_item_ban']."</td><td class='value'>".$twosell_items['twosell_item_count']."</td></tr>    
                </table>";    
            echo "</td></tr>";
            echo "<tr><th>Per Receipt</th><td>";
            echo "<table>
                    <tr><td class='field'>Products Before TS</td><td class='field'>Products After Twosell</td><td class='field'>Value Before</td><td class='field'>Value After</td></tr>
                    <tr><td class='value'>".$avg_before."</td><td class='value'>".$avg_pro."</td><td class='value'>".$avg_value_before."</td><td class='value'>".$avg_value_after."</td></tr>
                  </table>";    
            echo  "</td></tr>";
            echo "</table>";
            
            
            $_SESSION['twosell_all_products'] = $total_twosell_receipt;
            $_SESSION['n_rows'] = $all_final_products['total_item_ban'];
            $_SESSION['total_cost'] = $twosell_items['total_cost_ban'];
            $_SESSION['total_twosell'] = $twosell_items['twosell_item_count'];
            $_SESSION['total_item'] = $twosell_items['total_item_ban'];
            $_SESSION['total_twosell_cost'] = $twosell_items['direct_gross_incl_vat'];
            
            include '../class/user.php';
            $user = new user($_SESSION['user']['name']);
            //$group = $user->getGroupName();
            if($user->getGroupName() == "twosell_super" || $user->getGroupName() == "twosell"){ 
                echo "<div id='nav_div'>";

                echo "</div>";
            }
        }
        else {
            echo "Query Execution Error..please contact webmaster".  mysql_error();
        }
    }
    else{
       echo "Store is NULL";
   }
}
?>
