<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', '1' );

require ("../../lib/base.inc.php");
// require ("../authDevice.php");

if (isset($_GET['page'])) {
	$page = $_GET['page'];
}

$user_id=$_SESSION['fuTelldus_user_loggedin'];
$device_desc="";
if (isset($_GET['device'])){
	$device_id = clean($_GET['device']);
	
	$query = " SELECT * FROM ".$db_prefix."displays
	WHERE display_id='{$device_id}'
	";
	
	$result = $mysqli->query($query);
	$deviceResult = $result->fetch_assoc();
	$device_desc=$deviceResult['name'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<title><?php echo $config['pagetitle']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content=""> 

<link href="../css/display.css" rel="stylesheet">


<!-- Jquery -->
<script src="../../lib/packages/jquery/jquery-1.9.1.min.js"></script>
<script src="../../lib/jscripts/jquery.bootstrap.confirm.popover.js"></script>

<script	src="../../lib/packages/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
<link href="../../lib/packages/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css" rel="stylesheet">

<script src="../../lib/packages/timeago_jquery/jquery.timeago.js"></script>
<script src="../../lib/packages/jquery_csv/jquery_csv-0_71.min.js"></script>
	

<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>

<!-- FontAwsome -->
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
<link rel="stylesheet" href="../../lib/packages/weather-icons/css/weather-icons.min.css">


<!-- Jasny Bootstrap -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>


<script src="../../lib/jscripts/futelldus_functions.js"></script>




<script type="text/javascript">
$(function() {
	$("#wait").hide();
});
</script>


</head>
	<body style="overflow:hidden;">
	
	<input type="hidden" id="current_device_id" value="<?php echo $device_id; ?>" />
	
		<div class="container-fluid" style="height:100%;">
			<div class="row" style="height:100%">
				<!--  navigation -->			
				<div class="col-xs-1 col-md-1" style="height:100%;margin:0px;padding:0px">
					<?php if ($page!='register') {?>
						<nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-left offcanvas" style="width:100px" role="navigation">
	 		  				
	 		  				<?php 
	 		  				$navSensors_active = "";
	 		  				$navDevices_active = "";
	 		  				
	 		  				if (!isset($_GET['page']) || $_GET['page'] == "sensors") $navSensors_active = "active";
	 		  				elseif (substr($_GET['page'], 0, 7) == "devices") $navDevices_active = "active";
	 		  				?>
	 		  				
		  					<ul class="nav navmenu-nav">
			    				<li class="<?php echo $navSensors_active; ?>"><a href="?page=sensors&device=<?php echo $device_id;?>">Sensors</a></li>
			    				<li class="<?php echo $navDevices_active; ?>"><a href="?page=devices&device=<?php echo $device_id;?>">Devices</a></li>
			    				<li><a hre f="../">Reload</a></li>
			  				</ul>
						</nav>
			
					  	<button type="button" class="btn-white icon-align-justify " data-toggle="offcanvas" data-target="#myNavmenu" data-canvas="body" style="margin-left:5px;padding: 0px 0px 0px 0px;border:none;">
						    
					  	</button>
					  	<span id="wait" style="">
							<img src='../../images/ajax-loader2.gif' style="padding:5px;"/>
						</span>
					<?php }?>
				</div>				
				<div class="col-xs-11 col-md-11" style="height:100%;padding:0px;">
					<div class="container-fluid" style="height:100%;text-align:center;">
						<?php include("../include_script.inc.php"); ?>
					</div>
				</div>

			</div>
		</div>
	</body>
</html>
