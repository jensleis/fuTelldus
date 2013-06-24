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
	
	// get parameter
	$user_id = clean($_GET['user_id']);
	if (isset($_GET['scene_id'])) $scene_id = clean($_GET['scene_id']);
	
	/* get the devices
	-------------------------------------------------------*/
	$result = getChartableDevices($user_id, $scene_id);
	header('Content-Type: text/javascript');
	echo json_encode($result);

	// returns an array in array with the return-type definied by the plugin
	// and the data found in the DB belonging to this return-type
	function getChartableDevices($user_id, $scene_id) {
		global $mysqli;
		global $db_prefix;	
		
		if (! isset($scene_id)){
			$scene_id=-1;
		} 

		$query = "select name, 
						id, 
						type, 
						if ((select count(sd.scene_id) from ".$db_prefix."scenes_data sd where sd.scene_id='".$scene_id."' and sd.type=type and sd.type_id=id)=1,'true', 'false') as activated 
					from (
									select s.name, s.sensor_id as id, 'sensor' as type
										from ".$db_prefix."sensors s
											where s.monitoring=1 and s.user_id='".$user_id."'
									union
									select vs.description as name, vs.id as id, 'virtual' as type
										from ".$db_prefix."virtual_sensors vs
												where vs.monitoring=1 and vs.user_id='".$user_id."'
									union
									select d.name, d.device_id as id, 'device' as type
										from ".$db_prefix."devices d 
												where d.user_id='".$user_id."'
					) as outertable 
				";
		$result = $mysqli->query($query);
		
		$json = array();
		
		$index = 1;
		while ($item = $result->fetch_array()) {
			// remove all where the plugin is not 'chartable'
			$itemID = $item["id"];
			$itemType = $item["type"];
			$itemActivated= $item["activated"];
			
			$chartAble = false;
			if ($itemType=='virtual'){
				$chartAble = isPluginProvidingCharts($itemID);
			} else if ($itemType=='sensor') {
				$chartAble = true;
			} else if ($itemType=='device') {
				$chartAble = true;
			}
			
			if ($chartAble == true) {
				$json[$index]['name'] = $item["name"];
				$json[$index]['id'] = $itemID;
				$json[$index]['type'] = $itemType;
				$json[$index]['activated'] = $itemActivated;
				$index++;
			}
		}
		
		return $json;
	}
?>
