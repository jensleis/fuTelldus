<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../lib/config.inc.php");
require_once("../../../lib/base.inc.php");

// Create DB-instance
$mysqli = new Mysqli($host, $username, $password, $db_name);

// Check for connection errors
if ($mysqli->connect_errno) {
	die('Connect Error: ' . $mysqli->connect_errno);
}

// Set DB charset
mysqli_set_charset($mysqli, "utf8");

if (isset($_GET['device_id']))
	$displayID = clean($_GET['device_id']);

if (isset($_GET['type']))
	$type = clean($_GET['type']); // sensor or device

if (isset($_GET['pageaction']))
	$action = clean($_GET['pageaction']);	//prev, next, current, first

if (isset($_GET['currentpageid']))
	$currentPage = clean($_GET['currentpageid']);	//could be empty if action is first or next

$firstPageQuery = "select * from ".$db_prefix."displays_pages where
					display_id='".$displayID."' and type='".$type."' order by page_id asc limit 1";

$lastPageQuery = "select * from ".$db_prefix."displays_pages where
					display_id='".$displayID."' and type='".$type."' order by page_id desc limit 1";

$nextPageQuery = "select * from ".$db_prefix."displays_pages where display_id='".$displayID."' and type='".$type."' and page_id=
			(select min(page_id) from ".$db_prefix."displays_pages where display_id='".$displayID."' and type='".$type."' and page_id>'".$currentPage."') limit 1";

$prevPageQuery = "select * from ".$db_prefix."displays_pages where display_id='".$displayID."' and type='".$type."' and page_id=
			(select max(page_id) from ".$db_prefix."displays_pages where display_id='".$displayID."' and type='".$type."' and page_id<'".$currentPage."') limit 1";

$currentPageQuery = "select * from ".$db_prefix."displays_pages where
					display_id='".$displayID."' and type='".$type."' and page_id='".$currentPage."' order by page_id asc limit 1"; 

if ($action=='next' || $action=='first') {
	if ($currentPage==null) {
		// load first page
		$query = $firstPageQuery;
	} else {
		//load next page
		$query = $nextPageQuery;
		$result = $mysqli->query($query);
		if ($result->num_rows == 0) { // if no next page exists, load first page
			$query = $firstPageQuery;
		}
	}
} else if ($action=="current") {
	$query = $currentPageQuery;
} else if ($action=='prev') {
	if ($currentPage==null) {
		// load first page
		$query = $firstPageQuery;
	} else {
		//load prev page
		$query = $prevPageQuery;
		$result = $mysqli->query($query);
		if ($result->num_rows == 0) { // if no next page exists, load last page
			$query = $lastPageQuery;
		}
	}
}


$result = $mysqli->query($query);
if ($result->num_rows == 1) {
	$resultArray = mysqli_fetch_assoc($result);
// 	echo $resultArray['html'];
// 	$html = base64_decode($resultArray['html']);
	$html = $resultArray['html'];
	$reqCurrent = $resultArray['reqCurrent'];
	if ($reqCurrent == "1") {
		$resultArray['html'] = replacePlaceHolder($html, $resultArray['type_id'], $currentUser, true, $type );
	} else {
		$resultArray['html'] = replacePlaceHolder($html, $resultArray['type_id'], $currentUser, false, $type );
	}
	

	echo json_encode($resultArray);
} else if ($result->num_rows == 0) {
	echo "";
} else {
	// ????
}

function replacePlaceHolder($html, $virtualSensorID, $currentUser, $currentValue, $type) { 
	if ($type=='sensor') {
		if ($currentValue) {
			global $mysqli;
			global $db_prefix;
			$currentUserQuery = "select user_id from ".$db_prefix."displays where display_id='".$displayID."'";
			$resultUser = $mysqli->query($currentUserQuery);
			$currentUser = mysqli_fetch_assoc($resultUser)['user_id'];
			
			$sensorValues = getCurrentVirtualSensorState($virtualSensorID, $currentUser);
		} else {
			$sensorValues =  getLastVirtualSensorLog($virtualSensorID);
		}
	} else if ($type=='device') {
		$state =  getCurrentVirtualDeviceStateWithoutUser($virtualSensorID);
		$sensorValues = array('state' => $state);
	}
	
	foreach ($sensorValues as $key => $sensorVal) {
		$replace = $sensorVal;
		$search = "/{{(\s)*".$key."(\s)*}}/";
		$html = preg_replace($search,$replace,$html);
	}

	return $html;
}

exit();

?>