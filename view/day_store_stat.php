    <script>
    //$('#time_pic').datetimepicker();    
    
    $(document).ready(function(){
        $('#store_day_submit_btn').trigger('click');    
        //$('#search_result').load('controller/ajax_store_day_statistics.php').fadeIn("slow");    
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
        $('#selectAll').click(function(){
            //alert('ok');
            var value = $(this).is(':checked');
            //$('#' + id).is(":checked")
            if(value === true){
                // select all stores
                $('#stat-storeList').find('input:checkbox').not(':checked').attr('checked', this.checked);
            }
            else{
                // unselect all stores
                $('#stat-storeList').find('input[type=checkbox]:checked').removeAttr('checked');
                
            }
        });
    });
    
    </script>
<?php
if(!isset($_SESSION)) session_start();
$stores = $user->getStoreIdByChain(1);
echo "<h1>Store Days Statistics: </h1>";
echo "<form id='report'>";
echo "<table border='0'>";
echo "<tr>";
echo "<td>";

echo "<table border='0' width='100%' class='form_table'>
        <tr><td><input type='radio' name='box' id='store7days' value='store7days' checked='checked' />Last 10 days</td><td>
        <input type='radio' name='box' id='storeTwoDate' value='dateRange' />Date range
        <div>From: <input type='text' name='fromDate' id='fromDate' /> To <input type='text' name='toDate' id='toDate' /></div>
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
echo "<td><input type='button' name='store_day_submit_btn' id='store_day_submit_btn' value='Submit' class='submit_btn' /></td></tr>    
     </table>";

echo "</td>";
if($user->getGroupName() == "twosell_super" || $user->getGroupName() == "twosell" || $user->getGroupName() == "chain" || $user->getGroupName()== "store"){ 
    echo "<td>
    <div><input type='checkbox' name='selectAll' id='selectAll' checked='checked' />Select All Stores </div>    
    <div id='stat-storeList'>";
    for($i=0; $i < count($stores); $i++){
        echo "<input type='checkbox' name='stores[]' checked='checked' value='$stores[$i]' />".$stores[$i];
        if($i%6 == 5) echo "<br />";
    }
    echo "</div></td>";
}
echo "</tr></table>";
echo "</form>";

echo "<div id='search_result'>

</div>";

?>
