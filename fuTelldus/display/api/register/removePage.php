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

$page_id=clean($_GET['page_id']);

$query = "delete from ".$db_prefix."displays_pages 
					where page_id='".$page_id."'";	

$result = $mysqli->query($query);


exit();

?>