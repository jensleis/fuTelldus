<?php
	
	require("lib/base.inc.php");
	require("lib/auth.php");

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['userid'])) $userID = clean($_GET['userid']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);
	if (isset($_GET['state'])) $state = clean($_GET['state']);
	if (isset($_GET['btnID'])) $btnID = clean($_GET['btnID']);


	// get the plugin
	$path = getPluginPathToVDeviceId($getID);
	$parameter = getPluginParameters($getID, 'device', $userID);
	$nameSpace = includePlugin($path."/index.php");
	$funcOff = $nameSpace."\\switchOff";
	$funcOn = $nameSpace."\\switchOn";
	$funcState = $nameSpace."\\getStatus";


	// switch the device
	if ($state == "on") {
		$response = $funcOn($parameter, $getID);
		if ($response==1){
			// check until the state is reached
			echo checkState($funcState, 1, $parameter, $getID);
			exit();
		}

	}

	if ($state == "off") {
		$response = $funcOff($parameter, $getID);
		if ($response == 1){
			echo checkState($funcState, 0, $parameter, $getID);
			exit();
		}
	}
	
	echo "error";
	exit();
	
	
	// checks every 2nd second if the state is reached. Aborts after 150 tries (about 5 mins)
	function checkState($funcState, $toState, $parameter, $deviceID) {
		$maxOfflineChecks = 150;
		while (true) {
			// if maxOfflineChecks is 0, exit with error
			if ($maxOfflineChecks == 0) {
				return "timeout";
			}

			// get the current state
			$actStaue = $funcState($parameter, $deviceID);
			if($actStaue == $toState) {
				return "changed";
			} 
			
			sleep(2);
			$maxOfflineChecks = $maxOfflineChecks - 1;
		}	
	}

?>