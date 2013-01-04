<?php
    include 'conf_sw.php';
?>
<script type="text/javascript">
      $(document).ready(function() {

        document.title = '<?php echo $title; ?>';

      });
    </script>

<?php
    
    $ps =array();
    if(isset($_POST[already_sent])) $already_sent = $_POST[already_sent];
    else $already_sent = 0;
    
    if(isset($_POST['product-values']))
        $ps = unserialize($_POST['product-values']);
    else
        unset($ps);
    if(!empty($_SESSION['ps']))
        $ps = $_SESSION['ps'];
    //echo $already_sent;
    //print_r($ps);
    
    //print_r($_SESSION['ps']);
    
?>
<form name="pos-product" id="pos-product" action="suggestion_sw.php" method="post">
<div id="content">
    <table align="center" cellspacing="1" cellpadding="1" border="0" width="100%" id="divs">
        <?php
            for($i = 0; $i < sizeof($images); $i++ ){
                //if($i%3 == 0) echo "<tr><td align='center'>";
                if($i%3 == 0) echo "<tr>";
                echo "<td><div class='vcenter round' id='div-".$i."'";
                echo "><img class='pro-img' src='images/products/".$images[$i][image_name]."'  /></div>
                </td>";
                if($i%3 == 2) echo "</tr>";
            }
        ?>
    </table>
    <div style="visibility: hidden">
        <?php
        for($j=0; $j<9; $j++){
            echo "<input type='checkbox' name='product[]' value='".$j."' id='chk-".$j."' ";
            for($i=0; $i < sizeof($ps); $i++){
                //echo $ps[6];
                if($ps[$i] == $j) echo "checked='checked'";
                //print_r($ps);
            }
            echo "/>";
            
        }
        ?>
              
        
        <input type="hidden" name="sent" id="sent" value="<?php echo $already_sent; ?>" />
        
    </div>
    <div id="bottom_left"><textarea id="text_area" rows="2" cols="55" name="groupIDs"></textarea></div>
</div>
    <div id="bottom_right"><input type="submit" name="submit_products" id="submit_products" value="Betala" /></div>
</form>