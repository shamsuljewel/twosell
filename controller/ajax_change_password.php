<?php
session_start();  
include '../class/changePassword.php';
if(!empty($_POST['currPassword']) && !empty($_POST['newPassword']) && !empty($_POST['confirmPassword'])){
    $currPassword = $_POST['currPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    $change_password = new changePassword($_SESSION['user']['name'], $currPassword, $newPassword, $confirmPassword);
    if($change_password->okToGo()){
        // change the password
        $change_password->setNewPassword();
        echo true;
    }
    else{
        // show the error message to user, where is the problem
        echo " Please try again..";
    }
}
else{
    echo "All the Fields marks * must fillup!";
}
?>
