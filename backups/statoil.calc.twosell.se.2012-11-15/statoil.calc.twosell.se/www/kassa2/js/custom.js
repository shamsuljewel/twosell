// JavaScript Document
$(document).ready(function(){
    $("#submit_products").attr("disabled", "disabled");
    if($('#chk-0').is(':checked')){
        //alert('0');
        //$('div.vcenter').addClass("selected-background");
        $('#div-0').css('background-color', '#E98300');
    }
//    var val = [];
    $(':checkbox:checked').each(function(i){
        //alert(i);
        //$(this).val();
        $('#div-'+$(this).val()).css('background-color', '#E98300');
        $("#submit_products").attr("disabled", false);
    });
    
    $('#divs div').click(function(){
        //alert("hi");
        //$(this).css('background-color', '#000');
        var dv = $('#divs div').index(this);
        //alert(dv);
        var select_count = 0;
        var i = 0;
        //alert(select_count);
        $(':checkbox:checked').each(function(i){
            ++select_count;
            
            
        });
        if( $('#chk-'+dv).is(':checked')){
           
            //$(this).toggleClass('select-background', 0);
            $(this).css('background-color', '#CCC');
            select_count--;
            $('#chk-'+dv).removeAttr('checked');
            
        }  
        else{
            if(select_count < 3){
                $(this).css('background-color', '#E98300');
                $('#chk-'+dv).attr('checked', 'checked');
                select_count++;
            }
        }
        if(select_count > 0){
            $("#submit_products").attr("disabled", false);
        }
        else{
            $("#submit_products").attr("disabled", true);
        }
        //alert(select_count);
        return false;
    });
    $('#pos-product').unbind().submit(function(){
        var options = { 
            target: '#load',
            //url: 'suggestion.php',
            //method: post,
            success: function(data) { 
                //alert("hi");
                //if(data == "yes")	alert("Hi");
                $("#load").html(data);
                //$('#show-inv-flat').load('flat-inv-list.php?id='+pid).fadeIn("slow");
            } 	
        }; 
        $('#pos-product').ajaxSubmit(options);

        return false;
    });
    $('#sugg-form').unbind().submit(function(){
        //alert("ok");
        /*$.post("index.php",{ already_sent: 1, product: $('#product-values').val() }, function(data){
            $("#load").html(data).fadeTo(900,1);
        });*/
         var options = { 
            target: '#load',
            //url: 'suggestion.php',
            //method: post,
            success: function(data) { 
                //alert("hi");
                //if(data == "yes")	alert("Hi");
                $("#load").html(data);
                //$('#show-inv-flat').load('flat-inv-list.php?id='+pid).fadeIn("slow");
            } 	
        }; 
        $('#sugg-form').ajaxSubmit(options);
        return false;
    });
//    $('#sugg_content div.sugg_yes').mouseover(function() {
//        alert("hi");
//    });
    $('#sugg_content div.sugg_yes').live('mouseover', function() { 
        $('#sugg_content div.sugg_yes').css('cursor','pointer');
    });
//    $('div#sugg_content').unbiind().click(function(){
//        alert("hi");
//        return false;
//    });
//    $('#submit_products').unbind().click(function(){
//        var i = 0;
//        //var k = $('input[type=checkbox][name=submit_products]:checked');
//        var val = [];
//        $(':checkbox:checked').each(function(i){
//            val[i] = $(this).val();
//        });
//        
//        $( "#dialog-edit-highLow" ).dialog({
//            resizable: false,
//            autoOpen: false,
//            height: 300,
//            width: 350,
//            modal: true,
//            buttons: {
//                "Update": function() {
//
//                },
//                Cancel: function() {
//                    $( this ).dialog( "close" );
//                }
//            },
//            close: function() {
//                allFields.val( "" ).removeClass( "ui-state-error" );
//            }
//        });
//
//        $( "#dialog-edit-highLow" ).dialog( "open" );
//        
//        //alert(val);
////        var loading = "<div class='loading'><img src='images/loader.gif' /></div>";
////        $("#main-content").html(loading).fadeIn(5000);
////        $.post("suggestion.php",{ val: val }, function(data){
////            $("#main-content").html(data).fadeTo(900,1);
////        });
////            $('input[type=checkbox][name=submit_products]:checked').each( function() {
////            if(i==0){
////                str = $(this).val();
////                i = 1;
////            }
////            else
////                str = str + '-' + $(this).val();
////        });
//        
//    });
     /*$("#example div").click(function() {

        var index = $(this).index();
        //alert(index);
        $("#example_index").html("Index " + index + " was clicked");

    });*/
    					   
});