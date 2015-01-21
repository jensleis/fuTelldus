<?php 

if (isset($_SESSION['fuTelldus_user_loggedin'])) {
	if (isset($_GET['device_id'])) {
		header("Location: ./inc/index.php?page=show");
		exit();
	}

	header("Location: ./inc/index.php?page=register");
	exit();
}


?>