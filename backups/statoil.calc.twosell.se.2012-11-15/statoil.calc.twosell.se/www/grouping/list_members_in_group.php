

<img src="twosellbypocada.png"><br><br>
            <?php



            function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
                require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
            }

            $database = new DatabaseUtility();
            $utility = new Utility();
// lista allt i  tsln_produkt_group
// om man inte anger vilken group_id så lista alla (det blir en väldigt lång lista men det är ok)
// Om man skickar med group_id = 1 listar man bara de produkter som har group_id = 1

            $groupId = $_REQUEST['meta_group_id'];
            ?>

            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>
                    TWOSELL: members in group <?php echo $groupId; ?>
                </title>
            </head>
            <body>

                <?php
                include("menue.php");
                $groupNames = $database->getFieldData("Select group_name from tsln_meta_groups where meta_group_id=" . $groupId, 'group_name');
                foreach ($groupNames as $groupName)
                    $groupName = $groupName;

//echo $groupName ;
//include('conn.php');
                $sort = $_REQUEST['sort'];
                if ($sort == "")
                    $sort = " title";

// Toggle order in sorting
                $order = $_REQUEST['order'];
                if ($order == "") {
                    $order = " ASC";
                    $new_order = " DESC";
                } elseif ($order == " DESC") {
                    $new_order = " ASC";
                } else {
                    $new_order = " DESC";
                }

                if ($groupId != "") {
                    $sql = "SELECT a.group_id as group_id, a.product_id as product_id, b.id as id, b.title as title, b.articlenum as articlenum, b.screen_text as screen_text, 'other groups', c.max_price as max_price, c.lastdate as lastdate, c.totalsold as totalsold  FROM tsln_product_group as a, twosell_product as b, tsln_price_product as c WHERE a.product_id = b.id and a.product_id=c.product_id  and a.group_id=" . $groupId . " order by " . $sort . $order;
                } else {
                    //$sql = "SELECT * FROM tsln_product_group WHERE product_id=product_id order by $sort $order";
                }

//cho $sql . '<br><br>';
//$res = mysql_query($sql);
                $products = $database->getFieldsData($sql, array('group_id', 'product_id', 'id', 'title', 'articlenum', 'screen_text', 'other groups', 'max_price', 'lastdate', 'totalsold'));
//print_r($products);

                if ($sort == "group_id")
                    $sortTep = "&order=" . $new_order;
                if ($sort == "product_id")
                    $sortTep = "&order=" . $new_order;
                if ($sort == "title")
                    $sortTep = "&order=" . $new_order;
                if ($sort == "articlenum")
                    $sortTep = "&order=" . $new_order;
                if ($sort == "screen_text")
                    $sortTep = "&order=" . $new_order;
                if ($sort == "max_price")
                    $sortTep = "&order=" . $new_order;
                if ($sort == "lastdate")
                    $sortTep = "&order=" . $new_order;
                if ($sort == "totalsold")
                    $sortTep = "&order=" . $new_order;


	$div_c = '<div style="float:right; font-size:14px">';
	$div = '<div style="float:center; font-size:14px">';
	$div_200 = '<div style="float:center; font-size:14px">';
	$div_270 = '<div style="width: 270px; font-size:14px">';


                $colomn = array();
                $colomn[1] = $div.'<a href="list_members_in_group.php?meta_group_id=' . $groupId . '&sort=group_id' . $sortTep . '">Grupp ID</a></div>';
                $colomn[2] = $div.'<a href="list_members_in_group.php?meta_group_id=' . $groupId . '&sort=product_id' . $sortTep . '">Prod. ID</a></div>';
                $colomn[3] = $div.'<a href="list_members_in_group.php?meta_group_id=' . $groupId . '&sort=title' . $sortTep . '">Produktnamn</a></div>';
                $colomn[4] = $div.'<a href="list_members_in_group.php?meta_group_id=' . $groupId . '&sort=articlenum' . $sortTep . '">Artikelnummer</a></div>';
                $colomn[5] = $div.'<a href="list_members_in_group.php?meta_group_id=' . $groupId . '&sort=screen_text' . $sortTep . '">Ext. Group Info.</a></div>';
                $colomn[6] = $div.'<a href="list_members_in_group.php?meta_group_id=' . $groupId . '&sort=max_price' . $sortTep . '">H&ouml;gsta pris</a></div>';
                $colomn[7] = $div.'<a href="list_members_in_group.php?meta_group_id=' . $groupId . '&sort=lastdate' . $sortTep . '">Senast Såld</a></div>';
                $colomn[8] = $div.'<a href="list_members_in_group.php?meta_group_id=' . $groupId . '&sort=totalsold' . $sortTep . '">Ant. sålda</a></div>';
                $colomn[9] = $div.'Även i grupp';


                for ($i = 0; $i < sizeof($products); $i++) {
                    //echo "SELECT group_id FROM tsln_product_group WHERE product_id=" . $products[$i]['id'] . ' and group_id !=' . $products[$i]['group_id'];
                    $groups = $database->getFieldsData("SELECT group_id FROM tsln_product_group WHERE product_id=" . $products[$i]['id'] . ' and group_id !=' . $products[$i]['group_id'], array('group_id'));
                    $groupId = '';

                    
                    $products[$i]['max_price'] = $div_c.$products[$i]['max_price'].'</div>';
                    $products[$i]['totalsold'] = $div_c.substr($products[$i]['totalsold'], 0, -3).'</div>';
 					$products[$i]['lastdate'] = str_replace("-", "", $products[$i]['lastdate']);
 					$products[$i]['lastdate'] = $div_c.substr($products[$i]['lastdate'],2).'</div>';


                    for ($j = 0; $j < sizeof($groups); $j++) {


                        // echo $groups[$j]['group_id'] .'qwerwq';
                        $groupId = $groupId . '<a href="edit.php?meta_group_id=' . $groups[$j]['group_id'] . '">' . $groups[$j]['group_id'] . '</a>  ';
                    }
                    $products[$i]['other groups'] = $groupId;
                }
//print_r($colomn);exit();
				
                if (sizeof($products) > 0) {
                
                    $title = '<b>'.$groupName. '</b> (products in group: '.sizeof($products).')';
                    TableView::showDiv($products, array('group_id', 'product_id', 'title', 'articlenum', 'screen_text', 'max_price', 'lastdate', 'totalsold', 'other groups'), $colomn, $title, 'cbrB', 100000, 1024);
                }else
                    echo '<p align="center"><b>No result has been achived </b> </p>';
                ?>


                </table>

                <br><br><br>

                            <img src="twosellbypocada.png">
