var table_nonact_selected;
var table_nonact_selected_background_color;
var table_productList = {
    title:function(id){
        id_id = $(id).siblings();
        id_id = id_id.first();
        productId = id_id.text();
        //alert(productId);return;
        $(table_nonact_selected).css('background-color',table_nonact_selected_background_color);
        var randomnumber=Math.floor(Math.random()*110);
        $('#result').html('<h3>Result is loading...., Wait</h3>');
        //query = 'probability.php?productId='+jQuery.trim(productId)
        query = 'testing.php?productId='+jQuery.trim(productId)
        +'&t='+randomnumber;
         //alert(query);return;
        query = query.replace(/ /g,'%20');
        $('#result').load(query);
        table_nonact_selected = id;
        table_nonact_selected_background_color = $(id).css('background-color');
        $(id).css('background-color','darkseagreen');
    },
    productId:function(id){
        productId = $(id).text();
        alert(productId);return;
    }
};

var table_productListItemGroup = {
    itemName:function(id){
        productId = $(id).text();
        $(table_nonact_selected).css('background-color',table_nonact_selected_background_color);
        var randomnumber=Math.floor(Math.random()*110);
        $('#result').html('<h3>Result is loading....</h3>');
        //query = 'probability.php?productId='+jQuery.trim(productId)
        query = 'testing_item_group.php?productId='+jQuery.trim(productId)
        +'&t='+randomnumber;
        query = query.replace(/ /g,'%20');
        $('#result').load(query);
        table_nonact_selected = id;
        table_nonact_selected_background_color = $(id).css('background-color');
        $(id).css('background-color','darkseagreen');
    },
    productId:function(id){
        productId = $(id).text();
        alert(productId);return;
    }
};

