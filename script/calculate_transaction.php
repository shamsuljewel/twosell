<?php
$script_name = "calculate_transaction";
/*
 * This script will check each new final_receipt from final_data table and findout the 
 * corresponding preReceipt from request_data table
 * corresponding offerstatus from offerstatus_data table
 * corresponding response from response_data table
 * and saves the calculation on to twosell_purchase table with the 4 table reference id
 */
include 'dbconnect.php';
include 'commonFunction.php';
/************************************************/
/************ Define the tables *****************/
$statistics_database = "statistics_test";
$final_data = "final_data";
$request_data = "request_data";
$twosell_purchase = "twosell_purchase_last";
$script_tbl = "script_tbl";
/***********************************************/


$banned_groups =  banned_groups();
$banned_products = banned_products($banned_groups);
//print_r($banned_products);

mysql_select_db($statistics_database);
$q_get_variables = "SELECT * FROM $script_tbl WHERE name='$script_name' LIMIT 1";
//echo $q_get_variables;
$q_get_variables1 = mysql_query($q_get_variables) or die(mysql_error());
$script = mysql_num_rows($q_get_variables1);
//echo $script;

$final_id_result = mysql_fetch_array(mysql_query("SELECT MAX(id) as max_id FROM $final_data"));
if($script > 0){
    $q_get_variables2 = mysql_fetch_array($q_get_variables1);
    $from_final_id = $q_get_variables2['last_processed_id'];
    $to_final_id = $final_id_result['max_id'];
}
else{
    $from_final_id = 0;
    $to_final_id = $final_id_result['max_id'];
}

$error = array();
//$error[] = "system failure";

$q = "SELECT * FROM (SELECT  f.items as final_items, r.items as req_items, f.id as fid, r.id as rid,f.cashier, 
                        f.trans as final_trans,r.trans as req_trans, 
                        f.jogging_log_id as final_log, r.jogging_log_id as req_log_id, 
                        f.date_time_log as final_log_time, 
                        f.date_time_receipt as final_rec_time, r.date_time_log as req_time, 
                        r.store,r.device, r.serial FROM $request_data as r, 
                        $final_data as f WHERE f.store=r.store 
                        AND f.device=r.device 
                        AND f.trans = r.trans 
                        AND f.serial_chk = r.serial 
                        AND HOUR(timediff(f.date_time_log ,r.date_time_log)) <= 24
                        AND f.date_time_log >= r.date_time_log
                        AND (f.id>$from_final_id AND f.id<=$to_final_id)
                        ORDER BY req_time DESC ) AS tbl
                        GROUP BY fid";
// AND (f.id>$from_final_id AND f.id<=$to_final_id)
$q1 = mysql_query($q) or die(mysql_error());
$found_records = mysql_num_rows($q1);
$twosell_found = 0;
$screen_time = 0;
if($found_records > 0){
    mysql_create_table($twosell_purchase);
    while($q2 = mysql_fetch_array($q1)){
        $fid = $q2['fid'];
        $rid = $q2['rid'];
        $trans = $q2['final_trans'];
        $store = $q2['store'];
        $cashier = $q2['cashier'];
        $device = $q2['device'];
        $log_time = $q2['final_log_time'];
        $rec_time = $q2['final_rec_time'];
        if($rec_time == NULL) $rec_time = $log_time;
        $final_items = json_decode($q2['final_items']);
        $req_items = json_decode($q2['req_items']);
//        print_r($final_items)."<br />";
//        print_r($req_items)."<br /><br />"; 
        $total_item = count($final_items->items);
        $total_pre = count($req_items->items);
        $temp_final= array();
        $temp_pre = array();
        $total_cost = 0;
        $total_discount = 0;
        $total_cost_wgas = 0;
        $total_discount_wgas = 0;
        $total_cost_wogas = 0;
        $total_discount_wogas = 0;
        $total_item_wogas = $total_item;
        $total_pre_wogas = $total_pre;
        $gas_or_null = 0;
        for($i=0;$i<$total_item;$i++){
            $total_cost += $final_items->items[$i]->amount;
            $total_discount += $final_items->items[$i]->discount;
            if($final_items->items[$i]->id == NULL || in_array($final_items->items[$i]->id, $banned_products)){
                $total_cost_wgas += $final_items->items[$i]->amount;
                $total_discount_wgas += $final_items->items[$i]->discount;
                $gas_or_null = 1;
            }else{
                $temp_final[$i] = $final_items->items[$i]->id;
            }
        }
        //print_r($temp_final);
        $total_cost_wogas = $total_cost - $total_cost_wgas;
        $total_discount_wogas = $total_discount - $total_discount_wgas;
        
        for($i=0;$i<$total_pre;$i++){
            if($req_items->items[$i]->id != NULL && (in_array($req_items->items[$i]->id, $banned_products)) === FALSE){
                $temp_pre[$i] = $req_items->items[$i]->id;
            }
        }
        //print_r($temp_pre);
        $total_item_wogas = count($temp_final);
        $total_pre_wogas = count($temp_pre);
        $twosell = 0;
        $twosell_count_product = 0;
        $twosell_cost = 0;
        $twosell_items = array_diff_once($temp_final, $temp_pre);
        //print_r($twosell_items);
        if(!empty($twosell_items)){
            $twosell = 1;
            $twosell_found++;
            $twosell_keys = array_keys($twosell_items);
            //print_r($twosell_keys);
            $twosell_count_product = count($twosell_keys);
            for($l=0; $l < $twosell_count_product; $l++){
                $twosell_cost += $final_items->items[$twosell_keys[$l]]->amount - $final_items->items[$twosell_keys[$l]]->discount;
            }
//            print_r($twosell_items);
        }
//        print_r($temp_final);
//        print_r($temp_pre);
        
//        echo "gas_or_null ".$gas_or_null."<br />";
//        echo "final_id=".$fid."<br />";
//        echo "req_id=".$rid."<br />";
//        echo "Total_final_item=".$total_item."<br /> ";
//        echo "Total_pre_item=".$total_pre."<br /> Log Time=".$log_time."<br /> ";
//        echo "total_cost=".$total_cost."<br />";
//        echo "total_cost wogas=".$total_cost_wogas."<br />";
//        echo "total disc=".$total_discount."<br />";
//        echo "total disc wogas=".$total_discount_wogas."<br />";
//        echo "total_item wo gas=".$total_item_wogas."<br />";
//        echo "total_pre wo gas=".$total_pre_wogas."<br />";
//        echo "twosell = ".$twosell."<br />";
//        echo "twosell products=".$twosell_count_product."<br />";
//        echo "twosell_cost=".$twosell_cost."<br /><br />";
        $trans_format = $store."-".$device."-".$trans."-".$rec_time;
        $q_update_check = "SELECT id FROM $twosell_purchase WHERE transactionid = '$trans_format' LIMIT 1";
        $q_update_check1 = mysql_query($q_update_check);
        if($q_update_check1 != FALSE){
            if(mysql_num_rows($q_update_check1) > 0){
                $q_update_check2 = mysql_fetch_array($q_update_check1);
                $update_id = $q_update_check2['id'];
                // already exists and now it will update the transaction data
                $update_purchase = "UPDATE $twosell_purchase SET direct_gross_incl_vat = '$twosell_cost', twosell_item_count = $twosell_count_product WHERE id='$update_id' LIMIT 1";
                if(!mysql_query($update_purchase)){
                    $error[] = "Update transaction error". mysql_error();
                }
            }
            else{
                // no this transaction is not exists so it will insert it into the database
                $q = "INSERT INTO $twosell_purchase(transactionid, time_of_purchase, final, pos_id, seller_id, 
                n_rows, total_cost, direct_gross_incl_vat,store, screen_time, 
                twosell_item_count, total_discount,gas_or_null,total_cost_ban,total_item_ban,
                time_received,final_id,pre_id)
                VALUES('$trans_format','$rec_time','1','$device','$cashier',
                '$total_item','$total_cost', '$twosell_cost','$store','$screen_time', 
                '$twosell_count_product', '$total_discount','$gas_or_null','$total_cost_wogas','$total_item_wogas',
                '$log_time','$fid','$rid')";
                if(!mysql_query($q)){
                    $error[] = "Insert Query Error ". mysql_error();
                }
            }
        }
    }
}
if(!empty($error)){
    $json_error = json_encode(array('error'=>$error));
}
else{
    $json_error = "";
}
$error_log = $q_get_variables2['error_log'].$json_error;
$dateTime = date('Y-m-d H:i:s');
$update_script_tbl = "UPDATE $script_tbl SET last_processed_id = $to_final_id, datetime='$dateTime', error_log='$error_log' WHERE name='$script_name' LIMIT 1";
//echo $update_script_tbl;
mysql_query($update_script_tbl);

echo "Total Final match found = ".$found_records."<br />";
echo "Twosell Records Found = ".$twosell_found;
?>
