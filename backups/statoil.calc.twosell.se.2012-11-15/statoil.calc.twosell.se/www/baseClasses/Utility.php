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
        //print_r($words);
        $totalWord = sizeof($itemCandidates);
        $itemCandidatesASC = $this->combination($itemCandidates, $totalWord);
        $itemCandidatesRev = array_reverse($itemCandidates);
        //print_r($itemCandidatesRev);

        $itemCandidatesDESC = $this->combination($itemCandidatesRev, $totalWord);
        $itemCandidates = array_merge($itemCandidates, $itemCandidatesASC, $itemCandidatesDESC);
        //print_r($itemNames);

        $item = "";
        foreach ($itemCandidates as $itemCandidate) {
           
            if (in_array($itemCandidate, $itemNames)) {
                // echo $itemCandidate . '<br>';
                $item = $itemCandidate;
            }
        }
        //print_r($item);
        return $item;
    }

    public function onTaging($string, $itemNames, $brandNames, $colorNames, $attributes) {
        $databaseUtility = new DatabaseUtility();
        $flagItem = true;
        $flagAtt = true;

        $termTag = array();
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
            if (in_array($this->onTerm($word), $itemNames) && ($flagItem == true)) {
                $termTag['item'] = $this->onTerm($word);
                $flagItem = false;
            } elseif (in_array($this->onTerm($word), $brandNames)) {
                $termTag['brand'] = $this->onTerm($word);
            } elseif (in_array($this->onTerm($word), $colorNames)) {
                $termTag['color'] = $this->onTerm($word);
            } elseif (in_array($this->onTerm($word), $attributes)) {
                //echo $this->onTerm($word) . '<br>';
                $termTag['attributes'].=' ' . $this->onTerm($word);
            }
            else
                $termTag['other'].=' ' . $word;
            trim($termTag['other']);

            if (preg_match('/[0-9]+w/', $this->onTerm($word))) {
                $termTag['attributes'].=' ' . $this->onTerm($word);
            }
            if (preg_match('/[0-9]+v/', $this->onTerm($word))) {
                $termTag['attributes'].=' ' . $this->onTerm($word);
            }
            //if (preg_match('/[0-9]+vxl/', $this->onTerm($word))) {
            //  $termTag['item']='cycle';
            //}
            trim($termTag['attributes']);
        }

        if ($termTag['item'] == '')
            $termTag['item'] = '';
        if ($termTag['brand'] == '')
            $termTag['brand'] = '';
        if ($termTag['color'] == '')
            $termTag['color'] = '';
        if ($termTag['attributes'] == '')
            $termTag['attributes'] = '';
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

}

?>