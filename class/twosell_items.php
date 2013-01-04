<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of twosell_items
 *
 * @author Shamsul
 */
class twosell_items {
    private $twosell_items_array = array();
    private $total_twosell_cost = 0;
    private $total_receipt_cost = 0;
    private $total_receipt_twosell = 0;
    private $total_twosell_items = 0;
    private $total_twosell_receipt_items = 0;
    private $total_cost = 0;
    private $n_rows = 0;
    private $column = 0;
    private $array_index = 0;
    public function __construct($q, $items) {
        if(is_array($items)){
        $this->column = count($items);
        while($q2 = mysql_fetch_array($q)){
            for($i=0; $i < $this->column; $i++){
                $this->twosell_items_array[$this->array_index][$items[$i]] = $q2[$items[$i]];
            }
            // Get the transaction details which contain final items and twosell items
            $q_t_items = "SELECT * FROM transaction_tbl WHERE ref_purchase = '$q2[id]' LIMIT 1";
            $q_t_items1 = mysql_query($q_t_items) or die(mysql_error()); 
            $q_t_items2 = mysql_fetch_array($q_t_items1);
            if($q_t_items2['twosell_items'] != NULL) $twosell_items = json_decode($q_t_items2['twosell_items'], true);
//            //print_r($twosell_items);
            if(!empty($twosell_items[items])){
                foreach ($twosell_items[items] as $key => $value) {
              //      echo $value."->";
                    if($value == ""){
                        $all_items = json_decode($q_t_items2['items'], true);
                        $all_item_count = count($all_items[items]);
                       // echo  $all_item_count;
                        //print_r($all_items[items]);
                        for($i=0; $i< $all_item_count; $i++){
                            $all_items_list[$i] = $all_items['items'][$i]['item_id'];
                        }
                        //print_r($all_items_list);
                        $search_key = array_keys($all_items_list, $value);
                        if(!empty($search_key)){
                            $net_amount = $all_items[items][$search_key[0]][amount] - $all_items[items][$search_key[0]][discount];
                            $this->twosell_items_array[$this->array_index]['direct_gross_incl_vat'] -= $net_amount;
                            $this->twosell_items_array[$this->array_index]['direct_gross_incl_vat'] = round($this->twosell_items_array[$this->array_index]['direct_gross_incl_vat'], 2);
                            $this->twosell_items_array[$this->array_index]['twosell_item_count'] -= 1;
                        }
                        if($this->twosell_items_array[$this->array_index]['twosell_item_count'] <= 0){
                            unset($this->twosell_items_array[$this->array_index]);
                        }
                    }
                    else{
                        if(in_array($value, $_SESSION['banned_products'])){
                       // echo "banned!";
                        $all_items = json_decode($q_t_items2['items'], true);
                        $all_item_count = count($all_items[items]);
                       // echo  $all_item_count;
                        //print_r($all_items[items]);
                        for($i=0; $i< $all_item_count; $i++){
                            $all_items_list[$i] = $all_items['items'][$i]['item_id'];
                        }
                        //print_r($all_items_list);
                        $search_key = array_keys($all_items_list, $value);
                        if(!empty($search_key)){
                            $net_amount = $all_items[items][$search_key[0]][amount] - $all_items[items][$search_key[0]][discount];
                            $this->twosell_items_array[$this->array_index]['direct_gross_incl_vat'] -= $net_amount;
                            $this->twosell_items_array[$this->array_index]['direct_gross_incl_vat'] = round($this->twosell_items_array[$this->array_index]['direct_gross_incl_vat'], 2);
                            $this->twosell_items_array[$this->array_index]['twosell_item_count'] -= 1;
                        }
                        if($this->twosell_items_array[$this->array_index]['twosell_item_count'] <= 0){
                            unset($this->twosell_items_array[$this->array_index]);
                        }
                    }
                    }
                }
            }
            $all_items_cost = json_decode($q_t_items2['items'], true);
            if(!empty($all_items_cost['items'])){
                foreach ($all_items_cost['items'] as $key => $value) {
//                    print_r($value);
//                    echo "<br />*************<br />";
                    if($value['item_id'] == "" || in_array($value['item_id'], $_SESSION['banned_products'])){
                        $net = $value['amount'] - $value['discount'];
                        $this->twosell_items_array[$this->array_index]['total_cost'] -= $net;
                        $this->twosell_items_array[$this->array_index]['n_rows'] -= 1;
                    }
                }
            }
            $this->array_index++;
            }
            foreach ($this->twosell_items_array as $value) {
                $this->total_twosell_cost += $value['direct_gross_incl_vat']; 
                $this->total_receipt_cost += $value['total_cost']; 
                $this->total_receipt_twosell += $value['n_rows']; 
                $this->total_twosell_items += $value['twosell_item_count']; 
                
                $this->total_cost += $value['total_cost'];
                $this->n_rows += $value['n_rows'];
            }
        }
    }
    // full twosell sell rows
    public function twosell_items(){
        return array_values($this->twosell_items_array);
        //$twosell_all_products = array_values($twosell_all_products);
    }
    // number of column to represent into each row
    public function total_columns(){
        return $this->count;
    }
    // twosell items cost
    public function total_twosell_cost(){
        return $this->total_twosell_cost;
    }
    // total number of receipt that has twosell sell
    public function total_twosell_receipt(){
        return count($this->twosell_items_array);
    }
    // the cost of total cost of receipt that has twosell sell
    public function total_receipt_cost(){
        return $this->total_receipt_cost;
    }
    public function total_twosell_items(){
        return $this->total_twosell_items;
        
    }
    public function total_twosell_receipt_items(){
        return $this->total_receipt_twosell;
    }
    public function total_cost_withoutgas(){
        return $this->total_cost;
    }
    public function total_items_withoutgas(){
        return $this->n_rows;
    }
}
?>
