<?php
function save_data($date, $temp_tbl, $store_tbl, $cashier_tbl){
    
    $cashier_total = array();
    $cashier_twosell = array();
    $q_c = "SELECT s_id,c_id, sum(total) as total, twosell_cost 
            FROM $temp_tbl 
            WHERE twosell=1
            GROUP BY s_id,c_id ORDER BY s_id";
    $q_c1 = mysql_query($q_c);
    if($q_c1 != FALSE){
        $i = 1;
        while($q2 = mysql_fetch_assoc($q_c1)){
            $store_id = $q2['s_id'];
            $cashier_id = $q2['c_id'];
            $total = $q2['total'];
            $twosell_cost = $q2['twosell_cost'];
            
            $cashier_twosell[0][$i] = $store_id;
            $cashier_twosell[1][$i] = $cashier_id;
            $cashier_twosell[2][$i] = $total;
            $cashier_twosell[3][$i] = $twosell_cost;
            $i++;
        } 
        $q = "SELECT s_id, c_id, sum(total) AS total,total_cost  FROM $temp_tbl 
            WHERE twosell=0
            GROUP BY s_id,c_id ORDER BY s_id";
        $q1 = mysql_query($q);
        if($q1 != FALSE){
            $i = 1;

            while($q2 = mysql_fetch_assoc($q1)){
                $store_id = $q2['s_id'];
                $cashier_id = $q2['c_id'];
                $total = $q2['total'];
                $cashier_total[0][$i] = $store_id;
                $cashier_total[1][$i] = $cashier_id;
                $cashier_total[2][$i] = $total;
                $cashier_total[3][$i] = $q2['total_cost'];
                $i++;
            }
        }
    }

    for($i=1; $i <= count($cashier_total[0]); $i++){
        $s_id = $cashier_total[0][$i];
        $c_id = $cashier_total[1][$i];
        $total = $cashier_total[2][$i];
        $total_cost = $cashier_total[3][$i];
        //echo "Searching: ".$s_id;
        $key = "";
        for($j=1; $j < count($cashier_twosell[0]); $j++){
            if($c_id == $cashier_twosell[1][$j] && $s_id == $cashier_twosell[0][$j]){
                $key = $j;
                break;
            }
        }
        
        if($key != ""){
        //echo "key = ".$key;
            $t = $cashier_twosell[2][$key];  
            $twosell_cost = $cashier_twosell[3][$key];
        }
        else{
            $t = 0;
            $twosell_cost = 0;
        }
        //echo $s_id."->".$c_id."->".$key."->".$t."<br />";
        if($total!=0){
            $per = round(($t*100)/$total,2);
        }
        else{
            $per = 0;
        }
        //echo "val :".$t."<br />";
        $dateTime = date("Y-m-d H:i:s");
        //echo $store_total[0][$i]['store_id'];
        $q_i = "INSERT INTO $cashier_tbl(s_id, c_id, total_receipt, total_twosell, per, total_cost, twosell_cost, date, insert_date)
                VALUES('$s_id','$c_id','$total','$t','$per','$total_cost','$twosell_cost', '$date','$dateTime')";
        mysql_query($q_i) or die(mysql_error());
    //   echo $q_i."<br />";

    }
    // now calculate the store statistics
    $q_store = "SELECT SUM( total_receipt) AS count_receipt, SUM(total_twosell) AS count_twosell, s_id AS store_id, SUM(twosell_cost) AS twosell_cost, SUM(total_cost) AS total_cost 
                FROM $cashier_tbl AS sc WHERE date = '$date' 
                GROUP BY s_id ORDER BY id";
    $q_store1 = mysql_query($q_store);
    if($q_store1!= FALSE){
        if(mysql_num_rows($q_store1) > 0){
            while($rows = mysql_fetch_array($q_store1)){
                $dateTime = date("Y-m-d H:i:s");
                if($rows['count_receipt'] != 0){
                    $per = round(($rows['count_twosell'] * 100) / $rows['count_receipt'], 2);
                }
                else $per = 0;
                $q_i = "INSERT INTO $store_tbl(s_id, total_receipt, total_twosell, per, total_cost, twosell_cost, date, insert_date)
                VALUES('$rows[store_id]','$rows[count_receipt]','$rows[count_twosell]','$per','$rows[total_cost]','$rows[twosell_cost]', '$date','$dateTime')";
                mysql_query($q_i) or die(mysql_error());
            }
        }
    }
}
    include 'dbconnect.php';
    include 'commonFunction.php';
    $temp_tbl = "stat_temp";
    $store_tbl = "stat_store";
    $cashier_tbl = "stat_cashier";
    
    $error = "";
    $date = '2012-09-28';
//  $date = date("Y-m-d");
    $toDate = '2012-08-21';
    $date_diff = dateDiff($toDate, $date);
    echo $date_diff;
    for($i=0; $i < $date_diff ; $i++){
    $date = date('Y-m-d', strtotime('-1 days', strtotime($date)));
    echo $date;
    $dateTime = date("Y-m-d H:i:s");
    $empty = "TRUNCATE TABLE $temp_tbl";
    mysql_query($empty);
    // Get all the cashier sale from the twosell_purchase_test table
    $q_all = "SELECT COUNT( p.id ) AS count_receipt, store AS store_id, seller_id AS cashier_id, sum(total_cost_ban) AS total_cost
                FROM twosell_purchase_last AS p
                WHERE DATE( p.time_of_purchase ) =  '$date'
                GROUP BY store,seller_id
                ORDER BY store";
    //echo $q_all;
    $q_all1 = mysql_query($q_all) or die(mysql_error());
    if($q_all1 != FALSE){
        echo mysql_num_rows($q_all1)."<br />";
        if(mysql_num_rows($q_all1) > 0){
      //      echo "ok";
            while($q_all2 = mysql_fetch_array($q_all1)){
                $q = "INSERT INTO $temp_tbl(s_id, c_id, total, twosell, total_cost) VALUES('$q_all2[store_id]','$q_all2[cashier_id]','$q_all2[count_receipt];','0','$q_all2[total_cost]')";
                //echo $q;
                mysql_query($q) or die(mysql_error()); 
            }
        }
        $q_twosell = "SELECT COUNT( p.id ) AS count_receipt, store AS store_id, seller_id AS cashier_id, sum(direct_gross_incl_vat) AS twosell_cost
                FROM twosell_purchase_last AS p
                WHERE DATE( p.time_of_purchase ) =  '$date'
                AND p.direct_gross_incl_vat > 0
                GROUP BY store,seller_id
                ORDER BY store";
        //echo $q_twosell;
        $q_twosell1 = mysql_query($q_twosell);
        if($q_twosell1!=FALSE){
            if(mysql_num_rows($q_twosell1)>0){
                while($q_twosell2 = mysql_fetch_array($q_twosell1)){
                    $q = "INSERT INTO $temp_tbl(s_id, c_id, total, twosell, twosell_cost) VALUES('$q_twosell2[store_id]','$q_twosell2[cashier_id];','$q_twosell2[count_receipt];','1', '$q_twosell2[twosell_cost]')";
                    mysql_query($q) or die(mysql_error()); 
                }
            }
            /*
             * now insert data into the store and cashier table
             */
            save_data($date, $temp_tbl, $store_tbl, $cashier_tbl);
        }else{
            $error = "Select Twosell Query Error";
        }
    }
    else{
        $error = "Select All query Error";
    }
    }
    //echo $error;
    
//    $q_in = "INSERT INTO twosell_daily_statistics_log(date_time, message) VALUES('$dateTime','$error')";
//    mysql_query($q_in) or die(mysql_error());
    // save the logdatabase for this daily_statistics_log table
?>