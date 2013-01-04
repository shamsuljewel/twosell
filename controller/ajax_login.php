<?php 
session_start();
include '../dbconnect.php';
//echo "yes";	
$user_name=htmlspecialchars($_POST['user_name'], ENT_QUOTES);
//$pass=md5($_POST['password']);
$pass = $_POST['password'];
//$remember = $_POST['remember'];
//echo $remember;
//now validating the username and password
// if the active is 1 then the admin is disabled so can't login
$sql="SELECT * FROM admin WHERE user_id='".$user_name."' AND active='1' LIMIT 1";
//echo $sql;
$result=mysql_query($sql) or die("Select User Fail at controller/ajax_login.php: ".mysql_error());
if($result != FALSE){
    //if username exists then check for the password match
    if(mysql_num_rows($result) > 0)
    {
        $row=mysql_fetch_array($result);
        //compare the password
        if(strcmp($row['password'], md5($pass)) == 0){
//            if($remember == 1){
//                setcookie("loginCookie", $user_name, time()+3600);
//                setcookie("loginCookieId", $row['id'], time()+3600);
//            }
            //now set the session from here if needed
            //$user = new user($user_name, $group_id);
            $_SESSION['user']['name']=$user_name;
            $_SESSION['user']['id'] = $row['id'];
            
            
//            $dateTime = date("Y-m-d H:i:s");
//            $q = "UPDATE admin SET last_login_time = '$dateTime' WHERE id='$row[id]'";
//            mysql_query($q) or die(mysql_error());
            echo true;
        }
        else{
            //echo false; 
            echo "Invalid Login, Please try again.";
        }
    }
    else{
        //echo false; //Invalid Login, password does not match
        echo "Invalid Login, Please try again.";
    }
}
else{
    //echo false;  // query or other error, show the message contact to the administrator!!!
    echo "Something went wrong, please contact to Administrator.";
}
?>