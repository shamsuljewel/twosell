<?php

$linkOnline = mysql_connect('innodb.hostit.se', 'twosell', 'kdjiu7474747uyryr7yr'); 
        //$link = mysql_connect('localhost', 'shamsul', 'whim80(codex'); 
	if (!$linkOnline) {
		die('Could not connect to database: ' . mysql_error());
	}
	//select the database | Change the name of database from here
	mysql_select_db('twosell1_statoil'); 
        //mysql_select_db('statistics_test');
        //echo "connected";
?>
