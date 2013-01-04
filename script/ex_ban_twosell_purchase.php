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
$banned_groups =  banned_groups();
$banned_products = banned_products($banned_groups);
mysql_select_db('statistics_test');
$total_cost = 0;
$total_items = 0;
//print_r($banned_products);
//echo "**********************************";
//echo "**********************************";
$q_all = "SELECT id, transactionid, n_rows, total_cost, direct_gross_incl_vat, twosell_item_count FROM twosell_purchase_test ORDER BY id LIMIT 100";
$q1 = mysql_query($q_all) or die(mysql_error());
//echo $q;

if($q1!=FALSE){
    echo "Total data: ".mysql_num_rows($q1);
    $total_rec_gas = 0;
    while($q2 = mysql_fetch_array($q1)){
        $gas_found = 0;
        $total_cost = $q2['total_cost'];
        $total_items = $q2['n_rows'];
        //echo "<br />ID=".$q2['id']."->".$total_cost."->".$total_items."<br />";
        $q_t_items = "SELECT items FROM transaction_tbl WHERE ref_purchase = '$q2[id]' LIMIT 1";
        $q_t_items1 = mysql_query($q_t_items) or die(mysql_error()); 
        $q_t_items2 = mysql_fetch_array($q_t_items1);
        if($q_t_items2['items'] != NULL) $items = json_decode($q_t_items2['items'], true);
        if(!empty($items['items'])){
            foreach ($items['items'] as $key => $value) {
                if($value['item_id'] == "" || in_array($value['item_id'], $banned_products)){
                    $net = abs($value['amount'] - $value['discount']);
                    $total_cost -= $net;
                    $total_items -= 1;
                    $gas_found = 1;
                }
            }
            if($gas_found == 1){
                $q_update = "UPDATE twosell_purchase_test SET gas_or_null = 1, total_cost_ban = $total_cost, total_item_ban = $total_items WHERE id='$q2[id]'";
                mysql_query($q_update);
//                echo "Gas on it<br />";
//                echo "Cost=".$total_cost.", ".$total_items;
                $total_rec_gas++;
            }
            else{
                $q_update = "UPDATE twosell_purchase_test SET gas_or_null = 0 WHERE id='$q2[id]'";
                mysql_query($q_update);
//                echo "No Gas on it<br />";
//                echo "Cost=".$total_cost.", ".$total_items;
            }
        }
        //echo "<br>---------------<br>";
    }
    echo "<br />Finished The Calculation.</br >";
    echo "Total Rec. Gas = ".$total_rec_gas;
}
else {
   echo mysql_error();
}
?>
