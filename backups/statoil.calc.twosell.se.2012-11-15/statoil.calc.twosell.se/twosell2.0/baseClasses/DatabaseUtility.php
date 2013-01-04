<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DatabaseUtility
 *
 * @author siblee
 */
class DatabaseUtility {

    //put your code here
    private $conn = null;

    function DatabaseUtility() {
        $array_ini = parse_ini_file("config.ini",true);
        $database = $array_ini['database'];
        $this->conn = mysql_connect($database['host'], $database['user'], $database['pass']);
        if (!$this->conn) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db($database['database_name'], $this->conn);
        //mysql_set_charset('utf8',  $this->conn);
        //mysql_query('SET NAMES utf8'); 
    }

    public function getFieldsData($query, $fieldNames) {
        $result = mysql_query($query, $this->conn);
        $output = array();
        if ($result) {
            while ($row = mysql_fetch_object($result)) {
                $fields = array();
                foreach ($fieldNames as $fieldName) {
                    $t = str_replace('Ö', 'ö', strtolower(trim($row->$fieldName)));
                    $t = str_replace('Å', 'å', $t);
                    $t = str_replace('Ä', 'ä', $t);
                    $fields[$fieldName] = $t;
                }
                $output[] = $fields;
            }
        }
        return $output;
    }

    public function getFieldData($query, $fieldName) {
        $result = mysql_query($query, $this->conn);
        $output = array();
        if ($result) {
            while ($row = mysql_fetch_object($result)) {
                $t = str_replace('Ö', 'ö', strtolower(trim($row->$fieldName)));
                $t = str_replace('Å', 'å', $t);
                $t = str_replace('Ä', 'ä', $t);
                $output[] = $t;
            }
        }
        return $output;
    }

    public function executeQuery($query) {
        $result = mysql_query($query, $this->conn);
        if ($result) {
            return true;
        }
        //mysql_close();
        //mysql_close();
        //$myFile = "logFailQuery.log";
        //$fh = fopen($myFile, 'a') or die("can't open file");
        //fwrite($fh, $query);
        //fclose($fh);
        return false;
    }

    public function close() {
        mysql_close();
    }

}

?>
