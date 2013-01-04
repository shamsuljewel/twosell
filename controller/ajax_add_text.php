<?php
session_start();   
include '../functions/commonFunction.php';
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
if(isset($_POST['chain'])){
        $chain = $_POST['chain'];
        $store = $_POST['store'];
        $store_null = "Select a store";
        if($store == $store_null)
            $store = -1;
        $cashier = $_POST['cashier'];
        if($cashier == "Choose a Cashier »") $cashier = -1;
        $left_text = utf8_encode($_POST['left_text']);
        $right_text = utf8_encode($_POST['right_text']);
        //echo $chain."-".$store."-".$cashier."<br />";
//        echo $left_text."<br />";
//        echo $right_text;
        $dateTime = date("Y-m-d H:i:s");
        include '../dbconnect.php';
        
        //echo $level.",".$userName.",".$password.",".$firstName.",".$lastName;
        if($left_text == "" && $right_text == ""){
            $error = "Please Fill the Left or Right Text...";
        }else{
            if(!empty($chain) && !empty($store) && !empty($cashier)){
                // all three selects add to cashier
                $q = "INSERT INTO stat_text_msg(chain, store, cashier, left_text, right_text, insert_date, update_date, insert_by, update_by, active) 
                    VALUES('$chain', '$store', '$cashier', '$left_text', '$right_text', '$dateTime', '$dateTime', '2', '2', '1')";
                $q1 = mysql_query($q);
                if($q1 != FALSE){
                    echo true;
                }else{
                    $error = "Insert Problem Occured, Please contact the webmaster.";
                }
            }
            else {
                $error = "Please fill all the fields";
            }
        }
        //echo $q;
    }
    else {
        $error = "You are not authorized to this page..!";
    }
    echo $error;
?>