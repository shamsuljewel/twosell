<?php

include '../functions/commonFunction.php';
$d1 = "2012-10-27";
$d2 = "2012-10-27";
$diff = dateTimeDiff($d1, $d2);
$diffday = dateDiff($d1, $d2);
echo $diffday;
//$d2 = date('Y-m-d', strtotime('+1 days',  strtotime($d1)));
//echo $d2;
?>
