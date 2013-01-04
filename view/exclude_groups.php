<?php
session_start();
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
?>
<div id="admin_home">
    <div id="left_in">
	<div id="form_id">
            <p class="bold bigsize">Exclude Groups</p>
            <div id="error-box" class="error-valid" style=" display:none" >error</div> <!-- Error Message Here -->
            <form name="formexproduct" id="formexproduct" method="post" action="" >
                <fieldset>
                    <legend>Groups Names</legend>
                    <?php
                    //print_r($_SESSION['banned_group']);
                    mysql_select_db('statoil_canonical', $link);
                    $q = "SELECT meta_group_id, group_name FROM tsln_meta_groups ORDER BY group_name";
                    $result = sql_query($q, $link);
                    if(is_array($result)){
                        echo "<div id='checkboxes'>";
                        echo "<table class='view_tbl'>";
                        $i=0;
                        foreach ($result as $value) {
                            if($i%10 == 0) echo "<tr>"; 
                            echo "<td><input type='checkbox' name='ex_groups[]' id='ex_groups' value='$value[meta_group_id]' "; if(in_array($value[meta_group_id], $_SESSION['banned_group'])) echo "checked='checked'"; echo " />".  utf8_encode($value['group_name'])." ($value[meta_group_id])</td>";
                            if($i%10 == 9) echo "</tr>";
                            $i++;
                        }
                        echo "</table>";
                        echo "<div>";
                    }else{
                        echo $result;
                    }
                    ?>
                </fieldset>
                <div align='center'><input type='button' name='submit_btn_exproducts' id='submit_btn_exproducts' class='submit_btn' value='Submit' /></div>
            </form>
        </div>
    </div>
</div>

