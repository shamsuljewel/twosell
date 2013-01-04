<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        TWOSELL: Lista med alla klasser & grupper
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
        $sort = "group_name";

    $gid = $_REQUEST['gid'];
    if ($gid == '') {$gid = "id"; $gname='&gid=id';}
    elseif ($gid == 'id') {$gname='&gid=id';}
    else {$gname='&gid=name';}


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
    if ($sort == "meta_group_id")
        $sortTep = "&order=" . $new_order;
    if ($sort == "group_name")
        $sortTep = "&order=" . $new_order;
    if ($sort == "keyword_include_1")
        $sortTep = "&order=" . $new_order;
    if ($sort == "number_of_members")
        $sortTep = "&order=" . $new_order;
    if ($sort == "group_relation_top_manual")
        $sortTep = "&order=" . $new_order;
    if ($sort == "group_relation_manual_ok")
        $sortTep = "&order=" . $new_order;
    if ($sort == "never_suggest")
        $sortTep = "&order=" . $new_order;
    if ($sort == "price_min")
        $sortTep = "&order=" . $new_order;
    if ($sort == "price_max")
        $sortTep = "&order=" . $new_order;
    if ($sort == "change_time")
        $sortTep = "&order=" . $new_order;
    if ($sort == "create_time")
        $sortTep = "&order=" . $new_order;
    if ($sort == "latest_calculation_dattime")
        $sortTep = "&order=" . $new_order;
    if ($sort == "totalsold")
        $sortTep = "&order=" . $new_order;
    if ($sort == "description2")
        $sortTep = "&order=" . $new_order;

    $sql = "SELECT meta_group_id, group_name, description2, keyword_include_1,number_of_members, group_relation_top_manual, if (group_relation_manual_ok=1, 'Active', 'Not Active') as group_relation_manual_ok, never_suggest,price_min,price_max,change_time,create_time,latest_calculation_dattime,totalsold FROM tsln_meta_groups WHERE meta_group_id=meta_group_id order by $sort $order";
//echo $sql.'<br><br>';
    // $res = mysql_query($sql);
    $groups = $database->getFieldsData($sql, array('edit', 'browse', 'meta_group_id', 'group_name','description2', 'keyword_include_1', 'number_of_members', 'group_relation_top_manual', 'group_relation_manual_ok', 'never_suggest', 'price_min', 'price_max', 'change_time', 'create_time', 'latest_calculation_dattime', 'totalsold'));

    $col1 = "#F2F2F2";
    $col2 = "#FFFFFF";
    $div = '<div style="float:right; font-size:12px">';
    $div_c = '<div style="float:center; font-size:12px">';
    $div_200 = '<div style="float:center; font-size:14px">';
    $div_270 = '<div style="width: 270px; font-size:14px">';
    $colomn = array();
    $colomn[1] = ' ';
    $colomn[2] = '';
    $colomn[3] = $div_c .'<a href="list_groups.php?sort=meta_group_id' . $sortTep . $gname.'">Id</a></div>';
    $colomn[4] = $div_c . '<a href="list_groups.php?sort=group_name' . $sortTep . $gname.'">Klass</a></div>';
    $colomn[5] = $div_270 . '<a href="list_groups.php?sort=group_name' . $sortTep . $gname.'">Gruppnamn</a></div>';
    $colomn[6] = $div_c . '<a href="list_groups.php?sort=keyword_include_1' . $sortTep . $gname.'">Nyckelsträngar</a></div>';
    $colomn[7] = $div_200 . '<a href="list_groups.php?sort=group_relation_top_manual' . $sortTep . $gname.'">Grupprelation<br>manuell</a></div>';
    $colomn[8] = $div_200 . '<a href="list_groups.php?sort=group_relation_manual_ok' . $sortTep . $gname.'">Active <br> Grupprelation<br>manuell</a></div>';
    $colomn[9] = $div . '<a href="list_groups.php?sort=number_of_members' . $sortTep . $gname.'"><div style="float:center; font-size:14px">Ant.</div></a>';
    $colomn[10] = $div . '<a href="list_groups.php?sort=totalsold' . $sortTep . $gname.'">Tot. såld</a></div>';
    $colomn[11] = $div . '<a href="list_groups.php?sort=never_suggest' . $sortTep . $gname.'">block</a></div>';
    $colomn[12] = $div . '<a href="list_groups.php?sort=price_min' . $sortTep . $gname.'">Min kr</a></div>';
    $colomn[13] = $div . '<a href="list_groups.php?sort=price_max' . $sortTep . $gname.'">Max kr</a></div>';
    $colomn[14] = $div . '<a href="list_groups.php?sort=change_time' . $sortTep . $gname.'">Ändrad</a></div>';
    $colomn[15] = $div . '<a href="list_groups.php?sort=create_time' . $sortTep . $gname.'">Skapad</a></div>';
    $colomn[16] = $div . '<a href="list_groups.php?sort=latest_calculation_dattime' . $sortTep . $gname.'">Senast<br>ber&auml;knad</a></div>';
    $colomn[17] = $div . '<a href="list_groups.php?sort=description2' . $sortTep . $gname.'">Description2</a></div>';

    $color = 'red';
    $last_color = 'green';

    if (sizeof($groups) > 0) {
        for ($j = 0; $j < sizeof($groups); $j++) {
            $rest = substr($groups[$j]['keyword_include_1'], 0, 58);
            if (strlen($groups[$j]['keyword_include_1']) > 57) {
                $rest = $rest . "..";
            }
            $group = '';
            if ($groups[$j]['group_relation_top_manual'] != '') {
                $wordsExc = explode(';', strtolower($groups[$j]['group_relation_top_manual']));
                for ($m = 0; $m < sizeof($wordsExc); $m++) {
                    $sqlName = "SELECT group_name FROM tsln_meta_groups WHERE meta_group_id=" . $wordsExc[$m];
                    //echo $sqlName;
                    $gNames = $database->getFieldData($sqlName, 'group_name');
              //  print_r($gNames);
                    foreach ($gNames as $name) {
                        $name= $name;
                    }
                    if ($gid == 'name')  $group = $group . '<a href="edit.php?meta_group_id=' . $wordsExc[$m] . '" title="'.$wordsExc[$m].'"> '.$name.'</a><br>';
                    else $group = $group . '<a href="edit.php?meta_group_id=' . $wordsExc[$m] . '" title="'.$name.'">'. $wordsExc[$m].'</a> ';
                }
            }
            // echo $groups[$j]['group_id'] .'qwerwq';
            $groups[$j]['edit'] = '<a href="edit.php?meta_group_id=' . $groups[$j]["meta_group_id"] . '"><img height="16" width="16" class="icon" alt="Editera denna grupp" title="Editera denna grupp" src="b_edit.png"/></a>';
            $groups[$j]['browse'] = '<a href="list_members_in_group.php?meta_group_id=' . $groups[$j]["meta_group_id"] . 
            '&sort=totalsold&order= DESC"><img height="16" width="16" class="icon" alt="Lista denna grupp" title="Lista denna grupp" src="b_list.png"/></a>';


            if ($last_class !== $groups[$j]['class']) {
                $last_class = $groups[$j]['class'];
                $xcolor = $color;
                $color = $last_color;
                $last_color = $xcolor;
            }


            $c_pos = stripos($groups[$j]['group_name'], ':');
            $class = substr($groups[$j]['group_name'], 0, $c_pos);
            $class_p = substr($groups[$j - 1]['group_name'], 0, $c_pos);
            $grupp = substr($groups[$j]['group_name'], $c_pos);
            if ($class !== $class_p & $sort == 'group_name') {
                $xcolor = $color;
                $color = $last_color;
                $last_color = $xcolor;
            }

            $groups[$j]['class'] = '<font color="' . $color . '">' . $class . '</font>';


            $groups[$j]['keyword_include_1'] = $rest;
            $groups[$j]['number_of_members'] = '<div style="float:right; font-size:14px">' . $groups[$j]['number_of_members'] . '</div>';
            $groups[$j]['totalsold'] = '<div style="float:right; font-size:14px">' . $groups[$j]['totalsold'] . '</div>';
            $groups[$j]['never_suggest'] = '<div ALIGN="center" style="float:centre; font-size:14px">' . $groups[$j]['never_suggest'] . '</div>';
            $groups[$j]['price_min'] = '<div style="float:right; font-size:12px">' . $groups[$j]['price_min'] . '</div>';
            $groups[$j]['price_max'] = '<div style="float:right; font-size:12px">' . $groups[$j]['price_max'] . '</div>';
            $groups[$j]['group_relation_top_manual'] = $group;
            $groups[$j]['change_time'] = '<font size="1">' . str_replace("-", "", substr($groups[$j]['change_time'], 2, 8) . 'T' . substr($groups[$j]['change_time'], 11, -3));
            $groups[$j]['create_time'] = '<font size="1">' . str_replace("-", "", substr($groups[$j]['create_time'], 2, -9));
            $groups[$j]['latest_calculation_dattime'] = '<font size="1">' . str_replace("-", "", substr($groups[$j]['latest_calculation_dattime'], 2, 8) . 'T' . substr($groups[$j]['latest_calculation_dattime'], 11, -3));
        }
        $title = 'Totalt antal grupper: '.sizeof($groups);
        
		if( $gid == 'id')  $title  .= $div . '<a href="list_groups.php?sort='.$sort.'&order='.$order.'&gid=name">Grupprelationer: visa gruppnamn</a></div>';
		elseif( $gid == 'name')  $title  .= $div . '<a href="list_groups.php?sort='.$sort.'&order='.$order.'&gid=id">Grupprelationer: visa grupp ID</a></div>';		print  $switch_gname_gid;
        
        TableView::showDiv($groups, array('edit', 'browse', 'meta_group_id', 'class', 'group_name', 'keyword_include_1',
            'group_relation_top_manual', 'group_relation_manual_ok', 'number_of_members', 'totalsold', 'never_suggest', 'price_min', 'price_max', 'change_time', 'create_time', 'latest_calculation_dattime', 'description2'), $colomn, $title, 'cbrB', 15000, 1324);
    }else
        echo '<br><br><p align="center"><b>Listan &auml;r tom </b> </p>';
    ?>



    <br><br>
            <img src="twosellbypocada.png"><br>
                    </body>
