<?php
session_start();   
include '../functions/commonFunction.php';
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
if(isset($_POST['id'])){
        $id = $_POST['id'];
        $left_text = $_POST['left_text'];
        $right_text = $_POST['right_text'];
        $dateTime = date("Y-m-d H:i:s");
        include '../dbconnect.php';
        
       $q = "UPDATE stat_text_msg SET left_text = '$left_text', right_text = '$right_text', update_date='$dateTime' WHERE id='$id' LIMIT 1"; 
       $q1 = mysql_query($q);
       if($q1 != FALSE){
            echo true;
        }else{
            $error = "Update Problem Occured, Please contact the webmaster.";
        }
        //echo $q;
    }
    else {
        $error = "You are not authorized to this page..!";
    }
    echo $error;
?>