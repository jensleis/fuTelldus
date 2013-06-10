<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once("../../lib/config.inc.php");
	require_once("../../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	
	// get parameter
	$id = clean($_GET['id']);
	$type = clean($_GET['type']);
	$pos = clean($_GET['pos']);
	
	/* Generate the chart
	-------------------------------------------------------*/
	if ($type == "sensor") {
		updatePositionSensor($id, $pos);
	}
	
	if ($type == "virtual") {
		updatePositionVirtualSensor($id, $pos);
	}
	
	function updatePositionVirtualSensor($id, $pos) {
		global $mysqli;
		global $db_prefix;
		
		$query = "UPDATE ".$db_prefix."virtual_sensors SET show_in_main=".$pos." WHERE id=".$id."";
		echo $query;
		$mysqli->query($query);
	}
	
	function updatePositionSensor($id, $pos) {
		global $mysqli;
		global $db_prefix;
	
		$query = "UPDATE ".$db_prefix."sensors SET show_in_main=".$pos." WHERE sensor_id=".$id."";
		$mysqli->query($query);
	}
	
	
	

?>
