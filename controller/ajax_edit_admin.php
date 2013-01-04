<?php
if(!isset($_SESSION)) session_start();   

include '../functions/commonFunction.php';
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
if(isset($_POST['adminType'])){
//        $userId = $_SESSION['user']['id'];
        $adminType = $_POST['adminType'];
        $adminGroup = $_POST['adminGroup'];
        $userName = strtolower(trim($_POST['userName']));
        if(isset($_POST['password'])) $password = trim($_POST['password']);
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        if(isset($_POST['storeList'])){
            $storeList = $_POST['storeList'];
            $total_store = count($storeList);
            for ($i = 0; $i < $total_store; $i++) {
                if($i == $total_store-1)
                    $storeString .= $storeList[$i]; 
                else
                    $storeString .= $storeList[$i].",";
            }
        }
        
        $changePass = $_POST['changePass'];
//        for ($i = 0; $i < $total_store; $i++) {
//            if($i == $total_store-1)
//                $storeString .= $storeList[$i]; 
//            else
//                $storeString .= $storeList[$i].",";
//        }
//        echo $storeString."<br />";
//        echo $adminType.",".$userName.",".$password.",".$firstName.",".$lastName.",".$adminGroup.", ".$changePass;
        if($changePass == 1 && empty($password)){
            $error = "Password is Empty";
        }else if(!empty($userName) && !empty($firstName) && $adminType != 'null'){
            if($adminGroup == 'store' && empty($storeList)){
                $error = "Please select a store!";
            }else if(!password_checker($password) && $changePass == 1){
                $error = "Password must be strong (combination of Caps, letters, minimum 6 characters, numbers required)";
            }else{
                    include '../dbconnect.php';
                    $dateTime = date("Y-m-d H:i:s");
                    $password = md5($password);
                    $extra = "";
                    $extra_value = "";
                    
                    if($adminGroup == 'store'){
                        $extra = ",store_id='$storeString' ";
                    }
                    if($changePass == 1)
                        $pq = "UPDATE admin SET first_name='$firstName', last_name='$lastName', password='$password' $extra WHERE user_id='$userName' LIMIT 1";
                    else
                        $pq = "UPDATE admin SET first_name='$firstName', last_name='$lastName' $extra WHERE user_id='$userName' LIMIT 1";
//                    echo $pq;
                    $pq1 = mysql_query($pq) or trigger_error(mysql_error());
                    echo true;
            }
        }
     }
     else {
          $error = "You are not authorized to this page..!";
     }
     echo $error;
?>