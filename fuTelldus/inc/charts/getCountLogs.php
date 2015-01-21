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
	$objectID = clean($_GET['id']);
	$objectType = clean($_GET['type']);

	header('Content-Type: text/javascript');
	$result = -1;
	if ($objectType=='device') {
		$result = getCountDeviceLog($objectID);
	} else if ($objectType=='virtual') {
		$result = getCountVirtualSensorLog($objectID);
	}
	echo $result;
	
	function getCountDeviceLog($deviceID) {
		global $mysqli;
		global $db_prefix;
	
		$query = "select count(dl.time_updated)-1 as rows from ".$db_prefix."virtual_devices_log dl where dl.device_id='".$deviceID."'";
		$result = $mysqli->query($query);
		$count = $result->fetch_assoc()['rows'];
		return $count;
	}
	
	function getCountVirtualSensorLog($sensorID) {
		global $mysqli;
		global $db_prefix;
	
		$query = "select count(vsl.time_updated)-1 as rows from ".$db_prefix."virtual_sensors_log vsl where vsl.sensor_id='".$sensorID."'";
		$result = $mysqli->query($query);
		$count = $result->fetch_assoc()['rows'];
		return $count;
	}
?>