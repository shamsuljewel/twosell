<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__)."/baseClasses/{$className}.php";
}

function main() {
    $array_ini = parse_ini_file("baseClasses/config_canonical.ini", true);
    $functions = $array_ini['xmlToMySql'];
    foreach ($functions as $function => $para) {
        print "running function: " . $function . "\n";
        call_user_func_array($function, $para);
        print "function: " . $function . " is done!!!\n";
    }
}

main();

function importIdentifyNewProductPushlista($dirPath) {

    $databaseUtility = new DatabaseUtilityXML();
    $utility = new Utility();
//C:/mobyen_work/24_08_11/pagoda/twosell-elkedjan/pushlista/files/
    // $dirPath = "../puslistafiles/files/";

    $file = opendir($dirPath);
    $filenames = array();
    while ($a = readdir($file)) {
        if (is_file($dirPath . $a))
            $filenames[] = $dirPath . $a;
    }

    echo "<h1>Started ...</h1>";


//creating database table


    $fields = array();
    foreach ($filenames as $filename) {
        $xmlfile = simplexml_load_file($filename);
        $articles = $xmlfile->artiklar[0];

        foreach ($articles as $article) {
            $alias = 0;
            foreach ($article as $tag) {
                $tag = $tag->getName();
                $tag = str_replace('å', 'a', $tag);
                $tag = str_replace('ä', 'a', $tag);
                $tag = str_replace('ö', 'o', $tag);
                if ($tag == 'alias') {
                    $tag .= $alias;
                    $alias++;
                }
                $fields[] = $tag;
            }
            $fields = array_unique($fields);
        }
    }

    $createTableQuery = 'create table tsl_pushlista(';
    foreach ($fields as $field) {
        $createTableQuery .= $field . ' varchar(200), ';
    }

    $createTableQuery = substr_replace($createTableQuery, '', -2);
    $createTableQuery .= ')';

    $qdrop = "Drop table tsl_pushlista";

    if ($databaseUtility->executeQuery($qdrop)) {
        echo "tsl_pushlista Table is droped";
    } else {
        echo "tsl_pushlista Table is not droped</h1>";
    }

    if ($databaseUtility->executeQuery($createTableQuery)) {
        echo " tsl_pushlista Table is created</h1>";
    } else {
        echo "tsl_pushlista Table is not created</h1><br>" . $createTableQuery . '<br>';
    }


    if ($databaseUtility->executeQuery('ALTER TABLE  `tsl_pushlista` CHANGE  `beskrivning`  `beskrivning` VARCHAR( 500 )')) {
        echo "<h1>Table is altered</h1>";
    } else {
        echo "<h1>Table is not altered</h1>";
    }

    $qIndexed = "CREATE INDEX tsl_pushlista ON tsl_pushlista (artikelnr)";
    if ($databaseUtility->executeQuery($qIndexed)) {
        echo "Table is Indexed \n";
    } else {
        echo "Table is not Indexed \n";
    }

    echo 'total fields = ' . sizeof($fields) . '<br>';


//importing data

    foreach ($filenames as $filename) {
        $xmlfile = simplexml_load_file($filename);
        $articles = $xmlfile->artiklar[0];

        foreach ($articles as $article) {
            $query = "insert into tsl_pushlista(";
            $alias = 0;
            foreach ($article as $tag) {
                $tag = $tag->getName();
                $tag = str_replace('å', 'a', $tag);
                $tag = str_replace('ä', 'a', $tag);
                $tag = str_replace('ö', 'o', $tag);
                if ($tag == 'alias') {
                    $tag .= $alias;
                    $alias++;
                }
                $query .= $tag . ", ";
            }

            $query = substr_replace($query, '', -2);
            $query .= ") values(";

            foreach ($article as $tag) {
                $tag = $tag . '';
                $tag = mb_convert_encoding($tag, "iso-8859-1", "utf-8");
                $query .="'" . $tag . "', ";
            }
            $query = substr_replace($query, '', -2);
            $query .= ");";

            $databaseUtility->executeQuery($query);
        }

        echo "total record (" . $filename . ")= " . sizeof($articles) . "\n";
        // exit();
    }


// query to produce discontinued products
// 


    $qdrop = "Drop table tsl_discontinued";

    if ($databaseUtility->executeQuery($qdrop)) {
        echo "tsl_discontinued Table is droped";
    } else {
        echo "tsl_discontinued Table is not droped</h1>";
    }

    $queryDisCon = "Create table tsl_discontinued as SELECT * FROM `tsl_pushlista` WHERE `artikelnr` in (SELECT `articlenum` FROM `twosell_product`) and `utgatt`=1";
    if (mysql_query($queryDisCon)) {
        echo '<font color="#990000"> The table tsl_discontinued has been successfully added!  </font>';
    }

    $qIndexed = "CREATE INDEX tsl_discontinued ON tsl_discontinued (artikelnr)";
    if ($databaseUtility->executeQuery($qIndexed)) {
        echo "Table is Indexed \n";
    } else {
        echo "Table is not Indexed \n";
    }

//query to make discontinuted product with replacement product
//

    $qdrop = "Drop table tsl_discontinuedwithreplacement";

    if ($databaseUtility->executeQuery($qdrop)) {
        echo "tsl_discontinuedwithreplacement Table is droped";
    } else {
        echo "tsl_discontinuedwithreplacement Table is not droped</h1>";
    }

    $queryDisConWithReplace = "Create table tsl_discontinuedwithreplacement as SELECT * from tsl_discontinued where ersattav !='' and `artikelnr` not in (select id from twosell_product)";
    if (mysql_query($queryDisConWithReplace)) {
        echo '<font color="#990000"> The table tsl_discontinuedwithreplacement has been successfully added!  </font>';
    }

    $qIndexed = "CREATE INDEX tsl_discontinuedwithreplacement ON tsl_discontinuedwithreplacement (artikelnr)";
    if ($databaseUtility->executeQuery($qIndexed)) {
        echo "Table is Indexed \n";
    } else {
        echo "Table is not Indexed \n";
    }

//query to make new productlist found a replacement product with replacement product
//

    $qdrop = "Drop table tsl_productnew";

    if ($databaseUtility->executeQuery($qdrop)) {
        echo "tsl_productnew Table is droped";
    } else {
        echo "tsl_productnew Table is not droped</h1>";
    }

    $queryNewProduct = "Create table tsl_productnew as SELECT a.* FROM `tsl_pushlista` as a, `tsl_discontinuedwithreplacement` as b WHERE a.`artikelnr` = b.ersattav ";
    if (mysql_query($queryNewProduct)) {
        echo '<font color="#990000"> The table tsl_productnew has been successfully added!  </font>';
    }

    $qIndexed = "CREATE INDEX tsl_productnew ON tsl_productnew (artikelnr)";
    if ($databaseUtility->executeQuery($qIndexed)) {
        echo "Table is Indexed \n";
    } else {
        echo "Table is not Indexed \n";
    }
}

?>
