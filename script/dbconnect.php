<?php //Connect to database from here
	$link = mysql_connect('localhost', 'root', 'jhgtfhj'); 
        //$link = mysql_connect('localhost', 'shamsul', 'whim80(codex'); 
	if (!$link) {
		die('Could not connect to database: ' . mysql_error());
	}
	//select the database | Change the name of database from here
	mysql_select_db('statistics_test', $link); 
        //mysql_select_db('statistics_test'); 
?>		