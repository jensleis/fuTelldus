<?php
	/* Messages
	 --------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success autohide'>{$lang['Virtual device added']}</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-success autohide'>{$lang['Virtual device deleted']}</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-success autohide'>{$lang['Data saved']}</div>";
		if ($_GET['msg'] == 04) echo "<div class='alert alert-success autohide'>{$lang['Virtual device updated']}</div>";
	}

	echo "<h4>".$lang['Virtual devices']."</h4>";

	/* Get parameters
	--------------------------------------------------------------------------- */
	$action = "";
	$getID = "";
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);

	$description = "";
	$plugin_id = "-1";
	$plugin_description = "";
	if ($action == "edit") {
		// load data
		$query = "SELECT * FROM ".$db_prefix."virtual_devices vd, ".$db_prefix."plugins p where vd.plugin_id = p.type_int and vd.id='$getID' LIMIT 1";
	    $result = $mysqli->query($query);
	    $row = $result->fetch_array();
		
		$description = $row['description'];
		$plugin_id = $row['plugin_id'];
		$plugin_description = $row['type_description'];
		
		// load config data
	}


	/* Form
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Add virtual device']}</legend>";

		
			if ($action == "edit") {
				echo "<div class='alert alert-warning'>";
				echo "<form action='?page=settings_exec&action=updateVirtualDevice&id=$getID' method='POST'>";
			} else {
				echo "<div class='well'>";
				echo "<form action='?page=settings_exec&action=addVirtualDevice' method='POST'>";	
			}		
		
			// add hidden field with actual virtual device id
			echo "<input type='hidden' name='virtual_device_id' id ='virtual_device_id' value='$getID' />";
			echo "<input type='hidden' name='user_id' id ='user_id' value='{$user['user_id']}' />";
			
			echo "<table width='100%' id='configValues'>";

				echo "<tr>";
					echo "<td width='25%'>".$lang['Description']."</td>";
					echo "<td width='40%'>";
						echo "<input class='form-control' type='text' name='virtualdevice_description' id='virtualdevice_description' value='$description' />";
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
					echo "	<select class='form-control' $disabledSelect name='plugin_id' id ='plugin_id' size='1' selectedIndex='-1'>";
					echo "	  <option value='$plugin_id' '>$plugin_description</option>";
					// select all available plugin-Types
					$query = "SELECT * FROM ".$db_prefix."plugins where hidden='0' and plugin_type='device' ORDER BY type_int ASC";
					$result = $mysqli->query($query);		
					while($row = $result->fetch_array()) {
						echo "	  <option value='".$row['type_int']."'>".$row['type_description']."</option>";
					}
					echo "	</select>";
					echo "</td>";
// 					echo "<div id='waitlogo'></div>";
					echo "<td><div style='margin-left:15px' id='waitlogo'></div></td>";	
				echo "</tr>";
				
			echo "</table>";
			
			
			
			echo "<br/><div style='text-align:right;'>";
			if ($action == "edit") {
				echo "<a class='btn btn-default' href='?page=settings&view=virtualdevices'>{$lang['Cancel']}</a> &nbsp; ";
				echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Update device']."'/>";		
			} else {
				echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Add device']."'/>";		
			}
			echo "</div>";
		echo "</form></div>";

	echo "</fieldset>";

	/* Virtual devices
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Devices']}</legend>";
		
		$query = "SELECT * FROM ".$db_prefix."virtual_devices vd, ".$db_prefix."plugins p WHERE user_id='{$user['user_id']}' and p.type_int = vd.plugin_id ORDER BY description ASC";
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
								/*if ($row['show_in_main'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=putOnMainVirtualDevice&id={$row['id']}'>Remove from main</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=putOnMainVirtualDevice&id={$row['id']}'>Put on main</a></li>";*/
				    			
								if ($row['online'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=setOnlineDevice&id={$row['id']}'>Set offline</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=setOnlineDevice&id={$row['id']}'>Set online</a></li>";	
				    			
								echo "<li><a href='?page=settings&view=virtualdevices&action=edit&id={$row['id']}'>Edit</a></li>";
								echo "<li class='divider'></li>";
				    			echo "<li><a href='?page=settings_exec&action=deleteVirtualDevice&id={$row['id']}'>Delete</a></li>";
							echo "</ul>";
						echo "</div>";

		    		echo "</div>";

		    		echo "<div style='font-size:20px;'>".$row['description']."</div>";

		    		echo "<div style='font-size:11px;'>";
		    			echo "<b>{$lang['Type']}:</b> ".$row['type_description']. "<br />";
						//echo "<b>{$lang['Value']}:</b> ".$row["config_value"]. "<br />";
		    			echo "<b>{$lang['Online']}:</b> ".$lang["boolean_".$row['online']]. "<br />";
		    		echo "</div>";

		    		echo "<div style='font-size:10px'>";
		    			echo $lang["last switch"].": ".ago(getLastVirtualDeviceStatusSwitch($row['id']));
		    		echo "</div>";

		    	echo "</div>";

		    }

		}

	echo "</fieldset>";

?>

<script type="text/javascript">
	$('#plugin_id').change(function () {
	
		var type_int = $(this).val();
		var user_id = $('#user_id').val(); 

		$('#configValues').find('tr:gt(1)').remove();
		if (type_int >= 0) {
			$('#waitlogo').html("<img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/>");
					
			var type_id = $('#virtual_device_id').val();
			
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
									var htmlElementSelect = "<select class='form-control' name='virtualdevice_value_"+id+"' id='virtualdevice_value_"+id+"'>";
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
							$('#configValues tr:last').after("<tr><td>"+value_description+"</td><td><select class='form-control' name='virtualdevice_value_"+id+"'><option value='true' "+selectedTrue+">true</option><option value='false' "+selectedFalse+">false</option></select</td><td></td></tr>");
						} else if (value_type.toUpperCase() == "callBackMethodReturnList".toUpperCase()) {
							var method = value_type_config[1];
		
		 					$.ajax({
			 					url : "inc/plugins/executePluginCallBack.php?type="+type_int+"&method="+method+"&user_id="+user_id, 
			 					success : function(data1) {
			 						var htmlElementSelect = "<select class='form-control' name='virtualdevice_value_"+id+"' id='virtualdevice_value_"+id+"'>";
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
							$('#configValues tr:last').after("<tr><td>"+value_description+"</td><td><input class='form-control' type='"+value_type+"' name='virtualdevice_value_"+id+"' id='virtualdevice_value_"+id+"' value='"+config_value+"' /></td><td></td></tr>");
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