<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../lib/config.inc.php");
require_once("../../../lib/base.inc.php");

// Create DB-instance
$mysqli = new Mysqli($host, $username, $password, $db_name);

// Check for connection errors
if ($mysqli->connect_errno) {
	die('Connect Error: ' . $mysqli->connect_errno);
}

// Set DB charset
mysqli_set_charset($mysqli, "utf8");

if (isset($_GET['state']))
	$state = clean($_GET['state']);

if (isset($_GET['deviceID']))
	$deviceID = clean($_GET['deviceID']);

$path = getPluginPathToVDeviceId($deviceID);
$parameter = getPluginParametersWithoutUser($deviceID, 'device');
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
exit;

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
