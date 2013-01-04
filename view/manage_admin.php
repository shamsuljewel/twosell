<?php
    if(!isset($_SESSION)) session_start();   
    include 'model/admin_group.php';
?>
<script>
    
     
</script>
<?php
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
echo "<h1>View Admin Information: </h1>";
$groups = new admin_group();
$group_id = $user->getGroupID();
$group_lists = $groups->getChildGroups($group_id);
//print_r($group_lists);
$group_query_help = '';
for($i=0; $i < count($group_lists); $i++) {
    $id = $group_lists[$i]['id'];
    if($i != count($group_lists) -1) $group_query_help .= "group_id = $id OR ";
    else $group_query_help .= "group_id = $id";
}
//echo $group_query_help;
$q = "SELECT id,user_id FROM admin WHERE $group_query_help ORDER BY user_id";
$q1 = mysql_query($q);
if($q1 != FALSE){
    echo "<table class='view_tbl'><tr><td  style='border: 0; background-color: white'>";
    echo "<div id='error-box' class='error-valid' style='display:none'>error</div>"; 
    echo "</td></tr></table>";

    echo "<table border='1' class='view_tbl'>";
    echo "<tr><th>SL.</th><th>User Id</th><th>First Name</th><th>Last Name</th><th>Group</th><th>Active</th><th>Options</th><th>Join Date</th><th>Add By</th></tr>";
    $i = 1;
    while($q2 = mysql_fetch_array($q1)){
        $user = new user($q2[user_id]);
        if($q2['active'] == 1) $active = "Disabled";
        else $active = "Active";
            // <div id='editlevel-$i' style='display: none'><select id='seditlevel-$i'><option value='0'"; if($q2[level]==0) echo "selected"; echo ">Primary Admin</option><option value='1'"; if($q2[level]==1) echo "selected"; echo ">Super Admin</option></select></div>
            if($i%2 == 1) $back = "style='background-color: white'";
            else $back = '';
            echo "<tr ><td $back>".$i."</td>
                    <td width='100' $back><div id='adminlevel-$i'>$q2[user_id]</div></td>
                    <td width='120' $back><div id='fname-$i'>".$user->getFirstName()."</div><div id='editfname-$i' style='display: none; width: 140px'><input type='text' value='$q2[first_name]' id='textfname-$i'  /></div></td>
                    <td width='120' $back><div id='lname-$i'>".$user->getLastName()."</div><div id='editlname-$i' style='display: none; width: 140px'><input type='text' value='$q2[last_name]' id='textlname-$i'  /></div></td>
                    <td width='100' $back><div id='group-$i'>".$user->getGroupName()."</div></td>
                    <td width='60' $back><div id='adminactive-$i'>".$user->isActiveText()."</div><div id='editactive-$i' style='display: none'><select id='seditactive-$i'><option value='0'"; if($q2['active']==0) echo "selected"; echo ">Active</option><option value='1'"; if($q2['active']==1) echo "selected"; echo ">Disabled</option></select></div></td>
                    <td $back>"; //if($per == 0) echo "<div style='display: none'>"; echo "<div id='div-$i'><a id='$i' href='admin.php?page=edit_admin' title='Edit' class='edit_admin'><img src='images/edit.gif' /></a><a id='$i' href='admin.php?page=delete_admin' title='Delete' class='delete_admin'><img src='images/delete_on.png' /></a></div>
                   // <div id='update-$i' style='display:none'><input type='button' value='Update' id='$i' class='updateadminButton' /></div>"; if($per == 0) echo "</div>"; 
                   $user_id_encrypt = $q2[user_id];
                    echo "<a href='admin.php?page=edit-admin&id=$user_id_encrypt'><img src='images/edit.gif' /></a> </td>";
                   echo "<td width='80' $back>".date('d, M y',strtotime($user->getInsertDate()))."</td>
                            <td $back>-</td></tr>";
            echo "<input type='hidden' id='id-$i' value='$q2[id]' />";
            $i++;
        }
        
    echo "</table>";
}else{
    echo "Error Occured please contact to Webmaster";
}
echo "[N.B: No edit possible for current version]";
?>
