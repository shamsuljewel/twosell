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
function calPercent($total, $value){
    if($total <= 0){
        return 0;
    }else{
        return round(($value * 100) / $total, 2);
    }
}
function avg($value, $total){
    if($total <= 0){
        return 0;
    }  else {
        return round($value/$total,2);
    }
}
function isLoggedin(){

    if(empty($_SESSION['user']['name']))
        return false;
    else 
        return true;
}
function check_user(){
    $user = $_SESSION['user']['name'];
    $q_admin = "SELECT * FROM admin WHERE user_id = '$user' AND active='1' LIMIT 1";
    $q_admin1 = mysql_query($q_admin) or die(mysql_error());
    $valid = mysql_num_rows($q_admin1);
    if($valid > 0){
        return TRUE;
    }
    else{
        return FALSE;
    }
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
function sql_query($q, $link){
   $result = array(); 
   $error = ""; 
    $q1 = mysql_query($q, $link);
   if($q1 != FALSE){
       if(mysql_num_rows($q1) > 0){
           while($q2 = mysql_fetch_assoc($q1)){
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
function count_items_array_banned($q, $items){
    if(is_array($items)){
        $count_array = array();
        $count = count($items);
        while($q2 = mysql_fetch_array($q)){
            for($i=0; $i < $count; $i++){
                $count_array[$items[$i]] += $q2[$items[$i]];
            }
            $q_t_items = "SELECT * FROM transaction_tbl WHERE ref_purchase = '$q2[id]' LIMIT 1";
            $q_t_items1 = mysql_query($q_t_items) or die(mysql_error()); 
            $q_t_items2 = mysql_fetch_array($q_t_items1);
            if($q_t_items2['twosell_items'] != NULL) $twosell_items = json_decode($q_t_items2['twosell_items'], true);
            //print_r($twosell_items);
            if(!empty($twosell_items[items])){
                foreach ($twosell_items[items] as $key => $value) {
              //      echo $value."->";
                    if(in_array($value, $_SESSION['banned_products'])){
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
                             $count_array['direct_gross_incl_vat'] -= $all_items[items][$search_key[0]][amount];
                             $count_array['direct_gross_incl_vat'] += $all_items[items][$search_key[0]][discount];
                             $count_array['twosell_item_count'] -= 1; 
                        }
                    }
                }
            }
        }
        return $count_array;
    }
}
function print_table($data, $dateOf, $totalArray){
//    print_r($totalArray);
    //echo $all_store_total_receipt;
    $_SESSION['dateOf'] = $dateOf;
    $total_dates = count($dateOf);
    $total_store = count($data);
    if($total_dates > 0 && count($dateOf)> 0){
        $array_index = array_keys($data);
        //print_r($array_index);
//        echo $array_index[0];
        echo "<table class='view_tbl'>";
        echo "<tr><th>Store</th>";
        echo "<th>Total</th>";
        foreach($dateOf as $day){
            echo "<th>".date("d/m", strtotime($day))."</th>";
        }
        echo "</tr>";
        echo "<tr><td>All</td>";
        echo "<td><table class='conjusted_tbl'><tr><td></td><td><b>Tot</b></td><td><b>TS</b></td><td></td></tr>
            <tr><td><b>R</b></td>
            <td>".$totalArray['all_total_receipt']."</td><td>".$totalArray['all_twosell_receipt']."</td>
                <td>".  calPercent($totalArray['all_total_receipt'], $totalArray['all_twosell_receipt'])."</td></tr>
            <tr><td><b>C</b></td><td>".$totalArray['all_total_cost']."</td><td>".$totalArray['all_twosell_cost']."</td>
                <td>".  calPercent($totalArray['all_total_cost'], $totalArray['all_twosell_cost'])."</td></tr>        
            </table></td>";
        foreach($dateOf as $day){
            echo "<td><table class='conjusted_tbl'><tr><td></td><td><b>Tot</b></td><td><b>TS</b></td><td></td></tr>
                  <tr><td><b>R</b></td>
                  <td>".$totalArray[$day]['all_store_total_receipt']."</td>
                  <td>".$totalArray[$day]['all_store_twosell_receipt']."</td>";
            echo "<td>".calPercent($totalArray[$day]['all_store_total_receipt'], $totalArray[$day]['all_store_twosell_receipt'])."</td></tr>";      
            echo "<tr><td><b>C</b></td>    
                  <td>".$totalArray[$day]['all_store_total_cost']."</td>
                  <td>".$totalArray[$day]['all_store_twosell_cost']."</td>
                  <td>".calPercent($totalArray[$day]['all_store_total_cost'], $totalArray[$day]['all_store_twosell_cost'])."</td></tr>
            </table>";
        }
        echo "</tr>";
        
        for($i = 0; $i < $total_store; $i++){
            echo "<tr>";
            echo "<td><a href='admin.php?page=day-cashier-stat&store_id=$array_index[$i]'>$array_index[$i]</a></td>";
            echo "<td style='background-color: #e0dded'>";
            echo "<table class='conjusted_tbl'>
                            <tr><td></td><td><b>Tot</b></td><td><b>TS</b></td><td></td></tr>
                            <tr>
                                <td><b>R</b></td>
                                <td>".$data[$array_index[$i]]['all_total_receipt']."</td>
                                <td>".$data[$array_index[$i]]['all_twosell_receipt']."</td>
                                <td>"; if($data[$array_index[$i]]['all_total_receipt']!=0)
                                    echo round(($data[$array_index[$i]]['all_twosell_receipt'] * 100) / $data[$array_index[$i]]['all_total_receipt'],1);
                                    else echo "0";
                                    echo "%</td>    
                            </tr>
                            <tr>
                                <td><b>C</b></td>
                                <td>".$data[$array_index[$i]]['all_total_cost']."</td>
                                <td>".$data[$array_index[$i]]['all_twosell_cost']."</td>
                                <td>"; if($data[$array_index[$i]]['all_total_cost']!=0){
                                    echo round(($data[$array_index[$i]]['all_twosell_cost'] * 100) / $data[$array_index[$i]]['all_total_cost'],1);
                                    $all_avg_before = round(($data[$array_index[$i]]['all_total_cost'] - $data[$array_index[$i]]['all_twosell_cost']) / $data[$array_index[$i]]['all_total_receipt'],1);
                                    $all_avg_after = round($data[$array_index[$i]]['all_total_cost'] / $data[$array_index[$i]]['all_total_receipt'],1);
                                }
                                else echo "0";
                                    echo "%</td>    
                            </tr>
                            <tr>
                                <td><b>In</b></td>
                                <td>".$all_avg_before."</td>
                                <td>".$all_avg_after."</td>
                                <td>".round(abs($all_avg_after - $all_avg_before),1)." Kr.</td>    
                            </tr>
                        </table>";    
            echo "</td>";
            $dates_stat = array_keys($data[$array_index[$i]]);
//            print_r($dates_stat);
            $dates_stat_count = count($dates_stat);
            for($j=0; $j < $total_dates; $j++){
                echo "<td>";
                for($k=0; $k < $dates_stat_count; $k++){
 //                   echo $data[$array_index[$i]][$k];
                    if($dateOf[$j] == $dates_stat[$k]){
                        echo 
                        "<table class='conjusted_tbl'>
                            <tr><td></td><td><b>Tot</b></td><td><b>TS</b></td><td></td></tr>
                            <tr>
                                <td><b>R</b></td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['total_receipt']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['total_twosell']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['per']."%</td>    
                            </tr>
                            <tr>
                                <td><b>C</b></td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['total_cost']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['twosell_cost']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['twosell_per']."%</td>    
                            </tr>
                            <tr>
                                <td><b>In</b></td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['avg_rec_value_before']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['avg_rec_value_after']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['avg_rec_increase']." Kr.</td>    
                            </tr>
                        </table>";
                    }
                }
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}
function print_table_cashier($data, $dateOf){
    $total_dates = count($dateOf);
    $total_cashier = count($data);
    if($total_dates > 0 && count($dateOf)> 0){
        $array_index = array_keys($data);
        //print_r($array_index);
//        echo $array_index[0];
        echo "<table class='view_tbl'>";
        echo "<tr><th>Cashier</th>";
        echo "<th>Total</th>";
        foreach($dateOf as $day){
            echo "<th>".date("d/m", strtotime($day))."</th>";
        }
        echo "</tr>";
        for($i = 0; $i < $total_cashier; $i++){
            echo "<tr>";
            
            echo "<td>$array_index[$i]</td>";
            echo "<td style='background-color: green'>";
            echo "<table class='conjusted_tbl'>
                            <tr><td></td><td><b>Tot</b></td><td><b>TS</b></td><td></td></tr>
                            <tr>
                                <td><b>R</b></td>
                                <td>".$data[$array_index[$i]]['all_total_receipt']."</td>
                                <td>".$data[$array_index[$i]]['all_twosell_receipt']."</td>
                                <td>"; 
                                    if($data[$array_index[$i]]['all_total_receipt']!=0)
                                        echo round(($data[$array_index[$i]]['all_twosell_receipt'] * 100) / $data[$array_index[$i]]['all_total_receipt'],1);
                                    else 
                                        echo "0";
                                    echo "%</td>    
                            </tr>
                            <tr>
                                <td><b>C</b></td>
                                <td>".$data[$array_index[$i]]['all_total_cost']."</td>
                                <td>".$data[$array_index[$i]]['all_twosell_cost']."</td>
                                <td>"; 
                                    if($data[$array_index[$i]]['all_total_cost']!=0){
                                        echo round(($data[$array_index[$i]]['all_twosell_cost'] * 100) / $data[$array_index[$i]]['all_total_cost'],1);
                                        $all_avg_before = round(($data[$array_index[$i]]['all_total_cost'] - $data[$array_index[$i]]['all_twosell_cost'])/ $data[$array_index[$i]]['all_total_receipt'],1); 
                                        $all_avg_after = round($data[$array_index[$i]]['all_total_cost'] / $data[$array_index[$i]]['all_total_receipt'],1);
                                    }
                                    else{ 
                                        echo "0";
                                    }
                                        echo "%</td>    
                            </tr>
                            <tr>
                                <td><b>In</b></td>";
                                
                                echo "<td>$all_avg_before</td>
                                <td>$all_avg_after</td>
                                <td>"; echo round(abs($all_avg_after - $all_avg_before),1); echo "Kr.</td>    
                            </tr>
                        </table>";    
            echo "</td>";
            $dates_stat = array_keys($data[$array_index[$i]]);
//            print_r($dates_stat);
            $dates_stat_count = count($dates_stat);
            for($j=0; $j < $total_dates; $j++){
                echo "<td>";
                for($k=0; $k < $dates_stat_count; $k++){
 //                   echo $data[$array_index[$i]][$k];
                    if($dateOf[$j] == $dates_stat[$k]){
                        echo 
                        "<table class='conjusted_tbl'>
                            <tr><td></td><td><b>Tot</b></td><td><b>TS</b></td><td></td></tr>
                            <tr>
                                <td><b>R</b></td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['total_receipt']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['total_twosell']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['per']."%</td>    
                            </tr>
                            <tr>
                                <td><b>C</b></td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['total_cost']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['twosell_cost']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['twosell_per']."%</td>    
                            </tr>
                            <tr>
                                <td><b>In</b></td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['avg_rec_value_before']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['avg_rec_value_after']."</td>
                                <td>".$data[$array_index[$i]][$dates_stat[$k]]['avg_rec_increase']." Kr.</td>    
                            </tr>
                        </table>";
                    }
                }
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}
function dateDiff($d1, $d2){
    // return the number of days between two dates
    return round(abs(strtotime('-1days',  strtotime($d1)) - strtotime($d2))/86400);
}
function dateTimeDiff($d1, $d2){
    // return the number of hours between two date times 
    return round(abs(strtotime($d1) - strtotime($d2))/3600);
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
/*
 * Return the original difference of two arrays like array1 = { [0] => "123", [1] => "122", [2] => "123"} 
 * and array2 = { [0] => "123", [1] => "122"}
 * will return {[2] => "123"}
 */
function array_diff_once(){
    // check the arguments if less than 2 then do nothing and return false
    if(($args = func_num_args()) < 2)
        return false;
    // getting the arguments
    $arr1 = func_get_arg(0);
    $arr2 = func_get_arg(1);
    // if any array is not actually a array return false 
    if(!is_array($arr1) || !is_array($arr2))
        return false;
    
    // doing the comparison
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
function permission($allow, $permission_tasks){
//    print_r($permission_tasks);
//    print_r($allow);
    $count = count($allow);
    $yes = false;
    for($i=0; $i < $count; $i++){
        if(in_array($allow[$i], $permission_tasks)){
            $yes = true;
            return $yes;
            break;
        }
    }
    return $yes;
}
function email_validation($email) {
  // First, we check that there's one @ symbol, 
  // and that the lengths are right.
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    // Email invalid because wrong number of characters 
    // in one section or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
    if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
      return false;
    }
  }
  // Check if domain is IP. If not, 
  // it should be valid domain name
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
        return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}
function password_checker($password){
    if(preg_match("#.*^(?=.{6,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password)){
        return true;
    } else {
        return false;
    }
}
function generate_Password($l = 9, $c = 0, $n = 0, $s = 0) {
     // get count of all required minimum special chars
     $count = $c + $n + $s;
 
     // sanitize inputs; should be self-explanatory
     if(!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)) {
          trigger_error('Argument(s) not an integer', E_USER_WARNING);
          return false;
     }
     elseif($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0) {
          trigger_error('Argument(s) out of range', E_USER_WARNING);
          return false;
     }
     elseif($c > $l) {
          trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
          return false;
     }
     elseif($n > $l) {
          trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
          return false;
     }
     elseif($s > $l) {
          trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
          return false;
     }
     elseif($count > $l) {
          trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
          return false;
     }
 
     // all inputs clean, proceed to build password
 
     // change these strings if you want to include or exclude possible password characters
     $chars = "abcdefghijklmnopqrstuvwxyz";
     $caps = strtoupper($chars);
     $nums = "0123456789";
     $syms = "!@#$%^&*()-+?";
 
     // build the base password of all lower-case letters
     for($i = 0; $i < $l; $i++) {
          $out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
     }
 
     // create arrays if special character(s) required
     if($count) {
          // split base password to array; create special chars array
          $tmp1 = str_split($out);
          $tmp2 = array();
 
          // add required special character(s) to second array
          for($i = 0; $i < $c; $i++) {
               array_push($tmp2, substr($caps, mt_rand(0, strlen($caps) - 1), 1));
          }
          for($i = 0; $i < $n; $i++) {
               array_push($tmp2, substr($nums, mt_rand(0, strlen($nums) - 1), 1));
          }
          for($i = 0; $i < $s; $i++) {
               array_push($tmp2, substr($syms, mt_rand(0, strlen($syms) - 1), 1));
          }
 
          // hack off a chunk of the base password array that's as big as the special chars array
          $tmp1 = array_slice($tmp1, 0, $l - $count);
          // merge special character(s) array with base password array
          $tmp1 = array_merge($tmp1, $tmp2);
          // mix the characters up
          shuffle($tmp1);
          // convert to string for output
          $out = implode('', $tmp1);
     }
 
     return $out;
}
function encrypt_text($value)
{
   if(!$value) return false;
 
   $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'twosell&Pocada', $value, MCRYPT_MODE_ECB, 'Shamsul Alam');
   // remove + coz it does not get from the get method when it encode it changes to space
   $remove_plus = base64_encode($crypttext);
   return trim(base64_encode($remove_plus));
   
}
 
function decrypt_text($value)
{
   if(!$value) return false;
 
   $crypttext = base64_decode($value);
   $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 'twosell&Pocada', $crypttext, MCRYPT_MODE_ECB, 'Shamsul Alam');
   return trim($decrypttext);
}
?>
