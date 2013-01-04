<!--
    two rows which is belongs to main table structure 
    
-->

<tr>
    <td width="19%" colspan="4" background="images/top-bg-logo.jpg" height="70" style="color:green; padding:5px 20px;">
        <img src="images/logo.png" width="290" height="60" align="left" />
            <div style='font-size:24px; margin-top:20px; margin-left:350px'><strong><?php //echo $adminTitle_adminText; ?></strong></div>
            <div style="float:right"></div>
    </td>
</tr>
<tr>
    <td align="left" id="top-navigation" width="100%" style="border: 0 solid blue">
        <table style="width: 1000px" cellpadding="0" cellspacing="0" border="0">
            <tr><td id="header-left">
        <a href="admin.php?page=dashboard" > Home &rArr; </a>    
        
        </td><td id="header-right">
                   
        Welcome: <?php echo "<b>". $user->getFirstName()." ".$user->getLastName()."</b>"; ?>
        <?php echo "[".$user->getGroupName()."][ <a href='admin.php?logout' style='color: #8aa7e9'><b>Sign out</b></a> ]"; ?>
                           
        </td></tr></table>
    </td>
</tr>
