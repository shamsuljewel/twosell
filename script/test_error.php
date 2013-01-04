<?php

// error reporting on for error log file
error_reporting(E_ALL); 
// error log ON
ini_set('log_errors','1'); 
// display error NO
ini_set('display_errors','0'); 
// set the location of this script error log file
//$root = $_SERVER['DOCUMENT_ROOT']."/twosell";
ini_set('error_log', 'c:/xampp/htdocs/twosell/error_log/test_store_cashier.log');
set_error_handler("customError");

function customError($errno, $errstr, $errfile, $errline)
{
    
  error_log("Error: [$errno] $errstr on $errfile at line $errline", 0);
  die();
}
$nice = "ok";
echo $nice;

echo $hello;

echo "hi";
echo $alo;
?>
