<?php
namespace telldus\telldus_device;
/*
 Needs:

*/

	function activateHook() {
		// return an array with the description, and all fields needed for determining to correct sensor state
		return getConfigArray();
	}
	
	function disableHook() {
		// nothing todo for this plugin
	}
	
	function updateHook() {
		return getConfigArray();
	}
	
	function getConfigArray() {
		return $configs = array(
				array('key' => 'telldus_device_id', 
 						'type' => 'callBackMethodReturnList;listExistingDevices',
						'description' => 'Telldus device'), 
				array('key' => 'telldus_public_key',
						'type' => 'text',
						'config_type' => 'user',
						'description' => 'Public Key'),
				array('key' => 'telldus_private_key',
						'type' => 'text',
						'config_type' => 'user',
						'description' => 'Private Key'),
				array('key' => 'telldus_token',
						'type' => 'text',
						'config_type' => 'user',
						'description' => 'Token'),
				array('key' => 'telldus_token_secret',
						'type' => 'text',
						'config_type' => 'user',
						'description' => 'Token secret')
		);
	}
	
	/*
	 * Return a list of existing sensors from telldus via the API.
	 * The $params array parameter includes the API keys set form the user configuration
	 */
	function listExistingDevices($params) {
		$consumer = connectToTelldus($params);
		$requestParams = array();
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/devices/list', $requestParams, 'GET');

		// Get XML and create array with SimpleXMLElement
		$xmlData = $response->getBody();

		$xml = new \SimpleXMLElement($xmlData);

		$returnVal = array();
		foreach($xml->device as $sensorData) {
			
			$deviceID = trim($sensorData['id']);
			$name = trim($sensorData['name']);
			$state = trim($sensorData['state']);
			$statevalue = trim($sensorData['statevalue']);
			$methods = trim($sensorData['methods']);
			$client = trim($sensorData['client']);
			$clientName = trim($sensorData['clientName']);
			$online = trim($sensorData['online']);
			$editable = trim($sensorData['editable']);
			
			array_push($returnVal, array('id' => $deviceID, 'name' => $name));
		}
		
 		return $returnVal;
	}
	
	
	function connectToTelldus($params) {
		require_once 'HTTP/OAuth/Consumer.php';
		
		$public_key = $params['telldus_public_key']['value'];
		$private_key = $params['telldus_private_key']['value'];
		$token = $params['telldus_token']['value'];
		$token_secret =  $params['telldus_token_secret']['value'];
		
		
		if (!defined("URL")) define('URL', 'http://api.telldus.com'); //https should be used in production!
		if (!defined("REQUEST_TOKEN")) define('REQUEST_TOKEN', constant('URL').'/oauth/requestToken');
		if (!defined("AUTHORIZE_TOKEN")) define('AUTHORIZE_TOKEN', constant('URL').'/oauth/authorize');
		if (!defined("ACCESS_TOKEN")) define('ACCESS_TOKEN', constant('URL').'/oauth/accessToken');
		if (!defined("REQUEST_URI")) define('REQUEST_URI', constant('URL').'/xml');
		
		if (!defined("BASE_URL")) define('BASE_URL', 'http://'.$_SERVER["SERVER_NAME"].dirname($_SERVER['REQUEST_URI']));
		
// 		define('TELLSTICK_TURNON', 1);
// 		define('TELLSTICK_TURNOFF', 2);
		$consumer = new \HTTP_OAuth_Consumer($public_key, $private_key, $token, $token_secret);
		return $consumer;
	}
	
	
	function switchOn($parameter, $deviceID) {
		$consumer = connectToTelldus($parameter);
		$requestParams = array('id'=> $parameter['telldus_device_id']['value']);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/turnOn', $requestParams, 'GET');
		
		// Get XML and create array with SimpleXMLElement
		$xmlData = $response->getBody();
		$xml = new \SimpleXMLElement($xmlData);
		
		$state = $xml->status;
		
		if (strcasecmp($state, "success")) {
			return 1;
		} else {
			return 0;
		}
	}
	
	
	// should return 1 if the device is on and 0 if off
	function getStatus($parameter, $deviceID) {			
		$consumer = connectToTelldus($parameter);
		$requestParams = array('id'=> $parameter['telldus_device_id']['value'],
					'supportedMethods'=> 1023);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/info', $requestParams, 'GET');
		// Get XML and create array with SimpleXMLElement
		$xmlData = $response->getBody();
		$xml = new \SimpleXMLElement($xmlData);
		
		$state = $xml->state;
		if ($state == 1) {
			return 1;
		} else if ($state == 2) {
			return 0;
		}
		
		return -1;
	}
	
	// contains the logic to turn the device off
	// return 1 on success, 0 on error
	function switchOff($parameter, $deviceID) {
		$consumer = connectToTelldus($parameter);
		$requestParams = array('id'=> $parameter['telldus_device_id']['value']);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/turnOff', $requestParams, 'GET');
		
		// Get XML and create array with SimpleXMLElement
		$xmlData = $response->getBody();
		$xml = new \SimpleXMLElement($xmlData);
		
		$state = $xml->status;
		
		if (strcasecmp($state, "success")) {
			return 1;
		} else {
			return 0;
		}
	}

?>
