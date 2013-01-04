<?php //header('Content-type: html; charset=utf-8');     ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script src="js/jquery-1.5.2.min.js" language="javascript" type="text/javascript"></script>
<script src="js/scripting.js" language="javascript" type="text/javascript"></script>

<?php
ini_set('memory_limit','1024M');
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
    $productList = $database->getFieldsData('select id, concat(title," (",articlenum,")") as title from twosell_product', array('id', 'title'));
    //taking price infor for eleminating product having more price than allowed percent of target price
    $productPrices = $database->getFieldsData('SELECT product_id as productId, max_price as price FROM tsln_price_product', array('productId', 'price'));
    $productPrices = $utility->mappingArray($productPrices, 'productId', 'price');
    //taking price infor for eleminating product having more price than allowed percent of target price end
    foreach($productList as $k=>$row){
        $price = key_exists($row['id'], $productPrices) ? $productPrices[$row['id']] : 0;
        $productList[$k]['price'] = round($price,2);
        
    }
    
    $table = new TableView();
    $headers = array('id', 'title', 'price');
    $colTitles = array('Id', 'Title', 'Max_Price');
    $table->showTable($productList, $headers, $colTitles, 'Product List', 'productList', 800, 400); ///// change the table size
    echo '</div><div id="result" style="margin-left:460px;"></div>';
}

main();
?>
