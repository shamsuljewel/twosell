<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
print "\n\n";
print "\n\n";
print "\n File replacement is Started and date :" . date("Ymd G:i:s") . "\n";

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

function main() {
    $array_ini = parse_ini_file("baseClasses/config.ini", true);
    $functions = $array_ini['replacement'];
    foreach ($functions as $function => $para) {
        print "running function: " . $function . "\n";
        call_user_func_array($function, $para);
        print "function: " . $function . " is done!!!\n";
    }
}

main();

function getMapBetweenProductStore($productsInStore) {
    $mapProductStore = array();
    foreach ($productsInStore as $r) {
        if (!key_exists($r['product_id'], $mapProductStore))
            $mapProductStore[$r['product_id']] = array();
        $mapProductStore[$r['product_id']][] = $r['store_id'];
    }
    return $mapProductStore;
}

function cbrReplacement() {
    print "\n\n";
    print "\n\n";
    print "\n Function cbrReplacement is Started and date :" . date("Ymd G:i:s") . "\n";
    $databaseUtility = new DatabaseUtility();
    $utility = new Utility();


    //cleaning existing tsl_recommendation table
    $qdrop = "Drop table tsl_recommendationCBR";

    if ($databaseUtility->executeQuery($qdrop)) {
        echo "tsl_recommendationCBR Table is droped\n";
    } else {
        echo "tsl_recommendationCBR Table is not droped\n";
    }

    $qAll = "CREATE TABLE `tsl_recommendationCBR` (nonActive int(11), active int(11), store_id int(11))";
    if ($databaseUtility->executeQuery($qAll)) {
        echo "tsl_recommendationCBR Table is created\n";
    } else {
        echo "tsl_recommendationCBR Table is not created\n";
    }

    $qIndexed = "CREATE INDEX tsl_recommendationCBR ON tsl_recommendationCBR (nonActive, active, store_id)";
    if ($databaseUtility->executeQuery($qIndexed)) {
        echo "Table is Indexed \n";
    } else {
        echo "Table is not Indexed \n";
    }


    $productIds = $databaseUtility->getFieldData('SELECT productId FROM tsl_producttaging where status=0', 'productId');
    $query = 'SELECT  productId,name,itemName,brandName,colorName,attributeName,otherName,priceMean,priceStddev,productgroup_id FROM tsl_producttaging where status=1';
    $fields = array('productId', 'name', 'itemName', 'brandName', 'colorName', 'attributeName', 'otherName', 'priceMean', 'priceStddev', 'productgroup_id');
    $tsl_activeproductprice = $databaseUtility->getFieldsData($query, $fields);
    $tsl_activeproductprice = array_map("unserialize", array_unique(array_map("serialize", $tsl_activeproductprice)));
    $query = 'select featureName,weight,"" as featureValue from tsl_weight';
    $fields = array('featureName', 'weight', 'featureValue');
    $allWeights = $databaseUtility->getFieldsData($query, $fields);
    $productsInStore = $databaseUtility->getFieldsData('select product_id, store_id from twosell_productinstore where active = 1', array('product_id', 'store_id'));
    $productsInStore = getMapBetweenProductStore($productsInStore);

    //for the progress bar
    $currentlyDone = 0;
    $lastPercent = 0;
    $total = sizeof($productIds);
    //end for the progress bar

    foreach ($productIds as $productId) {
        $query = 'SELECT  distinct productId, name,itemName,brandName,colorName,attributeName,otherName,priceMean,priceStddev,productgroup_id FROM   tsl_producttaging WHERE status=0 and productId=' . $productId;
        $fields = array('productId', 'name', 'itemName', 'brandName', 'colorName', 'attributeName', 'otherName', 'priceMean', 'priceStddev', 'productgroup_id');
        $tsl_nonactiveproduct = $databaseUtility->getFieldsData($query, $fields);
        //echo $query. "\n";        continue;
        $stores = $databaseUtility->getFieldData("select store_id from twosell_productinstore where product_id = " . $productId, 'store_id');
        $weights = array();
        foreach ($allWeights as $key => $weight) {
            /* putting tag for weight table as feature value */
            if ($weight['featureName'] == 'itemname')
                $allWeights[$key]['featureValue'] = $tsl_nonactiveproduct[0]['itemName'];
            else if ($weight['featureName'] == 'company_group')
                $allWeights[$key]['featureValue'] = $tsl_nonactiveproduct[0]['productgroup_id'];
            else if ($weight['featureName'] == 'brandname')
                $allWeights[$key]['featureValue'] = $tsl_nonactiveproduct[0]['brandName'];
            else if ($weight['featureName'] == 'colorname')
                $allWeights[$key]['featureValue'] = $tsl_nonactiveproduct[0]['colorName'];
            else if ($weight['featureName'] == 'attributename')
                $allWeights[$key]['featureValue'] = $tsl_nonactiveproduct[0]['attributeName'];
            else if ($weight['featureName'] == 'othername')
                $allWeights[$key]['featureValue'] = $tsl_nonactiveproduct[0]['otherName'];
            else if ($weight['featureName'] == 'pricemean')
                $allWeights[$key]['featureValue'] = $tsl_nonactiveproduct[0]['priceMean'];
            else if ($weight['featureName'] == 'pricestddev')
                $allWeights[$key]['featureValue'] = $tsl_nonactiveproduct[0]['priceStddev'];
            else if ($weight['featureName'] == 'threshold')
                $threshold = $weight['weight']; //taking threshold;
            else if ($weight['featureName'] == 'number_of_case')
                $thresholdNumber = $weight['weight']; //taking threshold for number of cases;
            else if ($weight['featureName'] == 'selling_rate_duration')
                $sales_rate_duration = $weight['weight']; //taking $sales_rate_duration for number of cases;
            else
                ;
            /* end of putting tag for weight table as feature value */
            $weights[$weight['featureName']] = $weight['weight'];
        }
        $cbr = new CBR();
        $cbrOutput = $cbr->cbrCalculation($tsl_activeproductprice, $tsl_nonactiveproduct[0], $weights, $threshold);


        if (!empty($cbrOutput)) {
            for ($i = 0; $i < sizeof($cbrOutput); $i++) {
                $cbrOutput[$i]['similarity'] = round($cbrOutput[$i]['similarity'], 2) . '%';
            }
            if ($thresholdNumber < sizeof($cbrOutput))
                $cbrOutput = array_slice($cbrOutput, 0, $thresholdNumber);

            //print_r($cbrOutput); exit();


            /*             * **********************this note is written on 2011.09.26. Note: currently we are not concerned to recent sale for the cbr. So the section is deactivated            
              $maxDate = $databaseUtility->getFieldData('SELECT SUBDATE(max(time), INTERVAL ' . ($sales_rate_duration * 30) . ' DAY) as date FROM `tsl_producthistory`', 'date');
              $maxDate = $maxDate[0];


              $productIds = $utility->getValuesOfKey($cbrOutput, 'productId');
              $whereClause = '';
              for ($i = 0; $i < sizeof($productIds); $i++) {
              if ($i == 0)
              $whereClause = ' where (productId = ' . $productIds[$i];
              else
              $whereClause .= ' or productId = ' . $productIds[$i];
              }

              $query = 'select productId, name,sum(noSolditem) as total from tsl_producthistory ' . $whereClause . ') and time >"' .
              $maxDate . '" group by productId, name order by total desc';


              $title = 'Sales Rate (' . $sales_rate_duration . ' months)' . '<br><font size="3pt">(' . $tsl_nonactiveproduct[0]['name'] . ')</font>';
              $fieldNames = array('productId', 'name', 'total');
              $data = $databaseUtility->getFieldsData($query, $fieldNames);
             * ************************************************************************************************************************************************* */
            $data = $cbrOutput; // this line is a extra and should be erased when the above deactive part will be active.

            if (!empty($data)) {
                foreach ($stores as $store) {
                    $gotReplace = false;
                    foreach ($data as $output) {
                        if (key_exists($output['productId'], $productsInStore) && in_array($store, $productsInStore[$output['productId']])) {
                            $query = 'insert into tsl_recommendationCBR values(' . $tsl_nonactiveproduct[0]['productId'] . ',' . $output['productId'] . ',' . $store . ')';
                            $databaseUtility->executeQuery($query);
                            $gotReplace = true;
                            continue;
                        }
                    }
                    if (!$gotReplace) {
                        $query = 'insert into tsl_recommendationCBR values(' . $tsl_nonactiveproduct[0]['productId'] . ',' . $tsl_nonactiveproduct[0]['productId'] . ',' . $store . ')';
                        $databaseUtility->executeQuery($query);
                    }
                }
            } else {
                foreach ($stores as $store) {
                    $gotReplace = false;
                    foreach ($cbrOutput as $output) {
                        if (key_exists($output['productId'], $productsInStore) && in_array($store, $productsInStore[$output['productId']])) {
                            $query = 'insert into tsl_recommendationCBR values(' . $tsl_nonactiveproduct[0]['productId'] . ',' . $output['productId'] . ',' . $store . ')';
                            $databaseUtility->executeQuery($query);
                            $gotReplace = true;
                            continue;
                        }
                    }
                    if (!$gotReplace) {
                        $query = 'insert into tsl_recommendationCBR values(' . $tsl_nonactiveproduct[0]['productId'] . ',' . $tsl_nonactiveproduct[0]['productId'] . ',' . $store . ')';
                        $databaseUtility->executeQuery($query);
                    }
                }
            }
        } else {
            foreach ($stores as $store) {
                $query = 'insert into tsl_recommendationCBR values(' . $tsl_nonactiveproduct[0]['productId'] . ',' . $tsl_nonactiveproduct[0]['productId'] . ',' . $store . ')';
                $databaseUtility->executeQuery($query);
                //echo $tsl_nonactiveproduct[0]['name'];
            }
        }
        //for the progress bar
        $currentlyDone++;
        $lastPercent = $utility->progressBar("CBR Calculation is done & saved for : ", $currentlyDone, $total, $lastPercent);
        //end for the progress bar
    }
    print "\nCBR Calculation is done!!!\n";
    /* function margingPushCBRReplacement() {
      /// merging tsl_recommenationCBR and tst_recommendationPushlista

      $databaseUtility = new DatabaseUtility();
      $utility = new Utility();
      $qdrop = "Drop table tsl_recommendation";

      if ($databaseUtility->executeQuery($qdrop)) {
      echo "tsl_recommendation Table is droped  \n";
      } else {
      echo "tsl_recommendation Table is not droped \n";
      }

      $qAll = "CREATE TABLE  tsl_recommendation (nonActive int(11), active	int(11) )";
      if ($databaseUtility->executeQuery($qAll)) {
      echo "<h1>Table is created</h1>";
      } else {
      echo "<h1>Table is not created</h1>";
      }

      $recommendation = $databaseUtility->getFieldsData('select nonActive,active from tsl_recommendationCBR', array('nonActive', 'active'));
      $recommendationPushlista = $databaseUtility->getFieldsData('select nonActive,active from tsl_recommendationPushlista', array('nonActive', 'active'));
      $data = array_merge($recommendation, $recommendationPushlista);
      foreach ($data as $row) {
      $query = 'insert into tsl_recommendation values (' . $row['nonActive'] . ',' . $row['active'] . ')';
      $databaseUtility->executeQuery($query);
      }
      } */

    function matrixReplacementCBRDeactive() {
        print "\n\n";
        print "\n\n";
        print "\n Function matrixReplacementCBRDeactive is Started and date :" . date("Ymd G:i:s") . "\n";
        print "matrixReplacementCBRDeactive replacement accordingly deactive starts...............\n";
        $database = new DatabaseUtility();
        $utility = new Utility();
        $database->executeQuery("update recommender_two_productstorematch as a, tsl_recommendationCBR as b set a.match_id = b.active  where a.match_id = b.nonActive and a.store_id = b.store_id");
        $stores = $database->getFieldData("select id from twosell_store", 'id');
        foreach ($stores as $store) {
            $database->executeQuery("delete from recommender_two_productstorematch where store_id = " . $store . " and match_id  in (select nonActive from tsl_recommendationCBR where nonActive = active and store_id = " . $store . ")");
        }
        print "\nreplacing deactive is done!!! ...........\n";
    }

}

?>
