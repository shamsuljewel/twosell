
<?php

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

$database = new DatabaseUtility();
$utility = new Utility();
// lista allt i  tsln_produkt_group
// om man inte anger vilken group_id så lista alla (det blir en väldigt lång lista men det är ok)
// Om man skickar med group_id = 1 listar man bara de produkter som har group_id = 1


//include('conn.php');
$sort = $_REQUEST['sort'];
if ($sort == "")
    $sort = " title";

// Toggle order in sorting
$order = $_REQUEST['order'];
if ($order == "") {
    $order = " ASC";
    $new_order = " DESC";
} elseif ($order == " DESC") {
    $new_order = " ASC";
} else {
    $new_order = " DESC";
}

$sql = "SELECT a.id as id, a.title as title, a.articlenum as articlenum, a.screen_text as screen_text, b.max_price as max_price FROM twosell_product as a, tsln_price_product as b WHERE a.id=b.product_id and a.title!='' and a.id not in (select product_id from tsln_product_group) order by " . $sort . $order;


//echo $sql . '<br><br>';

//$res = mysql_query($sql);
$products = $database->getFieldsData($sql, array('id', 'title', 'articlenum', 'screen_text', 'max_price'));
//print_r($products);


if ($sort == "id")
    $sortTep = "&order=" . $new_order;
if ($sort == "title")
    $sortTep = "&order=" . $new_order;
if ($sort == "articlenum")
    $sortTep = "&order=" . $new_order;
if ($sort == "screen_text")
    $sortTep = "&order=" . $new_order;
if ($sort == "max_price")
    $sortTep = "&order=" . $new_order;


$colomn = array();
$colomn[1] = 'Id';
$colomn[2] = 'Namn';
$colomn[3] = 'Artikelnr';
$colomn[4] = 'Skärmtext';
$colomn[5] = 'Högs pris
';


if (sizeof($products) > 0) {    
    $title = '';
    TableView::showTableTxt($products, array('id', 'title', 'articlenum', 'screen_text', 'max_price'), $colomn, $title, 'cbrB', 100000000, 1024);
}else
    echo '<br><br><p align="center"><b>Listan &auml;r tom </b> </p>';
?>

