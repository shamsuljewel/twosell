<?php //header('Content-type: html; charset=utf-8');     ?>
<script src="js/jquery-1.5.2.min.js" language="javascript" type="text/javascript"></script>
<script src="js/scripting.js" language="javascript" type="text/javascript"></script>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//header ('Content-type: html; charset=utf-8');
//mb_internal_encoding('utf-8');
//ini_set('default_charset', 'utf-8');

//ini_set('default_mimetype','text/html');
function __autoload($className) {
//require_once "./models/{$className}.php";
    require_once "./baseClasses/{$className}.php";
}

function main() {
    $database = new DatabaseUtility();
    $utility = new Utility();
    echo '<div style="float:left;">';
    $productList = $database->getFieldsData('select distinct itemName from tsl_producttaging where itemName not in ("","service") order by itemName', array('itemName'));
        
    $table = new TableView();
    $headers = array('itemName');
    $colTitles = array('itemName');
    $table->showTable($productList, $headers, $colTitles, 'Product List', 'productListItemGroup', 400, 200);
    echo '</div><div id="result" style="margin-left:320px;"></div>';
}

main();
?>
