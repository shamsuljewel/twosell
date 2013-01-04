<?php
// Creates a Redis client
$redis = new Redis();
// connect to redis

if($redis->connect('127.0.0.1')){
    $redis->select(12);
    echo "Connect Successfully";
    //$redis->set('jewel', 'my name');
    
    //$redis->set('jewel','my name');
    
    $allKeys = $redis->keys('*');
    print_r($allKeys);
    $oneKey = $redis->keys('arkenzoo:v:556674-3414-1');
    echo $oneKey;
    print_r($oneKey);
}
else{
    echo "Connection Problem"; 
}


?>
