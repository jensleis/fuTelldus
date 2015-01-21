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

$display_id=clean($_POST['display_id']);
$page_id=clean($_POST['page_id']);
$type=clean($_POST['type']);
$description=clean($_POST['description']);
$showFor=clean($_POST['showFor']);
$refreshAfter=clean($_POST['refreshAfter']);
$device=clean($_POST['device']);
$html=clean($_POST['html']);
$reqCurrent=clean($_POST['reqCurrent']);

if (strlen($page_id)==0) {
	// insrt
	$query = "insert into ".$db_prefix."displays_pages SET
					display_id='".$display_id."',
					type='".$type."',
					description='".$description."',
					showFor='".$showFor."',
					refreshAfter='".$refreshAfter."',
					type_id='".$device."',
					reqCurrent='".$reqCurrent."',
					html='".$html."'";	
} else {
	$query = "update ".$db_prefix."displays_pages SET
					display_id='".$display_id."',
					type='".$type."',
					description='".$description."',
					showFor='".$showFor."',
					refreshAfter='".$refreshAfter."',
					type_id='".$device."',
					html='".$html."',
					reqCurrent=".$reqCurrent."
					where page_id='".$page_id."'";
	echo $query;
}

$result = $mysqli->query($query);


exit();

?>