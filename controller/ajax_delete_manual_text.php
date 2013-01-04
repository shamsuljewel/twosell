<?php
session_start();   
include '../functions/commonFunction.php';
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
if(isset($_POST['id'])){
    $id = $_POST['id'];
    include '../dbconnect.php';
    $pq = "DELETE FROM stat_text_msg WHERE id='$id' LIMIT 1";
//    echo $pq;
    $pq1 = mysql_query($pq);
    if($pq1 != FALSE){
        echo "Manual Text Deleted..";
    }else{
        echo "Problem Occured, Please contact the webmaster.";
    }
}
else {
    echo "You are not authorized to this page..!";
}
?>