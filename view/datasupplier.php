<?php 

// simulate that this proccess might take a while so you can see the loadingMessage option work.
sleep(0);

$store = $_GET['store'];
$cashier = $_GET['cashier'];

//echo $store;
//echo $cashier;

include '../dbconnect.php';
include '../admin_config.php';
$stores = array();

$qc = "SELECT id,internal_id FROM twosell_store ORDER BY id ASC";
$qc1 = mysql_query($qc);
if($qc1 != FALSE){
    if(mysql_num_rows($qc1)>0){
      while($qc2 = mysql_fetch_array($qc1)){
          $stores[$qc2[id]] = $qc2[internal_id];
      }  
    }
}
//print_r($stores);
$cashiers = array();
$qp = "SELECT id,idnum,store_id FROM twosell_seller ORDER BY idnum, store_id";
$qp1 = mysql_query($qp);
if($qp1 != FALSE){
    if(mysql_num_rows($qp1)>0){
      while($qp2 = mysql_fetch_array($qp1)){
          $cashiers[$qp2[store_id]][$qp2[id]] = $qp2['idnum'];
      }  
    }
}
//print_r($stores);
//echo "<br /><br /><br /><br />";
//print_r($cashiers);

if($store && !$cashier){
    echo json_encode( $cashiers[$store] );
} else {
	echo '{}';
}
?>