<?php 
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../lib/config.inc.php");
require_once("../../lib/base.inc.php");

unset($_SESSION['fuTelldus_user_loggedin']);
unset($_SESSION['token']);

$_SESSION['token'] = null;
setcookie("fuTelldus_user_loggedin", "", time()-3600);

session_destroy();

exit();

?>