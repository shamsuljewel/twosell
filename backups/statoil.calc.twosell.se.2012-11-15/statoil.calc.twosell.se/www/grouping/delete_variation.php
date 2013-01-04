<?php

include("menue.php");

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

$database = new DatabaseUtility();

$id = $_GET['id'];
//echo 'wdqwsdasds' . $id;
if ($id > 0) {
    //print " ki khobor";
    $sql = "Delete from tsln_variation_text WHERE id =" . $id;
//echo $sql;
    print "<br><br><br>";
    if ($database->executeQuery(utf8_decode($sql))) {
        print "Information is deleted";
    } else {
        print "Information is not deleted";
    }
} 
?>
    <a href="list_variation.php"> Lista variation </a>