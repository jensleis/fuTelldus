<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', '1' );

require ("../lib/base.inc.php");
set_include_path ( get_include_path () . PATH_SEPARATOR . '../lib/packages' );
require_once ("Google/Client.php");
require_once ("Google/Service/Oauth2.php");

$client_id = '986456351171-i2ci2tkej2ii8u097m0m39mvk7o184mq.apps.googleusercontent.com';
$client_secret = 'MXGffTH6esBE24WYSh2mBx9f';
$redirect_uri = 'http://localhost/fuTelldus/login/google_login.php';

$client = new Google_Client ();
$client->setClientId ( $client_id );
$client->setClientSecret ( $client_secret );
$client->setRedirectUri ( $redirect_uri );
$client->setDeveloperKey ( 'AIzaSyCjJAeO-Svx_iX-ONKEvs4JQbBMCwDDP9A' ); // API key
$client->setApprovalPrompt ( "auto" ); //force

$client->addScope ( "https://www.googleapis.com/auth/userinfo.profile" );
$client->addScope ( "https://www.googleapis.com/auth/userinfo.email" );

$oAuthService = new Google_Service_Oauth2 ( $client );

// get the token from the session, if existant
$token = $_SESSION ['token'];

// refresh the token from the session
if (isset($token) && $client->isAccessTokenExpired ()) {
	$token = $client->refreshToken ( $token );
	$_SESSION ['token'] = $token;
}

if (isset ( $_GET ['code'] )) {
	$client->authenticate ( $_GET ['code'] );
	$token = $client->getAccessToken ();
	$_SESSION ['token'] = $token->refresh_token;
}

if (! $client->getAccessToken ()) { // auth call to google
	$authUrl = $client->createAuthUrl ();
	header ( "Location: " . $authUrl );
	die ();
}

// check if the user is already in the database
// if yes, login is successfull --> set internal userid in session
// if not, redirect to register page
$userinfo = $oAuthService->userinfo->get ( 'me' );
$userid = $userinfo->id;

$query = "select user_id from " . $db_prefix . "users where account_type=1 and provider_id='" . $userid . "'";
// echo $query;
$result = $mysqli->query ( $query );
$userIDArr = $result->fetch_assoc ();

if (sizeof ( $userIDArr ) == 1) {
	$_SESSION ['fuTelldus_user_loggedin'] = $userIDArr ['user_id'];
	
	// update refresh_token in database
	$query = "update " . $db_prefix . "users set provider_token='".$token."' where user_id='".$userIDArr ['user_id']."'";
	$mysqli->query($query);
	
	header ( "Location: ../index.php" );
	exit ();
} else {
	// unset the session-user
	// the token should persist in the session
	unset ( $_SESSION ['fuTelldus_user_loggedin'] );

	header ( "Location: ./registerUser.php" );
	exit ();
}
?>
