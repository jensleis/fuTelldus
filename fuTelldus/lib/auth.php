<?php
	
	if (!isset($_SESSION['fuTelldus_user_loggedin'])) {
		
		$_SESSION['request_uri'] = urlencode($_SERVER['REQUEST_URI']);
		header("Location: ./login/");
		exit();
	}

?>