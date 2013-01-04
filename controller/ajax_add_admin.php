<?php
session_start();   

include '../functions/commonFunction.php';
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
if(isset($_POST['adminType'])){
        $userId = $_SESSION['user']['id'];
        $adminType = $_POST['adminType'];
        $adminGroup = $_POST['adminGroup'];
        $userName = strtolower(trim($_POST['userName']));
        $password = trim($_POST['password']);
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $storeList = $_POST['storeList'];
        $total_store = count($storeList);
        for ($i = 0; $i < $total_store; $i++) {
            if($i == $total_store-1)
                $storeString .= $storeList[$i]; 
            else
                $storeString .= $storeList[$i].",";
        }
//        echo $storeString."<br />";
//        echo $adminType.",".$userName.",".$password.",".$firstName.",".$lastName.",".$adminGroup;
        if(!empty($userName) && !empty($password) && !empty($firstName) && $adminType != 'null'){
            if($adminGroup == 'store' && empty($storeList)){
                $error = "Please select a store!";
            }else if(!password_checker($password)){
                $error = "Password must be strong (combination of Caps, letters, minimum 6 characters, numbers required)";
            }else{
                $email_check = email_validation($userName); 
                if($email_check){
                include '../dbconnect.php';
                $dateTime = date("Y-m-d H:i:s");
                $pq = "SELECT user_id FROM admin WHERE user_id='$userName' LIMIT 1";
                $extra = "";
                $extra_value = "";
                $pq1 = mysql_query($pq);
                if($pq1 != FALSE){
                    if(mysql_num_rows($pq1) < 1){
                        if($adminGroup == 'twosell_super'){
                            $extra = ",chain_id,store_id";
                            $extra_value = ",'*', '*'";
                        }else if($adminGroup == 'twosell'){
                            $extra = ",chain_id,store_id";
                            $extra_value = ",'*', '*'";
                        }else if($adminGroup == 'chain'){
                            $extra = ",chain_id,store_id";
                            $extra_value = ",'1', '*'";
                        }else if($adminGroup == 'store'){
                            $extra = ",chain_id,store_id";
                            $extra_value = ",'1', '$storeString'";
                        }
                        $password = md5($password);
                        $q = "INSERT INTO admin(first_name, last_name, user_id, password, active, group_id, insert_date, insert_by".$extra.") 
                            VALUES('$firstName', '$lastName', '$userName', '$password','1','$adminType', '$dateTime', '$userId'".$extra_value.")";
//                        echo $q;
                        $q1 = mysql_query($q);
                        if($q1 != FALSE){
                            echo true;
                        }
                        else{
                            $error = "Query Execution Error, Please Contact to Webmaster...";
                        }
                    }
                    else{
                        $error = "Same User Name Already exists!!!";
                    }
                }
                // end check user exist query false chheck
                }
                // end check email validation
                else{
                    $error = "Write the email correctly";
                }
            }
        }
        else {
            $error = "Please fill all the fields";
        }
    }
    
    else {
        $error = "You are not authorized to this page..!";
    }
    echo $error;
?>