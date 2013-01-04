<script>
$(document).ready(function(){
    $('#cashier_day_submit_btn').trigger('click');
//    $('#search_result').load('controller/ajax_cashier_day_statistics.php').fadeIn("slow");
    $(function() {
        var dates = $( "#fromDate, #toDate" ).datepicker({
                dateFormat: 'yy-mm-dd',
                defaultDate: "-1w",
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                onSelect: function( selectedDate ) {
                    var option = this.id == "fromDate" ? "minDate" : "maxDate",
                            instance = $( this ).data( "datepicker" ),
                            date = $.datepicker.parseDate(
                                    instance.settings.dateFormat ||
                                    $.datepicker._defaults.dateFormat,
                                    selectedDate, instance.settings );
                    dates.not( this ).datepicker( "option", option, date );
                }
        });
        
    });
});
</script>
<?php
    //include 'functions/commonFunction.php';
    $store_id = $_GET['store_id'];
    //echo $store_id;
    // which option is selected??
    $store7days = $_GET['option'];

    $dates = $_SESSION['dateOf'];
//    print_r($dates);
    $count_dates = count($dates);
    //echo $count_dates;
    $fromDate = $dates[$count_dates-1];
    $toDate = $dates[0];
    
    $dataArray = array();
    echo "<h1>Cashier Day Statistics</h1>";
    
//    echo "<div style='float: right'><img src='images/showing.jpg' width='310' /></div>";
    echo "<form id='report'>";
    echo "<table border='0'>";
    echo "<tr><td colspan='3'>";
    echo "Store: ";
    $chainId = 1;
    $stores = $user->getStoreIdByChain($chainId);
//    print_r($stores);
    echo "<select name='store-cashier-day-form' id='store-cashier-day-form'>";
    for($i=0; $i < count($stores); $i++){
        echo "<option value='$stores[$i]' ";
        if($stores[$i] == $store_id) echo "selected='selected'";
        echo " >".$stores[$i]."</option>";
    }
    echo "</select>";
    echo "</td></tr>";
    echo "<tr>";
    echo "<td>";

    echo "<table border='0' width='100%' class='form_table'>
        <tr><td><input type='radio' name='box' id='store7days' value='store7days' checked='checked' />Last 10 days</td><td>
        <input type='radio' name='box' id='storeTwoDate' value='dateRange' ";
        if($store7days == 'dateRange') echo "checked='checked' ";
    echo " />Date range
        <div>From: <input type='text' name='fromDate' id='fromDate' value='$fromDate' /> To <input type='text' name='toDate' id='toDate' value='$toDate' /></div>
        </td>";
    if($user->getGroupName() == "twosell_super" || $user->getGroupName() == "twosell" || $user->getGroupName() == "chain"){ 
    echo "<td><input type='radio' name='box' value='weeks' />Weeks
        <div>From: <select name='weekFrom' id='weekFrom' >
        <option value='null'>From</option>";
        for($i=1; $i<=52; $i++) echo "<option value='$i'>$i</option>";
    echo "</select> To <select name='weekTo' id='weekTo' ><option value='null'>To</option>";
     for($i=1; $i<=52; $i++) echo "<option value='$i'>$i</option>";   
    echo "</select></div>
        </td></tr>
        <tr><td><input type='radio' name='box' value='months' />Months
        <div>From: <select name='monthFrom' id='monthFrom' ><option value='null'>From</option>";
        for($i=1; $i <= 12; $i++) echo "<option value='$i'>$i</option>";
    echo "</select> To <select name='monthTo' id='monthTo' ><option value='null'>To</option>";
        for($i=1; $i <= 12; $i++) echo "<option value='$i'>$i</option>";
    echo "</select></div>
        </td><td><input type='radio' name='box' value='years' />Years
        <div>From: <select name='yearFrom' id='yearFrom' ><option value='null'>From</option>";
        $start = 2012;
        $last = date('Y');
        for($start = 2012; $start <= $last; $start++){
            echo "<option value='$start'>$start</option>";
        }
    echo "</select> To <select name='yearTo' id='yearTo'><option value='null'>To</option>";
        for($start = 2012; $start <= $last; $start++){
            echo "<option value='$start'>$start</option>";
        }
    echo "</select></div>
        </td>";
    }
    echo "<td><input type='button' name='cashier_day_submit_btn' id='cashier_day_submit_btn' value='Submit' class='submit_btn' /></td></tr>    
     </table>";

    echo "</td>";

    echo "</tr></table>";
    echo "</form>";

    echo "<h1>Store: <span id='store_title'>".$store_id."</span></h1>";
    
    
    
    
    //print_r($dataArray);
    echo "<div id='search_result'>
        
    </div>";
    
    


?>
