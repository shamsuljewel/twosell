<?php
include("menue.php");

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

function combo($city_id) {

    $database = new DatabaseUtility();
    $COMORBCity = $database->getFieldsData('SELECT id, name FROM weather_dev.city_tbl order by name', array('id', 'name'));
//print_r($COMORBCity);
    if (!empty($COMORBCity)) {
        echo "<select name='city'>";
        for ($i = 0; $i < sizeof($COMORBCity); $i++) {
            echo "<option value='" . utf8_encode($COMORBCity[$i]['id']) . "' ";
            if (utf8_encode($COMORBCity[$i]['id']) == $city_id)
                echo "selected='selected'";
            echo ">" . strtoupper(utf8_encode($COMORBCity[$i]['name'])) . "</option>";
        }
        echo "</select>";
    }
}


$database = new DatabaseUtility();

$id = $_REQUEST['id'];
if ($_POST[save] == 'Save store') {
    if ($_POST["restProduct"] == '')
        $select = '0';
    else
        $select = '1';

    if ($_POST["active"] == '')
        $active = '0';
    else
        $active = '1';

    if ($_POST["active_online"] == '')
        $active_online = '0';
    else
        $active_online = '1';


    $queryUp = "update twosell_store set city_id=" . trim($_REQUEST['city']). ", block_group_id_target='" . trim($_REQUEST['groupIDs_target']) . "', block_group_id_suggestion = '" . trim($_REQUEST['groupIDs_suggestion']) . "' , block_products_other_then_group = " . $select .
            ", active = " . $active . ", active_online = " . $active_online . ", description3_text_1 ='" . trim($_REQUEST['description3_text_1']) . "' , group_ids_1='" . trim($_REQUEST['group_ids_1']) . "' , description3_text_2='" . trim($_REQUEST['description3_text_2']) . "', group_ids_2='" . trim($_REQUEST['group_ids_2']) .
            "', description3_text_3 = '" . trim($_REQUEST['description3_text_3']) . "', group_ids_3='" . trim($_REQUEST['group_ids_3']) . "' , description3_text_4='" . trim($_REQUEST['description3_text_4']) . "' , group_ids_4= '" . trim($_REQUEST['group_ids_4']) .
            "', description3_text_5='" . trim($_REQUEST['description3_text_5']) . "' , group_ids_5 ='" . trim($_REQUEST['group_ids_5']) . "' WHERE id =" . $id;
    //echo $queryUp;
    if ($database->executeQuery(utf8_decode($queryUp))) {
        $msg = '<tr><td bgcolor="RED" colspan="3" align="center"> <h2> Store information is saved </h2></td></tr>';
    } else {
        $msg = '<tr><td bgcolor="RED" colspan="3" align="center"> <h2> Store information is not saved </h2></td></tr>';
    }
}



$sql = "SELECT id, internal_id, title, chain_id, recommendation_config_id, active, active_online, address, postal_code, city_id, phone, notes, block_group_id_target, block_group_id_suggestion, block_products_other_then_group,
description3_text_1, group_ids_1, description3_text_2, group_ids_2, description3_text_3, group_ids_3, description3_text_4, group_ids_4, description3_text_5, group_ids_5 
FROM twosell_store WHERE id = $id";

$res = mysql_query($sql);
$row = mysql_fetch_array($res);
?>
<html>
    <head>
        <title>Edit Store</title>

        <style>
            <!--
            h1 { font-family: Arial, sans-serif; font-size: 30px; color: #004080;}
            h2 { font-family: Arial, sans-serif; font-size: 18px; color: #004080;}

            body,p,b,i,em,dt,dd,dl,sl,caption,th,td,tr,u,blink,select,option,form,div,li { font-family: Arial, sans-serif; font-size: 12px; }

            /* IE Specific */
            body, textarea {
                scrollbar-3dlight-color: #808080;
                scrollbar-highlight-color: #808080;
                scrollbar-face-color: #004080;
                scrollbar-shadow-color: #808080;
                scrollbar-darkshadow-color: #805B32;
                scrollbar-arrow-color: #000000;
                scrollbar-track-color: #F8EFE2;
            }
            .button {background-color:#0000FF; color: #FFF; font-size: 20px; font-weight: bold; height: 30px}
            /* END IE Specific */
            -->
        </style>
    </head>
    <body bgcolor="#ffffff" >

        <div style="background-image: url(twosell_line_760.png);  height:38px"> <img src="twosellbypocada.png" style="float:left"> </div>
        <table cellspacing="1" cellpadding="3" width="1307" align="left" bgcolor="#004080" 
               border="0" style="WIDTH: 1307px; HEIGHT: 557px">

            <tr>
                <td colspan="3">&nbsp;</td></tr>
            <tr>
                <td bgcolor="#ffffff">&nbsp;</td>
                <td bgcolor="#ffffff">
                    <p align="center">&nbsp;</p>
                    <h1 align="center"> Edit Store on Blocking Groups and Products</h1>
                    <p><strong><font size="3">Store ID: </font></strong><?php print $row['id']; ?></p>
                    <p><strong><font size="3">Store internal_id: </font></strong><?php print $row['internal_id']; ?></p>
                    <p><strong><font size=3>Store name: </font></strong><?php print utf8_encode($row['title']); ?></font></strong></p>

                    <form name="theForm" action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" > 
                        
                        <p><strong><font size=3>City name: </font></strong><?php combo($row['city_id']); ?></font></strong> Don't find your city!! Please <b>add city</b> into weather data through the <a href="http://dev1.twosell.se" target="_blank"> http://dev1.twosell.se <a/> and <b>Refresh </b>the page</p>
                        <p><strong><font size="3">Is store active in offline: </font></strong>
                            <input value="1" <?php if ($row['active'] == '1')
    echo 'CHECKED'; ?>  type="checkbox" name=active>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <strong><font size="3">Is store active in online: </font></strong>
                            <input value="1" <?php if ($row['active_online'] == '1')
                                       echo 'CHECKED'; ?>  type="checkbox" name=active_online>

                        <p><strong><font size="3">Provide group ids which going to block for <font color="red"><i> target </i></font>: </font></strong></p>
                        <p><textarea rows="3" cols="140" name=groupIDs_target ><?php print $row['block_group_id_target']; ?></textarea></p>
                        <p><strong><font size="3">Provide group ids which going to block for <font color="red"><i> suggestion </i></font>: </font></strong></p>
                        <p><textarea rows="3" cols="140" name=groupIDs_suggestion ><?php print $row['block_group_id_suggestion']; ?></textarea></p>
                        <p><strong><font size="3">Please checked, if the products other then groups is going to block</font></strong>
                            <strong> <input value="1" <?php if ($row['block_products_other_then_group'] == '1')
                                       echo 'CHECKED'; ?>  type="checkbox" name=restProduct></p>
                                <br>      
                                <p><strong><font size="3">Provide store wise text and groups ids for description3 : </font></strong></p>
                                <table border="0" cellspacing="9" cellpadding="9">
                                    <tr>
                                        <td><p><strong>Text_1: </strong></p>
                                            <p><textarea rows="1" cols="80" name=description3_text_1><?php print utf8_encode($row['description3_text_1']); ?></textarea></p>
                                        </td>
                                        <td><p><strong>Group ids for text_1: </strong></p>
                                            <p><textarea rows="1" cols="80" name=group_ids_1><?php print $row['group_ids_1']; ?></textarea></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><strong>Text_2: </strong></p>
                                            <p><textarea rows="1" cols="80" name=description3_text_2><?php print utf8_encode($row['description3_text_2']); ?></textarea></p>
                                        </td>
                                        <td>
                                            <p><strong>Group ids for text_2: </font></strong></p>
                                            <p><textarea rows="1" cols="80" name=group_ids_2><?php print $row['group_ids_2']; ?></textarea></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><strong>Text_3: </strong></p>
                                            <p><textarea rows="1" cols="80" name=description3_text_3><?php print utf8_encode($row['description3_text_3']); ?></textarea></p>
                                        </td>
                                        <td>
                                            <p><strong>Group ids for text_3: </strong></p>
                                            <p><textarea rows="1" cols="80" name=group_ids_3><?php print $row['group_ids_3']; ?></textarea></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> 
                                            <p><strong>Text_4: </strong></p>
                                            <p><textarea rows="1" cols="80" name=description3_text_4><?php print utf8_encode($row['description3_text_4']); ?></textarea></p>
                                        </td>
                                        <td>
                                            <p><strong>Group ids for text_4: </strong></p>
                                            <p><textarea rows="1" cols="80" name=group_ids_4><?php print $row['group_ids_4']; ?></textarea></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><strong>Text_5: </strong></p>
                                            <p><textarea rows="1" cols="80" name=description3_text_5><?php print utf8_encode($row['description3_text_5']); ?></textarea></p>
                                        </td>
                                        <td>
                                            <p><strong>Group ids for text_5: </strong></p>
                                            <p><textarea rows="1" cols="80" name=group_ids_5><?php print $row['group_ids_5']; ?></textarea></p> 
                                        </td>
                                    </tr>

                                </table>

                                <br>
                                <p><strong><font style="BACKGROUND-COLOR: #ffffff" size=3></strong>&nbsp;</p></font></td>
                                <td bgcolor="#ffffff">&nbsp;</td>
                                </tr>
                                <input value=" <?php echo $id ?> " type="hidden" name="id">
                                <tr>
                                    <td bgcolor="#ffffff" colspan="3" align="center"><input value="Save store" type="submit" name="save" class="button"> </td>
                                </tr>
                    </form> 
            <tr>
                <td bgcolor="#ffffff" colspan="3" align="center">  <?php print $msg; ?> </td>
            </tr>
            <tr>
                <td colspan="3" align="center"> &nbsp; </td>
            </tr>
        </table>

    </body>
</html>

