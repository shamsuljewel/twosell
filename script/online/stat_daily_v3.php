<?php
function banned_groups(){
    $banned_group = array();
    $q = "SELECT group_id FROM exclude_groups WHERE chain_id=1 ORDER BY group_id";
    $q1 = mysql_query($q) or die(mysql_error());
    while($rows = mysql_fetch_array($q1)){
        $banned_group[] = $rows['group_id'];
    }
    return $banned_group;
}
function banned_products($banned_groups){
    $banned_products = array();
    mysql_select_db('statoil_canonical');
    foreach ($banned_groups as $value) {
        $q = "SELECT p.articlenum FROM twosell_product as p, tsln_product_group as t 
              where t.product_id = p.id AND group_id = '$value'";
        $q1 = mysql_query($q) or die(mysql_error());
        while($q2 = mysql_fetch_array($q1)){
            $banned_products[] = $q2['articlenum'];
        }
    }
    return $banned_products;
}
function orig_twosell($ref_id, $items, $cost){
    $banned_groups = banned_groups();
    $banned_products = banned_products($banned_groups);
    mysql_select_db('statistics_test');
    $q_t_items = "SELECT * FROM transaction_tbl WHERE ref_purchase = '$ref_id' LIMIT 1";
    $q_t_items1 = mysql_query($q_t_items) or die(mysql_error()); 
    $q_t_items2 = mysql_fetch_array($q_t_items1);
    if($q_t_items2['twosell_items'] != NULL) $twosell_items = json_decode($q_t_items2['twosell_items'], true);
//            //print_r($twosell_items);
    if(!empty($twosell_items['items'])){
        foreach ($twosell_items['items'] as $key => $value) {
        //      echo $value."->";
            if($value == ""){
                $all_items = json_decode($q_t_items2['items'], true);
                $all_item_count = count($all_items['items']);
                // echo  $all_item_count;
                //print_r($all_items[items]);
                for($i=0; $i< $all_item_count; $i++){
                    $all_items_list[$i] = $all_items['items'][$i]['item_id'];
                }
                //print_r($all_items_list);
                $search_key = array_keys($all_items_list, $value);
                if(!empty($search_key)){
                    $net_amount = $all_items['items'][$search_key[0]]['amount'] - $all_items['items'][$search_key[0]]['discount'];
                    $cost -= $net_amount;
                    $items -= 1;
                }
            }
            else{
                if(in_array($value, $banned_products)){
                // echo "banned!";
                    $all_items = json_decode($q_t_items2['items'], true);
                    $all_item_count = count($all_items['items']);
                    // echo  $all_item_count;
                    //print_r($all_items[items]);
                    for($i=0; $i< $all_item_count; $i++){
                        $all_items_list[$i] = $all_items['items'][$i]['item_id'];
                    }
                    //print_r($all_items_list);
                    $search_key = array_keys($all_items_list, $value);
                    if(!empty($search_key)){
                        $net_amount = $all_items['items'][$search_key[0]]['amount'] - $all_items['items'][$search_key[0]]['discount'];
                        $cost -= $net_amount;
                        $items -= 1;
                    }
                }
            }
        }
    }
    $return_array = array("$items","$cost");
    return $return_array;
}
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
    $temp_tbl = "stat_temp";
    $store_tbl = "stat_store";
    $cashier_tbl = "stat_cashier";
    
    $error = "";
    
    //$date = '2012-09-25';
    //for($date_index=0; $date_index < 4; $date_index++){
    $twosell_org_array = array();
    $date = date("Y-m-d");
    $date = date('Y-m-d', strtotime('-1 days', strtotime($date)));
    $dateTime = date("Y-m-d H:i:s");
    $empty = "TRUNCATE TABLE $temp_tbl";
    mysql_query($empty);
    // Get all the cashier sale from the twosell_purchase_test table
    $q_all = "SELECT COUNT( p.id ) AS count_receipt, store AS store_id, seller_id AS cashier_id, sum(total_cost) AS total_cost
                FROM twosell_purchase_test AS p
                WHERE DATE( p.time_of_purchase ) =  '$date'
                GROUP BY store,seller_id
                ORDER BY store";
    //echo $q_all;
    $q_all1 = mysql_query($q_all) or die(mysql_error());
    if($q_all1 != FALSE){
        if(mysql_num_rows($q_all1) > 0){
      //      echo "ok";
            // Save All the cashier Sell Into the temp table with twosell = 0
            while($q_all2 = mysql_fetch_array($q_all1)){
                $q = "INSERT INTO $temp_tbl(s_id, c_id, total, twosell, total_cost) VALUES('$q_all2[store_id]','$q_all2[cashier_id]','$q_all2[count_receipt];','0','$q_all2[total_cost]')";
                //echo $q;
                mysql_query($q) or die(mysql_error()); 
            }
        }
        // get all the twosell sell records which direct_gross_incl_vat > 0
        // But I now need to check if that twosell is not banned group or that is not a null
        $q_twosell = "SELECT p.id, p.store AS store_id, p.seller_id AS cashier_id,twosell_item_count, p.direct_gross_incl_vat AS twosell_cost
                    FROM twosell_purchase_test AS p
                    WHERE DATE( p.time_of_purchase ) =  '$date'
                    AND p.direct_gross_incl_vat > 0
                    ORDER BY store";
        //echo $q_twosell;
        $q_twosell1 = mysql_query($q_twosell);
        if($q_twosell1!=FALSE){
            if(mysql_num_rows($q_twosell1)>0){
                $twosell_org_array[$q_twosell2['store_id']][$q_twosell2['cashier_id']]['twosell_cost'] = 0;
                $twosell_org_array[$q_twosell2['store_id']][$q_twosell2['cashier_id']]['total_twosell_item'] = 0;
                $twosell_org_array[$q_twosell2['store_id']][$q_twosell2['cashier_id']]['total_twosell_receipt'] = 0;
                while($q_twosell2 = mysql_fetch_array($q_twosell1)){
                    $twosell = orig_twosell($q_twosell2['id'], $q_twosell2['twosell_item_count'], $q_twosell2['twosell_cost']);
                    $twosell_org_array[$q_twosell2['store_id']][$q_twosell2['cashier_id']]['twosell_cost'] += $twosell[1]; 
                    $twosell_org_array[$q_twosell2['store_id']][$q_twosell2['cashier_id']]['total_twosell_item'] += $twosell[0];
                    if($twosell[0] > 0)
                        $twosell_org_array[$q_twosell2['store_id']][$q_twosell2['cashier_id']]['total_twosell_receipt'] += 1;                    
                    
                }
                $keys = array_keys($twosell_org_array);
                //print_r($keys);
                for($i=0; $i< count($keys); $i++){
                    $cashiers = array_keys($twosell_org_array[$keys[$i]]);
                    //print_r($cashiers);
                    for($j=0;$j < count($cashiers); $j++){
                        $store = $keys[$i];
                        $cashier = $cashiers[$j];
                        $twosell_cost = $twosell_org_array[$store][$cashier]['twosell_cost'];
                        $total_twosell_item = $twosell_org_array[$store][$cashier]['total_twosell_item'];
                        $total_twosell_receipt = $twosell_org_array[$store][$cashier]['total_twosell_receipt'];
                        //echo "<br>".$store.", ".$cashier.", ".$total_twosell_item.", ".$total_twosell_receipt.", ".$twosell_cost;
                        $q = "INSERT INTO $temp_tbl(s_id, c_id, total, twosell, twosell_cost) 
                        VALUES('$store','$cashier;','$total_twosell_receipt','1', '$twosell_cost')";
                        mysql_query($q) or die(mysql_error()); 
                    }
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
    //echo $error;
//}    
//    $q_in = "INSERT INTO twosell_daily_statistics_log(date_time, message) VALUES('$dateTime','$error')";
//    mysql_query($q_in) or die(mysql_error());
    // save the logdatabase for this daily_statistics_log table
?>
