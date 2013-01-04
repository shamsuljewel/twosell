<?php
session_start();   
include '../functions/commonFunction.php';
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
$chain_id = 1; // need to make dynamic later
if(isset($_POST['list'])){
    $ex_groups = $_POST['list'];
    //print_r($ex_groups);
    include '../dbconnect.php';
    $q_empty = mysql_query("TRUNCATE TABLE exclude_groups");
    foreach ($ex_groups as $key => $value) {
        $q = "INSERT IGNORE INTO exclude_groups(group_id, chain_id) VALUES('$value','$chain_id')";
        $q1 = mysql_query($q);       
    }
    $banned_group = banned_groups();
    $_SESSION['banned_group'] = $banned_group;

    $banned_products = banned_products($banned_group);
    $_SESSION['banned_products'] = $banned_products;
    echo true;
    
    //$count_total = count($ex_groups);
}
else{
    $error = "Un-Authorised Access!!!";
}
echo $error;
?>
