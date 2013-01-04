<?php
session_start();   
include '../functions/commonFunction.php';
if(isLoggedin() == FALSE){
    header("Location:index.php");	
    exit('<p>You are not logged-in! <br />You do not have the access to this page.</p>');
}
if(isset($_POST['list'])){
    $group_id = $_POST['group_id'];
    //echo $group_id;
    $tasks = $_POST['list'];
    //print_r($ex_groups);
    include '../dbconnect.php';
    
    $q_delete = "DELETE FROM permission WHERE group_id='$group_id'";
    mysql_query($q_delete) or 
    die('Delete permission group task fail at controller/ajax_change_permission.php:'. mysql_error());
    foreach ($tasks as $task_id) {
      //echo $task_id.", ";
        $q = "INSERT INTO permission(group_id, task_id) VALUES('$group_id','$task_id')";
        mysql_query($q) or 
        die('Insert permission fail at controller/ajax_change_permission.php: '.mysql_error());       
    }
    //print_r($tasks);
    echo true;
    
    //$count_total = count($ex_groups);
}
else{
    $error = "Un-Authorised Access!!!";
    exit($error);
}
?>
