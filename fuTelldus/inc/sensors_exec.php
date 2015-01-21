<?php

	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);

	

	/*
	 --------------------------------------------------------------------------- */
	if ($action == "setMonitoring") {
		$getCurrentValue = getField("monitoring", "".$db_prefix."virtual_sensors", "WHERE id='".$getID."'");
		$returnStateMessage="";
		
		if ($getCurrentValue == 0) {
			$query = "UPDATE ".$db_prefix."virtual_sensors SET monitoring='1' WHERE user_id='".$user['user_id']."' AND id='".$getID."'";
			$result = $mysqli->query($query);
			$returnStateMessage="01";
		} else {
			$query = "UPDATE ".$db_prefix."virtual_sensors SET monitoring='0' WHERE user_id='".$user['user_id']."' AND id='".$getID."'";
			$result = $mysqli->query($query);
			$returnStateMessage="02";
		}

		// Redirect
		header("Location: ?page=sensors&msg=".$returnStateMessage);
		exit();
	}


	
?>