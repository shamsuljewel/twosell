// JavaScript Document
 	
$(document).ready(function(){
	function Redirect(url){
            location.href = url;
        }
        function showMessageSE(data,message){
//            not works in ie 7 and 8
//            $("#error-box").fadeTo(200,0.1,function() //start fading the messagebox
//            {
                //alert(data);
                if(data == 1){
                    $('#error-box').html("<div class='green-success'>"+message+"...</b>").fadeTo(900,1).css('border-color','Green').css('background-color','#00CC99');
                }
                else{
                    $('#error-box').html("<div class='red-error'>"+data+"</div>").fadeTo(900,1).css('border-color','red').css('background-color','pink');
                }
//            });
        }
        $('#EditsetPassword').click(function(){
            if($('#EditsetPassword').val() == 'Set Password'){
                $('#password').attr("disabled", false);
                $('#EditsetPassword').attr('value', 'Disable');
            }else{
                $('#password').attr("disabled", true);
                $('#EditsetPassword').attr('value', 'Set Password');
            }
        });
        $('#generatePassword').click(function(){
            $("#showPassword").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Generating Password...').fadeIn(100);
            $.post("controller/ajax_generate_password.php",{ } ,function(data){
                $('#showPassword').html("<div>"+data+"</div>").fadeTo(900,1).css('border-color','black');
            });
        });
        
        $('#addAdmin').click(function(){
//		alert("hi");			   
                var prb = 0; 
                var adminType = $('#admin_type').find(":selected").val();
                var adminGroup = $('#admin_type').find(":selected").text();
                var userName = $('#userName').val();
                var password = $('#password').val();
                var firstName = $('#firstName').val();
                var lastName = $('#lastName').val();
                var errorMsg = '';
                
                var storeList = new Array();
                $('#store_asign input:checked').each(function() {
                    //selected.push($(this).attr('name'));
                    storeList.push($(this).val());
                });
                
                if(adminType=='null'){
                    errorMsg = "Select Admin Type";
                    prb = 1;
                }else if(userName.length==0 || password.length==0 || firstName.length == 0){
                    errorMsg = "Please fill all the required firlds";
                    prb = 1;
                }else if(adminGroup=='store' && storeList.length == 0){
                    errorMsg = "Please select a store!";
                }
                $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
                if(prb == 1){
                    showMessageSE(errorMsg, "anything not matter");
                }else{
                    $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
                    $.post("controller/ajax_add_admin.php",{adminType: adminType, userName: userName, password: password, firstName: firstName, lastName: lastName, storeList: storeList,adminGroup: adminGroup } ,function(data){
                        showMessageSE(data,"Admin Successfully Created");
                    });
                }
	});
        $('#editAdmin').click(function(){
//		alert("hi");			   
                var prb = 0; 
                var adminType = $('#admin_type').find(":selected").val();
                var adminGroup = $('#admin_type').find(":selected").text();
                var userName = $('#userName').val();
                var pass = 0; 
                if($('#password').attr('disabled') == 'disabled'){
                    pass = 0;
                }else{
                    pass = 1;
                }
//                alert(pass);
                var password = $('#password').val();
                
                var firstName = $('#firstName').val();
                var lastName = $('#lastName').val();
                var errorMsg = '';
                
                var storeList = new Array();
                $('#store_asign input:checked').each(function() {
                    //selected.push($(this).attr('name'));
                    storeList.push($(this).val());
                });
                
                if(adminType=='null'){
                    errorMsg = "Select Admin Type";
                    prb = 1;
                }else if(userName.length==0 || firstName.length == 0){
                    errorMsg = "Please fill all the required firlds";
                    prb = 1;
                }else if(adminGroup=='store' && storeList.length == 0){
                    errorMsg = "Please select a store!";
                }
                $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
                if(prb == 1){
                    showMessageSE(errorMsg, "anything not matter");
                }else{
                    $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
                    $.post("controller/ajax_edit_admin.php",{adminType: adminType, userName: userName, password: password, firstName: firstName, lastName: lastName, storeList: storeList,adminGroup: adminGroup, changePass: pass } ,function(data){
                        showMessageSE(data,"Admin Successfully Updated");
                    });
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
            $.post("controller/ajax_update_admin.php",{id: id, fname: fname, lname: lname, level: level, active: active} ,function(data){
                
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
        $('#submit_btn').click(function(){
            // send store text coz search with this internal id not auto increment id of the store
            var store = $('#store').find(":selected").text();
            var cashier = $('#cashier').find(":selected").text();
            
            //var store = $('#cashier').val(); 
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var fromTime = $('#fromTime').val();
            var toTime = $('#toTime').val();
//          //alert(city+" "+place+" "+fromDate+" "+toDate+" "+fromTime+" "+toTime);
            $("#search_result").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_store_stat.php", {store: store, cashier: cashier, fromDate:fromDate, toDate:toDate, fromTime: fromTime, toTime: toTime }, function(data){
                $("#search_result").html(data).fadeIn(100);
            });
            return false;
        });
         $('#submit_btn_stat').unbind().click(function(){
            // send store text coz search with this internal id not auto increment id of the store
            var ok = 1;
            var store = $('#store').find(":selected").text();
            var cashier = $('#cashier').find(":selected").text();
//            //var store = $('#cashier').val(); 
            //alert(store);
            if(store == ''){ 
                store = $('#store').val();
                cashier = $('#cashier').val();
            }
            //alert(store);
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var fromTime = $('#fromTime').val();
            var toTime = $('#toTime').val();
            
//            alert(store+" "+cashier+" "+fromDate+" "+toDate+" "+fromTime+" "+toTime);
            if(toDate == '' && fromDate != '' && fromTime != '' && toTime != ''){
//                alert(fromTime+" "+toTime);
                fromTime = parseInt(fromTime);
                toTime = parseInt(toTime);
                if(fromTime > toTime){
                    ok = 0;
                }
            }
            //alert(store+" "+cashier+" "+fromDate+" "+toDate+" "+fromTime+" "+toTime);
            if(ok == 0){
                alert('Start time must be less than Next time');
            }
            else{
                $("#search_result").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
                $.post("controller/ajax_store_stat_final.php", {store: store, cashier: cashier, fromDate:fromDate, toDate:toDate, fromTime: fromTime, toTime: toTime }, function(data){
                    $("#search_result").html(data).fadeIn(100);
                });    
            }
            return false;
        });
        // for auto next combo values
//        $("#report").relatedSelects({
//		onChangeLoad: 'view/datasupplier.php',
//		selects: ['store', 'cashier'],
//		loadingMessage: 'Loading, wait...'
//	});
        // for manual text for 3 select combo box
//        $("#manual_text").relatedSelects({
//		onChangeLoad: 'view/datasupplier_manual.php',
//		loadingMessage: 'Loading, wait...',
//		selects: ['chain', 'store','cashier'],
//		disableIfEmpty:true
//		onEmptyResult: function(){
//			// 'this' is a reference to the changed select box
//			alert('Unable to find any matches for ' + $(this).find('option:selected').text() + '!');
//		}
//	});
	$('.related-select').change(function(){
            var id=$(this).val();
            var dataString = 'id='+ id;
//            alert(dataString);
            $.ajax({
                type: "POST",
                url: "view/cashier-select.php",
                data: dataString,
                cache: false,
                success: function(data)
                {
                    $(".cashier-select").html(data);
                } 
            });

            return false;
        });
        // choose all stores or choose only active stores
        $('.store_a').change(function(){
            //alert("ok");
            var myRadio = $('input[name=store_a]');
            var checkedValue = myRadio.filter(':checked').val();
            if(checkedValue != "active"){
                $('#store-div-active').hide();
                
                //$('#store-div-all').load('view/store-stat.php?include=yes #store-div-all').fadeIn("slow");
                $('#store-div-all').show();
                
            }
            else{
                $('#store-div-all').hide();
                
                //$('#store-div-active').load('view/store-stat.php?include=yes #store-div-active').fadeIn("slow");
                $('#store-div-active').show();
            }
            return false;
        });
        // manual text add button
        $('#manual_submit_btn').click(function(){
            // send store text coz search with this internal id not auto increment id of the store
            var chain = $('#chain').find(":selected").val();
            var store = $('#store').find(":selected").text();
            var cashier = $('#cashier').find(":selected").text();
            var left_text = $('#left_text').val();
            var right_text = $('#right_text').val();
            
            $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_add_text.php", {chain: chain, store: store, cashier: cashier, left_text: left_text, right_text: right_text }, function(data){
                showMessageSE(data, "Text Data Successfully Added");    
            });
            return false;
        });
        $('#manual_search_btn').click(function(){
           var chain = $('#chain').find(":selected").val();
           var store = $('#store').find(":selected").text();
           var cashier = $('#cashier').find(":selected").text();
           
           $("#search_result").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
           $.post("controller/ajax_search_text.php", {chain: chain, store: store, cashier: cashier }, function(data){
               $("#search_result").html(data).fadeIn(100);
           });
           return false; 
        });
        $('#manual_update_btn').click(function(){
            var id = $('#id').val();
            var left = $('#left_text').val();
            var right = $('#right_text').val();
            //alert(id);
            $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_update_manual_text.php", {id: id, left_text: left, right_text: right }, function(data){
               showMessageSE(data, "Text Data Successfully Updated");    
            });
           return false; 
        });
         $('#store_day_submit_btn').click(function(){
            var store7days = $('input[name=box]');
            var store7days = store7days.filter(':checked').val();
            var from = 'null';
            var to = 'null';
            var selected = new Array();
            $('#stat-storeList input:checked').each(function() {
                //selected.push($(this).attr('name'));
                selected.push($(this).val());
            });
            
            //alert(selected);
            if(store7days == 'dateRange'){
                
                var fromDate = $('#fromDate').val();
                var toDate = $('#toDate').val();
                if(fromDate == '' || toDate == ''){
                    alert('Please select the dates...');
                    return false;
                }
                from = fromDate;
                to = toDate;
            }
            else if(store7days == 'weeks'){
                var fromWeek = $('#weekFrom').val();
                var toWeek = $('#weekTo').val();
                if(fromWeek == 'null' || toWeek == 'null'){
                    alert('Please select the Weeks...');
                    return false;
                }
                else if(parseInt(fromWeek) > parseInt(toWeek)){
                    alert('From Week Must be less than Next week...');
                    return false;
                }
                from = fromWeek;
                to = toWeek;
            }
            else if(store7days == 'months'){
                var fromMonth = $('#monthFrom').val();
                var toMonth = $('#monthTo').val();
                if(fromMonth == 'null' || toMonth == 'null'){
                    alert('Please select months');
                    return false;
                }else if(parseInt(fromMonth) > parseInt(toMonth)){
                    alert('From Month must be less than next month');
                    return false;
                }
                from = fromMOnth;
                to = toMonth;
            }
            else if(store7days == 'years'){
                var fromYear = $('#yearFrom').val();
                var toYear = $('#yearTo').val();
                if(fromYear == 'null' || toYear == 'null'){
                    alert('Please Select years');
                    return false;
                }else if(parseInt(fromYear) > parseInt(toYear)){
                    alert('From Year must be less than the next year');
                    return false;
                }
                from = fromYear;
                to = toYear;
            }
            //alert(fromDate);
            //alert(store7days);
            $("#search_result").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_store_day_statistics.php", {store7days: store7days, fromDate: from, toDate: to, storeList: selected }, function(data){
               $("#search_result").html(data).fadeIn(100);
            });
           return false; 
        });
        // submit button for cashier day submit
        $('#cashier_day_submit_btn').click(function(){
            var store7days = $('input[name=box]');
            var store7days = store7days.filter(':checked').val();
            var from = 'null';
            var to = 'null';
            var selected = $('#store-cashier-day-form').val();
            
//            alert(selected);
            if(store7days == 'dateRange'){
                
                var fromDate = $('#fromDate').val();
                var toDate = $('#toDate').val();
                if(fromDate == '' || toDate == ''){
                    alert('Please select the dates...');
                    return false;
                }
                from = fromDate;
                to = toDate;
            }
            else if(store7days == 'weeks'){
                var fromWeek = $('#weekFrom').val();
                var toWeek = $('#weekTo').val();
                if(fromWeek == 'null' || toWeek == 'null'){
                    alert('Please select the Weeks...');
                    return false;
                }
                else if(parseInt(fromWeek) > parseInt(toWeek)){
                    alert('From Week Must be less than Next week...');
                    return false;
                }
                from = fromWeek;
                to = toWeek;
            }
            else if(store7days == 'months'){
                var fromMonth = $('#monthFrom').val();
                var toMonth = $('#monthTo').val();
                if(fromMonth == 'null' || toMonth == 'null'){
                    alert('Please select months');
                    return false;
                }else if(parseInt(fromMonth) > parseInt(toMonth)){
                    alert('From Month must be less than next month');
                    return false;
                }
                from = fromMOnth;
                to = toMonth;
            }
            else if(store7days == 'years'){
                var fromYear = $('#yearFrom').val();
                var toYear = $('#yearTo').val();
                if(fromYear == 'null' || toYear == 'null'){
                    alert('Please Select years');
                    return false;
                }else if(parseInt(fromYear) > parseInt(toYear)){
                    alert('From Year must be less than the next year');
                    return false;
                }
                from = fromYear;
                to = toYear;
            }
            //alert(fromDate);
            //alert(store7days);
            $("#search_result").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_cashier_day_statistics.php", {store7days: store7days, fromDate: from, toDate: to, storeList: selected }, function(data){
               $('#store_title').html(selected);
               $("#search_result").html(data).fadeIn(100);
            });
           return false; 
        });
        $('#submit_btn_exproducts').click(function(){
            $('html, body').animate({ scrollTop: 0 }, 'slow');
            var selected = new Array();
            $('#checkboxes input:checked').each(function() {
                //selected.push($(this).attr('name'));
                selected.push($(this).val());
            });
            $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_exclude_groups.php", {list: selected }, function(data){
               showMessageSE(data, "Blocked Group List Successfully Updated...");
            });
            //alert(selected);
            return false;
        });
        $('.change_permission').click(function(){
            var group_id = $(this).attr('id');
            window.location = 'admin.php?page=change-permission&id='+group_id;
            
        });
        $('#change-permission').click(function(){
           
            var selected = new Array();
            var group_id = $('#group_id').val();
            $('#checkboxes input:checked').each(function() {
                //selected.push($(this).attr('name'));
                selected.push($(this).val());
            });
            $("#error-box").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/ajax_change_permission.php", {group_id: group_id, list: selected }, function(data){
               showMessageSE(data, "Permissions Are Successfully Updated...");
            });
            //alert(selected);
           
            return false;
        });
        
	/***************** END PAGES  ************************/
	/*
         * if Jquery page loads successfully then it hides the loading div of the admin content div and showing the other div
         * if you see the loading image no other page, make sure you add all the js folder files
         */
        $('#loading').hide();
	$('#admin_content').show().fadeIn(1000);
});