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
            $.post("controller/navigation_manual_text.php",{z:a},function(data){
                $("#nav_div").html(data).fadeIn(100);
            });
            return false;
        });
        $('.delete_manual_text').click(function(){
           var answer = confirm("Delete selected Row ?")
           if (answer){
                var link = $(this).attr('id');
                var id = $('#id-'+link).val();
                //alert(id);
                $(this).closest("tr").hide(); // hide the table nearest row of the clicked event
                $.post("controller/ajax_delete_manual_text.php", {id: id}, function(data){
                    $("#error-box").html(data).fadeIn(100);
                    if(data != 1){
                        $(this).closest("tr").show(); // hide the table nearest row of the clicked event
                    }
                    //$('#adminactive-'+link).text('Disabled');
                });
           }
           return false;
        });
    });
</script>

<?php
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
//echo $_SESSION['query'];

$q1 = mysql_query($_SESSION['query_manual_text']);
if($q1 != FALSE){
    
$total = mysql_num_rows($q1);
    $max_row = 500;
    if($_POST['z']) $z = $_POST['z'];
    else $z = 0;
    $cur = $z*$max_row;
    //echo $z;
    $new_q = $_SESSION['query_manual_text']." LIMIT $cur, $max_row";
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
        echo "<table class='view_tbl'><tr><th>SL</th><th>Chain</th><th>Store</th><th>Cashier</th><th>Left Text</th><th>Right Text</th><th>Operation</th><th>Insert By</th><th>Update By</th></tr>";
            while($q2 = mysql_fetch_array($new_q1)){
                echo "<tr><td>$i</td><td>";if($q2[chain] == 1) echo "Statoil"; echo "</td><td>"; if($q2[store] == '-1') echo "-"; else echo $q2[store]; echo "</td>
                <td>";if($q2[cashier] == '-1') echo "-"; else echo $q2[cashier]; echo "</td>
                <td>".utf8_decode($q2[left_text])."</td>
                <td>".utf8_decode($q2[right_text])."</td><td><div id='div-$i'><a id='$i' href='admin.php?page=edit-manual-text&id=$q2[id]' title='Edit' class='edit_admin'><img src='images/edit.gif' /></a><a id='$i' href='admin.php?page=delete_manual_text' title='Delete' class='delete_manual_text'><img src='images/delete_on.png' /></a></div></td><td>$q2[insert_by]</td><td>$q2[update_by]</td></tr>";
                echo "<input type='hidden' id='id-$i' value='$q2[id]' />";
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
