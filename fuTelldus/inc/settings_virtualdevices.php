<?php
	
	echo "<h4>".$lang['Virtual devices']."</h4>";

	/* Get parameters
	--------------------------------------------------------------------------- */
	$action = "";
	$getID = "";
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);

	/* Messages
	--------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success'>{$lang['Virtual device added']}</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-success'>{$lang['Virtual device deleted']}</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-success'>{$lang['Data saved']}</div>";
		if ($_GET['msg'] == 04) echo "<div class='alert alert-success'>{$lang['Virtual device updated']}</div>";
	}

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
				echo "<div class='alert'>";
				echo "<form action='?page=settings_exec&action=updateVirtualDevice&id=$getID' method='POST'>";
			} else {
				echo "<div class='well'>";
				echo "<form action='?page=settings_exec&action=addVirtualDevice' method='POST'>";	
			}		
		
			// add hidden field with actual virtual device id
			echo "<input type='hidden' name='virtual_device_id' id ='virtual_device_id' value='$getID' />";
		
			echo "<table width='100%' id='configValues'>";

				echo "<tr>";
					echo "<td width='40%'>".$lang['Description']."</td>";
					echo "<td>";
						echo "<input style='width:180px;' type='text' name='virtualdevice_description' id='virtualdevice_description' value='$description' />";
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
					echo "	<select $disabledSelect name='plugin_id' id ='plugin_id' size='1' selectedIndex='-1'>";
					echo "	  <option value='$plugin_id'>$plugin_description</option>";
					// select all available plugin-Types
					$query = "SELECT * FROM ".$db_prefix."plugins where hidden='0' and plugin_type='device' ORDER BY type_int ASC";
					$result = $mysqli->query($query);		
					while($row = $result->fetch_array()) {
						echo "	  <option value='".$row['type_int']."'>".$row['type_description']."</option>";
					}
					echo "	</select>";
					echo "</td>";
					
					echo "<td></td>";	
				echo "</tr>";
				
			echo "</table>";
			echo "<br/><div style='text-align:right;'>";
			if ($action == "edit") {
				echo "<a class='btn' href='?page=settings&view=virtualdevices'>{$lang['Cancel']}</a> &nbsp; ";
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
							if ($row['show_in_main'] == 1){
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
		
		if (type_int < 0) {
			$('#configValues').find('tr:gt(1)').remove();		
		}
		
		var type_id = $('#virtual_device_id').val();
		
		$.getJSON("inc/plugins/getPluginConfig.php?type_int="+type_int+"&plugin_id="+type_id, function(data) {
			// remove all from 1 on
			$('#configValues').find('tr:gt(1)').remove();
			
			// add all new
			jQuery.each(data , function(index, value){
				var value_description = value.description;
				var value_key = value.value_key;
				var value_type = value.value_type;
				var config_value = value.config_value;
				var id = value.id;
				if (value_type.toUpperCase() == "boolean".toUpperCase()) {
					var selectedTrue, selectedFalse = 0;
					if (config_value.toUpperCase() == "true".toUpperCase()) selectedTrue="selected='selected'";
					if (config_value.toUpperCase() == "false".toUpperCase()) selectedFalse="selected='selected'";
					$('#configValues tr:last').after("<tr><td>"+value_description+"</td><td><select name='virtualdevice_value_"+id+"'><option value='true' "+selectedTrue+">true</option><option value='false' "+selectedFalse+">false</option></select</td><td></td></tr>");
				} else {
					$('#configValues tr:last').after("<tr><td>"+value_description+"</td><td><input style='width:180px;' type='"+value_type+"' name='virtualdevice_value_"+id+"' id='virtualdevice_value_"+id+"' value='"+config_value+"' /></td><td></td></tr>");
				}
			});
		});
	//});
	}).trigger('change');
</script>