<?php

include('../dbconnect.php');
if($_POST['id']){
    $id=$_POST['id'];
    $q = "SELECT id,idnum, store_id FROM twosell_seller WHERE store_id='$id' ORDER BY idnum";
    $sql=mysql_query($q) or die(mysql_error());
    //echo $q;
    echo "<option value=''>Choose a Cashier &raquo;</option>";
    while($row = mysql_fetch_array($sql))
    {
        $id=$row['id'];
        $data=$row['idnum'];
        echo '<option value="'.$id.'">'.$data.'</option>';
    }
}

?>
