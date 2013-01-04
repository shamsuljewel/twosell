<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        TWOSELL: Lista med alla stores
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
    $sort = $_REQUEST['sort'];
    if ($sort == '')
        $sort = "title";

    $gid = $_REQUEST['gid'];
    
    if ($gid == '') {
        $gid = "id";
        $gname = '&gid=id';
    } elseif ($gid == 'id') {
        $gname = '&gid=id';
    } else {
        $gname = '&gid=name';
    }


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
    if ($sort == "internal_id")
        $sortTep = "&order=" . $new_order;
    if ($sort == "title")
        $sortTep = "&order=" . $new_order;
    if ($sort == "chain_id")
        $sortTep = "&order=" . $new_order;
    if ($sort == "recommendation_config_id")
        $sortTep = "&order=" . $new_order;
    if ($sort == "active")
        $sortTep = "&order=" . $new_order;
    if ($sort == "active_online")
        $sortTep = "&order=" . $new_order;
    if ($sort == "address")
        $sortTep = "&order=" . $new_order;
    if ($sort == "postal_code")
        $sortTep = "&order=" . $new_order;
    if ($sort == "city_id")
        $sortTep = "&order=" . $new_order;
    if ($sort == "phone")
        $sortTep = "&order=" . $new_order;
    if ($sort == "notes")
        $sortTep = "&order=" . $new_order;
    if ($sort == "block_group_id_target")
        $sortTep = "&order=" . $new_order;
    if ($sort == "block_group_id_suggestion")
        $sortTep = "&order=" . $new_order;
    if ($sort == "block_products_other_then_group")
        $sortTep = "&order=" . $new_order;

    $sql = "SELECT id, 	internal_id, title, chain_id, recommendation_config_id, IF(active=1,'<font color=green>Active</font>','<font color=red>Not active</font>') as active, IF(active_online=1,'<font color=green>Active</font>','<font color=red>Not active</font>') as active_online, address, postal_code, city_id, phone, notes, block_group_id_target, block_group_id_suggestion, IF(block_products_other_then_group=1,'Blocked all the products not belongs to any group','') as block_products_other_then_group FROM twosell_store WHERE id=id order by $sort $order";
//echo $sql.'<br><br>';
    // $res = mysql_query($sql);
    $groups = $database->getFieldsData($sql, array('edit', 'id', 'internal_id', 'title', 'chain_id', 'recommendation_config_id', 'active', 'active_online','address', 'postal_code', 'city_id', 'phone', 'notes', 'block_group_id_target', 'block_group_id_suggestion', 'block_products_other_then_group'));

    $col1 = "#F2F2F2";
    $col2 = "#FFFFFF";
    $div = '<div style="float:right; font-size:12px">';
    $div_c = '<div style="float:center; font-size:12px">';
    $div_200 = '<div style="float:center; font-size:14px">';
    $div_270 = '<div style="width: 270px; font-size:14px">';
    $colomn = array();
    $colomn[1] = ' ';  
    $colomn[2] =  '<a href="list_of_stores.php?sort=id' . $sortTep . $gname . '">Id</a></div>';
    $colomn[3] =  '<a href="list_of_stores.php?sort=internal_id' . $sortTep . $gname . '">internal_id</a></div>';
    $colomn[4] =  '<a href="list_of_stores.php?sort=title' . $sortTep . $gname . '">title</a></div>';
    $colomn[5] =  '<a href="list_of_stores.php?sort=chain_id' . $sortTep . $gname . '">chain_id</a></div>';
    $colomn[6] =  '<a href="list_of_stores.php?sort=recommendation_config_id' . $sortTep . $gname . '">recommendation_config_id</div>';
    $colomn[7] =  '<a href="list_of_stores.php?sort=active' . $sortTep . $gname . '">active off line</a>';
    $colomn[8] =  '<a href="list_of_stores.php?sort=active_online' . $sortTep . $gname . '">active on line</a>';
    $colomn[9] =  '<a href="list_of_stores.php?sort=address' . $sortTep . $gname . '">address</a></div>';
    $colomn[10] =  '<a href="list_of_stores.php?sort=postal_code' . $sortTep . $gname . '">postal_code</a></div>';
    $colomn[11] = '<a href="list_of_stores.php?sort=city_id' . $sortTep . $gname . '">city</a></div>';
    $colomn[12] =  '<a href="list_of_stores.php?sort=phone' . $sortTep . $gname . '">phone</a></div>';
    $colomn[13] =  '<a href="list_of_stores.php?sort=notes' . $sortTep . $gname . '">notes</a></div>';
    $colomn[14] =  '<a href="list_of_stores.php?sort=block_group_id_target' . $sortTep . $gname . '">block_group_id_target</a></div>';
    $colomn[15] =  '<a href="list_of_stores.php?sort=block_group_id_suggestion' . $sortTep . $gname . '">block_group_id_suggestion</a></div>';
    $colomn[16] =  '<a href="list_of_stores.php?sort=block_products_other_then_group' . $sortTep . $gname . '">block_products_other_then_group</a></div>';

    $color = 'red';
    $last_color = 'green';

    if (sizeof($groups) > 0) {
        for ($j = 0; $j < sizeof($groups); $j++) {   
            if ($groups[$j]['city_id']==0)$col='red'; else $col='green';
            $groups[$j]['edit'] = '<a href="edit_stores.php?id=' . $groups[$j]["id"] . '"><img height="16" width="16" class="icon" alt="Editera denna grupp" title="Editera denna grupp" src="b_edit.png"/></a>';
             $city_names = $database->getFieldData('Select name from  weather_dev.city_tbl where id=' .  $groups[$j]['city_id'], 'name');
             foreach($city_names as $city_name){
                 $groups[$j]['city_id']= '<font color='. $col . '>'. $city_name . ' (' . $groups[$j]['city_id']. ')</font>';
             }
            
      }
        $title = 'Totalt antal butiker: ' . sizeof($groups);        

        TableView::showDiv($groups, array('edit', 'id', 'internal_id', 'title', 'chain_id', 'recommendation_config_id', 'active', 'active_online', 'address', 'postal_code', 'city_id', 'phone', 'notes','block_group_id_target', 'block_group_id_suggestion', 'block_products_other_then_group'), $colomn, $title, 'cbrB', 15000, 1324);
    }else
        echo '<br><br><p align="center"><b>Listan &auml;r tom </b> </p>';
    ?>



    <br><br>
            <img src="twosellbypocada.png"><br>
                    </body>
