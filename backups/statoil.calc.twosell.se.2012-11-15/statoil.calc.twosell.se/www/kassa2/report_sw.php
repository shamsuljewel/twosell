<?php
require_once 'conf_sw.php';
//echo $path;
if(!empty($_SESSION['ps']))
                $_SESSION['ps'] = "";
            echo "<div style='width: 100%; border: 0px solid #000'>";
     ?>
<img src="images/invoice_sample_long_description_10.gif" class='receipt' /><br />

<input type="button" name="back_home" id="back_home" onClick="window.location='<?php echo $path."index_sw.php"; ?>'" value="Klar, nästa kund">
<?php

            echo "</div>";
?>            