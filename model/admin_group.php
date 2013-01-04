<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin_group
 *
 * @author Shamsul
 */
class admin_group {
    private $number_of_groups = 0;
    private $group_list = array();
    private $child_groups = array();
    private $counter = 0;
    
    public function __construct() {
        include $_SESSION['root_dir'].'/dbconnect.php';
        $q_select = "SELECT * FROM admin_group";
        $q_select1 = mysql_query($q_select) or die(mysql_error());
        $this->number_of_groups = mysql_num_rows($q_select1);
        if($this->number_of_groups > 0){
            $i = 0;
            while($rows = mysql_fetch_object($q_select1)){
                $this->group_list[$i]['id'] = $rows->id;
                $this->group_list[$i]['name'] = $rows->name;
                $this->group_list[$i]['create_by'] = $rows->create_by;
                $i++;
            }
        }else{
            return 0;
        }      
    }
    public function getNumberOfGroups(){
        return $this->number_of_groups;
    }
    public function getGroupList(){
        return $this->group_list;
    }
    public function getChildGroups($parent_id){
        $q_child = mysql_query("SELECT id, name FROM admin_group WHERE parent_group=$parent_id") or trigger_error(mysql_error());
        while($result = mysql_fetch_object($q_child)){
            $this->child_groups[$this->counter]['id'] = $result->id;
            $this->child_groups[$this->counter]['name'] = $result->name;
//            print_r($this->child_groups);
            $this->counter++;
            $this->getChildGroups($this->child_groups[$this->counter-1]['id']);
        }
        return $this->child_groups;
    }
    public function getGroupNameByID($group_id){
        print_r($this->group_list);
        echo array_search($group_id, $this->group_list);
    }
/*    public function getAccessGroupList($group_name){
        
        if($group_name == 'twosell_super'){
            return array(
                '0' => 'twosell',
                '1' => 'chain',
                '2' => 'store',
                '3' => 'cashier'
            );
        }else if($group_name == 'twosell'){
            return array(
                '0' => 'chain',
                '1' => 'store',
                '2' => 'cashier'
            );
        }else if($group_name == 'chain'){
            return array(
                '0' => 'store',
                '1' => 'cashier'
            );
        }else if($group_name == 'store'){
            return array(
                '0' => 'cashier'
            );
        }
        
    }*/
}

?>
