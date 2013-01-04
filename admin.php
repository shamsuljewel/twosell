<?php 
ob_start();
error_reporting(E_ALL); 
// error log ON
ini_set('log_errors','1'); 
// display error NO
ini_set('display_errors','0'); 
// set the location of this script error log file
//$root = $_SERVER['DOCUMENT_ROOT']."/twosell";
ini_set('error_log', 'c:/xampp/htdocs/twosell/error_log/site.log');
//ini_set('error_log', '/home/twosell/data/admin.twosell.se/error_log/site.log');
//ini_set('error_log', '/home/twosell/data/admin.dev.twosell.se/error_log/site.log');
set_error_handler("customError");
/*
 * error number, error message, error file name and the line number
 * after then kill the script
 */
function customError($errno, $errstr, $errfile, $errline){
  error_log("Error: [$errno] $errstr on $errfile at line $errline", 0);
}
/* set the cache limiter to 'private' */

//session_cache_limiter('private');
//$cache_limiter = session_cache_limiter();

/* set the cache expire to 3 days */
//session_cache_expire(4320);
//$cache_expire = session_cache_expire();
//ini_set(‘session.cookie_httponly’, true);
session_start();
//    include 'include_pages.php';
    include 'admin_config.php';
//  Developed by Shamsul Alam
//  Visit http://bangladeshprogrammer.com for this script and more.
//  This notice MUST stay intact for legal use

// if session is not set redirect the user
if(empty($_SESSION['user']))
	header("Location:index.php");	

//if logout then destroy the session and redirect the user
if(isset($_GET['logout'])){
        if(isset($_COOKIE['loginCookie']) && isset($_COOKIE['loginCookieId'])){
            setcookie("loginCookie", "", time()-3600);
            setcookie("loginCookieId", "", time()-3600);
        }
        include 'dbconnect.php';
        $dateTime = date("Y-m-d H:i:s");
        $user_id = $_SESSION[user][id];
//        echo $user_id;
        $q = "UPDATE admin SET last_login_time = '$dateTime' WHERE id='$user_id' LIMIT 1";
        mysql_query($q) or die(mysql_error());
	session_destroy();
	header("Location:index.php");

        die();
}	
//echo "The cache limiter is now set to $cache_limiter<br />";
//echo "The cached session pages expire after $cache_expire minutes";
if(isset($_GET['page'])) $page = $_GET['page'];
else $page='null';
include 'dbconnectonline.php';
include 'dbconnect.php';
//echo $_COOKIE['loginCookie'];
if(isset($_COOKIE['loginCookie']) && isset($_COOKIE['loginCookieId'])){
    $user = $_SESSION['user']['name'] = $_COOKIE['loginCookie']; 
}else{
    $user = $_SESSION['user']['name'];
}
include 'dbconnect.php';
include 'class/check_permissions.php';
include 'functions/commonFunction.php';
// check again the user from the database for further validation to avoide the session stole security
// further validation 

$valid = check_user();
if($valid === TRUE){
    include 'class/user.php';
    $user = new user($user);
    $group = $user->getGroupID();
    $group_name = $user->getGroupName();
    //echo $group_name;
    $permissions = new check_permissions($group);
    $permission = array();
    if(is_array($permissions->permission())){
        $permission = $permissions->permission();
        $permission_tasks = $permissions->permissionTasks();
        if(!empty($permission)){
           //print_r($permission);
        } 
    }
?>
<!DOCTYPE html>
<head>
    <?php 
        // all the head section is here ex. javascript links, css links, meta data etc.
        require_once 'view/head.php';
    ?>
</head>

<body>
<table width="99%" border="0" cellspacing="0" cellpadding="0">
  <?php
    // This is contain the template top banner, menu navigation
    require_once 'view/header.php';
    
  ?>
<!--  <tr>
    <td colspan="3"> <?php //require_once 'view/top_menu.php'; ?>	</td>
  </tr>-->
  <tr>
    <td colspan="3" style="border-bottom: 1px solid #CCC;" width="100%" >
	<table border="0" width="99%" cellpadding="0" cellspacing="0">
            <tr><td width="14%" valign="top">
            <?php 
                // left menu 
                require_once 'view/left_menu.php';
                // left side contents after menu code add here...
            ?>
            </td>
            <td valign="top" width="85%" >
            <div id="loading" align="center">Loading...<br /><img src="images/loader.gif" /></div>
            <div id="admin_content" style="display:none">
                    <?php 
                        echo "<div align='right' style='width: 810px'>You Last login at: ".date('M, d Y',strtotime($user->getLastLoginTime()))."</div>";
                        // this is the main content section that loads according to the page called
                        if($page == "dashboard") require_once 'view/dashboard.php';
                        else if($page == "store-stat") require_once 'view/store-stat.php';
                        else if($page == "store-stat-final") require_once 'view/store_stat_final.php';
                        else if($page == "view-city") require_once 'view_city.php';
                        else if($page == "add-place") require_once 'add_place.php';
                        else if($page == "view-place") require_once 'view_place.php';
                        else if($page == "add-admin") require_once 'view/add_admin.php';
                        else if($page == "manage-admin") require_once 'view/manage_admin.php';
                        else if($page == "report") require_once 'view/report.php';
                        else if($page == "missdata") require_once 'view/miss_data.php';
                        else if($page == "add-manual-text") require_once 'view/add_manual_text.php';
                        else if($page == "view-manual-text") require_once 'view/view_manual_text.php';
                        else if($page == "edit-manual-text") require_once 'view/edit_manual_text.php';
                        else if($page == "day-store-stat") require_once 'view/day_store_stat.php';
                        else if($page == "day-cashier-stat") require_once 'view/day_cashier_stat.php';
                        else if($page == "exclude-groups") require_once 'view/exclude_groups.php';
                        else if($page == "change-permission") require_once 'view/change_permission.php';
                        else if($page == "view-permission") require_once 'view/view_permission.php';
                        else if($page == "change-password") require_once 'view/change_password.php';
                        else if($page == "edit-admin") require_once 'view/edit_admin.php';
                        //else if($page == "exclude-groups") require_once 'controller/ajax_exclude_groups.php';

                        else echo "<div style='margin: 25px 50px; padding: 20px;'>NO Page selected...Please select from the menu.</div>";

                    ?>
		</div></td>
		</tr></table>
    </td>
  </tr>
  <tr>
    <td colspan="4"><?php require_once 'view/footer.php'; ?></td>
  </tr>
</table>
</body>
    <?php include 'include/jqplot-js.php'; ?>

</html>
<?php
}else{
?>
<script>
    function Redirect(url){
            location.href = url;
    }
    Redirect("index.php");
</script>
<?php
    /* Make sure that code below does not get executed when we redirect. */
ob_flush();
    exit();
}
?>