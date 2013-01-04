<?php
    session_start();
    if($_POST['product']){
        include 'conf.php';
      ?>
<script type="text/javascript">
      $(document).ready(function() {

        document.title = '<?php echo $title; ?>';

      });
    </script>

<?php

        $ps = $_POST['product'];
        //print_r($ps);
        if($_POST['sent'] == 0){
        $_SESSION['ps'] = $ps;
        //print_r($_SESSION['ps']);
        
  ?>  
<form name="sugg-form" id="sugg-form" action="?page=home" method="post"> 
    <div id="sugg_content">
        
    <?php
    echo "<div class='sp'></div>";
    $count = sizeof($ps);
    $link = $path."?page=report";
    //echo $link;
    if($count >= 3){
        
        for($i = 0; $i < $count; $i++){
            $img_url = "images/suggestions/".$suggestions[$ps[$i]][0]['id'].".jpg";
            if(!file_exists($img_url)){
//                $img_url = "images/suggestions/".$suggestions[$ps[$i]][0]['id'].".png";
//                if(!file_exists($img_url)){
//                    $img_url = "images/suggestions/".$suggestions[$ps[$i]][0]['id'].".gif";
//                }
                //      
                $img_url = "images/suggestions/statoil.jpg"; 
            }
            //echo $img_url;
            echo "<div class='sugg_box'>";
            
                //echo "<div class='sugg_yes'>";    
            ?>
        <div class='sugg_yes' onClick='parent.location="<?php echo $link; ?>"'>
        <?php
                    echo "<table width='100%'><tr><td class='img-td' align='center'><img src='".$img_url."' /></td>
                    <td class='des-td'>"; 
                    echo "<div><p class='art-name'>".utf8_encode($suggestions[$ps[$i]][0][art_name])."</p></div><br />";
                    if($suggestions[$ps[$i]][0][art_num] != "" ){
                        echo "<div>Art: ".$suggestions[$ps[$i]][0][art_num]."</div>";
                    }
                    else{
                        echo "<div>&nbsp</div>";
                    }
                    echo "<div class='art_text'>".utf8_encode($suggestions[$ps[$i]][0][text])."</div>";
                    echo "</td><td class='price-td'>";
                    if($suggestions[$ps[$i]][0][price] > 0){
                        echo $suggestions[$ps[$i]][0][price]." SEK";
                    }
                    
                    echo "</td></tr></table>";
                echo "</div>";
                
                echo "<div class='sugg_no'>";
                ?>  
        
                <input type='button' value='NO' class='nej' onClick='parent.location="<?php echo $link; ?>"' /></div>
            <?php
            echo "</div>";
            
            echo "<div class='sp'></div>";
        }
    }
    else if($count == 2){
        if($images[$ps[0]][price] > $images[$ps[1]][price]){
            $max = $ps[0];
            $min = $ps[1];
        }
        else{
            $max = $ps[1];
            $min = $ps[0];
        }
       // echo $max.", ".$min;
        for($i = 0; $i < 2; $i++){
            $img_url = "images/suggestions/".$suggestions[$max][$i]['id'].".jpg";
            if(!file_exists($img_url)){
//                $img_url = "images/suggestions/".$suggestions[$max][0]['id'].".png";
//                if(!file_exists($img_url)){
//                    $img_url = "images/suggestions/".$suggestions[$max][0]['id'].".gif";
//                }
                        $img_url = "images/suggestions/statoil.jpg"; 

                
            }
            echo "<div class='sugg_box'>";
             //   echo "<div class='sugg_yes'>";    
            ?>
        <div class='sugg_yes' onClick='parent.location="<?php echo $link; ?>"'>
        <?php
                    echo "<table width='100%'><tr><td class='img-td' align='center'><img src='".$img_url."' /></td>
                    <td class='des-td'>"; 
                    echo "<div class='art-name'>".utf8_encode($suggestions[$max][$i][art_name])."</div><br />";
                    if($suggestions[$max][$i][art_num] != ""){
                        echo "<div>Art: ".$suggestions[$max][$i][art_num]."</div>";
                    }
                    else{
                        echo "<div>&nbsp</div>";
                    }
                    echo "<div>".utf8_encode($suggestions[$max][$i][text])."</div>";
                    echo "</td><td class='price-td'>";
                    if($suggestions[$max][$i][price] > 0){
                        echo $suggestions[$max][$i][price]." SEK";
                    }
                    
                    echo "</td></tr></table>";
                echo "</div>";
         
                echo "<div class='sugg_no'>";
                ?>  
        

    <input type='button' value='NO' class='nej' onClick='window.location="<?php echo $link; ?>"' /></div>
            <?php
            echo "</div>";
            echo "<div class='sp'></div>";
        }
        $img_url = "images/suggestions/".$suggestions[$min][0]['id'].".jpg";
        if(!file_exists($img_url)){
//                $img_url = "images/suggestions/".$suggestions[$min][0]['id'].".png";
//                if(!file_exists($img_url)){
//                    $img_url = "images/suggestions/".$suggestions[$min][0]['id'].".gif";
//                }
               $img_url = "images/suggestions/statoil.jpg"; 
//                
        }
        echo "<div class='sugg_box'>";
             //   echo "<div class='sugg_yes'>";    
        ?>
        <div class='sugg_yes' onClick='parent.location="<?php echo $link; ?>"'>
        <?php
                    echo "<table width='100%'><tr><td class='img-td' align='center'><img src='".$img_url."' /></td>
                    <td class='des-td'>"; 
                    echo "<div class='art-name'>".utf8_encode($suggestions[$min][0][art_name])."</div><br />";
                    if($suggestions[$min][0][art_num] != ""){
                        echo "<div>Art: ".$suggestions[$min][0][art_num]."</div>";
                    }
                    else{
                        echo "<div>&nbsp</div>";
                    }
                    echo "<div>".utf8_encode($suggestions[$min][0][text])."</div>";
                    echo "</td><td class='price-td'>";
                    if($suggestions[$min][0][price] > 0){
                        echo $suggestions[$min][0][price]." SEK";
                    }
                    
                    echo "</td></tr></table>";
                echo "</div>";
                
                echo "<div class='sugg_no'>"
                ?>  
        
                <input type='button' value='NO' class='nej' onClick="parent.location='<?php echo $link; ?>'" /></div>
            <?php
            echo "</div>";
            echo "<div class='sp'></div>";
    }
    else{
        for($i = 0; $i < 3; $i++){
            //echo $i;
            $img_url = "images/suggestions/".$suggestions[$ps[0]][$i]['id'].".jpg";
            //echo $img_url;
            if(!file_exists($img_url)){
//                $img_url = "images/suggestions/".$suggestions[$ps[0]][$i]['id'].".png";
//                if(!file_exists($img_url)){
//                    $img_url = "images/suggestions/".$suggestions[$ps[0]][$i]['id'].".gif";
//                }
                $img_url = "images/suggestions/statoil.jpg"; 
            }
            
           // echo $suggestions[$ps[0]][$i]['id'].", ";
           // echo $img_url;
            echo "<div class='sugg_box'>";
             //   echo "<div class='sugg_yes'>";    
            ?>
        <div class='sugg_yes' onClick='parent.location="<?php echo $link; ?>"'>
        <?php
                    echo "<table width='100%'><tr><td class='img-td' align='center'><img src='".$img_url."' /></td>
                    <td class='des-td'>"; 
                    echo "<div class='art-name'>".utf8_encode($suggestions[$ps[0]][$i][art_name])."</div><br />";
                    if($suggestions[$ps[0]][$i][art_num] != ""){
                        echo "<div>Art: ".$suggestions[$ps[0]][$i][art_num]."</div>";
                    }
                    else{
                        echo "<div>&nbsp</div>";
                    }
                    echo "<div>".utf8_encode($suggestions[$ps[0]][$i][text])."</div>";
                    echo "</td><td class='price-td'>";
                    if($suggestions[$ps[0]][$i][price] > 0){
                        echo $suggestions[$ps[0]][$i][price]." SEK";
                    }
                    
                    echo "</td></tr></table>";
                echo "</div>";
                echo "<div class='sugg_no'>";
                
                //echo $link;
             ?>  
        
                <input type='button' value='NO' class='nej' onClick="parent.location='<?php echo $link; ?>'" /></div>
            <?php
                echo "</div>";
                echo "<div class='sp'></div>";
        }
    }
    ?>
    <table width="98%" align="center" border="0" style="margin: 0;">
        <tr><td align="left" class="row3" style="text-align: left"><div style="text-align: left">Additional sales in store today: 1.8%</div></td>
            <td align="center" class="row3">Scan item or use article number</td><td align="right" class="row3" style="text-align: right">Additional sales from this checkout today: 2.2%</td>
        </tr>
    </table>
    
    <div class="txt">
        <input type="text" name="input_box" id="input_box" />
    </div>    
    <input type="hidden" name="product-values" id="product-values" value="<?php echo htmlentities(serialize($ps)); ?>" />
    <input type="hidden" name="already_sent" id="already_sent" value="1" />
    <table align="center" style="margin-top: 10px"><tr><td><div id="button1"><input type="submit" name="button11" id="button11" value="Park" /></div></td><td><div id="button2"><input type="submit" name="button22" id="button22" value="Sell More" /></div></td></tr></table>
	
</div> 
</form>    
<?php
        }
        else{
            if(!empty($_SESSION['ps']))
                $_SESSION['ps'] = "";
            echo "<div style='width: 100%; border: 0px solid #000'>";
     ?>
<img src="images/invoice_sample_long_description_10.gif" class='receipt' /><br />

<input type="button" name="back_home" id="back_home" onClick="parent.location='<?php echo $path; ?>'" value="Next customer">
    <?php
            echo "</div>";
        }
    }
    else{
        echo "<p>opps form not submitted !!!</p>";    
        
    }
    //print_r($ps);
?>