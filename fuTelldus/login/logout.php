<?php
	ob_start();
	require ("../lib/base.inc.php");
	require_once 'HTTP/OAuth/Consumer.php';
	
	unset($_SESSION['fuTelldus_user_loggedin']);

	setcookie("fuTelldus_user_loggedin", "", time()-3600);

	session_destroy(); 
	
	header("Location: index.php?msg=02");
	exit();
?>