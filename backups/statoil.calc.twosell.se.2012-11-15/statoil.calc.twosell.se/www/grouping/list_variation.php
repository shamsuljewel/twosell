
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        TWOSELL: List of Variation
    </title>
</head>
<body>
    <?php
    include("menue.php");

    function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
        require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
    }
    
    function variation_factor_names($tableName, $pos, $variation_id) {
        $database = new DatabaseUtility();
        //echo $pos . 'sadsadsad';
       //echo " SELECT if (id='0', 'NO', Name) as Name FROM weather_dev." .$tableName. " WHERE id=" . SUBSTR($variation_id, $pos, 1);
        $arstidNames = $database->getFieldData("SELECT if (id='0', 'NO', Name) as Name FROM weather_dev." .$tableName. " WHERE id=" . SUBSTR($variation_id, $pos, 1), 'Name');
       //print_r($arstidNames);
        foreach ($arstidNames as $arstidNames) {
            $variation_fatro_name = $arstidNames . ' | ';
        }
        return $variation_fatro_name;
    }

    function variation_id_means($variation_id) {
        return variation_factor_names('tsln_seasons', 0, $variation_id) . variation_factor_names('tsln_celebration_day', 1, $variation_id) . variation_factor_names('tsln_week', 2, $variation_id). variation_factor_names('tsln_days', 3,  $variation_id) . variation_factor_names('tsln_weather', 4,  $variation_id) .  variation_factor_names('tsln_temparature', 5, $variation_id) ;
    }

    $database = new DatabaseUtility();
    $utility = new Utility();
    $sort = $_REQUEST['sort'];
    if ($sort == '')
        $sort = "id";


// Toggle order in sorting
    $order = $_REQUEST['order'];
    if ($order == "") {
        $order = "ASC";
        $new_order = "DESC";
    } elseif ($order == "DESC") {
        $new_order = "ASC";
    } else {
        $new_order = "DESC";
    }
    if ($sort == "id")
        $sortTep = "&order=" . $new_order;
    if ($sort == "variation_id")
      $sortTep = "&order=" . $new_order;
    if ($sort == "variation_text")
        $sortTep = "&order=" . $new_order;
    if ($sort == "group_ids")
        $sortTep = "&order=" . $new_order;
    if ($sort == "store_ids")
        $sortTep = "&order=" . $new_order;
    if ($sort == "create_date")
        $sortTep = "&order=" . $new_order;
    if ($sort == "modified_date")
        $sortTep = "&order=" . $new_order;
    if ($sort == "comments")
        $sortTep = "&order=" . $new_order;


    $sql = "SELECT `id`, `variation_id`, `variation_text`, group_ids, store_ids, `create_date`, `modified_date`, `comments` FROM `tsln_variation_text` WHERE id=id order by $sort $order";
//echo $sql.'<br><br>';
    // $res = mysql_query($sql);
    $groups = $database->getFieldsData($sql, array('delete', 'edit', 'id', 'variation_id', 'Variation_means', 'variation_text', 'group_ids', 'store_ids', 'create_date', 'modified_date', 'comments'));


    $colomn = array();
    $colomn[0] = ' ';
    $colomn[1] = ' ';
    $colomn[2] = $div_c . '<a href="list_variation.php?sort=id' . $sortTep . $gname . '">Id</a></div>';
    $colomn[3] = '<a href="list_variation.php?sort=id' . $sortTep . $gname . '">variation_id</a></div>';
    $colomn[4] = utf8_encode('Årstid  | Celebration | Veckan | Dygn | Väder | Temparature');
    $colomn[5] = $div_270 . '<a href="list_variation.php?sort=variation_text' . $sortTep . $gname . '">variation_text</a></div>';
    $colomn[6] = $div_c . '<a href="list_variation.php?sort=group_ids' . $sortTep . $gname . '">group_ids</a></div>';
    $colomn[7] = $div_200 . '<a href="list_variation.php?sort=store_ids' . $sortTep . $gname . '">store_ids</a></div>';
    $colomn[8] = $div . '<a href="list_variation.php?sort=create_date' . $sortTep . $gname . '"><div style="float:center; font-size:14px">create_date</div></a>';
    $colomn[9] = $div . '<a href="list_variation.php?sort=modified_date' . $sortTep . $gname . '">modified_date</a></div>';
    $colomn[10] = $div . '<a href="list_variation.php?sort=comments' . $sortTep . $gname . '">comments</a></div>';

    $on = "return confirm('Are you sure you want to delete?')";
    //echo $on;
    if (sizeof($groups) > 0) {
        for ($j = 0; $j < sizeof($groups); $j++) {
            $groups[$j]['delete'] = '<a href="delete_variation.php?id=' . $groups[$j]["id"] . '" onclick="' . $on . '"><img height="16" width="16" class="icon" alt="delete denna variation" title="delete denna variation" src="b_delete.png"/></a>';
            $groups[$j]['edit'] = '<a href="edit_variation.php?id=' . $groups[$j]["id"] . '"><img height="16" width="16" class="icon" alt="Editera denna grupp" title="Editera denna variation" src="b_edit.png"/></a>';
            $groups[$j]['Variation_means'] = SUBSTR(variation_id_means($groups[$j]['variation_id']),0 , strlen (variation_id_means($groups[$j]['variation_id']))-2);
        }
        $title = 'Totalt antal grupper: ' . sizeof($groups);

        TableView::showDiv($groups, array('delete', 'edit', 'id', 'variation_id', 'Variation_means', 'variation_text', 'group_ids', 'store_ids', 'create_date', 'modified_date', 'comments'), $colomn, $title, 'cbrB', 15000, 1324);
    }else
        echo '<br><br><p align="center"><b>Listan &auml;r tom </b> </p>';
    ?>



    <br><br>
    <img src="twosellbypocada.png"><br>
</body>
