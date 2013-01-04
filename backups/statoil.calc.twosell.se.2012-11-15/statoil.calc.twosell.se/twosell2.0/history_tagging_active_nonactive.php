<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

print "\n\n";
print "\n\n";
print "\n File history_tagging_active_nonactive is Started and date :" . date("Ymd G:i:s") . "\n";

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

function main() {
    $array_ini = parse_ini_file("baseClasses/config.ini", true);
    $functions = $array_ini['history_tagging_active_nonactive'];
    foreach ($functions as $function => $para) {
        print "running function: " . $function . "\n";
        call_user_func_array($function, $para);
        print "function: " . $function . " is done!!!\n";
    }
}

main();

function productPriceTotalSold() {
    print "\n\n";
    print "\n\n";
    print "\n Function productPriceTotalSold is Started and date :" . date("Ymd G:i:s") . "\n";
//completely backend work
    $database = new DatabaseUtility();
    $utility = new Utility();

    $database->executeQuery('Drop table tsln_price_product');
    $database->executeQuery('Create table tsln_price_product AS SELECT product_id, MAX(price) AS max_price FROM twosell_productinstore GROUP BY product_id');
    $database->executeQuery("CREATE INDEX tsln_price_product ON tsln_price_product (product_id, max_price)");

    $qAlter = "Alter table tsln_price_product ADD (lastdate date Not Null, totalsold decimal(60,2) Not Null)";

    if ($database->executeQuery($qAlter)) {
        echo "tsln_price_product Table is Altered\n";
    } else {
        echo "tsln_price_product Table is not Altered\n";
    }
    echo "tsln_price_product Table is started updating ..... \n";

    $date_sold = 'SELECT b.product_id as product_id, Max(a.`time_of_purchase`) as time, Sum(b.n_items) as noSolditem FROM twosell_purchase AS a, twosell_purchasedproduct AS b 
                        WHERE a.id = b.purchase_id group by b.product_id';
    $date_sold_data = $database->getFieldsData($date_sold, array('product_id', 'time', 'noSolditem'));
    $r = 0;
    $lastPercent_r = 0;
    $total_r = sizeof($date_sold_data);

    for ($y = 0; $y < sizeof($date_sold_data); $y++) {
        $query = "update tsln_price_product set lastdate = '" . $date_sold_data[$y]['time'] . "', totalsold='" . $date_sold_data[$y]['noSolditem'] . "'  Where product_id=" . $date_sold_data[$y]['product_id'];
//echo $query;
        $database->executeQuery($query);
        $r++;
        $lastPercent_r = $utility->progressBar("Update tsln_price_product is done & saved for : ", $r, $total_r, $lastPercent_r);
    }

    echo "tsln_price_product updating is finished..... \n";
}

function clusteringExcuteAllNew() {
    print "\n\n";
    print "\n\n";
    print "\n Function clusteringExcuteAllNew is Started and date :" . date("Ymd G:i:s") . "\n";
//completely backend work
    $database = new DatabaseUtility();
    $utility = new Utility();

    echo "Products grouping is started..... \n";
    $database->executeQuery('Drop table tsln_product_group_backup');
    $database->executeQuery('Create table tsln_product_group_backup as select * from tsln_product_group');
    //$database->executeQuery('delete from tsln_product_group');
    //$database->executeQuery('delete from tsln_group_suggestion');
    
     $database->executeQuery('Drop table tsln_product_group');
    $database->executeQuery('CREATE TABLE IF NOT EXISTS `tsln_product_group` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `group_id` int(11) NOT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `tsln_product_group` (`product_id`,`group_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;');


    $database->executeQuery('Drop table tsln_group_suggestion');
    $database->executeQuery('CREATE TABLE IF NOT EXISTS `tsln_group_suggestion` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `group_id` int(11) NOT NULL,
    `suggestion_group_id` int(11) NOT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `tsln_group_suggestion` (`group_id`,`suggestion_group_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');
    
    $database->executeQuery("UPDATE tsln_meta_groups SET status=0, number_of_members = 0, totalsold =0");

    $groups = $database->getFieldsData("select meta_group_id, group_name, keyword_include_1,keyword_exclude_1,keyword_include_2,keyword_exclude_2, keyword_include_3, keyword_exclude_3, exclude_prod_items, include_prod_items, price_min, price_max, group_relation_top_manual, group_relation_manual_ok from tsln_meta_groups where status = 0", array('meta_group_id', 'group_name', 'keyword_include_1', 'keyword_exclude_1', 'keyword_include_2', 'keyword_exclude_2', 'keyword_include_3', 'keyword_exclude_3', 'exclude_prod_items', 'include_prod_items', 'price_min', 'price_max', 'group_relation_top_manual', 'group_relation_manual_ok'));
//print_r($groups); exit();
    $p = 0;
    $lastPercent = 0;
    $total = sizeof($groups);
    for ($j = 0; $j < sizeof($groups); $j++) {

        if ($groups[$j]['keyword_include_1'] != '') {
            $query = $utility->findMemberProducts($groups, $j, 'keyword_exclude_1', 'keyword_include_1');
//echo $query; exit();
            $products = $database->getFieldsData($query, array('id', 'title'));
            for ($k = 0; $k < sizeof($products); $k++) {
                $query = "insert into tsln_product_group(product_id, group_id) values(" . $products[$k]['id'] . "," . $groups[$j]['meta_group_id'] . ")";
//echo $query ; exit();
                $database->executeQuery($query);
            }
        }


        if ($groups[$j]['keyword_include_2'] != '') {
            $query = $utility->findMemberProducts($groups, $j, 'keyword_exclude_2', 'keyword_include_2');
//echo $query; exit();
            $products = $database->getFieldsData($query, array('id', 'title'));
            for ($k = 0; $k < sizeof($products); $k++) {
                $query = "insert into tsln_product_group(product_id, group_id) values(" . $products[$k]['id'] . "," . $groups[$j]['meta_group_id'] . ")";
//echo $query ;
                $database->executeQuery($query);
            }
        }

        if ($groups[$j]['keyword_include_3'] != '') {
            $query = $utility->findMemberProducts($groups, $j, 'keyword_exclude_3', 'keyword_include_3');
//echo $query; exit();
            $products = $database->getFieldsData($query, array('id', 'title'));
            for ($k = 0; $k < sizeof($products); $k++) {
                $query = "insert into tsln_product_group(product_id, group_id) values(" . $products[$k]['id'] . "," . $groups[$j]['meta_group_id'] . ")";
//echo $query ;
                $database->executeQuery($query);
            }
        }


        if ($groups[$j]['exclude_prod_items'] != '') {
            $productId = explode(';', strtolower($groups[$j]['exclude_prod_items']));
            for ($m = 0; $m < sizeof($productId); $m++) {
                $database->executeQuery('delete from tsln_product_group WHERE product_id =' . $productId[$m] . ' and group_id =' . $groups[$j]['meta_group_id']);
            }
        }

        if ($groups[$j]['include_prod_items'] != '') {
            $productId = explode(';', strtolower($groups[$j]['include_prod_items']));
            for ($m = 0; $m < sizeof($productId); $m++) {
                $database->executeQuery('insert into tsln_product_group(product_id, group_id) values(' . $productId[$m] . ',' . $groups[$j]['meta_group_id'] . ')');
            }
        }

        $productsfinal = $database->getFieldsData('Select product_id from tsln_product_group WHERE group_id =' . $groups[$j]['meta_group_id'], array('product_id'));



       if (($groups[$j]['group_relation_top_manual']!='') && ($groups[$j]['group_relation_manual_ok'])==1){
            $suggssionids = explode(';', strtolower($groups[$j]['group_relation_top_manual']));
            for ($m = 0; $m < sizeof($suggssionids); $m++) {
            //echo 'insert into tsln_group_suggestion(group_id, suggestion_group_id) values(' . $groups[$j]['meta_group_id']. ',' . $suggssionids[$m] . ')';
                $database->executeQuery('insert into tsln_group_suggestion(group_id, suggestion_group_id) values(' . $groups[$j]['meta_group_id'] . ',' . $suggssionids[$m] . ')');
            }
        
       }

        $totalsold = 0;
        $totalsoldGroup = $database->getFieldsData('SELECT sum(`totalsold`) as totalsold FROM `tsln_price_product` where `product_id` in (select `product_id` from tsln_product_group where group_id=' . $groups[$j]['meta_group_id'] . ')', array('totalsold'));
        //print_r($totalsoldGroup);

        for ($q = 0; $q < sizeof($totalsoldGroup); $q++) {
            $totalsold = $totalsoldGroup[$q]['totalsold'];
        }


        $queryUp = "update tsln_meta_groups set status = 1, latest_calculation_dattime= now(), number_of_members = " . sizeof($productsfinal) . ", totalsold =" . $totalsold . " WHERE meta_group_id =" . $groups[$j]['meta_group_id'];
//echo $query;exit();
        $database->executeQuery($queryUp);

        $p++;
        $lastPercent = $utility->progressBar("Product grouping is done & saved for : ", $p, $total, $lastPercent);
    }

    echo "execution done \n\n";
}


function productGASPriceChanged() {
    print "\n\n";
    print "\n\n";
    print "\n Function productGASPriceChanged is Started and date :" . date("Ymd G:i:s") . "\n";
//completely backend work
    $database = new DatabaseUtility();
    $utility = new Utility();

    echo "tsln_price_product Table is started updating ..... \n";

    $date_sold = 'SELECT b.product_id as product_id, b.max_price as max_price FROM  tsln_product_group as a, tsln_price_product as b  WHERE a.product_id=b.product_id and a.group_id=106';
    $date_sold_data = $database->getFieldsData($date_sold, array('product_id', 'max_price'));
    $r = 0;
    $lastPercent_r = 0;
    $total_r = sizeof($date_sold_data);

    for ($y = 0; $y < sizeof($date_sold_data); $y++) {
        $query = "update tsln_price_product set max_price = " .$date_sold_data[$y]['max_price']*20 ." Where product_id=" . $date_sold_data[$y]['product_id'];
//echo $query; exit();
        $database->executeQuery($query);
        $r++;
        $lastPercent_r = $utility->progressBar("Update tsln_price_product is done & saved for : ", $r, $total_r, $lastPercent_r);
    }

    echo "tsln_price_product updating is finished..... \n";
}

function changeProductsGroup() {
    print "\n\n";
    print "\n\n";
    print "\n Function changeProductsGroup is Started and date :" . date("Ymd G:i:s") . "\n";
//completely backend work
    $database = new DatabaseUtility();
    $utility = new Utility();
    $productsfinal = $database->getFieldData('Select Distinct product_id from tsln_product_group order by product_id', 'product_id');
    $productsprevious = $database->getFieldData('Select Distinct product_id from tsln_product_group_backup order by product_id', 'product_id');
    $addProducts = array_diff($productsfinal, $productsprevious);
    $delProducts = array_diff($productsprevious, $productsfinal);
    //print_r(sizeof($delProducts));
    //exit();

    $delToIns = $database->getFieldsData('Select product_id, group_id  from tsln_product_group_backup where product_id in (' . implode(",", $delProducts) . ')', array('product_id', 'group_id'));
    $addToIns = $database->getFieldsData('Select product_id, group_id  from tsln_product_group where product_id in (' . implode(",", $addProducts) . ')', array('product_id', 'group_id'));

    //print_r(sizeof($addToIns));
    //exit();
    for ($q = 0; $q < sizeof($addToIns); $q++) {
        //echo 'insert into tsln_product_group_changes(product_id, group_id, changedate, whatChange) values(' . $addToIns[$q]['product_id'] . ',' . $addToIns[$q]['group_id'] . ', now(), 1)';
        //exit();
        $database->executeQuery('insert into tsln_product_group_changes(product_id, group_id, changedate, whatChange) values(' . $addToIns[$q]['product_id'] . ',' . $addToIns[$q]['group_id'] . ', now(), 1)');
    }
    for ($b = 0; $b < sizeof($delToIns); $b++) {
        //echo 'insert into tsln_product_group_changes(product_id, group_id, changedate, whatChange) values(' . $delToIns[$b]['product_id'] . ',' . $delToIns[$b]['group_id'] . ', now(), 2)';
        $database->executeQuery('insert into tsln_product_group_changes(product_id, group_id, changedate, whatChange) values(' . $delToIns[$b]['product_id'] . ',' . $delToIns[$b]['group_id'] . ', now(), 2)');
    }
}

// In make producthistory function add this line in the queary ::: and c.title!='' and c.do_not_trigger_offer=0 and c.never_offer_direct=0  and add delete queary


function makeProductHistory() {
    print "\n\n";
    print "\n\n";
    print "\n Function makeProductHistory is Started and date :" . date("Ymd G:i:s") . "\n";
    $databaseUtility = new DatabaseUtility();
    $utility = new Utility();
    
    
    $thReceipt = $databaseUtility->getFieldsData("SELECT MinRange, MaxRange FROM recommender_two_algorithmsettings where FeatureName='n_rows'", array('MinRange', 'MaxRange'));
    //echo "SELECT MinRange, MaxRange FROM recommender_two_algorithmsettings where FeatureName='n_rows'";
    //print_r($thReceipt);
    $qdrop = "Drop table tsl_producthistory";

    if ($databaseUtility->executeQuery($qdrop)) {
        echo "tsl_producthistory Table is droped\n";
    } else {
        echo "tsl_producthistory Table is not droped\n";
    }
// and (a.`time_of_purchase` between '2012-02-11' and '2012-04-11')
    $qAll = "Create table tsl_producthistory as
  SELECT a.`time_of_purchase` as time, b.`total_cost` as price, c.`title` as name, c.`articlenum` as slno, b.n_items
  as noSolditem, c.id as productId, b.purchase_id as purchaseID
  FROM `twosell_purchase` AS a, twosell_purchasedproduct AS b, twosell_product AS c
  WHERE a.id = b.purchase_id AND b.product_id = c.id and b.final = 1 and (a.n_rows between " . $thReceipt[0]['MinRange'] . " and " . $thReceipt[0]['MaxRange'] . " ) and c.title!='' and (c.do_not_trigger_offer=0 or c.never_offer_direct=0)";
    
   // echo $qAll; exit();
    
    if ($databaseUtility->executeQuery($qAll)) {
        echo "tsl_producthistory Table is created\n";
    } else {
        echo "tsl_producthistory Table is not created\n";
    }



    $qIndexed = "CREATE INDEX tsl_producthistory ON tsl_producthistory (productId, name)";
    if ($databaseUtility->executeQuery($qIndexed)) {
        echo "Table is Indexed \n";
    } else {
        echo "Table is not Indexed \n";
    }


///added delete query in order to remove a group of products which even not consedered for Twosell calculation////////////

    $qDelete = "Delete FROM tsl_producthistory where productId in (select a.product_id as id from tsln_product_group as a, tsln_meta_groups as b
  WHERE a.group_Id = b.meta_group_id and b.never_suggest='*')";
    if ($databaseUtility->executeQuery($qDelete)) {
        echo "Bolcked products are deleted\n";
    } else {
        echo "Bolcked products are  not deleted \n";
    }
/////////////////done /////////////////////////
}

function makeProductTagging() {

    print "\n\n";
    print "\n\n";
    print "\n Function makeProductTagging is Started and date :" . date("Ymd G:i:s") . "\n";
    $databaseUtility = new DatabaseUtility();
    $utility = new Utility();

// create table for Product taging

    $qdrop = "Drop table tsl_producttaging";

    if ($databaseUtility->executeQuery($qdrop)) {
        echo "tsl_producttaging Table is droped\n";
    } else {
        echo "tsl_producttaging Table is not droped\n";
    }

    $qAll = "CREATE TABLE  `tsl_producttaging` (
  `productId` INT( 11 ) NOT NULL ,
  `name` VARCHAR( 300 ) NOT NULL ,
  `itemName` VARCHAR( 300 ) NOT NULL ,
  `brandName` VARCHAR( 300 ) NOT NULL ,
  `colorName` VARCHAR( 300 ) NOT NULL ,
  `attributeName` VARCHAR( 300 ) NOT NULL ,
  `otherName` VARCHAR( 300 ) NOT NULL ,
  `status` INT( 11 ) NOT NULL ,
  `comment` VARCHAR( 300 ) NOT NULL,
  `priceMean` DECIMAL( 9,2 ) NOT NULL,
  `priceStddev` DECIMAL( 9,2 ) NOT NULL,
  productgroup_id INT( 11 )
  ) ";
    if ($databaseUtility->executeQuery($qAll)) {
        echo "Table is created\n";
    } else {
        echo "Table is not created\n";
    }

    $qIndexed = "CREATE INDEX tsl_producttaging ON tsl_producttaging (productId, name, status, priceMean, productgroup_id)";
    if ($databaseUtility->executeQuery($qIndexed)) {
        echo "Table is Indexed \n";
    } else {
        echo "Table is not Indexed \n";
    }

///Code for insert products taging with Features ////////////
    print "\nTagging is starting.....\n";

    $query = 'SELECT BrandsName FROM tsl_brands';
    $brandNames = $databaseUtility->getFieldData($query, 'BrandsName');
    $query = 'SELECT ColorName FROM tsl_colors';
    $colorNames = $databaseUtility->getFieldData($query, 'ColorName');
    $query = 'SELECT attribute FROM tsl_attribute';
    $itemAttributes = $databaseUtility->getFieldData($query, 'attribute');

    $checkActive = "SELECT Distinct productId, `name` FROM `tsl_producthistory` where name!=''";
    $ProductNames = $databaseUtility->getFieldsData($checkActive, array('productId', 'name'));
    $ProductNames = array_map("unserialize", array_unique(array_map("serialize", $ProductNames)));

    $data = array();
    $utility = new Utility();
//for the progress bar
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($ProductNames);
//end for the progress bar
    foreach ($ProductNames as $ProductName) {
        $name = $ProductName['name'];
        $productId = $ProductName['productId'];
        $group_names = $databaseUtility->getFieldsData("select meta_group_id, group_name from tsln_meta_groups as a, tsln_product_group as b where b.group_id=a.meta_group_id and b.product_id=" . $productId, array('meta_group_id', 'group_name'));
        $item = '';
        $GroupId = 0;
//print_r($group_names);exit();
        for ($j = 0; $j < sizeof($group_names); $j++) {
            if ($group_names[$j]['group_name'] != '') {
                $item = $group_names[$j]['group_name'];
                $GroupId = $group_names[$j]['meta_group_id'];
            }
        }
//$productGroupId = $ProductName['productgroup_id'];
        $tagPnonProduct = $utility->onTaging($name, $brandNames, $colorNames, $itemAttributes, $productId);
        $insQuery = "INSERT INTO tsl_producttaging(productId, name, itemName, brandName, colorName, attributeName, otherName, status, comment, productgroup_id )
  VALUES(" . $productId . ",'" . $name . "','" . $item . "','" . $tagPnonProduct['brand'] . "','" . $tagPnonProduct['color'] . "','" . $tagPnonProduct['attributes']
                . "','" . trim($tagPnonProduct['other']) . "', 0 ,''," . $GroupId . ")";

        $databaseUtility->executeQuery($insQuery);
//for the progress bar
        $i++;
        $lastPercent = $utility->progressBar("Tagging is done & saved for : ", $i, $total, $lastPercent);
//end for the progress bar
    }
    print "\nTagging is done!!!\nPrice and service updating.....\n";


//insertion of price mean and stddev from producthistory to producttagig table
//select productId,price from tsl_producthistory where price>0

    $query = 'SELECT b.product_id as productId, a.`price` as price FROM `twosell_pricinghistory` as a, twosell_productinstore as b WHERE a.`priced_product_id`=b.id and a.price>0';
    $fieldNames = array('productId', 'price');
    $producthistory = $databaseUtility->getFieldsData($query, $fieldNames);
    $producthistory = array_map("unserialize", array_unique(array_map("serialize", $producthistory)));
    $productMultiplePrices = array();
    foreach ($producthistory as $product) {
//print_r($product); exit();
        if (!key_exists($product['productId'], $productMultiplePrices))
            $productMultiplePrices[$product['productId']] = array();
        $productMultiplePrices[$product['productId']][] = $product['price'];
    }
    foreach ($productMultiplePrices as $key => $productMultiplePrice) {
        $productMultiplePrices[$key] = standard_deviation($productMultiplePrice);
    }
//for the progress bar
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($productMultiplePrices);
//end for the progress bar
    foreach ($productMultiplePrices as $key => $productMultiplePrice) {
        $query = 'update tsl_producttaging set priceMean = ' . $productMultiplePrice['mean'] .
                ' , priceStddev = ' . $productMultiplePrice['stddev'] .
                ' where productId = ' . $key;
        $databaseUtility->executeQuery($query);
//for the progress bar
        $i++;
        $lastPercent = $utility->progressBar("Prices are updating & saved for : ", $i, $total, $lastPercent);
//end for the progress bar
    }
    print "\nPrice updating is done!!!\n";
//end of insertion of price mean and stddev from producthistory to producttagig table
}

function standard_deviation($aValues, $bSample = false) {
    $fMean = array_sum($aValues) / count($aValues);
    $fVariance = 0.0;
    foreach ($aValues as $i) {
        $fVariance += pow($i - $fMean, 2);
    }
    $fVariance /= ( $bSample ? count($aValues) - 1 : count($aValues) );
    $t['mean'] = $fMean;
    $t['stddev'] = (float) sqrt($fVariance);
    return $t;
}

function mappingArray($array, $key, $value) {
    $mappingArray = array();
    foreach ($array as $elements) {
        $mappingArray[$elements[$key]] = $elements[$value];
    }
    return $mappingArray;
}
/*
function makeProductActiveNotActive() {
    print "\n\n";
    print "\n\n";
    print "\n Function makeProductActiveNotActive is Started and date :" . date("Ymd G:i:s") . "\n";

///////////code for replacement non active product with active using puslista
    $databaseUtility = new DatabaseUtility();
    $utility = new Utility();



    $discons = $databaseUtility->getFieldsData('select artikelnr, ersattav from tsl_discontinued where ersattav !=""', array('artikelnr', 'ersattav'));
    $discons = mappingArray($discons, 'artikelnr', 'ersattav');

    $products = $databaseUtility->getFieldsData('select articlenum, id from twosell_product ', array('articlenum', 'id'));
    $products = mappingArray($products, 'articlenum', 'id');

    $recommendationPushlista = array();
    foreach ($discons as $articlenum => $replaceArticlenum) {
        if (array_key_exists($articlenum, $products) && array_key_exists($replaceArticlenum, $products))
            $recommendationPushlista[$products[$articlenum]] = $products[$replaceArticlenum];
    }

    $qdrop = "Drop table tsl_recommendationPushlista";

    if ($databaseUtility->executeQuery($qdrop)) {
        echo "Table tsl_recommendationPushlista is droped\n";
    } else {
        echo "Table tsl_recommendationPushlista is not droped\n";
    }

    $qAll = "CREATE TABLE  `tsl_recommendationPushlista` (
  nonActive INT( 11 ) NOT NULL ,
  active INT( 11 ) NOT NULL
  )";
    if ($databaseUtility->executeQuery($qAll)) {
        echo "Table tsl_recommendationPushlista is created\n";
    } else {
        echo "Table tsl_recommendationPushlista is not created\n";
    }


    $qIndexed = "CREATE INDEX tsl_recommendationPushlista ON tsl_recommendationPushlista (nonActive, active)";
    if ($databaseUtility->executeQuery($qIndexed)) {
        echo "Table is Indexed \n";
    } else {
        echo "Table is not Indexed \n";
    }
    echo "recommendationPushlista is started \n";
//for the progress bar
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($recommendationPushlista);
//end for the progress bar
    foreach ($recommendationPushlista as $nonActive => $active) {
        $query = 'insert into tsl_recommendationPushlista values(' . $nonActive . ',' . $active . ')';
        $databaseUtility->executeQuery($query);
//for the progress bar
        $i++;
        $lastPercent = $utility->progressBar("recommendationPushlista is done & saved for : ", $i, $total, $lastPercent);
//end for the progress bar
    }
    print "\nrecommendationPushlista updating is done!!!\n";

    print "\nupdate product as actve using pulsita suggession is started!!!\n";

    $checkActive = "select productId from tsl_producthistory where name != '' and productId in (select active from tsl_recommendationPushlista)";
    $ids = $databaseUtility->getFieldData($checkActive, 'productId');
    $ids = array_unique($ids);
//for the progress bar
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($ids);
//end for the progress bar
    foreach ($ids as $id) {

        $insQuery = "Update tsl_producttaging Set status = 1,
  comment ='According to discontinued product list from puslist' where productId=" . $id;

        $databaseUtility->executeQuery($insQuery);
//for the progress bar
        $i++;
        $lastPercent = $utility->progressBar("ids is done & saved for : ", $i, $total, $lastPercent);
//end for the progress bar
    }
    print "\nids updating is done!!!\n";

///////code for updating products as nonactive (status=2) but don't need any replacement , previously status was 0 //

    $checkActive = "select productId from tsl_producthistory where name != '' and productId in (select nonActive from tsl_recommendationPushlista)";
    $ids = $databaseUtility->getFieldData($checkActive, 'productId');
    $ids = array_unique($ids);
//for the progress bar
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($ids);
//end for the progress bar
    foreach ($ids as $id) {

        $insQuery = "Update tsl_producttaging Set status = 2,
  comment ='According to discontinued product list from puslist' where productId=" . $id;

        $databaseUtility->executeQuery($insQuery);
//for the progress bar
        $i++;
        $lastPercent = $utility->progressBar("ids updating is done according to discontinued product list from puslist & saved for : ", $i, $total, $lastPercent);
//end for the progress bar
    }
    print "\nids updating is done according to discontinued product list from puslist!!!\n";

///////code for updating products as active (status=1) using discontinuted product list, previously status was 0 //

    $checkActive = "select productId from tsl_producthistory where name != '' and slno not in (SELECT artikelnr FROM `tsl_discontinued`)";
    $ids = $databaseUtility->getFieldData($checkActive, 'productId');
    $ids = array_unique($ids);
//for the progress bar
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($ids);
//end for the progress bar
    foreach ($ids as $id) {

        $insQuery = "Update tsl_producttaging Set status = 1, comment ='According to discontinued product list from puslist' where productId=" . $id;

        $databaseUtility->executeQuery($insQuery);
//for the progress bar
        $i++;
        $lastPercent = $utility->progressBar("ids updating according to discontinued product list from puslist is done & saved for : ", $i, $total, $lastPercent);
//end for the progress bar
    }
    print "\nids updating is done according to discontinued product list from puslist!!!\n";
///////code for updating products as non active from the active list if it found in the twosell_productinstore, previously status was 1//time duration time >= '2008 -01-01

    $checkActive = "select product_Id from twosell_productinstore where active=0";
    $ids = $databaseUtility->getFieldData($checkActive, 'product_Id');
    $ids = array_unique($ids);
//for the progress bar
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($ids);
//end for the progress bar
    foreach ($ids as $id) {

        $insQuery = "Update tsl_producttaging Set status = 0,
  comment ='According to status in twosell_productinstore' where productId=" . $id;

        $databaseUtility->executeQuery($insQuery);
//for the progress bar
        $i++;
        $lastPercent = $utility->progressBar("ids updating is done according to status in twosell_productinstore & saved for : ", $i, $total, $lastPercent);
//end for the progress bar
    }
    print "\nids updating is done according to status in twosell_productinstore!!!\n";
}
 * 
 */
?>

