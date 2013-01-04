<?php
$phparray = array(
    0 => array('item_id' => '123',
    'quantity'=> '5.0',
    'amount' => '1.0',
    'discount' => '0.78',
    'tax_rates' => '2',
    'article_name' => 'ablout name'
    ),
    1 => array('item_id' => '124',
    'quantity'=> '5.0',
    'amount' => '1.0',
    'discount' => '0.78',
    'tax_rates' => '2',
    'article_name' => 'ablout name')
);

print_r($phparray);

$json = json_encode(array('items' => $phparray));
echo $json;

$backArray = json_decode(utf8_encode($json));


print_r($backArray);
echo count($backArray->items);
print_r($backArray->items);
?>
