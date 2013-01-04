
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
class DivTableView {


    //put your code here

    function showDivTable($data, $headers, $colTitles, $tableTitle = 'Table', $tableId = '', $tableHeight = 200, $tableWidth = 0) {
        ?>
        <style type="text/css">
            #divTable{
                display: table;                
                margin:20px 10px 0px 10px;
            }

            #divRow{
                display: table-row;    
            }

            #divRowColumnHeader{
                display:table-header-group;
            }

            #divCell0{
                display: table-cell;
                padding:0px 10px 0px 10px;
                background-color:lightgrey;
                color:black;

            }

            #divCell1{
                display: table-cell;
                padding:0px 10px 0px 10px;
                background-color:lightsteelblue;
                color:black;
            }
            #divCellHead0,#divCellHead1{
                display: table-cell;
                padding:0px 10px 0px 10px;
                background-color:darkseagreen;
                color:#FFFFFF;
                font-weight: bold;
                font-size:large;

            }

            #divRowTitle{
                display:table-caption;
                margin-bottom: 20px;
                text-align:center;
                font-size: x-large;

            }

            #divRwoGroup<?php echo $tableId;?>{
                height: <?php echo $tableHeight; ?>px; 
                <?php if ($tableWidth != 0) { ?>
                    width: <?php echo $tableWidth; ?>px;
                <?php } ?>
                overflow: scroll;
            }
        </style>
        <?php $stripe = 0; ?>
        <div id="divTable" name="<?php //echo $tableId; ?>">
            <div id="divRowTitle" ><h4><?php echo $tableTitle; ?></h4></div>
            <div id="divRwoGroup<?php echo $tableId;?>">
                <div id="divRowColumnHeader">
                    <?php foreach ($colTitles as $colTitle) { ?>
                        <div id="divCellHead<?php echo ($stripe % 2); ?>" >
                            <?php echo $colTitle; ?>
                        </div>
                        <?php $stripe++; ?>
                    <?php }//end for each for every column ?>
                </div>
                <?php //if (sizeof($headers) % 2 == 0)
                    //$stripe++; ?>
                <?php foreach ($data as $row) { ?>
                    <div id="divRow">
                        <?php foreach ($headers as $header) { ?>
                            <div id="divCell<?php echo ($stripe % 2); ?>" onClick="<?php echo 'table_' . $tableId . '.' . $header; ?>(this);">
                                <?php echo $row[$header]; ?>
                            </div>
                            <?php //$stripe++; ?>
                        <?php }//end for each for every column ?>
                    </div>
                    <?php //if (sizeof($headers) % 2 == 0)
                        $stripe++; ?>
                <?php }//end for each for every row   ?>
            </div>
        </div>
        <?php
    }

}
?>