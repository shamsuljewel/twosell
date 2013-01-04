<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        TWOSELL: Editera grupp -sparad
    </title>
</head>
<body>


    <?php
    include("menue.php");

    function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
        require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
    }

    $database = new DatabaseUtility();
    $utility = new Utility();
//ramins kod
//if($meta_group_id != ""){
   // print_r($_POST);
    foreach ($_POST as $field => $value) {
        //   echo $field;        
        $fields .= "$field = '$value'";
        if ($field != 'latest_calculation_dattime') {
            $fields.=", ";
        }
    }
    if ($_POST["if_group_selected"] == '')
        $fields = $fields . 'if_group_selected=0, ';
    if ($_POST["if_sold_ingroup"] == '')
        $fields = $fields . 'if_sold_ingroup=0, ';
     if ($_POST["group_relation_manual_ok"] == '')
        $fields = $fields . 'group_relation_manual_ok=0, ';
    
    

    $len = strlen($fields);
    $fields = substr($fields, 0, $len - 2) . " ";
    $meta_group_id = $_POST['meta_group_id'];



    if ($meta_group_id > 0) {
        print "";
        $sql = "UPDATE tsln_meta_groups SET $fields WHERE meta_group_id ='" . $meta_group_id . "'";

       // echo $sql;

        print "<br><br><br>";
        if ($database->executeQuery(utf8_decode($sql))) {
            print "Group information is saved";
        } else {
            print "Group information is not saved";
        }
////ramins kod
//}
////ramins kod
    } elseif ($meta_group_id == "") {

//---////ramins kod


        foreach ($_POST as $fieldNew => $valueNew) {
            $fieldsNew .= $fieldNew . ", ";
            $valuesNew .= "'" . $valueNew . "', ";
        }
        $len2New = strlen($fieldsNew);
        $fieldsNew = substr($fieldsNew, 0, $len2New - 2) . "";

        $lenNew = strlen($valuesNew);
        $valuesNew = substr($valuesNew, 0, $lenNew - 2) . "";

        // print "";

        $sql = "INSERT INTO tsln_meta_groups ($fieldsNew) VALUES ($valuesNew)";

        //print $sql;

        if ($database->executeQuery(utf8_decode($sql))) {
            print "Group information is saved";
        } else {
            print "Group information is not saved";
        }
        ?>
        <br><br>Ny grupp har skapats. OBS, använd ej backa-knappen i webbläsaren.<br> 
                    <form method="post">
                        <INPUT TYPE="BUTTON" VALUE="Fortsätt" ONCLICK="window.location.href='index.php'">
                    </form>

                    <?php
                }

                if ($meta_group_id > 0) {

                    $productsprevious = $database->getFieldData('Select product_id from tsln_product_group WHERE group_id =' . $meta_group_id, 'product_id');

                    $database->executeQuery('delete from tsln_product_group WHERE group_id =' . $meta_group_id);
//echo 'delete from tsln_product_group WHERE group_id =' . $meta_group_id;
                    $database->executeQuery('UPDATE tsln_meta_groups SET status=0, number_of_members = 0, totalsold =0, change_time = now() WHERE meta_group_id =' . $meta_group_id);
                    $groups = $database->getFieldsData("select meta_group_id, group_name, keyword_include_1,keyword_exclude_1,keyword_include_2,keyword_exclude_2,keyword_include_3, keyword_exclude_3, exclude_prod_items, include_prod_items, price_min, price_max, group_relation_top_manual  from tsln_meta_groups where status = 0 and meta_group_id =" . $meta_group_id, array('meta_group_id', 'group_name', 'keyword_include_1', 'keyword_exclude_1', 'keyword_include_2', 'keyword_exclude_2', 'keyword_include_3', 'keyword_exclude_3', 'exclude_prod_items', 'include_prod_items', 'price_min', 'price_max', 'group_relation_top_manual'));
                    // print_r($groups);
                    for ($j = 0; $j < sizeof($groups); $j++) {

                        if ($groups[$j]['keyword_include_1'] != '') {
                            $query = $utility->findMemberProducts($groups, $j, 'keyword_exclude_1', 'keyword_include_1');
                            //echo $query; exit();
                            $products = $database->getFieldsData($query, array('id', 'title'));
                            for ($k = 0; $k < sizeof($products); $k++) {
                                $query = "insert into tsln_product_group(product_id, group_id) values(" . $products[$k]['id'] . "," . $meta_group_id . ")";
                                //echo $query ;
                                $database->executeQuery($query);
                            }
                        }


                        if ($groups[$j]['keyword_include_2'] != '') {
                            $query = $utility->findMemberProducts($groups, $j, 'keyword_exclude_2', 'keyword_include_2');
                            //echo $query; exit();
                            $products = $database->getFieldsData($query, array('id', 'title'));
                            for ($k = 0; $k < sizeof($products); $k++) {
                                $query = "insert into tsln_product_group(product_id, group_id) values(" . $products[$k]['id'] . "," . $meta_group_id . ")";
                                //echo $query ;
                                $database->executeQuery($query);
                            }
                        }

                        if ($groups[$j]['keyword_include_3'] != '') {
                            $query = $utility->findMemberProducts($groups, $j, 'keyword_exclude_3', 'keyword_include_3');
                            //echo $query; exit();
                            $products = $database->getFieldsData($query, array('id', 'title'));
                            for ($k = 0; $k < sizeof($products); $k++) {
                                $query = "insert into tsln_product_group(product_id, group_id) values(" . $products[$k]['id'] . "," . $meta_group_id . ")";
                                //echo $query ;
                                $database->executeQuery($query);
                            }
                        }


                        if ($groups[$j]['exclude_prod_items'] != '') {
                            $productId = explode(';', strtolower($groups[$j]['exclude_prod_items']));
                            for ($m = 0; $m < sizeof($productId); $m++) {
                                $database->executeQuery('delete from tsln_product_group WHERE product_id =' . $productId[$m] . ' and group_id =' . $meta_group_id);
                            }
                        }

                        if ($groups[$j]['include_prod_items'] != '') {
                            $productId = explode(';', strtolower($groups[$j]['include_prod_items']));
                            for ($m = 0; $m < sizeof($productId); $m++) {
                                $database->executeQuery('insert into tsln_product_group(product_id, group_id) values(' . $productId[$m] . ',' . $meta_group_id . ')');
                            }
                        }

                        $productsfinal = $database->getFieldData('Select product_id from tsln_product_group WHERE group_id =' . $meta_group_id, 'product_id');

                        // print_r($groups[$j]['group_relation_top_manual']);
                        if ($groups[$j]['group_relation_top_manual'] != '') {
                            $database->executeQuery('delete from tsln_group_suggestion where group_id=' . $meta_group_id);

                            $suggssionids = explode(';', strtolower($groups[$j]['group_relation_top_manual']));
                            //print_r($suggssionids);
                            for ($m = 0; $m < sizeof($suggssionids); $m++) {
                                //echo 'insert into tsln_group_suggestion(group_id, suggestion_group_id) values(' . $meta_group_id . ',' . $suggssionids[$m] . ')';
                                $database->executeQuery('insert into tsln_group_suggestion(group_id, suggestion_group_id) values(' . $meta_group_id . ',' . $suggssionids[$m] . ')');
                            }
                        }


                        $totalsoldGroup = $database->getFieldsData('SELECT sum(`totalsold`) as totalsold FROM `tsln_price_product` where `product_id` in (select `product_id` from tsln_product_group where group_id=' . $meta_group_id . ')', array('totalsold'));
                        //print_r($totalsoldGroup);

                        if (sizeof($totalsoldGroup) >= 1) {
                            for ($q = 0; $q < sizeof($totalsoldGroup); $q++) {
                                $totalsold = $totalsoldGroup[$q]['totalsold'];
                            }
                            $totalsold = ", totalsold = " . $totalsold;
                        } else {
                            $totalsold = ", totalsold = 0";
                        }


                        $queryUp = "update tsln_meta_groups set status = 1, latest_calculation_dattime= now(), number_of_members = " . sizeof($productsfinal) . $totalsold . " WHERE meta_group_id =" . $meta_group_id;
                        //echo $queryUp;
                        $database->executeQuery($queryUp);
                    }

                    echo "<br>Execution done ..........! <br> In total " . sizeof($productsfinal) . " products are listed in this group !";

                    $addproducts = array_diff($productsfinal, $productsprevious);
                    $delproducts = array_diff($productsprevious, $productsfinal);
                    // print_r($addproducts);
                    if (sizeof($addproducts) > 0) {
                        echo '<br> Previously, there was ' . sizeof($productsprevious) . ' products and <font color=red><B> the newly added product ID(s) : ' . implode(",", $addproducts) . '</B></Font>';
                        $newproducts = $database->getFieldsData('SELECT a.id as id, a.title as title, a.articlenum as articlenum, a.screen_text as screen_text, b.max_price as max_price FROM twosell_product as a, tsln_price_product as b WHERE a.id=b.product_id and a.id in (' . implode(",", $addproducts) . ')', array('id', 'title', 'articlenum', 'screen_text', 'max_price'));

                        if (sizeof($newproducts) > 0) {
                            $title = sizeof($newproducts) . '<i> Products are newly added in the group </i>';
                            TableView::showTable($newproducts, array('id', 'title', 'articlenum', 'screen_text', 'max_price'), array('ID', 'Namn', 'Artikelnr', 'Skärmtext', 'Högsta pris'), $title, 'cbrB', 300, 1024);
                        }
                    } elseif (sizeof($delproducts) > 0) {
                        echo '<br> Previously, there was ' . sizeof($productsprevious) . ' products and <font color=red><B> the deleted product ID(s) : ' . implode(",", $delproducts) . '</B></Font>';
                        $delproducts = $database->getFieldsData('SELECT a.id as id, a.title as title, a.articlenum as articlenum, a.screen_text as screen_text, b.max_price as max_price FROM twosell_product as a, tsln_price_product as b WHERE a.id=b.product_id and a.id in (' . implode(",", $delproducts) . ')', array('id', 'title', 'articlenum', 'screen_text', 'max_price'));
                        if (sizeof($delproducts) > 0) {
                            $title = sizeof($delproducts) . '<i> Products are deleted from the group </i>';
                            TableView::showTable($delproducts, array('id', 'title', 'articlenum', 'screen_text', 'max_price'), array('ID', 'Namn', 'Artikelnr', 'Sk&auml;rmtext', 'H&ouml;gsta pris'), $title, 'cbrB', 300, 1024);
                        }
                    }
                    ?>

                    <form method="post"><br>
                            <INPUT TYPE="BUTTON" VALUE="Lista gruppen (mest såld först)" ONCLICK="window.location.href='list_members_in_group.php?meta_group_id=<?php print $meta_group_id . "&sort=totalsold&order= DESC"; ?>'">
                                <INPUT TYPE="BUTTON" VALUE="Visa gruppen (alfabetisk order)" ONCLICK="window.location.href='list_members_in_group.php?meta_group_id=<?php print $meta_group_id; ?>'">
                                    <INPUT TYPE="BUTTON" VALUE="Tillbaks till editeringssidan" ONCLICK="window.location.href='edit.php?meta_group_id=<?php print $meta_group_id; ?>'">
                                        </form>

<?php } ?>
                                
