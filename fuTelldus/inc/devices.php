<script src="lib/jscripts/futelldus_devices.js"></script>

<link rel="stylesheet" href="lib/packages/bootstrap-switch/bootstrapSwitch.css">
<script src="lib/packages/bootstrap-switch/bootstrapSwitch.js"></script>

<?php
	echo "<div class='hidden-xs' style='height:30px;'></div>"; 

	echo "<div class='col-md-2'></div>";
	
	echo "<div class='col-md-8'>";
	
// 		/* List groups
// 		--------------------------------------------------------------------------- */
// 		echo "<h3 class='hidden-xs'>{$lang['Groups']}</h3>";
	
// 		//echo "<div class='well'>";
// 			echo "<table class='table table-striped table-hover'>";
// 				echo "<thead class='hidden-xs'>";
// 					echo "<tr>";
// 						echo "<th>{$lang['Name']}</th>";
// 						echo "<th class='hidden-xs' width='40%'>{$lang['Location']}</th>";
// 						echo "<th width='20%'></th>";
// 					echo "</tr>";
// 				echo "</thead>";
				
// 				echo "<tbody>";
// 					$query = "SELECT * FROM ".$db_prefix."devices WHERE type='group' AND user_id='{$user['user_id']}' ORDER BY name ASC LIMIT 100";
// 					$result = $mysqli->query($query);
	
// 					while ($row = $result->fetch_array()) {
// 						echo "<tr>";
// 							echo "<td>{$row['name']}</td>";
// 							echo "<td class='hidden-xs'>{$row['clientname']}</td>";
// 							echo "<td style='text-align:right;'>";
// 								echo "<div id='ajax_loader_{$row['device_id']}'></div>";
// 								echo "<div class='btn-group'>";
// 									echo "<a id='btn_{$row['device_id']}_off' class='btn $activeStateOff' href=\"javascript:lightControl('off', '{$row['device_id']}');\">{$lang['Off']}</a>";
// 									echo "<a id='btn_{$row['device_id']}_on' class='btn $activeStateOn' href=\"javascript:lightControl('on', '{$row['device_id']}');\">{$lang['On']}</a>";
// 								echo "</div>";
// 							echo "</td>";
// 						echo "</tr>";
// 					}
// 				echo "</tbody>";
// 			echo "</table>";
		//echo "</div>";
	
			
			/* List virtual devices
			 --------------------------------------------------------------------------- */
			echo "<h3 class='hidden-xs'>{$lang['Devices']}</h3>";
			
			//echo "<div class='well'>";
			echo "<div class='table-responsive'>";
				echo "<table class='table table-striped'>";
					echo "<thead class='hide-xs'>";
						echo "<tr>";
							echo "<th>{$lang['Name']}</th>";
							echo "<th class='hidden-xs' width='40%'>{$lang['Plugin']}</th>";
							echo "<th width='20%'></th>";
						echo "</tr>";
				echo "</thead>";
										
				echo "<tbody>";
				$query = "select * from ".$db_prefix."virtual_devices vd, ".$db_prefix."plugins p where p.type_int = vd.plugin_id and vd.user_id='{$user['user_id']}' order by vd.description asc";
				$result = $mysqli->query($query);
				
				while ($row = $result->fetch_array()) {
						echo "<tr valign='top'>";
							echo "<td>";
								echo "<div style='display:inline-block;' id='ajax_loader_{$row['id']}'></div>";
								echo "{$row['description']}";
							echo "</td>";
	
							echo "<td class='hidden-xs'>{$row['type_description']}</td>";
							echo "<td style='text-align:right;'>";
	
								$period="current"; // alternative 'last'
							
								echo "<span id='ajax_".$row['id']."'><img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/></span>";
								echo "<div class='switch switch-small device_switch' id='switch_virtual_".$row['id']."' data-id='".$row['id']."' data-period='".$period."' data-userid='".$user['user_id']."'>";
									 echo "<input type='checkbox' disabled='disabled'>";
								echo "</div>";
							echo "</td>";
						echo "</tr>";
					}
				echo "</tbody>";
			echo "</table>";		
		echo "</div>";
		echo "</div>";
		
		echo "<div class='col-md-2'></div>";
?>

<script type="text/javascript">	
	$(function() {

		$('.device_switch').each(function(i, obj) {
			var objID = $(obj).data('id');
			var objPeriod = $(obj).data('period');
			var user_id = $(obj).data('userid');
			//$(obj).bootstrapSwitch('setState', false);
			$.ajax({
			  url: "inc/plugins/getDeviceState.php?id="+objID+"&period="+objPeriod+"&user_id="+user_id+"",
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
				var ajaxLoadID = '#ajax_'+objID;
				$(ajaxLoadID).html("");
					
				$(obj).bind('switch-change', function() {
					var toState = $(this).bootstrapSwitch('status');
					var newState = '';
					if (toState == false) {
						newState = 'off';
					} else if (toState == true) {
						newState = 'on';
					}
					deviceControl(newState, objID, user_id);
				});
			  },
			});
		});
    });

</script>