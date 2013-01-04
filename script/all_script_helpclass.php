<?php


/*
 * Description of all-script-helpclass
 *
 * @author Shamsul
 */
class all_script_helpclass{
    
    protected $data_array = array();
    function OpenfileReturnArray($file_url){		
        $handle = @fopen("$file_url", "r");
        if ($handle != FALSE) {
            $line = 0;
            while ($buffer = fgets($handle)) {
                $this->data_array[++$line] = trim($buffer);
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
        else {
            echo "File opening problem";
        }
        return $this->data_array;
    }
}
?>
