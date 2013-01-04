<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of check_permissions
 *
 * @author Shamsul
 */
class check_permissions {
    private $group = '';
    private $permission_array = array();
    private $permission_tasks = array();
    private $permission_id = array();
    public function __construct($group) {
        $this->group = $group;
        $q = "SELECT task_code, task_name, tasks.id as task_id FROM tasks JOIN permission ON permission.task_id = tasks.id WHERE permission.group_id = $group";
        $q1 = mysql_query($q) or die(mysql_error());
        if(mysql_num_rows($q1) > 0){
            $i = 0;
            while($rows = mysql_fetch_array($q1)){
                $this->permission_array[$i]['task_id'] = $rows['task_id']; 
                $this->permission_array[$i]['task_code'] = $rows['task_code']; 
                $this->permission_array[$i]['task_name'] = $rows['task_name'];
                $this->permission_tasks[$i] = $rows['task_code']; 
                $this->permission_id[$i] = $rows['task_id'];
                $i++;
            }
        }
    }
    public function permission(){
        return $this->permission_array;
    }
    public function permissionTasks(){
        return $this->permission_tasks;
    }
    public function permissionTaskIds(){
        return $this->permission_id;
    }
}

?>
