<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once("../../lib/config.inc.php");
	require_once("../../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 
	 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");
	
	// get parameter
	$usereMail = clean($_GET['email']);

	$query = "select * from ".$db_prefix."users where mail = '".$usereMail."'";
	
	$result = $mysqli->query($query);
	
	if (mysqli_num_rows($result) == 1) {
		$userReult = $result->fetch_assoc();
		echo json_encode($userReult);
	} else {
		echo json_encode("");	
	}
	
	
?>