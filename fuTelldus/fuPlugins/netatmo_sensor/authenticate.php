<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once 'API/NAApiClient.php';
	require_once 'API/Config.php';
	
	$config = array();
	$config['client_id'] = $client_id;
	$config['client_secret'] = $client_secret;
// 	$config['redirect_uri']= "localhost";
	//application will have access to station and theromstat
	$config['scope'] = 'read_station';
	
	$client = new NAApiClient($config);
	
	$username = $_POST['netatmo_user'];
	$pwd = $_POST['netatmo_passwd'];
	$client->setVariable("username", $username);
	$client->setVariable("password", $pwd);
	
	try
	{
		$tokens = $client->getAccessToken();
		$refresh_token = $tokens["refresh_token"];
		$access_token = $tokens["access_token"];
		
	}
	catch(NAClientException $ex)
	{
		exit($ex->getMessage());
	}
	
	echo $refresh_token;
	
// 	exit();
?>
