<?php

include("menue.php");

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

$database = new DatabaseUtility();
$utility = new Utility();

$id = $_POST['id'];

$variation_id = $_POST['tsln_seasons'] .  $_POST['tsln_celebration_day']. $_POST['tsln_week'] . $_POST['tsln_days'] . $_POST['tsln_weather']. $_POST['tsln_temparature'];
//echo 'wdqwsdasds' . $variation_id;
if ($id > 0) {
    //print " ki khobor";
    $sql = "UPDATE tsln_variation_text SET variation_id ='" . $variation_id . "', variation_text='" . $_POST['variation_text'] . "' , group_ids = '" . $_POST['group_ids'] .
            "', store_ids='" . $_POST['store_ids'] . "', modified_date=now(), comments='" . $_POST['comments'] . "' WHERE id =" . $id;
//echo $sql;
    print "<br><br><br>";
    if ($database->executeQuery(utf8_decode($sql))) {
        print "Information is saved";
    } else {
        print "Information is not saved";
    }
} elseif ($id == "") {
    
    
    $resultMax = $database->getFieldsData("SELECT Max(id) as MaxID FROM tsln_variation_text", array('MaxID'));
    for ($j = 0; $j < sizeof($resultMax); $j++) {
        $MaxID = $resultMax[$j]['MaxID'];
       // ECHO $MaxID;
    }     
    $MaxID=$MaxID+1;
    
    $valuesNew = $MaxID . ", '" . $variation_id . "', '" . $_POST['variation_text'] . "', '" . $_POST['group_ids'] . "', '" . $_POST['store_ids'] . "', now(), now(), '" . $_POST['comments'] . "'";
    $sql = "INSERT INTO tsln_variation_text (Id, variation_id, variation_text, group_ids, store_ids, create_date, modified_date, comments) VALUES ($valuesNew)";

//print $sql;
 print "<br><br><br>";
    if ($database->executeQuery(utf8_decode($sql))) {
        print "Information is saved";
    } else {
        print "Information is not saved";
    }
}
?>
    <a href="list_variation.php"> Lista variation </a>    