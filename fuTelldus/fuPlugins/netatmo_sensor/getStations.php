<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once 'API/NAApiClient.php';
	require_once 'API/Config.php';
	
	$config = array();
	$config['client_id'] = $client_id;
	$config['client_secret'] = $client_secret;
	
	$access_token = $_POST['access_token'];
	$config['access_token'] = $access_token;

	$client = new NAApiClient($config);
	
	$deviceList = $client->api("devicelist");
	
	echo json_encode($deviceList);
	
	exit();
?>
