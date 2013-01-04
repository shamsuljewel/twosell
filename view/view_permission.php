<?php

if(!isset($_SESSION)) session_start();
if(isLoggedin() == FALSE){
    header("Location:index.php");	
    exit();
}
include 'class/allGroupsTasks.php';
$group_tasks = new allGroupsTasks();
//print_r($group_tasks->getAllGroupsTasks());
$allGroupTasks = $group_tasks->getAllGroupsTasks();
$total_group = $group_tasks->getTotalGroup();
echo "<h1>View Group Permissions: </h1>";
echo "<table border='0' width='98%' class='view_tbl'> ";
echo "<tr><th>Group Name</th><th></th><th>Tasks</th>";
for($i = 0; $i < $total_group; $i++){

    echo "<tr>";
    echo "<td>";
    echo $allGroupTasks[$i]['group_name'];
    //echo $allGroupTasks[$i]['group_id'];
    $group_id = $allGroupTasks[$i]['group_id'];
    echo "</td>";
    echo "<td><input type='button' name='change_permission' id='$group_id' value='Change' class='change_permission' /> </td>";
    echo "<td>";
    $tasks = $allGroupTasks[$i]['tasks'];
    //print_r($tasks);
    //echo $tasks[0]['task_name'];
    $task_count = count($tasks);
    for($j = 0; $j < $task_count; $j++){
        echo $tasks[$j]['task_name'].", ";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

?>
