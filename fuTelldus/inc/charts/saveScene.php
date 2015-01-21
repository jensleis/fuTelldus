
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
	$action = clean($_GET['action']);
	$sceneID = clean($_GET['id']);
	$userID = clean($_GET['userid']);
	$sceneName = clean($_GET['name']);
	$sceneDataJSON = clean($_GET['scenedata']);
	$sceneDaraArray = json_decode($sceneDataJSON);
	
	$returnVal = 0;
	if ($action=='update') {
		updateScene($sceneName, $sceneID, $sceneDaraArray);
		$returnVal = 1;
	} else if ($action=='insert') {
		insertScene($sceneName, $userID, $sceneDaraArray);
		$returnVal = 1;
	} else if ($action=='delete') {
		deleteScene($sceneID);
		$returnVal = 1;
	}
	//header('Content-Type: text/javascript');
	echo $returnVal;

	function deleteScene($sceneID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "delete from ".$db_prefix."scenes where
				id='".$sceneID."'";
		$mysqli->query($query);
		
		$querySub = "delete from ".$db_prefix."scenes_data where
				scene_id='".$sceneID."'";
		$mysqli->query($querySub);
	}
	
	function updateScene($name, $scene_id, $sceneData) {
		global $mysqli;
		global $db_prefix;
		
		$query = "UPDATE ".$db_prefix."scenes set 
				name='".$name."'
				where id='".$scene_id."'";
		$result = $mysqli->query($query);
		
		// delete all with id first
		$queryDelete = "delete from ".$db_prefix."scenes_data where scene_id='".$scene_id."'";
		$mysqli->query($queryDelete);
		
		// insert new values
		foreach ($sceneData as $oneSceneData) {
			$sceneDataType = $oneSceneData->type;
			$sceneDataID = $oneSceneData->id;
			$querySub = "insert into ".$db_prefix."scenes_data set 
				scene_id='".$scene_id."',
				type='".$sceneDataType."',
				type_id='".$sceneDataID."'";
			$mysqli->query($querySub);
		}
	}
	
	function insertScene($name, $user_id, $sceneData) {
		global $mysqli;
		global $db_prefix;
		
		$query = "INSERT INTO ".$db_prefix."scenes set 
				user_id='".$user_id."',
				name='".$name."'";
		$result = $mysqli->query($query);
		$scene_id = $mysqli->insert_id;
		
		// insert new values
		foreach ($sceneData as $oneSceneData) {
			$sceneDataType = $oneSceneData->type;
			$sceneDataID = $oneSceneData->id;
			$querySub = "insert into ".$db_prefix."scenes_data set 
				scene_id='".$scene_id."',
				type='".$sceneDataType."',
				type_id='".$sceneDataID."'";
			$mysqli->query($querySub);
		}
	}
	
?>


