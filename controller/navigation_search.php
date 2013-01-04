<?php
session_start();
$twosell_all_products = $_SESSION['twosell_all_products'];
//$twosell_all_products = array_values($twosell_all_products);
//print_r($twosell_all_products);
include '../dbconnect.php';
include '../functions/commonFunction.php';
?>
<script type="text/javascript" src="js/tinybox.js"></script>
<link rel="stylesheet" href="css/style.css" />
<script>
    $(document).ready(function(){
        $('.a_li').click(function(){
            var a = $(this).attr('id');
            $("#nav_div").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/navigation_search.php",{z:a},function(data){
                $("#nav_div").html(data).fadeIn(100);
            });
            return false;
        });
        $('.showTransaction').click(function(){
            var id = $(this).attr('id');
            //alert(id);
            TINY.box.show({url:'view/transaction.php',post:'id='+id ,width:600,height:600,opacity:20,topsplit:3});
            return false;
        });    
    });
</script>

<?php
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
//echo $_SESSION['query'];
$q_update = "SELECT last_log_id, date_time, last_final_receipt FROM twosell_statistics_crontab_log ORDER BY id DESC LIMIT 1";
$result = sql_query($q_update, $link);
//print_r($result[0]);
if(gettype($result) == "array"){
    echo "<div style='border: 1px dotted #CCC; padding: 10px'>";
    echo "<b>Statistics Updated at: </b>".$result[0][date_time]."<br />";
    echo "Last Final ID / Trans: ".$result[0][last_log_id]." / ".$result[0][last_final_receipt];
    echo "</div>";
}
else{
    echo $result;
}
$total = count($_SESSION['twosell_all_products']);
//echo $total;
if($total > 0){
    $max_row = 500;
    if($_POST['z']) $z = $_POST['z'];
    else $z = 0;
    $cur = $z*$max_row;
    
    echo "<table width='100%'>
        <tr><td style='padding:10px;'>";

                    $l = $cur + $max_row;
                    if($l>$total) $l = $total;
                    //if($cur == 0) $cur= 1;
                    $cur++;
                    echo "<div>Showing <b>".$cur."</b> to <b>".$l."</b> out of <b>".$total."</b></div>";	
        echo "</td><td>";
        echo "<ul class='pagi'>";    
            for($i=0; $i*$max_row < $total; $i++)
                {
                        if($z != $i)
                                echo "  <li><a href='#' class='a_li' id='$i'>".($i+1)."</a></li>";
                        else
                                echo "  <li class='active_a'><strong style='color:#FF0000; float: left'; >".($i+1)."</strong></li>";
                }
                echo "</ul></div>";
        echo "</td></tr>
        </table>";
        //$i=$cur;
        if($total < $max_row) $max_show = $total;
        else $max_show = $cur + $max_row -1;
        echo "<table class='view_tbl'><tr><th>SL</th><th>Transaction Id</th><th>Date Time</th><th>Cashier</th><th>Total Cost<br />".$_SESSION['total_cost']."</th><th>Total Item<br />".$_SESSION['total_item']."</th><th>Twosell Item<br />".$_SESSION['total_twosell']."</th><th>Screen Time<br />(sec.)</th><th>Twosell<br />".$_SESSION['total_twosell_cost']."</th></tr>";
            for($i=$cur-1; $i < $max_show; $i++){
                //$i=$i+1;
                $index = $i+1;
                echo "<tr><td>".$index."</td><td><a href='#' id='".$twosell_all_products[$i][id]."' class='showTransaction' >".$twosell_all_products[$i]['transactionid']."</a></td><td>".$twosell_all_products[$i][time_of_purchase]."</td>
                <td>".$twosell_all_products[$i][seller_id]."</td>
                <td>".$twosell_all_products[$i][total_cost]."</td>
                <td>".$twosell_all_products[$i][n_rows]."</td><td>".$twosell_all_products[$i][twosell_item_count]."</td><td>";if($twosell_all_products[$i][screen_time] == -1) echo "Not Found"; else echo $twosell_all_products[$i][screen_time]; echo "</td><td>".$twosell_all_products[$i][direct_gross_incl_vat]."</td></tr>";
            }
            echo "</table>";
    echo "<ul class='pagi'>";    
            for($i=0; $i*$max_row < $total; $i++)
                {
                        if($z != $i)
                                echo "  <li><a href='#' class='a_li' id='$i'>".($i+1)."</a></li>";
                        else
                                echo "  <li class='active_a'><strong style='color:#FF0000; float: left'; >".($i+1)."</strong></li>";
                }
                echo "</ul></div>";
}
else {
    echo "No Data Found...";
}
?>
