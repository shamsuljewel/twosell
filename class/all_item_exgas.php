<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of all_item_exgas
 *
 * @author Shamsul
 */
class all_item_exgas {
    private $total_cost = 0;
    private $total_items = 0;
    public function __construct($q, $items) {
        if(is_array($items)){
            while($q2 = mysql_fetch_array($q)){
                // that row costs and n_rows value
                $this->total_cost += $q2['total_cost'];
                $this->total_items += $q2['n_rows'];
                $q_t_items = "SELECT * FROM transaction_tbl WHERE ref_purchase = '$q2[id]' LIMIT 1";
                $q_t_items1 = mysql_query($q_t_items) or die(mysql_error()); 
                $q_t_items2 = mysql_fetch_array($q_t_items1);
                if($q_t_items2['items'] != NULL) $items = json_decode($q_t_items2['items'], true);
                if(!empty($items['items'])){
                    foreach ($items[items] as $key => $value) {
                        if($value['item_id'] == "" || in_array($value['item_id'], $_SESSION['banned_products'])){
                            $net = $value['amount'] - $value['discount'];
                            $this->total_cost -= $net;
                            $this->total_items -= 1;
                        }
                    }
                }
                
            }
        }
    }
    public function total_items(){
        return $this->total_items;
    }
    public function total_costs(){
        return $this->total_cost;
    }
}

?>
