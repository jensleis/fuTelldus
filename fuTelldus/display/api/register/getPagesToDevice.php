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

$device_id = clean($_GET['device']);

$query = "select * from ".$db_prefix."displays_pages 
					where display_id='".$device_id."' order by type, description asc";

$result = $mysqli->query($query);

$rows = array();
while($r = mysqli_fetch_assoc($result)) {
// 	$r['html'] = "";//base64_decode($r['html']);
	$rows[] = $r;
// 	print_r($r['html']);
}

echo json_encode($rows);

exit();

?>