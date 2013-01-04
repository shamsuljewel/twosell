<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

include("menue.php");


echo "Execution started .... <br><br> ";
echo "Do not close this window until calculation is finished. <br><br> ";
//completely backend work
$database = new DatabaseUtility();
$utility = new Utility();

$database->executeQuery('Drop table tsln_price_product');
$database->executeQuery('Create table tsln_price_product AS SELECT product_id, MAX(price) AS max_price FROM twosell_productinstore GROUP BY  product_id');
$database->executeQuery("CREATE INDEX tsln_price_product ON tsln_price_product (product_id, max_price)");

$qAlter = "Alter table tsln_price_product ADD (lastdate date Not Null, totalsold decimal(60,2) Not Null)";

if ($database->executeQuery($qAlter)) {
    echo "tsln_price_product Table is Altered\n";
} else {
    echo "tsln_price_product Table is not Altered\n";
}

$date_sold = 'SELECT b.product_id as product_id, Max(a.`time_of_purchase`) as time, Sum(b.n_items) as noSolditem FROM twosell_purchase AS a, twosell_purchasedproduct AS b 
                        WHERE a.id = b.purchase_id group by b.product_id';
$date_sold_data = $database->getFieldsData($date_sold, array('product_id', 'time', 'noSolditem'));
for ($y = 0; $y < sizeof($date_sold_data); $y++) {
    $query = "update tsln_price_product set lastdate = '" . $date_sold_data[$y]['time'] . "', totalsold='" . $date_sold_data[$y]['noSolditem'] . "'  Where product_id=" . $date_sold_data[$y]['product_id'];
    $database->executeQuery($query);
}

$database->executeQuery('delete from tsln_product_group');
$database->executeQuery('delete from tsln_group_suggestion');
$database->executeQuery("UPDATE tsln_meta_groups SET status=0, number_of_members = 0, totalsold =0");

 $groups = $database->getFieldsData("select meta_group_id, group_name, keyword_include_1,keyword_exclude_1,keyword_include_2,keyword_exclude_2, keyword_include_3, keyword_exclude_3, exclude_prod_items, include_prod_items, price_min, price_max, group_relation_top_manual, group_relation_manual_ok from tsln_meta_groups where status = 0", array('meta_group_id', 'group_name', 'keyword_include_1', 'keyword_exclude_1', 'keyword_include_2', 'keyword_exclude_2', 'keyword_include_3', 'keyword_exclude_3', 'exclude_prod_items', 'include_prod_items', 'price_min', 'price_max', 'group_relation_top_manual', 'group_relation_manual_ok'));
//print_r($groups); exit();

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


    $queryUP = "update tsln_meta_groups set status = 1, latest_calculation_dattime= now(), number_of_members = " . sizeof($productsfinal) . ", totalsold =" . $totalsold . " WHERE meta_group_id =" . $groups[$j]['meta_group_id'];
//echo $query;exit();
    $database->executeQuery($queryUP);
}


echo ">> Execution done <br><br>Close this window and return to group admin.<br>
    Reload group list to se new 'Antal' and new 'Senast beräknad'";
