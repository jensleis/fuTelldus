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
	
	$pluginID = clean($_GET['type']);
	$callBackMethod = clean($_GET['method']);
	$userid = clean($_GET['user_id']);
	
	$pluginParams = getPluginUserConfigArrayWithValues($userid, $pluginID);
	
	// find the script
	$phpscript = getPluginPathToPluginID($pluginID);
	$nameSpace = includePlugin($phpscript."/index.php");
	$funcGroup = $nameSpace."\\$callBackMethod";

	// if the method exists, call the method
	if (function_exists($funcGroup)) {
		$returnValues = @$funcGroup($pluginParams);
		echo json_encode($returnValues);
	}

?>