<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//ini_set('default_charset','utf-8');
print "\n\n";
print "\n\n";
print "\n File Matrix is Started and date :" . date("Ymd G:i:s") . "\n";

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

function main() {
    $array_ini = parse_ini_file("baseClasses/config.ini", true);
    $functions = $array_ini['matrix2_0'];
    foreach ($functions as $function => $para) {
        print "running function: " . $function . "\n";
        call_user_func_array($function, $para);
        print "function: " . $function . " is done!!!\n";
    }
}

main();

function getReciepts($producthistory) {
    $reciepts = array();
    foreach ($producthistory as $product) {
//creating reciept
        if (!array_key_exists($product['purchaseID'], $reciepts))
            $reciepts[$product['purchaseID']] = array();
        $reciepts[$product['purchaseID']][] = $product['productId'];
    }
    return $reciepts;
}

function getCount($reciepts, $products) {
//both $product and $group is mapping array 
//intializing by zero
    foreach ($products as $key => $val)
        $products[$key] = 0;
    $arrayCount = array();
    foreach ($reciepts as $reciept) {
//product,group count and single recipt
        if (sizeof($reciept) < 2)
            continue; //reciept contains only one product should not be considered
        foreach ($reciept as $p) {
            $products[$p]++;
        }
    }
    return $products;
}

function mergeP_PandPG_P($matrixP_P, $matrixPG_P, $numberOfRecieptInSubjectObject) {
    $utility = new Utility();
    $matrixP_P = $utility->multipleKeyArrySort($matrixP_P, 'prob', true);
    $matrixPG_P = $utility->multipleKeyArrySort($matrixPG_P, 'prob', true);
    $matrixPG_P_Plus = array_merge($matrixP_P, $matrixPG_P);

    $len = sizeof($matrixPG_P_Plus);

    for ($i = 0; $i < $len - 1; $i++) {
        if (!key_exists($i, $matrixPG_P_Plus))
            continue;
        for ($j = $i + 1; $j < $len; $j++) {
            if (!key_exists($j, $matrixPG_P_Plus) || $matrixPG_P_Plus[$j]['group'] == 0)
                continue;
            if ($matrixPG_P_Plus[$i]['group'] == $matrixPG_P_Plus[$j]['group']) {
                $matrixPG_P_Plus[$i]['obj'] += $matrixPG_P_Plus[$j]['obj'];
                $matrixPG_P_Plus[$i]['pred'] += $matrixPG_P_Plus[$j]['pred'];
                unset($matrixPG_P_Plus[$j]);
            }
        }
    }

    $total_prob = 0;
    foreach ($matrixPG_P_Plus as $k => $r) {
        $matrixPG_P_Plus[$k]['prob_obj'] = $r['pred'] / $r['obj'];
        $matrixPG_P_Plus[$k]['prob_pred'] = $r['pred'] / $numberOfRecieptInSubjectObject;
        $matrixPG_P_Plus[$k]['prob_obj_mul_pred'] = $matrixPG_P_Plus[$k]['prob_obj'] * $matrixPG_P_Plus[$k]['prob_pred'];
        $total_prob += $matrixPG_P_Plus[$k]['prob_obj_mul_pred'];
    }
    foreach ($matrixPG_P_Plus as $k => $v) {
        $matrixPG_P_Plus[$k]['prob'] = $matrixPG_P_Plus[$k]['prob_obj_mul_pred'] / $total_prob;
    }

    $matrixPG_P_Plus = $utility->multipleKeyArrySort($matrixPG_P_Plus, 'prob', true);
    return $matrixPG_P_Plus;
}

function getAllowedPricePercentage($subjectPrice, $AllowedPricePercentage) {
    $percent = 100;
    foreach ($AllowedPricePercentage as $price) {
        if ($subjectPrice >= $price['MinRange'] && $subjectPrice <= $price['MaxRange'])
            return $price['Value'];
    }
    return $percent;
}

// if ($count == 10) changed 20 from 10 to increase number of suggession
function easyMergeP_PandPG_P($matrixP_P, $matrixPG_P, $productPrices, $AllowedPricePercentage, $productId, $DuplicateProductNameSimilarity) {
    $mergedMatrix = array();
    $utility = new Utility();
    $matrixP_P = $utility->multipleKeyArrySort($matrixP_P, 'prob', true);
    $matrixPG_P = $utility->multipleKeyArrySort($matrixPG_P, 'prob', true);
    $count = 0;
    $current_group = array();
    $names = array();
    $maxAllowedPrice = key_exists($productId, $productPrices) ? $productPrices[$productId] * getAllowedPricePercentage($productPrices[$productId], $AllowedPricePercentage) / 100 : 999; // calculating allowed price
    foreach ($matrixPG_P as $rg) {
//eleminating product having more price than allowed percent of target price
        $price = key_exists($rg['obj_id'], $productPrices) ? $productPrices[$rg['obj_id']] : 0;
        if ($price > $maxAllowedPrice || $price == $maxAllowedPrice)
            continue;
//eleminating product having more price than allowed percent of target price end
        if (in_array($rg['group'], $current_group))
            continue;
//for removing similar name
        $nameFound = false;
        foreach ($names as $name) {
            $sim = 0;
            similar_text($rg['obj_nm'], $name, $sim);
            if ($sim >= $DuplicateProductNameSimilarity)
                $nameFound = true;
        }
        if ($nameFound)
            continue;
//end for removing similar name
        if ($rg['group'] != 0)
            $current_group[] = $rg['group'];
        $mergedMatrix[] = $rg;
        $names[] = $rg['obj_nm']; //for detect similar name, all names hold in array
        $count++;
        if ($count == 15)///changed it from 10 to 10
            break;
        if ($count == 3) {
            foreach ($matrixP_P as $rp) {
//eleminating product having more price than allowed percent of target price
                $price = key_exists($rp['obj_id'], $productPrices) ? $productPrices[$rp['obj_id']] : 0;
                if ($price > $maxAllowedPrice || $price == $maxAllowedPrice)
                    continue;
//eleminating product having more price than allowed percent of target price end
                if (in_array($rp['group'], $current_group))
                    continue;
//for removing similar name
                $nameFound = false;
                foreach ($names as $name) {
                    $sim = 0;
                    similar_text($rp['obj_nm'], $name, $sim);
                    if ($sim >= $DuplicateProductNameSimilarity)
                        $nameFound = true;
                }
                if ($nameFound)
                    continue;
//end for removing similar name
                if ($rp['group'] != 0)
                    $current_group[] = $rp['group'];
                $mergedMatrix[] = $rp;
                $names[] = $rp['obj_nm']; //for detect similar name, all names hold in array
                $count++;
                if ($count == 5)
                    break;
            }//end of inner foreach
        }//end of if
    }
    return $mergedMatrix;
}

function formSentence($allReciepts, $product_group) {
    $sentences = array();
    $sentences_group = array();
    foreach ($allReciepts as $reciept) {
        if (sizeof($reciept) < 2)
            continue;
        $group = array();
        foreach ($reciept as $p) {
//for product_product
            if (!key_exists($p, $sentences)) { //if the product does not exist in the sentence
                $sentences[$p] = array();
                $sentences[$p]['subject'] = 0;
                $sentences[$p]['predicates'] = array();
            }
            $sentences[$p]['subject']++;
//for group_product
            if (key_exists($p, $product_group)) {
                $pg = $product_group[$p];
                if (!in_array($pg, $group)) {
                    if (!key_exists($pg, $sentences_group)) { //if the product does not exist in the sentence_group
                        $sentences_group[$pg] = array();
                        $sentences_group[$pg]['subject'] = 0;
                        $sentences_group[$pg]['predicates'] = array();
                    }
                    $sentences_group[$pg]['subject']++;
                }
            }
            $reciptMinusP = array_diff($reciept, array($p)); //rest of the recipt except current product $p
            foreach ($reciptMinusP as $rp) {
//for product_product
                if (!key_exists($rp, $sentences[$p]['predicates'])) {
                    $sentences[$p]['predicates'][$rp] = 0;
                }
                $sentences[$p]['predicates'][$rp]++;
//for group_product
                if (key_exists($p, $product_group)) {
                    $pg = $product_group[$p];
                    if (in_array($pg, $group))
                        continue;
                    $group[] = $pg;
                    if (key_exists($rp, $product_group)) {
                        if ($pg == $product_group[$rp])
                            continue;
                    }
                    if (!key_exists($rp, $sentences_group[$pg]['predicates'])) {
                        $sentences_group[$pg]['predicates'][$rp] = 0;
                    }
                    $sentences_group[$pg]['predicates'][$rp]++;
                }
            }
        }
    }
    $allSentences['p_p'] = $sentences;
    $allSentences['pg_p'] = $sentences_group;
    return $allSentences;
}

function getProbabilityMatrix($productId, $sentences, $products, $product_group, $th, $sentences_p_p = null) {
    //echo $th; exit();
    $probabilityMatrix = array();
    if (!key_exists($productId, $sentences))
        return $probabilityMatrix;
    $sentencesPerProduct = $sentences[$productId];
    //print_r($sentences_p_p); exit();
    $innerMatrix = array();
    $total_prob = 0;
    foreach ($sentencesPerProduct['predicates'] as $predKey => $predVal) {
        $t = array();
        //echo is_array($sentences_p_p) ? $sentences_p_p[$predKey]['subject'] : $sentences[$predKey]['subject'];exit();
        if ($predVal > $th) {
            $t['obj_id'] = $predKey;
            $t['group'] = key_exists($predKey, $product_group) ? $product_group[$predKey] : 0;
            $t['obj_nm'] = $products[$predKey];
            $t['obj'] = is_array($sentences_p_p) ? $sentences_p_p[$predKey]['subject'] : $sentences[$predKey]['subject'];
            $t['pred'] = $predVal;
            $t['prob_obj'] = $t['pred'] / $t['obj'];
            $t['prob_pred'] = $t['pred'] / $sentencesPerProduct['subject'];
            $t['prob_obj_mul_pred'] = $t['prob_obj'] * $t['prob_pred'];
            $total_prob += $t['prob_obj_mul_pred'];
            $probabilityMatrix[] = $t;
        }
    }
    foreach ($probabilityMatrix as $k => $v) {
        $probabilityMatrix[$k]['prob'] = $probabilityMatrix[$k]['prob_obj_mul_pred'] / $total_prob;
    }
    return $probabilityMatrix;
}

/////change i=10 to have more suggessions/////////////
function saveHiddenMatrix($productId, $matrix, $tableName) {
    $database = new DatabaseUtility();
    $i = 0;
    foreach ($matrix as $r) {
        $query = "insert into " . $tableName . "(main_id, match_id, obj, pred, prob_obj, prob_pred, prob_obj_mul_pred, prob) values(";
        $query .= $productId . ",";
        $query .= $r['obj_id'] . ",";
        $query .= $r['obj'] . ",";
        $query .= $r['pred'] . ",";
        $query .= $r['prob_obj'] . ",";
        $query .= $r['prob_pred'] . ",";
        $query .= $r['prob_obj_mul_pred'] . ",";
        $query .= $r['prob'];
        $query .= ")";
        $database->executeQuery($query);
        $i++;
        if ($i == 15)// changed it from 10 to 10 to increase number of suggessions
            return;
    }
}

/// i=10 changed score hight 10 and lowest 0
function saveMatrix($productId, $matrixPG_P_Plus, $stores, $products = null, $product_group = null, $services = null) {
    $database = new DatabaseUtility();
    $i = 15; // changed from 10 to 10 since therer are 10 suggessions so score should come from 10 to 0
    $database->executeQuery("insert into recommender_two_presentationproxy(product_id) values(" . $productId . ")");
    $proxy_main_id = $database->getFieldData("select max(id) as id from recommender_two_presentationproxy", 'id');
    $proxy_main_id = $proxy_main_id[0];

    if (is_array($services)) { // if there any array name service exists
        if (sizeof($matrixPG_P_Plus) > 1)
            if (in_array($matrixPG_P_Plus[0]['obj_id'], $services)) {// if the first product is a service
                for ($j = 1; $j < sizeof($matrixPG_P_Plus); $j++) {
                    if (!in_array($matrixPG_P_Plus[$j]['obj_id'], $services)) {// if the current product is not service
//then just swap between current and first product
                        $t = $matrixPG_P_Plus[0]['obj_id'];
                        $matrixPG_P_Plus[0]['obj_id'] = $matrixPG_P_Plus[$j]['obj_id'];
                        $matrixPG_P_Plus[$j]['obj_id'] = $t;
                        break;
                    }
                }//end for
            }
    }

    foreach ($matrixPG_P_Plus as $r) {
        foreach ($stores as $storeId) {
            $query = "insert into recommender_two_productstorematch (main_id, algorithm, score, match_id, proxy_main_id, store_id) values(";
            $query .= $productId . ",";
            $query .= "'recommender_two',";
            $query .= $i . ",";
            $query .= $r['obj_id'] . ",";
            $query .= $proxy_main_id . ",";
            $query .= $storeId;
            $query .= ")";
            $database->executeQuery($query);
            // echo $query;exit();
        }
        $i--;
    }
}

function makeNewTables() { /// KEY `recommender_two_presentationproxy_bb420c12` (`product_id`) change one to two
    $database = new DatabaseUtility();
    $database->executeQuery("DROP TABLE IF EXISTS `recommender_two_productstorematch_backup`;");
    $database->executeQuery("DROP TABLE IF EXISTS `recommender_two_presentationproxy`;");
    $database->executeQuery("CREATE TABLE IF NOT EXISTS `recommender_two_presentationproxy` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `product_id` int(11) NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `recommender_two_presentationproxy_bb420c12` (`product_id`)
                            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
    $database->executeQuery("DROP TABLE IF EXISTS `recommender_two_productstorematch`;");
    $database->executeQuery("CREATE TABLE IF NOT EXISTS `recommender_two_productstorematch` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `main_id` int(11) NOT NULL,
                              `probability` double NOT NULL DEFAULT '0',
                              `algorithm` varchar(255) NOT NULL,
                              `proportion` double NOT NULL DEFAULT '0',
                              `score` double NOT NULL DEFAULT '0',
                              `n_main` int(11) NOT NULL DEFAULT '0',
                              `n_match` int(11) NOT NULL DEFAULT '0',
                              `n_main_intersect_match` int(11) NOT NULL DEFAULT '0',
                              `n_main_union_match` int(11) NOT NULL DEFAULT '0',
                              `match_id` int(11) NOT NULL,
                              `store_id` int(11) NOT NULL,
                              `proxy_main_id` int(11) DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `recommender_two_productstorematch_faee36dd` (`main_id`),
                              KEY `recommender_two_productstorematch_661a1ece` (`match_id`),
                              KEY `recommender_two_productstorematch_b8866dce` (`store_id`),
                              KEY `recommender_two_productstorematch_8d90e78e` (`proxy_main_id`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
    $database->executeQuery("DROP TABLE IF EXISTS `matrix_two_p_p`;");
    $database->executeQuery("CREATE TABLE IF NOT EXISTS `matrix_two_p_p` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `main_id` int(11) NOT NULL,
                              `match_id` int(11) NOT NULL,
                              `obj` int(11) NOT NULL,
                              `pred` int(11) NOT NULL,
                              `prob_obj` double NOT NULL,
                              `prob_pred` double NOT NULL,
                              `prob_obj_mul_pred` double NOT NULL,
                              `prob` double NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `recommender_two_productstorematch_faee36dd` (`main_id`),
                              KEY `recommender_two_productstorematch_661a1ece` (`match_id`),
                              KEY `recommender_two_productstorematch_b8866dce` (`prob`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
    $database->executeQuery("DROP TABLE IF EXISTS `matrix_two_pg_p`;");
    $database->executeQuery("CREATE TABLE IF NOT EXISTS `matrix_two_pg_p` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `main_id` int(11) NOT NULL,
                              `match_id` int(11) NOT NULL,
                              `obj` int(11) NOT NULL,
                              `pred` int(11) NOT NULL,
                              `prob_obj` double NOT NULL,
                              `prob_pred` double NOT NULL,
                              `prob_obj_mul_pred` double NOT NULL,
                              `prob` double NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `recommender_two_productstorematch_faee36dd` (`main_id`),
                              KEY `recommender_two_productstorematch_661a1ece` (`match_id`),
                              KEY `recommender_two_productstorematch_b8866dce` (`prob`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
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

/*
  function adjustProductGroup($product_group, $producttaging) {
  $group = array();
  $utility = new Utility();
  $itemNames = array_values($producttaging);
  $itemNames = array_unique($itemNames);

  $i = max(array_values($product_group)) + 1;

  foreach ($itemNames as $itemName) {
  $group[$itemName] = $i;
  $i++;
  }

  $product_keys = array_keys($producttaging);
  foreach ($product_group as $k => $v) {
  $product_group[$k] = in_array($k, $product_keys) ? $group[$producttaging[$k]] : $v;
  }

  return $product_group;
  }
 * 
 */

// change the twosell_product query "select id, title from twosell_product where title!='' and do_not_trigger_offer=0 and never_offer_direct=0"
function matrixCalculation() {
    print "\n\n";
    print "\n\n";
    print "\n Function matrixCalculation is Started and date :" . date("Ymd G:i:s") . "\n";
    print "matrix2.0 calculation starts...............\n";
    $database = new DatabaseUtility();
    $utility = new Utility();

    $AllowedPricePercentage = $database->getFieldsData("SELECT MinRange, MaxRange, Value FROM `recommender_two_algorithmsettings` WHERE FeatureName = 'AllowedPricePercentage' order by MinRange", array("MinRange", "MaxRange", "Value"));

    $query = 'select productId, purchaseID from tsl_producthistory';
    $fields = array('productId', 'purchaseID');
    $history = $database->getFieldsData($query, $fields);
    $allReciepts = getReciepts($history);
    print "All reciept are fetched and ready for processing...............\n";

    $products = $database->getFieldsData("select id, title from twosell_product", array('id', 'title'));
    $products = $utility->mappingArray($products, 'id', 'title');
//$productsCount = getCount($allReciepts, $products);
    $productsInStore = $database->getFieldsData('select product_id, store_id from twosell_productinstore', array('product_id', 'store_id'));
    $productsInStore = getMapBetweenProductStore($productsInStore);

    //$product_group = $database->getFieldsData('select product_id, productgroup_id from twosell_product_product_groups', array('product_id', 'productgroup_id'));
    //$product_group = $utility->mappingArray($product_group, 'product_id', 'productgroup_id');

    $producttaging = $database->getFieldsData("select productId, itemName from tsl_producttaging where itemName!=''", array('productId', 'itemName'));
    $producttaging = $utility->mappingArray($producttaging, 'productId', 'itemName');
    $product_group = $producttaging;
    //print_r($product_group); exit();
    //$product_group = adjustProductGroup($product_group, $producttaging);
    //$services = $database->getFieldData('select productId from tsl_producttaging where itemName = "service"', 'productId');
//taking price infor for eleminating product having more price than allowed percent of target price
    $productPrices = $database->getFieldsData('SELECT b.product_id as productId, avg(a.price) as price FROM `twosell_pricinghistory` as a, twosell_productinstore as b WHERE a.`priced_product_id`=b.id and a.price>0 group by productId', array('productId', 'price'));
    $productPrices = $utility->mappingArray($productPrices, 'productId', 'price');
//taking price infor for eleminating product having more price than allowed percent of target price end

    $DuplicateProductNameSimilarity = $database->getFieldData("select Value from recommender_two_algorithmsettings where FeatureName = 'DuplicateProductNameSimilarity'", "Value");
    $DuplicateProductNameSimilarity = $DuplicateProductNameSimilarity[0];

    print "All required data are fetched mapped and formatted...............\n";

    $allSentences = formSentence($allReciepts, $product_group);
    print "sentences are made according to both product and group...............\n";




    $i = 0;
    $lastPercent = 0;
    $total = sizeof($products);
    $p_p_ths = $database->getFieldData("SELECT Value FROM recommender_two_algorithmsettings where FeatureName='p_p_th'", 'Value');
    $pg_p_ths = $database->getFieldData("SELECT Value FROM recommender_two_algorithmsettings where FeatureName='pg_p_th'", 'Value');
    //print_r($p_p_ths); print_r($pg_p_ths);    
    //echo $p_p_ths[0]; echo $pg_p_ths[0]; exit();     
    $p_p_th = $p_p_ths[0]; //6   
    $pg_p_th = $pg_p_ths[0]; //15;


    makeNewTables();

    foreach ($products as $productId => $v) {
        $matrixP_P = getProbabilityMatrix($productId, $allSentences['p_p'], $products, $product_group, $p_p_th);
        $matrixPG_P = array();
        if (array_key_exists($productId, $product_group)) {
            $groupId = $product_group[$productId];
            $matrixPG_P = getProbabilityMatrix($groupId, $allSentences['pg_p'], $products, $product_group, $pg_p_th, $allSentences['p_p']);
        }
        else
            $matrixPG_P = getProbabilityMatrix($productId, $allSentences['p_p'], $products, $product_group, $p_p_th);
        $matrixPG_P_Plus = easyMergeP_PandPG_P($matrixP_P, $matrixPG_P, $productPrices, $AllowedPricePercentage, $productId, $DuplicateProductNameSimilarity);

        saveHiddenMatrix($productId, $matrixP_P, 'matrix_two_p_p');
        if (key_exists($productId, $product_group)) {
            $pg = $product_group[$productId];
            saveHiddenMatrix($productId, $matrixPG_P, 'matrix_two_pg_p');
        }
        saveMatrix($productId, $matrixPG_P_Plus, $productsInStore[$productId], $products, $product_group); //$services
        $i++;
        $lastPercent = $utility->progressBar("calculation is done & saved for : ", $i, $total, $lastPercent);
    }

    print "\nmatrix calculation is done!!!\n";
    $database->executeQuery("create table recommender_two_productstorematch_backup as select * from recommender_two_productstorematch");
}

function getMapValuetoKeyArray($inputArray) {
    $outputArray = array();
    foreach ($inputArray as $k => $v) {
        if (!key_exists($v, $outputArray))
            $outputArray[$v] = array();
        $outputArray[$v][] = $k;
    }
    return $outputArray;
}

/*
  function matrixReplacement() { //should be modified later
  print "matrix2.0 replacement starts...............\n";
  $database = new DatabaseUtility();
  $utility = new Utility();

  $products = $database->getFieldsData('select id, title from twosell_product where id in (select product_id from tsln_product_group)', array('id', 'title'));
  $products = $utility->mappingArray($products, 'id', 'title');

  $productsInStore = $database->getFieldsData('select product_id, store_id from twosell_productinstore where active = 1', array('product_id', 'store_id'));
  $productsInStore = getMapBetweenProductStore($productsInStore);

  $product_group = $database->getFieldsData('select product_id, productgroup_id from twosell_product_product_groups', array('product_id', 'productgroup_id'));
  $product_group = $utility->mappingArray($product_group, 'product_id', 'productgroup_id');
  $groupProducts = getMapValuetoKeyArray($product_group);

  $recommendations = $database->getFieldsData("select id, main_id, match_id, store_id from recommender_two_productstorematch", array('id', 'main_id', 'match_id', 'store_id'));
  $recommendations = $utility->mappingArray($recommendations, 'id');

  $productTag = $database->getFieldsData("select itemName, productId from tsl_producttaging", array('itemName', 'productId'));
  $product_item = $utility->mappingArray($productTag, 'productId', 'itemName');
  $itemProducts = getMapValuetoKeyArray($product_item);

  print "All required data are fetched mapped and formatted...............\n";
  print "replacing calculation starts...................................\n";

  $i = $lastPercent = 0;
  $total = sizeof($recommendations);
  foreach ($recommendations as $k => $r) {
  $match_id = $r['match_id'];
  $main_id = $r['main_id'];
  $store_id = $r['store_id'];
  $match_itemName = key_exists($match_id, $product_item) ? $product_item[$match_id] : '';
  $match_group = key_exists($match_id, $product_group) ? $product_group[$match_id] : 0;
  $main_itemName = key_exists($main_id, $product_item) ? $product_item[$main_id] : '';
  $main_group = key_exists($main_id, $product_group) ? $product_group[$main_id] : 0;
  $sim = 0;
  $currentSim = 0;
  $newMatchId = $match_id;
  if ($match_itemName != '') {
  $productsHavingSameItemNm = $itemProducts[$match_itemName];
  $productsHavingSameItemNm = array_diff($productsHavingSameItemNm, array($match_id));
  foreach ($productsHavingSameItemNm as $p) {
  if (!key_exists($p, $productsInStore))
  continue;  //for deactive product
  if (!in_array($store_id, $productsInStore[$p]))
  continue;

  similar_text($products[$main_id], $products[$p], $currentSim);

  if ($currentSim > $sim) {
  $newMatchId = $p;
  $sim = $currentSim;
  }
  }
  } else {
  $productsHavingSameGroup = ($match_group == 0) ? array_diff(array_keys($products), array_keys($product_group)) : $groupProducts[$match_group];
  $productsHavingSameGroup = array_diff($productsHavingSameGroup, array($match_id));
  foreach ($productsHavingSameGroup as $p) {
  if (!key_exists($p, $productsInStore))
  continue; //for deactive product
  if (!in_array($store_id, $productsInStore[$p]))
  continue;
  similar_text($products[$match_id], $products[$p], $currentSim);
  if ($currentSim > $sim) {
  $newMatchId = $p;
  $sim = $currentSim;
  }
  }
  }


  $query = "";
  if ($newMatchId != $match_id) {
  $query = "update recommender_two_productstorematch set match_id = " . $newMatchId . " where id = " . $k;
  $database->executeQuery($query);
  }
  $i++;
  $lastPercent = $utility->progressBar("calculation is done & saved for : ", $i, $total, $lastPercent);
  }
  print "\nreplacing calculation done!!! ...........\n";
  }
 * 
 */

function matrixReplacementPushlistaDeactive() {
    print "\n\n";
    print "\n\n";
    print "\n Function matrixReplacementPushlistaDeactive is Started and date :" . date("Ymd G:i:s") . "\n";
    print "matrix2.0 replacement accordingly deactive starts...............\n";
    $database = new DatabaseUtility();
    $utility = new Utility();
    $database->executeQuery("update recommender_two_productstorematch as a, tsl_recommendationPushlista as b set a.match_id = b.active  where a.match_id = b.nonActive");
    $stores = $database->getFieldData("select id from twosell_store", 'id');
    foreach ($stores as $store) {
        $database->executeQuery("delete from recommender_two_productstorematch where match_id  in (select nonActive from tsl_recommendationPushlista where nonActive = active)");
    }
    print "\nreplacing deactive is done!!! ...........\n";
}

function matrixReplacementInStore() { //matrix replacement according to store
    print "\n\n";
    print "\n\n";
    print "\n Function matrixReplacementInStore is Started and date :" . date("Ymd G:i:s") . "\n";
    print "matrix2.0 replacement accordingly in store starts...............\n";
    $database = new DatabaseUtility();
    $utility = new Utility();

    $products = $database->getFieldsData('select productId as id, name as title  from tsl_producttaging', array('id', 'title'));
    $products = $utility->mappingArray($products, 'id', 'title');

    $productsInStore = $database->getFieldsData('select product_id, store_id from twosell_productinstore where active = 1', array('product_id', 'store_id'));
    $productsInStore = getMapBetweenProductStore($productsInStore);

    //$product_group = $database->getFieldsData('select product_id, productgroup_id from twosell_product_product_groups', array('product_id', 'productgroup_id'));
    //$product_group = $utility->mappingArray($product_group, 'product_id', 'productgroup_id');
    //$groupProducts = getMapValuetoKeyArray($product_group);

    $recommendations = $database->getFieldsData("select id, main_id, match_id, store_id from recommender_two_productstorematch", array('id', 'main_id', 'match_id', 'store_id'));
    $recommendations = $utility->mappingArray($recommendations, 'id');

    //$producttaging = $database->getFieldsData('select productId, itemName from tsl_producttaging where itemName not in ("","service")', array('productId', 'itemName'));
    //$producttaging = $utility->mappingArray($producttaging, 'productId', 'itemName');
    //$product_group = adjustProductGroup($product_group, $producttaging);
    //$product_group = $producttaging;

    $productTag = $database->getFieldsData("select itemName, productId from tsl_producttaging", array('itemName', 'productId'));
    $product_item = $utility->mappingArray($productTag, 'productId', 'itemName');
    //$itemProducts = getMapValuetoKeyArray($product_item);

    print "All required data are fetched mapped and formatted...............\n";
    print "replacing calculation starts...................................\n";

    $i = $lastPercent = 0;
    $total = sizeof($recommendations);
    foreach ($recommendations as $k => $r) {
        $match_id = $r['match_id'];
        $main_id = $r['main_id'];
        $store_id = $r['store_id'];
        $match_itemName = key_exists($match_id, $product_item) ? $product_item[$match_id] : '';
        //$match_group = key_exists($match_id, $product_group) ? $product_group[$match_id] : 0;
        $main_itemName = key_exists($main_id, $product_item) ? $product_item[$main_id] : '';
        // $main_group = key_exists($main_id, $product_group) ? $product_group[$main_id] : 0;
        $sim = 0;
        $currentSim = 0;
        $newMatchId = $match_id;
//current 2011-09-09
        if (!key_exists($match_id, $productsInStore) || !in_array($store_id, $productsInStore[$match_id])) {
//if ($match_itemName == '') {
            $query = "delete from recommender_two_productstorematch where id = " . $k;
            $database->executeQuery($query);
        }
//end current 2011-09-09
        $i++;
        $lastPercent = $utility->progressBar("calculation is done & saved for : ", $i, $total, $lastPercent);
    }
    print "\nreplacing In store calculation done!!! ...........\n";
}

function matrixCalculationItemGroup() {
    print "\n\n";
    print "\n\n";
    print "\n Function matrixCalculationItemGroup is Started and date :" . date("Ymd G:i:s") . "\n";

    print "matrix Calculation ItemName For New Product starts...............\n";
    $database = new DatabaseUtility();
    $utility = new Utility();
    $database->executeQuery("DROP TABLE IF EXISTS `recommender_two_productstorematch_item_group`;");
    $database->executeQuery("CREATE TABLE IF NOT EXISTS `recommender_two_productstorematch_item_group` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `main_id` varchar(255) NOT NULL,
          `probability` double NOT NULL DEFAULT '0',
          `algorithm` varchar(255) NOT NULL,
          `proportion` double NOT NULL DEFAULT '0',
          `score` double NOT NULL DEFAULT '0',
          `n_main` int(11) NOT NULL DEFAULT '0',
          `n_match` int(11) NOT NULL DEFAULT '0',
          `n_main_intersect_match` int(11) NOT NULL DEFAULT '0',
          `n_main_union_match` int(11) NOT NULL DEFAULT '0',
          `match_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL,
          `proxy_main_id` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `recommender_two_productstorematch_item_group1` (`main_id`),
          KEY `recommender_two_productstorematch_item_group2` (`match_id`),
          KEY `recommender_two_productstorematch_item_group3` (`store_id`),
          KEY `recommender_two_productstorematch_item_group4` (`proxy_main_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

    /*     * ********************new coding 2011.10.11 ************************************************************** */
    $query = 'SELECT main_id, count(main_id)as totalCount FROM recommender_two_productstorematch  group by main_id order by totalCount desc';
    $productInRecommendationAll = $database->getFieldData($query, 'main_id');

    $producttaging = $database->getFieldsData('select productId, itemName from tsl_producttaging where itemName not in ("","service")', array('productId', 'itemName'));
    $producttaging = $utility->mappingArray($producttaging, 'productId', 'itemName'); // all product to item it belongs to mapping

    $itemNames = array_values($producttaging);
    $itemNames = array_unique($itemNames); // all distinct items

    print "All reciept are fetched and ready for processing...............\n";

    $i = 0;
    $lastPercent = 0;
    $total = sizeof($itemNames);
    foreach ($itemNames as $itemName) {
        $productInItem = array_keys($producttaging, $itemName); //return 
        $productInRecommendation = array_intersect($productInRecommendationAll, $productInItem);
        if (!empty($productInRecommendation)) {
            $productInRecommendation = array_values($productInRecommendation);
            $p = $productInRecommendation[0];
            $fieldNames = array('main_id', 'algorithm', 'score', 'match_id', 'store_id');
            $rows = $database->getFieldsData("SELECT '" . $itemName . "' as main_id, algorithm, score, match_id, store_id FROM recommender_two_productstorematch WHERE main_id = " . $p, $fieldNames);
            foreach ($rows as $row) {
                $query = "insert into recommender_two_productstorematch_item_group (main_id, algorithm, score, match_id, store_id) values('";
                $query .= $row['main_id'] . "','";
                $query .= $row['algorithm'] . "',";
                $query .= $row['score'] . ",";
                $query .= $row['match_id'] . ",";
                $query .= $row['store_id'];
                $query .= ")";
                $database->executeQuery($query);
            }
        }
        $i++;
        $lastPercent = $utility->progressBar("calculation is done & saved for : ", $i, $total, $lastPercent);
    }
    print "\nmatrix Calculation ItemName is done!!!\n";

    /*     * ********************new coding 2011.10.11 end *********************************************************** */
}

function topingUp() {
    print "\n\n";
    print "\n\n";
    print "\n Function topingUp is Started and date :" . date("Ymd G:i:s") . "\n";
    print "Toping up starts...............\n";
    $database = new DatabaseUtility();
    $utility = new Utility();
    $products_noRecommendation = $database->getFieldsData("select productId as id, name as title  from tsl_producttaging where productId not in (select distinct main_id from recommender_two_productstorematch)", array('id', 'title'));
    $products_noRecommendation = $utility->mappingArray($products_noRecommendation, 'id', 'title');

    /*     * *product and group name (Item Name) mapping considering synonym** */
    /*
      $product_group = $database->getFieldsData("select product_id, group_id from tsl_product_group", array('product_id', 'group_id'));
      $product_group = $utility->mappingArray($product_group, 'product_id', 'group_id');
      $group_name = $database->getFieldsData("select id, name from tsl_group", array('id', 'name'));
      $group_name = $utility->mappingArray($group_name, 'id', 'name');
      $product_group_synonym = $database->getFieldsData("select synonym_group_id, group_id from tsl_group_synonym", array('synonym_group_id', 'group_id'));
      $product_group_synonym = $utility->mappingArray($product_group_synonym, 'synonym_group_id', 'group_id');

      foreach ($product_group as $p => $g) {
      if (key_exists($g, $product_group_synonym)) {
      $g = $product_group_synonym[$g];
      }
      $product_group[$p] = $group_name[$g];
      }

      $tsl_product_groupSynonym = $product_group;
      /*     * *end of product and group name (Item Name) mapping considering synonym** */


    // ********************************toping up item group and price***********************************************************
    //$producttaging = $database->getFieldsData('select productId, itemName from tsl_producttaging where itemName not in ("","service")', array('productId', 'itemName'));
    //$producttaging = $utility->mappingArray($producttaging, 'productId', 'itemName');
    //print_r($producttaging); exit();
    //taking price infor for eleminating product having more price than allowed percent of target price
    $productPrices = $database->getFieldsData('SELECT b.product_id as productId, avg(a.price) as price FROM `twosell_pricinghistory` as a, twosell_productinstore as b WHERE a.`priced_product_id`=b.id and a.price>0 group by productId', array('productId', 'price'));
    $productPrices = $utility->mappingArray($productPrices, 'productId', 'price');
    $productPrices2 = $database->getFieldsData("SELECT twosell_product.id, inpris FROM tsl_productnew,twosell_product 
        WHERE twosell_product.articlenum = tsl_productnew.artikelnr and twosell_product.id in 
        (SELECT distinct b.product_id as productId  FROM `twosell_pricinghistory` as a, twosell_productinstore as b WHERE a.`priced_product_id`=b.id and a.price = 0)", array('id', 'inpris'));
    $productPrices2 = $utility->mappingArray($productPrices2, 'id', 'inpris');
    $productPrices2 = array_diff_key($productPrices2, $productPrices);
    $productPrices = array_merge($productPrices, $productPrices2);
    //taking price infor for eleminating product having more price than allowed percent of target price end
    $productsInStore = $database->getFieldsData('select product_id, store_id from twosell_productinstore', array('product_id', 'store_id'));
    $productsInStore = getMapBetweenProductStore($productsInStore);
    $query = 'SELECT productId, count(productId)as totalCount FROM `tsl_producthistory` WHERE price>0 group by productId';
    $fields = array('productId', 'totalCount');
    $productFrequencyInHistory = $database->getFieldsData($query, $fields);
    $productFrequencyInHistory = $utility->mappingArray($productFrequencyInHistory, 'productId', 'totalCount'); // all product to its frequecy mapping


    $i = 0;
    $lastPercent = 0;
    $total = sizeof($products_noRecommendation);
    foreach ($products_noRecommendation as $productId => $title) {
        if (!key_exists($productId, $productPrices)) {
            $i++;
            $lastPercent = $utility->progressBar("Toping up calculation is done & saved for : ", $i, $total, $lastPercent);
            continue;
        }
        //echo 'select product_id from tsln_product_group where group_id in (select group_id from tsln_product_group where product_id =' . $productId . ')';
        $productInGroup = $database->getFieldsData('select product_id from tsln_product_group where group_id in (select group_id from tsln_product_group where product_id =' . $productId . ')', array('product_id'));
        // $tagging = $utility->onTaging($title, $itemNames, $brandNames, $colorNames, $attributes, $productId, $product_group_synonym);
        //$productInGroup = array_diff(array_keys($producttaging, 'Brödrostar'), array($productId)); //return 
        //$productInGroup = array_flip($productInGroup); //exchange index with values

        $productInGroup = array_intersect_key($productFrequencyInHistory, $productInGroup); //compares the key of $productFrequencyInHistory and $productInItem and return both key and value from first array
        //Print_r($productInGroup);
        arsort($productInGroup, SORT_NUMERIC); //sorting should not assign. read php5 manual for more
        foreach ($productInGroup as $p => $v) {
            $fieldNames = array('main_id', 'algorithm', 'score', 'match_id', 'store_id');
            $rows = $database->getFieldsData("SELECT '" . $productId . "' as main_id, algorithm, score, match_id, store_id FROM recommender_two_productstorematch WHERE main_id = " . $p, $fieldNames);
            $proxy_main_id = 0;
            $commonStore = array_intersect($productsInStore[$p], $productsInStore[$productId]);
            if (!key_exists($p, $productPrices) || $productPrices[$p] > $productPrices[$productId] || empty($commonStore))
                continue;
            if (!empty($rows)) {
                $database->executeQuery("insert into recommender_two_presentationproxy(product_id) values(" . $productId . ")");
                $proxy_main_id = $database->getFieldData("select max(id) as id from recommender_two_presentationproxy", 'id');
                $proxy_main_id = $proxy_main_id[0];
                unset($products_noRecommendation[$productId]);
            }
            else
                continue;
            foreach ($rows as $row) {
                $query = "insert into recommender_two_productstorematch (main_id, algorithm, score, match_id, proxy_main_id, store_id) values('";
                $query .= $row['main_id'] . "','";
                $query .= $row['algorithm'] . "',";
                $query .= $row['score'] . ",";
                $query .= $row['match_id'] . ",";
                $query .= $proxy_main_id . ",";
                $query .= $row['store_id'];
                $query .= ")";
                $database->executeQuery($query);
            }
            break;
        }
        $i++;
        $lastPercent = $utility->progressBar("Toping up calculation is done & saved for : ", $i, $total, $lastPercent);
    }
    print "\n";

    // *********************************toping up considering company group*****************************************************
    $product_group = $database->getFieldsData('select product_id, group_id from tsln_product_group', array('product_id', 'group_id'));
    $product_group = $utility->mappingArray($product_group, 'product_id', 'group_id');
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($products_noRecommendation);
    foreach ($products_noRecommendation as $productId => $title) {
        if (!key_exists($productId, $productPrices)) {
            $i++;
            $lastPercent = $utility->progressBar("Toping up calculation is done & saved for : ", $i, $total, $lastPercent);
            continue;
        }
        if (!key_exists($productId, $product_group)) {// when there is no company product means company product id is 0
            //unset($products_noRecommendation[$productId]);
            $i++;
            $lastPercent = $utility->progressBar("Toping up calculation is done & saved for : ", $i, $total, $lastPercent);
            continue;
        }
        $productInGroup = array_diff(array_keys($product_group, $product_group[$productId]), array($productId)); //return 
        $productInGroup = array_flip($productInGroup); //exchange index with values
        $productInGroup = array_intersect_key($productFrequencyInHistory, $productInGroup); //compares the key of $productFrequencyInHistory and $productInItem and return both key and value from first array
        arsort($productInGroup, SORT_NUMERIC); //sorting should not assign. read php5 manual for more
        foreach ($productInGroup as $p => $v) {
            $fieldNames = array('main_id', 'algorithm', 'score', 'match_id', 'store_id');
            $rows = $database->getFieldsData("SELECT '" . $productId . "' as main_id, algorithm, score, match_id, store_id FROM recommender_two_productstorematch WHERE main_id = " . $p, $fieldNames);
            $proxy_main_id = 0;
            $commonStore = array_intersect($productsInStore[$p], $productsInStore[$productId]);
            if (!key_exists($p, $productPrices) || $productPrices[$p] > $productPrices[$productId] || empty($commonStore))
                continue;
            if (!empty($rows)) {
                $database->executeQuery("insert into recommender_two_presentationproxy(product_id) values(" . $productId . ")");
                $proxy_main_id = $database->getFieldData("select max(id) as id from recommender_two_presentationproxy", 'id');
                $proxy_main_id = $proxy_main_id[0];
                unset($products_noRecommendation[$productId]);
            }
            else
                continue;
            foreach ($rows as $row) {
                $query = "insert into recommender_two_productstorematch (main_id, algorithm, score, match_id, proxy_main_id, store_id) values('";
                $query .= $row['main_id'] . "','";
                $query .= $row['algorithm'] . "',";
                $query .= $row['score'] . ",";
                $query .= $row['match_id'] . ",";
                $query .= $proxy_main_id . ",";
                $query .= $row['store_id'];
                $query .= ")";
                $database->executeQuery($query);
            }
            break;
        }
        $i++;
        $lastPercent = $utility->progressBar("Toping up calculation is done & saved for : ", $i, $total, $lastPercent);
    }

    print "\n";


    //************************************************repeating everything but not considering price and store**************************************************


    $i = 0;
    $lastPercent = 0;
    $total = sizeof($products_noRecommendation);
    foreach ($products_noRecommendation as $productId => $title) {

        $productInGroup = $database->getFieldsData('select product_id from tsln_product_group where group_id in (select group_id from tsln_product_group where product_id =' . $productId . ')', array('product_id'));

        $productInGroup = array_intersect_key($productFrequencyInHistory, $productInGroup); //compares the key of $productFrequencyInHistory and $productInItem and return both key and value from first array
        arsort($productInGroup, SORT_NUMERIC); //sorting should not assign. read php5 manual for more


        foreach ($productInGroup as $p => $v) {
            $fieldNames = array('main_id', 'algorithm', 'score', 'match_id', 'store_id');
            $rows = $database->getFieldsData("SELECT '" . $productId . "' as main_id, algorithm, score, match_id, store_id FROM recommender_two_productstorematch WHERE main_id = " . $p, $fieldNames);
            $proxy_main_id = 0;

            if (!empty($rows)) {
                $database->executeQuery("insert into recommender_two_presentationproxy(product_id) values(" . $productId . ")");
                $proxy_main_id = $database->getFieldData("select max(id) as id from recommender_two_presentationproxy", 'id');
                $proxy_main_id = $proxy_main_id[0];
                unset($products_noRecommendation[$productId]);
            }
            else
                continue;
            foreach ($rows as $row) {
                $query = "insert into recommender_two_productstorematch (main_id, algorithm, score, match_id, proxy_main_id, store_id) values('";
                $query .= $row['main_id'] . "','";
                $query .= $row['algorithm'] . "',";
                $query .= $row['score'] . ",";
                $query .= $row['match_id'] . ",";
                $query .= $proxy_main_id . ",";
                $query .= $row['store_id'];
                $query .= ")";
                $database->executeQuery($query);
            }
            break;
        }
        $i++;
        $lastPercent = $utility->progressBar("Toping up calculation is done & saved for : ", $i, $total, $lastPercent);
    }


    print "\n";

    // *********************************toping up considering company group*****************************************************
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($products_noRecommendation);
    foreach ($products_noRecommendation as $productId => $title) {

        if (!key_exists($productId, $product_group)) {// when there is no company product means company product id is 0
            //unset($products_noRecommendation[$productId]);
            $i++;
            $lastPercent = $utility->progressBar("Toping up calculation is done & saved for : ", $i, $total, $lastPercent);
            continue;
        }
        $productInGroup = array_diff(array_keys($product_group, $product_group[$productId]), array($productId)); //return 
        $productInGroup = array_flip($productInGroup); //exchange index with values
        $productInGroup = array_intersect_key($productFrequencyInHistory, $productInGroup); //compares the key of $productFrequencyInHistory and $productInItem and return both key and value from first array
        arsort($productInGroup, SORT_NUMERIC); //sorting should not assign. read php5 manual for more
        foreach ($productInGroup as $p => $v) {
            $fieldNames = array('main_id', 'algorithm', 'score', 'match_id', 'store_id');
            $rows = $database->getFieldsData("SELECT '" . $productId . "' as main_id, algorithm, score, match_id, store_id FROM recommender_two_productstorematch WHERE main_id = " . $p, $fieldNames);
            $proxy_main_id = 0;

            if (!empty($rows)) {
                $database->executeQuery("insert into recommender_two_presentationproxy(product_id) values(" . $productId . ")");
                $proxy_main_id = $database->getFieldData("select max(id) as id from recommender_two_presentationproxy", 'id');
                $proxy_main_id = $proxy_main_id[0];
                unset($products_noRecommendation[$productId]);
            }
            else
                continue;
            foreach ($rows as $row) {
                $query = "insert into recommender_two_productstorematch (main_id, algorithm, score, match_id, proxy_main_id, store_id) values('";
                $query .= $row['main_id'] . "','";
                $query .= $row['algorithm'] . "',";
                $query .= $row['score'] . ",";
                $query .= $row['match_id'] . ",";
                $query .= $proxy_main_id . ",";
                $query .= $row['store_id'];
                $query .= ")";
                $database->executeQuery($query);
            }
            break;
        }
        $i++;
        $lastPercent = $utility->progressBar("Toping up calculation is done & saved for : ", $i, $total, $lastPercent);
    }

    print "\nToping up is done!!!\n";
}

// where active =1 newly added
function puttingManualSuggestionsGroup() {

    print "\n\n";
    print "\n\n";
    print "\n Function puttingManualSuggestionsGroup is Started and date :" . date("Ymd G:i:s") . "\n";
    print "Manual suggestion insertion starts...............\n";
    $database = new DatabaseUtility();
    $utility = new Utility();
    // where active =1 newly added
    $stores = $database->getFieldData('select id from twosell_store where active =1', 'id');

    $product_group = $database->getFieldsData('select a.product_id as product_id, a.group_id as group_id  from tsln_product_group as a, tsln_price_product as b where a.product_id=b.product_id order by b.totalsold desc, b.lastdate desc', array('product_id', 'group_id'));
    $product_group = $utility->mappingArray($product_group, 'product_id', 'group_id');

    // $tsl_group_synonym = $database->getFieldsData('select group_id, synonym_group_id from tsl_group_synonym', array('group_id', 'synonym_group_id'));
    //$tsl_group_synonym = $utility->mappingArray($tsl_group_synonym, 'synonym_group_id', 'group_id');

    /*     * ******change the sysnonym group availale in $product_group by master group with the help of $tsl_group_synonym********* */
    // foreach ($product_group as $p => $g) {
    //   if (key_exists($g, $tsl_group_synonym))
    //     $product_group[$p] = $tsl_group_synonym[$g];
    //}
    /*     * ******end of changing the sysnonym group availale in $product_group by master group with the help of $tsl_group_synonym********* */

    $productsInStore = $database->getFieldsData('select product_id, store_id from twosell_productinstore where product_id in (select product_id from tsln_product_group)', array('product_id', 'store_id'));
    $productsInStore = getMapBetweenProductStore($productsInStore);

    //taking price infor for eleminating product having more price than allowed percent of target price
    $productPrices = $database->getFieldsData('SELECT product_id as productId, max_price as price FROM tsln_price_product where product_id in (select product_id from tsln_product_group)', array('productId', 'price'));
    $productPrices = $utility->mappingArray($productPrices, 'productId', 'price');
    //taking price infor for eleminating product having more price than allowed percent of target price end

    $AllowedPricePercentage = $database->getFieldsData("SELECT MinRange, MaxRange, Value FROM `recommender_two_algorithmsettings` WHERE FeatureName = 'AllowedPricePercentage' order by MinRange", array("MinRange", "MaxRange", "Value"));

    $tsl_group_suggestion = $database->getFieldsData('select group_id, suggestion_group_id from tsln_group_suggestion order by id', array('group_id', 'suggestion_group_id'));
    //print_r($tsl_group_suggestion); exit();
    $suggestion_group_group = array();

    foreach ($tsl_group_suggestion as $r) {
        if (!key_exists($r['group_id'], $suggestion_group_group)) {
            $suggestion_group_group[$r['group_id']] = array();
        }
        $suggestion_group_group[$r['group_id']][] = $r['suggestion_group_id'];
    }

    $recommendationIds = $database->getFieldData('select distinct main_id from recommender_two_productstorematch', 'main_id');
    $productIds = $database->getFieldData('select id from twosell_product where id in (select product_id from tsln_product_group)', 'id');
    $proxyIds = $database->getFieldData('select product_id from recommender_two_presentationproxy', 'product_id');

    print "All data are fetched to work\n";

    /*     * ***************making table to keep data******************************* */
    $database->executeQuery("DROP TABLE IF EXISTS `recommender_two_productstorematch_manual`");
    $database->executeQuery("CREATE TABLE IF NOT EXISTS `recommender_two_productstorematch_manual` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `main_id` int(11) NOT NULL,
          `probability` double NOT NULL DEFAULT '0',
          `algorithm` varchar(255) NOT NULL,
          `proportion` double NOT NULL DEFAULT '0',
          `score` double NOT NULL DEFAULT '0',
          `n_main` int(11) NOT NULL DEFAULT '0',
          `n_match` int(11) NOT NULL DEFAULT '0',
          `n_main_intersect_match` int(11) NOT NULL DEFAULT '0',
          `n_main_union_match` int(11) NOT NULL DEFAULT '0',
          `match_id` int(11) NOT NULL,
          `store_id` int(11) NOT NULL,
          `proxy_main_id` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `recommender_two_productstorematch_faee36dd` (`main_id`),
          KEY `recommender_two_productstorematch_661a1ece` (`match_id`),
          KEY `recommender_two_productstorematch_b8866dce` (`store_id`),
          KEY `recommender_two_productstorematch_8d90e78e` (`proxy_main_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
    /*     * ***************end of making table to keep data******************************* */

    foreach ($stores as $storeId) {
        $product_group_store = array();
        foreach ($product_group as $key => $val) {
            if (!key_exists($key, $productsInStore))
                continue;
            if (in_array($storeId, $productsInStore[$key]))
                $product_group_store[$key] = $val;
        }

        $suggestion_product_product = array();

        foreach ($suggestion_group_group as $targetGroup => $suggGroups) {
            //$productInTargetGroup = array_keys($product_group_store, $targetGroup); //checking target product in store
            $productInTargetGroup = array_keys($product_group, $targetGroup); //not checking target product in store
            foreach ($productInTargetGroup as $tProduct) {
                $maxAllowedPrice = key_exists($tProduct, $productPrices) ? $productPrices[$tProduct] * getAllowedPricePercentage($productPrices[$tProduct], $AllowedPricePercentage) / 100 : 999;
                $suggestion_product_product[$tProduct] = array();
                foreach ($suggGroups as $suggGroup) {
                    $productInSuggGroup = array_keys($product_group_store, $suggGroup);
                    foreach ($productInSuggGroup as $suggProduct) {
                        $suggProductPrice = key_exists($suggProduct, $productPrices) ? $productPrices[$suggProduct] : 0;
                        if ($suggProductPrice > $maxAllowedPrice)
                            continue;
                        $suggestion_product_product[$tProduct][] = $suggProduct;
                        break;
                    }
                    //$suggestion_product_product[$tProduct] = array_reverse($suggestion_product_product[$tProduct]);
                }
            }
        }


        $i = 0;
        $lastPercent = 0;
        $total = sizeof($productIds);
        //print_r($productIds); exit();
        foreach ($productIds as $productId) {
            if (!key_exists($productId, $suggestion_product_product)) {
                $i++;
                $lastPercent = $utility->progressBar("Adding menual suggession is done & saved for : ", $i, $total, $lastPercent);
                continue;
            }
            $proxy_main_id = 0;

            if (!in_array($productId, $proxyIds)) {
                $database->executeQuery("insert into recommender_two_presentationproxy(product_id) values(" . $productId . ")");
            }

            $proxy_main_id = $database->getFieldData("select id as id from recommender_two_presentationproxy where product_id = " . $productId, 'id');
            $proxy_main_id = $proxy_main_id[0];

            $score = 100;
            foreach ($suggestion_product_product[$productId] as $suggestion) {
                $query = "insert into recommender_two_productstorematch_manual (main_id, algorithm, score, match_id, proxy_main_id, store_id) values('";
                $query .= $productId . "','";
                $query .= "recommender_two" . "',";
                $query .= $score . ",";
                $query .= $suggestion . ",";
                $query .= $proxy_main_id . ",";
                $query .= $storeId;
                $query .= ")";
                $database->executeQuery($query);
                //print $query."\n";
                //exit();
                $score--;
            }
            $i++;
            $lastPercent = $utility->progressBar("Manual Suggestion for store " . $storeId . " is done & saved for : ", $i, $total, $lastPercent);
        }
        print "\n";
    }

    print "\nManual suggestion insertion done!!\n";
}

// where active =1 newly added
function mergeRecommendationsAndManualSuggestionsGroup() {
    print "\n\n";
    print "\n\n";
    print "\n Function mergeRecommendationsAndManualSuggestionsGroup is Started and date :" . date("Ymd G:i:s") . "\n";
    print "Merging to manual suggestion starts...............\n";
    $database = new DatabaseUtility();
    $utility = new Utility();

    $manual_suggestions = $database->getFieldsData('SELECT main_id, score, match_id, proxy_main_id, store_id FROM recommender_two_productstorematch_manual', array('main_id', 'score', 'match_id', 'proxy_main_id', 'store_id'));
    print "Injekting manual suggestion ..............\n";
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($manual_suggestions);
    foreach ($manual_suggestions as $suggestion) {
        $query = "insert into recommender_two_productstorematch (main_id, algorithm, score, match_id, proxy_main_id, store_id) values('";
        $query .= $suggestion['main_id'] . "','";
        $query .= "recommender_two_manual" . "',";
        $query .= $suggestion['score'] . ",";
        $query .= $suggestion['match_id'] . ",";
        $query .= $suggestion['proxy_main_id'] . ",";
        $query .= $suggestion['store_id'];
        $query .= ")";
        $database->executeQuery($query);
        $i++;
        $lastPercent = $utility->progressBar("Manual Suggestion injection is saved for : ", $i, $total, $lastPercent);
    }
    print "\nInjektion completed ..............\n";

    print "Removing duplicates considering manual group ..............\n";
    $product_group = $database->getFieldsData('select a.product_id as product_id, a.group_id as group_id  from tsln_product_group as a, tsln_price_product as b where a.product_id=b.product_id order by b.totalsold desc', array('product_id', 'group_id'));
    $product_group = $utility->mappingArray($product_group, 'product_id', 'group_id');

    //$tsl_group_synonym = $database->getFieldsData('select group_id, synonym_group_id from tsl_group_synonym', array('group_id', 'synonym_group_id'));
    //$tsl_group_synonym = $utility->mappingArray($tsl_group_synonym, 'synonym_group_id', 'group_id');

    /*     * ******change the sysnonym group availale in $product_group by master group with the help of $tsl_group_synonym********* */
    //foreach ($product_group as $p => $g) {
    //  if (key_exists($g, $tsl_group_synonym))
    //     $product_group[$p] = $tsl_group_synonym[$g];
    //}
    /*     * ******end of changing the sysnonym group availale in $product_group by master group with the help of $tsl_group_synonym********* */


    // where active =1 newly added
    $stores = $database->getFieldData('select id from twosell_store  where active =1', 'id');
    foreach ($stores as $storeId) {
        $available_group = array();
        $recommendations = $database->getFieldsData('SELECT id, main_id, match_id FROM recommender_two_productstorematch where store_id = ' . $storeId . ' order by score desc', array('id', 'main_id', 'match_id'));
        //print_r($recommendations); exit();
        $i = 0;
        $lastPercent = 0;
        $total = sizeof($recommendations);
        foreach ($recommendations as $row) {
            if (!key_exists($row['main_id'], $available_group)) {
                $available_group[$row['main_id']] = array();
                if (key_exists($row['main_id'], $product_group)) {
                    $available_group[$row['main_id']][] = $product_group[$row['main_id']];
                }
            }
            if (key_exists($row['match_id'], $product_group)) {
                if (in_array($product_group[$row['match_id']], $available_group[$row['main_id']])) {
                    $database->executeQuery("delete from recommender_two_productstorematch where id = " . $row['id']);
                } else {
                    $available_group[$row['main_id']][] = $product_group[$row['match_id']];
                }
            }

            $i++;
            $lastPercent = $utility->progressBar("Duplicates removed for store " . $storeId . " : ", $i, $total, $lastPercent);
        }
        print "\n";
    }
    print "\nEnd of Removing";
    print "\nMerging to manual suggestion is done!!\n";
}

// newly added and a.store_id in (select id from twosell_store where active=1)
function recommender_two_online() {
    print "\n\n";
    print "\n Function recommender_two_online is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();

    /*     * ***************making table to keep data******************************* */


    if ($database->executeQuery("DROP TABLE IF EXISTS `recommender_two_online`")) {
        echo "`recommender_two_online` Table is Droped\n";
    } else {
        echo "`recommender_two_online` Table is not Droped\n";
    }

    $table = "CREATE TABLE IF NOT EXISTS `recommender_two_online` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                              `store_id` int(11) NOT NULL,
                              `main_id` int(11) NOT NULL,
                              `positemid` varchar(250) NOT NULL,
                              `title` varchar(250) NOT NULL,
                              `group_id` int(11) NOT NULL,
                              `description1` varchar(250) NOT NULL,
                              `description2` varchar(250) NOT NULL,
                              `description3` varchar(250) NOT NULL,
                               `score` int(11) NOT NULL,
                               PRIMARY KEY (`id`),
                               KEY `recommender_two_online_1` (`main_id`),
                               KEY `recommender_two_online_2` (`score`),
                               KEY `recommender_two_online_3` (`store_id`),
                               KEY `recommender_two_online_4` (`group_id`), 
                               KEY `recommender_two_online_5` (`positemid`)  
                               ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";

    if ($database->executeQuery($table)) {
        echo "`recommender_two_online` Table is Created\n";
    } else {
        echo "`recommender_two_online` Table is not Created\n";
    }

    /*     * ***************end of making table to keep data******************************* */

    // newly added and a.store_id in (select id from twosell_store where active=1)
    $suggestions = $database->getFieldsData('SELECT a.store_id as store_id, a.main_id as main_id, a.match_id as match_id, b.articlenum as articlenum, b.title as title, a.score as score FROM recommender_two_productstorematch as a,twosell_product as b where a.match_id=b.id and a.store_id in (select id from twosell_store where active=1)', array('store_id', 'main_id', 'match_id', 'articlenum', 'title', 'score'));
    print "Transfaring suggestion are started..............\n";
    $i = 0;
    $lastPercent = 0;
    $total = sizeof($suggestions);


    foreach ($suggestions as $suggestion) {

        $group_ids = $database->getFieldsData('SELECT group_id FROM tsln_product_group where product_id=' . $suggestion['match_id'], array('group_id'));
        foreach ($group_ids as $group_id) {
            $group_id = $group_id['group_id'];
        }

        $query = "insert into recommender_two_online (store_id, main_id, positemid, title, group_id, score) values(";
        $query .= $suggestion['store_id'] . ",";
        $query .= $suggestion['main_id'] . ",";
        $query .= "'" . $suggestion['articlenum'] . "',";
        $query .= "'" . $suggestion['title'] . "',";
        $query .= $group_id . ",";
        $query .= $suggestion['score'];
        $query .= ")";
        // echo $query;
        // exit();
        $database->executeQuery($query);
        $i++;
        $lastPercent = $utility->progressBar("Suggestions are transfaring and saved for : ", $i, $total, $lastPercent);
    }
    print "\n Transfaring is completed ..............\n";


    print "\nEnd of recommender_two_online";
    print "\n Transfering all  suggestions are done!!\n";
}

// where active =1 newly added
function if_sold_ingroup_text() {
    print "\n\n";
    print "\n Function if_sold_ingroup_text is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();
    $if_sold_ingroup_texts = $database->getFieldsData('SELECT `meta_group_id`, `if_sold_ingroup_text` FROM `tsln_meta_groups` WHERE `if_sold_ingroup`=1', array('meta_group_id', 'if_sold_ingroup_text'));
    $j = 0;
    $lastPercent1 = 0;
    $total1 = sizeof($if_sold_ingroup_texts);
    foreach ($if_sold_ingroup_texts as $if_sold_ingroup_text) {
        $group_products = $database->getFieldData('SELECT product_id FROM tsln_product_group WHERE group_id=' . $if_sold_ingroup_text['meta_group_id'], 'product_id');
        $text = $if_sold_ingroup_text['if_sold_ingroup_text'];
        foreach ($group_products as $group_product) {
            $product_id = $group_product;
            $stores = $database->getFieldData('select id from twosell_store where active =1', 'id');
            foreach ($stores as $storeId) {
                $query = "insert into recommender_two_online (store_id, main_id, title, group_id, score) values(";
                $query .= $storeId . ",";
                $query .= $product_id . ",";
                $query .= "'" . $text . "',";
                $query .= $if_sold_ingroup_text['meta_group_id'] . ",";
                $query .= 150;
                $query .= ")";
                //echo $query;
                //exit();
                $database->executeQuery($query);
            }
        }
        $j++;
        $lastPercent1 = $utility->progressBar("Suggestions as if_sold_ingroup_text are inserted and saved for : ", $j, $total1, $lastPercent1);
    }
}

// where active =1 newly added
function if_group_selected_text() {
    print "\n\n";
    print "\n Function if_group_selected_text is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();


    $if_group_selected_texts = $database->getFieldsData('SELECT `meta_group_id`, `if_group_selected_text` FROM `tsln_meta_groups` WHERE `if_group_selected`=1', array('meta_group_id', 'if_group_selected_text'));
    //print_r($if_group_selected_texts); exit;
    $j = 0;
    $lastPercent1 = 0;
    $total1 = sizeof($if_group_selected_texts);
    $score = 130;
    foreach ($if_group_selected_texts as $if_group_selected_text) {
        $text = $if_group_selected_text['if_group_selected_text'];
        $suggession_groups = $database->getFieldData('SELECT `group_id` FROM `tsln_group_suggestion` where `suggestion_group_id`=' . $if_group_selected_text['meta_group_id'], 'group_id');
        // where active =1 newly added
        foreach ($suggession_groups as $suggession_group) {
            $group_products = $database->getFieldData('SELECT product_id FROM tsln_product_group WHERE group_id=' . $suggession_group, 'product_id');
            // print_r($group_products); exit;
            foreach ($group_products as $group_product) {
                $product_id = $group_product;
                $stores = $database->getFieldData('select id from twosell_store where active =1', 'id');
                foreach ($stores as $storeId) {
                    $query = "insert into recommender_two_online (store_id, main_id, title, group_id, score) values(";
                    $query .= $storeId . ",";
                    $query .= $product_id . ",";
                    $query .= "'" . $text . "',";
                    $query .= $if_group_selected_text['meta_group_id'] . ",";
                    $query .= $score;
                    $query .= ")";

                    // echo $query;
                    //exit();
                    $database->executeQuery($query);
                }
            }
        }
        $score--;
        $j++;
        $lastPercent1 = $utility->progressBar("Suggestions as if_group_selected_text are inserted and saved for : ", $j, $total1, $lastPercent1);
    }
}

// where active =1 newly added
function delete_suggession_similar_group() {
    print "\n\n";
    print "\n Function delete_suggession_similar_group is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();
    //print_r($if_group_selected_texts); exit;
    $j = 0;
    $lastPercent1 = 0;
    $stores = $database->getFieldData('select id from twosell_store where active =1', 'id');
    foreach ($stores as $storeId) {
        $main_ids = $database->getFieldData('SELECT Distinct main_id FROM recommender_two_online where store_id=' . $storeId, 'main_id');
        //print_r($main_ids); exit();
        $total1 = sizeof($main_ids);
        foreach ($main_ids as $main_id) {
            //echo 'SELECT id, positemid, group_id FROM recommender_two_online where main_id=' . $main_id . ' and store_id=' . $storeId . ' and group_id=group_id order by score DESC';
            $suggession_ids = $database->getFieldsData('SELECT id, positemid, group_id FROM recommender_two_online where main_id=' . $main_id . ' and store_id=' . $storeId, array('id', 'positemid', 'group_id'));
            //print_r($suggession_ids); exit();
            foreach ($suggession_ids as $suggession_id) {
                //echo  'SELECT id, positemid, group_id FROM recommender_two_online where main_id=' . $main_id . ' and store_id=' . $storeId . ' and group_id=' . $suggession_id['group_id'];
                $delete_ids = $database->getFieldsData('SELECT id, positemid, group_id FROM recommender_two_online where main_id=' . $main_id . ' and store_id=' . $storeId . ' and group_id=' . $suggession_id['group_id'] . " order by score DESC", array('id', 'positemid', 'group_id'));
                //print_r($delete_ids); exit();
                if (sizeof($delete_ids) > 0) {
                    for ($i = 1; sizeof($delete_ids) > $i; $i++) {
                        $query = "Delete from recommender_two_online where id =" . $delete_ids[$i]['id'];
                        //  echo $query;
                        $database->executeQuery($query);
                    }
                }
                // echo 'nothing to delete';                
            }
            $j++;
            $lastPercent1 = $utility->progressBar("Duplicates removed for store " . $storeId . " : ", $j, $total1, $lastPercent1);
        }
    }
}

function delete_store_wise_suggession_not_in_group() {
    print "\n\n";
    print "\n Function delete_store_wise_suggession_not_in_group is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();
    //print_r($if_group_selected_texts); exit;
    $j = 0;
    $lastPercent1 = 0;
    $stores = $database->getFieldData('select id from twosell_store where block_products_other_then_group=1 and active =1', 'id');
    foreach ($stores as $storeId) {
        $product_ids = $database->getFieldsData("SELECT id, articlenum FROM twosell_product where  articlenum!='' and id not in (SELECT product_id FROM tsln_product_group)", array('id', 'articlenum'));
        //print_r($main_ids); exit();
        $total1 = sizeof($product_ids);
        if (sizeof($product_ids) > 0) {
            for ($i = 0; sizeof($product_ids) > $i; $i++) {
                $query1 = "Delete from recommender_two_online where main_id =" . $product_ids[$i]['id'] . ' and store_id=' . $storeId;
                // echo $query; exit();
                $database->executeQuery($query1);
                $query2 = "Delete from recommender_two_online where positemid =" . $product_ids[$i]['articlenum'] . ' and store_id=' . $storeId;
                //echo $query; exit();
                $database->executeQuery($query2);
                $j++;
                $lastPercent1 = $utility->progressBar("Products outside form groups are removed for store " . $storeId . " : ", $j, $total1, $lastPercent1);
            }
        }
        // echo 'nothing to delete';               
    }
}

function delete_store_wise_terget_of_blocked_group() {
    print "\n\n";
    print "\n Function delete_store_wise_suggession_not_in_group is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();
    //print_r($if_group_selected_texts); exit;
    $j = 0;
    $lastPercent1 = 0;
    $stores = $database->getFieldsData("select id, block_group_id_target from twosell_store where block_group_id_target!='' and active =1", array('id', 'block_group_id_target'));
    // print_r($stores);exit();
    for ($k = 0; sizeof($stores) > $k; $k++) {
        $block_groupids = explode(';', strtolower($stores[$k]['block_group_id_target']));
        for ($m = 0; $m < sizeof($block_groupids); $m++) {

            $product_ids = $database->getFieldsData("SELECT id, articlenum FROM twosell_product where  articlenum!='' and id in (SELECT product_id FROM tsln_product_group where group_id=" . $block_groupids[$m] . ")", array('id', 'articlenum'));
            //print_r($product_ids); exit();
            $total1 = sizeof($product_ids);
            if (sizeof($product_ids) > 0) {
                for ($i = 0; sizeof($product_ids) > $i; $i++) {
                    $query1 = "Delete from recommender_two_online where main_id =" . $product_ids[$i]['id'] . ' and store_id=' . $stores[$k]['id'];
                    //echo $query; exit();
                    $database->executeQuery($query1);
                    // it was for suggesion $query2 = "Delete from recommender_two_online where positemid =" . $product_ids[$i]['articlenum'] . ' and store_id=' . $stores[$k]['id'];
                    //echo $query; exit();
                    //$database->executeQuery($query2);
                    $j++;
                    $lastPercent1 = $utility->progressBar("Products from blocked groups as terget products are removed for store " . $stores[$k]['id'] . " : ", $j, $total1, $lastPercent1);
                }
            }
        }
        // echo 'nothing to delete';               
    }
}

function delete_store_wise_suggession_of_blocked_group() {
    print "\n\n";
    print "\n Function delete_store_wise_suggession_not_in_group is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();
    //print_r($if_group_selected_texts); exit;
    $j = 0;
    $lastPercent1 = 0;
    $stores = $database->getFieldsData("select id, block_group_id_suggestion from twosell_store where block_group_id_suggestion!='' and active =1", array('id', 'block_group_id_suggestion'));
    // print_r($stores);exit();
    for ($k = 0; sizeof($stores) > $k; $k++) {
        $block_groupids = explode(';', strtolower($stores[$k]['block_group_id_suggestion']));
        for ($m = 0; $m < sizeof($block_groupids); $m++) {

            $product_ids = $database->getFieldsData("SELECT id, articlenum FROM twosell_product where  articlenum!='' and id in (SELECT product_id FROM tsln_product_group where group_id=" . $block_groupids[$m] . ")", array('id', 'articlenum'));
            //print_r($product_ids); exit();
            $total1 = sizeof($product_ids);
            if (sizeof($product_ids) > 0) {
                for ($i = 0; sizeof($product_ids) > $i; $i++) {
                    // this was for terget product $query1 = "Delete from recommender_two_online where main_id =" . $product_ids[$i]['id'] . ' and store_id=' . $stores[$k]['id'];
                    //echo $query; exit();
                    //$database->executeQuery($query1);
                    $query2 = "Delete from recommender_two_online where positemid =" . $product_ids[$i]['articlenum'] . ' and store_id=' . $stores[$k]['id'];
                    //echo $query; exit();
                    $database->executeQuery($query2);
                    $j++;
                    $lastPercent1 = $utility->progressBar("Products from blocked groups as suggesated products are removed for store " . $stores[$k]['id'] . " : ", $j, $total1, $lastPercent1);
                }
            }
        }
        // echo 'nothing to delete';               
    }
}

function Update_description1() {
    print "\n\n";
    print "\n Function Update_description1 is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();
    $database->executeQuery("Update recommender_two_online set description1=''");

    $algorithm_settings = $database->getFieldData("SELECT valueText FROM recommender_two_algorithmsettings WHERE FeatureName='description1'", 'valueText');
    foreach ($algorithm_settings as $algorithm_setting) {
        $setting = $algorithm_setting;
    }
    //  print_r($setting); exit;
    if ($setting == 'long_product_name') {
        $description1 = 'long_product_name';
    } elseif ($setting == 'external_groupname') {
        $description1 = 'screen_text';
    }else
        $description1 = '';
    //print_r($description1); exit;
    if ($description1 != '') {
        $positemids = $database->getFieldData("SELECT distinct positemid FROM recommender_two_online WHERE positemid!=''", 'positemid');
        //print_r($positemids); exit;
        $j = 0;
        $lastPercent1 = 0;
        $total1 = sizeof($positemids);

        //removed "' and " . $description1 . "!=''" from the where clause, since we need articale no even there is no longtext.
        foreach ($positemids as $positemid) {
            $description1_texts = $database->getFieldData("SELECT " . $description1 . " FROM twosell_product where articlenum ='" . $positemid . "'", $description1);
            // echo "SELECT " . $description1 . " FROM twosell_product where articlenum ='" . $positemid . "' and " . $description1 . "!=''";
            //print_r($description1_texts); exit;
            // concate artical no and long text name with ":"
            if (sizeof($description1_texts) > 0) {
                for ($i = 0; sizeof($description1_texts) > $i; $i++) {
                    $query = "Update recommender_two_online set description1='" . $positemid . ": " . $description1_texts[$i] . "' WHERE positemid='" . $positemid . "'";
                    // echo $query;
                    // exit();
                    $database->executeQuery($query);
                }
            }
            $j++;
            $lastPercent1 = $utility->progressBar("Description1 text are inserted and saved for : ", $j, $total1, $lastPercent1);
        }
    }
}

function Update_description2() {
    print "\n\n";
    print "\n Function Update_description2 is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();
    $database->executeQuery("Update recommender_two_online set description2=''");

    $meta_groups = $database->getFieldsData("SELECT meta_group_id, description2 FROM tsln_meta_groups WHERE description2!=''", array('meta_group_id', 'description2'));
    //  print_r($meta_groups); exit;
    for ($a = 0; sizeof($meta_groups) > $a; $a++) {
        $description2 = $meta_groups[$a]['description2'];
        $positemids = $database->getFieldData("select a.articlenum as articlenum from twosell_product as a, tsln_product_group as b where a.id=b.product_id and b.group_id=" . $meta_groups[$a]['meta_group_id'], 'articlenum');
        // print_r($positemids); exit;
        $j = 0;
        $lastPercent1 = 0;
        $total1 = sizeof($positemids);
        foreach ($positemids as $positemid) {
            $query = "Update recommender_two_online set description2='" . $description2 . "' WHERE positemid='" . $positemid . "'";
            $database->executeQuery($query);
            $j++;
            $lastPercent1 = $utility->progressBar("\n Description2 text are inserted and saved for group_id: " . $meta_groups[$a]['meta_group_id'] . "  ", $j, $total1, $lastPercent1);
        }
    }
}

function Update_description3() {
    print "\n\n";
    print "\n Function Update_description3 is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();
    $database->executeQuery("Update recommender_two_online set description3=''");

    $store_desc3_groups = $database->getFieldsData("SELECT id,description3_text_1, group_ids_1, description3_text_2, group_ids_2, description3_text_3, group_ids_3, description3_text_4, group_ids_4, description3_text_5, group_ids_5 FROM `twosell_store` where active=1", array('id', 'description3_text_1', 'group_ids_1', 'description3_text_2', 'group_ids_2', 'description3_text_3', 'group_ids_3', 'description3_text_4', 'group_ids_4', 'description3_text_5', 'group_ids_5'));
    // print_r(sizeof($store_desc3_groups)); exit;

    for ($a = 0; sizeof($store_desc3_groups) > $a; $a++) {
        $description3_text_1 = $store_desc3_groups[$a]['description3_text_1'];
        $group_ids_1 = $store_desc3_groups[$a]['group_ids_1'];

        $description3_text_2 = $store_desc3_groups[$a]['description3_text_2'];
        $group_ids_2 = $store_desc3_groups[$a]['group_ids_2'];

        $description3_text_3 = $store_desc3_groups[$a]['description3_text_3'];
        $group_ids_3 = $store_desc3_groups[$a]['group_ids_3'];

        $description3_text_4 = $store_desc3_groups[$a]['description3_text_4'];
        $group_ids_4 = $store_desc3_groups[$a]['group_ids_4'];

        $description3_text_5 = $store_desc3_groups[$a]['description3_text_5'];
        $group_ids_5 = $store_desc3_groups[$a]['group_ids_5'];

        if (( $description3_text_1 != '') && ($group_ids_1 != '')) {
            description3($description3_text_1, $group_ids_1, $store_desc3_groups[$a]['id']);
        }

        if (( $description3_text_2 != '') && ($group_ids_2 != '')) {
            description3($description3_text_2, $group_ids_2, $store_desc3_groups[$a]['id']);
        }

        if (( $description3_text_3 != '') && ($group_ids_3 != '')) {
            description3($description3_text_3, $group_ids_3, $store_desc3_groups[$a]['id']);
        }

        if (( $description3_text_4 != '') && ($group_ids_4 != '')) {
            description3($description3_text_4, $group_ids_4, $store_desc3_groups[$a]['id']);
        }

        if (( $description3_text_5 != '') && ($group_ids_5 != '')) {
            description3($description3_text_5, $group_ids_5, $store_desc3_groups[$a]['id']);
        }
        echo "\n Description3 text are inserted and saved for store_id: " . $store_desc3_groups[$a]['id'] . '\n';
    }
}

function description3($description3_text, $group_ids, $store_id) {
    $database = new DatabaseUtility();
    $groupids = explode(';', $group_ids);
    for ($m = 0; $m < sizeof($groupids); $m++) {
        $query = "Update recommender_two_online set description3='" . $description3_text . "' WHERE group_id='" . $groupids[$m] . "' and store_id=" . $store_id;
        //echo $query;
        $database->executeQuery($query);
    }
}

