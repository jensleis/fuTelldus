<?php
namespace telldus\telldus_sensor;
/*
 Needs:

*/

/*	$paras = array(
		'datapath' => '/cm160_data.db.txt', // path to SQLlite db
		'energy_consumption' => '', //return
		'voltage' => '', // text
	);
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
				array('key' => 'telldus_sensor_id', 
 						'type' => 'callBackMethodReturnList;listExistingSensors',
						'description' => 'Telldus sensor'), 
				array('key' => 'temperature',
						'type' => 'return',
						'description' => 'Temperature'),
				array('key' => 'humidity',
						'type' => 'return',
						'description' => 'Humidity'),
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
	function listExistingSensors($params) {
		$consumer = connectToTelldus($params);
		$requestParams = array();
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensors/list', $requestParams, 'GET');

		// Get XML and create array with SimpleXMLElement
		$xmlData = $response->getBody();
		$xml = new \SimpleXMLElement($xmlData);

		$returnVal = array();
		foreach($xml->sensor as $sensorData) {
			
			$sensorID = trim($sensorData['id']);
			$name = trim($sensorData['name']);
			$lastUpdate = trim($sensorData['lastUpdated']);
			$ignored = trim($sensorData['ignored']);
			$client = trim($sensorData['client']);
			$clientName = trim($sensorData['clientName']);
			$online = trim($sensorData['online']);
			$editable = trim($sensorData['editable']);
			
			array_push($returnVal, array('id' => $sensorID, 'name' => $name));
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
	
	

	// marks that the plugin is supporting charts
	// maybe it will transform the data before it will be shown in
	// UI
	function editChartData($virtualSensorID, $chartDataArray) {
// 		// round each value and convert it into Watt
// 		$newChartDataArray = array();
// 		while (list($returnKey, $returnValues) = each($chartDataArray)) { 
			
// 			//if returnKey == 'energy_consumption' --> convert and round
// 			if ($returnKey=='energy_consumption') {
// 				while (list($timestamp, $value) = each($returnValues)) { 
// 					$consumptionAmpere = round($value,2);
// 					$voltageConfig = getPluginConfigToKey($virtualSensorID, "voltage");
// 					$consumptionWatt = $consumptionAmpere * $voltageConfig;
// 					$newChartDataArray[$returnKey][$timestamp] = $consumptionWatt;
// 				}
// 			} else {
// 				$newChartDataArray[$returnKey][$timestamp] = $value;
// 			}
// 		}
		
		return $chartDataArray;
	}
	
	// chance to redefine the description and the suffix for the axis
	// like they will shown on the UI
	// --> iterate over the array, included is another array, keys:
	// 0 --> position, don't change
	// 1 --> description
	// 2 --> suffix
	function overwriteChartAxisDefinition($axisDefinition) {
		$newAxisDefinition = array();
		while (list($value_key, $configArray) = each($axisDefinition)) { 
			if ($value_key == 'temperature') {
				$configArray[2] = "\u00B0C";
			}
			if ($value_key == 'humidity') {
				$configArray[2] = "%";
			}
			$newAxisDefinition[$value_key] = $configArray;
		}
		return $newAxisDefinition;
	}
	
	function getDashBoardWidget($lastLogValues, $virtualSensorID) {
		$myPath = getPluginPathToVSensorId($virtualSensorID);
	
		$temperature = $lastLogValues['temperature'];
		$humidity = $lastLogValues['humidity'];

		$widget = "";

			$widget.= "<div class='sensor-name'>";
				$widget.= getVirtualSensorDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-location'>";
				$widget.= getVirtualSensorTypeDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-temperature'>";
					if (isset($temperature) && strlen($temperature) > 0) {
						$widget.= "<img src='". $myPath."/thermometer.png' alt='icon' />";
						$widget.= "".$temperature."&deg;&nbsp;";
					}
					if (isset($humidity) && strlen($humidity) > 0) {
						$widget.= "<img src='". $myPath."/water.png' alt='icon' />";
						$widget.= "".$humidity."%&nbsp;";
					}
			$widget.= "</div>";

			$widget.= "<div class='sensor-timeago'>";
				$timeUpdatedByInsertedLog = getLastVirtualSensorLogTimestamp($virtualSensorID);
				if ($timeUpdatedByInsertedLog==0){
					$timeUpdatedByInsertedLog = time();
				}
				
				$widget.= "<abbr class=\"timeago\" title='".date("c", $timeUpdatedByInsertedLog)."'>".date("d-m-Y H:i", $timeUpdatedByInsertedLog)."</abbr>";
			$widget.= "</div>";	
		
		return $widget;
	}
	
	function getVirtualSensorVal($parameter, $virtualSensorID) {
		$consumer = connectToTelldus($parameter);
		$requestParams = array('id'=> $parameter['telldus_sensor_id']['value']);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensor/info', $requestParams, 'GET');
		
		// Get XML and create array with SimpleXMLElement
		$xmlData = $response->getBody();
		$xml = new \SimpleXMLElement($xmlData);
		
		$returnValArr = array(
 				'temperature'=>(string)$xml->data[0]['value'],
 				'humidity'=>(string)$xml->data[1]['value']
		);

		return $returnValArr;
	}

	// return true if the data should grouped according to the 
	// time range selected in the chart to get a better performance
	function groupChartData() {
		return true;
	}

?>
