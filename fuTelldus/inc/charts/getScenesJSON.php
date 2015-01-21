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

	
	/* get the devices
	-------------------------------------------------------*/
	$result = getScenes($user_id);
	header('Content-Type: text/javascript');
	echo json_encode($result);

	function getScenes($user_id) {
		global $mysqli;
		global $db_prefix;	
		
		$query = "
			select sub.id, sub.name as scene_name, group_concat(sub.included SEPARATOR '; ') as scene_content from (
				select 
					s.id, 
					s.name,
					case sd.type
						when 'sensor' then (select s.name from ".$db_prefix."sensors s where s.sensor_id=sd.type_id)
						when 'virtual' then (select v.description from ".$db_prefix."virtual_sensors v where v.id=sd.type_id)
						when 'device' then (select d.name from ".$db_prefix."devices d where d.device_id=sd.type_id)
					end as included
				from ".$db_prefix."scenes s
				left outer join ".$db_prefix."scenes_data sd on s.id = sd.scene_id					
				where s.user_id='".$user_id."'
			) as sub group by sub.id order by scene_name";	
				
		$result = $mysqli->query($query);
		
		$json = array();
		
		$index = 1;
		while ($item = $result->fetch_array()) {
			$sceneID = $item["id"];
			$sceneName = $item["scene_name"];
			$sceneContent = $item["scene_content"];
			if (is_null($sceneContent)) {
				$sceneContent="";
			}
			$json[$index]['id'] = $sceneID;
			$json[$index]['name'] = $sceneName;
			$json[$index]['included'] = $sceneContent;
			$index++;
		}
		
		return $json;
	}
?>
