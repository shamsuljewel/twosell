<?php

ini_set('memory_limit', '1024M');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//ini_set('default_charset','utf-8');

function __autoload($className) {
//require_once "./models/{$className}.php";
    require_once "./baseClasses/{$className}.php";
}

function main() {
    $productId = $_GET['productId'];

    $database = new DatabaseUtility();
    $utility = new Utility();

    echo '<table border ="1" cellspacing="3" cellpadding="3" width="1000"><tr><td>';
    $query = 'SELECT a.match_id, b.title, a.store_id, a.score FROM recommender_two_productstorematch_backup as a, twosell_product as b where a.score >0 and a.main_id = ' . $productId . ' and a.match_id = b.id order by store_id, score desc';
    $fields = array('match_id', 'title', 'store_id', 'score');
    $data = $database->getFieldsData($query, $fields);

    for ($j = 0; $j < sizeof($data); $j++) {
        $dataP_P = $database->getFieldData("SELECT match_id FROM matrix_two_p_p where main_id=" . $productId . " and match_id=" . $data[$j]['match_id'], 'match_id');
        if (sizeof($dataP_P)) {
            $data[$j]['score'] = $data[$j]['score'] . "(P_P)";
        }
    }


    $table = new TableView();
    $headers = $colTitles = $fields;
    $title = 'Result for all Stores using only Twosell'; // new Text ///////////////////////
    $table->showTable($data, $headers, $colTitles, $title, 'op', 900);

    echo '</td><td>';

    //new
    
    $query = 'SELECT store_id,	positemid, title FROM recommender_two_variation where main_id = ' . $productId . ' order by store_id';
   // echo $query;
    $fields = array('store_id', 'positemid', 'title');
    $data = $database->getFieldsData($query, $fields);
    //print_r($data);
    $table = new TableView();
    $headers = $colTitles = $fields;
    $title = 'Result for all Stores considering variation suggession'; // new Text ///////////////////////
    $table->showTable($data, $headers, $colTitles, $title, 'op', 900);
    echo '</td><td>';

    //new
    $query = "SELECT positemid, CONCAT(title,'<br> desc1:', description1, '<br>desc2:', description2, '<br>desc3:', description3) as title,  store_id, score FROM recommender_two_online where score >0 and main_id = " . $productId . " order by store_id, score desc";
    $fields = array('positemid', 'title', 'store_id', 'score');
    $data = $database->getFieldsData($query, $fields);
    $table = new TableView();
    $headers = $colTitles = $fields;
    $title = 'Result for online final suggession'; // new Text ///////////////////////
    $table->showTable($data, $headers, $colTitles, $title, 'op', 900);

    echo '</td></tr></table>';

    // new code ///////////////////////
    // new code ///////////////////////// new code ///////////////////////
    echo '<table border ="1" cellspacing="3" cellpadding="3" width="1000"><tr>';

    $Stores = $database->getFieldsData('select id, title from twosell_store where active = 1', array('id', 'title'));
    $m = 1;
    if (sizeof($Stores) > 0) {
        for ($i = 0; $i < sizeof($Stores); $i++) {
            $query = "SELECT positemid, CONCAT(title,'<br>desc1:', description1, '<br>desc2:', description2, '<br>desc3:', description3) as title, store_id, score FROM recommender_two_online where score >0 and main_id = " . $productId . " and store_id=" . $Stores[$i]['id'] . " order by score desc";
            // echo $query;
            $fields = array('positemid', 'title', 'store_id', 'score');
            $data = $database->getFieldsData($query, $fields);

            if (sizeof($data) > 0) {
                echo '<td align=left>';
                $table = new TableView();
                $headers = $colTitles = $fields;
                $title = 'Result for the store "' . $Stores[$i]['title'] . ' (' . $Stores[$i]['id'] . ')"';
                $table->showTable($data, $headers, $colTitles, $title, 'op', 300);
                echo '</td>';
                if ($m > 2) {
                    echo '</tr><tr>';
                    $m = 1;
                }
                $m = $m + 1;
            }
        }
    }else
        echo '<p align="center"><b>No result has been achived </b> </p>';
    echo '</tr></table>';
    // new code ///////////////////////// new code ///////////////////////
}

main();
?>
