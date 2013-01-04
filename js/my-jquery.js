// ******************************************//
// JavaScript Document
/* Author: Shamsul Alam
 * Date: 2012-08-29
 * Jquery Login Function for administrator
 */
//*******************************************//
$(document).ready(function()
{
    $("#loginform").submit(function()
    {
        //remove all the class add the messagebox classes and start fading
        $("#login_error").removeClass().addClass('messagebox').text('Validating....').fadeIn(1000);
        //check the username exists or not from ajax
        if($('#user_login').val() == ""){
                $("#login_error").fadeTo(200,0.1,function() //start fading the messagebox
                { 
                        $(this).html('<strong>ERROR:</strong> The User Name field is empty.').addClass('messageboxerror').fadeTo(900,1);
                });
        }
        else if($('#user_pass').val() == ""){
                $("#login_error").fadeTo(200,0.1,function() //start fading the messagebox
                { 
                        $(this).html('<strong>ERROR:</strong> The Password field is empty.').addClass('messageboxerror').fadeTo(900,1);
                });
        }
        else{ 
            var userName = $('#user_login').val();
            var remember = 0;
            if($('#rememberme').attr('checked')){
                remember = 1;
            }
            else{
                remember = 0;
            }
            //alert(remember);
            //$("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_login.php",{user_name:userName, password:$("#user_pass").val() } ,function(data){
                //   showMessageSE(data,"Admin Successfully Created");
                
                if(data==true) //if correct login detail
                {
                    $("#login_error").fadeTo(200,0.1,function()  //start fading the messagebox
                    { 
                        //add message and change the class of the box and start fading
                        $(this).html('Logging in.....').addClass('messageboxok').fadeTo(900,1, function() { 
                            
                                //redirect to secure page
                                document.location='admin.php?page=dashboard';
                        });

                    });
                }
                
                else {
                    $("#login_error").fadeTo(200,0.1,function() //start fading the messagebox
                    { 
                        //add message and change the class of the box and start fading
                        //$(this).html('<strong>ERROR:</strong> Your user / password is WRONG.').addClass('messageboxerror').fadeTo(900,1);
                        $(this).html(data).addClass('messageboxerror').fadeTo(900,1);
                    });		

                }
            });                
        }
        return false; //not to post the  form physically
    });
    //now call the ajax also focus move from 
    $("#user_login").blur(function()
    {
        if($('#user_login').val() == "") 
            $("#loginform").trigger('submit');
    });
    $("#user_pass").blur(function()
    {
        $("#loginform").trigger('submit');
    });
});