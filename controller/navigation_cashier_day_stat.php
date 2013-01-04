<?php
session_start();

include '../dbconnect.php';
include '../functions/commonFunction.php';
?>
<script>
    $(document).ready(function(){
        $('.a_li').click(function(){
            var a = $(this).attr('id');
            $("#nav_div").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Processing...').fadeIn(100);
            $.post("controller/navigation_store_day_stat.php",{z:a},function(data){
                $("#nav_div").html(data).fadeIn(100);
            });
            return false;
        });
    });
</script>

<?php
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
//echo $_SESSION['query'];

$q1 = mysql_query($_SESSION['query_store_day_stat']);
if($q1 != FALSE){
    
$total = mysql_num_rows($q1);
    $max_row = 500;
    if($_POST['z']) $z = $_POST['z'];
    else $z = 0;
    $cur = $z*$max_row;
    //echo $z;
    $new_q = $_SESSION['query_store_day_stat']." LIMIT $cur, $max_row";
    //echo "<br />".$new_q;
    $new_q1 = mysql_query($new_q);
    if($new_q1 != FALSE){
        echo "<form name='edit-manual' action='' method='post'>";
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
        $i=$cur;
        echo "<table class='view_tbl'><tr><th>SL</th><th>Store</th><th>Day</th><th>Total</th><th>Avg</th></tr>";
            while($q2 = mysql_fetch_array($new_q1)){
                echo "<tr><td>$i</td>
                <td>$q2[s_id]</td>
                <td>$q2[per]%</td>
                <td>to</td><td>avg</td></tr>";
                $i++;
            }
            
            echo "</table></form>";
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
        echo "Query Execution Error..please contact webmaster";
    }

}
else {
    echo "Main Query Execution Error..please contact webmaster";
}
?>
