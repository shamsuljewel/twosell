<?php
require_once 'conf.php';
//echo $path;
if(!empty($_SESSION['ps']))
                $_SESSION['ps'] = "";
            echo "<div style='width: 100%; border: 0px solid #000'>";
     ?>
<img src="images/invoice_sample_long_description_10.gif" class='receipt' /><br />

<input id="back_home" type="button" name="back_home"  onClick="window.location='<?php echo $path; ?>'" value="Next customer">
<?php

            echo "</div>";
?>            