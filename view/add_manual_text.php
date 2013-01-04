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

echo "<div>";
$q_chain = "SELECT id,internal_id, title FROM twosell_chain ORDER BY internal_id";
//echo $q_store;
$result = sql_query($q_chain, $link);
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
echo "<div id='store-div-active'>";

$q_store = "SELECT id,internal_id, title FROM twosell_store WHERE active = 1 ORDER BY internal_id";
//echo $q_store;
$result = sql_query($q_store, $link);
if(gettype($result) == "array"){
    echo "<b>Store: </b>";
    echo "<select name='store' id='store' tabindex='1' class='related-select'>";
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
echo "<select name='cashier' id='cashier' tabindex='2' class='cashier-select'>";
        echo "<option value=''>Choose a Cashier &raquo;</option>";
echo "</select>";

echo "<table style='margin: 10px 0' cellpadding='3'><tr><td><b>Left Text: </b></td><td><textarea name='left_text' id='left_text' onKeyDown='limitText(this.form.left_text,this.form.countdown1,80);' onKeyUp='limitText(this.form.left_text,this.form.countdown1,80);' tabindex='3'></textarea><font size='1'>(Maximum characters: 80)<br>
You have <input readonly type='text' name='countdown1' size='3' value='80'> characters left.</font></td></tr>";
echo "<tr><td><b>Right Text: </b></td><td> <textarea name='right_text' id='right_text' onKeyDown='limitText(this.form.right_text,this.form.countdown2,80);' onKeyUp='limitText(this.form.right_text,this.form.countdown2,80);' tabindex='4'></textarea><font size='1'>(Maximum characters: 80)<br>
You have <input readonly type='text' name='countdown2' size='3' value='80'> characters left.</font></td></tr></table>";
echo "</fieldset>";
echo "</td>";
echo "</tr><tr><td colspan='2' align='center'><input type='button' name='manual_submit_btn' id='manual_submit_btn' class='submit_btn' value='ADD' tabindex='5' /></td></tr></table>";
echo "</form>";


?>
