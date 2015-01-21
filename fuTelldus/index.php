

<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', '1' );

require ("lib/base.inc.php");
require ("lib/auth.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<title><?php echo $config['pagetitle']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content=""> 

<link href="css/pagestyle.css" rel="stylesheet">


<!-- Jquery -->
<script src="lib/packages/jquery/jquery-1.9.1.min.js"></script>
<script src="lib/jscripts/jquery.bootstrap.confirm.popover.js"></script>

<script	src="lib/packages/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
<link href="lib/packages/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css" rel="stylesheet">

<script src="lib/packages/timeago_jquery/jquery.timeago.js"></script>
<script src="lib/packages/jquery_csv/jquery_csv-0_71.min.js"></script>
	
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nobile">
	
	
	<?php
	if ($defaultLang == "no")
		echo "<script src=\"lib/packages/timeago_jquery/jquery.timeago.no.js\"></script>";
	?>


	<!-- Bootstrap framework -->
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
<script src="lib/packages/bootstrap/js/bootstrap.min.js"></script>
<link href="lib/packages/bootstrap-editable-1.4.4/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet">
<script	src="lib/packages/bootstrap-editable-1.4.4/bootstrap-editable/js/bootstrap-editable.js"></script>

<!-- FontAwsome -->
<link rel="stylesheet" href="lib/packages/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="lib/packages/weather-icons/css/weather-icons.min.css">
	
<link href="https://gitcdn.github.io/bootstrap-toggle/2.1.0/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.1.0/js/bootstrap-toggle.min.js"></script>

<!-- For iPhone 4 Retina display: -->
<link rel="apple-touch-glyphicon glyphicon-precomposed" sizes="114x114"	href="images/thermometer.png">

<!-- For iPad: -->
<link rel="apple-touch-glyphicon glyphicon-precomposed" sizes="72x72" href="images/thermometer.png">

<!-- For iPhone: -->
<link rel="apple-touch-glyphicon glyphicon-precomposed"	href="images/thermometer.png">

<script src="lib/jscripts/futelldus_functions.js"></script>

<script type="text/javascript">
		idleTime = 0;
		
		$(document).ready(function () {
			window.setTimeout(function() { $(".autohide").alert('close'); }, 5000);
			
		    //Increment the idle time counter every minute.
		    var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

		    //Zero the idle timer on mouse movement.
		    $(this).mousemove(function (e) {
		        idleTime = 0;
		    });
		    $(this).keypress(function (e) {
		        idleTime = 0;
		    });
		});
		
		
		function timerIncrement() {
		    idleTime = idleTime + 1;
		    if (idleTime > 19) { // 20 minutes
		        window.location.reload();
		    }
		}
	</script>

</head>
<body
	class="home page page-id-217 page-template page-template-template-homepage-php body-class">
	<header id="header" style="background-color: #5B7A93;">

		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class=" entry-info-social clearfix">
						
							<div class="row">
								<div class="col-lg-7 hidden-xs hidden-sm" style="color:#FFFFFF; padding-top:4px">
									<strong>Control</strong> and <strong>monitor</strong> your <strong>home</strong> from everwhere ...							
								</div> <!-- /topinfo_block_width -->
			
								<div class="col-lg-2 hidden-xs hidden-sm">
									<ul class="follow-block">				
										<li><a target="_blank" title="Apple" href="http://www.apple.com"><i class="fa fa-apple"></i></a></li>
										<li><a target="_blank" title="Android" href="http://www.android.com"><i class="fa fa-android"></i></a></li>
										<li><a target="_blank" title="Google Plus" href="http://www.google.com"><i class="fa fa-google-plus"></i></a></li>
										<li><a target="_blank" title="Facebook" href="http://www.facebook.com"><i class="fa fa-facebook"></i></a></li>
									</ul>							
								</div>
								
								<div class="col-lg-3 col-xs-12 col-sm-12">
										
									<div class="btn-group pull-right" style="margin-top:-5px; margin-bottom:-5px; float: right;">
										<a class="btn dropdown-toggle" data-toggle="dropdown" href="#" style="color:#FFFFFF">
											<?php
											echo $user ['mail'];
											?>
											<span class="caret"></span>
										</a>
				
										<ul class="dropdown-menu">
											<?php
											echo "<li><a href='?page=settings&view=user'>" . $lang ['My profile'] . "</a></li>";
											echo "<li><a href='./public/index.php'>" . $lang ['View public page'] . "</a></li>";
											echo "<li><a href='./login/logout.php'>" . $lang ['Log out'] . "</a></li>";
											?>
										</ul>
									</div>	
								</div>
								
							</div>
										
					</div>	
				</div> <!-- /socialicons_block_width -->
			</div> <!-- /entry-info-social -->
		</div> <!-- col-lg-12 -->		
	
	
		<div class="container" >
			<div class="row top-block" style="padding-top:30px; padding-bottom:30px"">
				<div class="col-lg-5 logo-block">
					<div id="logo">
						<a href='index.php'> <img style='height: 64px;' src="images/logo.gif" alt='logo' />
<!-- 						<span>MyHoPo</span> -->
<!-- 						MyHomeInMyPocket -->
<!-- 						MyHoPo -->
							<?php 
// 							echo $config['pagetitle']; 
							?>
						</a>
					</div> <!-- /logo -->
				</div><!-- /logo-block -->

				<div class="col-lg-7 right-block">
					
				</div> <!-- /col-lg-7 -->
			</div>
		</div>

		<div class="masthead hidden-md hidden-lg">
		
			<nav class="navbar navbar-default" role="navigation">
			   <div class="navbar-header">
			      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example-navbar-collapse">
			         <span class="sr-only">Toggle navigation</span>
			         <span class="icon-bar"></span>
			         <span class="icon-bar"></span>
			         <span class="icon-bar"></span>
			      </button>
			      <a class="navbar-brand" href="#">Navigation</a>
			   </div>
			   <div class="collapse navbar-collapse" id="example-navbar-collapse">
			      <ul class="nav navbar-nav">
			         <li><a href="index.php">Home</a></li>
				    <li><a href="?page=sensors">Sensors</a></li>
				    <li><a href="?page=devices">Devices</a></li>
				    <li><a href="?page=flows">Flows</a></li>
				    <li><a href="?page=chart">Chart</a></li>
				    <li><a href="?page=settings">Settings</a></li>
			      </ul>
			   </div>
			</nav>
		</div>
		
		
		<div class="masthead hidden-xs hidden-sm">
			<div id="mainmenu-block-bg" style="top: 0px; padding-top: 0px; background: none repeat scroll 0% 0% rgb(66, 88, 106); left: 0px; z-index: 11; border-top: 4px solid #FFFFFF; text-align:center">
				<div class="container" style="">
				  <div class="row" style="">
				  	<div class="col-lg-12" style="">
				  		<div id="menu" style="display:inline-block; margin-top:15px; margin-bottom:10px;line-height:22px;">
				  			<div style="line-height:22px;">
						    <ul class="nav navbar-nav" style="">
								<?php
									$navMainpage_active = "";
									$navSensors_active = "";
									$navDevices_active = "";
									$navChart_active = "";
									$navFlows_active = "";
									$navReport_active = "";
									$navSettings_active = "";
								
									// Set menuelements as active
									if (!isset($_GET['page']) || $_GET['page'] == "mainpage") $navMainpage_active = "active";
									elseif (substr($_GET['page'], 0, 7) == "sensors") $navSensors_active = "active";
									elseif (substr($_GET['page'], 0, 7) == "devices") $navDevices_active = "active";
									elseif (substr($_GET['page'], 0, 5) == "chart") $navChart_active = "active";
									elseif (substr($_GET['page'], 0, 6) == "flows") $navFlows_active = "active";
									//elseif (substr($_GET['page'], 0, 6) == "report") $navReport_active = "active";
									elseif (substr($_GET['page'], 0, 8) == "settings") $navSettings_active = "active";
								?>
									<li id="menu-item-127" class="custom-menu-item menu-item">
										<a class="custom-menu-item-test <?php echo $navMainpage_active; ?>" href="index.php" onmouseover="this.style.backgroundColor='#42586A'"><?php echo $lang['Home']; ?></a>
									</li>
									<li id="menu-item-128" class="custom-menu-item menu-item ">
										<a class="custom-menu-item-test <?php echo $navSensors_active; ?>" href="?page=sensors" onmouseover="this.style.backgroundColor='#42586A'"><?php echo $lang['Sensors']; ?></a>
									</li>
									<li id="menu-item-129" class="custom-menu-item menu-item">
										<a class="custom-menu-item-test <?php echo $navDevices_active; ?>" href="?page=devices" onmouseover="this.style.backgroundColor='#42586A'"><?php echo $lang['Lights']; ?></a>
									</li>
									<li id="menu-item-130" class="custom-menu-item menu-item">
										<a class="custom-menu-item-test <?php echo $navFlows_active; ?>" href="?page=flows" onmouseover="this.style.backgroundColor='#42586A'"><?php echo $lang['Flows']; ?></a>
									</li>
									<li id="menu-item-131" class="custom-menu-item menu-item">
										<a class="custom-menu-item-test <?php echo $navChart_active; ?>" href="?page=chart" onmouseover="this.style.backgroundColor='#42586A'"><?php echo $lang['Chart']; ?></a>
									</li>
									<li id="menu-item-132" class="custom-menu-item menu-item custom-menu-item-last">
										<a class="custom-menu-item-test <?php echo $navSettings_active; ?>" href="?page=settings" onmouseover="this.style.backgroundColor='#42586A'"><?php echo $lang['Settings']; ?></a>
									</li>
								</ul>
								</div>
							</div>
						</div>
						<!-- /span12 -->
					</div>
					<!-- /row -->
				</div>
				<!-- /container -->
			</div>
		</div>
	</header>

	<div id="content" class="container-full" >
		<?php include("include_script.inc.php"); ?>
	</div>

	<div class='clearfix'></div>

	<div class='hidden-xs'
		style='text-align: center; border-top: 1px solid #eaeaea; font-size: 10px; margin-top: 35px; color: #c7c7c7;'>
			Developed by <a href='http://www.fosen-utvikling.no'>Fosen Utvikling</a> &nbsp;&nbsp;
			Last load: <?php echo date("d-m-Y H:i"); ?>

			<br /> This work is licensed under a <a
			href='http://creativecommons.org/licenses/by-nc/3.0/'>Creative
			Commons Attribution-NonCommercial 3.0 Unported License</a>.
	</div>


<!-- 	</div> -->
	<!-- /container -->

</body>
</html>