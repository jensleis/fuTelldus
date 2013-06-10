<script>

	jQuery(document).ready(function() {
	  jQuery("abbr.timeago").timeago();
	});

</script>


<style>

	.sensors-wrap {
		padding-left:20px;
	}

	.sensors-wrap .sensor-blocks {
		display:inline-block;
		valign:top;
		margin-right:40px;
		margin-bottom:20px;
		min-width:200px;
		border:0px solid red;
	}

	.sensors-wrap .sensor-blocks img {
		margin-right:10px;
	}

	.sensors-wrap .sensor-name {
		font-size:14px; margin-bottom:0px; font-weight:bold;
	}

	.sensors-wrap .sensor-location {
		font-size:12px; margin-bottom:0px; font-weight:normal;
	}

	.sensors-wrap .sensor-location img {
		height:10px !important;
		margin-left:5px !important;
		margin-right:5px !important;
	}

	.sensors-wrap .sensor-temperature {
		font-size:40px; display:inline-block; valign:top; margin-left:15px; margin-top:6px; margin-bottom:6px; padding-top:10px; border:0px solid red;
	}

	.sensors-wrap .sensor-humidity {
		font-size:40px; display:inline-block; valign:top; margin-left:15px; padding-top:10px; border:0px solid red;
	}

	.sensors-wrap .sensor-timeago {
		font-size:10px; color:#777; text-align: center; padding-top: 8px;
	}

  #sortable { list-style-type: none; }
  #sortable li { }
</style>

<script>
  $(function() {
    $( "#sortable" ).sortable({
    	update: function (event, ui) {
	         var new_pos = ui.item.index()+1;
	         var type = ui.item.data('type');
	         var id = ui.item.data('id');
	         // update new pos into db
	         $.ajax({
	   		  	url: "inc/main/updatePosition.php?id="+id+"&type="+type+"&pos="+new_pos+"",
	   		  	dataType: "text",
	   		  	method: "get",
	   		  	cache: false,
	   		});
        }
    });
    $( "#sortable" ).disableSelection();
  });
  </script>


<?php

	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='?page=settings&view=user'>your userprofile</a>.";
		exit();
	}

	// Margin for desktop and pad
	echo "<div style='height:30px;' class='hidden-phone'></div>";

	// Sensors
	echo "<div class='sensors-wrap'>";
	echo "<ul id='sortable'>";
		/* My sensors
   		--------------------------------------------------------------------------- */
		$query = "select * from (
					SELECT s.sensor_id as id, s.show_in_main as show_in_main, 'sensor' as displaytype FROM ".$db_prefix."sensors s WHERE s.user_id='{$user['user_id']}' AND s.monitoring='1'
					UNION ALL
					SELECT vs.id as id, vs.show_in_main as show_in_main, 'virtual' as displaytype FROM ".$db_prefix."virtual_sensors vs WHERE vs.user_id='{$user['user_id']}' AND vs.show_in_main>='1'
				) u where u.show_in_main>0 ORDER BY u.show_in_main ASC";
		//echo $query;
	    $result = $mysqli->query($query);

	    while ($row = $result->fetch_array()) {
	    	if ($row['displaytype'] == 'sensor'){
	    		$showFromDate = 0;
	    		
	    		$sensorID = trim($row['id']);

	    		$querySub = "SELECT * FROM ".$db_prefix."sensors WHERE sensor_id='$sensorID' LIMIT 1";
	    		$resultSub = $mysqli->query($querySub);
	    		$rowsub = $resultSub->fetch_assoc();
	    		
	    		$queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$sensorID' AND time_updated > '$showFromDate' ORDER BY time_updated DESC LIMIT 1";
	    		$resultS = $mysqli->query($queryS);
	    		$sensorData = $resultS->fetch_array();
	    		
	    		echo "<li class='sensor-blocks well' data-type='sensor' data-id='".$sensorID."'>";
	    		//            echo "<div class='sensor-blocks well'>";
	    		
		    		echo "<div class='sensor-name'>";
		    			echo "{$rowsub['name']}";
		    		echo "</div>";
		    		
		    		echo "<div class='sensor-location'>";
		    			echo "<img src='images/location.png' alt='icon' />";
		    			echo "{$rowsub['clientname']}";
	            	echo "</div>";
		    		
		    		echo "<div class='sensor-temperature'>";
		    			echo "<img src='images/thermometer02.png' alt='icon' />";
	            		echo "{$sensorData['temp_value']}&deg;";
	            	echo "</div>";
	    		
					if ($sensorData['humidity_value'] > 0) {
						echo "<div class='sensor-humidity'>";
							echo "<img src='images/water.png' alt='icon' />";
							echo "{$sensorData['humidity_value']}%";
		            	echo "</div>";
					}
		    		
					echo "<div class='sensor-timeago'>";
						echo "<abbr class=\"timeago\" title='".date("c", $sensorData['time_updated'])."'>".date("d-m-Y H:i", $sensorData['time_updated'])."</abbr>";
					echo "</div>";
				echo "</li>";
	    	
	    	} elseif ($row['displaytype'] == 'virtual'){
	    		$querySub = "SELECT * FROM ".$db_prefix."virtual_sensors WHERE id=".$row['id']." LIMIT 1";
	    		$resultSub = $mysqli->query($querySub);
	    		$sensorType  = $resultSub->fetch_assoc()['sensor_type'];
	    		
	    		$queryVHT = "SELECT * FROM ".$db_prefix."plugins WHERE type_int='$sensorType'";
	    		$resultVHT = $mysqli->query($queryVHT);
	    		$virtualHostType = $resultVHT->fetch_array();
	    		
	    		$actualState = getCurrentVirtualSensorStateWidet($row['id']);
	    		echo "<li class='sensor-blocks well' data-type='virtual' data-id='".$row['id']."'>";
	    		echo $actualState;
	    		echo "</li>";
	    	}
	    }

	    /* Shared sensors
    	--------------------------------------------------------------------------- */
/*	    
		    $query = "SELECT * FROM ".$db_prefix."sensors_shared WHERE user_id='{$user['user_id']}' AND show_in_main>='1' ORDER BY show_in_main ASC";
		    $result = $mysqli->query($query);
		    $numRows = $result->num_rows;

		    if ($numRows > 0) {
			
		    	echo "<h4 style='margin-top:40px;'>{$lang['Shared sensors']}</h4>";

		    	while($row = $result->fetch_array()) {

			    	$xmlData = simplexml_load_file($row['url']);
					echo "<li class='usensor-blocks well'>";
			    	//echo "<div class='sensor-blocks well'>";

		            	echo "<div class='sensor-name'>";
		            		echo $row['description'];
		            	echo "</div>";

		            	echo "<div class='sensor-location'>";
		            		echo "<img src='images/location.png' alt='icon' />";
		            		echo $xmlData->sensor->location . " (".$xmlData->sensor->name.")";
		            	echo "</div>";

		            	echo "<div class='sensor-temperature'>";
		            		echo "<img src='images/thermometer02.png' alt='icon' />";
		            		echo $xmlData->sensor->temp . "&deg;";
		            	echo "</div>";

		            	if ($xmlData->sensor->humidity > 0) {
		            		echo "<div class='sensor-humidity'>";
			            		echo "<img src='images/water.png' alt='icon' />";
			            		echo $xmlData->sensor->humidity . "%";
			            	echo "</div>";
		            	}

		            	echo "<div class='sensor-timeago'>";
		            		echo "<abbr class=\"timeago\" title='".date("c", trim($xmlData->sensor->lastUpdate))."'>".date("d-m-Y H:i", trim($xmlData->sensor->lastUpdate))."</abbr>";
		            	echo "</div>";

		            //echo "</div>";
					echo "</li>";
			    }
			}
*/		
		
		/* Virtual sensors
    	--------------------------------------------------------------------------- */
	    
/*		    $query = "SELECT * FROM ".$db_prefix."virtual_sensors WHERE user_id='{$user['user_id']}' AND show_in_main>='1' ORDER BY show_in_main ASC";
		    $result = $mysqli->query($query);

		    while ($row = $result->fetch_array()) {
	    	
				$sensorType = trim($row['sensor_type']);
				
				$queryVHT = "SELECT * FROM ".$db_prefix."plugins WHERE type_int='$sensorType'";
				$resultVHT = $mysqli->query($queryVHT);
				$virtualHostType = $resultVHT->fetch_array();

				$actualState = getCurrentVirtualSensorStateWidet($row['id']);
				echo "<li class='sensor-blocks well'>";
				echo $actualState;
				echo "</li>";
			}*/

	echo "</ul>";
	echo "</div>";

?>