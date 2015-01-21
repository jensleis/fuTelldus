<?php

	/* Messages
	 --------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success autohide autohide'>{$lang['Virtual sensor added']}</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-success autohide autohide'>{$lang['Virtual sensor deleted']}</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-success autohide autohide'>{$lang['Data saved']}</div>";
		if ($_GET['msg'] == 04) echo "<div class='alert alert-success autohide autohide'>{$lang['Virtual sensor updated']}</div>";
	}

	echo "<h4>".$lang['Virtual sensors']."</h4>";

	/* Get parameters
	--------------------------------------------------------------------------- */
	$action = "";
	$getID = "";
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);

	$description = "";
	$sensor_type = "-1";
	$sensor_type_description = "";
	if ($action == "edit") {
		// load data
		$query = "SELECT * FROM ".$db_prefix."virtual_sensors vs, ".$db_prefix."plugins vst where vs.sensor_type = vst.type_int and vs.id='$getID' LIMIT 1";
	    $result = $mysqli->query($query);
	    $row = $result->fetch_array();
		
		$description = $row['description'];
		$sensor_type = $row['sensor_type'];
		$sensor_type_description = $row['type_description'];
		
		// load config data
	}


	/* Form
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Add virtual sensor']}</legend>";

		
			if ($action == "edit") {
				echo "<div class='alert alert-warning'>";
				echo "<form action='?page=settings_exec&action=updateVirtualSensor&id=$getID' method='POST'>";
			} else {
				echo "<div class='well'>";
				echo "<form action='?page=settings_exec&action=addVirtualSensor' method='POST'>";	
			}		
		
			// add hidden field with actual virtual sensor id
			echo "<input type='hidden' name='virtual_sensor_id' id ='virtual_sensor_id' value='$getID' />";
			echo "<input type='hidden' name='user_id' id ='user_id' value='{$user['user_id']}' />";
			
			echo "<table width='100%' id='configValues'>";

				echo "<tr>";
					echo "<td width='25%'>".$lang['Description']."</td>";
					echo "<td width='40%'>";
						echo "<input style='' class='form-control' type='text' name='virtualsensor_description' id='virtualsensor_description' value='$description' />";
					echo "</td>";					
					echo "<td></td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Type']."</td>";
					echo "<td>";
					$disabledSelect = "";
					if ($action == "edit") {
						$disabledSelect = "disabled";
					}
					echo "	<select class='form-control' $disabledSelect name='virtualsensor_type' id ='virtualsensor_type' size='1' selectedIndex='-1'>";
					echo "	  <option value='$sensor_type'>$sensor_type_description</option>";
					// select all available vSensor-Types
					$query = "SELECT * FROM ".$db_prefix."plugins where hidden='0' and plugin_type='sensor' ORDER BY type_int ASC";
					$result = $mysqli->query($query);		
					while($row = $result->fetch_array()) {
						echo "	  <option value='".$row['type_int']."'>".$row['type_description']."</option>";
					}
					echo "	</select>";
					echo "</td>";
					
					echo "<td><div style='margin-left:15px' id='waitlogo'></div></td>";
				echo "</tr>";
				
			echo "</table>";
			echo "<br/><div style='text-align:right;'>";
			if ($action == "edit") {
				echo "<a class='btn btn-default' href='?page=settings&view=virtualsensors'>{$lang['Cancel']}</a> &nbsp; ";
				echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Update sensor']."'/>";		
			} else {
				echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Add sensor']."'/>";		
			}
			echo "</div>";
		echo "</form></div>";

	echo "</fieldset>";

	/* Virtual sensors
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Sensors']}</legend>";
		
		//$query = "SELECT * FROM ".$db_prefix."virtual_sensors WHERE user_id='{$user['user_id']}' ORDER BY description ASC";
		$query = "SELECT * FROM ".$db_prefix."virtual_sensors s, ".$db_prefix."plugins st WHERE user_id='{$user['user_id']}' and st.type_int = s.sensor_type ORDER BY description ASC";
	    $result = $mysqli->query($query);
	    $numRows = $result->num_rows;

	    if ($numRows > 0) {

	    	while($row = $result->fetch_array()) {
		    	echo "<div style='border-bottom:1px solid #eaeaea; margin-left:15px; padding:10px;'>";
				
		    		// Tools
		    		echo "<div style='float:right;'>";

						echo "<div class='btn-group'>";

							$toggleClass = "";
							if ($row['show_in_main'] >= 1){
								$toggleClass = "btn-success";
							} else {
								$toggleClass = "btn-warning";
							}

							if ($row['online'] == 0){
								$toggleClass = "btn-danger";
							}

							echo "<a class='btn dropdown-toggle $toggleClass' data-toggle='dropdown' href='#''>";
								echo "{$lang['Action']}";
								echo "<span class='caret'></span>";
							echo "</a>";

							echo "<ul class='dropdown-menu'>";
								if ($row['show_in_main'] >= 1)
				    				echo "<li><a href='?page=settings_exec&action=putOnMainVirtualSensor&id={$row['id']}'>Remove from main</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=putOnMainVirtualSensor&id={$row['id']}'>Put on main</a></li>";
				    			
								if ($row['online'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=setOnline&id={$row['id']}'>Set offline</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=setOnline&id={$row['id']}'>Set online</a></li>";	
				    			
								echo "<li><a href='?page=settings&view=virtualsensors&action=edit&id={$row['id']}'>Edit</a></li>";
								echo "<li><a href='#sohwDialog' class='sohwDialog' data-id='".$row['id']."' data-user='".$user['user_id']."' data-action='batch' data-include='monitoring.php'>Monitoring</a></li>";								
								
								echo "<li class='divider'></li>";
								echo "<li><a href='#sohwDialog' class='sohwDialog' data-id='".$row['id']."' data-user='".$user['user_id']."' data-action='share' data-include='share_sensor.php'>Share</a></li>";

								
								echo "<li class='divider'></li>";
								echo "<li><a href='#sohwDialog' class='sohwDialog' data-id='".$row['id']."' data-user='".$user['user_id']."' data-action='merge'>Merge</a></li>";
								echo "<li><a href='#sohwDialog' class='sohwDialog' data-id='".$row['id']."' data-user='".$user['user_id']."' data-action='export'>Export</a></li>";
								echo "<li><a href='#sohwDialog' class='sohwDialog' data-id='".$row['id']."' data-user='".$user['user_id']."' data-action='import'>Import</a></li>";
								echo "<li class='divider'></li>";
				    			echo "<li><a href='?page=settings_exec&action=deleteVirtualSensor&id={$row['id']}'>Delete</a></li>";
							echo "</ul>";
						echo "</div>";

		    		echo "</div>";

		    		echo "<div style='font-size:20px;'>".$row['description']."</div>";

		    		echo "<div style='font-size:11px;'>";
		    			echo "<b>{$lang['Type']}:</b> ".$row['type_description']. "<br />";
						//echo "<b>{$lang['Value']}:</b> ".$row["config_value"]. "<br />";
		    			echo "<b>{$lang['Online']}:</b> ".$lang["boolean_".$row['online']]. "<br />";
						echo "<b>{$lang['Monitor']}:</b> ".$lang["boolean_".$row['monitoring']]. "<br />";
		    		echo "</div>";

		    		echo "<div style='font-size:10px'>";
		    			echo $lang["last check"].": ".ago(getLastVirtualSensorCheck($row['id']))."<br /> last update: ".ago(getLastVirtualSensorTimestamp($row['id']));
		    		echo "</div>";

		    	echo "</div>";

		    }

		}

	echo "</fieldset>";

?>

	<!-- model dialog scenes-->
	<div class="modal modal-wide fade" id="sohwDialog">
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
	$(document).on("click", ".sohwDialog", function () {
		var id = $(this).data('id');
		var userID = $(this).data('user');
		var action = $(this).data('action');
		var include = $(this).data('include');
		

		var name = $(this).html();
		
		$("#header-scene-text").text(name);
		$("#modal-body-scene").css("min-height", $(window).height() - 250); // 520
		$("#modal-body-scene").html("<img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/>");
		
		$.ajax({
			  url: "inc/settings/"+include,
			  dataType: "text",
			  method: "get",
			  cache: false,
			  success: function(data) {
				  $("#modal-body-scene").html(data);
			  },
			});
		
		$('#sohwDialog').modal('show');
	});

	$('#virtualsensor_type').change(function () {
	
		var type_int = $(this).val();
		var user_id = $('#user_id').val(); 
		
		$('#configValues').find('tr:gt(1)').remove();
		if (type_int >= 0) {
			$('#waitlogo').html("<img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/>");
		
			var type_id = $('#virtual_sensor_id').val();
			$.ajax({
				url: "inc/plugins/getPluginConfig.php?type_int="+type_int+"&plugin_id="+type_id+"&configType=instance",
					success: function(data) {
						$('#configValues tr:last').after("<tr><td> </td><td><hr /></td><td></td></tr>");
	
					// add all new
					jQuery.each(data , function(index, value){
						var value_description = value.description;
						var value_key = value.value_key;
						var value_type_config = $.csv.toArray(value.value_type, {separator:';'});
						var value_type = value_type_config[0];
						var config_value = value.config_value;
						var id = value.id;
						
						if (value_type.toUpperCase() == "plugin".toUpperCase()) {
							// get all configured plugin instances for this user
							var plugin_type = value_type_config[1];
							var plugin_name = value_type_config[2];	
							$.ajax({
			 					url : "inc/plugins/getPluginInstances.php?type="+plugin_type+"&plugin_name="+plugin_name+"&user_id="+user_id, 
			 					success : function(data1) {
									var htmlElementSelect = "<select class='form-control' name='virtualsensor_value_"+id+"' id='virtualsensor_value_"+id+"'>";
									if (config_value<0) {
										htmlElementSelect += "<option value='-1' selected='selected'></option>";		
									} else {
										htmlElementSelect += "<option value='-1'></option>";
									}
									
									
									jQuery.each(data1 , function(index1, value1){
										var plugin_description = value1.description;
										var plugin_key = value1.id;
										var selected = "";
										if (plugin_key==config_value) {
											selected = "selected='selected'"
										}
										htmlElementSelect += "<option value='"+plugin_key+"' "+selected+">"+plugin_description+"</option>";
									});
									
									htmlElementSelect += "</select>";
									
									$('#configValues tr:last').after("<tr><td>"+value_description+"</td>"+
											"<td>"+htmlElementSelect+"</td><td></td></tr>");
			 					}, 
			 					async: false,
			 					dataType: 'json'
		 					});	// end ajax
							
						} else if (value_type.toUpperCase() == "boolean".toUpperCase()) {
							var selectedTrue, selectedFalse = 0;
							if (config_value.toUpperCase() == "true".toUpperCase()) selectedTrue="selected='selected'";
							if (config_value.toUpperCase() == "false".toUpperCase()) selectedFalse="selected='selected'";
							$('#configValues tr:last').after("<tr><td>"+value_description+"</td><td><select class='form-control' name='virtualsensor_value_"+id+"'><option value='true' "+selectedTrue+">true</option><option value='false' "+selectedFalse+">false</option></select</td><td></td></tr>");
						} else if (value_type.toUpperCase() == "callBackMethodReturnList".toUpperCase()) {
							var method = value_type_config[1];
		
							$.ajax({
			 					url : "inc/plugins/executePluginCallBack.php?type="+type_int+"&method="+method+"&user_id="+user_id, 
			 					success : function(data1) {
			 						var htmlElementSelect = "<select class='form-control' name='virtualsensor_value_"+id+"' id='virtualsensor_value_"+id+"'>";
			 						if (config_value<0) {
			 							htmlElementSelect += "<option value='-1' selected='selected'></option>";		
			 						} else {
			 							htmlElementSelect += "<option value='-1'></option>";
			 						}
			 							
			 							
			 						jQuery.each(data1 , function(index1, value1){
			 							var callBackID = value1.id;
			 							var callBackValue = value1.name;
			 							var selected = "";
			 							if (callBackID==config_value) {
			 								selected = "selected='selected'"
			 							}
			 							htmlElementSelect += "<option value='"+callBackID+"' "+selected+">"+callBackValue+"</option>";
			 						});
			 							
			 						htmlElementSelect += "</select>";
			 						
			 						$('#configValues tr:last').after("<tr><td>"+value_description+"</td>"+
			 								"<td>"+htmlElementSelect+"</td><td></td></tr>");
			 					}, 
			 					async: false,
			 					dataType: 'json'
		 					});	// end ajax
						} else {
							$('#configValues tr:last').after("<tr><td>"+value_description+"</td><td><input class='form-control' type='"+value_type+"' name='virtualsensor_value_"+id+"' id='virtualsensor_value_"+id+"' value='"+config_value+"' /></td><td></td></tr>");
						}
					});
					
					$('#waitlogo').html("");
				},
				fail : function() {
					$('#waitlogo').html("<img style='height:15px; margin-right:8px;' src='images/error.png'/>");
				},
				async: false,
				dataType: 'json'
			}); // end ajax
		} // end if		
	}).trigger('change');
</script>