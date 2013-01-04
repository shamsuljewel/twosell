<?php 
    session_start();
  
    //print_r($images);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="css/styles_pos.css" />
<script src="js/jquery-1.7.1.js" type="text/javascript"></script>
<script type="text/javascript" src="js/custom.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
</head>

<body>
<div id="wrapper">
    
    <div  id="main-content">
<!--        <div id="header">
            <div id="left-header"><p style="margin: 20px 20px;">Twosell Testkassa System</p></div>
            <div id="right-header"><img src="images/twosellbypocada.png" width="163" height="34" style="margin-top: 20px; float:right;" /></div>
        </div>-->
        <div id="load">
            <?php 
                
                if($_GET[page] == "suggestions") require_once 'suggestion_sw.php';
                else if($_GET[page] == "report") require_once 'report_sw.php'; 
                else require_once 'home_sw.php';
               
            ?>
            
        </div>    
        <div class="img-bottom" align="right"><img src="images/twosellbypocada.png" class="img-bottom-gap" /></div>
        <div class="bottom_border"></div>    
        
    </div>
     <div align="left"> <img border="0"  weight="70px" height="80px"  src="images/<?php echo $bottom_logo2; ?>" />&nbsp;&nbsp;&nbsp;<img  weight="60px" height="70px"  src="images/<?php echo $bottom_logo3; ?>"/> </div>
</div>
</body>
</html>
