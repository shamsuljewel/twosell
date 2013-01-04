<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//ini_set('default_charset','utf-8');

function __autoload($className) {
//require_once "./models/{$className}.php";
    require_once "./baseClasses/{$className}.php";
}

function getMapBetweenProductStore($productsInStore) {
    $mapProductStore = array();
    foreach ($productsInStore as $r) {
        if (!key_exists($r['product_id'], $mapProductStore))
            $mapProductStore[$r['product_id']] = array();
        $mapProductStore[$r['product_id']][] = $r['store_id'];
    }
    return $mapProductStore;
}

function main() {
    $productId = $_GET['productId'];

    $database = new DatabaseUtility();
    $utility = new Utility();
    
    $productsInStore = $database->getFieldsData('select product_id, store_id from twosell_productinstore where active = 1', array('product_id', 'store_id'));
    $productsInStore = getMapBetweenProductStore($productsInStore);
    
    //taking price infor for eleminating product having more price than allowed percent of target price
    $productPrices = $database->getFieldsData('SELECT b.product_id as productId, avg(a.price) as price FROM `twosell_pricinghistory` as a, twosell_productinstore as b WHERE a.`priced_product_id`=b.id and a.price>0 group by productId', array('productId', 'price'));
    $productPrices = $utility->mappingArray($productPrices, 'productId', 'price');
    //taking price infor for eleminating product having more price than allowed percent of target price end

    $query = 'SELECT a.match_id, b.title, a.store_id, a.score FROM recommender_two_productstorematch_item_group as a, twosell_product as b where a.main_id = "'.$productId.'" and a.match_id = b.id order by store_id, score desc';
    $fields = array('match_id', 'title', 'store_id', 'score');
    $data = $database->getFieldsData($query, $fields);
    
    foreach($data as $k=>$row){
        $price = key_exists($row['match_id'], $productPrices) ? $productPrices[$row['match_id']] : 0;
        $data[$k]['price'] = round($price,2);
        if(key_exists($row['match_id'], $productsInStore) && in_array($row['store_id'], $productsInStore[$row['match_id']]))
            $data[$k]['correct'] = 'yes';
        else
            $data[$k]['correct'] = 'no';
    }
    
    $table = new TableView();
    $headers = $colTitles = array('match_id', 'title', 'price', 'store_id', 'score', 'correct' );
    $title = 'Result';
    $table->showTable($data, $headers, $colTitles, $title, 'op',400); 
    
    
}

main();
?>
