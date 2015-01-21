<?php
	
	require ('virtualSensor.functions.inc.php');
	require ('virtualDevice.functions.inc.php');
	require ('actions.functions.inc.php');

	// returns an array with all plugins. The array contains an array with 
	// the plugin information from the plugin_info.txt
	// the result list is filtered by the given type
	function getAvailablePlugins($type) {
		$dirs = getPluginDirs();

		$plugins = array();
		foreach (array_keys($dirs) as $key) {
			$ini_array = parse_ini_file($dirs[$key]."/plugin_info.txt");
			if (trim($ini_array['type']) == $type){
				//array_push($ini_array, array("directory" => $dirs[$key]));
				$ini_array["directory"] = $dirs[$key];
				array_push($plugins, $ini_array);
			}
		}
		
		return $plugins;
	}
	
	function getActivatedAvailablePlugins() {
		$dirs = getPluginDirs();
		
		$plugins = array();
		foreach (array_keys($dirs) as $key) {
			$activated = isPluginActivated($dirs[$key]);
			$ini_array = parse_ini_file($dirs[$key]."/plugin_info.txt");
			
			$ini_array["directory"] = $dirs[$key];
			if ($activated == true) {
				array_push($plugins, $ini_array);
			}
		}
		
		return $plugins;
	}
	
	// returns an array with all plugin-dirs
	function getPluginDirs() {
		$plugins = array();

		$path = "fuPlugins/";
		foreach(glob($path.'*', GLOB_ONLYDIR) as $dir) {
			array_push($plugins, $dir);
		}

		return $plugins;
	}
	
	// checks if the plugin to the plugin-path is activated
	function isPluginActivated($pluginPath) {
		global $mysqli;
		global $db_prefix;
		
		$query = "SELECT * FROM ".$db_prefix."plugins where plugin_path='".$pluginPath."'";
	    $result = $mysqli->query($query);
		if (isset($result)) {
			$numRows = $result->num_rows;	
			if ($numRows > 1) {
				$numRows=1;
			}
		} else {
			$numRows=0;
		}
	    
		return $numRows;
	}
	
	// return the id of a plugin
	function getPluginIDToPluginPath($pluginPath) {
		global $mysqli;
		global $db_prefix;
		
		$query = "SELECT type_int FROM ".$db_prefix."plugins where plugin_path='".$pluginPath."'";
		$result = $mysqli->query($query);
		$pluginID = $result->fetch_assoc()['type_int'];
		return $pluginID;
	}
	
	function activatePlugin($pluginName, $pluginPath, $user_settings_path, $version, $pluginType) {
		global $mysqli;
		global $db_prefix;
		
		$pluginIndex = $pluginPath."/index.php";
		if (file_exists($pluginIndex)) {
		
			$nameSpace = includePlugin($pluginIndex);
			$func = $nameSpace."\\activateHook";		
			//include_once $pluginIndex;
			$configFields = @$func();
			
			if (isset($user_settings_path) && strlen($user_settings_path)>0) {
				$user_settings_path = $pluginPath."/".$user_settings_path;
			}

			$query = "INSERT INTO ".$db_prefix."plugins SET
				type_description='".$pluginName."', 
				activated_version='".$version."',
				plugin_type='".$pluginType."',
				plugin_path='".$pluginPath."',
				user_settings_path='".$user_settings_path."'";
			$result = $mysqli->query($query);
			$vSensorPluginID = $mysqli->insert_id;
			
			// add every config type
			foreach (array_keys($configFields) as $value_key) {
				$pluginKey = $configFields[$value_key]['key'];
				
				// default values
				$pluginType = "text";
				$pluginConfigType = "instance";
				$pluginDescription = "";
				
				// overwrite if was set
				if (array_key_exists("type", $configFields[$value_key])) $pluginType = $configFields[$value_key]['type'];
				if (array_key_exists("config_type", $configFields[$value_key])) $pluginConfigType = $configFields[$value_key]['config_type'];
				if (array_key_exists("description", $configFields[$value_key])) $pluginDescription = $configFields[$value_key]['description'];
				
				$queryInsert2 = "INSERT INTO ".$db_prefix."plugins_config SET
					type_int='".$vSensorPluginID."', 
					config_type='".$pluginConfigType."', 
					value_key='".$pluginKey."', 
					value_type='".$pluginType."', 
					description='".$pluginDescription."'";
				$resultInsert2 = $mysqli->query($queryInsert2);
			}
		}		
	}
	
	function getPluginPathToPluginID($pluginID) {
		global $mysqli;
		global $db_prefix;
		
		$queryPath = "select plugin_path from ".$db_prefix."plugins where type_int='".$pluginID."'";
		$result = $mysqli->query($queryPath);
		$pluginPath = $result->fetch_assoc()['plugin_path'];
		return $pluginPath;
	}
	
	function getPluginUserSettingsPath($pluginID) {
		global $mysqli;
		global $db_prefix;
	
		$queryPath = "select user_settings_path from ".$db_prefix."plugins where type_int='".$pluginID."'";
		$result = $mysqli->query($queryPath);
		$pluginPath = $result->fetch_assoc()['user_settings_path'];
		return $pluginPath;
	}
	
	function updatePlugin($pluginID, $version) {
		global $mysqli;
		global $db_prefix;
		
		$path = getPluginPathToPluginID($pluginID);
		
		$query = "update ".$db_prefix."plugins SET
				activated_version='".$version."' where type_int='".$pluginID."'"; 				
		$mysqli->query($query);

		$nameSpace = includePlugin($path."/index.php");
		$func = $nameSpace."\\updateHook";	
		//include_once $path."/index.php";
		$configFields = @$func();
		foreach (array_keys($configFields) as $value_key) {
					$pluginKey = $configFields[$value_key]['key'];
					
					// default values
					$pluginType = "text";
					$pluginConfigType = "instance";
					$pluginDescription = "";
					
					// overwrite if was set
					if (array_key_exists("type", $configFields[$value_key])) $pluginType = $configFields[$value_key]['type'];
					if (array_key_exists("config_type", $configFields[$value_key])) $pluginConfigType = $configFields[$value_key]['config_type'];
					if (array_key_exists("description", $configFields[$value_key])) $pluginDescription = $configFields[$value_key]['description'];
					
					$queryInsert = "REPLACE INTO ".$db_prefix."plugins_config SET 
						type_int='".$vSensorPluginID."', 
						config_type='".$pluginConfigType."', 
						value_key='".$pluginKey."', 
						value_type='".$pluginType."', 
						description='".$pluginDescription."'";
					$resultInsert = $mysqli->query($queryInsert);
				}
		
		// get all which where deleted
		$querySelectAll = "select value_key, id from ".$db_prefix."plugins_config where type_int='".$pluginID."'";
		$resultSelectAll = $mysqli->query($querySelectAll);
		
		while ($row = $resultSelectAll->fetch_array()) {
			$value_key_db = $row['value_key'];
			$rowId = $row['id'];
			
			if (!array_key_exists($value_key_db, $configFields)){
				$queryDelete = "delete from ".$db_prefix."plugins_config where id='".$rowId."' and type_int='".$pluginID."' and value_key='".$value_key_db."'";
				$mysqli->query($queryDelete);
				
				$queryDeleteVirtualSensorUserConfig = "delete from ".$db_prefix."plugins_user_config where config_id in (select id FROM ".$db_prefix."plugins_config where type_int='".$pluginID."')";
				$mysqli->query($queryDeleteVirtualSensorUserConfig);
				
				$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."plugins_instance_config where config_id='".$rowId."'";
				$mysqli->query($queryDeleteVirtualSensorConfig);
			}
		}
	}
	
	// returns the type in string for the given pluginID
	function getPluginType($pluginID) {
		global $db_prefix;
		global $mysqli;
		
		$queryTmpVal = "select plugin_type from ".$db_prefix."plugins where type_int = ".$pluginID;
		$result = $mysqli->query($queryTmpVal);
		$return=null;
		if (mysqli_num_rows($result) == 1) {
			$return = $result->fetch_assoc()['plugin_type'];
		}
		return $return;
	}
	
	// returns the description to the given pluginID
	function getPluginTypeDescription($pluginID) {
		global $db_prefix;
		global $mysqli;
	
		$queryTmpVal = "select type_description from ".$db_prefix."plugins where type_int = ".$pluginID;
		$result = $mysqli->query($queryTmpVal);
		$return=null;
		if (mysqli_num_rows($result) == 1) {
			$return = $result->fetch_assoc()['type_description'];
		}
		return $return;
	}
	
	function disableSensorPlugin($pluginID) {
		global $mysqli;
		global $db_prefix;
		
		$querySelectVirtualSensor = "select * from ".$db_prefix."virtual_sensors where sensor_type='".$pluginID."'";
		$resultAllSensors = $mysqli->query($querySelectVirtualSensor);
		
		while ($row = $resultAllSensors->fetch_array()) {
			$sensorID = $row['id'];
				
			$querySelectVirtualSensorLogs = "select * from ".$db_prefix."virtual_sensors_log where sensor_id='".$sensorID."'";
			$resultAllSensorsLogs = $mysqli->query($querySelectVirtualSensorLogs);
				
			while ($row1 = $resultAllSensorsLogs->fetch_array()) {
				$logID = $row1['id'];
		
				$queryDeleteVirtualSensorLogValue = "delete from ".$db_prefix."virtual_sensors_log_values where log_id='".$logID."'";
				$mysqli->query($queryDeleteVirtualSensorLogValue);
			}
				
			$queryDeleteVirtualSensorLogs = "delete from ".$db_prefix."virtual_sensors_log where sensor_id='".$sensorID."'";
			$mysqli->query($queryDeleteVirtualSensorLogs);
				
			$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."virtual_sensors where id='".$sensorID."'";
			$mysqli->query($queryDeleteVirtualSensorConfig);
		}
		
	}
	
	function disableDevicePlugin($pluginID) {
		global $mysqli;
		global $db_prefix;

		$querySelectVirtualSensor = "select * from ".$db_prefix."virtual_devices where plugin_id='".$pluginID."'";
		$resultAllSensors = $mysqli->query($querySelectVirtualSensor);
		
		while ($row = $resultAllSensors->fetch_array()) {
			$deviceID = $row['id'];
			
			$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."virtual_devices where id='".$deviceID."'";
			$mysqli->query($queryDeleteVirtualSensorConfig);
		}
		
	}
	
	function getPluginConfigToKey($virtualSensorID, $config_key) {
		global $mysqli;
		global $db_prefix;
	
		$query = "select vsc.value from ".$db_prefix."plugins_instance_config vsc, ".$db_prefix."plugins_config vstc where vstc.id = vsc.config_id and vstc.value_key='$config_key' and vsc.sensor_id=$virtualSensorID";
		$result = $mysqli->query($query);
		$configValue = "";
		if (mysqli_num_rows($result) == 1) {
			$configValue = $result->fetch_assoc()['value'];
		}
		return $configValue;
	}
	
	function disablePlugin($pluginID) {
		global $mysqli;
		global $db_prefix;
		
		$pluginType = getPluginType($pluginID);
		
		if ($pluginType=='sensor') {
			disableSensorPlugin($pluginID);	
		} elseif ($pluginType=='device'){
			disableDevicePlugin($pluginID);
		}
		
		$queryDeleteVirtualSensor = "delete from ".$db_prefix."plugins where type_int='".$pluginID."'";
		$mysqli->query($queryDeleteVirtualSensor);
		
		$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."plugins_config where type_int='".$pluginID."'";
		$mysqli->query($queryDeleteVirtualSensorConfig);
		
		$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."plugins_instance_config where sensor_id='".$sensorID."'";
		$mysqli->query($queryDeleteVirtualSensorConfig);
		
		$queryDeleteVirtualSensorUserConfig = "delete from ".$db_prefix."plugins_user_config where config_id in (select id FROM ".$db_prefix."plugins_config where type_int='".$pluginID."')";
		$mysqli->query($queryDeleteVirtualSensorUserConfig);
		
		$queryDeleteVirtualSensorTmp = "delete from ".$db_prefix."plugins_tmpvals where sensor_id='".$sensorID."'";
		$mysqli->query($queryDeleteVirtualSensorTmp);
	}
	
	function getPluginParametersWithoutUser($pluginInstanceID, $type) {
		$userID = getUserToDevices($pluginInstanceID);
		return getPluginParameters($pluginInstanceID, $type, $userID);
	}
	
	
	function getPluginParameters($pluginInstanceID, $type, $userID) {
		global $mysqli;
		global $db_prefix;
		
		// get all configured values for the instance
		$query = "select pc.value_key, pic.value , pc.description, pc.type_int, pc.value_type, pc.id from ".$db_prefix."plugins_instance_config pic, ".$db_prefix."plugins_config pc, ".$db_prefix."plugins p where p.type_int=pc.type_int and pic.sensor_id=$pluginInstanceID and pc.id = pic.config_id and p.plugin_type='".$type."'";// and pc.value_type!='return'";
		$result = $mysqli->query($query);
		
		$parameter = array();
		$pluginID;
		while ($row = $result->fetch_array()) {
			$pluginID = $row['type_int'];
			//$parameter[$row['value_key']] = $row['value'];
			$parameter[$row['value_key']] = array('id' => $row['id'], 'description' => $row['description'], 'value_type' => $row['value_type'], 'value' => $row['value']);
		}
		
		// add all user configured values
		$userConfig = getPluginUserConfigArrayWithValues($userID, $pluginID);

		foreach (array_keys($userConfig) as $value_key) {
			$parameter[$value_key] = array('id' => $userConfig[$value_key]['id'], 'description' => $userConfig[$value_key]['description'], 'value_type' => $userConfig[$value_key]['value_type'], 'value' => $userConfig[$value_key]['value']);
			//$parameter[$value_key] = $userConfig[$value_key];
		}

		return $parameter;
	}
	
	// get all user-configs of the given user for the plugin
	// if there is allready data existing, return it, if not, return null
	// values for those config-records
	function getPluginUserConfigArrayWithValues($userID, $pluginID) {
		global $mysqli;
		global $db_prefix;
		
		$parameter = array();
		$query = "select pc.id, pc.description, pc.value_key, pc.value_type, puc.value from ".$db_prefix."plugins_config  pc
			left outer join ".$db_prefix."plugins_user_config puc on pc.id=puc.config_id and puc.user_id='".$userID."'
			where type_int=".$pluginID." and config_type='user'";
		$result = $mysqli->query($query);
		
		while ($row = $result->fetch_array()) {
 			$parameter[$row['value_key']] = array('id' => $row['id'], 'description' => $row['description'], 'value_type' => $row['value_type'], 'value' => $row['value']);
			//$parameter[$row['value_key']] = $row['value'];
		}
		
		return $parameter;
	}
	
	// returns the parameter array for return-values. loaded fully, including description, ...
	function getPluginReturnParameter($sensorID) {
		global $mysqli;
		global $db_prefix;
		
		$parameter = array();
		$query = "select pc.id, pc.value_key, pc.description, pc.value_type from ".$db_prefix."plugins_config pc, ".$db_prefix."virtual_sensors vs 
				where vs.id = '".$sensorID."'
				and vs.sensor_type = pc.type_int
				and pc.value_type='return'";
		$result = $mysqli->query($query);
		
		while ($row = $result->fetch_array()) {
			$parameter[$row['value_key']] = array('id' => $row['id'], 'description' => $row['description'], 'value_type' => $row['value_type'], 'value' => '');
		}
		
		return $parameter;
	}
	
	
?>