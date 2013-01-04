<?php 
    include 'admin_config.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
	<title><?php echo $admin_title ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript" src="js/jquery-1.7.1.js"></script>
	<script type="text/javascript" src="js/my-jquery.js"></script>
	<link rel="stylesheet" id="login-css" href="css/login.css" type="text/css" media="all" />
	<meta name="robots" content="noindex,nofollow" />
	<link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico" />
</head>
<body class="login">
<div id="login">
	<h1 align="center"><a href="http://localhost/twosell" title="Powered by Pocada">Twosell Logo</a></h1>
	<div id="login_error" style="display:none"></div> <!-- error message shows here -->
	<form name="loginform" id="loginform" action="" method="post">
		<p>
			<label>Username<br />
			<input name="user_login" id="user_login" class="input" size="20" tabindex="1" type="text" /></label>
		</p>
		<p>
			<label>Password<br />
			<input name="user_pass" id="user_pass" class="input" value="" size="20" tabindex="2" type="password" /></label>
		</p>
		<p class="forgetmenot">
			<label><input name="rememberme" id="rememberme" value="forever" tabindex="90" checked="checked" type="checkbox" /> Remember Me</label></p>
		<p class="submit">
			<input name="submit" id="submit" class="button-primary" value="Log In" tabindex="3" type="submit" />
			<input name="redirect_to" value="http://localhost/twoselll/" type="hidden" />
			<input name="testcookie" value="1" type="hidden" />
		</p>
	</form>

	<p id="nav">
		<a href="http://localhost/twosell/index.php?action=lostpassword" title="Password Lost and Found">Lost your password?</a>
	</p>
</div>
<p id="backtoblog"></p>

<script type="text/javascript">
function wp_attempt_focus(){
setTimeout( function(){ try{
d = document.getElementById('user_login');
d.value = '';
d.focus();
} catch(e){}
}, 200);
}

wp_attempt_focus();
if(typeof wpOnload=='function')wpOnload();
</script>
</body></html>