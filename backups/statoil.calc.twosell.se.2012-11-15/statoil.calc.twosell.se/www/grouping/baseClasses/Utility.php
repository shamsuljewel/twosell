<?php

//error_reporting(0);
class Utility {

    function Utility() {
        
    }

    public function multipleKeyArrySort($simPriceDisc, $key, $order = false) {
        $keys = array_keys($simPriceDisc);
        for ($i = 0; $i < sizeOf($simPriceDisc) - 1; $i++) {
            for ($j = $i + 1; $j < sizeOf($simPriceDisc); $j++) {
                if ($simPriceDisc[$keys[$i]][$key] > $simPriceDisc[$keys[$j]][$key] && !$order) {
                    $t = $simPriceDisc[$keys[$i]];
                    $simPriceDisc[$keys[$i]] = $simPriceDisc[$keys[$j]];
                    $simPriceDisc[$keys[$j]] = $t;
                }
                if ($simPriceDisc[$keys[$i]][$key] < $simPriceDisc[$keys[$j]][$key] && $order) {
                    $t = $simPriceDisc[$keys[$i]];
                    $simPriceDisc[$keys[$i]] = $simPriceDisc[$keys[$j]];
                    $simPriceDisc[$keys[$j]] = $t;
                }
            }
        }

        return $simPriceDisc;
    }

    public function getValuesOfKey($arrayData, $key) {
        $values = array();
        foreach ($arrayData as $singleData) {
            $values[] = $singleData[$key];
        }
        return $values;
    }

    public function simLike($like, $fieldRows) {
        $fieldRows = array_unique($fieldRows);
        //print_r($fieldRows);exit();
        $similarItem = array();
        $like = str_replace('/', '\/', $like);
        $pattern = '/\b' . $like . '\b/i';
        //echo $pattern;
        foreach ($fieldRows as $fieldRow) {
            if (preg_match($pattern, $fieldRow)) {
                //print_r($fieldRow);//exit();
                $similarItem[] = $fieldRow;
            }
        }
        return $similarItem;
    }

    public function singleKeyArrayToStr($simDecS, $what, $unit, $i) {
        $strSimDes = "";
        foreach ($simDecS as $key => $val) {
            $strSimDes .= "<div><u style = 'cursor:pointer;'><span onClick='setRecomandationBrand(this," . $i . ")'> ";
            $strSimDes .= $key . "</span></u>&nbsp;&nbsp;&nbsp;&nbsp;(" . $what . ":" . round($val, 2) . ")" . $unit . "</div>";
        }
        return $strSimDes;
    }

    public function combination($itemCandidates, $totalWord) {
        $itemCandidatesTemp = array();

        for ($i = 0; $i < $totalWord; $i++) {
            for ($j = $i + 1; $j < $totalWord; $j++) {
                for ($k = $j + 1; $k < $totalWord; $k++) {
                    for ($m = $k + 1; $m < $totalWord; $m++) {
                        for ($n = $m + 1; $n < $totalWord; $n++) {
                            $itemCandidatesTemp[] = $itemCandidates[$i] . $itemCandidates[$j] . $itemCandidates[$k] . $itemCandidates[$m] . $itemCandidates[$n];
                        }
                    }
                }
            }
        }


        for ($i = 0; $i < $totalWord; $i++) {
            for ($j = $i + 1; $j < $totalWord; $j++) {
                for ($k = $j + 1; $k < $totalWord; $k++) {
                    for ($m = $k + 1; $m < $totalWord; $m++) {
                        $itemCandidatesTemp[] = $itemCandidates[$i] . $itemCandidates[$j] . $itemCandidates[$k] . $itemCandidates[$m];
                    }
                }
            }
        }

        for ($i = 0; $i < $totalWord; $i++) {
            for ($j = $i + 1; $j < $totalWord; $j++) {
                for ($k = $j + 1; $k < $totalWord; $k++) {
                    $itemCandidatesTemp[] = $itemCandidates[$i] . $itemCandidates[$j] . $itemCandidates[$k];
                }
            }
        }


        for ($i = 0; $i < $totalWord; $i++) {
            for ($j = $i + 1; $j < $totalWord; $j++) {
                $itemCandidatesTemp[] = $itemCandidates[$i] . $itemCandidates[$j];
            }
        }

        return $itemCandidatesTemp;
    }

    public function itemCollector($string, $itemNames) {
        $words = explode(' ', $string);
        $itemCandidates = $words;

        $totalWord = sizeof($itemCandidates);
        //print_r($itemCandidates);

        $itemCandidatesASC = $this->combination($itemCandidates, $totalWord);

        $itemCandidatesRev = array_reverse($itemCandidates);
        //print_r($itemCandidatesRev);

        $itemCandidatesDESC = $this->combination($itemCandidatesRev, $totalWord);
        $itemCandidates = array_merge($itemCandidates, $itemCandidatesASC, $itemCandidatesDESC);

        $item = "";
        foreach ($itemCandidates as $itemCandidate) {
            if (in_array($itemCandidate, $itemNames)) {
                $item = $itemCandidate;
            }
        }

        return $item;
    }

    public function onTaging($string, $itemNames, $brandNames, $colorNames, $attributes) {
        $databaseUtility = new DatabaseUtility();
        $flagItem = true;
        $flagAtt = true;

        $termTag = array();
        $termTag['item'] = '';
        //$word=trim($this->onTerm($string));
        $words = explode(' ', $string);
        //print_r($words);
        foreach ($words as $word) {
            $qSysnonym = "Select ItemName From tsl_synonyms_items where ItemSynoyms = '" . strtolower($this->onTerm($word)) . "'";
            //echo $qSysnonym . "<br>";
            $SynWords = $databaseUtility->getFieldData($qSysnonym, 'ItemName');
            foreach ($SynWords as $SynWord) {
                //echo "s´yn" .$SynWord . "8in <br>";
                if ($SynWord != '') {
                    $word = trim($SynWord);
                    //echo $word . "in <br>";
                }
            }
            $word = $this->onTerm($word);
            //if (in_array($this->onTerm($word), $itemNames) && ($flagItem == true)) {
            if (in_array($termTag['item'] . $word, $itemNames)) {
                $termTag['item'] = $termTag['item'] . $word;
                //$flagItem = false;
            } elseif (in_array($word, $brandNames)) {
                $termTag['brand'] = $word;
            } elseif (in_array($word, $colorNames)) {
                $termTag['color'] = $word;
            } elseif (in_array($word, $attributes)) {
                //echo $this->onTerm($word) . '<br>';
                if (!key_exists('attributes', $termTag))
                    $termTag['attributes'] = '';
                $termTag['attributes'].=' ' . $word;
            }
            else {
                if (!key_exists('other', $termTag))
                    $termTag['other'] = ' ';
                $termTag['other'].=' ' . $word;
            }
            if (key_exists('other', $termTag))
                $termTag['other'] = trim($termTag['other']);

            if (preg_match('/[0-9]+w/', $this->onTerm($word))) {
                if (!key_exists('attributes', $termTag))
                    $termTag['attributes'] = ' ';
                $termTag['attributes'].=' ' . $this->onTerm($word);
            }
            if (preg_match('/[0-9]+v/', $this->onTerm($word))) {
                if (!key_exists('attributes', $termTag))
                    $termTag['attributes'] = ' ';
                $termTag['attributes'].=' ' . $this->onTerm($word);
            }
            //if (preg_match('/[0-9]+vxl/', $this->onTerm($word))) {
            //  $termTag['item']='cycle';
            //}
            if (key_exists('attributes', $termTag))
                $termTag['attributes'] = trim($termTag['attributes']);
        }

        if (!key_exists('item', $termTag) || $termTag['item'] == '')
            $termTag['item'] = '';
        if (!key_exists('brand', $termTag) || $termTag['brand'] == '')
            $termTag['brand'] = '';
        if (!key_exists('color', $termTag) || $termTag['color'] == '')
            $termTag['color'] = '';
        if (!key_exists('attributes', $termTag) || $termTag['attributes'] == '')
            $termTag['attributes'] = '';
        if (!key_exists('other', $termTag) || $termTag['other'] == '')
            $termTag['other'] = '';
        return $termTag;
    }

    public function onTerm($string) {
        $expreS = array('"', ',', '*', '?', '(', ')');
        $ou = str_replace("'", ' ', trim($string));
        foreach ($expreS as $expre) {
            $ou = str_replace($expre, ' ', trim($ou));
        }
        return $ou;
    }

    public function onSweChar($string) {
        $ou = str_replace('å', '&aring;', trim($string));
        $ou = str_replace('ä', '&auml;', trim($ou));
        $ou = str_replace('ö', '&ouml;', trim($ou));
        return $ou;
    }

    public function mappingArray($array, $key, $value = null) {
        $mappingArray = array();
        if (isset($value)) {
            foreach ($array as $elements) {
                $mappingArray[$elements[$key]] = $elements[$value];
            }
        } else {
            foreach ($array as $elements) {
                $mappingArray[$elements[$key]] = $elements;
            }
        }
        return $mappingArray;
    }

    function show_status($done, $total, $size=30) {

        static $start_time;

        // if we go over our bound, just ignore it
        if ($done > $total)
            return;

        if (empty($start_time))
            $start_time = time();
        $now = time();

        $perc = (double) ($done / $total);

        $bar = floor($perc * $size);

        $status_bar = "\r[";
        $status_bar.=str_repeat("=", $bar);
        if ($bar < $size) {
            $status_bar.=">";
            $status_bar.=str_repeat(" ", $size - $bar);
        } else {
            $status_bar.="=";
        }

        $disp = number_format($perc * 100, 0);

        $status_bar.="] $disp%  $done/$total";

        $rate = ($now - $start_time) / $done;
        $left = $total - $done;
        $eta = round($rate * $left, 2);

        $elapsed = $now - $start_time;

        $status_bar.= " remaining: " . number_format($eta) . " sec.  elapsed: " . number_format($elapsed) . " sec.";

        echo "$status_bar  ";

        flush();

        // when done, send a newline
        if ($done == $total) {
            echo "\n";
        }
    }

    function progressBar($msg, $i, $total, $lastPercent) {
        if ($i == 1)
            print $msg . " 00%";
        else {
            $percent = round($i * 100 / $total);
            if ($lastPercent == $percent)
                return $lastPercent;
            $lastPercent = $percent;

            $percent = '' . $percent;
            if (strlen($percent) == 1)
                print "\010\010";
            else if (strlen($percent) == 2)
                print "\010\010\010";
            else if (strlen($percent) == 3)
                print "\010\010\010\010";
            else
                ;
            print $percent . "%";
        }
        return $lastPercent;
    }

    function findMemberProducts($groups, $j, $col1, $col2) {
        $srtingInc = "";
        $srtingExc = "";
        $query = "";
        // echo $col1 . "dfdf" . $col2;

        if ($groups[$j][$col1] != '') {
            $wordsExc = explode(';', strtolower($groups[$j][$col1]));
            
           // echo 'fddsfds' . substr($wordsExc[0], 0, 2) . '<br>';
            if (strcasecmp (substr($wordsExc[0], 0, 2), 'G#')==0){
                $concat=" concat(lower(title), ' G#', lower(screen_text), 'G') ";
            }else{
                $concat=" lower(title) ";
            }
            
            $srtingExc = " and " . $concat . " not like '%" . $wordsExc[0] . "%'";
            for ($m = 1; $m < sizeof($wordsExc); $m++) {
                $srtingExc = $srtingExc . " and " . $concat . " not like '%" . $wordsExc[$m] . "%'";
            }
        }

        if ($groups[$j][$col2] != '') {
            $user = strpbrk(strtolower($groups[$j][$col2]), '&');
            if ($user != '') {
                $opS = '&';
                $op = ' and ';
            } else {
                $opS = ';';
                $op = ' or ';
            }
            $wordsInc = explode($opS, strtolower($groups[$j][$col2]));
            //     echo 'inc dfd' . substr($wordsInc[0], 0, 2). '<br>';
           // echo 'inc' . strcasecmp (substr($wordsInc[0], 0, 2), 'G#'). '<br>';
            if (strcasecmp (substr($wordsInc[0], 0, 2), 'G#')==0){
                $concat=" concat(lower(title), ' G#', lower(screen_text), 'G') ";
            }else{
                $concat=" lower(title) ";
            }
            $srtingInc = " and ( " . $concat . " like '%" . $wordsInc[0] . "%'";
            for ($n = 1; $n < sizeof($wordsInc); $n++) {
                $srtingInc = $srtingInc . $op . $concat . "like '%" . $wordsInc[$n] . "%'";
            }
            $srtingInc = $srtingInc . ")";
        }
       //echo "select a.id as id, lower(a.title)as title from twosell_product as a, tsln_price_product as b  where a.id = b.product_id and b.max_price between " . $groups[$j]['price_min'] . " and " . $groups[$j]['price_max'] . $srtingExc . $srtingInc;

        $query = "select a.id as id, lower(a.title)as title from twosell_product as a, tsln_price_product as b  where a.id = b.product_id and b.max_price between " . $groups[$j]['price_min'] . " and " . $groups[$j]['price_max'] . $srtingExc . $srtingInc;

        return $query;
    }

}

?>