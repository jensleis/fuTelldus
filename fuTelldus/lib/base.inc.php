<?php
	
	require ("config.inc.php");
	require_once ("session.class.php");
	$session = new Session();
	
	ob_start();
	session_start();
	
	/* Connect to DB and get config
	--------------------------------------------------------------------------- */
	
	
	/* Connect to database
	--------------------------------------------------------------------------- */
	// Create DB-instance
	$mysqli = new Mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME); 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");
	
	/* Include functions
	--------------------------------------------------------------------------- */
	require ('php_functions/global.functions.inc.php');
	require ('php_functions/datetime.functions.inc.php');
	require ('php_functions/plugin.functions.inc.php');
	
	
	/* Get URL
	--------------------------------------------------------------------------- */
	$currentURL_01 = 'http'. ($_SERVER['HTTPS'] ? 's' : null) .'://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$currentURL = urlencode($currentURL_01);
	
	
	
	
	/* Get userdata
	--------------------------------------------------------------------------- */
	$result = $mysqli->query("SELECT * FROM ".$db_prefix."users WHERE user_id='".$_SESSION['fuTelldus_user_loggedin']."'");
	$user = $result->fetch_array();

	/* Get page config
	--------------------------------------------------------------------------- */
	$result = $mysqli->query("SELECT * FROM ".$db_prefix."config");
	while ($row = $result->fetch_array()) {
		$config[$row['config_name']] = $row['config_value'];
	}

	/* Set page language
	--------------------------------------------------------------------------- */
	if (empty($user['language'])) $defaultLang = $config['default_language'];
	else $defaultLang = $user['language'];

	include("languages/".$defaultLang.".php");

?>