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
            $.post("controller/navigation_search_fetch.php",{z:a},function(data){
                $("#nav_div").html(data).fadeIn(100);
            });
            return false;
        });
        $('.showTransaction').click(function(){
            var id = $(this).attr('id');
            //alert(id);
            TINY.box.show({url:'view/transaction_fetch.php',post:'id='+id ,width:600,opacity:20,topsplit:3, fixed:false});
            return false;
        });    
    });
</script>

<?php
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
//echo $_SESSION['query'];

$total = $_SESSION['twosell_all_products'];
//echo $total;
if($total > 0){
    $max_row = 500;
    if($_POST['z']) $z = $_POST['z'];
    else $z = 0;
    $cur = $z*$max_row;
    $new_q = $_SESSION['query']." LIMIT $cur, $max_row";
    //echo "<br />".$new_q;
    $new_q1 = mysql_query($new_q);
    if($new_q1 != FALSE){
    echo "<table style='width:900px'>
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
        $i=$cur;
        if($total < $max_row) $max_show = $total;
        else $max_show = $cur + $max_row -1;
        
        echo "<table class='view_tbl'><tr><th>SL</th><th>ID(F-T)</th><th>Transaction Id</th><th>Date Time</th><th>Cashier</th><th>Total Cost<br />".$_SESSION['total_cost']."</th><th>Total Item<br />".$_SESSION['total_item']."</th><th>Twosell Item<br />".$_SESSION['total_twosell']."</th><th>Screen Time<br />(sec.)</th><th>Twosell<br />".$_SESSION['total_twosell_cost']."</th></tr>";
           while($q2 = mysql_fetch_array($new_q1)){
                $transaction_id = $q2['final_id']."-".$q2['pre_id']."-".$q2['id'];
                echo "<tr><td>".$i."</td><td>".$q2['final_id']."-".$q2['id']."</td><td><a href='#' id='".$transaction_id."' class='showTransaction' >".$q2['transactionid']."</a></td><td>".$q2['time_of_purchase']."</td>
                <td>".$q2['seller_id']."</td>
                <td>".$q2['total_cost']."<br />".$q2['total_cost_ban']."</td>
                <td>".$q2['n_rows']."-".$q2['total_item_ban']."</td><td>".$q2['twosell_item_count']."</td><td>";if($q2['screen_time'] == -1) echo "Not Found"; else echo $q2['screen_time']; echo "</td><td>".$q2['direct_gross_incl_vat']."</td></tr>";
                $i++;
            }
            echo "</table>";
    echo "<div style='width: 900px'><ul class='pagi'>";    
            for($i=0; $i*$max_row < $total; $i++)
                {
                        if($z != $i)
                                echo "  <li><a href='#' class='a_li' id='$i'>".($i+1)."</a></li>";
                        else
                                echo "  <li class='active_a'><strong style='color:#FF0000; float: left'; >".($i+1)."</strong></li>";
                }
                echo "</ul></div>";
    }
}
else {
    echo "No Data Found...";
}
?>
