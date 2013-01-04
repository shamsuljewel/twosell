<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of changePassword
 *
 * @author Shamsul
 */
include '../class/user.php';

class changePassword {
    private $password;
    private $new_password;
    private $confirm_password;
    private $old_password;
    private $user_id;
    /*
     * constructor new the user_id, current password, new and confirm password
     */
    public function __construct($user_id, $curr_password, $new_password, $confirm_password) {
//        echo $user_id;
        $this->password = md5($curr_password);
        $this->new_password = $new_password;
        $this->confirm_password = $confirm_password;
        $this->user_id = $user_id;
        
        $user = new user($user_id);
        $this->old_password = $user->getPassword();
    }
    /*
     * check if everything given by the user is ok to change the password
     * if this function return true that means is ok to change the password else not ok
     */
    public function okToGo(){
        $notok = 0;
        if(empty($this->password) || empty($this->new_password) || empty($this->confirm_password)){
            $notok = 1;
            echo "field empty";
        }else{
            // check the current password match?
            if($this->password !== $this->old_password){
                $notok = 1;
                echo "current password not match!";
            }else{
                // check the new password and confirm password
                if($this->new_password !== $this->confirm_password){
                    $notok = 1;
                    echo "confirm password not matched!";
                }
            }
        }
        if($notok){
            return false;
        }
        else{
            return true;
        }
    }
    public function setNewPassword(){
        $password = md5($this->new_password);
        // update the current password with the new password given
        $update_password = "UPDATE admin SET password = '$password' WHERE user_id='$this->user_id' LIMIT 1";
        mysql_query($update_password) or error_log("Set password Error at change password class", 0);
        
    }
}

?>
