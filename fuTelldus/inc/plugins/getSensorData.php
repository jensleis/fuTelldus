<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once("../../lib/config.inc.php");
	require_once("../../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 
	 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");
	

	// get parameter
	$sensorID = clean($_GET['sensor_id']);
	$user_id = clean($_GET['user_id']);
	
	$sensorName = getVirtualSensorDescriptionToSensorId($sensorID);
	$sensorType = getVirtualSensorTypeDescriptionToSensorId($sensorID);
	$lastUpdate = getLastVirtualSensorTimestamp($sensorID);
	$lastCheck = getLastVirtualSensorCheck($sensorID);
	
	$returnParams = getPluginReturnParameter($sensorID);
?>


	<ul class="nav nav-tabs" id="dataTab">
		<li class="active"><a href="#meta">Meta data</a></li>
		<li><a href="#overview">Data: Overview</a></li>
	  	<li><a href="#humidity">Data: Humidity</a></li>
	  	<li><a href="#temperature">Data: Temperature</a></li>
	</ul>

    <div id='content' class="tab-content">
      <div class="tab-pane active" id="meta">
      	<div class="row" style="margin-top:15px">
      		<div class="col-md-2">Sensor-ID:</div>
      		<div class="col-md-4"><?php echo $sensorID?></div>
      	</div>
		<div class="row">
			<div class="col-md-2">Sensor-Name:</div>
      		<div class="col-md-4"><?php echo $sensorName?></div>
		</div>
		<div class="row">
			<div class="col-md-2">Sensor-Type:</div>
      		<div class="col-md-4"><?php echo $sensorType?></div>
		</div>
		<div class="row">
			<div class="col-md-2">Last update:</div>
      		<div class="col-md-4"><?php echo date("F j, Y - g:i a ", $lastUpdate)." (".ago($lastUpdate).")"?></div>
		</div>
		<div class="row">
			<div class="col-md-2">Last check:</div>
      		<div class="col-md-4"><?php echo date("F j, Y - g:i a ", $lastCheck)." (".ago($lastCheck).")"?></div>
		</div>
		
		<!-- table with return values and count of logs -->
		<div class="row" style="margin-top:30px">
			<div class="col-md-2">Return values:</div>
      		<div class="col-md-4">
      		<?php 	
      			foreach (array_keys($returnParams) as $key) {
					echo "<div class='row'><div class='col-md-9'>";
					echo $returnParams[$key]['description'];
					$countLogs = getCountSensorLogToReturnParameter($sensorID, $key);
					echo " (".$countLogs." log entries archived)";
					echo "</div></div>";
				}
      		?>
      		</div>
		</div>
		
		<!-- Merge-funtion. Not sure if here or with an extra tab 
		<div class="row" style="margin-top:30px">
			<div class="col-md-2">Merge with other sensor:</div>
      		<div class="col-md-4"><?php echo ""?></div>
		</div> -->
      </div>
      <div class="tab-pane" id="overview">
      	Overview
      </div>
      <div class="tab-pane" id="humidity">
      	data humidity
      </div>
      <div class="tab-pane" id="temperature">
      	data temperature
      </div>
    </div> 
    

	<script type="text/javascript">	
	    $('#dataTab a').click(function (e) {
		  e.preventDefault()
		  $(this).tab('show')
		});
	</script>
