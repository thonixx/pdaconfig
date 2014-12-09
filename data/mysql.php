<?php
	
	/* PDAconfig
	 * 
	 * scripted by Michael Tanner for Schleuniger Montagen AG
	 * www.white-tiger.ch
	 * www.schleuniger-montagen.ch
	 */
	
	// settings for mysql connection
	
	include "mysql_data.php";
	
	mysql_connect($server, $user, $pass) or die('Connection to database failed');
	mysql_select_db($database) or die('DB inexistent');
	
?>
