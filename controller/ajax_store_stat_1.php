<?php
// store stat
session_start();
include '../functions/commonFunction.php';
include '../admin_config.php';
?>
<script>
    $(document).ready(function(){
        $('#nav_div').load('controller/navigation_search.php').fadeIn("slow");    
        $('.a_li').click(function(){
            var a = $(this).attr('id');
            $("#nav_div").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/navigation_search.php",{z:a},function(data){
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
        $store = $_POST['store'];
        $cashier = $_POST['cashier'];
        $cas_null = "Choose a Cashier »";
        if($cashier == $cas_null)
            $cashier = '';
        $fromDate = $_POST['fromDate'];
        $toDate = $_POST['toDate'];
        if($toDate != ""){
            $toDate = strtotime($toDate);
            $toDate = date("Y-m-d", strtotime('+24 hours', $toDate));
            //echo $toDate;
        }
        $fromTime = $_POST['fromTime'];
        $toTime = $_POST['toTime'];
        if($store != ""){
         //   echo "store=".$store."-cashier=".$cashier."-fromDate=".$fromDate."-Todate=".$toDate."-Fromtime".$fromTime."-Totime=".$toTime."<br />";
        // only Store    
        if($fromDate=="" && $toDate == "" && $cashier == "" && $fromTime == "" ){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' ORDER BY id";
           
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // Store and Cashier
        else if( $cashier != "" && $fromDate=="" && $toDate == "" && $fromTime == "" ){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id='$cashier' ORDER BY id";
           
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id='$cashier' AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // store and cashier and fromdate
        else if($fromDate!="" && $toDate == "" && $cashier!="" && $fromTime == "" ){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id='$cashier' AND date(time_of_purchase) = '$fromDate' ORDER BY id";
           
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id='$cashier' AND date(time_of_purchase) = '$fromDate' AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // store and cashier and fromdate and todate
        else if($fromDate != "" && $toDate != "" && $cashier != "" && $fromTime == ""){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id='$cashier' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate')";
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id='$cashier' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate') AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        
        }
        // store and fromdate 
        else if($fromDate != "" && $toDate == "" && $cashier == "" && $fromTime == ""){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND DATE(time_of_purchase) = '$fromDate'";
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // store and fromdate and todate
        else if($fromDate != "" && $toDate != "" && $cashier == "" && $fromTime == ""){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate')";
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate') AND direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // store and cashier and fromdate and todate and fromtime
        else if($fromDate != "" && $toDate != "" && $fromTime != "" && $cashier != "" && $toTime == ""){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate') AND HOUR(time_of_purchase) = '$fromTime'";
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase BETWEEN '$fromDate' AND '$toDate') AND HOUR(time_of_purchase) = '$fromTime' AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // store and cashier and fromdate and fromtime
        else if($fromDate != "" && $toDate == "" && $fromTime != "" && $cashier != "" && $toTime == ""){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id = '$cashier' AND date(time_of_purchase) = '$fromDate' AND HOUR(time_of_purchase) = '$fromTime'";
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id = '$cashier' AND date(time_of_purchase) = '$fromDate' AND HOUR(time_of_purchase) = '$fromTime' AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // store and cashier and fromdate and fromtime and totime
        else if($fromDate != "" && $toDate == "" && $fromTime != "" && $toTime != "" && $cashier != ""){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id = '$cashier' AND date(time_of_purchase) = '$fromDate' AND (HOUR(time_of_purchase) >= '$fromTime' AND HOUR(time_of_purchase) <= '$toTime')";
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id = '$cashier' AND date(time_of_purchase) = '$fromDate' AND (HOUR(time_of_purchase) >= '$fromTime' AND HOUR(time_of_purchase) <= '$toTime') AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // store and fromdate and fromtime
        else if($fromDate != "" && $toDate == "" && $fromTime != "" && $toTime == "" && $cashier == ""){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND HOUR(time_of_purchase) = '$fromTime'";
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND HOUR(time_of_purchase) = '$fromTime' AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // store and fromdate and fromtime and totime
        else if($fromDate != "" && $toDate == "" && $fromTime != "" && $toTime != "" && $cashier == ""){
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND (HOUR(time_of_purchase) >= '$fromTime' AND HOUR(time_of_purchase) <= '$toTime')";
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND date(time_of_purchase) = '$fromDate' AND (HOUR(time_of_purchase) >= '$fromTime' AND HOUR(time_of_purchase) <= '$toTime') AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
        // store and cashier and fromdate and todate and from time and totime
        else if($fromDate != "" && $toDate != "" && $fromTime != "" && $cashier != "" && $toTime != ""){
            $toDate1 = strtotime($toDate);
            $toDate1 = date("Y-m-d", strtotime('-24 hours', $toDate1));
            $from = $fromDate." ".$fromTime.":0:0";
            $to = $toDate1." ".$toTime.":0:0";
            
            $q = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase >= '$from' AND time_of_purchase <= '$to')";
            $q_twosell = "SELECT * FROM twosell_purchase_test WHERE store='$store' AND seller_id = '$cashier' AND (time_of_purchase >= '$from' AND time_of_purchase <= '$to') AND  direct_gross_incl_vat > 0 ORDER BY time_of_purchase DESC"; 
        }
//        echo $q;
        
        include '../dbconnect.php';
//        echo $q."<br />";
//        echo $q_twosell;
        $q1 = mysql_query($q);
        if($q1 != FALSE){
            $_SESSION['query'] = $q_twosell;
            $total_final = mysql_num_rows($q1);
            $all_final_products = count_items_array($q1, array("n_rows", "total_cost"));
            //echo $all_final_products;
            echo "<b>Total Final Receipt:</b> ".$total_final;
        
            $q_twosell_1 = mysql_query($q_twosell);
            $q_twosell_2 = mysql_query($q_twosell);
            // calculates the sum of the twosell_table fields
            $twosell_all_products = count_items_array_banned($q_twosell_2, array("n_rows", "twosell_item_count","total_cost", "direct_gross_incl_vat"));
            //print_r($twosell_all_products);
            //echo $twosell_final_products;
            $total = mysql_num_rows($q_twosell_1);
            if($total_final !=0) $twosell_percent = round((100*$total)/$total_final, 2);
            echo "<br /><b>Twosell Sell:</b> ".$total." ($twosell_percent%)";
            
            echo "<br /><br /><b>Total Cost: </b>".$all_final_products['total_cost'];
            echo "<br /><b>Total Twosell Cost: </b>".$twosell_all_products['direct_gross_incl_vat'];
            if($all_final_products['total_cost'] != 0) $total_per =  round(($twosell_all_products['direct_gross_incl_vat'] * 100) / $all_final_products['total_cost'], 2);
            else $total_per = 0;
            echo " ($total_per %)";
            if($total_final > 0){
                $avg_pro = round($all_final_products['n_rows'] / $total_final, 1);
            }
            else{
                $avg_pro = 0;
            }
            //
            echo "<br /><br />Total Products in all the receipts: <b>".$all_final_products['n_rows']."</b>, Average Product per Receipt: <b>$avg_pro</b><br />";
            echo "Total Twosell Products in all the receipts: <b>".$twosell_all_products[twosell_item_count]."</b><br />";
            $_SESSION['twosell_all_products'] = $twosell_all_products;
            echo "<div id='nav_div'>";
            
            echo "</div>";
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
