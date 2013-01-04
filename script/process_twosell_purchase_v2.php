<?php
/* json[0] saves the request or response 1st portion
 * json[1] saves the request json format string
 */

    
include 'config.php';
include 'dbconnectonline.php';
include 'commonFunction.php';
$error = array();
$q = "SELECT id, datetime, level, msg FROM jogging_log WHERE (datetime BETWEEN '$dateFrom' AND '$dateTo') AND level = 'INFO' ORDER BY id DESC";
//echo $q;
$result = sql_query($q, $linkOnline);
mysql_close($linkOnline);
//echo "<br />Close Online DB";
$transaction_tbl = "transaction_tbl_test";
$purchase_tbl = "twosell_purchase_test1";

$getType = gettype($result);
// if its an array then it successfully get the data
if($getType == "array"){
    // is a array of data
    include 'dbconnect.php';
    //echo "Connected to offline";
    if(mysql_create_table('twosell_purchase_test')){
        // twosell_purchase table created or already exists!!
        $hash = array();
        $final = array();
        $data = array();
        $offer_status_hash = array();
        $final_data = array();
        // response requests , twosell suggestions
        $twosell_suggestions_hash = array();
        $twosell_suggestions = array();
        $i = 0;
        $j = 0;
        $offer = 0;
        $final_not_find_preReceipt = 0;
        $sugg = 0;
        foreach($result as $row){
            //echo $row['msg']."<br />";
            $msgDateTime = $row['datetime'];
            $json = explode(':', $row['msg'], 2);
            $first_part = $json[0];
            // need to encode it to utf8 otherwise if swedish character there its not worked
            $second_part = utf8_encode($json[1]);
            /*
             * When it gets any final_receipt then it saves this into an array, later i will go by each final receipt and serach for the 
             * preliminary receipts for the same store, device and trans, but some cases the trans may be duplicated by 3-4 days
             * so have to use a condition to time difference of 24 hr
             */
            if($first_part == "receipt_final request"){
                $data = json_decode($second_part, true);
                // process final request
                $transPre = explode('-', $data['trans']);
                if($transPre != "test"){
                    $id = $row['id'];
                    $trans_ref = $data['trans'];
                    // Insert All finals into an array
                    if($trans_ref!=""){
                        // two array used, one is single contain only trans and another contain whole information of final receipt
                        $final[$i] = $trans_ref;
                        $final_data[$i] = $data;
                        // as the last processed final comes first so i saved this info into last_id and last_trans
                        if($i==0){
                            $last_id = $id;
                            $last_trans = $final_data[$i]['trans'];
                        }
                        $i++;
                    }
                }
            }
            /*
             * save all the pre requested receipts
             */
            else if($first_part == "receipt request"){
                // two array used same as final, preReceipt saves only the trans and hash saves all the data
                $data = json_decode($second_part, true);
                $hash[$j] =  $data;
                // All the preliminary receipts trans are on $preReceipt array // its for searching
                $preRecceipt[$j] = $data['trans'];
                $j++;
            }
            /*
             * Saves all the offer_status requests, this needs for getting the screen time and queue time
             */
            else if($first_part == "offer_status request"){
                $data = json_decode($second_part, true);
                $offer_status_hash[$offer] =  $data;
                $offer_status_trans[$offer] = $data['trans'];
                $offer++;
            }
            else if($first_part == "receipt response"){
                $data = json_decode($second_part, true);
                $twosell_suggestions_hash[$sugg] = $data;
                $twosell_suggestions[$sugg] = $data['trans'];
                $sugg++;
            }
        }
        $final_total = count($final);
        // check each final and search for its all trans from the main array
        for($i=0; $i < $final_total; $i++){
            // calculate screen time, its for all the final receipt so at first
            // Get the matched offer status with trans 
            $key_offer_status = array_keys($offer_status_trans, $final[$i]);
            if(!empty($key_offer_status)){
                // check store id
                if($offer_status_hash[$key_offer_status[0]]['store'] == $final_data[$i]['store']){
                    $screen_field = $offer_status_hash[$key_offer_status[0]]['screen'];
                    if(!empty($screen_field)){
                        //echo $final[$i];
                        $time1 = strtotime($screen_field[0]);
                        $time2 = strtotime($screen_field[1]);
//                        $dateTime1 = date("Y-m-d H:i:s", $time1);
//                        $dateTime2 = date("Y-m-d H:i:s", $time2);
//                        echo $dateTime1." < - > ".$dateTime2;
                        //print_r($screen_field)."<br />";
                        $screen_time = abs($time2 - $time1);
                        //echo $screen_time;
                        //break;
                    }
                    else{
                        $screen_time = "-0";
                    }
                }
                else{
                    $screen_time = "-0";
                }
            }
            else{
                $screen_time = "-0"; // can't calculate no value
            }
            //echo $screen_time."<br />";
            // Need to saves the twosell suggestions into the database ?? not now
            
            
            /*
             * start checking the preReceipts with the final_receipt from here
             */
            $keys_preReceipt = array_keys($preRecceipt, $final[$i]);
            
            $total_keys_preReceipt = count($keys_preReceipt);
            if($total_keys_preReceipt <= 0)
                $final_not_find_preReceipt++;
            //print_r($keys_preReceipt);
            
            /// check the serial of final check and to all the matching trans from PreReceipts
            for($j=0; $j < $total_keys_preReceipt; $j++){
                // check if the serial is same with the same trans and store id
                // apply time difference here
                $hour_diff_final_pre = dateTimeDiff($hash[$keys_preReceipt[$j]]['date_time_receipt'], $final_data[$i]['date_time_receipt']); 
                if(($hash[$keys_preReceipt[$j]]['serial'] == $final_data[$i]['serial_check']) && ($hash[$keys_preReceipt[$j]]['store']) == $final_data[$i]['store'] && $hour_diff_final_pre <=24){
                   // echo $hash[$keys_preReceipt[$j]][serial]." == ".$final_data[$i][serial_check];
//                    $keys_offer_status = array_keys($offer_status_trans, $final[$i]);
//                    echo "<br />";
//                    print_r($keys_offer_status);
//                    echo "<br />";
                    //echo $keys_preReceipt[$j].", ";    
                    $preRecceipt_count_item = count($hash[$keys_preReceipt[$j]]['items']);
                    $finalReceipt_count_item = count($final_data[$i]['items']);
                    // temporarily saves the pre receipts products ids
                    $temp_pre = array();
                    // temporarily saves the final receipts products ids
                    // this two array is used for finding the item difference
                    $temp_final = array();
                    // difference products ids are saved into this array, these are consider as the twosell sale
                    // for twosell items only, ithis will lastly convert into the json data
                    $diff_sale = array();
                    $pre_count_amount  = 0;
                    
                    for($k=0; $k < $preRecceipt_count_item; $k++){
                        $pre_count_amount = $pre_count_amount + $hash[$keys_preReceipt[$j]]['items'][$k]['quantity'];
                        $temp_pre[$k] = $hash[$keys_preReceipt[$j]]['items'][$k]['id'];
                    }
                    $final_count_amount  = 0;
                    for($k=0; $k < $finalReceipt_count_item; $k++){
                        $final_count_amount = $final_count_amount + $final_data[$i]['items'][$k]['quantity'];
                        $temp_final[$k] = $final_data[$i]['items'][$k]['id'];
                    }
                    $diff_sale = array_diff($temp_final, $temp_pre);
                    if(empty($diff_sale)) $diff_sale = array_diff_key($temp_final, $temp_pre);
                    
                    $cashier = $final_data[$i]['cashier'];
                    $datadateTime =  $final_data[$i]['datetime'];
                    $total_product = $finalReceipt_count_item;
                    $pos_id = $final_data[$i]['device'];
                    $total_cost = $final_data[$i]['total_amount'];
                    $trans = $final_data[$i]['trans'];
                    $store = $final_data[$i]['store'];
                    $total_discount = $final_data[$i]['total_discount'];
                    // this is use for iinserting into the twosell_purchase table for avoiding the duplicate we try to make new format
                    $trans_format = $store."-".$pos_id."-".$trans."-".date("Y-m-d", strtotime($datadateTime));
                    if(($finalReceipt_count_item > $preRecceipt_count_item) || ($final_count_amount > $pre_count_amount) || !empty($diff_sale)){
                        // for final items this will lastly convert into the json data
                        $phparray = array();
                        
                        $twosell_keys = array_keys($diff_sale);
                        //print_r($twosell_keys);
                        $twosel_cost = 0;
                        $twosell_count_product = count($twosell_keys);
                        for($l=0; $l < $twosell_count_product; $l++){
                            $twosel_cost += $final_data[$i]['items'][$twosell_keys[$l]]['amount'];
                            if($final_data[$i]['items'][$twosell_keys[$l]]['discount'] != 0)
                                $twosel_cost -= $final_data[$i]['items'][$twosell_keys[$l]]['discount'];
                        }
                        
                        // insert into table for statistics
                        
                        $q = "INSERT IGNORE INTO $purchase_tbl(transactionid, time_of_purchase, final, pos_id, seller_id, n_rows, total_cost, time_for_twosell, direct_gross_incl_vat,store, screen_time, twosell_item_count, total_discount)
                        VALUES('$trans_format','$datadateTime','1','$pos_id','$cashier','$total_product','$total_cost', '0', '$twosel_cost','$store','$screen_time', '$twosell_count_product', '$total_discount')";
                        if(!mysql_query($q, $link)){
                            $error[] = "Insert Query Error ". mysql_error();
                        }
                        else{
                            // insert into table for statistics view transaction details 
                            $last = mysql_insert_id();
                            // total item in the final_data items field
                            if($last>0){
                                //$final_items_count = count($final_data[$i]['items']);
                                // not insert into an array all the items sequencially
                                for($fi=0; $fi < $total_product; $fi++){
                                    $phparray[$fi]['item_id'] = $final_data[$i]['items'][$fi]['id'];
                                    $phparray[$fi]['quantity'] = $final_data[$i]['items'][$fi]['quantity'];
                                    $phparray[$fi]['amount'] = $final_data[$i]['items'][$fi]['amount'];
                                    $phparray[$fi]['discount'] = $final_data[$i]['items'][$fi]['discount'];
                                    $phparray[$fi]['tax_rate'] = $final_data[$i]['items'][$fi]['tax_rate'];
                                    $phparray[$fi]['article_name'] = $final_data[$i]['items'][$fi]['article_name'];
                                }
                                $json_items = json_encode(array('items' => $phparray));
                                $json_t_items = json_encode(array('items' => $diff_sale));
                                $q_insert = "INSERT IGNORE INTO $transaction_tbl(ref_purchase, transaction_id, items, twosell_items, datetime) 
                                    VALUES('$last','$trans_format','$json_items','$json_t_items', '$datadateTime')";
                                if(!mysql_query($q_insert, $link)){
                                    $error[] = "Insert Query Error ". mysql_error();
                                }
                            }
                        }
                        //echo $q."<br />";
                    }
                    else{
                        // sale but not twosell
                        $q = "INSERT IGNORE INTO twosell_purchase_test1(transactionid, time_of_purchase, final, pos_id, seller_id, n_rows, total_cost, time_for_twosell, direct_gross_incl_vat, store, screen_time, total_discount)
                              VALUES('$trans_format','$datadateTime','1','$pos_id','$cashier','$total_product','$total_cost', '0', '0.00','$store', '$screen_time','$total_discount')";
                        if(!mysql_query($q, $link)){
                            $error[] = "Insert Query Error ".  mysql_error();
                        }else{
                            // do not need the details but still saves into the database
                            $last = mysql_insert_id();
                            // total item in the final_data items field
                            if($last>0){
                                $final_items_count = count($final_data[$i]['items']);
                                // not insert into an array all the items sequencially
                                for($fi=0; $fi < $final_items_count; $fi++){
                                    $phparray[$fi]['item_id'] = $final_data[$i]['items'][$fi]['id'];
                                    $phparray[$fi]['quantity'] = $final_data[$i]['items'][$fi]['quantity'];
                                    $phparray[$fi]['amount'] = $final_data[$i]['items'][$fi]['amount'];
                                    $phparray[$fi]['discount'] = $final_data[$i]['items'][$fi]['discount'];
                                    $phparray[$fi]['tax_rate'] = $final_data[$i]['items'][$fi]['tax_rate'];
                                    $phparray[$fi]['article_name'] = $final_data[$i]['items'][$fi]['article_name'];
                                }
                                $json_items = json_encode(array('items' => $phparray));
                                $q_insert = "INSERT IGNORE INTO transaction_tbl_test(ref_purchase, transaction_id, items, datetime) 
                                    VALUES('$last','$trans_format','$json_items','$datadateTime')";
                                if(!mysql_query($q_insert, $link)){
                                    $error[] = "Insert Query Error ". mysql_error();
                                }
                            }
                        }
                        //echo $q."<br />";
                    }
                    // if any serial of preReceipt match , no need to check other receipts so break
                    break;
                }
            }
            // end final and preReceipt match loop
        }
        // end final total
        
    }
    $time = date("Y-m-d H:i:s");
    $q = "INSERT INTO twosell_statistics_crontab_log(date_time, last_log_id, last_final_receipt, final_processed, final_failed)
        VALUES('$time', '$last_id', '$last_trans', '$final_total', '$final_not_find_preReceipt')";
    if(!mysql_query($q, $link)){
        $error[] = "Insert Query Error Log ". mysql_error();
    }
    mysql_close($link);
//    echo "Total receipt Processed: ".$final_total."<br />";
//    echo "Can't Processed: ".$final_not_find_preReceipt."<br />";
}
else{
    // is a string and just echo it
    //echo $result;
}

?>
