<?php
	include('common.php');

    $connect = mysqli_connect($host_name, $user_name, $password, $database);
	if (!$connection) {
  		die("Could not connect to the database: <br/>".mysql_error());
	}
	$db_select=mysql_select_db($db_database);
	if (!$db_select) {
  		die("Could not select the database: <br/>".mysql_error());
	}
?>
  	
