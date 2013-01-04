<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//ini_set('default_charset','utf-8');
print "\n\n";
print "\n\n";
print "\n File Variation is Started and date :" . date("Ymd G:i:s") . "\n";

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

function main() {
    $array_ini = parse_ini_file("baseClasses/config_var.ini", true);
    $functions = $array_ini['variation'];
    foreach ($functions as $function => $para) {
        print "running function: " . $function . "\n";
        call_user_func_array($function, $para);
        print "function: " . $function . " is done!!!\n";
    }
}

main();

// where active =1 newly added
function add_variation_suggestion() {
    print "\n\n";
    print "\n Function add_variation_suggestion is Started and date :" . date("Ymd G:i:s") . "\n";

    $database = new DatabaseUtility();
    $utility = new Utility();
    
    $database->executeQuery("Drop TABLE IF EXISTS tsln_variation_text");
    $database->executeQuery("Create table tsln_variation_text as (SELECT * FROM statoil_canonical.tsln_variation_text)");


    $database->executeQuery("Drop TABLE IF EXISTS tsln_current_weather");
    $database->executeQuery("Create table tsln_current_weather as (SELECT a.validdate, a.weathersymbol, a.temperature, a.city_id,  a.place_id, now() as time FROM weather_dev.data as a, statoil_calc.twosell_store as b WHERE b.city_id=a.city_id and b.active=1 and a.validdate > DATE_SUB(now(), INTERVAL 40 MINUTE) and a.validdate < DATE_ADD(now(), INTERVAL 40 MINUTE) group by a.city_id)");

    $database->executeQuery("Drop table IF EXISTS recommender_two_variation");
    $database->executeQuery("CREATE TABLE IF NOT EXISTS recommender_two_variation (
  id int(11) NOT NULL AUTO_INCREMENT,
  store_id int(11) NOT NULL,
  main_id int(11) NOT NULL,
  positemid varchar(250) NOT NULL,
  title varchar(250) NOT NULL,  
  PRIMARY KEY (`id`),
  KEY recommender_two_online_1 (`main_id`),  
  KEY recommender_two_online_3 (`store_id`),  
  KEY recommender_two_online_5 (`positemid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

    $ar_id = 0;
    $arstids = $database->getFieldsData('SELECT id FROM weather_dev.tsln_seasons WHERE StartDuration < NOW() and  NOW() <EndDuration', array('id'));
    foreach ($arstids as $arstid) {
        $ar_id = $arstid['id'];
    }
    $cele_id = 0;
    $celeids = $database->getFieldsData('SELECT id FROM  weather_dev.tsln_celebration_day WHERE StartDuration < NOW() and  NOW() <EndDuration', array('id'));
    foreach ($celeids as $celeid) {
        $cele_id = $celeid['id'];
    }


    $day_nmes = $database->getFieldsData('SELECT DAYNAME(NOW()) as day', array('day'));
    // print_r($day_nmes);
    $day = 0;
    foreach ($day_nmes as $day_nme) {
        if ($day_nme['day'] == 'monday')
            $day = 1;
        if ($day_nme['day'] == 'tuesday')
            $day = 2;
        if ($day_nme['day'] == 'wednesday')
            $day = 3;
        if ($day_nme['day'] == 'thursday')
            $day = 4;
        if ($day_nme['day'] == 'friday')
            $day = 5;
        if ($day_nme['day'] == 'saturday')
            $day = 6;
        if ($day_nme['day'] == 'sunday')
            $day = 7;
    }

    $week_id = 0;
    // echo 'SELECT id FROM tsln_week WHERE StartDuration <=' .$day .' and ' . $day . ' <= EndDuration';
    $weeks = $database->getFieldsData('SELECT id FROM weather_dev.tsln_week WHERE StartDuration <=' . $day . ' and ' . $day . ' <= EndDuration', array('id'));
    //print_r($weeks);
    foreach ($weeks as $week) {
        $week_id = $week['id'];
    }

    $day_id = 0;
    $daytimes = $database->getFieldsData('SELECT id FROM  weather_dev.tsln_days WHERE StartDuration <= Time(NOW()) and  Time(NOW()) <= EndDuration', array('id'));
    //print_r($daytimes);
    foreach ($daytimes as $daytime) {
        $day_id = $daytime['id'];
    }


    $curr_weathers = $database->getFieldsData('SELECT validdate, weathersymbol, temperature, city_id FROM tsln_current_weather', array('validdate', 'weathersymbol', 'temperature', 'city_id'));
    //print_r($daytimes);
    foreach ($curr_weathers as $curr_weather) {
        $weathersymbol = 0;
        // echo 'SELECT id FROM tsln_week WHERE StartDuration <=' .$day .' and ' . $day . ' <= EndDuration';
        $weathersymbol_ids = $database->getFieldsData('SELECT id FROM  weather_dev.tsln_weather WHERE (StartDuration <=' . $curr_weather['weathersymbol'] . ' and ' . $curr_weather['weathersymbol'] . ' <= EndDuration) or individual in (' . $curr_weather['weathersymbol'] . ')', array('id'));
        //print_r($weeks);
        foreach ($weathersymbol_ids as $weathersymbol_id) {
            $weathersymbol = $weathersymbol_id['id'];
        }

        $temperature = 0;
        // echo 'SELECT id FROM tsln_week WHERE StartDuration <=' .$day .' and ' . $day . ' <= EndDuration';
        $temperature_ids = $database->getFieldsData('SELECT id FROM  weather_dev.tsln_temparature WHERE StartDuration <=' . $curr_weather['weathersymbol'] . ' and ' . $curr_weather['weathersymbol'] . ' <= EndDuration', array('id'));
        //print_r($weeks);
        foreach ($temperature_ids as $temperature_id) {
            $temperature = $temperature_id['id'];
        }

        $variations_texts = $database->getFieldsData("SELECT variation_id, variation_text, group_ids, store_ids FROM tsln_variation_text where 
            (SUBSTRING(variation_id,1,1)='0' or SUBSTRING(variation_id,1,1)='" . $ar_id . "') and 
            (SUBSTRING(variation_id,2,1)='0' or SUBSTRING(variation_id,2,1)='" . $cele_id . "') and 
            (SUBSTRING(variation_id,3,1)='0' or SUBSTRING(variation_id,3,1)='" . $week_id . "') and 
            (SUBSTRING(variation_id,4,1)='0' or SUBSTRING(variation_id,4,1)='" . $day_id . "') and
            (SUBSTRING(variation_id,5,1)='0' or SUBSTRING(variation_id,5,1)='" . $weathersymbol . "') and
            (SUBSTRING(variation_id,6,1)='0' or SUBSTRING(variation_id,6,1)='" . $temperature . "')",                
                array('variation_id', 'variation_text', 'group_ids', 'store_ids'));
        
        /*echo "SELECT variation_id, variation_text, group_ids, store_ids FROM tsln_variation_text where 
            (SUBSTRING(variation_id,1,1)='0' or SUBSTRING(variation_id,1,1)='" . $ar_id . "') and 
            (SUBSTRING(variation_id,2,1)='0' or SUBSTRING(variation_id,2,1)='" . $cele_id . "') and 
            (SUBSTRING(variation_id,3,1)='0' or SUBSTRING(variation_id,3,1)='" . $week_id . "') and 
            (SUBSTRING(variation_id,4,1)='0' or SUBSTRING(variation_id,4,1)='" . $day_id . "') and
            (SUBSTRING(variation_id,5,1)='0' or SUBSTRING(variation_id,5,1)='" . $weathersymbol . "') and
            (SUBSTRING(variation_id,6,1)='0' or SUBSTRING(variation_id,6,1)='" . $temperature . "')";
         * 
         */
       // print_r($variations_texts); 
        // $score = 250;

        foreach ($variations_texts as $variations_text) {
            $variation_id = $variations_text['variation_id'];
            $variation_text = $variations_text['variation_text'];

            if ($variations_text['store_ids'] != '') {
                if ($variations_text['store_ids'] == '*') {
                    $store_ids_query = 'select id from twosell_store where active =1 and city_id=' . $curr_weather['city_id'];
                } else {
                    $store_ids_query = 'select id from twosell_store where active =1 and city_id=' . $curr_weather['city_id'] . ' id in (' . str_replace($variations_text['store_ids'], ';', ',') . ')';
                }
            }

            if (strpbrk($variations_text['group_ids'], ';') != FALSE) {
                $group_ids = str_replace(';', ',', $variations_text['group_ids']);
            } else {
                $group_ids = $variations_text['group_ids'];
            }

            // echo $store_ids_query;
            $stores = $database->getFieldData($store_ids_query, 'id');
            foreach ($stores as $storeId) {

                $productsIds = $database->getFieldData("SELECT distinct product_id FROM tsln_product_group WHERE group_id in (" . $group_ids . ")", 'product_id');


                //echo "SELECT distinct product_id FROM tsln_product_group WHERE group_id in (" . $group_ids . ")";
                // print_r($productsIds);
                foreach ($productsIds as $productsId) {
                    $query = "insert into recommender_two_variation (store_id, main_id, positemid, title) values(";
                    $query .= $storeId . ",";
                    $query .= $productsId . ",";
                    $query .= "'" . $variation_id . "',";
                    $query .= "'" . $variation_text . "'";
                    $query .= ")";
                    //echo $query;
                    //exit();
                    $database->executeQuery($query);
                }
            }
        }
    }
     print "\n\n";
    print "\n Function add_variation_suggestion is done and date :" . date("Ymd G:i:s") . "\n";
}



?>