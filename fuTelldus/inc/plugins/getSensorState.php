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
	$sensorID = clean($_GET['sensor_id']);
	$user_id = clean($_GET['user_id']);

	header('Content-Type: text/javascript');
	
	$json = array();
	$returnParams = getPluginReturnParameter($sensorID);
	
	$returnValues = getCurrentVirtualSensorState($sensorID, $user_id);
	
	foreach (array_keys($returnValues) as $key) {
		$description = $returnParams[$key]['description'];
		$value = $returnValues[$key];
		if (isset($value) && strlen($value)>0) {
			$json[$description] = $value;
		}
	}

	echo json_encode($json);
	
// 	$json = "";
// 	foreach (array_keys($returnValues) as $key) {
// 		$description = $returnParams[$key]['description'];
// 		$value = $returnValues[$key];
// 		if (isset($value) && strlen($value)>0) {
// 			echo $description.": ".$value."<br />";
// 		}
// 	}
?>

