
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CBR
 *
 * @author siblee
 */
class CBR {

    //put your code here
    public function CBRCalculation($caseLibrary, $targetCase, $weights, $threshold) {
        $utility = new Utility();
        $tempSimArray = array();
        //print_r($caseLibrary);
        foreach ($caseLibrary as $case) {
            $t = array();
            $t['productId'] = $case['productId'];
            $t['name'] = $case['name'];
            $t['priceMean'] = $case['priceMean'];
            $t['priceStddev'] = $case['priceStddev'];
            $t['productgroup_id'] = $case['productgroup_id'];
            //echo $utility->onSweChar($case['name']) . "<br>";
            $t['similarity'] = $this->similarity($targetCase, $case, $weights);
            if ($t['similarity'] > $threshold)
                $tempSimArray[] = $t;
        }
        $tempSimArray = $utility->multipleKeyArrySort($tempSimArray, 'similarity', true);
        return $tempSimArray;
    }

    public function similarity($product1, $product2, $weights, $showSimilarity = false) {
        //print_r($product2);exit();
        $comments = array();

        $percentItem = $percentBrand = $similar_productgroup_id = 0;

        if (($product1['itemName'] != '' && $product2['itemName']) != "") {
            $percentItem = ($product1['itemName'] == $product2['itemName']) ? 100 : 0;
        }

        if (($product1['brandName'] != '' && $product2['brandName']) != "") {
            $percentBrand = ($product1['brandName'] == $product2['brandName']) ? 100 : 0;
        }

        similar_text($product1['colorName'], $product2['colorName'], $percentColor);
        similar_text($product1['attributeName'], $product2['attributeName'], $percentAttribute);
        similar_text($product1['otherName'], $product2['otherName'], $percentOther);


        $maxPriceMean = max($product1['priceMean'], $product2['priceMean']);
        $maxPriceStddev = max($product1['priceStddev'], $product2['priceStddev']);
        if ($maxPriceMean == 0)
            $maxPriceMean = 1;
        if ($maxPriceStddev == 0)
            $maxPriceStddev = 1;
        $percentPriceMean = (1 - abs($product1['priceMean'] - $product2['priceMean']) / $maxPriceMean) * 100;
        $percentPriceStddev = (1 - abs($product1['priceStddev'] - $product2['priceStddev']) / $maxPriceStddev) * 100;

        if (($product1['productgroup_id'] != 0 && $product2['productgroup_id'] != 0)) {
            $similar_productgroup_id = ($product1['productgroup_id'] == $product2['productgroup_id']) ? 100 : 0;
        }



        $wightedSum = $weights['itemname'] + $weights['brandname'] +
                $weights['colorname'] + $weights['attributename'] + $weights['othername'] +
                $weights['pricemean'] + $weights['pricestddev'] + $weights['company_group'];
        

        if ($product1['itemName'] == '' && $product2['itemName'] == ''){
            $wightedSum -= $weights['itemname'];
            if($weights['itemname'] != 0)
                $comments[] = 'item weight is converted to 0 due to both being empty';
        }
        if ($product1['brandName'] == '' && $product2['brandName'] == ''){
            $wightedSum -= $weights['brandname'];
            if($weights['brandname'] != 0)
                $comments[] = 'brand weight is converted to 0 due to both being empty';
        }
        if ($product1['colorName'] == '' && $product2['colorName'] == ''){
            $wightedSum -= $weights['colorname'];
            if($weights['colorname'] != 0)
                $comments[] = 'color weight is converted to 0 due to both being empty';
        }
        
        /*handling the weight of other name*/
        if(($product1['itemName'] == '' && $product2['itemName'] == '')
                && ($product1['brandName'] == 0 && $product2['brandName'] == 0) 
                && ($product1['productgroup_id'] == 0 && $product2['productgroup_id'] == 0) && 
                $weights['othername'] != 0){
            $comments[] = 'other weight is considered due to no item, brand and group';
        }
        else{
            if($weights['othername'] != 0){
                $comments[] = 'other name is not considered, other weight = 0';
                $wightedSum -= $weights['othername'];
                $weights['othername'] = 0;
            }
        }
        /*end of handling the weight of other name*/
        
        if ($product1['attributeName'] == '' && $product2['attributeName'] == ''){
            $wightedSum -= $weights['attributename'];
            if($weights['attributename'] != 0)
                $comments[] = 'attribute weight is converted to 0 due to both being empty';
        }
        if ($product1['otherName'] == '' && $product2['otherName'] == ''){
            $wightedSum -= $weights['othername'];
            if($weights['othername'] != 0)
                $comments[] = 'other weight is converted to 0 due to both being empty';
     
        }
        if ($product1['productgroup_id'] == 0 && $product2['productgroup_id'] == 0){
            $wightedSum-=$weights['company_group'];
            if($weights['company_group'] != 0)
                $comments[] = 'company_group weight is converted to 0 due to both being empty';
        }
        if ($wightedSum == 0){
            $wightedSum = 1;
        }

        

        $similarity = $percentItem * $weights['itemname'] +
                $percentBrand * $weights['brandname'] +
                $percentColor * $weights['colorname'] +
                $percentAttribute * $weights['attributename'] +
                $percentOther * $weights['othername'] +
                $percentPriceMean * $weights['pricemean'] +
                $percentPriceStddev * $weights['pricestddev'] +
                $similar_productgroup_id * $weights['company_group'];

        
        $similarity /= $wightedSum;
        

        if ($showSimilarity) {
            $tableCalcDesc = array();
            $t = array();
            $t['featureName'] = 'Item Name';
            $t['sourceValue'] = $product1['itemName'];
            $t['targetValue'] = $product2['itemName'];
            $t['similarity'] = round($percentItem, 2);
            $t['weight'] = $weights['itemname'];
            $t['normalized_wight'] = round($weights['itemname'] / $wightedSum, 2);
            $t['wightedSimilarity'] = round($t['similarity'] * $t['normalized_wight'], 2);
            $tableCalcDesc[] = $t;

            $t['featureName'] = 'Company Group';
            $t['sourceValue'] = $product1['productgroup_id'];
            $t['targetValue'] = $product2['productgroup_id'];
            $t['similarity'] = round($similar_productgroup_id, 2);
            $t['weight'] = $weights['company_group'];
            $t['normalized_wight'] = round($weights['company_group'] / $wightedSum, 2);
            $t['wightedSimilarity'] = round($t['similarity'] * $t['normalized_wight'], 2);
            $tableCalcDesc[] = $t;

            $t['featureName'] = 'Brand Name';
            $t['sourceValue'] = $product1['brandName'];
            $t['targetValue'] = $product2['brandName'];
            $t['similarity'] = round($percentBrand, 2);
            $t['weight'] = $weights['brandname'];
            $t['normalized_wight'] = round($weights['brandname'] / $wightedSum, 2);
            $t['wightedSimilarity'] = round($t['similarity'] * $t['normalized_wight'], 2);
            $tableCalcDesc[] = $t;

            $t['featureName'] = 'Color Name';
            $t['sourceValue'] = $product1['colorName'];
            $t['targetValue'] = $product2['colorName'];
            $t['similarity'] = round($percentColor, 2);
            $t['weight'] = $weights['colorname'];
            $t['normalized_wight'] = round($weights['colorname'] / $wightedSum, 2);
            $t['wightedSimilarity'] = round($t['similarity'] * $t['normalized_wight'], 2);
            $tableCalcDesc[] = $t;

            $t['featureName'] = 'Attribute Name';
            $t['sourceValue'] = $product1['attributeName'];
            $t['targetValue'] = $product2['attributeName'];
            $t['similarity'] = round($percentAttribute, 2);
            $t['weight'] = $weights['attributename'];
            $t['normalized_wight'] = round($weights['attributename'] / $wightedSum, 2);
            $t['wightedSimilarity'] = round($t['similarity'] * $t['normalized_wight'], 2);
            $tableCalcDesc[] = $t;

            $t['featureName'] = 'Other Name';
            $t['sourceValue'] = $product1['otherName'];
            $t['targetValue'] = $product2['otherName'];
            $t['similarity'] = round($percentOther, 2);
            $t['weight'] = $weights['othername'];
            $t['normalized_wight'] = round($weights['othername'] / $wightedSum, 2);
            $t['wightedSimilarity'] = round($t['similarity'] * $t['normalized_wight'], 2);
            $tableCalcDesc[] = $t;

            $t['featureName'] = 'Mean Price';
            $t['sourceValue'] = $product1['priceMean'];
            $t['targetValue'] = $product2['priceMean'];
            $t['similarity'] = round($percentPriceMean, 2);
            $t['weight'] = $weights['pricemean'];
            $t['normalized_wight'] = round($weights['pricemean'] / $wightedSum, 2);
            $t['wightedSimilarity'] = round($t['similarity'] * $t['normalized_wight'], 2);
            $tableCalcDesc[] = $t;

            $t['featureName'] = 'Price StdDev';
            $t['sourceValue'] = $product1['priceStddev'];
            $t['targetValue'] = $product2['priceStddev'];
            $t['similarity'] = round($percentPriceStddev, 2);
            $t['weight'] = $weights['pricestddev'];
            $t['normalized_wight'] = round($weights['pricestddev'] / $wightedSum, 2);
            $t['wightedSimilarity'] = round($t['similarity'] * $t['normalized_wight'], 2);
            $tableCalcDesc[] = $t;

            $utility = new Utility();
            $sum = round(array_sum($utility->getValuesOfKey($tableCalcDesc, 'wightedSimilarity')), 2);
            $sum_w = round(array_sum($utility->getValuesOfKey($tableCalcDesc, 'weight')), 2);

            echo '<div>source: (' . $product1['productId'] . ') ' . $product1['name'] . '</div>';
            echo '<div>target: (' . $product2['productId'] . ') ' . $product2['name'] . '</div>';

            echo '<div style = "height:450px; width: 500px; overflow:scroll; margin-top:30px;">';
            echo '<table border="2px;">';
            echo '</tr>';
            $headNames = array('feature', 'source', 'target', 'sim', 'wight', 'norm_w', 'sim*norm_w');
            echo '<tr>';
            foreach ($headNames as $headName) {
                echo '<th>' . $headName . '</th>';
            }
            echo '</tr>';
            foreach ($tableCalcDesc as $row) {
                echo '<tr>';
                foreach ($row as $col) {
                    echo '<td>' . $col . '</td>';
                }
                echo '</tr>';
            }
            echo '</tr><th>Total</th><th colspan="3"></th>';
            echo '<th>'.$sum_w.'~'.$wightedSum.'</th>';
            echo '<th></th><th>' . $sum . '~' . round($similarity, 2) . '</th></tr>';
            echo '</table>';
            echo '</div>';
            
            foreach($comments as $comment){
                echo '<div>'.$comment.'</div>';
            }
            
        }

        return $similarity;
        //echo "simi: sjnjsd " . $similarity . "<br>";
    }

}

?>
