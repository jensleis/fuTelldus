<?php
	
	require ('virtualSensor.functions.inc.php');

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
	
	function activatePlugin($pluginName, $pluginPath, $version, $pluginType) {
		global $mysqli;
		global $db_prefix;
		
		$pluginIndex = $pluginPath."/index.php";
		if (file_exists($pluginIndex)) {
		
			$nameSpace = includePlugin($pluginIndex);
			$func = $nameSpace."\\activateHook";		
			//include_once $pluginIndex;
			$configFields = @$func();

			$query = "INSERT INTO ".$db_prefix."plugins SET
				type_description='".$pluginName."', 
				activated_version='".$version."',
				plugin_type='".$pluginType."',
				plugin_path='".$pluginPath."'";
			$result = $mysqli->query($query);
			$vSensorPluginID = $mysqli->insert_id;
			
			// add every config type
			foreach (array_keys($configFields) as $value_key) {
				foreach (array_keys($configFields[$value_key]) as $description) {
					$queryInsert2 = "INSERT INTO ".$db_prefix."plugins_config SET
						type_int='".$vSensorPluginID."', 
						value_key='".$value_key."', 
						value_type='".$configFields[$value_key][$description]."', 
						description='".$description."'";
					$resultInsert2 = $mysqli->query($queryInsert2);
				}
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
	
	function getPluginPathToVSensorId($sensorID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select vst.plugin_path from ".$db_prefix."virtual_sensors vs, ".$db_prefix."plugins vst where vs.id=$sensorID and vs.sensor_type=vst.type_int";
		$result = $mysqli->query($query);
		$phpscript = $result->fetch_assoc();
		return $phpscript['plugin_path'];
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
			foreach (array_keys($configFields[$value_key]) as $description) {
				// replace/insert all
				$queryInsert = "REPLACE INTO ".$db_prefix."plugins_config SET 
						type_int='".$pluginID."', 
						value_key='".$value_key."', 
						value_type='".$configFields[$value_key][$description]."', 
						description='".$description."'";
				$resultInsert = $mysqli->query($queryInsert);
			}
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
			$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."virtual_devices where id='".$sensorID."'";
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
		
		$queryDeleteVirtualSensorTmp = "delete from ".$db_prefix."plugins_tmpvals where sensor_id='".$sensorID."'";
		$mysqli->query($queryDeleteVirtualSensorTmp);
	}
	
?>