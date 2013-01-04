<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


print "\n\n";
print "\n\n";
print "\n File redundantRecommendation is Started and date :" . date("Ymd G:i:s") . "\n";

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

function main() {
    $array_ini = parse_ini_file("baseClasses/config.ini", true);
    $functions = $array_ini['redundantRecommendation'];
    foreach ($functions as $function => $para) {
        print "running function: " . $function . "\n";
        call_user_func_array($function, $para);
        print "function: " . $function . " is done!!!\n";
    }
}

main();

function makeRedundantRecommendation($databaseName) {
    print "\n\n";
    print "\n\n";
    print "\n Function makeRedundantRecommendation is Started and date :" . date("Ymd G:i:s") . "\n";
    print "redundant recommendation calculation starts...............\n";
    $database = new DatabaseUtility();
    $utility = new Utility();

    $redundantSoldProducts = $database->getFieldsData('SELECT product_id, count(purchase_id) as freq, max(n_items) as maxQtySell FROM twosell_purchasedproduct WHERE n_items >= 2 group by product_id order by freq desc', array('product_id', 'freq', 'maxQtySell'));
    $redundantSoldProducts = $utility->mappingArray($redundantSoldProducts, 'product_id');
    //taking price infor for eleminating product having more price than allowed percent of target price
    $productPrices = $database->getFieldsData('SELECT b.product_id as productId, avg(a.price) as price FROM `twosell_pricinghistory` as a, twosell_productinstore as b WHERE a.`priced_product_id`=b.id and a.price>0 group by productId', array('productId', 'price'));
    $productPrices = $utility->mappingArray($productPrices, 'productId', 'price');
    //taking price infor for eleminating product having more price than allowed percent of target price end
    $RedundantRecommendationPrice = $database->getFieldData("select Value from recommender_two_algorithmsettings where FeatureName = 'RedundantRecommendationPrice'", "Value");
    $RedundantRecommendationPrice = $RedundantRecommendationPrice[0];
    foreach ($productPrices as $product => $price) {
        if ($price > $RedundantRecommendationPrice && key_exists($product, $redundantSoldProducts))
            unset($redundantSoldProducts[$product]);
    }
    $database->executeQuery("DROP TABLE IF EXISTS `recommender_two_redundant`;");
    $database->executeQuery("CREATE TABLE IF NOT EXISTS `recommender_two_redundant` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `product_id` int(11) NOT NULL,
                              `freq` int(11) NOT NULL,
                              `maxQtySell` decimal(60,2) NOT NULL,
                              PRIMARY KEY (`id`)
                            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
                            ");

    $qIndexed = "CREATE INDEX recommender_two_redundant ON recommender_two_redundant (product_id)";
    if ($database->executeQuery($qIndexed)) {
        echo "Table is Indexed \n";
    } else {
        echo "Table is not Indexed \n";
    }

    foreach ($redundantSoldProducts as $p) {
        $query = "insert into recommender_two_redundant(product_id, freq, maxQtySell) values(" . $p['product_id'] . "," . $p['freq'] . "," . $p['maxQtySell'] . ")";
        $database->executeQuery($query);
    }
    print "redundant recommendation calculation is done\n";

    print "Filtering not redundant products from the list starts ...........\n";

    if ($databaseName == 'elkedjan') {
        $database->executeQuery("Delete from recommender_two_redundant where product_id in (3906,472,370,441,25,44,9,359,1123,3987,142,1397,863,75,827,924,387,1175,5926,1244,409,1145,55,1962,2063,2145,
2589,291,48,5573,1010,679,3581,885,21,1173,1954,1400,639,6,396,5718,1972,5258,483,527,346,1217,495,814,287,1768,305,1107,1171,1004,626,2868,5886,497,777,538,369,1074,2498,911,371,4046,1568,2272,
1306,177,146,1551,4409,519,1172,191,1102,1357,89,5230,766,767,5663,5688,1762,2423,1178,5328,1461,1198,2661,2176,2889,5824,1687,963,1105,1483,2329,2673,1658,123,2476,6465,3289,1103,1742,309,2885,
5874,127,3239,113,3020,517,1025,1794,100,670,1818,4,2767,980,446,401,2617,1952,2985,2908,1197,2029,26,1312,163,3259,8,2274,2757,3315,3733,4779,2292,2219,899,2775,3560,2283,783,1995,3345,5642,
5993,2873,322,1569,6451,7207,2575,3071,151,2264,6141,2948,2300,2878,5327,1935,3419,1063,6337,4018,293,745,2186,1075,3739,5385,3656,5961,185,3865,513,5705,5357,1653,936,1061,1864,1182,1315,4400,
5580,1853,1996,7242,5069,2224,3781,4505,352,2956,134,2691,1873,1985,6290,6476,912,1854,327,6096,2756,386,1558,1203,3440,5419,2612,4071,1570,1673,5783,3204,1760,2174,205,436,2086,4378,869,1518,
4080,1703,380,2118,1250,5070,5558,5918,1340,2907,3845,4764,141,5832,5444,402,3878,23,1393,3304,2213,5020,3076,6178,6457,2350,4346,4448,116,7328,844,1229,6218,6108,3305,3174,467,1632,6535,3299,1206,
4142,4775,1477,5572,6342,336,6649,7026,3409,7388,3846,2779,4638,4704,5649,5247,2313,6595,385,7302,713,2972,4118,4566,5153,5351,1973,2103,6683,1437,6846,3296,714,2043,4740,1817,1354,5854,4784,
575,925,3890,4871,188,412,1333,1875,3728,6745,420,2655,4525,1026,5495,1262,6282,1707,1791,1890,3796,4780,4890,101,5376,1399,5859,6129,199,2657,6490,6886,7353,198,1816,4712,4763,4434,5037,1987,
342,6262,6599,6795,6238,7311,7384,3435,3929,4139,4122,1585,4621,1146,1326,5336,3080,2184,6446,776,3797,2119,3847,4680,4655,5302,5255,5860,5995,124,2963,6834,593,3823,1471,4586,364,4813,5593,
1571,6796,12,7385,4373,4730,633,2398,1147,2287,5819,6015,2189,2960,2004,6707,3568,2626,318,4068,4777,4224,395,642,1790,821,2897,2991,7025,3787,3683,244,1048,741,820,2654,7386,4024,511,41,4383,
354,1148,2050,1557,5881,1201,3788,2929,4683,4131,109,341,1692,1151)");
    } elseif ($databaseName == 'elon') {

        $database->executeQuery("Delete from recommender_two_redundant where product_id in (156,391,2057,39,1119,303,261,345,439,235,1032,30,3493,947,139,300,647,966,1243,898,1263,655,1856,164,
2075,376,1249,140,412,1929,583,1841,835,3561,525,2786,554,926,157,2691,588,1023,538,385,3088,17,427,1860,577,1393,3348,541,1064,1138,2436,1783,40,1635,608,543,2553,615,2073,1826,255,343,1031,341,
1262,1375,533,373,1914,956,1877,2645,1036,2384,1850,3208,330,1520,378,779,1316,289,150,2424,2789,1184,2039,2221,2127,2698,3785,1134,1902,2311,3141,630)");
    }else
        ;

    print "Filtering not redundant products from the list is done\n";
}

?>
