<?php
	echo "<div class='hidden-xs' style='height:30px;'></div>";

	echo "<div class='col-md-2'></div>";
	
	echo "<div class='col-md-8'>";
	
			
		/* Messages
		--------------------------------------------------------------------------- */
		if (isset($_GET['msg'])) {
			if ($_GET['msg'] == 01) echo "<div class='alert alert-success autohide'>".$lang['Sensor added to monitoring']."</div>";
			if ($_GET['msg'] == 02) echo "<div class='alert alert-success autohide'>".$lang['Sensor removed from monitoring']."</div>";
		}

		/* Headline
		 --------------------------------------------------------------------------- */
		echo "<h3 class='hidden-xs'>".$lang['Sensors']."</h3>";
		
		echo "<div class='hidden-xs'>{$lang['Sensors description']}</div>";
	
		/* Sensors
		--------------------------------------------------------------------------- */
	
		echo "<div class='table-responsive'>";
			echo "<table class='table table-striped'>";
				echo "<thead class='hide-xs'>";
					echo "<tr>";
						echo "<th>".$lang['Name']."</th>";
						echo "<th>Sensor data</th>";
						echo "<th>".$lang['Last update']."</th>";
						echo "<th>Sensor type</th>";
						echo "<th width='23%'></th>";
					echo "</tr>";
				echo "</thead>";
				
				echo "<tbody>";
					$result = getSensors($user['user_id']);
	
					while ($row = $result->fetch_array()) {
						
						echo "<tr>";
							echo "<td>";
								echo "<a class='showSensorData' href='#showSensorData' data-toggle='modal' id='show_sensor_data_".$row['id']."' data-name='".$row['description']."' data-sensorid='".$row['id']."' data-user='".$user['user_id']."'>". $row['description'] . "</a>";
								echo "<div class='visible-xs'>" . ago($row['last_update']) . "</div>";
							echo "</td>";
							
	
							echo "<td>";
							echo "<div class='sensor_val' id='sensor_".$row['id']."' data-user='".$user['user_id']."' data-id='".$row['id']."'>";
							echo "<img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/>";
							echo "</div>";
							echo "</td>";

							if (isset($row['last_update']) && $row['last_update']>0) {
								echo "<td class='hidden-xs'>" . ago($row['last_update']) . "</td>";
							} else {
								echo "<td class='hidden-xs'>-</td>";
							}
	
							echo "<td class='hidden-xs'>".getPluginTypeDescription($row['sensor_type'])."</td>";
	
							echo "<td class='hidden-xs' style='text-align:right;'>";
								if ($row['monitoring'] == 0) {
									echo "<a class='btn btn-success' href='?page=sensors_exec&action=setMonitoring&id={$row['id']}'>Enable Monitoring</a>";
								} else {
									echo "<a class='btn btn-danger' href='?page=sensors_exec&action=setMonitoring&id={$row['id']}'>Disable Monitoring</a>";
								}
							echo "</td>";
	
						echo "</tr>";
					}
	
	
				echo "</tbody>";
			echo "</table>";
		echo "</div>";
	echo "</div>";
		
	echo "<div class='col-md-2'></div>";		

?>

	<!-- model dialog scenes-->
	<div class="modal modal-wide fade" id="showSensorData">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="modal-header-scene">
					<a class="close" data-dismiss="modal">&times;</a>
					<div class="header" id="header-scene">
						<h3 id="header-scene-text"></h3>
					</div>
				</div>
				<div class="modal-body" id="modal-body-scene" style="text-align:left">
				</div>
				<div class="modal-footer" id="modal-footer-scene">
<!-- 					<a href="#" class="btn btn-success" id='save_scene' onClick="javascript: saveScene();">Save</a>&nbsp; -->
					<a href="#" class="btn btn-primary" data-dismiss="modal"><?php echo $lang['Close'] ?></a>
				</div>
			</div>
		</div>
	</div>	

<script type="text/javascript">
$(function() {
// echo "<td><small class='sensor_val' id='sensor_".$row['id']."'></small></td>";
	$('.sensor_val').each(function(i, obj) {
		var objID = $(obj).data('id');
		var userID = $(obj).data('user');	
		var url = 'inc/plugins/getSensorState.php?user_id='+userID+'&sensor_id='+objID;
		$.getJSON(url, function(data) {
			$(obj).fadeOut("fast");
			$(obj).html("");
			 $.each( data, function( key, val ) {
				 $(obj).html($(obj).html() + key+": "+val + "<br />");
			 });
			 $(obj).fadeIn("normal");
		}).fail(function() {
			$(obj).fadeOut("fast");
			$(obj).html("<img style='height:15px; margin-right:8px;' src='images/error.png'/>");
			$(obj).fadeIn("normal");
		})
	});
});


$(".modal-wide").on("show.bs.modal", function() {
	  var height = $(window).height() - 200;
	  $(this).find(".modal-body").css("max-height", height);
	});

$(document).on("click", ".showSensorData", function () {
	var name = $(this).data('name');
	var sensorID = $(this).data('sensorid');
	var userID = $(this).data('user');
	
	$("#header-scene-text").text(name);
	$("#modal-body-scene").css("min-height", $(window).height() - 250); // 520
	$("#modal-body-scene").html("<img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/>");
	
	$.ajax({
		  url: "inc/plugins/getSensorData.php?sensor_id="+sensorID+"&user_id="+userID+"",
		  dataType: "text",
		  method: "get",
		  cache: false,
		  success: function(data) {
			  $("#modal-body-scene").html(data);
		  },
		});
	
	$('#showSensorData').modal('show');
});
</script>