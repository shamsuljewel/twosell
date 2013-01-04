<script language="javascript" type="text/javascript">
function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}
</script>
<?php
session_start();
function timeOption(){
    for($i=0; $i<=23; $i++){
        echo "<option value='$i'>$i</option>";
    }
}
echo "<h1>Statistics Manually Applied Text : </h1>";
echo "<div id='error-box' class='error-valid' style='display:none' >error</div>"; 
echo "<form id='manual_text'>";
echo "<table>";
echo "<tr><td>";
echo "<fieldset style='width: 410px; padding: 10px;'>";
echo "<legend>Store & Cashier</legend>";

//include 'dbconnectonline.php';
echo "<div>";
$q_store = "SELECT id,internal_id, title FROM twosell_chain ORDER BY internal_id";
$result = sql_query($q_store, $link);
if(gettype($result) == "array"){
    echo "<b>Chain: </b>";
    echo "<select name='chain' id='chain'>";
    foreach ($result as $rows) {
        echo "<option value='$rows[id]'>$rows[internal_id]</option>";
    }
    echo "</select>";
}
else{
    echo $result;
}
echo "</div>";
//echo "<div class='div-space'><input type='radio' name='store_a' class='store_a' checked='checked' value='active' />Active <input type='radio' name='store_a' class='store_a' value='all' />All</div>";
echo "<div id='store-div-active'>";
//if($_GET['include'] == "yes"){
//    require_once '../dbconnectonline.php';
//    require_once '../functions/commonFunction.php';
//}
$q_store = "SELECT id,internal_id, title FROM twosell_store WHERE active = 1 ORDER BY internal_id";
$result = sql_query($q_store, $link);
if(gettype($result) == "array"){
    echo "<b>Store: </b>";
    echo "<select name='store' id='store' class='related-select'>";
    echo "<option value='-1'>Select a store</option>";
    foreach ($result as $rows) {
        echo "<option value='$rows[id]'>$rows[internal_id]</option>";
    }
    echo "</select>";
}
else{
    echo $result;
}
echo "</div>";
    
echo " <b>Cashier: </b>";
echo "<select name='cashier' id='cashier' class='cashier-select'>
        <option value=''>Choose a Cashier &raquo;</option>
      </select>";

echo "</fieldset>";
echo "</td>";
echo "</tr><tr><td colspan='2' align='center'><input type='button' name='manual_search_btn' id='manual_search_btn' class='submit_btn' value='View' /></td></tr></table>";
echo "</form>";

echo "<div id='search_result'>
    
</div>"

?>
