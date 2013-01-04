<?php
/* json[0] saves the request or response 1st portion
 * json[1] saves the request json format string
 */
include 'config.php';
include '../dbconnect.php';
include '../functions/commonFunction.php';
$q = "SELECT id, datetime, level, msg FROM jogging_log WHERE (datetime BETWEEN '$dateFrom' AND '$dateTo') AND level = 'INFO' ORDER BY id DESC";
//echo $q;
$result = sql_query($q);
$getType = gettype($result);
//echo $getType;
if($getType == "array"){
    // is a array of data
    //print_r($result);
    if(mysql_create_table('twosell_purchase_test')){
        // twosell_purchase table created or already exists!!
        $hash = array();
        $final = array();
        $data = array();
        $i = 0;
        $j = 0;
        $offer = 0;
        foreach($result as $row){
            $json = explode(':', $row['msg'], 2);
            $first_part = $json[0];
            // need to encode it to utf8 otherwise if swedish character there its not worked
            $second_part = utf8_encode($json[1]);
            if($first_part == "receipt_final request"){
                $data = json_decode($second_part, true);
                // process final request
                $transPre = explode('-', $data[trans]);
             //   print_r($transPre);
                //if($transPre != "test"){
                    $id = $row['id'];
                    //echo $id.", ";
                    
                    $trans_ref = $data['trans'];
                    // Insert All finals into an array
                    if($trans_ref!=""){
                        
                        $final[$i] = $trans_ref;
                        $final_data[$i] = $data;
                        $i++;
                    }
                    else{
                        echo $id.", ";
                    }
                    // break;
               // }
            }
            
            else if($first_part == "receipt request"){
                $data = json_decode($second_part, true);
                $hash[$j] =  $data;
                $preRecceipt[$j] = $data[trans];
                $j++;
                    
//                    if($countItem < $countItem_ref){
//                        echo $store_ref."->".$trans_ref."-".$countItem_ref."-".$countItem."<br />";
//                        // twosell sell happens
//                        // selling item is more than last given item
//                        // insert the final sell into the database
//                        $extra_item = array_diff_key($items_ref, $data[items]);
//                        //print_r($extra_item);
//                        $keys = array_keys($extra_item);
//                        //print_r($keys);
//                        for($i=0; $i<count($keys); $i++){
//                            $twosell_total = $extra_item[$keys[$i]][amount];
//                        }
                        //echo $twosell_total;
//                        $q = "INSERT INTO twosell_purchase_test(transactionid, time_of_purchase, final, pos_id, seller_id, n_rows, total_cost, time_for_twosell, direct_gross_incl_vat)
//                              VALUES('$trans','$dateTime','1','$device_ref','$cashier_ref','$countItem_ref','$total_amount', '0', '$twosell_total')";
//                        if(!mysql_query($q)){
//                            echo "Insert Query Error";
//                        }
                   // }
                  //  else{
                        // sell but not twosell sell
//                        $q = "INSERT INTO twosell_purchase_test(transactionid, time_of_purchase, final, pos_id, seller_id, n_rows, total_cost, time_for_twosell, direct_gross_incl_vat)
//                              VALUES('$trans','$dateTime','1','$device_ref','$cashier_ref','$countItem_ref','$total_amount', '0', '0.00')";
//                        if(!mysql_query($q)){
//                            echo "Insert Query Error";
//                        }
                //    }
                    // end process
               // }
            }
            else if($first_part == "offer_status request"){
                $data = json_decode($second_part, true);
                $offer_status_hash[$offer] =  $data;
                $offer_status_trans[$offer] = $data[trans];
                $offer++;
            }
            
        }
        $final_total = count($final);
        // check each final and search for its all trans from the main array
        //print_r($final_data);
     //   print_r($preRecceipt);
        //print_r($hash);        
        for($i=0; $i < $final_total; $i++){
            
            $keys_preReceipt = array_keys($preRecceipt, $final[$i]);
            //print_r($keys_preReceipt);
            /// check the serial of final check and to all the matching trans from PreReceipts
            for($j=0; $j< count($keys_preReceipt); $j++){
                if($hash[$keys_preReceipt[$j]][serial] == $final_data[$i][serial_check]){
                    echo $final[$i]."->";
                    echo $hash[$keys_preReceipt[$j]][serial]." == ".$final_data[$i][serial_check];
//                    $keys_offer_status = array_keys($offer_status_trans, $final[$i]);
//                    echo "<br />";
//                    print_r($keys_offer_status);
//                    echo "<br />";
                    //echo $keys_preReceipt[$j].", ";    
                    $preRecceipt_count_item = count($hash[$keys_preReceipt[$j]][items]);
                    $finalReceipt_count_item = count($final_data[$i][items]);
                    $pre_count_amount  = 0;
                    for($k=0; $k< $preRecceipt_count_item; $k++){
                        $pre_count_amount = $pre_count_amount + $hash[$keys_preReceipt[$j]][items][$k][quantity];
                    }
                    $final_count_amount  = 0;
                    for($k=0; $k< $finalReceipt_count_item; $k++){
                        $final_count_amount = $final_count_amount + $final_data[$i][items][$k][quantity];
                    }
                    echo $preRecceipt_count_item."=".$pre_count_amount."/".$finalReceipt_count_item."=".$final_count_amount;
                    echo "<br />";
                    if(($finalReceipt_count_item > $preRecceipt_count_item) || ($final_count_amount > $pre_count_amount)){
                        
                        echo $preRecceipt_count_item."=".$finalReceipt_count_item."twosell sell";
                        echo "<br />";
                    }
                }
            }
        }
        // end final total
    }
    
}
else{
    // is a string and just echo it
    echo $result;
}
?>
