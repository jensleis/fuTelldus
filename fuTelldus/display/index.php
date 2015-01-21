<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', '1' );

require ("../lib/base.inc.php");
require ("authDevice.php");

?>


<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<title><?php echo $config['pagetitle']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content=""> 

<link href="css/display.css" rel="stylesheet">


<!-- Jquery -->
<script src="../lib/packages/jquery/jquery-1.9.1.min.js"></script>
<script src="../lib/jscripts/jquery.bootstrap.confirm.popover.js"></script>

<script	src="../lib/packages/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
<link href="../lib/packages/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css" rel="stylesheet">

<script src="../lib/packages/timeago_jquery/jquery.timeago.js"></script>
<script src="../lib/packages/jquery_csv/jquery_csv-0_71.min.js"></script>
	

<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
<script src="../lib/packages/bootstrap/js/bootstrap.min.js"></script>
<link href="../lib/packages/bootstrap-editable-1.4.4/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet">
<script	src="../lib/packages/bootstrap-editable-1.4.4/bootstrap-editable/js/bootstrap-editable.js"></script>

<!-- FontAwsome -->
<link rel="stylesheet" href="../lib/packages/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="../lib/packages/weather-icons/css/weather-icons.min.css">

<script src="../lib/jscripts/futelldus_functions.js"></script>


<script type="text/javascript">
	$(function() {
		$("#login").hide();
		$("#wait").show();
		
		var device_id = localStorage.getItem("myhopo_device_id");
// 		var device_id = getCookie("myhopo_device_id");

		 $.ajax( "api/authenticate.php?myhopo_device_id="+device_id+"" )
			 .done(function(returnVal) {
				 $("#wait").hide();
				 $("#device_id").val(device_id);
				 
				 if (device_id!='' && returnVal == device_id ) {
					 window.location = "./inc/index.php?page=sensors&device="+device_id;
				 } else  {
					 $("#login").show();
				 }
				 
			 });
	});
	
</script>


</head>
	<body>
	 	<div class="container" style="height:100%; text-align:center" id="content">

	 		<div id="wait">
	 			<img src='../images/ajax-loader2.gif'/>
	 		</div>
	 		<div id="login">
	 			<div class="row">
	 				<b>
	 				This device is not registered. Login to link this device to an existing user.
	 				</b>	
	 			</div>
	 			<div class="row" style="margin-top:15px">
	 				<div class="col-xs-12">
		 				<form class="form-signin" action="../login/login_exec.php?source=display" method="POST">
							<input type="text" class="form-control" name="mail" id="mail" placeholder="Email" required autofocus>
							<input type="password" class="form-control" name="password" placeholder="Password" style="margin-top:15px" required>
							<input type="hidden" name="device_id"/>
						 	<?php
          						// Create a random key to secure the login from this form!
          						$_SESSION['secure_fuCRM_loginForm'] = "fuTelldus3sfFwer35tF36¤234%&".time()."254543";
          						$hashSecureFormLogin = hash('sha256', $_SESSION['secure_fuTelldus_loginForm']);
          						echo "<input type='hidden' name='uniq' value='$hashSecureFormLogin' />";
        					?>
        
							<button class="btn-white btn-block" type="submit" style="margin-top:15px;" >Login</button>
						</form>
					</div>
	 			</div>
	 		</div>
	 	</div>
	</body>
</html>