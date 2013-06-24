<?php
	function getVirtualDeviceDescriptionToDeviceId($deviceID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select description from ".$db_prefix."virtual_devices where id='".$deviceID."'";
		$result = $mysqli->query($query);
		$description = "";
		if (mysqli_num_rows($result) == 1) {
			$description = $result->fetch_assoc()['description'];
		}
		return $description;
	}
	
	function getVirtualDeviceTypeDescriptionToDeviceId($deviceID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select p.type_description from ".$db_prefix."virtual_devices vd, ".$db_prefix."plugins p where vd.id='".$deviceID."' and p.type_int = vs.plugin_id";
		$result = $mysqli->query($query);
		$typeDescription = "";
		if (mysqli_num_rows($result) == 1) {
			$typeDescription = $result->fetch_assoc()['type_description'];
		}
		return $typeDescription;
	}
	
	function getCurrentVirtualDeviceState($deviceID) {
		global $mysqli;
		global $db_prefix;
		
		$parameter = getPluginParameters($deviceID);
		
		// find the script
		$phpscript = getPluginPathToVDeviceId($deviceID);
		
		$nameSpace = includePlugin($phpscript."/index.php");
		$func = $nameSpace."\\getStatus";	
		//include_once $phpscript."/index.php";		
		$returnFromScript = @$func($parameter, $deviceID); 	

		// update last state
		$updateCheck = "update ".$db_prefix."virtual_devices set last_status='".$returnFromScript."' WHERE id='".$deviceID."'";
		$mysqli->query($updateCheck);
		
		return $returnFromScript;
	}
	
	// return the unix-timestamp when the last switch of the device took place
	function getLastVirtualDeviceStatusSwitch($virtualDeviceID){
		global $db_prefix;
		global $mysqli;

		$queryTimestamp = "select last_switch from ".$db_prefix."virtual_devices where id='".$virtualDeviceID."' order by last_switch desc limit 1";
		$result = $mysqli->query($queryTimestamp);
		$timeStamp=0;
		if (mysqli_num_rows($result) == 1) {
			$timeStamp = $result->fetch_assoc()['last_switch'];
		}
		return $timeStamp;
	}
	
	// return the last status of the device, known by the system
	function getLastVirtualDeviceStatus($virtualDeviceID){
		global $db_prefix;
		global $mysqli;
	
		$queryTimestamp = "select last_status from ".$db_prefix."virtual_devices where id='".$virtualDeviceID."' order by last_status desc limit 1";
		$result = $mysqli->query($queryTimestamp);
		$timeStamp=0;
		if (mysqli_num_rows($result) == 1) {
			$timeStamp = $result->fetch_assoc()['last_status'];
		}
		return $timeStamp;
	}
	

	function getPluginPathToVDeviceId($sensorID) {
		global $mysqli;
		global $db_prefix;
	
		$query = "select p.plugin_path from ".$db_prefix."virtual_devices vd, ".$db_prefix."plugins p where vd.id=$sensorID and vd.plugin_id=p.type_int";
		$result = $mysqli->query($query);
		$phpscript = $result->fetch_assoc();
		return $phpscript['plugin_path'];
	}
	
	function getDevices($userID){
		global $mysqli;
		global $db_prefix;
		
		$query = "select vd.* from futelldus_virtual_devices vd, futelldus_plugins p where p.type_int = vd.plugin_id and vd.user_id='".$userID."' order by vd.description asc";
		$result = $mysqli->query($query);
		
		return $result;
	}
?>