<?php
session_start();
//echo "abcd";
include '../dbconnect.php';
include '../functions/commonFunction.php';
$statistics_database = "statistics_test";
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
if($_POST['id']){
    $banned_groups =  banned_groups();
    $banned_products = banned_products($banned_groups);
    //print_r($banned_products);
    /*
     * select the statistics database
     */
    mysql_select_db($statistics_database);
    $ids = explode("-", $_POST['id']);
    $final_id = $ids[0];
    $pre_id = $ids[1];
    $tid = $ids[2];
    //print_r($ids);
    $q = "SELECT t.transactionid, t.seller_id, t.store, f.items as fitem,f.id as fid, r.items as ritem, r.id as rid FROM final_data as f, request_data as r, twosell_purchase_last as t WHERE f.id = $final_id AND r.id = $pre_id AND t.id=$tid LIMIT 1";
    $result = sql_query($q, $link);
    echo "<div><h1>Transaction Details</h1></div>";
    if(is_array($result) == "array"){
        //print_r($result);
        
        echo "<table class='view_tbl'>";
        echo "<tr><td>Transaction ID: </td><td>".$result[0]['transactionid']."</td></tr>";
        echo "<tr><td>Store: </td><td>".$result[0]['store']."</td></tr>";
        echo "<tr><td>Cashier: </td><td>".$result[0]['seller_id']."</td></tr>";
        $toArrayFinalItem = json_decode($result[0]['fitem'], true);
        $toArrayPreItem = json_decode($result[0]['ritem'], true);
//        print_r($toArrayFinalItem);
        $count = count($toArrayFinalItem['items']);
        $preCount = count($toArrayPreItem['items']);
        $total_cost = 0;
        $total_discount = 0;
        $total_cost_wgas = 0;
        $total_discount_wgas = 0; 
        $total_discount_wogas = 0;
        $total_cost_wogas = 0;
        $twosell_cost = 0;
        $temp_final = array();
        $temp_pre = array();
        $twosell_items = array();
        $gas_or_null = 0 ;
        for($i=0;$i<$count;$i++){
            $total_cost += $toArrayFinalItem['items'][$i]['amount'];
            $total_discount += $toArrayFinalItem['items'][$i]['discount'];
            if($toArrayFinalItem['items'][$i]['id'] == NULL || in_array($toArrayFinalItem['items'][$i]['id'], $banned_products)){
                $total_cost_wgas += $toArrayFinalItem['items'][$i]['amount'];
                $total_discount_wgas += $toArrayFinalItem['items'][$i]['discount'];
                $gas_or_null = 1;
            }else{
                $temp_final[$i] = $toArrayFinalItem['items'][$i]['id'];
            }
        }
        $total_cost_wogas = $total_cost - $total_cost_wgas;
        $total_discount_wogas = $total_discount - $total_discount_wgas;
        echo "<tr><td>Final Items: </td><td> Total Products: ".$count;
        echo "<table><tr><th>Item Id</th><th>Name</th><th>Quantity</th><th>Cost</th><th>Discount</th></tr>";
        for($i=0; $i < $preCount; $i++){
            if($toArrayPreItem['items'][$i]['id'] != NULL && (in_array($toArrayPreItem['items'][$i]['id'], $banned_products)) === FALSE){
                $temp_pre[$i] = $toArrayPreItem['items'][$i]['id'];
            }
        }
//        print_r($temp_final);
//        print_r($temp_pre);
        $twosell_items = array_diff_once($temp_final, $temp_pre);
        if(!empty($twosell_items)){
            $twosell_keys = array_keys($twosell_items);
            //print_r($twosell_keys);
            $twosell_count_product = count($twosell_keys);
            for($l=0; $l < $twosell_count_product; $l++){
                $twosell_cost += $toArrayFinalItem['items'][$twosell_keys[$l]]['amount'] - $toArrayFinalItem['items'][$twosell_keys[$l]]['discount'];
                $toArrayFinalItem['items'][$twosell_keys[$l]]['twosell'] = 1;
            }
//            print_r($twosell_items);
        }
//        print_r($toArrayFinalItem);
        for($i=0; $i < $count; $i++){
            if($toArrayFinalItem['items'][$i]['twosell'] == 1){
                echo "<tr><td  style='background-color:yellow'>".$toArrayFinalItem['items'][$i]['id']."</td>
                <td style='background-color:yellow'>".$toArrayFinalItem['items'][$i]['article_name']."</td>    
                <td style='background-color:yellow'>".$toArrayFinalItem['items'][$i]['quantity']."</td>     
                <td style='background-color:yellow'>".$toArrayFinalItem['items'][$i]['amount']."</td>        
                <td style='background-color:yellow'>".$toArrayFinalItem['items'][$i]['discount']."</td>        
                </tr>";
            }else{
                echo "<tr><td>".$toArrayFinalItem['items'][$i]['id']."</td>
                <td>".$toArrayFinalItem['items'][$i]['article_name']."</td>    
                <td>".$toArrayFinalItem['items'][$i]['quantity']."</td>     
                <td>".$toArrayFinalItem['items'][$i]['amount']."</td>        
                <td>".$toArrayFinalItem['items'][$i]['discount']."</td>        
                </tr>";
            }
            
        }
        echo "</table>";
        echo "</td></tr>";
        
        echo "<tr><td>Pre Receipt</td><td>Total Products: ".$preCount;
        echo "<table><tr><th>Item Id</th><th>Quantity</th></tr>";
        
        for($i=0; $i < $preCount; $i++){
            if($toArrayPreItem['items'][$i]['id'] == "") $item_id = "Petrol / Gas Items";
            else $item_id = $toArrayPreItem['items'][$i]['id'];
            
            echo "<tr><td>".$item_id."</td>
                  <td>".$toArrayPreItem['items'][$i]['quantity']."</td>    
            </tr>";
        }
        
        echo "</table>";
        echo "</td></tr>";
        echo "</table>";
    }
    else{
        echo $result;
    }
}
else{
    echo "Not Posted..";
}    
?>
