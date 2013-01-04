<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DivTableView
 *
 * @author siblee
 */
class TableView {

    //put your code here

    function showTable($data, $headers, $colTitles, $tableTitle = 'Table', $tableId = '', $tableHeight = 200, $tableWidth = 0) {
        ?>
        <style type="text/css">
            #divTable<?php echo $tableId; ?>{
                height: <?php echo $tableHeight; ?>px; 
                <?php if ($tableWidth != 0) { ?>
                    width: <?php echo $tableWidth; ?>px;
                <?php } ?>
                overflow: scroll;
            }

            #divRow{
            }

            #divRowColumnHeader{

            }

            #divCell0{
                background-color:lightgrey;
                color:black;
                text-align:left;                
                font-weight: normal;
            }

            #divCell1{
                background-color:lightsteelblue;
                color:black;
                text-align:left;                
                font-weight: normal;
            }
            #divCellHead0,#divCellHead1{
                background-color:darkseagreen;
                color:#FFFFFF;
                font-weight: bold;
                font-size:large;

            }

            #divRowTitle{
                text-align:center;
                font-size: x-large;

            }

        </style>
        <?php $stripe = 0; ?>
        <table><tr><th>
        <div id="divRowTitle" ><h4><?php echo utf8_encode($tableTitle); ?></h4></div>
        <div id="divTable<?php echo $tableId; ?>" name="<?php echo $tableId; ?>">
            <table>
                <?php foreach ($colTitles as $colTitle) { ?>
                    <th id="divCellHead<?php echo ($stripe % 2); ?>" >
                        <?php echo $colTitle; ?>
                    </th>
                    <?php $stripe++; ?>
                <?php }//end for each for every column ?>

                <?php //if (sizeof($headers) % 2 == 0)
                //$stripe++; ?>
                <?php foreach ($data as $row) { ?>
                    <tr id="divRow">
                        <?php  
                           foreach ($headers as $header) { ?>
                            <td id="divCell<?php echo ($stripe % 2); ?>" onClick="<?php echo 'table_' . $tableId . '.' . $header; ?>(this);">
                                <?php
                                    echo utf8_encode($row[$header]);
                                ?>
                            </td>
                            <?php //$stripe++;  
                           }  //end for each for every column ?>
                    </tr>
                    <?php //if (sizeof($headers) % 2 == 0)
                    $stripe++;
                    }//end for each for every row   ?>
            </table>
        </div>
        </th> </tr></table>
        <?php
    }


    function showTableTxt($data, $headers, $colTitles, $tableTitle = 'Table', $tableId = '', $tableHeight = 200, $tableWidth = 0) {

                foreach ($colTitles as $colTitle) { echo $colTitle.';'; $stripe++; }                   
                    echo '
                            ';

                foreach ($data as $row) {foreach ($headers as $header) { echo utf8_encode($row[$header]).';'; }                           
                          echo '
                            ';
                    $stripe++; }
    }

    function showDiv($data, $headers, $colTitles, $tableTitle = 'Table', $tableId = '', $tableHeight = 200, $tableWidth = 0) {
        ?>
        <style type="text/css">
            #divTable<?php echo $tableId; ?>{
//                height: <?php echo $tableHeight; ?>px; 
                <?php if ($tableWidth != 0) { ?>
//                    width: <?php echo $tableWidth; ?>px;
                <?php } ?>
//                overflow: scroll;
            }

            #divRow{
            }

            #divRowColumnHeader{

            }

            #divCell0{
                background-color:lightgrey;
                color:black;
                text-align:left;                
                font-weight: normal;
            }

            #divCell1{
                background-color:lightsteelblue;
                color:black;
                text-align:left;                
                font-weight: normal;
            }
            #divCellHead0,#divCellHead1{
                background-color:darkseagreen;
                color:#FFFFFF;
                font-weight: bold;
                font-size:large;

            }

            #divRowTitle{
                text-align:center;
                font-size: x-large;

            }

        </style>
        <?php $stripe = 0; ?>
        <div id="divRowTitle" ><h4><?php echo utf8_encode($tableTitle); ?></h4></div>
        <div id="divTable<?php echo $tableId; ?>" name="<?php echo $tableId; ?>">
            <table>
                <?php foreach ($colTitles as $colTitle) { ?>
                    <th id="divCellHead<?php echo ($stripe % 2); ?>" >
                        <?php echo $colTitle; ?>
                    </th>
                    <?php $stripe++; ?>
                <?php }//end for each for every column ?>

                <?php //if (sizeof($headers) % 2 == 0)
                //$stripe++; ?>
                <?php foreach ($data as $row) { ?>
                    <tr id="divRow">
                        <?php  
                           foreach ($headers as $header) { ?>
                            <td id="divCell<?php echo ($stripe % 2); ?>" onClick="<?php echo 'table_' . $tableId . '.' . $header; ?>(this);">
                                <?php
                                    echo utf8_encode($row[$header]);
                                ?>
                            </td>
                            <?php //$stripe++;  
                           }  //end for each for every column ?>
                    </tr>
                    <?php //if (sizeof($headers) % 2 == 0)
                    $stripe++;
                    }//end for each for every row   ?>
            </table>
        </div>

        <?php
    }



}
?>