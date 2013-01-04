<?php
function dateTimeDiff($d1, $d2){
    // return the number of hours between two date times 
    return round(abs(strtotime($d1) - strtotime($d2))/3600);
}
/* json[0] saves the request or response 1st portion
 * json[1] saves the request json format string
 */
function mysql_create_tables(){
    $q_final_data = "CREATE TABLE IF NOT EXISTS `final_data` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`jogging_log_id` INT(11) NOT NULL,
	`store` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`device` INT(11) NULL DEFAULT NULL,
	`trans` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`serial_chk` INT(11) NULL DEFAULT NULL,
	`items` TEXT NULL COLLATE 'utf8_unicode_ci',
	`total_amount` DOUBLE NULL DEFAULT NULL,
	`total_discount` DOUBLE NULL DEFAULT NULL,
	`cashier` INT(11) NULL DEFAULT NULL,
	`date_time_receipt` DATETIME NULL DEFAULT NULL,
	`date_time_log` DATETIME NOT NULL,
	PRIMARY KEY (`id`),
        UNIQUE INDEX `jogging_log_id` (`jogging_log_id`)
    )
    COLLATE='utf8_unicode_ci'
    ENGINE=MyISAM
    AUTO_INCREMENT=1207;";
    mysql_query($q_final_data);
    $q_offer_status = "CREATE TABLE IF NOT EXISTS `offerstatus_data` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`jogging_log_id` INT(11) NOT NULL,
	`store` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`device` INT(11) NULL DEFAULT NULL,
	`trans` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`batch` INT(11) NULL DEFAULT NULL,
	`screen` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`service` TEXT NULL COLLATE 'utf8_unicode_ci',
	`items` TEXT NULL COLLATE 'utf8_unicode_ci',
	`date_time_log` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
        UNIQUE INDEX `jogging_log_id` (`jogging_log_id`)
    )
    COLLATE='utf8_unicode_ci'
    ENGINE=MyISAM
    AUTO_INCREMENT=1321;";
    mysql_query($q_offer_status);
    $q_request = "CREATE TABLE IF NOT EXISTS `request_data` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`jogging_log_id` INT(11) NOT NULL,
	`store` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`device` INT(11) NULL DEFAULT NULL,
	`trans` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`serial` INT(11) NULL DEFAULT NULL,
	`items` TEXT NULL COLLATE 'utf8_unicode_ci',
	`date_time_log` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
        UNIQUE INDEX `jogging_log_id` (`jogging_log_id`)
    )
    COLLATE='utf8_unicode_ci'
    ENGINE=MyISAM
    AUTO_INCREMENT=3050;";
    mysql_query($q_request);
    $q_response = "CREATE TABLE IF NOT EXISTS `response_data` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`jogging_log_id` INT(11) NOT NULL,
	`store` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`device` INT(11) NULL DEFAULT NULL,
	`trans` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`min_screen_time` INT(11) NULL DEFAULT NULL,
	`desc_left` TEXT NULL COLLATE 'utf8_unicode_ci',
	`desc_right` TEXT NULL COLLATE 'utf8_unicode_ci',
	`batch` INT(11) NULL DEFAULT NULL,
	`items` TEXT NULL COLLATE 'utf8_unicode_ci',
	`num_display` INT(11) NULL DEFAULT NULL,
	`date_time_log` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
        UNIQUE INDEX `jogging_log_id` (`jogging_log_id`)
    )
    COLLATE='utf8_unicode_ci'
    ENGINE=MyISAM
    AUTO_INCREMENT=3039;";
    mysql_query($q_response);
    return true;
}
include 'config.php';
include 'dbconnectonline.php'; // online mysql database connection
//include 'commonFunction.php'; // My common function are here different from that used into the admin site
/* Get the last saved ID into the online jogging table
 * 
 */
$max_id1 = mysql_fetch_array(mysql_query("SELECT MAX(id) as max FROM $jogging_tbl")); 
$max_id = $max_id1['max'];
//$max_id = 1063408;
mysql_close($linkOnline);
include 'dbconnect.php';
$last_processed_query = mysql_fetch_array(mysql_query("SELECT last_processed_id FROM script_tbl WHERE name='saving_joggin_log'"));
$last_processed_id = $last_processed_query['last_processed_id'];
//echo $max_id;
echo "start from: ".$last_processed_id.", to: ".$max_id."<br />";
include 'dbconnectonline.php';
$q = "SELECT id, datetime, level, msg FROM $jogging_tbl WHERE id > '$last_processed_id' AND id <= '$max_id' AND level = 'INFO' ORDER BY id ASC";
//echo $q;
$q1 = mysql_query($q);
mysql_close($linkOnline);
//echo "query success";
    include 'dbconnect.php';
    //echo "Connected to offline";
    if(mysql_create_tables()){
//        // twosell_purchase table created or already exists!!
        $data = array();
        $transPre = array();
        $total_final = 0;
        $total_request = 0;
        $total_response = 0;
        $total_offer = 0;
        while($row = mysql_fetch_array($q1)){
          unset($data);  
          unset($transPre);
            $json = explode(':', $row['msg'], 2);
            
            $first_part = $json[0];
//            // need to encode it to utf8 otherwise if swedish character there its not worked
            $second_part = utf8_encode($json[1]);
//            /*
//             * When it gets any final_receipt then it saves this into an array, later i will go by each final receipt and serach for the 
//             * preliminary receipts for the same store, device and trans, but some cases the trans may be duplicated by 3-4 days
//             * so have to use a condition to time difference of 24 hr
//             */
            if($first_part == "receipt_final request"){
                $data = json_decode($second_part, true);
//                // process final request
                $transPre = explode('-', $data['trans']);
                if($transPre != "test"){
                    $id = $row['id'];
                    $trans_ref = $data['trans'];
                    if($trans_ref!=""){
                        $store = $data['store'];
                        $device = $data['device'];
                        $trans = $trans_ref;
                        $log_id = $id;
                        $items = mysql_real_escape_string(json_encode(array('items' => $data['items'])));
                        $date_time_log = $row['datetime'];
                        //$items = stripslashes($items);
                        
                        $serial_check = $data['serial_check'];    
                        $total_amount = $data['total_amount'];
                        $total_discount = $data['total_discount'];
                        $cashier = $data['cashier'];
                        $date_time_receipt = date("Y-m-d H:i:s", strtotime($data['datetime']));
                        
$q_final = "INSERT INTO final_data(jogging_log_id, store, device, 
                        trans, serial_chk, items, 
                        total_amount, total_discount, cashier, 
                        date_time_receipt, date_time_log) 
           VALUES('$log_id','$store','$device',
                        '$trans','$serial_check', '$items', 
                        '$total_amount', '$total_discount', '$cashier', 
                        '$date_time_receipt', '$date_time_log')";
                        mysql_query($q_final) or die(mysql_error());
                        $total_final++;
                    }
                }
            }
//            /*
//             * save all the pre requested receipts
//             */
            else if($first_part == "receipt request"){
                $data = json_decode($second_part, true);
                $transPre = explode('-', $data['trans']);
                if($transPre != "test"){
                    $id = $row['id'];
                    $trans_ref = $data['trans'];
                    if($trans_ref!=""){
                        $store = $data['store'];
                        $device = $data['device'];
                        $trans = $trans_ref;
                        $log_id = $id;
                        $date_time_log = $row['datetime'];
                        $items = mysql_real_escape_string(json_encode(array('items' => $data['items'])));
                        
                        $serial = $data['serial'];    
                        
                        
$q_request = "INSERT INTO request_data(jogging_log_id, store, device, 
                        trans, serial, items, 
                        date_time_log) 
              VALUES('$log_id','$store','$device',
                        '$trans','$serial', '$items', 
                        '$date_time_log')";
                        mysql_query($q_request) or die(mysql_error());
                        $total_request++;
                    }
                }
            }
//            /*
//             * Saves all the offer_status requests, this needs for getting the screen time and queue time, the status of the twosell
//             */
            else if($first_part == "offer_status request"){
                $data = json_decode($second_part, true);
                $transPre = explode('-', $data['trans']);
                if($transPre != "test"){
                    $id = $row['id'];
                    $trans_ref = $data['trans'];
                    if($trans_ref!=""){
                        $store = $data['store'];
                        $device = $data['device'];
                        $trans = $trans_ref;
                        $log_id = $id;
                        $date_time_log = $row['datetime'];
                        $items = mysql_real_escape_string(json_encode(array('items' => $data['items'])));
                        
                        $batch = $data['batch'];    
                        $screen = json_encode(array('screen' => $data['screen']));
                        $service = json_encode(array('service' => $data['service']));
                        
                        
$q_offer = "INSERT INTO offerstatus_data(jogging_log_id, store, device, 
                        trans, batch, screen, 
                        service, items, date_time_log) 
           VALUES('$log_id','$store','$device',
                        '$trans','$batch', '$screen', 
                        '$service', '$items','$date_time_log')";
                        mysql_query($q_offer) or die(mysql_error());
                        $total_offer++;
                    }
                }              
               
            }
//            /*
//             * The response gives us the suggestoins that twosell provides to the kassa
//             * not properly implemented yet
//             */ 
            else if($first_part == "receipt response"){
                $data = json_decode($second_part, true);
                $transPre = explode('-', $data['trans']);
                if($transPre != "test"){
                    $id = $row['id'];
                    $trans_ref = $data['trans'];
                    if($trans_ref!=""){
                        $store = $data['store'];
                        $device = $data['device'];
                        $trans = $trans_ref;
                        $log_id = $id;
                        $date_time_log = $row['datetime'];
                        $items = mysql_real_escape_string(json_encode(array('items' => $data['items'])));
                        
                        $num_display = $data['num_display'];    
                        $batch = $data['batch'];
                        $desc_right = mysql_real_escape_string($data['desc_right']);
                        $desc_left = mysql_real_escape_string($data['desc_left']);
                        $min_screen_time = $data['min_screen_time'];
                        
                        $q_response = "INSERT INTO response_data(jogging_log_id, store, device, trans, min_screen_time, desc_left, desc_right, batch, items, num_display, date_time_log) 
                            VALUES('$log_id','$store','$device','$trans', '$min_screen_time', '$desc_left', '$desc_right', '$batch', '$items', '$num_display', '$date_time_log')";
                        mysql_query($q_response) or die(mysql_error());
                        $total_response++;
                    }
                }     
            }
        }
        $dateTime = date('Y-m-d H:i:s');
        mysql_query("UPDATE script_tbl SET last_processed_id = $max_id, datetime='$dateTime' WHERE name='saving_joggin_log'");
        echo " // Total Final:".$total_final;
        echo " // Total Request:".$total_request;
        echo " // Total Response:".$total_response;
        echo " // Total Offer:".$total_offer;
        //include 'calculate_transaction.php';
    }
?>
