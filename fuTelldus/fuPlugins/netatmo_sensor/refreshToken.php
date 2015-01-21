<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once 'API/NAApiClient.php';
	require_once 'API/Config.php';
	
	$config = array();
	$config['client_id'] = $client_id;
	$config['client_secret'] = $client_secret;
	
	
	$refreshToken = $_POST['refresh_token'];
	
	$client = new NAApiClient($config);
	
	try
	{
		$token_array = array();
		$token_array['refresh_token'] = $refreshToken;
		$client->setTokensFromStore($token_array);
		
		$tokens = $client->getAccessToken();
		$access_token = $tokens["access_token"];
		
	}
	catch(NAClientException $ex)
	{
		exit($ex->getMessage());
	}
	
	echo $access_token;
	
// 	exit();
?>
