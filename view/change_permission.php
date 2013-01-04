<?php
    if($_GET['id']) $group_id = $_GET['id'];
    else{
        exit('OPPS: Something wrong into this page...');
    }
    //echo $group_id;
    //$change_group_name =
    include 'class/allGroupsTasks.php';
    echo "<h1>Change Group Permissions:</h1>";
    $group_tasks = new allGroupsTasks();
    //echo "Group tasks:";
    $group = $group_tasks->getGroupById($group_id); 
    //print_r($group);
    //echo "<br />";
    echo "<div id='error-box' class='error-valid' style=' display:none' >error</div> <!-- Error Message Here -->";
    //echo "My Permissions:";
    //print_r($permission_tasks);
    $tasks = $group_tasks->getTasksById($group_id);
    //print_r($tasks);
    //print_r($permissions->permissionTaskIds());
    if(in_array('all', $permission_tasks) || in_array('update_group_permissions', $permission_tasks)){
        //echo "YOU CAN DO IT...";
    echo "<form name='change-permission-form' id='change-permission-form' method='post' action=''>";
    echo "<table border='1'><tr>";
    echo "<td>".$group['group_name']."</td>";
    echo "<td>";
    //echo "good";
    $q = "SELECT * FROM tasks";
    $q1 = mysql_query($q) or die("select tasks fail at view/change_permission.php: ".mysql_error());
    //$count = mysql_num_rows($q1);
    $i = 0;
    echo "<div id='checkboxes'>";
    echo "<table>";
    while($rows = mysql_fetch_object($q1)){
        if($i%3 == 0) echo "<tr>";
        echo "<td><input type='checkbox' name='tasks[]' value='$rows->id' ";
        if(in_array($rows->id, $tasks)){
            echo "checked='checked' ";
        }
        echo "/>".$rows->task_name."</td>";
        if($i%3 == 2) echo "</tr>";
        $i++;
    }
    echo "</table>";
    echo "</div>";
    echo "</td>";
    echo "</tr>
    <tr><td></td><td><input type='submit' name='change-permission' id='change-permission' class='submit_btn' value='UPDATE' /></td></tr>
    </table>";
    echo "<input type='hidden' name='group_id' id='group_id' value='$group_id' />";
    echo "</form>";
    }
    else{
        echo "You are not authorized to change group permissions";
    }
?>
