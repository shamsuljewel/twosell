<?php
function fullArrayDiff($left, $right) 
{ 
    return array_diff($left, array_intersect($left, $right)); 
}
function array_diff_once(){
    if(($args = func_num_args()) < 2)
        return false;
    $arr1 = func_get_arg(0);
    $arr2 = func_get_arg(1);
    if(!is_array($arr1) || !is_array($arr2))
        return false;
    foreach($arr2 as $remove){
        foreach($arr1 as $k=>$v){
            if((string)$v === (string)$remove){ //NOTE: if you need the diff to be STRICT, remove both the '(string)'s
                unset($arr1[$k]);
                break; //That's pretty much the only difference from the real array_diff :P
            }
        }
    }
    //Handle more than 2 arguments
    $c = $args;
    while($c > 2){
        $c--;
        $arr1 = array_diff_once($arr1, func_get_arg($args-$c+1));
    }
    return $arr1;
}

$temp_final = array();
    $temp_final[0] = "201";
    $temp_final[1] = "202";
    $temp_final[2] = "202";
    $temp_final[3] = "201";
    $temp_final[4] = "201";
    
$temp_pre = array();
$temp_pre[0] = "203";
$temp_pre[1] = "201";
$temp_pre[2] = "202";
$temp_pre[3] = "202";

$arr1 = $temp_final;
$arr2 = $temp_pre;
$diff = array_diff_once($arr1, $arr2);
print_r($diff);

?>
