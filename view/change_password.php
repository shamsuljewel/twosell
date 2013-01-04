<script type="text/javascript">
$(document).ready(function(){
    $('#curr_password').focus();
    
    $('#smt_change_password').click(function(){
        $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
        var errorMsg = '';
        var prb = 0;
        var currPass = $('#curr_password').val();
        var newPassword = $('#new_password').val();
        var confirmPassword = $('#confirm_new_password').val();
        if(currPass.length == 0 || newPassword.length == 0 || confirmPassword.length == 0){
            errorMsg = "Please fill all the required firlds";
            prb = 1;
        }
        if(prb == 1){
            showMessageSE(errorMsg, "");
        }else{
            $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_change_password.php",{currPassword: currPass, newPassword: newPassword, confirmPassword: confirmPassword } ,function(data){
                var successMsg = "Password Successfully Changed...";
                
//                    if(data == 1){
//                       $('#error-box').html("<div class='green-success'>"+successMsg+"...</b>").fadeTo(900,1).css('border-color','Green').css('background-color','#00CC99');
//                    }
//                    else{
//                        $('#error-box').html("<div class='red-error'>"+data+"</div>").fadeTo(900,1).css('border-color','red').css('background-color','pink');
//                    }
                showMessageSE(data, successMsg);
            });
        }
        //alert(errorMsg);
        return false;
    });
    function showMessageSE(data,message){
            if(data == '1'){
                $('#error-box').html("<div class='green-success'>"+message+"...</b>").fadeTo(900,1).css('border-color','Green').css('background-color','#00CC99');
            }
            else{
                $('#error-box').html("<div class='red-error'>"+data+"</div>").fadeTo(900,1).css('border-color','red').css('background-color','pink');
            }
    }
});
</script>
<?php
/*
 * This page is form to change the password for the current user, which
 * will required the current password and new password, confirm password
 */
echo "<h1>Change Password</h1>";
echo "<div id='error-box' class='error-valid' style=' display:none' >error</div> <!-- Error Message Here -->";
echo "<form autocomplete='off' name='change-permission-form' id='change-permission-form' method='post' action=''>";
echo "<table border='0' class='form_table'>";
    echo "<tr><td>Current Password *</td>";
    echo "<td><input type='password' name='curr_password' id='curr_password' /></td></tr>";
    
    echo "<tr><td>New Password *</td>";
    echo "<td><input type='password' name='new_password' id='new_password' /></td></tr>";
    
    echo "<tr><td>Confirm New Password *</td>";
    echo "<td><input type='password' name='confirm_new_password' id='confirm_new_password' /></td></tr>";
    
    echo "<tr><td colspan='2' align='center'><input type='submit' name='smt_change_password' id='smt_change_password' value='Change Password' class='submit_btn' style= 'width: 170px' /></td></tr>";
    
echo "</form>";
?>
