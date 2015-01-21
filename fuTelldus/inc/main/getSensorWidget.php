<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once("../../lib/config.inc.php");
	require_once("../../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 
	mysqli_set_charset($mysqli, "utf8");
	
	// get parameter
	$id = clean($_POST['id']);
	
	$actualState = getCurrentVirtualSensorStateWidet($id);
	
	echo $actualState;	

?>
