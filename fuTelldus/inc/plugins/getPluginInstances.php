<?php

	require("../../lib/config.inc.php");
	require("../../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 
	 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");
	
	$type = clean($_GET['type']);
	$plugin_name = clean($_GET['plugin_name']);
	$user_id = clean($_GET['user_id']);
	
	//select in instance id and instance description
	if ($type=='sensor'){
		$query = "select vd.id, vd.description from ".$db_prefix."plugins p, ".$db_prefix."virtual_sensors vd
				where vd.sensor_type = p.type_int
				and p.plugin_path like '%".$plugin_name."'
				and vd.user_id=".$user_id."";
	} else if ($type=='device') {
		$query = "select vd.id, vd.description from ".$db_prefix."plugins p, ".$db_prefix."virtual_devices vd
				where vd.plugin_id = p.type_int
				and p.plugin_path like '%".$plugin_name."'
				and vd.user_id=".$user_id."";
	}
	$result = $mysqli->query($query);
	
	$rows = array();
	while($r = $result->fetch_assoc()) {
		$rows[] = $r;
	}
	echo json_encode($rows);


?>