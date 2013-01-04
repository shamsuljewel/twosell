<?php
session_start();
echo "abcd";
include '../dbconnect.php';
include '../functions/commonFunction.php';
if(isLoggedin() == FALSE){
    header("Location:index.php");	
}
if($_POST['id']){
    $id = $_POST['id'];
    echo $id;
    $q = "SELECT * FROM final_data where id='$id' LIMIT 1";
    $result = sql_query($q, $link);
    echo "<div><h1>Transaction Details</h1></div>";
    if(is_array($result) == "array"){
        //print_r($result);
        
        echo "<table class='view_tbl'>";
        echo "<tr><td>Transaction ID: </td><td>".$result[0]['transaction_id'].", Date: ".$result[0]['datetime']."</td></tr>";
        echo "<tr><td>Store: </td><td>".$result[0]['']."</td></tr>";
        echo "<tr><td>Cashier: </td><td>".$result[0]['']."</td></tr>";
        $toArrayItem = json_decode($result[0]['items'], true);
        $toArrayItemTwosell = json_decode($result[0]['twosell_items'], true);
        $toArrayItemPre = json_decode($result[0]['pre_receipt_item'], true);
        //print_r($toArrayItem);
        $count = count($toArrayItem['items']);
        echo "<tr><td>Products: </td><td> Total Products: ".$count;
        echo "<table><tr><th>Item Id</th><th>Name</th><th>Quantity</th><th>Cost</th></tr>";
        
        for($i=0; $i < $count; $i++){
            echo "<tr><td>".$toArrayItem['items'][$i]['item_id']."</td>
            <td>".$toArrayItem['items'][$i]['article_name']."</td>    
            <td>".$toArrayItem['items'][$i]['quantity']."</td>     
            <td>".$toArrayItem['items'][$i]['amount']."</td>        
            </tr>";
        }
        echo "</table>";
        echo "</td></tr>";
        $keys  = array_keys($toArrayItemTwosell['items']);
        $tCount = count($keys);
        
        echo "<tr><td>Twosell</td><td>Total Products: ".$tCount;
        echo "<table><tr><th>Item Id</th><th>Name</th><th>Quantity</th><th>Cost</th></tr>";
        
        for($i=0; $i < $tCount; $i++){
            echo "<tr><td>".$toArrayItemTwosell['items'][$keys[$i]]."</td>
            </tr>";
        }
        echo "</table>";
        echo "</td></tr>";
        
        $keys  = array_keys($toArrayItemPre['items']);
        $preCount = count($keys);
        
        echo "<tr><td>Pre Receipt</td><td>Total Products: ".$preCount;
        echo "<table><tr><th>Item Id</th><th>Name</th><th>Quantity</th><th>Cost</th></tr>";
        
        for($i=0; $i < $preCount; $i++){
            if($toArrayItemPre['items'][$keys[$i]]['id'] == "") $item = "Petrol / Gas Items";
            else $item = $toArrayItemPre['items'][$keys[$i]]['id'];
            echo "<tr><td>".$item."</td>
            </tr>";
        }
        echo "</table>";
        echo "</td></tr>";
        echo "</table>";
    }
    else{
        echo $result;
    }
}
else{
    echo "Not Posted..";
}    

?>
