<?php
/*
 * Read the file and return all the data as a string
 */
function OpenfileReturn($file_url){		
    $handle = @fopen("$file_url", "r");
    $string = "";
    if ($handle != FALSE) {
        while ($buffer = fgets($handle)) {
            $string .= trim($buffer);
        }
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
    }
    else {
        echo "File opening problem";
        exit();
    }
    return $string;
}
function isLoggedin(){
    if(empty($_SESSION['user']['name']))
        return false;
    else 
        return true;
}
function query($q){
    $q1 = mysql_query($q);
    if($q1 != FALSE){
        $total = mysql_num_rows($q1);
        $max_row = 100;
	if($_GET['z']) $z = $_GET['z'];
	else $z = 0;
	$cur = $z*$max_row;
        $new_q = $q." LIMIT $cur, $max_row";
        //echo $new_q;
        $new_q1 = mysql_query($new_q);
        if($new_q1 != FALSE){
            
            echo "<table width='100%'>
            <tr><td style='padding:10px;'>";

			$l = $cur + $max_row;
			if($l>$total) $l = $total;
			//if($cur == 0) $cur= 1;
			$cur++;
			echo "<div>Showing <b>".$cur."</b> to <b>".$l."</b> out of <b>".$total."</b></div>";	
            echo "</td><td>";
            echo "<div align='right' style='margin-right: 10px; margin-top: 10px; margin-bottom: 5px;'>";
            echo "<ul class='pagi'>";    
            for($i=0; $i*$max_row < $total; $i++)
                {
                        if($z != $i)
                                echo "  <li><a href='#' class='a_li' id='$i'>".($i+1)."</a></li>";
                        else
                                echo "  <li class='active_a'><strong style='color:#FF0000; float: left'; >".($i+1)."</strong></li>";
                }
                echo "</ul></div>";
            echo "</td></tr>
            </table>";
            $i=$cur;
            echo "<table class='view_tbl'><tr><th>SL</th><th>Date Time</th><th>Symbol</th><th>Temperature</th><th>Day/Night</th></tr>";
            while($q2 = mysql_fetch_array($new_q1)){
                echo "<tr><td>$i</td><td>$q2[validdate]</td>
                <td><img src='images/symbol/$q2[weathersymbol]-$q2[day_night].png' />(".$q2[weathersymbol].")</td>
                <td>$q2[temperature]&deg;C</td><td>$q2[day_night]</td></tr>";
                $i++;
            }
            echo "</table>";
            echo "<ul class='pagi'>";    
            for($i=0; $i*$max_row < $total; $i++)
                {
                        if($z != $i)
                                echo "  <li><a href='#' class='a_li' id='$i'>".($i+1)."</a></li>";
                        else
                                echo "  <li class='active_a'><strong style='color:#FF0000; float: left'; >".($i+1)."</strong></li>";
                }
                echo "</ul></div>";
                
        }
        else {
            echo "Query Execution Error..please contact webmaster";
        }

    }
    else {
        echo "Query Execution Error..please contact webmaster";
    }
    
}
function sql_query($q){
   $result = array(); 
   $error = ""; 
    $q1 = mysql_query($q);
   if($q1 != FALSE){
       if(mysql_num_rows($q1) > 0){
           while($q2 = mysql_fetch_array($q1)){
               $result[] = $q2; 
           }
       }
       else{
           $error = "No data found";
       }
   }
   else{
       $error = "Query failed".mysql_error(); 
   }
   if(!empty($result)){
       return $result;
   }
   else{
       return $error;
   }
}
function mysql_create_table($tbl_name){
    if($tbl_name == 'twosell_purchase_last'){
        $q_create = "CREATE TABLE `twosell_purchase_last` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`transactionid` VARCHAR(255) NOT NULL,
	`store` VARCHAR(50) NULL DEFAULT NULL,
	`seller_id` INT(11) NULL DEFAULT NULL,
	`time_of_purchase` DATETIME NOT NULL,
	`time_received` DATETIME NULL DEFAULT NULL,
	`gas_or_null` INT(1) NOT NULL DEFAULT '0',
	`total_cost_ban` DOUBLE NOT NULL DEFAULT '-1',
	`total_cost` DECIMAL(60,2) NOT NULL,
	`total_item_ban` INT(11) NULL DEFAULT NULL,
	`n_rows` INT(11) NOT NULL,
	`pos_id` INT(11) NOT NULL,
	`total_discount` DECIMAL(60,2) NOT NULL,
	`direct_gross_incl_vat` DECIMAL(60,2) NOT NULL,
	`gen_discount` DECIMAL(60,2) NOT NULL,
	`total_cost_excl_vat` DECIMAL(60,2) NOT NULL,
	`time_for_twosell` INT(11) NULL DEFAULT NULL,
	`twosell_item_count` INT(11) NULL DEFAULT '0',
	`direct_gross_excl_vat` DECIMAL(60,2) NOT NULL,
	`direct_net_incl_vat` DECIMAL(60,2) NOT NULL,
	`direct_net_excl_vat` DECIMAL(60,2) NOT NULL,
	`coupon_gross_incl_vat` DECIMAL(60,2) NOT NULL,
	`coupon_gross_excl_vat` DECIMAL(60,2) NOT NULL,
	`coupon_net_incl_vat` DECIMAL(60,2) NOT NULL,
	`coupon_net_excl_vat` DECIMAL(60,2) NOT NULL,
	`direct_reported_shown` TINYINT(1) NOT NULL,
	`screen_time` INT(11) NULL DEFAULT NULL,
	`final` TINYINT(1) NOT NULL,
	`final_id` INT(11) NOT NULL,
	`pre_id` INT(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `transactionid` (`transactionid`, `final`),
	INDEX `twosell_purchase_7acf05e3` (`pos_id`),
	INDEX `twosell_purchase_2ef613c9` (`seller_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;
";
    }
    else if($tbl_name == 'twosell_purchase_test'){
        $q_create = "CREATE TABLE IF NOT EXISTS `$tbl_name` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`transactionid` VARCHAR(255) NOT NULL,
	`time_of_purchase` DATETIME NOT NULL,
	`final` TINYINT(1) NOT NULL,
	`pos_id` INT(11) NOT NULL,
	`seller_id` INT(11) NULL DEFAULT NULL,
	`n_rows` INT(11) NOT NULL,
	`gen_discount` DECIMAL(60,2) NOT NULL,
	`total_discount` DECIMAL(60,2) NOT NULL,
	`total_cost` DECIMAL(60,2) NOT NULL,
	`total_cost_excl_vat` DECIMAL(60,2) NOT NULL,
	`time_for_twosell` INT(11) NULL DEFAULT NULL,
	`direct_gross_incl_vat` DECIMAL(60,2) NOT NULL,
	`direct_gross_excl_vat` DECIMAL(60,2) NOT NULL,
	`direct_net_incl_vat` DECIMAL(60,2) NOT NULL,
	`direct_net_excl_vat` DECIMAL(60,2) NOT NULL,
	`coupon_gross_incl_vat` DECIMAL(60,2) NOT NULL,
	`coupon_gross_excl_vat` DECIMAL(60,2) NOT NULL,
	`coupon_net_incl_vat` DECIMAL(60,2) NOT NULL,
	`coupon_net_excl_vat` DECIMAL(60,2) NOT NULL,
	`time_received` DATETIME NULL DEFAULT NULL,
	`direct_reported_shown` TINYINT(1) NOT NULL,
        `store` VARCHAR(50) NULL DEFAULT NULL,
	`screen_time` INT(11) NULL DEFAULT NULL,
	`twosell_item_count` INT(11) NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `transactionid` (`transactionid`, `final`),
	INDEX `twosell_purchase_7acf05e3` (`pos_id`),
	INDEX `twosell_purchase_2ef613c9` (`seller_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=1;
";
        
}
    $q_create_1 = mysql_query($q_create);
    if($q_create_1 != FALSE){
        return true;
    }
    else{
        return false;
    }
}
function count_items($q, $field_name){
    $sum = 0;
    while($q2 = mysql_fetch_array($q)){
        $sum += $q2[$field_name];
    }
    return $sum;
}
function count_items_array($q, $items){
    if(is_array($items)){
        $count_array = array();
        $count = count($items);
        while($q2 = mysql_fetch_array($q)){
            for($i=0; $i < $count; $i++){
                $count_array[$items[$i]] += $q2[$items[$i]];
            }
        }
        return $count_array;
    }
    else{
        $sum = 0;
        while($q2 = mysql_fetch_array($q)){
            $sum += $q2[$field_name];
        }
        return $sum;
    }
}
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
function array_diff_once(){
    if(($args = func_num_args()) < 2)
        return false;
    $arr1 = func_get_arg(0);
    $arr2 = func_get_arg(1);
    if(!is_array($arr1) || !is_array($arr2))
        return false;
    foreach($arr2 as $remove){
        foreach($arr1 as $k=>$v){
            if((string)$v === (string)$remove){ //NOTE: if you need the diff to be STRICT, remove both the '(string)'s
                unset($arr1[$k]);
                break; //That's pretty much the only difference from the real array_diff :P
            }
        }
    }
    //Handle more than 2 arguments
    $c = $args;
    while($c > 2){
        $c--;
        $arr1 = array_diff_once($arr1, func_get_arg($args-$c+1));
    }
    return $arr1;
}
function dateDiff($d1, $d2){
    // return the number of days between two dates
    return round(abs(strtotime('-1days',  strtotime($d1)) - strtotime($d2))/86400);
}
function dateTimeDiff($d1, $d2){
    // return the number of hours between two date times 
    return round(abs(strtotime($d1) - strtotime($d2))/3600);
}
?>
