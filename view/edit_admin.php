<?php
if(!isset($_SESSION)) session_start();
//    $userId = $_SESSION['user']['id']; 
//    echo $userId;
//    print_r($permission_tasks);
//    if(in_array('_store_admin', $permission_tasks) || in_array('all', $permission_tasks)){
if(isset($_GET['id'])){    
$user_id = $_GET['id'];
//echo $_GET['id'];
//echo decrypt_text($_GET['id']);
    
if(permission(array('all','create_twosell_admin','create_chain_admin', 'create_store_admin', 'create_cashier_admin'), $permission_tasks)){    
    include 'model/admin_group.php';
    include '../class/user.php';
    $groups = new admin_group();
//    $total_group = $groups->getNumberOfGroups();
    $group_name = $user->getGroupName();
    $group_id = $user->getGroupID();
    $group_lists = $groups->getChildGroups($group_id);
    $total_group = count($group_lists);
    /*
     * edit users information
     */
    $edit_user = new user($user_id);
    $id = $edit_user->getID();
    $edit_group_id = $edit_user->getGroupID();
    $edit_firstName = $edit_user->getFirstName();
    $edit_lastName = $edit_user->getLastName();
    $edit_group_name = $edit_user->getGroupName();
    $edit_user_storeList = $edit_user->getStoreIdByChain(1);
//    print_r($edit_user_storeList);
?>
  <div id="admin_home">
    <div id="left_in">
	<div id="form_id">
        <p class="bold bigsize">Edit Admin Information</p>
        <div id="error-box" class="error-valid" style="display:none" >error</div> <!-- Error Message Here -->
            <form name="formAddAdmin" id="formAddAdmin" method="post" action="" >
                <fieldset>
                <legend>Edit Admin</legend>
                <table border="0" width="800"><tr><td width="340">
                <ol>
                    <li>
                            <label>Admin Type *</label>
                            <?php 
                                if($total_group > 0){
//                                    $access_group_list = $groups->getAccessGroupList($group_name);
                                    
//                                    $group_lists = $groups->getGroupList();
                                    echo "<select name='admin_type' id='admin_type' disabled='disabled'>";
                                    echo "<option value='null'>Select a Group</option>";
                                    for($i=0; $i < $total_group; $i++){
                                        $group_id = $group_lists[$i]['id'];
                                        $group_name = $group_lists[$i]['name'];
                                        // block adding cashier for now
                                        
                                        if($group_name != 'cashier'){
                                            echo "<option value='$group_id' "; 
                                            if($group_id == $edit_group_id) echo "selected='selected'";
                                            echo ">$group_name</option>";
                                        }
                                    }
                                    echo "</select>";
                                }
                                else{
                                    echo "No groups";
                                }
                            ?>    
                            
                    </li>
                    <li>
                            <label>User Name *<br />(email)</label>
                            <input type="text" name="userName" id="userName" onFocus="userName.value='<?php echo $id; ?>'" disabled="disabled" value="<?php echo $user_id; ?>" />
                    </li>
                    <li>
                            <label>Password *</label>
                            <input type="password" name="password" id="password" onFocus="password.value=''" disabled="disabled"/>
                            <input type="button" value="Set Password" id="EditsetPassword" style="float: right" />
                    </li>
                    <li>
                        <label>&nbsp;</label>
                        <div id="showPassword"></div>
                        <input type="button" name="generatePasssword" id ="generatePassword" value="Gen. Password" style="float:right" />
                    </li>
                    <li>
                            <label>Admin First Name *</label>
                            <input type="text" name="firstName" id="firstName" value="<?php echo $edit_firstName; ?>" />
                    </li>
                    <li>
                            <label>Admin Last Name</label>
                            <input type="text" name="lastName" id="lastName" value="<?php echo $edit_lastName; ?>" />
                            
                    </li>
                    </ol>
                    </td><td valign="top" width="460" style="border-left: 1px solid #CCC">
                        <div>Assign Stores</div>
                        <div id="store_asign">
                            <?php 

//                                print_r($childs);
                                if($edit_group_name == 'store'){
                                    $q = "SELECT title, id, internal_id FROM twosell_store WHERE active='1'";
                                    $result = sql_query($q, $link);
                                    if(count($result) > 0){
    //                                    print_r($result);
                                        for($i=0; $i < count($result); $i++){
                                            $store_internalid = $result[$i]['internal_id'];
                                            echo "<input type='checkbox' name='storebox' value='$store_internalid' "; 
                                            if(in_array($store_internalid, $edit_user_storeList)) 
                                                    echo "checked='checked'";
                                            echo ">$store_internalid";
                                        }
                                    }
                                    else{
                                        //echo $result;
                                    }
                                }
                            ?>
                        </div>
                    </td></tr>
                    <tr><td align="center"><input type="button" name="editAdmin" id="editAdmin" value="Edit Admin" class="submit_btn"  /></td><td></td></tr>
                    </table>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<?php
}
else{
    echo "<div class='w_msg'>You Do not have permission to add new admin.<br />Only Super Admin can add new admin.</div>";
}
}else{
    header("LOCATION: index.php");
}
?>
