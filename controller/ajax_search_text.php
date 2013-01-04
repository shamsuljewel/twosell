<?php
// store stat
session_start();
include '../functions/commonFunction.php';
include '../admin_config.php';
?>
<script>
    $(document).ready(function(){
        $('#nav_div').load('controller/navigation_manual_text.php').fadeIn("slow");    
        $('.a_li').click(function(){
            var a = $(this).attr('id');
            $("#nav_div").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/navigation_search.php",{z:a},function(data){
                $("#nav_div").html(data).fadeIn(100);
            });
            return false;
        });
    
    });
</script>

<?php
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
if(isset($_POST['chain'])){
        include '../dbconnect.php';
        $chain = $_POST['chain'];
        $store = $_POST['store'];
        $store_null = "Select a store";
        if($store == $store_null)
            $store = -1;
        $cashier = $_POST['cashier'];
        $cas_null = "Choose a Cashier »";
        if($cashier == $cas_null)
            $cashier = -1;
        //echo $chain.", ".$store.", ".$cashier;
        if($chain != '-1'){
            
            // if for cashier
            if($store != '-1' && $cashier != '-1')
                $q = "SELECT * FROM stat_text_msg WHERE chain='$chain' AND store='$store' AND cashier='$cashier'";
            else if($store!= '-1')
                $q = "SELECT * FROM stat_text_msg WHERE chain='$chain' AND store='$store'";
            else 
                $q = "SELECT * FROM stat_text_msg WHERE chain='$chain'";
        }
        //echo "<br />".$q;
        
        $q1 = mysql_query($q);
        if($q1 != FALSE){
        $_SESSION['query_manual_text'] = $q;
        $total_found = mysql_num_rows($q1);
        echo "<b>Total Record:</b> ".$total_found;
        
        echo "<div id='nav_div'>";
            
        echo "</div>";
    }
    else {
        echo "Query Execution Error..please contact webmaster:)".  mysql_error();
    }
   }
   else{
       echo "Store is NULL";
   }

?>
