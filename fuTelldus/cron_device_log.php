<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require("lib/base.inc.php");
	
	ob_start();
	session_start();
	$_SESSION['batch']='cron_device_log';

	/* Connect to database
	--------------------------------------------------------------------------- */

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");

	/* Get oAuth class
	--------------------------------------------------------------------------- */
	require_once 'HTTP/OAuth/Consumer.php';


	/* ##################################################################################################################### */
	/* ######################################## SCRIPT RUNS BELOW THIS LINE ################################################ */
	/* ##################################################################################################################### */


	/* Find users
	--------------------------------------------------------------------------- */
	$query = "SELECT * FROM ".$db_prefix."users";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_array()) {


    	/* Connect to telldus
		--------------------------------------------------------------------------- */
    	$queryDevices = "SELECT * FROM ".$db_prefix."virtual_devices WHERE user_id='{$row['user_id']}'";
  		$resultDevicesSet = $mysqli->query($queryDevices);
  		

  		while ($device = $resultDevicesSet->fetch_array()) {
//   			echo "<pre>";
//   			print_r($device);
//   			echo "</pre>";
  			
			$id = $device['id'];
			$description = $device['description'];
			$pluginID = $device['plugin_id'];
// 			$lastStatus = $device['last_status'];
			$online = $device['online'];
			
			// get last state
			$lastStatus = -1;
			$queryLastState = "select status from ".$db_prefix."virtual_devices_log where device_id='".$id."' order by time_updated desc LIMIT 1";
			$resultLastState = $mysqli->query($queryLastState);
			if (mysqli_num_rows($resultLastState) == 1) {
				$lastStatus = $resultLastState->fetch_assoc()['status'];
			}
// 			echo "selectedLastState ... ";
			
			$currentState = getCurrentVirtualDeviceState($id, $row['user_id']);
			// only update if value changed
// 			echo "selectedCurrentState ... ";
// 			echo $description." --- ".$lastStatus." --- ".$currentState;
			
			if ($currentState != $lastStatus) {
				// Add values to DB
				$queryInsert = "REPLACE INTO ".$db_prefix."virtual_devices_log SET
								device_id='". $id ."',
								time_updated='". time() ."',
								status='". $currentState ."'";
				$resultInsert = $mysqli->query($queryInsert);
			}
			
  		}

    } //end-while-users

//     session_destroy(); 
?>