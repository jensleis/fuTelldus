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

$user_id = clean($_GET['user_id']);

$query = "select * from ".$db_prefix."displays where
					user_id='".$user_id."' order by name asc";
$result = $mysqli->query($query);

$rows = array();
while($r = mysqli_fetch_assoc($result)) {
	$rows[] = $r;
}

// unset($_SESSION['fuTelldus_user_loggedin']);

echo json_encode($rows);

exit();

?>