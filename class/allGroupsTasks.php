<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of allGroupsTasks
 *
 * @author Shamsul
 */
//include 'check_permissions.php';
class allGroupsTasks {
    private $groups_tasks = array();
    private $groups = array();
    private $tasks = array();
    private $total_group = 0;
    
    public function __construct(){
        $q_groups = "SELECT id, name FROM admin_group";
        $q_groups1 = mysql_query($q_groups) or die("Select group failed: ".mysql_error());
        $i = 0;
        while($rows = mysql_fetch_object($q_groups1)){
            $this->groups_tasks[$i]['group_id'] = $rows->id;
            $this->groups_tasks[$i]['group_name'] = $rows->name;
            $permissions = new check_permissions($rows->id); 
            $this->groups_tasks[$i]['tasks'] = $permissions->permission();
            $i++;
            $this->groups[$rows->id]['group_name'] = $rows->name;
            $this->groups[$rows->id]['tasks'] = $permissions->permission();
        }
    } 
    public function getAllGroupsTasks(){
        return $this->groups_tasks;
    }
    public function getTotalGroup(){
        return count($this->groups_tasks);
    }
    public function getGroupById($group_id){
        return $this->groups[$group_id];
    }
    public function getTasksById($group_id){
        $count = count($this->groups[$group_id]['tasks']);
        for($i=0; $i< $count ; $i++){
            $this->tasks[$i] = $this->groups[$group_id]['tasks'][$i]['task_id'];
        }
        return $this->tasks;
    }
}

?>
