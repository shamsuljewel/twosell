// JavaScript Document
 	
$(document).ready(function(){
	
//    $('.input_control').click(function(){
//           if($('input[name='+ $(this).attr('value')+']').attr('disabled') == true){
//               $('input[name='+ $(this).attr('value')+']').attr('disabled', false);
//			   $('#addArea').attr("disabled", false);
//
//           }else{
//               $('input[name='+ $(this).attr('value')+']').attr('disabled', true);
//			   $('#addArea').attr("disabled", true);
//           }
//    });
//	
//	$('.input_control_updateArea').attr('checked', false);
//	
//	$('.input_control_updateArea').click(function(){
//		
//		if($('input[name='+ $(this).attr('value')+']').attr('disabled') == true){
//			$('input[name='+ $(this).attr('value')+']').attr('disabled', false);
//			$('#updateArea').attr('disabled', false);
//			$areaCode = $('#areaCode').find(":selected").text();
//			$('#areaNameUpdate').val($areaCode); 
//			
//		}
//		else{
//			$('input[name='+ $(this).attr('value')+']').attr('disabled', true);
//			$('#updateArea').attr('disabled', true);	
//		}
//	});
//	$("#areaCode").change(function(){
//		if($('#areaNameUpdate').attr('disabled') == false){
//			
//			$areaCode = $('#areaCode').find(":selected").text();
//			$('#areaNameUpdate').val($areaCode); 	
//		}
//		
//	});
//	$('.input_control_delArea').attr('checked', false);
//	$('.input_control_delArea').click(function(){
//		if($('#deleteArea').attr('disabled') == true){
//			
//			$('#deleteArea').attr('disabled', false);
//			$areaCode = $('#areaCode').find(":selected").val();
//			
//		}
//		else{
//			$('#deleteArea').attr('disabled', true);	
//		}
//	});
//	//$('.input_control_updateArea').attr('checked', false);
//	
//	$('#deleteArea').click(function(){
//		$("#a_status").text('Deleting....').fadeIn(1000);
//		$.post("ajax-update-area.php",{areaCode: $('#areaCode').val(), mode: 1}, function(data){
//			if(data == 0){
//				
//				$('#areaNameReload').load('update_area.php #areaNameReload').fadeIn("slow"); 
//				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Area Name successfully Deleted...</font>").fadeTo(900,1);
//				$('#deleteArea').attr('disabled',true);
//				$('.input_control_updateArea').attr('checked', false);
//				$('#areaNameUpdate').attr('disabled', true);
//				$('#updateArea').attr('disabled', true);
//				
//			}
//			else if(data == 1){
//				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Area Name Can't Delete...</font>").fadeTo(900,1);
//			}
//			else{
//				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Unknown Error Occured...</font>").fadeTo(900,1);	
//			}
//		});
//	});
//	
//	
//	/*** project type start */
//	$('.input_control_ptype').click(function(){
//           if($('input[name='+ $(this).attr('value')+']').attr('disabled') == true){
//               $('input[name='+ $(this).attr('value')+']').attr('disabled', false);
//			   $('#addType').attr("disabled", false);
//
//           }else{
//               $('input[name='+ $(this).attr('value')+']').attr('disabled', true);
//			   $('#addType').attr("disabled", true);
//           }
//        });
	/***********************************************/
        //         Add City
        function showMessageSE(data,message){
            $("#error-box").fadeTo(200,0.1,function() //start fading the messagebox
            {
                //alert(data);
                if(data == 1){
                    $(this).html("<div class='green-success'>"+message+"...</b>").fadeTo(900,1).css('border-color','Green').css('background-color','#00CC99');
                }
                else{
                    $(this).html("<div class='red-error'>"+data+"</div>").fadeTo(900,1).css('border-color','red').css('background-color','pink');
                }
            });
        }
        $('#addCity').click(function(){
		//alert("hi");			   
		$("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
		$.post("ajax_add_city.php",{cityName:$('#cityName').val(), lat:$("#lat").val(), lon:$("#long").val()} ,function(data){
                     showMessageSE(data,"City Successfully Added");
                });
	});
        $('#addPlace').click(function(){
		//alert("hi");			   
		$("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
		$.post("ajax_add_place.php",{cityName:$('#city_id').val(), placeName:$('#placeName').val(), lat:$("#lat").val(), lon:$("#long").val()} ,function(data){
                     showMessageSE(data,"Place Successfully Added");
                });
	 
	});
        $('#city_id').change(function(){
            $("#view-all-place").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("ajax_show_place.php",{cityName:$('#city_id').find(":selected").val()} ,function(data){
                $("#view-all-place").html(data).fadeIn(100);
            });
         });
         $('#addAdmin').click(function(){
		//alert("hi");			   
		$("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
		$.post("ajax_add_admin.php",{adminLevel:$('#admin_level').find(":selected").val(), userName:$('#userName').val(), password:$("#password").val(), firstName:$("#firstName").val(), lastName:$('#lastName').val()} ,function(data){
                     showMessageSE(data,"Admin Successfully Created");
                });
	 
	});
        $('.edit_city').click(function(){
            var rowid = $(this).attr('id');
            //alert(rowid);
            $('#name-'+rowid).hide();
            $('#lat-'+rowid).hide();
            $('#lon-'+rowid).hide();
            $('#div-'+rowid).hide();
            
            $('#editname-'+rowid).show();
            $('#editlat-'+rowid).show();
            $('#editlon-'+rowid).show();
            $('#update-'+rowid).show();
            
            return false;
        });
        $('.updateButton').click(function(){
            var button = $(this).attr('id');
            var id = $('#id-'+button).val();
            var name = $('#textname-'+button).val();
            var lat = $('#textlat-'+button).val();
            var lon = $('#textlon-'+button).val();
            
            //alert(id);
            $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_update_city.php",{id: id, name: name, lat: lat, lon: lon } ,function(data){
                
                showMessageSE(data,"City Successfully Updated");
                
                $('#editname-'+button).hide();
                $('#editlat-'+button).hide();
                $('#editlon-'+button).hide();
                $('#update-'+button).hide();
                
                $('#name-'+button).show();
                $('#lat-'+button).show();
                $('#lon-'+button).show();
                $('#div-'+button).show();
                
                $('#name-'+button).text(name);
                $('#lat-'+button).text(lat);
                $('#lon-'+button).text(lon);
                
            });
            
        });
         
        $('.delete_city').click(function(){
           var answer = confirm("Delete selected City ?")
           if (answer){
                $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
                var link = $(this).attr('id');
                var id = $('#id-'+link).val();
                //alert(id);
                $(this).closest("tr").hide(); // hide the table nearest row of the clicked event
                $.post("controller/ajax_delete_city.php", {id: id}, function(data){
                    showMessageSE(data, "City Successfully Deleted");
                });
           }
           return false;
        });
        
        $("#report").relatedSelects({
		onChangeLoad: 'view/datasupplier.php',
		selects: ['city', 'place'],
		loadingMessage: 'Loading, wait...'
	});
        $('#submit_btn').click(function(){
            var city = $('#city').val(); 
            var place = $('#place').val();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var fromTime = $('#fromTime').val();
            var toTime = $('#toTime').val();
            //alert(city+" "+place+" "+fromDate+" "+toDate+" "+fromTime+" "+toTime);
            $("#search_result").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_search_weather.php", {city: city, place:place, fromDate:fromDate, toDate:toDate, fromTime:fromTime, toTime:toTime }, function(data){
                $("#search_result").html(data).fadeIn(100);
            });
            return false;
        });
        $('#all_data').click(function(){
            if($('#all_data').attr('checked')){
                $('#fromDate').attr('disabled', true);
                $('#toDate').attr('disabled', true);
            }
            else{
                $('#fromDate').attr('disabled', false);
                $('#toDate').attr('disabled', false);
            }
        });
         $('.edit_admin').click(function(){
            var rowid = $(this).attr('id');

            $('#fname-'+rowid).hide();
            $('#lname-'+rowid).hide();
            $('#adminlevel-'+rowid).hide();
            $('#adminactive-'+rowid).hide();
            
            $('#div-'+rowid).hide();
//            
            $('#editfname-'+rowid).show();
            $('#editlname-'+rowid).show();
            $('#editlevel-'+rowid).show();
            $('#editactive-'+rowid).show();
            
            $('#update-'+rowid).show();
            
            return false;
        });
        $('.updateadminButton').click(function(){
            var button = $(this).attr('id');
            var id = $('#id-'+button).val();
            var fname = $('#textfname-'+button).val();
            var lname = $('#textlname-'+button).val();
            var level = $('#seditlevel-'+button).find(":selected").val();
            var leveltext = $('#seditlevel-'+button).find(":selected").text();
            
            var active = $('#seditactive-'+button).find(":selected").val();
            var activetext = $('#seditactive-'+button).find(":selected").text();
//            var leveltext = $("#seditlevel-"+button+" option:selected").text();
            //alert(active);
            $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_update_admin.php",{id: id, fname: fname, lname: lname, level: level, active: active } ,function(data){
                
                showMessageSE(data, "Admin Successfully Updated");
                if(data == 1){
                    $('#editfname-'+button).hide();
                    $('#editlname-'+button).hide();
                    $('#editlevel-'+button).hide();
                    $('#editactive-'+button).hide();

                    $('#update-'+button).hide();

                    $('#fname-'+button).show();
                    $('#lname-'+button).show();
                    $('#adminlevel-'+button).show();
                    $('#adminactive-'+button).show();

                    $('#div-'+button).show();

                    $('#fname-'+button).text(fname);
                    $('#lname-'+button).text(lname);
                    $('#adminlevel-'+button).text(leveltext);
                    $('#adminactive-'+button).text(activetext);
                }
            });
            
        });
        $('.delete_admin').click(function(){
           var answer = confirm("Disabled selected Admin ?")
           if (answer){
                var link = $(this).attr('id');
                var id = $('#id-'+link).val();
              //  alert(id);
                //$(this).closest("tr").hide(); // hide the table nearest row of the clicked event
                $.post("controller/ajax_delete_admin.php", {id: id}, function(data){
                    $("#error-box").html(data).fadeIn(100);
                    $('#adminactive-'+link).text('Disabled');
                });
           }
           return false;
        });
//        $('#submit_missdata').click(function(){
//            //alert("all ok");
//            if($('#all_data').attr('checked') || ($('#fromDate').val() != "" && $('#toDate').val() != "")){
//                var a;
//                if($('#all_data').attr('checked')){ 
//                    a = 1;
//                }
//                else{
//                    a = 0;
//                }
//                var fromDate = $('#fromDate').val();
//                var toDate = $('#toDate').val();
//                $.post("controller/ajax_missdata.php", {a: a, fromDate: fromDate, toDate: toDate},function(data){
//                    $("#miss_result").html(data).fadeIn(100);
//                });
//                return false;
//            }
//            else{
//                alert('please select both date fields');
//            }
//        });
//        $( "#fromDate" ).datepicker({
//                defaultDate: "+1w",
//                changeMonth: true,
//                numberOfMonths: 1,
//                onSelect: function( selectedDate ) {
//                        $( "#toDate" ).datepicker( "option", "minDate", selectedDate );
//                }
//        });
//        $( "#toDate" ).datepicker({
//                defaultDate: "+1w",
//                changeMonth: true,
//                
//                numberOfMonths: 1,
//                onSelect: function( selectedDate ) {
//                        $( "#fromDate" ).datepicker( "option", "maxDate", selectedDate );
//                }
//        });
        
        
        
        
        
        
        
        
        
	$('.input_control_updateType').click(function(){
		
		if($('input[name='+ $(this).attr('value')+']').attr('disabled') == true){
			$('input[name='+ $(this).attr('value')+']').attr('disabled', false);
			$('#updateType').attr('disabled', false);
			$typeCode = $('#typeCode').find(":selected").text();
			$('#typeNameUpdate').val($typeCode); 
			
		}
		else{
			$('input[name='+ $(this).attr('value')+']').attr('disabled', true);
			$('#updateType').attr('disabled', true);	
		}
	});
	$("#typeCode").change(function(){
		if($('#typeNameUpdate').attr('disabled') == false){
			
			$areaCode = $('#typeCode').find(":selected").text();
			$('#typeNameUpdate').val($areaCode); 	
		}
		
	});
	$('#updateType').click(function(){
		$("#a_status").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Checking availability...').fadeIn(1000);
		$.post("ajax-update-type.php",{typeCode:$('#typeCode').val(),typeName: $('#typeNameUpdate').val(), mode: 2, rand:Math.random()} ,function(data){//alert("hi");
								
			if(data==0){
				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Project Type Successfully Updated...</font>").fadeTo(900,1);
				$('#typeCode').find(":selected").text($('#typeNameUpdate').val());
			}
			else if(data == 1){
				$('#a_status').html("<img src='images/not_available.png' align='absmiddle'> <font color='Red'>Update Error Occured...</font>").fadeTo(900,1);
			}
			else{
				$('#a_status').html("<img src='images/not_available.png' align='absmiddle'> <font color='Red'>Unknown Error Occured when updating...</font>").fadeTo(900,1);	
			}
			   
			   
		});
	});
	$('.input_control_delType').attr('checked', false);
	$('.input_control_delType').click(function(){
		if($('#deleteType').attr('disabled') == true){
			
			$('#deleteType').attr('disabled', false);
			$typeCode = $('#typeCode').find(":selected").val();
			
		}
		else{
			$('#deleteType').attr('disabled', true);	
		}
	});
	$('#deleteType').click(function(){
		$("#a_status").text('Deleting....').fadeIn(1000);
		$.post("ajax-update-type.php",{typeCode: $('#typeCode').val(), mode: 1}, function(data){
			if(data == 0){
				
				$('#typeNameReload').load('update_ptype.php #typeNameReload').fadeIn("slow"); 
				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Project Type Successfully Deleted...</font>").fadeTo(900,1);
				$('#deleteType').attr('disabled',true);
				$('.input_control_updateType').attr('checked', false);
				$('#typeNameUpdate').attr('disabled', true);
				$('#updateType').attr('disabled', true);
				
			}
			else if(data == 1){
				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Project Type Can't Delete...</font>").fadeTo(900,1);
			}
			else{
				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Unknown Error Occured...</font>").fadeTo(900,1);	
			}
		});
	});
	/*** project type end */
	/***  room definition start */
	$('.input_control_room').click(function(){
           if($('input[name='+ $(this).attr('value')+']').attr('disabled') == true){
               $('input[name='+ $(this).attr('value')+']').attr('disabled', false);
			   $('#sName').attr("disabled", false);
			   $('#addRoom').attr("disabled", false);
			   

           }else{
               $('input[name='+ $(this).attr('value')+']').attr('disabled', true);
			   $('#addRoom').attr("disabled", true);
			   $('#sName').attr("disabled", true);
           }
    });
	$('#addRoom').click(function(){
		//alert("hi");			   
		var roomName = $("#roomName").val();  // Area name text field value
		var sName = $('#sName').val();
		$("#a_status").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Checking availability...');
		$.ajax({  //Make the Ajax Request
	 		type: "POST",
	 		url: "ajax_insert_room.php",  //file name
	 		data: "roomName="+ roomName + "&sName="+ sName,  //data
	 		success: function(server_response){
 
	 		$("#a_status").ajaxComplete(function(event, request){
	 			var str = server_response.split(" ");
				var a = "";
				for(i = 2; i < str.length; i++){
					//document.write(str[i]); 
					a = a+" "+str[i];
				}
				if(str[0] == '0')//if ajax_check_username.php return value "0"
 	 			{
	 				$("#a_status").html('<img src="images/available.png" align="absmiddle"> <font color="Green"> Room Name Successfully Inserted... </font>  ');
					//$('#areaNameReload').load('update_area.php?areaCode='+str[1]+' #areaNameReload').fadeIn("slow"); 					
					$("#roomCode").append('<option selected="selected" value="'+str[1]+'">'+a+'</option>');
					$('#addRoom').attr("disabled", true);
					//$('#areaCode').remove();
	 				//add this image to the span with id "availability_status"
	 			}
	 			else  if(server_response == '2')//if it returns "1"
	 			{
	 				$("#a_status").html('<img src="images/not_available.png" align="absmiddle"> <font color="red">This Room Name already Exist. </font>');
	 			}
				else{
					$("#a_status").html('<img src="images/not_available.png" align="absmiddle"> <font color="red">Unknow Error Occured. </font>');	
				}
				//$("#a_status").html(server_response);
	 	 	});
	 	   }
	 
	 	});
	 
	});
	$('.input_control_updateRoom').click(function(){
           if($('input[name='+ $(this).attr('value')+']').attr('disabled') == true){
               $('input[name='+ $(this).attr('value')+']').attr('disabled', false);
			   $('#sName').attr("disabled", false);
			   $('#updateRoom').attr("disabled", false);
			   $('.input_control_delRoom').attr("checked", false);
			   $('#deleteRoom').attr("disabled", true);
			   var roomCode = $('#roomCode').find(":selected").text();
			   $('#roomNameUpdate').val(roomCode);
			   //$('#sName').val($('').find(":selected").text());
			   $.post("ajax_update_room.php",{roomCode: $('#roomCode').find(":selected").val(), mode: 1,rand:Math.random()}, function(data){
					if(data == 1){
						$("#a_status").html('<img src="images/not_available.png" align="absmiddle"> <font color="red">Unknow Error Occured. </font>');
					}
					else{
						$('#sName').val(data);	
					}
				});
			   
           }else{
               $('input[name='+ $(this).attr('value')+']').attr('disabled', true);
			   $('#updateRoom').attr("disabled", true);
			   $('#sName').attr("disabled", true);
           }
    });
	$("#roomCode").change(function(){
		if($('#roomNameUpdate').attr('disabled') == false){
			
			$roomCode = $('#roomCode').find(":selected").text();
			$('#roomNameUpdate').val($roomCode);
			$.post("ajax_update_room.php",{roomCode: $('#roomCode').find(":selected").val(), mode: 1,rand:Math.random()}, function(data){
					if(data == 1){
						$("#a_status").html('<img src="images/not_available.png" align="absmiddle"> <font color="red">Unknow Error Occured. </font>');
					}
					else{
						$('#sName').val(data);	
					}
				});
		}
		
	});
	$('#updateRoom').click(function(){
		$("#a_status").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Checking availability...').fadeIn(1000);
		$.post("ajax_update_room.php",{roomCode:$('#roomCode').val(),roomName: $('#roomNameUpdate').val(), sName: $('#sName').val(), mode: 2, rand:Math.random()} ,function(data){//alert("hi");
								
			if(data==0){
				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Room Name Successfully Updated...</font>").fadeTo(900,1);
				$('#roomCode').find(":selected").text($('#roomNameUpdate').val());
			}
			else if(data == 1){
				$('#a_status').html("<img src='images/not_available.png' align='absmiddle'> <font color='Red'>Update Error Occured...</font>").fadeTo(900,1);
			}
			else{
				$('#a_status').html("<img src='images/not_available.png' align='absmiddle'> <font color='Red'>Unknown Error Occured when updating...</font>").fadeTo(900,1);	
			}
			//$("#a_status").html(data);  
		});
		
	});
	$('.input_control_delRoom').attr('checked', false);
	$('.input_control_delRoom').click(function(){
		if($('#deleteRoom').attr('disabled') == true){
			
			$('#deleteRoom').attr('disabled', false);
			$roomCode = $('#roomCode').find(":selected").val();
			$('.input_control_updateRoom').attr('checked', false);
			$('#roomNameUpdate').attr('disabled', true);
			$('#sName').attr('disabled', true);
			$('#updateRoom').attr('disabled', true);
		}
		else{
			$('#deleteRoom').attr('disabled', true);	
		}
		
	});
	$('#deleteRoom').click(function(){
		$("#a_status").text('Deleting....').fadeIn(1000);
		$.post("ajax_update_room.php",{roomCode: $('#roomCode').val(), mode: 3}, function(data){
			if(data == 0){
				
				$('#roomNameReload').load('update_room_def.php #roomNameReload').fadeIn("slow"); 
				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Room Name Successfully Deleted...</font>").fadeTo(900,1);
				$('#deleteRoom').attr('disabled',true);
				$('.input_control_updateRoom').attr('checked', false);
				$('#roomNameUpdate').attr('disabled', true);
				$('#updateRoom').attr('disabled', true);
				
			}
			else if(data == 1){
				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Room Name Can't Delete...</font>").fadeTo(900,1);
			}
			else{
				$('#a_status').html("<img src='images/available.png' align='absmiddle'> <font color='Green'>Unknown Error Occured...</font>").fadeTo(900,1);	
			}
		});
		
	});
	/*   room definition end **/
	$('.input_control2').attr('checked', false);
       $('.input_control2').click(function(){
           if($('input[name='+ $(this).attr('value')+']').attr('disabled') == true){
               $('input[name='+ $(this).attr('value')+']').attr('disabled', false);
			   $('#addproType').attr("disabled", false);

           }else{
               $('input[name='+ $(this).attr('value')+']').attr('disabled', true);
			   $('#addproType').attr("disabled", true);
           }
    });
	   $('#addproType').click(function(){
		//alert("hi");			   
		var proTypetext = $("#proTypetext").val();  // Area name text field value
		$("#availability_status2").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Checking availability...');
		$.ajax({  //Make the Ajax Request
	 		type: "POST",
	 		url: "ajax_insert_proType.php",  //file name
	 		data: "proTypetext="+ proTypetext,  //data
	 		success: function(server_response){
 
	 		$("#availability_status2").ajaxComplete(function(event, request){
	 			var str = server_response.split(" ");
				var a = "";
				for(i = 2; i < str.length; i++){
					//document.write(str[i]); 
					a = a+" "+str[i];
				}
				if(str[0] == '0')//if ajax_check_username.php return value "0"
 	 			{
	 				$("#availability_status2").html('<img src="images/available.png" align="absmiddle"> <font color="Green"> Area Name Inserted </font>  ');
										
					$("#proType").append('<option selected="selected" value="'+str[1]+'">'+a+'</option>');
	 				//add this image to the span with id "availability_status"
	 			}
	 			else  if(server_response == '2')//if it returns "1"
	 			{
	 				$("#availability_status2").html('<img src="images/not_available.png" align="absmiddle"> <font color="red">This Area Name already Exist. </font>');
	 			}
				else{
					$("#availability_status2").html('<img src="images/not_available.png" align="absmiddle"> <font color="red">Unknow Error Occured. </font>');	
				}
				//$("#availability_status2").html(server_response);
	 	 	});
	 	   }
	 
	 	});
	 	
	});
	$('.input_control_head').attr('checked', false);
       $('.input_control_head').click(function(){
           if($('input[name='+ $(this).attr('value')+']').attr('disabled') == true){
               $('input[name='+ $(this).attr('value')+']').attr('disabled', false);
			   $('#addHead').attr("disabled", false);

           }else{
               $('input[name='+ $(this).attr('value')+']').attr('disabled', true);
			   $('#addHead').attr("disabled", true);
           }
    });
	   $('#addHead').click(function(){
		//alert("hi");			   
		
		var headNameText = $("#headNameText").val();  // Area name text field value
		//headNameText.replace(/([ #;&,.+*~\':"!^$[\]()=>|\/])/g,'\\$1') 
		headNameText=headNameText.replace("\&","1000000000");
		/*headNameText=headNameText.replace("\<","&lt;");
		headNameText=headNameText.replace("\>","&gt;");
		headNameText=headNameText.replace("\"","&quot;");*/

		
		
		//alert(headNameText);
		$("#availability_status_head").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Checking availability...');
		$.ajax({  //Make the Ajax Request
	 		type: "POST",
	 		url: "ajax_insert_head.php",  //file name
	 		data: "headNameText="+ headNameText,  //data
	 		
			success: function(server_response){
 			
	 		$("#availability_status_head").ajaxComplete(function(event, request){
	 			var str = server_response.split(" ");
				var a = "";
				for(i = 2; i < str.length; i++){
					//document.write(str[i]); 
					a = a+" "+str[i];
				}
				//alert(str);
				//alert(a);
				if(str[0] == '0')//if ajax_check_username.php return value "0"
 	 			{
	 				$("#availability_status_head").html('<img src="images/available.png" align="absmiddle"> <font color="Green"> Heading Name Inserted </font>  ');
										
					$("#headName").append('<option selected="selected" value="'+str[1]+'">'+a+'</option>');
					//$('#addHead').removeAttr('disabled');
					$('#addHead').attr("disabled", true);

	 				//add this image to the span with id "availability_status"
	 			}
	 			else  if(server_response == '2')//if it returns "1"
	 			{
	 				$("#availability_status_head").html('<img src="images/not_available.png" align="absmiddle"> <font color="red">This Area Name already Exist. </font>');
	 			}
				else{
					$("#availability_status_head").html('<img src="images/not_available.png" align="absmiddle"> <font color="red">Unknow Error Occured. </font>');	
				}
				//$("#availability_status_head").html(server_response);
	 	 	});
	 	   }
	 
	 	});
	 	
	});

	/**************** Add Project Images START***********************/
    $('#formProjectAddImage').submit(function() { 
		
		var options = {target: '#projectimage-submit-error'}; 
		$('#formProjectAddImage').ajaxSubmit(options);
		$("#project-submit-error").text('Checking....').fadeIn(1000);
        return false; 
		
    });
	/*************      ADD PROJECT IMAGES END     *************************/
	/**************** Edit Project Images START***********************/
    $('#formProjectEditImage').submit(function() { 
		$('#setFocus').focus();
		var pid = $('#pid').val();
		$("#projectimage-submit-error").text('Updating....').fadeIn(1000);
		var options = { 
			target: '#projectimage-submit-error',
			success: function(data) { 
        		//if(data == "yes")	alert("Hi");
				$("#projectimage-submit-error").html(data).fadeTo(900,1);
				$('#show-project-images').load('show-inproject-images.php?id='+pid).fadeIn("slow");
			} 	
		}; 
		$('#formProjectEditImage').ajaxSubmit(options);
		return false; 
		
    });
	$("#formProjectAdd").submit(function()
	{
		$('#setFocus').focus();
		var editor1 = changeBack();
		//alert($('#areaCode').val());//alert(editor1);
		$("#project-submit-error").text('Checking....').fadeIn(1000);
		if($('#projectCode').val() == ""){
			$("#project-submit-error").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
				$(this).html('<strong>ERROR:</strong> Project Code is Empty.').fadeTo(900,1);
			});
		}
		else if($('#projectName').val() == ""){
			$("#project-submit-error").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
				$(this).html('<strong>ERROR:</strong> Project Name is Empty.').fadeTo(900,1);
			});
			
		}
		else if($('#projectAddress').val() == ""){
			$("#project-submit-error").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
				$(this).html('<strong>ERROR:</strong> Project Address is Empty.').fadeTo(900,1);
			});
			
		}
		else if(editor1 == ""){
			
			$("#project-submit-error").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
				$(this).html('<strong>ERROR:</strong> Feature &amp; Amenities is Empty.').fadeTo(900,1);
			});
		}
		else{ 
			$.post("save_project.php",{projectCode:$('#projectCode').val(),editor1:editor1,rand:Math.random(), projectName:$('#projectName').val(),projectAddress:$('#projectAddress').val(),priceFrom:$('#priceFrom').val(),priceTo:$('#priceTo').val(),typ:$('#typ').val(),projectDetails:$('#projectDetails').val(),areaCode:$('#areaCode').val(), faceName:$('#faceName').val(),proType:$('#proType').val(),handOver:$('#datepicker').val(),storey:$('#storey').val(),unitNo:$('#unitNo').val(),parking:$('#parking').val(),no_of_lift:$('#no_of_lift').val(),landArea:$('#landArea').val(),apSize:$('#apSize').val(), publish: $('#publish').val()} ,function(data){
			var str = data.split(" ");
			var and = "&";
			if(str[0] == 'yes'){
				$("#project-submit-error").fadeTo(200,0.1,function()  //start fading the messagebox
				{ 
				  //add message and change the class of the box and start fading
				  $(this).html('<b>SUCCESS:</b> Project Successfully Inserted.....').addClass('messageboxok').fadeTo(900,1,
				  function() { 
			  	 		//redirect to secure page
				 		document.location='admin.php?page=add-project-image'+and+'id='+str[1];
			  	  });
				});
			}
			else if(data == 'no'){
				$("#project-submit-error").fadeTo(200,0.1,function() //start fading the messagebox
				{ 
			  		//add message and change the class of the box and start fading
			  		$(this).html('<strong>ERROR:</strong> Insert Error Occures...').addClass('messageboxerror').fadeTo(900,1);
				});
			}
			else{
				$("#project-submit-error").fadeTo(200,0.1,function() //start fading the messagebox
				{ 
			  		//add message and change the class of the box and start fading
			  		$(this).html('<strong>ERROR:</strong> Unknown Error Occures...').addClass('messageboxerror').fadeTo(900,1);
				});	
			}
			//$("#news-submit-error").fadeTo(200,0.1,function() //start fading the messagebox
			//$('#project-submit-error').html(data);
			});
		}
		return false; //not to post the  form physically
		
	});
	//onchangeblur(projectCode,formProjectAdd);
	$("#projectCode").blur(function()
	{
		if($('#projectCode').val() == "") $("#formProjectAdd").trigger('submit');
	});
	$("#projectName").blur(function()
	{
		if($('#projectName').val() == "") $("#formProjectAdd").trigger('submit');
	});
	$("#projectAddress").blur(function()
	{
		if($('#projectAddress').val() == "") $("#formProjectAdd").trigger('submit');
	});
	
        //onchangeblur(projectCode,formProjectAdd);
	$("#projectCode").blur(function()
	{
		if($('#projectCode').val() == "") $("#formProjectEdit").trigger('submit');
	});
	$("#projectName").blur(function()
	{
		if($('#projectName').val() == "") $("#formProjectEdit").trigger('submit');
	});
	$("#projectAddress").blur(function()
	{
		if($('#projectAddress').val() == "") $("#formProjectEdit").trigger('submit');
	});
	/***********   ADD Flat Start            **************/
	$('#formFlatAdd').submit(function() { 
		
		//alert("hi");
		$('#setFocus').focus();
		//var pid = $('#pid').val();
		$("#flat-submit-error").text('Updating....').fadeIn(1000);
		var options = { 
			target: '#flat-submit-error',
			url: 'ajax-insert-flat.php',
			type: 'POST',
			success: function(data) { 
        		//if(data == "yes")	alert("Hi");
				$("#flat-submit-error").html(data).fadeTo(900,1);
				//$('#show-project-images').load('show-inproject-images.php?id='+pid).fadeIn("slow");
			} 	
		}; 
		$('#formFlatAdd').ajaxSubmit(options);
		return false; 
		
    });
	$('#pId').unbind().change(function(){
		pId = $('#pId').find(":selected").val();
		//alert(pId);
		$('#select-pro-image').load('select-pro-image.php?id='+pId, 
			function(response, status, xhr) {
			  if (status == "error") {
				var msg = "Sorry but there was an error: ";
				$("#select-pro-image").html(msg + xhr.status + " " + xhr.statusText);
			  }
			  else{
				//alert("success");
				/*var url = 'id='+pName+'&flatType='+flatType;
				$('#show-flat-images').load('show-inflat-images.php?'+url);//.fadeIn("slow");
				//event.stopImmediatePropagation();
				//alert(flatType);	  */
			  }
				  
		});
	});
	/************  ADD Flat END          ******************/
	/************  Flat image Start  ***********************/
	$("#pName1").unbind().change(function(){
		

		//alert($('#pName').find(":selected").val());
		//if($('#pName').find(":selected").val() != 0){
			pName = $('#pName1').find(":selected").val();
			if($('#flatName').attr('disabled') == true){
				$('#flatName').attr('disabled', false);
			}
			/*$.post("ajax_flat_type.php",{ pName: $('#pName').find(":selected").val(), mode: 1,rand:Math.random() }, function(data){
					
				
			});*/
			
			//alert(pName);
			
			//alert($('#flatName').find(":selected").val());
			//flatType = null;
			$('#load_flatType').load('ajax-flat-type.php?id='+pName, 
				function(response, status, xhr) {
				  if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#flatimage-submit-error").html(msg + xhr.status + " " + xhr.statusText);
				  }
				  else{
					flatType = $('#flatName').find(":selected").val();
					var url = 'id='+pName+'&flatType='+flatType;
					$('#show-flat-images').load('show-inflat-images.php?'+url);//.fadeIn("slow");
					//event.stopImmediatePropagation();
					//alert(flatType);	  
				  }
				  
				}
				
			);
			
		
	});
	
	
	
	$('#flatName').unbind().change(function(){
		//alert("hi");						   	
		pName = $('#pName1').find(":selected").val();
		//alert(pName);
		flatType = $('#flatName').find(":selected").val();
		var url = 'id='+pName+'&flatType='+flatType;
		//alert(url);
		$('#show-flat-images').load('show-inflat-images.php?'+url).fadeIn("slow");
		/*$('#load_flatType').load('ajax-flat-type.php?id='+pName, 
				function(response, status, xhr) {
				  if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#flatimage-submit-error").html(msg + xhr.status + " " + xhr.statusText);
				  }
				  else{
					flatType = $('#flatName').find(":selected").val();
					var url = 'id='+pName+'&flatType='+flatType;
					$('#show-flat-images').load('show-inflat-images.php?'+url).fadeIn("slow");
					//alert(flatType);	  
				  }
				}
			);*/
		
	});
	$('#formFlatAddImage').unbind().submit(function() { 
		$('#setFocus').focus();
		var pid = $('#pName1').val();
		var flatType = $('#flatName').find(":selected").text();
		//alert(pid+flat_type); 
		$("#flatimage-submit-error").html('Updating....').fadeIn(1000);
		var options = { 
			target: '#flatimage-submit-error',
			success: function(data) { 
        		alert("success");
				//if(data == "yes")	alert("Hi");
				$("#flatimage-submit-error").html(data);//.fadeTo(900,1);
				var url = 'id='+pid+'&flatType='+flatType;
				$('#show-flat-images').load('show-inflat-images.php?'+url).fadeIn("slow");
			} 	
		}; 
		$('#formFlatAddImage').ajaxSubmit(options);
		
		return false; 
		
    });
	/************* Flat Image End  ************/
	/*************    UPDATE FLAT START             **********/
	$("#pName2").unbind().change(function(){
		

		//alert($('#pName').find(":selected").val());
		//if($('#pName').find(":selected").val() != 0){
			pName = $('#pName2').find(":selected").val();
			if($('#flatName2').attr('disabled') == true){
				$('#flatName2').attr('disabled', false);
			}
			/*$.post("ajax_flat_type.php",{ pName: $('#pName').find(":selected").val(), mode: 1,rand:Math.random() }, function(data){
					
				
			});*/
			
		//	alert(pName);
			
			//alert($('#flatName').find(":selected").val());
			//flatType = null;
			$('#load_flatType').load('ajax-flat-type2.php?id='+pName, 
				function(response, status, xhr) {
				  if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#flatimage-submit-error").html(msg + xhr.status + " " + xhr.statusText);
				  }
				  else{
					flatType = $('#flatName2').find(":selected").val();
					var url = 'id='+pName+'&flatType='+flatType;
					$('#show-flat').load('show-flat-update.php?'+url);//.fadeIn("slow");
					//event.stopImmediatePropagation();
					//alert(flatType);	  
				  }
				  
				}
				
			);
			
		
	});
	
	
	
	$('#flatName2').unbind().change(function(){
		//alert("hi");						   	
		pName = $('#pName2').find(":selected").val();
		//alert(pName);
		flatType = $('#flatName2').find(":selected").val();
		var url = 'id='+pName+'&flatType='+flatType;
		//alert(url);
		$('#show-flat').load('show-flat-update.php?'+url).fadeIn("slow");
		/*$('#load_flatType').load('ajax-flat-type.php?id='+pName, 
				function(response, status, xhr) {
				  if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#flatimage-submit-error").html(msg + xhr.status + " " + xhr.statusText);
				  }
				  else{
					flatType = $('#flatName').find(":selected").val();
					var url = 'id='+pName+'&flatType='+flatType;
					$('#show-flat-images').load('show-inflat-images.php?'+url).fadeIn("slow");
					//alert(flatType);	  
				  }
				}
			);*/
		
	});
	$('#formFlatUpdate').submit(function(){
		$('#setFocus').focus();
		//var pid = $('#pid').val();
		//alert("hi");
		$("#flatupdate-submit-error").html('Updating....').fadeIn(1000);
		var options = { 
			target: '#flatupdate-submit-error',
			url: 'ajax-update-flat.php',
			type: 'POST',
			success: function(data) { 
        		//if(data == "yes")	alert("Hi");
				$("#flatupdate-submit-error").html(data).fadeTo(900,1);
				//$('#show-project-images').load('show-inproject-images.php?id='+pid).fadeIn("slow");
			} 	
		}; 
		$('#formFlatUpdate').ajaxSubmit(options);
		return false; 							
	});
	/*************    UPDATE FLAT END             **********/
	
	/****************Individual Flat Entry START *******************/
	$(".pName1").unbind().change(function(){
		

		//alert($('#pName').find(":selected").val());
		//if($('#pName').find(":selected").val() != 0){
			pName = $('.pName1').find(":selected").val();
			if($('.flatName').attr('disabled') == true){
				$('.flatName').attr('disabled', false);
			}
			/*$.post("ajax_flat_type.php",{ pName: $('#pName').find(":selected").val(), mode: 1,rand:Math.random() }, function(data){
					
				
			});*/
			
			//alert(pName);
			
			//alert($('#flatName').find(":selected").val());
			//flatType = null;
			$('#load_flatType').load('ajax-flat-type3.php?id='+pName, 
				function(response, status, xhr) {
				  if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#flatimage-submit-error").html(msg + xhr.status + " " + xhr.statusText);
				  }
				  else{
					flatType = $('#flatName').find(":selected").val();
					var url = 'id='+pName+'&flatType='+flatType;
					$('#show-inv-flat').load('flat-inv-list.php?'+url);//.fadeIn("slow");
					//event.stopImmediatePropagation();
					//alert(flatType);	  
				  }
				  
				}
				
			);
			
		
	});
	$('.flatName').unbind().change(function(){
		//alert("hi");						   	
		pName = $('.pName1').find(":selected").val();
		//alert(pName);
		flatType = $('.flatName').find(":selected").val();
		var url = 'id='+pName+'&flatType='+flatType;
		//alert(url);
		$('#show-flat-images').load('show-inflat-images.php?'+url).fadeIn("slow");
		/*$('#load_flatType').load('ajax-flat-type.php?id='+pName, 
				function(response, status, xhr) {
				  if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#flatimage-submit-error").html(msg + xhr.status + " " + xhr.statusText);
				  }
				  else{
					flatType = $('#flatName').find(":selected").val();
					var url = 'id='+pName+'&flatType='+flatType;
					$('#show-flat-images').load('show-inflat-images.php?'+url).fadeIn("slow");
					//alert(flatType);	  
				  }
				}
			);*/
		
	});
	$('#formInFlatAdd').unbind().submit(function(){
		//$('#setFocus').focus();
		var pid = $('.pName1').val();
		//alert("hi");
		$("#flat-submit-error").html('Updating....').fadeIn(1000);
		var options = { 
			target: '#flat-submit-error',
			url: 'process-inv-flat.php',
			type: 'POST',
			success: function(data) { 
        		//if(data == "yes")	alert("Hi");
				$("#flat-submit-error").html(data).fadeTo(900,1);
				$('#show-inv-flat').load('flat-inv-list.php?id='+pid).fadeIn("slow");
			} 	
		}; 
		$('#formInFlatAdd').ajaxSubmit(options);
		return false; 		
	});
	/******************Individual Flat Entry END****************/
	/******************  Video ADD start *************************/
	$('#formVideoAdd').submit(function(){
			//var p_id = $('.pName1').val();
			//alert(p_id);
			$('#video-submit-error').html('Submitting...').fadeIn(1000);
			var options = { 
				target: '#video-submit-error',
				url: 'process-video.php',
				type: 'POST',
				success: function(data) { 
					//alert("ok");
					//if(data == "yes")	alert("Hi");
					$("#video-submit-error").html(data);
					//$('#show-inv-flat').load('flat-inv-list.php?id='+pid).fadeIn("slow");
				} 	
			}; 
			$('#formVideoAdd').ajaxSubmit(options);
			
			return false;
		
	});
	$('#formVideoEdit').submit(function(){
			//var p_id = $('.pName1').val();
			//alert(p_id);
			$('#video-submit-error').html('Updating...').fadeIn(1000);
			var options = { 
				target: '#video-submit-error',
				url: 'process-video-update.php',
				type: 'POST',
				success: function(data) { 
					//alert("ok");
					//if(data == "yes")	alert("Hi");
					$("#video-submit-error").html(data);
					//$('#show-inv-flat').load('flat-inv-list.php?id='+pid).fadeIn("slow");
				} 	
			}; 
			$('#formVideoEdit').ajaxSubmit(options);
			
			return false;
		
	});
	/******************** Video Add END  *************************/
	/******************* UPDATE INV FLAT START   ******************/
	$(".pName2").unbind().change(function(){
		

		//alert($('#pName').find(":selected").val());
		//if($('#pName').find(":selected").val() != 0){
			pName = $('.pName2').find(":selected").val();
			if($('.flatName').attr('disabled') == true){
				$('.flatName').attr('disabled', false);
			}
			/*$.post("ajax_flat_type.php",{ pName: $('#pName').find(":selected").val(), mode: 1,rand:Math.random() }, function(data){
					
				
			});*/
			
			//alert(pName);
			
			//alert($('#flatName').find(":selected").val());
			//flatType = null;
			$('#load_flatType').load('ajax-flat-type3.php?id='+pName, 
				function(response, status, xhr) {
				  if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#flatimage-submit-error").html(msg + xhr.status + " " + xhr.statusText);
				  }
				  else{
					flatType = $('#flatName').find(":selected").val();
					var url = 'id='+pName+'&flatType='+flatType;
					$('#show-inv-flat').load('flat-inv-list-update.php?'+url);//.fadeIn("slow");
					//event.stopImmediatePropagation();
					//alert(flatType);	  
				  }
				  
				}
				
			);
			
		
	});
	$('#formInFlatUpdate').unbind().submit(function(){
		//$('#setFocus').focus();
		var pid = $('.pName2').val();
		//alert("hi");
		$("#flat-submit-error").html('Updating....').fadeIn(1000);
		var options = { 
			target: '#flat-submit-error',
			url: 'process-inv-flat-update.php',
			type: 'POST',
			success: function(data) { 
        		//alert("success");
				$("#flat-submit-error").html(data).fadeTo(900,1);
				$('#show-inv-flat').load('flat-inv-list-update.php?id='+pid).fadeIn("slow");
			} 	
		}; 
		$('#formInFlatUpdate').ajaxSubmit(options);
		return false; 		
	});
	
	/******************** UPDATE INV FLAT END ********************/
	/*************************  PAGES START ************************/
	$('#page_name').unbind().change(function(){
		pName = $('#page_name').find(":selected").val();
		var url = 'id='+pName;
		alert(url);
		$('#text_area').load('page-contents.php?'+url).fadeIn("slow");
	});
	$('#formPage').unbind().submit(function(){
		
		$("#page-submit-error").html('Updating....').fadeIn(1000);
		var editor1 = changeBack();
		
		if(editor1 == ""){
			$("#page-submit-error").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
				$(this).html('<strong>ERROR:</strong> News Details is Empty.').fadeTo(900,1);
			});
		}
		else{ 
			$.post("process_page.php",{editor1:editor1, nid: $('#nid').val()} ,function(data){
				$('#page-submit-error').html(data).fadeTo(900,1);
			});
		}
		return false; 	
	});
	/***************** END PAGES  ************************/
	$('#loading').hide();
	$('#admin_content').show().fadeIn(1000);
	

});