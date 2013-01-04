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
if($_GET[id]){
    $id = $_GET[id];
    //echo $chain.", ".$store.", ".$cashier;
    $q = "SELECT id,left_text, right_text FROM stat_text_msg WHERE id='$id' LIMIT 1";
    $q1 = mysql_query($q) or die(mysql_error());
    $q2 = mysql_fetch_array($q1);
    echo "<h1>Statistics Manually Applied Text : </h1>";
echo "<div id='error-box' class='error-valid' style='display:none' >error</div>"; 
echo "<form id='manual_text'>";
echo "<table>";
echo "<tr><td>";
echo "<fieldset style='width: 410px; padding: 10px;'>";
echo "<legend>Store & Cashier</legend>";
include 'dbconnectonline.php';
echo "<div>";
$q_store = "SELECT id,internal_id, title FROM twosell_chain ORDER BY internal_id";
$result = sql_query($q_store, $linkOnline);
if(gettype($result) == "array"){
    echo "<b>Chain: </b>";
    echo "<select name='chain' id='chain'>";
    foreach ($result as $rows){
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
$result = sql_query($q_store, $linkOnline);
if(gettype($result) == "array"){
    echo "<b>Store: </b>";
    echo "<select name='store' id='store' tabindex='1' disabled='disabled'>";
    echo "<option value='-1'>Select a store</option>";
    foreach ($result as $rows) {
        echo "<option value='$rows[id]'"; if($store == $rows[internal_id]) echo "selected='selected'"; echo ">$rows[internal_id]</option>";
    }
    echo "</select>";
}
else{
    echo $result;
}
echo "</div>";
   
echo " <b>Cashier: </b>";
if($cashier!=-1) echo $cashier;
else echo "No";

echo "<table style='margin: 10px 0' cellpadding='3'><tr><td><b>Left Text: </b></td><td><textarea name='left_text' id='left_text' onKeyDown='limitText(this.form.left_text,this.form.countdown1,80);' onKeyUp='limitText(this.form.left_text,this.form.countdown1,80);' tabindex='3'>$q2[left_text]</textarea><font size='1'>(Maximum characters: 80)<br>
You have <input readonly type='text' name='countdown1' size='3' value='80'> characters left.</font></td></tr>";
echo "<tr><td><b>Right Text: </b></td><td> <textarea name='right_text' id='right_text' onKeyDown='limitText(this.form.right_text,this.form.countdown2,80);' onKeyUp='limitText(this.form.right_text,this.form.countdown2,80);' tabindex='4'>$q2[right_text]</textarea><font size='1'>(Maximum characters: 80)<br>
You have <input readonly type='text' name='countdown2' size='3' value='80'> characters left.</font></td></tr></table>";
echo "</fieldset>";
echo "</td>";
echo "</tr><tr><td colspan='2' align='center'><input type='hidden' id='id' name='id' value='$q2[id]' /><input type='button' name='manual_update_btn' id='manual_update_btn' class='submit_btn' value='ADD' tabindex='5' /></td></tr></table>";
echo "</form>";

}
?>
