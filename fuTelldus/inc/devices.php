<script src="lib/jscripts/futelldus_lights.js"></script>
<script src="lib/jscripts/futelldus_devices.js"></script>

<link rel="stylesheet" href="lib/packages/bootstrap-switch/bootstrapSwitch.css">
<script src="lib/packages/bootstrap-switch/bootstrapSwitch.js"></script>

<?php

	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='?page=settings&view=user'>your userprofile</a>.";
		exit();
	}

	echo "<div class='hidden-phone' id='sync_state' style='float:right; margin-right:25px; margin-bottom:-50px; color:green; font-size:10px; display:none;'>".$lang['List synced']."</div>";

	$sync = "";
	if ($userTelldusConf['sync_from_telldus'] == 1) {
		$sync = "sync";
	} 
	echo "<input type='hidden' name='syncWithTelldus' id='syncWithTelldus' value='".$sync."' data-userid='".$user['user_id']."'/>";


	/* List groups
	--------------------------------------------------------------------------- */
	echo "<h3 class='hidden-phone'>{$lang['Groups']}</h3>";

	//echo "<div class='well'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>{$lang['Name']}</th>";
					echo "<th class='hidden-phone' width='40%'>{$lang['Location']}</th>";
					echo "<th width='20%'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
				$query = "SELECT * FROM ".$db_prefix."devices WHERE type='group' AND user_id='{$user['user_id']}' ORDER BY name ASC LIMIT 100";
				$result = $mysqli->query($query);

				while ($row = $result->fetch_array()) {
					echo "<tr>";
						echo "<td>{$row['name']}</td>";
						echo "<td class='hidden-phone'>{$row['clientname']}</td>";
						echo "<td style='text-align:right;'>";
							echo "<div id='ajax_loader_{$row['device_id']}'></div>";
							echo "<div class='btn-group'>";
								echo "<a id='btn_{$row['device_id']}_off' class='btn $activeStateOff' href=\"javascript:lightControl('off', '{$row['device_id']}');\">{$lang['Off']}</a>";
								echo "<a id='btn_{$row['device_id']}_on' class='btn $activeStateOn' href=\"javascript:lightControl('on', '{$row['device_id']}');\">{$lang['On']}</a>";
							echo "</div>";
						echo "</td>";
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
	//echo "</div>";



	/* List devices
	--------------------------------------------------------------------------- */
	echo "<h3 class='hidden-phone'>{$lang['Devices']}</h3>";

	//echo "<div class='well'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>{$lang['Name']}</th>";
					echo "<th class='hidden-phone' width='40%'>{$lang['Location']}</th>";
					echo "<th width='20%'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
				$query = "SELECT * FROM ".$db_prefix."devices WHERE type='device' AND user_id='{$user['user_id']}' ORDER BY name ASC";
				$result = $mysqli->query($query);

				while ($row = $result->fetch_array()) {
					echo "<tr valign='top'>";
						echo "<td>";
							echo "<div style='display:inline-block;' id='ajax_loader_{$row['device_id']}'></div>";
							echo "{$row['name']}";
						echo "</td>";

						echo "<td class='hidden-phone'>{$row['clientname']}</td>";
						echo "<td style='text-align:right;'>";

							$period="last";
							if ($userTelldusConf['sync_from_telldus'] == 1) {
								$period="current";
							}
							echo "<span id='ajax_device_".$row['device_id']."'><img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/></span>";
							echo "<div class='switch switch-small device_switch' id='switch_device_".$row['device_id']."' data-id='".$row['device_id']."' data-type='device' data-period='last'>";
								 echo "<input type='checkbox' disabled='disabled'>";
							echo "</div>";
						echo "</td>";
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
	//echo "</div>";

		
		/* List virtual devices
		 --------------------------------------------------------------------------- */
		echo "<h3 class='hidden-phone'>{$lang['Virtual devices']}</h3>";
		
		//echo "<div class='well'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>{$lang['Name']}</th>";
					echo "<th class='hidden-phone' width='40%'>{$lang['Plugin']}</th>";
					echo "<th width='20%'></th>";
				echo "</tr>";
			echo "</thead>";
									
			echo "<tbody>";
			$query = "select * from futelldus_virtual_devices vd, futelldus_plugins p where p.type_int = vd.plugin_id and vd.user_id='{$user['user_id']}' order by vd.description asc";
			$result = $mysqli->query($query);
			
			while ($row = $result->fetch_array()) {
					echo "<tr valign='top'>";
						echo "<td>";
							echo "<div style='display:inline-block;' id='ajax_loader_{$row['id']}'></div>";
							echo "{$row['description']}";
						echo "</td>";

						echo "<td class='hidden-phone'>{$row['type_description']}</td>";
						echo "<td style='text-align:right;'>";

							$period="last";
							if ($userTelldusConf['sync_from_telldus'] == 1) {
								// get actual state from device
								$period="current";
							}
						
							echo "<span id='ajax_virtual_".$row['id']."'><img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/></span>";
							echo "<div class='switch switch-small device_switch' id='switch_virtual_".$row['id']."' data-id='".$row['id']."' data-type='virtual' data-period='".$period."'>";
								 echo "<input type='checkbox' disabled='disabled'>";
								//echo "<a id='btn_device_{$row['id']}_off' class='btn device_on_button' href=\"javascript:deviceControl('off', '{$row['id']}');\">{$lang['Off']}</a>";
								//echo "<a id='btn_device_{$row['id']}_on' class='btn device_off_button' href=\"javascript:deviceControl('on', '{$row['id']}');\">{$lang['On']}</a>";
							echo "</div>";
						echo "</td>";
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";		
?>

<script type="text/javascript">	
	$(function() {
		var syncHiddenField = $('#syncWithTelldus');
		var syncWithTelldus = $(syncHiddenField).val();
		var user_id = $(syncHiddenField).data('userid');
		if (syncWithTelldus=='sync') {
			$.ajax({
			  url: "inc/plugins/getDeviceState.php?action=synchronizeTelldus&user_id="+user_id+"",
			  dataType: "text",
			  method: "get",
			  cache: false,
			  async: false,
			  success: function(data) {
				$('#sync_state').show();
				
			  },
			});
		}
	
		$('.device_switch').each(function(i, obj) {
			var objID = $(obj).data('id');
			var objType = $(obj).data('type');
			var objPeriod = $(obj).data('period');
			//$(obj).bootstrapSwitch('setState', false);
			$.ajax({
			  url: "inc/plugins/getDeviceState.php?id="+objID+"&type="+objType+"&period="+objPeriod+"&action=state",
			  dataType: "text",
			  method: "get",
			  cache: false,
			  success: function(data) {
				if (data==0) {
					$(obj).bootstrapSwitch('setState', false);
					$(obj).bootstrapSwitch('setActive', true);
				} else if (data==1) {
					$(obj).bootstrapSwitch('setState', true);				
					$(obj).bootstrapSwitch('setActive', true);
				}
				var ajaxLoadID = '#ajax_'+objType+'_'+objID;
				$(ajaxLoadID).html("");
					
				$(obj).bind('switch-change', function() {
					var toState = $(this).bootstrapSwitch('status');
					var newState = '';
					if (toState == false) {
						newState = 'off';
					} else if (toState == true) {
						newState = 'on';
					}
					if (objType=='device') {
						lightControl(newState, objID);
					} else if (objType =='virtual') {
						deviceControl(newState, objID);
					}
				});
			  },
			});
		});
    });

</script>