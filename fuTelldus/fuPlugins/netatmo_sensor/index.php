<?php
namespace netatmo\netatmo_sensor;
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
				array('key' => 'temperature',
						'type' => 'return',
						'description' => 'Temperature'),
				array('key' => 'humidity',
						'type' => 'return',
						'description' => 'Humidity'),
				array('key' => 'co2',
						'type' => 'return',
						'description' => 'CO2'),
				array('key' => 'pressure',
						'type' => 'return',
						'description' => 'Pressure'),				
				array('key' => 'noise',
						'type' => 'return',
						'description' => 'Noise'),
				array('key' => 'rain',
						'type' => 'return',
						'description' => 'Rain'),
				array('key' => 'netatmo_module_id',
						'type' => 'callBackMethodReturnList;listExistingModules',
						'description' => 'Netatmo Module'),				
				array('key' => 'netatmo_refresh_token',
						'type' => 'text',
						'config_type' => 'user',
						'description' => 'Refresh token'),				
		);
	}
	
	/*
	 * Return a list of existing sensors from netatmp via the API.
	* The $params array parameter includes the API keys set form the user configuration
	*/
	function listExistingModules($params) {
		$client = connectToNetatmo($params['netatmo_refresh_token']['value']);
		
		$deviceList = $client->api("devicelist");

		$returnVal = array();
		foreach($deviceList['devices'] as $device) {
			// put the device itself as the first module 
			$name = $device['station_name']." - " . $device['module_name'];
			$id = $device['_id'];
			array_push($returnVal, array('id' => $id, 'name' => $name));
			
			// add every module, which is assigned to this device
			foreach($device['modules'] as $module_id) {
				foreach($deviceList['modules'] as $module) {
					if ($module['_id'] == $module_id) {
						$module_name = $device['station_name']." - " .  $module['module_name'];
						array_push($returnVal, array('id' => $module_id, 'name' => $module_name));
					}
				}		
			}
		}
	
		return $returnVal;
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
	
	function getDashboardDataToId($id, $deviceList) {
		foreach($deviceList['devices'] as $device) {
			if ($device['_id'] == $id) {
				return $device['dashboard_data'];
			}
		}
		
		foreach($deviceList['modules'] as $module) {
			if ($module['_id'] == $id) {
				return $module['dashboard_data'];
			}
		}
	}
	
	function getVirtualSensorVal($parameter, $virtualSensorID) {
		$client = connectToNetatmo($parameter['netatmo_refresh_token']['value']);
		
		$deviceList = $client->api("devicelist");
		$id = $parameter['netatmo_module_id']['value'];
		
		$dashboard_data = getDashboardDataToId($id, $deviceList);

		$returnValArr = array();
		
		if (array_key_exists ("Temperature", $dashboard_data)) {
			$returnValArr['temperature']=$dashboard_data['Temperature'];
		}
		
		if (array_key_exists ("Humidity", $dashboard_data)) {
			$returnValArr['humidity']=$dashboard_data['Humidity'];
		}
		
		if (array_key_exists ("CO2", $dashboard_data)) {
			$returnValArr['co2']=$dashboard_data['CO2'];
		}
		
		if (array_key_exists ("Pressure", $dashboard_data)) {
			$returnValArr['pressure']=$dashboard_data['Pressure'];
		}
		
		if (array_key_exists ("Noise", $dashboard_data)) {
			$returnValArr['noise']=$dashboard_data['Noise'];
		}
		
		if (array_key_exists ("Rain", $dashboard_data)) {
			$returnValArr['rain']=$dashboard_data['Rain'];
		}

		return $returnValArr;
	}
	
	function connectToNetatmo($refreshToken) {
		require_once 'API/NAApiClient.php';
		require 'API/Config.php';

		$config = array();
		$config['client_id'] = $client_id;
		$config['client_secret'] = $client_secret;
		$config['refresh_token'] = $refreshToken;
		
		$client = new \NAApiClient($config);
		
		return $client;
	}

	// return true if the data should grouped according to the 
	// time range selected in the chart to get a better performance
	function groupChartData() {
		return true;
	}

?>
