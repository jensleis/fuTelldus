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
	
	// if only sync, do it now and return
	$action = clean($_GET['action']);
	if ($action=='synchronizeTelldus') {
		$user_id=clean($_GET['user_id']);
		synchronizeWithTelldus($user_id);
		exit();
	}
	
	// get parameter
	$deviceType = clean($_GET['type']);
	$deviceID = clean($_GET['id']);
	$period = clean($_GET['period']);


	
	header('Content-Type: text/javascript');
	$result = -1;
	if ($period=='last'){
		if ($deviceType=='virtual') {
			$result = getLastVirtualDeviceStatus($deviceID);
		} else if ($deviceType=='device') {
			$result = getLastValueFromDBForTelldusDevice($deviceID);
		}
	} else if ($period=='current') {
		if ($deviceType=='virtual') {
			$result = getCurrentVirtualDeviceState($deviceID);
		} else if ($deviceType=='device') {
			$result = getLastValueFromDBForTelldusDevice($deviceID);
		}
	}
	echo $result;
	
	function getLastValueFromDBForTelldusDevice($deviceID) {
		global $mysqli;
		global $db_prefix;	
		
		$query = "SELECT * FROM ".$db_prefix."devices WHERE type='device' AND device_id='".$deviceID."'";
		$result = $mysqli->query($query);
		$return = $result->fetch_assoc()['state'];
		
		if ($return>=0 and $return<2){
			return 1;
		} else {
			return 0;
		}
	}
	
	function synchronizeWithTelldus($user_id) {
		global $mysqli;
		global $db_prefix;	
		
		require_once '../../HTTP/OAuth/Consumer.php';
		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		$params = array('supportedMethods'=> 1023);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/devices/list', $params, 'GET');

		$xmlString = $response->getBody();
		echo $xmlString;
		$xmldata = new SimpleXMLElement($xmlString);

		/* Store devices in DB
		--------------------------------------------------------------------------- */
		foreach($xmldata->device as $deviceData) {
			
			$deviceID = trim($deviceData['id']);
			$name = trim($deviceData['name']);
			$state = trim($deviceData['state']);
			$statevalue = trim($deviceData['statevalue']);
			$methods = trim($deviceData['methods']);
			$type = trim($deviceData['type']);
			$client = trim($deviceData['client']);
			$clientName = trim($deviceData['clientName']);
			$online = trim($deviceData['online']);
			$editable = trim($deviceData['editable']);


			// Use REPLACE INTO to overwrite with device_id as primary
			$query = "REPLACE INTO ".$db_prefix."devices SET 
						device_id='".$deviceID."', 
						user_id='".$user_id."', 
						name='".$name."', 
						state='".$state."', 
						statevalue='".$statevalue."', 
						methods='".$methods."', 
						type='".$type."', 
						client='".$client."',  
						clientname='".$clientName."',  
						online='".$online."',  
						editable='".$editable."'";
			$result = $mysqli->query($query);
		}
	}
?>

