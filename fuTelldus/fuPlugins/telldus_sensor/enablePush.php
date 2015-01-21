
<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	$public_key = $_GET['telldus_public_key'];
	$private_key = $_GET['telldus_private_key'];
	$token = $_GET['telldus_token'];
	$token_secret = $_GET['telldus_token_secret'];
	
	$consumer = connectToTelldus($public_key, $private_key, $token, $token_secret);
	
	$requestParams = array('id'=> '52276', 'enable' => '1');
	$response = $consumer->sendRequest(constant('REQUEST_URI').'/client/setPush	', $requestParams, 'GET');
	
	var_dump($response);
// 	echo $response->getBody();	
exit();

	
	function connectToTelldus($public_key, $private_key, $token, $token_secret) {
		require_once 'C:\Users\jensl\git\fuTelldus\fuTelldus\HTTP/OAuth/Consumer.php';
	
		if (!defined("URL")) define('URL', 'http://api.telldus.com'); //https should be used in production!
		if (!defined("REQUEST_TOKEN")) define('REQUEST_TOKEN', constant('URL').'/oauth/requestToken');
		if (!defined("AUTHORIZE_TOKEN")) define('AUTHORIZE_TOKEN', constant('URL').'/oauth/authorize');
		if (!defined("ACCESS_TOKEN")) define('ACCESS_TOKEN', constant('URL').'/oauth/accessToken');
		if (!defined("REQUEST_URI")) define('REQUEST_URI', constant('URL').'/xml');
	
		if (!defined("BASE_URL")) define('BASE_URL', 'http://'.$_SERVER["SERVER_NAME"].dirname($_SERVER['REQUEST_URI']));
	
		$consumer = new \HTTP_OAuth_Consumer($public_key, $private_key, $token, $token_secret);
		return $consumer;
	}
	
	
?>
