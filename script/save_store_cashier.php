<?php

/* 
 * runs once in a day and gets all the new stores and cashiers from the new receipts
 * the new receipts calculates saves into twosell_purchase_last table and the store and cashiers statistics
 * are saved into the stat_cashier and stat_store table, stat_cashier also has the store information 
 * so this script will checks only this stat_cashier from statistics_test database table to check new cashier and store
 * the store infromation must be saves into the chain_database.twosell_store table and chain_database.twosell_seller table
 * *****
 * author: Shamsul Alam (shamsuljewel@gmail.com) 
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
//ini_set('error_log', 'c:/xampp/htdocs/twosell/error_log/save_store_cashier.log');
ini_set('error_log', '/home/twosell/data/admin.twosell.se/error_log/save_store_cashier.log');
set_error_handler("customError");
/*
 * error number, error message, error file name and the line number
 * after then kill the script
 */
function customError($errno, $errstr, $errfile, $errline){
  error_log("Error: [$errno] $errstr on $errfile at line $errline", 0);
  die();
}

/********************************************************************/
$script_name = 'save_store_cashier';
$statDb = 'statistics_test';
$canonicalDb = 'statoil_canonical';
$stat_cashier= 'stat_cashier';
$twosell_store = 'twosell_store';
$twosell_seller = 'twosell_seller';
/*
 * this function inserts stores and then retrive the store inserted auto id and then insert all the cashiers
 */
function insert_store($store, $store_table, $cashiers, $cashier_table, $database_name){
    $insert_q = "INSERT INTO $database_name.$store_table (internal_id, chain_id) VALUES('$store', 1)";
    mysql_query($insert_q) or trigger_error(mysql_error());
//    echo $insert_q."<br />";
    $store_id = mysql_insert_id();
    insert_cashiers($store_id, $cashiers, $cashier_table, $database_name);
}
/*
 * this function calls from the insert_store function and inserts all the cashiers of that store
 */
function insert_cashiers($store, $cashiers, $table_name, $database_name){
//    print_r($cashiers);
    $total_cashiers = count($cashiers);
    for($i=0; $i < $total_cashiers; $i++){
        $cashier = $cashiers[$i];
        $insert_q = "INSERT INTO $database_name.$table_name (idnum, store_id) VALUES('$cashier', '$store')";
        mysql_query($insert_q) or trigger_error(mysql_error());
//        echo $insert_q."<br />";
    }
}
// get the last processed id of the $stat_cashier table, so all time no need to calculate whole table
$q_script = "SELECT database_name, table_name, last_processed_id FROM script_tbl WHERE name = '$script_name' LIMIT 1";
$q_script1 = mysql_query($q_script) or trigger_error(mysql_error());
$num_rows = mysql_num_rows($q_script1);
$store_cashier_array = array();
$output = array();
if($num_rows > 0){
    // we got the script name on the script table
    $row = mysql_fetch_object($q_script1);
    $last_processed_id = $row->last_processed_id;
    //echo $last_processed_id;
    /*
     * get the last auto increment id of the stat_cashier table
     */
    $max = mysql_query("SELECT max(id) AS maxid FROM $statDb.$stat_cashier") or trigger_error(mysql_error());
    $max_id_object = mysql_fetch_object($max);
    $max_id = $max_id_object->maxid;
    /*
     * Now will go to fetch each unique store and their cashiers from stat_cashier table
     */
    $store_cashier = "SELECT id, s_id, c_id FROM $statDb.$stat_cashier WHERE (id > '$last_processed_id' AND id <= '$max_id') GROUP BY s_id,c_id ORDER BY s_id";
//    echo $store_cashier;
    $store_cashier1 = mysql_query($store_cashier) or trigger_error(mysql_error());
    $total_cashiers = mysql_num_rows($store_cashier1);
    if($total_cashiers > 0){
        while($rows = mysql_fetch_object($store_cashier1)){
            // saves into the array
            $store_cashier_array[$rows->s_id][] = $rows->c_id;
        }
    }
//    echo $max;
//    print_r($store_cashier_array);
    // now we have each unique stores with their cashier into the $store_cashier_array;
    /*
     * Now we will check if that store is exists into the twosell_store table if not then insert the store into
     * the twosell_store table and insert all the cashiers into twosell_seller table
     * if exists then checks each of the cashiers into the twosell_seller table if found then do nothing else
     * insert that cashier into the twosell_seller table
     */
    $all_stores_array = array_keys($store_cashier_array);
    $total_stores = count($all_stores_array);
//    echo "total Store: ".$total_stores;
//    echo "<br />";
    for($i=0; $i < $total_stores; $i++){
        $store = $all_stores_array[$i];
        $check_store_exists = "SELECT id, internal_id FROM $canonicalDb.$twosell_store WHERE internal_id='$store' LIMIT 1";
//        echo $check_store_exists."<br />";
        $check_store_exists1 = mysql_query($check_store_exists) or trigger_error(mysql_error());
        $found = mysql_num_rows($check_store_exists1);
        if($found > 0){
            // got the store, check the cashiers now
            $store_row = mysql_fetch_object($check_store_exists1);
            $store_id = $store_row->id;
//            echo "found".$store_row->internal_id.", ".$store_row->id;
            $total_cashier_of_store = count($store_cashier_array[$store]);
            // go through every cashier of that store
            for($j=0; $j < $total_cashier_of_store; $j++){
                $cashier = $store_cashier_array[$store][$j];
                $check_cashier_exists = "SELECT idnum FROM $canonicalDb.$twosell_seller WHERE idnum='$cashier' AND store_id = '$store_id' LIMIT 1";
//                echo $check_cashier_exists."<br />";
                $check_cashier_exists1 = mysql_query($check_cashier_exists) or trigger_error(mysql_error());
                if(mysql_num_rows($check_cashier_exists1) <= 0){
                    // cashier not exists
                    
                    $insert_q = "INSERT INTO $canonicalDb.$twosell_seller (idnum, store_id) VALUES('$cashier', '$store_id')";
//                    echo $insert_q;
                    mysql_query($insert_q) or trigger_error(mysql_error());
                    $output[] = "Cashier: ".$cashier." of ".$store."($store_id) not exists so inserted<br />";
                }
                // if cashier found do nothing
            }
        }
        else{
            // the store not exists, add store and cashiers
            // insert cashier called inside the insert store coz we need the inserted auto increment id
            $output[] = "New store: ".$store."Inserted with all cashiers<br />";
            insert_store($store, $twosell_store, $store_cashier_array[$store], $twosell_seller, $canonicalDb);
        }
    }
    /*
     * Now if no kind of error occured then we are going to update the last_processed id of the script
     * so that next time this script will not process previous stores and cashiers again
     * however if there is any php error occured this script will write the error and kill 
     * the rest of the script so that last_processed_id will not updated so after fixed it will run
     * again from where it left behind
     */
    $output[] = "Script Table Updated";
    $datetime = date("Y-m-d H:i:s");
    mysql_query("UPDATE script_tbl SET last_processed_id = '$max_id', datetime='$datetime' WHERE name='$script_name' LIMIT 1") or trigger_error(mysql_error());
    $json_output = json_encode($output);
    error_log($json_output);
    
    
    
//    die();
}

/*
 * importing the custom fields of twosell_purchase_last table into the canonical.twosell_purchase table
 * this is completely another script but it has a dependancy of the twosell_seller table so this script
 * will be run right after the save_store_cashier script, so add this after this script
 */
include 'import_twosell_purchase_table.php';
die();
?>
