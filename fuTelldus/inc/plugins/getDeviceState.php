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
	$deviceID = clean($_GET['id']);
	$period = clean($_GET['period']);
	$user_id = clean($_GET['user_id']);

	
	header('Content-Type: text/javascript');
	$result = -1;
	if ($period=='last'){
		$result = getLastVirtualDeviceStatus($deviceID);
	} else if ($period=='current') {
		$result = getCurrentVirtualDeviceState($deviceID, $user_id);
	}
	echo $result;
	
?>

