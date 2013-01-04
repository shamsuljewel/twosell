<?php
include 'dbconnect.php';
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
    mysql_select_db('statistics');
    $q_t_items = "SELECT * FROM transaction_tbl WHERE ref_purchase = '$ref_id' LIMIT 1";
    $q_t_items1 = mysql_query($q_t_items) or die(mysql_error()); 
    $q_t_items2 = mysql_fetch_array($q_t_items1);
    if($q_t_items2['twosell_items'] != NULL) $twosell_items = json_decode($q_t_items2['twosell_items'], true);
//            //print_r($twosell_items);
    if(!empty($twosell_items[items])){
        foreach ($twosell_items[items] as $key => $value) {
        //      echo $value."->";
            if($value == ""){
                $all_items = json_decode($q_t_items2['items'], true);
                $all_item_count = count($all_items[items]);
                // echo  $all_item_count;
                //print_r($all_items[items]);
                for($i=0; $i< $all_item_count; $i++){
                    $all_items_list[$i] = $all_items['items'][$i]['item_id'];
                }
                //print_r($all_items_list);
                $search_key = array_keys($all_items_list, $value);
                if(!empty($search_key)){
                    $net_amount = $all_items[items][$search_key[0]][amount] - $all_items[items][$search_key[0]][discount];
                    $cost -= $net_amount;
                    $items -= 1;
                }
            }
            else{
                if(in_array($value, $banned_products)){
                // echo "banned!";
                    $all_items = json_decode($q_t_items2['items'], true);
                    $all_item_count = count($all_items[items]);
                    // echo  $all_item_count;
                    //print_r($all_items[items]);
                    for($i=0; $i< $all_item_count; $i++){
                        $all_items_list[$i] = $all_items['items'][$i]['item_id'];
                    }
                    //print_r($all_items_list);
                    $search_key = array_keys($all_items_list, $value);
                    if(!empty($search_key)){
                        $net_amount = $all_items[items][$search_key[0]][amount] - $all_items[items][$search_key[0]][discount];
                        $net_amount = $all_items[items][$search_key[0]][amount] - $all_items[items][$search_key[0]][discount];
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
$q_twosell = "SELECT p.id, p.store AS store_id, p.seller_id AS cashier_id,twosell_item_count, p.direct_gross_incl_vat AS twosell_cost
                    FROM twosell_purchase_test AS p
                    WHERE DATE( p.time_of_purchase ) =  '2012-09-04'
                    AND p.direct_gross_incl_vat > 0
                    ORDER BY store, seller_id";
        //echo $q_twosell;
        $q_twosell1 = mysql_query($q_twosell);
        $twosell_org_array = array();
        if($q_twosell1!=FALSE){
            if(mysql_num_rows($q_twosell1)>0){
                while($q_twosell2 = mysql_fetch_array($q_twosell1)){
                    $twosell = orig_twosell($q_twosell2[id], $q_twosell2[twosell_item_count], $q_twosell2[twosell_cost]);
                    $twosell_org_array[$q_twosell2[store_id]][$q_twosell2[cashier_id]][twosell_cost] += $twosell[1]; 
                    $twosell_org_array[$q_twosell2[store_id]][$q_twosell2[cashier_id]][total_twosell_item] += $twosell[0];
                    if($twosell[0] > 0)
                        $twosell_org_array[$q_twosell2[store_id]][$q_twosell2[cashier_id]][total_twosell_receipt] += 1;
                    
                }
//                $q = "INSERT INTO $temp_tbl(s_id, c_id, total, twosell, twosell_cost) VALUES('$q_twosell2[store_id]','$q_twosell2[cashier_id];','$q_twosell2[count_receipt];','1', '$q_twosell2[twosell_cost]')";
//                mysql_query($q) or die(mysql_error()); 
                print_r($twosell_org_array);
                $keys = array_keys($twosell_org_array);
                //print_r($keys);
                for($i=0; $i< count($keys); $i++){
                    $cashiers = array_keys($twosell_org_array[$keys[$i]]);
                    //print_r($cashiers);
                    for($j=0;$j < count($cashiers); $j++){
                        $store = $keys[$i];
                        $cashier = $cashiers[$j];
                        $twosell_cost = $twosell_org_array[$store][$cashier][twosell_cost];
                        $total_twosell_item = $twosell_org_array[$store][$cashier][total_twosell_item];
                        $total_twosell_receipt = $twosell_org_array[$store][$cashier][total_twosell_receipt];
                        echo "<br>".$store.", ".$cashier.", ".$total_twosell_item.", ".$total_twosell_receipt.", ".$twosell_cost;
                        $q = "INSERT INTO $temp_tbl(s_id, c_id, total, twosell, twosell_cost) 
                        VALUES('$store','$cashier;','$total_twosell_receipt','1', '$twosell_cost')";
                        mysql_query($q) or die(mysql_error()); 
                    }
                }
                
            }
            /*
             * now insert data into the store and cashier table
             */
//            save_data($date, $temp_tbl, $store_tbl, $cashier_tbl);
        }else{
            $error = "Select Twosell Query Error";
        }
?>
