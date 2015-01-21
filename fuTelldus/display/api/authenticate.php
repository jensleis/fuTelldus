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

if (isset($_GET['myhopo_device_id'])) $deviceID = clean($_GET['myhopo_device_id']);

$query = "select * from ".$db_prefix."displays where
					display_id='".$deviceID."'";

$result = $mysqli->query($query);
$numRowsLogin = $result->num_rows;

   if ($numRowsLogin==1) {
   	echo $deviceID;
   } else {
   	echo "";
   }

exit();
?>
