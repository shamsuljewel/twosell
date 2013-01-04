    <script>
    //$('#time_pic').datetimepicker();    
    
    $(function() {
        var dates = $( "#fromDate, #toDate" ).datepicker({
                dateFormat: 'yy-mm-dd',
                defaultDate: "+1w",
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
    
//    function a_onClick(){
//        alert("a");
//        return false;
//    }
//    var times = $('#time_pic').datetimepicker();    
    </script>
<?php
if(!isset($_SESSION)) session_start();
function timeOption(){
    for($i=0; $i<=23; $i++){
        echo "<option value='$i'>$i</option>";
    }
}
$banned_group = banned_groups();
$_SESSION['banned_group'] = $banned_group;

$banned_products = banned_products($banned_group);
$_SESSION['banned_products'] = $banned_products;
//print_r($banned_products);
echo "<h1>Statistics: </h1>";
include 'dbconnect.php';
$chains = $user->getChainID();
//print_r($chains);
$chain_id = 1; // fixed for statoil for now, functionality in done
//$stores = $user->getStoreID();
$stores = $user->getStoreIdByChain($chain_id);
//print_r($stores);
echo "<form id='report'>";
echo "<table>";
echo "<tr>";
if($user->getGroupName() == "twosell_super" || $user->getGroupName() == "twosell" || $user->getGroupName() == "chain" || $user->getGroupName() == "store"){
echo "<td>";
echo "<fieldset style='width: 360px; padding: 10px; height: 70px'>";
echo "<legend>Store & Cashier</legend>";
//echo "<div class='div-space'><input type='radio' name='store_a' class='store_a' checked='checked' value='active' />Active <input type='radio' name='store_a' class='store_a' value='all' />All</div>";
echo "<div id='store-div-active'>";

$q_store = "SELECT id,internal_id FROM twosell_store WHERE active = '1' ORDER BY internal_id";
$result1 = sql_query($q_store, $link);
if(gettype($result1) == "array"){
    echo "<b>Store: </b>";
    echo "<select name='store' id='store' class='related-select'>";
    //echo "<option value='-1'>Select a Store</option>";
    $default_selected = 0;
    foreach ($result1 as $rows) {
        if($default_selected == 0) $default_selected = $rows['id'];
        if(in_array($rows['internal_id'], $stores))
            echo "<option value='$rows[id]'>$rows[internal_id]</option>";
    }
    echo "</select>";
}
else{
    echo $result1;
}
echo "</div>";
   
echo " <b>Cashier: </b>";
echo "<select name='cashier' id='cashier' class='cashier-select'>";
        $q = "SELECT id,idnum, store_id FROM twosell_seller WHERE store_id='$default_selected' ORDER BY idnum";
        $sql=mysql_query($q) or die(mysql_error());
        echo $q;
        echo "<option value=''>Choose a Cashier &raquo;</option>";
        while($row = mysql_fetch_array($sql))
        {
            $id=$row['id'];
            $data=$row['idnum'];
            echo '<option value="'.$id.'">'.$data.'</option>';
        }  
echo "</select>";
echo "</fieldset>";
echo "</td>";
}
else{
    echo "<input type='hidden' id='store' value='".$stores[0]."'>";
    echo "<input type='hidden' id='cashier' value='".$user->getEmployeeID()."'>";
}
// 1st column
echo "<td><fieldset style='width: 250px; padding: 10px; height: 70px'><legend>Duration</legend>";
echo "<div style='padding-bottom: 2px'>From: <input type='text' name='fromDate' id='fromDate' /> To <input type='text' name='toDate' id='toDate' /></div>";        
echo "<div>Time: <select name='fromTime' id='fromTime'><option value=''>From...</option>";
timeOption();
echo "</select> To <select name='toTime' id='toTime'><option value=''>To...</option>";
timeOption();
echo "</select></div>";

echo "</fieldset></td>";
if($user->getGroupName() == "twosell_super" || $user->getGroupName() == "twosell"){
echo "<td><fieldset style='width: 200px; padding: 10px; height: 70px'><legend><a href='admin.php?page=exclude-groups'>Excludes Groups</a></legend>";
foreach ($_SESSION['banned_group'] as $value) {
    echo $value.", ";
}   
echo "</fieldset></td>";
}
echo "</tr><tr><td colspan='2' align='center'><input type='button' name='submit_btn_stat' id='submit_btn_stat' class='submit_btn' value='Submit' /></td></tr></table>";
echo "</form>";
echo "<div id='search_result'>

</div>";

?>
 