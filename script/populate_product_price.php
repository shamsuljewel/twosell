<?php
/*
 * This script name = 'populate_product_price'
 * will go and check each record of final receipts and take all the article_num of that receipt and
 * checks if that article_num is present into the twosell_product table, if found then checks for 
 * the vat rate if not same update vat rate, if the article_num not found then insert it.  
 */

//connect to the statistics_database
include 'dbconnect.php';

error_reporting(E_ALL); 
// error log ON
ini_set('log_errors','1'); 
// display error NO
ini_set('display_errors','0'); 
// set the location of this script error log file
//$root = $_SERVER['DOCUMENT_ROOT']."/twosell";
//ini_set('error_log', 'c:/xampp/htdocs/twosell/error_log/populate_product_price.log');
ini_set('error_log', '/home/twosell/data/admin.twosell.se/error_log/populate_product_price.log');
set_error_handler("customError");
/*
 * error number, error message, error file name and the line number
 * after then kill the script
 */
function customError($errno, $errstr, $errfile, $errline){
  error_log("Error: [$errno] $errstr on $errfile at line $errline", 0);
  die();
}
/*************************************************************************/
$script_name = 'populate_product_price';
$statDb = 'statistics_test';
$canonicalDb = 'statoil_canonical';
$final_data = 'final_data';
$twosell_product = 'twosell_product';
$twosell_store = 'twosell_store';
$twosell_productinstore = 'twosell_productinstore';
$twosell_pricinghistory = 'twosell_pricinghistory';
// for compare the flaot number for price change
$epsilon = 0.1;
// get the last processed id of the $stat_cashier table, so all time no need to calculate whole table
$q_script = "SELECT database_name, table_name, last_processed_id FROM script_tbl WHERE name = '$script_name' LIMIT 1";
$q_script1 = mysql_query($q_script) or trigger_error(mysql_error());
$num_rows = mysql_num_rows($q_script1);
if($num_rows > 0){
    // we got the script name on the script table
    $row = mysql_fetch_object($q_script1);
    $last_processed_id = $row->last_processed_id;
//    echo $last_processed_id;
    /*
     * get the maximum id of the final_data table so that we get the last id and process upto that point
     */
    $max = mysql_query("SELECT max(id) AS maxid FROM $statDb.$final_data") or trigger_error(mysql_error());
    $max_id_object = mysql_fetch_object($max);
    $max_id = $max_id_object->maxid;
    
//    $last_processed_id = 100;
//    $max_id = 105;
    /*
     * Now we will go to fetch the items of the final receipts from final_data table for last processed id to 
     * max id
     */
    $q_get_final = "SELECT items, store, date_time_receipt FROM $final_data WHERE id > $last_processed_id AND id <= $max_id";
    $q_get_final1 = mysql_query($q_get_final) or trigger_error(mysql_error());
    $total_receipt = mysql_num_rows($q_get_final1);
    $product_i = 0;
    $new_product_insert_count = 0;
    $product_taxrate_updated_count = 0;
    $product_price_updated_count = 0;
    if($total_receipt > 0){
        // fetch every final receipts
        $product_list_array = array();
        while($receipt_row = mysql_fetch_object($q_get_final1)){
//            echo "<br />************************<br />";
            /*
             * retrieve the items from raw json format to array format
             */
            $store = $receipt_row->store;
            $date_receipt = $receipt_row->date_time_receipt;
            // get teh store internal id from twosell_store table
            $store_q = mysql_fetch_object(mysql_query("SELECT id FROM $canonicalDb.$twosell_store WHERE internal_id = '$store' LIMIT 1")) 
            or trigger_error(mysql_error());
            $store_id = $store_q->id;
            $this_row = json_decode($receipt_row->items, 1);
//            print_r($this_row);
            // now this receipt has number of products so need another loop for each product
            $count_product = count($this_row['items']);
            for($i_start = 0; $i_start < $count_product; $i_start++){
                $article_num = $this_row['items'][$i_start]['id'];
                $tax_rate = $this_row['items'][$i_start]['tax_rate'];
                $price = $this_row['items'][$i_start]['amount'];
                $article_name = utf8_decode($this_row['items'][$i_start]['article_name']);
                
                /*
                 * if the article_num is null (gas) then need to check with 
                 * the (article_name) title from twosell_product table
                 */
                
                // check this article number has into the twosell_product table
                if($article_num != ''){ 
                    $check_article_num = "SELECT id, vat_rate FROM $canonicalDb.$twosell_product WHERE articlenum='$article_num' LIMIT 1";
                    
                }else{
                    $check_article_num = "SELECT id, vat_rate, articlenum FROM $canonicalDb.$twosell_product WHERE title='$article_name' LIMIT 1";
                }
//                echo $check_article_num;
                $check_article_num1 = mysql_query($check_article_num) or trigger_error(mysql_error());
                $found = mysql_num_rows($check_article_num1);
                if($found > 0){
                    // this product is already into the twosell_product table
                    // so now check if the tax_rate changed or not?
                    $row_update = mysql_fetch_object($check_article_num1);
                    
                    if($article_num == '') $article_num = $row_update->articlenum;
                    
                    $id = $row_update->id;
                    $vat_rate = $row_update->vat_rate;
//                    echo "got it = $article_num.$article_name at $id <br />";
                    if(abs($vat_rate - $tax_rate) > $epsilon){
                        // UPDATE THE TAX RATE
//                        echo "update tax rate!"; 
                        $product_taxrate_updated_count++;
                        mysql_query("UPDATE $canonicalDb.$twosell_product SET vat_rate = $tax_rate WHERE id=$id LIMIT 1") or trigger_error(mysql_error());
                    }
                    // else do nothing
                }else{
                    // This product is not present into the twosell_product table
//                    echo "Did not got it = $article_num<br />";
                    $new_product_insert_count++;
                    if($article_num == '') $article_num = $article_name;
                    // insert this into twosell_product table
                    $insert_q = "INSERT INTO $canonicalDb.$twosell_product(title, articlenum, vat_rate, 
                            product_type,modified, chain_id, 
                            do_not_trigger_offer, never_offer_direct, never_offer_coupon, 
                            use_standard_coupon_discount, coupon_type_of_discount, coupon_percentage_discount, 
                            coupon_fixed_discount) 
                            VALUES('$article_name', '$article_num', '$tax_rate', 
                            'valid', 0, 1, 
                            0, 0, 0, 
                            1, 'p', 0, 
                            0 
                            )"; 
//                    echo $insert_q." / ";
                    mysql_query($insert_q) or trigger_error(mysql_error());
                    $id = mysql_insert_id();
                }
                $product_list_array[$product_i]['product_id'] = $id;
                $product_list_array[$product_i]['article_num'] = $article_num;
                $product_list_array[$product_i]['store'] = $store_id;
                $product_list_array[$product_i]['price'] = $price;
                $product_list_array[$product_i]['receipt_date'] = $date_receipt;
                $product_i++;
            } // end loop for
            
//            echo "<br />***********************</br >";
            
            
            
        } // end receipts loop
//        print_r($product_list_array);
        /* now all the receipts information is at $product_list_array
         * Now i will check with each receipt with their product_id, store_id 
         * from twosell_productinstore table, if found then checks the price, if price changed then update
         * the price else do nothing
         */
//        echo $product_i;
        for($i_product = 0; $i_product < $product_i; $i_product++){
            $product_id = $product_list_array[$i_product]['product_id'];
            $store_id = $product_list_array[$i_product]['store'];
            $price = $product_list_array[$i_product]['price'];
            $date_receipt = $product_list_array[$i_product]['receipt_date'];
            $checks_product_in_store = mysql_query("SELECT id, price FROM $canonicalDb.$twosell_productinstore WHERE product_id='$product_id' AND
            store_id = '$store_id' LIMIT 1") or trigger_error(mysql_error());
            if(mysql_num_rows($checks_product_in_store) > 0){
//                echo "match found for store-product: $store_id-$product_id, ";
                $row_productinstore = mysql_fetch_object($checks_product_in_store);
//                echo $row_productinstore->price."-".$price;
//                echo ",".$row_productinstore->id.", ";
                // check the price, for flaoting point number compare chould be like this
                if(abs($row_productinstore->price - $price) > $epsilon){
                    $priced_product_id = $row_productinstore->id;
                    $product_price_updated_count++;
//                    echo "price is not same, will update price, and add new row to price history<br />";
                    mysql_query("UPDATE $canonicalDb.$twosell_productinstore SET price=$price WHERE id=$row_productinstore->id LIMIT 1") or trigger_error(mysql_error());
//                    echo "price updated in product in store";
                    $insert_price_history = "INSERT INTO $canonicalDb.$twosell_pricinghistory
                    (priced_product_id, price, price_change_time) VALUES($priced_product_id, $price, '$date_receipt')";
                    mysql_query($insert_price_history) or trigger_error(mysql_error());
//                    echo $insert_price_history;
                }
                // else do nothing becoz the price is same
            }
            else{
//                echo "match not found for store-product: $store_id-$product_id";
//                echo "need to insert this product into the twosell_productinstore table<br />";
                $insert_productinstore = "INSERT INTO $canonicalDb.$twosell_productinstore(product_id, store_id, price, active, placeholder, stock_quantity)
                VALUES($product_id, $store_id, $price, 1, 0, 0.00)";
                mysql_query($insert_productinstore) or trigger_error(mysql_error());
//                echo $insert_productinstore;
                $last_insert_id = mysql_insert_id();
                $insert_into_pricing_history = "INSERT INTO $canonicalDb.$twosell_pricinghistory
                (priced_product_id, price, price_change_time) VALUES($last_insert_id, $price, '$date_receipt')";
                mysql_query($insert_into_pricing_history) or trigger_error(mysql_error());
//                echo "<br />".$insert_into_pricing_history;
            }
//            echo "<br />";
        }
    }
    else{
        trigger_error("Do not found any new receipt!");
    }
    $summery = "Total receipt: ".$total_receipt.", Total Products: ".$product_i.", Total New Product Inserted: "
            .$new_product_insert_count.", Total Taxrate Updated: ".$product_taxrate_updated_count.", Total Price Updated: ".$product_price_updated_count;
    echo "<br />".$summery;
}else{
    trigger_error("Script table do not have the script name");
}

trigger_error($summery);

?>
