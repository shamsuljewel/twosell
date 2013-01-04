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
            ?>

            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>
                    TWOSELL: all products not member in a group
                </title>
            </head>
            <body>

                <?php
                include("menue.php");

                echo ' <form action="list_all_products.php" method="post"> 
                    <table><tr><td align=center>
                    Filter using number of months &nbsp;&nbsp;&nbsp;: 
                    <input style="WIDTH: 60px; HEIGHT: 25px" value="';
                if ($_REQUEST['month'] != '')
                    echo $_REQUEST['month'];
                else
                    echo '15';
                echo '" maxlength="4" size="6" name="month"> &nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="submit" type="submit" name="show"> </td></tr></table></form>';

//include('conn.php');
                $sort = $_REQUEST['sort'];
                $month = $_REQUEST['month'];
                if ($sort == "")
                    $sort = " lastdate";

// Toggle order in sorting
                $order = $_REQUEST['order'];
                if ($order == "") {
                    $order = " DESC";
                    $new_order = " ASC";
                } elseif ($order == " DESC") {
                    $new_order = " ASC";
                } else {
                    $new_order = " DESC";
                }

                $sql = "SELECT a.id as id, a.title as title, a.articlenum as articlenum, a.screen_text as screen_text, b.max_price as max_price, b.lastdate as lastdate , b.totalsold as totalsold FROM twosell_product as a, tsln_price_product as b WHERE a.id=b.product_id and a.title!='' and a.id not in (select product_id from tsln_product_group) and period_diff(date_format(now(), '%Y%m'), date_format(b.lastdate, '%Y%m'))<" . $month . " order by " . $sort . $order . ', totalsold DESC';


               // echo $sql . '<br><br>';
//$res = mysql_query($sql);
                $products = $database->getFieldsData($sql, array('id', 'title', 'articlenum', 'screen_text', 'max_price', 'lastdate', 'totalsold'));
//print_r($products);


                if ($sort == "id")
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


                $colomn = array();
                $colomn[1] = '<a href="list_all_products.php?&sort=id' . $sortTep . '&month=' . $month . '">Id</a>';
                $colomn[2] = '<a href="list_all_products.php?&sort=title' . $sortTep . '&month=' . $month . '">Namn</a>';
                $colomn[3] = '<a href="list_all_products.php?&sort=articlenum' . $sortTep . '&month=' . $month . '">Artikelnr</a>';
                $colomn[4] = '<a href="list_all_products.php?&sort=screen_text' . $sortTep . '&month=' . $month . '">Ext. Group Info.</a>';
                $colomn[5] = '<a href="list_all_products.php?&sort=max_price' . $sortTep . '&month=' . $month . '">Högs pris</a>';
                $colomn[6] = '<a href="list_all_products.php?&sort=lastdate' . $sortTep . '&month=' . $month . '">Last Sold Date</a>';
                $colomn[7] = '<a href="list_all_products.php?&sort=totalsold' . $sortTep . '&month=' . $month . '">Total sold</a>';

                if (sizeof($products) > 0) {
                    $title = sizeof($products) . '<i> Products are not in any group </i>';
                    TableView::showTable($products, array('id', 'title', 'articlenum', 'screen_text', 'max_price', 'lastdate', 'totalsold'), $colomn, $title, 'cbrB', 100000000, 1024);
                }else
                    echo '<br><br><p align="center"><b>Listan &auml;r tom </b> </p>';
                ?>


                </table>

                <br><br><br>

                            <img src="twosellbypocada.png">
