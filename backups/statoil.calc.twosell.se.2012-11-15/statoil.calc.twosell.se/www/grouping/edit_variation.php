
<?php
include("menue.php");
include("debug.php");

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
    require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
}

function combo($table, $selected_id) {

    $database = new DatabaseUtility();

    $querySeasons = "SELECT id, Name FROM weather_dev." . $table . " Order by id";
    $COMORBSeasons = $database->getFieldsData($querySeasons, array('id', 'Name'));
    //print_r($COMORBSeasons);
    if (!empty($COMORBSeasons)) {
        echo "<select name='" . $table . "'>";
        for ($i = 0; $i < sizeof($COMORBSeasons); $i++) {
            echo "<option value='" . utf8_encode($COMORBSeasons[$i]['id']) . "' ";
            if (utf8_encode($COMORBSeasons[$i]['id']) == $selected_id)
                echo "selected='selected'";
            echo ">" . utf8_encode($COMORBSeasons[$i]['Name']) . "</option>";
        }
        echo "</select>";
    }
}

$database = new DatabaseUtility();



//print_r($querySeasons); exit();
//print_r($sql); exit();
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // $id=$_GET['id'];
    $sql = "SELECT id, variation_id, variation_text, group_ids, store_ids, create_date, modified_date, comments FROM tsln_variation_text WHERE id =" . $id;
    $variation = $database->getFieldsData($sql, array('id', 'variation_id', 'variation_text', 'group_ids', 'store_ids', 'create_date', 'modified_date', 'comments'));
    for ($a = 0; $a < sizeof($variation); $a++) {
        $variation_id = str_split($variation[$a]['variation_id']);
        //print_r($variation_id);
        ?>

        <head>

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>
                TWOSELL: Editera grupp <?php echo $id; ?>
            </title>
        </head>


        <div style="width:1200px; float:left; background:#F8F8FF;">


            <form name="theForm" action="save_variation.php" method="post"> 

                <table border="1" cellspacing="0" cellpadding="3" width="100%" align="center">

                    <tr>
                        <td>Internal ID :</td>
                        <td><?php echo utf8_encode($variation[$a]['id']); ?></td></tr>
                    <tr>
                        <td>Variation text was created :</td>
                        <td><?php print $variation[$a]['create_date']; ?></td>
                    </tr>
                    <tr>
                        <td>Variation text was modified :</td>
                        <td><?php print $variation[$a]['modified_date']; ?></td>
                    </tr>
                    <tr>
                        <td>Variation ID:</td>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="3" width="100%" align=center>              
                                <tr>
                                    <td><?php echo utf8_encode('Årstid'); ?></td>
                                    <td><?php echo utf8_encode('Celebration day'); ?></td>
                                    <td>Veckan</td>
                                    <td>Dygn</td>
                                    <td><?php echo utf8_encode('Väder'); ?></td>
                                    <td><?php echo utf8_encode('Temparature'); ?></td></tr>
                    <tr>
                        <td>
                            <?php
                            combo('tsln_seasons', $variation_id[0])
                            ?>                       
                        </td>
                        <td>
                            <?php
                            combo('tsln_celebration_day', $variation_id[1])
                            ?> 

                        </td>
                        <td>
                            <?php
                            combo('tsln_week', $variation_id[2])
                            ?> 

                        </td>
                        <td>
                            <?php
                            combo('tsln_days', $variation_id[3])
                            ?> 

                        </td>
                        <td>
                            <?php
                            combo('tsln_weather', $variation_id[4])
                            ?> 
                        </td>
                        <td>
                            <?php
                            combo('tsln_temparature', $variation_id[5])
                            ?> 
                        </td>
                    </tr>
                </table>
                </td>
                </tr>
                <tr>
                    <td>Variation text :</td>
                    <td><textarea rows="1" cols="80" name=variation_text><?php print utf8_encode($variation[$a]['variation_text']); ?></textarea></td>
                </tr>
                <tr>
                    <td>Variation text for group ids :</td>
                    <td><textarea rows="1" cols="80" name=group_ids><?php print $variation[$a]['group_ids']; ?></textarea> i.e. group ids for target products, don't put '*'</td>
                </tr>
                <tr>
                    <td>Variation text for store ids:</td>
                    <td><textarea rows="1" cols="80" name=store_ids><?php print $variation[$a]['store_ids']; ?></textarea> i.e. which store Twosell will consider, put '*' if it is for all active stores</td>
                </tr>
                <tr>
                    <td>Comments :</td>
                    <td><textarea rows="1" cols="80" name=comments><?php print utf8_encode($variation[$a]['comments']); ?></textarea></td>
                </tr>


                <input value=" <?php echo $id ?> " type="hidden" name="id">
                <tr>
                    <td bgcolor="#ffffff" colspan="2" align="center"><input value="Update Info." type="submit" name="update" class="button"> </td>
                </tr>

                </table>
            </form>
        <?php }
    } else { ?>

        <form name="the" action="save_variation.php" method="post"> 

            <table border="1" cellspacing="0" cellpadding="3" width="100%" align="center">
                <tr>
                    <td>Variation ID:</td>
                    <td>
                        <table border="0" cellspacing="0" cellpadding="3" width="100%" align=center>              
                            <tr>
                                <td><?php echo utf8_encode('Årstid'); ?></td>
                                <td><?php echo utf8_encode('Celebration day'); ?></td>
                                <td>Veckan</td>
                                <td>Dygn</td>
                                <td><?php echo utf8_encode('Väder'); ?></td>
                                <td><?php echo utf8_encode('Temparature'); ?></td></tr>
                <tr>
                    <td>
                        <?php
                        combo('tsln_seasons', 0)
                        ?> 
                    </td>
                    <td>
                        <?php
                        combo('tsln_celebration_day', 0)
                        ?> 

                    </td>
                    <td>
                        <?php
                        combo('tsln_week', 0)
                        ?> 
                    </td>
                    <td>
                        <?php
                        combo('tsln_days', 0)
                        ?> 
                    </td>
                    <td>
                        <?php
                        combo('tsln_weather', 0)
                        ?> 
                    </td>
                    <td>
                        <?php
                        combo('tsln_temparature', 0)
                        ?> 
                    </td>
                </tr>
            </table>
            </td>
            </tr>
            <tr>
                <td>Variation text :</td>
                <td><textarea rows="1" cols="80" name=variation_text></textarea></td>
            </tr>
            <tr>
                <td>Variation text for group ids :</td>
                <td><textarea rows="1" cols="80" name=group_ids></textarea></td>
            </tr>
            <tr>
                <td>Variation text for store ids:</td>
                <td><textarea rows="1" cols="80" name=store_ids></textarea></td>
            </tr>
            <tr>
                <td>Comments :</td>
                <td><textarea rows="1" cols="80" name=comments></textarea></td>
            </tr>


            <tr>
                <td bgcolor="#ffffff" colspan="2" align="center"><input value="Insert Info." type="submit" name="insert" class="button"> </td>
            </tr>

            </table>
        </form>
    <?php } ?>
    <div style="background-image: url(twosell_line_760.png);  height:38px"> <img src="twosellbypocada.png" style="float:right">
    </div>


</div>


