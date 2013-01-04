<?php

/*
 * this script has a dependency of seller information, so it runs after the save_store_cashier script, this
 * script has been included right after that script
 */
// connect to the statistics database
include 'dbconnect.php';
// error reporting on for error log file
error_reporting(E_ALL); 
// error log ON
ini_set('log_errors','1'); 
// display error NO
ini_set('display_errors','0'); 
// set the location of this script error log file
//$root = $_SERVER['DOCUMENT_ROOT']."/twosell";
//ini_set('error_log', 'c:/xampp/htdocs/twosell/error_log/import_twosell_purchase_table.log');
ini_set('error_log', '/home/twosell/data/admin.twosell.se/error_log/import_twosell_purchase_table.log');
set_error_handler("customError");
/*
 * error number, error message, error file name and the line number
 * after then kill the script
 */
/*function customError($errno, $errstr, $errfile, $errline){
  error_log("Error: [$errno] $errstr on $errfile at line $errline", 0);
  die();
}*/


/*
 * error reporting things and database connection are same as save_store_cashier script so did not repeat it here
 */
/********************************************************************/
$script_name = 'import_twosell_purchase_table';
$statDb = 'statistics_test';
$canonicalDb = 'statoil_canonical';
$twosell_seller = 'twosell_seller';
$twosell_purchase_last = 'twosell_purchase_last';
$twosell_purchase = 'twosell_purchase';
$update_records = 0;
$insert_records = 0;
// get the last processed id of the $stat_cashier table, so all time no need to calculate whole table
$q_script = "SELECT database_name, table_name, last_processed_id FROM script_tbl WHERE name = '$script_name' LIMIT 1";
$q_script1 = mysql_query($q_script) or trigger_error(mysql_error());
$num_rows = mysql_num_rows($q_script1);

$output = array();
if($num_rows > 0){
    // we got the script name on the script table
    $row = mysql_fetch_object($q_script1);
    $last_processed_id = $row->last_processed_id;
//    echo $last_processed_id;
    /*
     * get the last auto increment id of the twosell_purchase_last table
     */
    $max = mysql_query("SELECT max(id) AS maxid FROM $statDb.$twosell_purchase_last") or trigger_error(mysql_error());
    $max_id_object = mysql_fetch_object($max);
    $max_id = $max_id_object->maxid;
    
    $q_statistics_purchase = "SELECT id, transactionid, seller_id, time_of_purchase, time_received, 
        total_cost, n_rows, pos_id, total_discount, direct_gross_incl_vat, gen_discount, total_cost_excl_vat,
        direct_gross_excl_vat
        FROM $statDb.$twosell_purchase_last WHERE (id > $last_processed_id AND id <= $max_id);  
     ";
    $q_statistics_purchase1 = mysql_query($q_statistics_purchase) or trigger_error(mysql_error());
    // check if there are any new rows from the last processed id
    $total_new_records = mysql_num_rows($q_statistics_purchase1);
    if($total_new_records > 0){
        // there are new rows of purchase need to insert this rows into the canonical twosell_purchase
//        echo "Got $total_new_records new records, ";
        /*
         * go through each new records and check with transactionid since it is unique, if found update the 
         * record else just insert the record into the twosell_purchase table
         */
        
        while($new_record = mysql_fetch_object($q_statistics_purchase1)){
            $transactionid = $new_record->transactionid;
            $seller_id = $new_record->seller_id;
            $time_of_purchase = $new_record->time_of_purchase;
            $time_received = $new_record->time_received;
            $total_cost = $new_record->total_cost;
            $n_rows = $new_record->n_rows;
            $pos_id = $new_record->pos_id;
            $total_discount = $new_record->total_discount;
            $direct_gross_incl_vat = $new_record->direct_gross_incl_vat;
            $direct_gross_excl_vat = $new_record->direct_gross_excl_vat;
            $gen_discount = $new_record->gen_discount;
            $total_cost_excl_vat = $new_record->total_cost_excl_vat;
            // get the auto increment id of the seller_id (idnum)
            $seller_id_q = mysql_query("SELECT id FROM $canonicalDb.$twosell_seller WHERE idnum='$seller_id' LIMIT 1") or 
            trigger_error(mysql_error());
            $seller_id_q1 = mysql_fetch_object($seller_id_q);
            $seller_id = $seller_id_q1->id;
            /*
             * check this transactionid into the twosell_purchase table
             */
            $check_exists_transactionid = mysql_query("SELECT id FROM $canonicalDb.$twosell_purchase WHERE transactionid = '$transactionid' LIMIT 1")
                or trigger_error(mysql_error());
            if(mysql_num_rows($check_exists_transactionid) > 0){
                // found the same transaction so now needs to update the information
                $row_fetch = mysql_fetch_object($check_exists_transactionid);
                $row_id = $row_fetch->id;
                $update_q = "UPDATE $canonicalDb.$twosell_purchase SET 
                total_cost = $total_cost, 
                total_cost_excl_vat = $total_cost_excl_vat,
                direct_gross_incl_vat = $direct_gross_incl_vat,
                time_of_purchase = '$time_of_purchase',
                time_received = '$time_received',
                pos_id = $pos_id,
                seller_id = $seller_id,
                n_rows = $n_rows,
                gen_discount = $gen_discount,
                total_discount = $total_discount
                WHERE id = $row_id
                LIMIT 1
                ";
                mysql_query($update_q) or trigger_error(mysql_error());
                $update_records++;
            }else {
                // not found into the table, so needs to insert this record into the twosell_purchase table
                $insert_q = "INSERT INTO $canonicalDb.$twosell_purchase(
                    total_cost, total_cost_excl_vat, direct_gross_incl_vat,
                    direct_gross_excl_vat, coupon_gross_incl_vat, coupon_gross_excl_vat,
                    transactionid, time_of_purchase, time_received,
                    final, pos_id, seller_id,
                    n_rows, gen_discount, total_discount,
                    direct_reported_shown, direct_net_incl_vat, direct_net_excl_vat,
                    coupon_net_incl_vat, coupon_net_excl_vat
                )
                VALUES(
                    $total_cost, $total_cost_excl_vat, $direct_gross_incl_vat,
                    $direct_gross_excl_vat, 0, 0,
                    '$transactionid', '$time_of_purchase', '$time_received',
                    1, $pos_id, $seller_id, 
                    $n_rows, $gen_discount, $total_discount,
                    0, 0, 0,
                    0, 0
                )
                ";
                mysql_query($insert_q) or trigger_error(mysql_error());
                $insert_records++;
            }
        }
    }
    $output[] = "Total new record found: $total_new_records, New record Inserted: $insert_records, 
    New Records Updated: $update_records, Max id: $max_id, Last_processed: $last_processed_id";
    // else if there is no new rows then do nothing
    $datetime = date("Y-m-d H:i:s");
    mysql_query("UPDATE script_tbl SET last_processed_id = '$max_id', datetime='$datetime' WHERE name='$script_name' LIMIT 1") or trigger_error(mysql_error());
    $output[] = "Script table Updated:)";
    $json_output = json_encode($output);
    echo $json_output;
    error_log($json_output);
}   
?>
