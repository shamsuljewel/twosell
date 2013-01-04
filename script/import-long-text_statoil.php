<?php
/*
 * this script will take the .tab file from the localhost same as script folder and import the data of
 * article num and long_text into the database long_text table
 */
//connect to the statistics_database
include 'dbconnect.php';

error_reporting(E_ALL); 
// error log ON
ini_set('log_errors','1'); 
// display error NO
ini_set('display_errors','0'); 
// set the location of this script error log file
//$root = $_SERVER['DOCUMENT_ROOT']."/twosell";
//ini_set('error_log', 'c:/xampp/htdocs/twosell/error_log/import_long_text.log');
ini_set('error_log', '/home/twosell/data/admin.twosell.se/error_log/import_long_text.log');
set_error_handler("customError");
/*
 * error number, error message, error file name and the line number
 * after then kill the script
 */
function customError($errno, $errstr, $errfile, $errline){
  error_log("Error: [$errno] $errstr on $errfile at line $errline", 0);
  die();
}
/*************************************************************************/
include 'all_script_helpclass.php';

$long_array = array();
$head = array();
$content = array();

$file_url = "Twosell_articles.tab";
$canonicalDb = "statoil_canonical";
$table_name = "long_text";
$replace_char = "'";

$all_script = new all_script_helpclass();
$long_array = $all_script->OpenfileReturnArray($file_url);
//print_r($long_array);
//echo count($long_array);
$create_table = "CREATE TABLE IF NOT EXISTS $canonicalDb.`$table_name`  (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`article_num` INT(10) NOT NULL,
	`long_text` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `article_num` (`article_num`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;";
mysql_query($create_table) or trigger_error(mysql_error());
for($line = 1; $line <= count($long_array); $line++){
//    echo $long_array[$line];
//    echo "<br />";
    if($line == 1){
        $head = explode("\t", trim($long_array[$line]));
        $article_pos = array_search('article_no', $head);
        $long_pos = array_search('nlu_text_long', $head);
//        echo $article_pos.", ".$long_pos."</r>";
        
    }else{
        $content = explode("\t", trim($long_array[$line]));
        $article_num = str_replace($replace_char, '', $content[$article_pos]);
        $long_text = str_replace($replace_char, '', $content[$long_pos]);
        
        $insert_q = "INSERT IGNORE INTO $canonicalDb.$table_name(article_num, long_text) VALUES('$article_num', '$long_text')";
//        echo $insert_q."<br />";
        mysql_query($insert_q) or trigger_error(mysql_error());
    }
}
//print_r($content);
$total_article = count($long_array)-1;
echo "Total Article Inserted: ".$total_article;
?>
