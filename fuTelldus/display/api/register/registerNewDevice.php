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
$name = clean($_GET['name']);
$uuid = newGuid(); 

$query = "INSERT INTO ".$db_prefix."displays SET
					display_id='".$uuid."',
					name='".$name."',
					user_id='".$user_id."'";
$result = $mysqli->query($query);

// unset($_SESSION['fuTelldus_user_loggedin']);

echo $uuid;
exit();


function newGuid() {
	$s = strtoupper(md5(uniqid(rand(),true)));
	$guidText =
	substr($s,0,8) . '-' .
	substr($s,8,4) . '-' .
	substr($s,12,4). '-' .
	substr($s,16,4). '-' .
	substr($s,20);
	return $guidText;
}

?>