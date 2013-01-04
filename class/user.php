<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author Shamsul
 */
include $_SESSION['root_dir'].'/dbconnect.php';

class user {
    private $user_name = '';
    private $group_id = '';
    private $group_name = '';
    private $chain_id = array();
    private $chain_name = array();
    private $store_id = array();
    private $employee_id = '';
    private $first_name = '';
    private $last_name = '';
    private $last_login_time = '';
    private $address = '';
    private $password = '';
    private $isActive = 0;
    private $insert_date = '';
    private $id = '';
    
    public function __construct($user){
        $q_user = "SELECT * FROM admin WHERE user_id='$user' LIMIT 1";
        $q_user1 = mysql_query($q_user) or die("User query at class/user.php:".mysql_error());
        $user_row = mysql_fetch_object($q_user1);
        
        $this->setUser($user);
        
        $q = "SELECT name FROM admin_group WHERE id=$user_row->group_id LIMIT 1";
        $q1 = mysql_query($q) or die("Select Group Error on class/user.php:".mysql_error());
        $row = mysql_fetch_object($q1);
        $this->setGroup($user_row->group_id, $row->name);
        
        $this->setFirstName($user_row->first_name);
        $this->setLastName($user_row->last_name);
        $this->setChain($user_row->chain_id);
        $this->setStoreId($user_row->store_id);
        $this->setEmployeeId($user_row->employee_id);
        $this->setLastLoginTime($user_row->last_login_time);
        $this->setPassword($user_row->password);
        $this->setActive($user_row->active);
        $this->setInsertDate($user_row->insert_date);
        $this->setID($user_row->id);
    }
    
    public function setUser($user_name){
        $this->user_name = $user_name;
    }
    public function setGroup($group_id, $group_name){
        $this->group_id = $group_id;
        $this->group_name = $group_name;
    }
    public function setChain($chain_id){
        if($chain_id == '*'){
            $q = "SELECT * FROM twosell_chain ORDER BY internal_id";
            $q1 = mysql_query($q) or die("Select chain fail at class/user.php:".  mysql_error());
            while($rows = mysql_fetch_object($q1)){
                $this->chain_id[] = $rows->id;
                $this->chain_name[$rows->id] = $rows->internal_id;
            }
        }
        else{
            $this->chain_id = explode(',',$chain_id);
            // chain name needs to be add later
            //$this->chain_name[$chain_id] = 
        }
    }
    public function getChainCount(){
        return count($this->chain_id);
    }
    public function setEmployeeId($employee_id){
        $this->employee_id = $employee_id;
    }
    public function setFirstName($fname){
        $this->first_name = $fname;
    }
    public function setLastName($lname){
        $this->last_name = $lname;
    }
    private function setPassword($password){
        $this->password = $password;
    }
    public function setStoreId($store_id){
//        print_r($this->chain_id);
        if($store_id == '*'){
            for($i=0; $i < $this->getChainCount(); $i++){
                $chain = $this->chain_id[$i];
                
                $q_store = "SELECT * FROM twosell_store WHERE chain_id=$chain AND active=1";
                
                $q_store1 = mysql_query($q_store) or die('Select stores fail at class/user.php:'.mysql_error());
                $j = 0;
                if(mysql_num_rows($q_store1) > 0){
               //     echo $q_store."<br/>";
                    while($q_store2 = mysql_fetch_object($q_store1)){
                        $this->store_id[$this->chain_id[$i]][$j] = $q_store2->internal_id;
                        $j++;
                    }
                }
            }
        }
        else{
            $this->store_id[$this->chain_id[0]] = explode(',', $store_id);
        }
    }
    public function setAddress($address){
        $this->address = $address;
    }
    public function setActive($active){
        $this->isActive = $active;
    }
    public function setLastLoginTime($last_login){
        $this->last_login_time = $last_login;
    }
    public function setInsertDate($date){
        $this->insert_date = $date;
    }
    public function setID($id){
        $this->id = $id;
    }
    
    public function getChainId(){
        return $this->chain_id;
    }
    public function getChainName(){
        return $this->chain_name;
    }
    public function getStoreID(){
        return $this->store_id;
    }
    public function getStoreIdByChain($chain_id){
        return $this->store_id[$chain_id];
    }
    public function getEmployeeID(){
        return $this->employee_id;
    }
    public function getGroupID(){
        return $this->group_id;
    }
    public function getFirstName(){
        return $this->first_name;
    }
    public function getLastName(){
        return $this->last_name;
    }
    public function getGroupName(){
        return $this->group_name;
    }
    public function getPassword(){
        return $this->password;
    }
    public function getLastLoginTime(){
        return $this->last_login_time;
    }
    public function isActiveText(){
        if($this->isActive == true)
            return "Active";
        else return "Disabled";
    }
    public function isActive(){
        return $this->isActive;
    }
    public function getInsertDate(){
        return $this->insert_date;
    }
    public function getID(){
        return $this->id;
    }
}

?>
