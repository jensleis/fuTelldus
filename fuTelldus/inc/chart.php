<script src="../../lib/packages/Highstock-1.3.1/js/highstock.js"></script>
<script src="../../lib/packages/Highstock-1.3.1/js/modules/exporting.js"></script> 



<?php

	echo "<input type='hidden' id='user_id' value='".$user['user_id']."'/>";

    /* Headline
    --------------------------------------------------------------------------- */
    echo "<h3>{$lang['Charts']}</h3>";

    /* Scenes
    --------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Scenes']}</legend>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th width='30%'>{$lang['Name']}</th>";
					echo "<th width='55%'>Included data</th>";
					echo "<th width='15%' ></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody id='scene_table'>";
				echo "<tr><td><img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/></td><td></td><td></td></tr>";
			echo "</tbody>";
		
		echo "</table>";
		
	echo "<div style='text-align:right;'>";
		echo "<button class='btn btn-primary showScene' href='#showScene' data-userid='".$user['user_id']."' data-type='scene' data-action='new' data-name='New scene'>Create scene</button>";		
	echo "</div>";
	echo "</fieldset>"; 
	
    /* Sensors
    --------------------------------------------------------------------------- */	
	echo "<fieldset>";
		echo "<legend>{$lang['Sensors']}</legend>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th width='50%'>{$lang['Name']}</th>";
					echo "<th width='35%'></th>";
					echo "<th width='15%' style='float:right'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
			
			
			$query = "
				select s.name, s.sensor_id as id, 'sensor' as type from ".$db_prefix."sensors s
					where s.monitoring=1 and s.user_id='".$user['user_id']."'
				union
				select vs.description as name, vs.id as id, 'virtual' as type from ".$db_prefix."virtual_sensors vs
					where vs.monitoring=1 and vs.user_id='".$user['user_id']."'";
			$result = $mysqli->query($query);
			
			while($row = $result->fetch_array()) {
				echo "<tr>";
					echo "<td>".$row['name']."</td>";
					echo "<td><small class='count_log' id='count_".$row['type']."_".$row['id']."' data-id='".$row['id']."' data-type='".$row['type']."'></small></td>";
					echo "<td>";
						$activateChartButton = "";
						if ($row['type']=='virtual' and !isPluginProvidingCharts($row['id'])) {
							$activateChartButton = "disabled";
						}					
						echo "<button class='btn btn-info btn-mini showScene $activateChartButton' $activateChartButton id='showChart_".$row['type']."_".$row['id']."' href='#showScene' style='float:right' data-action='showChart' data-id='".$row['id']."' data-name='".$row['name']."' data-type='".$row['type']."' title='show the chart'><i class='icon-white icon-signal'></i></button>";
					echo "</td>";
				echo "</tr>";
			}
			
			echo "</tbody>";
		echo "</table>";
	echo "</fieldset>";
	
    /* Devices
    --------------------------------------------------------------------------- */		
	echo "<fieldset>";
		echo "<legend>{$lang['Devices']}</legend>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th width='50%'>{$lang['Name']}</th>";
					echo "<th width='35%'></th>";
					echo "<th width='15%' style='float:right'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
			
			$query = "SELECT d.name, d.type as type, d.device_id as id FROM ".$db_prefix."devices d where d.user_id='".$user['user_id']."'";
			$result = $mysqli->query($query);
			
			while($row = $result->fetch_array()) {
				echo "<tr>";
					echo "<td>".$row['name']."</td>";
					echo "<td><small class='count_log' id='count_".$row['type']."_".$row['id']."' data-id='".$row['id']."' data-type='".$row['type']."'></small></td>";
					
					echo "<td>";
						echo "<button class='btn btn-info btn-mini showScene' href='#showScene' id='showChart_".$row['type']."_".$row['id']."' style='float:right' data-action='showChart' data-id='".$row['id']."' data-name='".$row['name']."' data-type='".$row['type']."' title='show the chart'><i class='icon-white icon-signal'></i></button>";
					echo "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
		
		echo "</table>";		
	echo "</fieldset>";
?>


	
	<!-- model dialog scenes-->
	<div class="modal fade" id="showScene">
		<div class="modal-header" id="modal-header-scene">
			<a class="close" data-dismiss="modal">&times;</a>
			<div class="header" id="header-scene">
				<h3 id="header-scene-text"></h3>
			</div>
		</div>
		<div class="modal-body" id="modal-body-scene" style="text-align:left">
		</div>
		<div class="modal-footer" id="modal-footer-scene">
			<a href="#" class="btn btn-success" id='save_scene' onClick="javascript: saveScene();">Save</a>&nbsp;
			<a href="#" class="btn btn-primary" data-dismiss="modal"><?php echo $lang['Close'] ?></a>
		</div>
	</div>	
	
<script type="text/javascript">	
	$(function() {
		// generate scene table
		setSceneTable();
	
		$('.count_log').each(function(i, obj) {
			var objID = $(obj).data('id');
			var objType = $(obj).data('type');
			$.ajax({
			  url: "inc/charts/getCountLogs.php?id="+objID+"&type="+objType+"",
			  dataType: "text",
			  method: "get",
			  cache: false,
			  success: function(data) {
				$(obj).hide();
				if (data <= 0) {
					var buttonID = '#showChart_'+objType+'_'+objID;
					$(buttonID).attr('disabled', 'disabled');
					$(obj).html("<i>no data</i>");
					$(obj).fadeIn("normal");
					
				} else {
					$(obj).html("<i>about "+data+" logs</i>");
					$(obj).fadeIn("normal");
				}
				 
			  },
			});
		});
    });
	
	$(document).on("click", ".showScene", function () {
		$('#showScene').css({ //		.modal({backdrop: true,keyboard: true}).css({
		   'width': function () { 
			   return ($(document).width() * .7) + 'px';  
		   },
		   'margin-left': function () { 
			   return -($(this).width() / 2); 
		   }
		});
		
		var action = $(this).data('action');
		var sensortype = $(this).data('type');
		var sensorName = $(this).data('name');
		var userID = $('#user_id').val();
		
		$("#header-scene-text").text(sensorName);
		$("#modal-body-scene").css({'min-height': '520px'});
		
		var url ='';
		var sceneID = -1;
		var showMode=false;
		if (action=='new') {
			 url = 'inc/charts/getChartableItemsJSON.php?user_id='+userID;
		} else if (action=='edit') {
			sceneID = $(this).data('id');
			url = 'inc/charts/getChartableItemsJSON.php?user_id='+userID+'&scene_id='+sceneID;
		} else if (action=='showScene' || action=='showChart') {
			sceneID = $(this).data('id');
			showMode=true;
			url = 'inc/charts/getChartableItemsJSON.php?user_id='+userID+'&scene_id='+sceneID;
			
		}
		
		$.getJSON(url, function(data) {
		   var items = "";
		   if (showMode){
			   $("#modal-body-scene").html(""+
					"		<div style='display:none;' id='sceneitemslist'>"+
					"		</div>"+
					"		<div class='' style='border:none' id='model-body-scene-content' style='min-height:500px'>"+
					"		</div>");
		   } else {
			   $("#modal-body-scene").html("<div class='container-fluid'>"+
					"<div class='row-fluid'>"+
					"	<div class='span3 alert' id='modal-body-scene-nav' style='min-height:500px; max-height:500px; overflow: auto;'>"+
					"		<li class='nav-header'>Name of the scene</li>"+
					"		<input type='text' name='scene_name' id='scene_name' value='' data-id='"+sceneID+"' placeholder='Name of the scene'/>"+
					"		<div style='overflow: auto;' id='sceneitemslist'>"+
					"			<li class='nav-header'>Selected</li>"+
					"		</div>"+
					"	</div>"+
					"	<div class='span9 ui-widget-content' style='border:none' id='model-body-scene-content' style='min-height:500px'>"+
					"	</div>"+
					"</div>"+
				"</div>");
		   }
           
			
			// for scene get all selected scenes, for charts, selecth the given one		
			if (action=='showChart') {
				var htmlID = sensortype+"_"+sceneID;
				var string = "<a href='#' class='badge toggleChartContent badge-warning' id='"+htmlID+"' name='"+htmlID+"' data-id='"+sceneID+"' data-type='"+sensortype+"' data-showInChart='true' style='width:80%'>"+sensorName+"</a>";
				var item = $('<div/>').html(string).contents();
				item.data('showInChart', 'true');
				item.addClass('activatedClass');
				
				$('#sceneitemslist').append(item);
			}
			
			// for scene get all selected scenes, for charts, selecth the given one		
			if (action=='showScene' || action=='new' || action=='edit') {
				$.each(data, function(index, value){
					var htmlID = value.type+"_"+value.id;
					
					var activated = value.activated;
					var showInChart='false';
					var activatedClass='';
					
					if	(activated=='true'){
						showInChart='true';
						activatedClass='badge-warning';
					}
					
					var string = "<a href='#' class='badge toggleChartContent "+activatedClass+"' id='"+htmlID+"' name='"+htmlID+"' data-id='"+value.id+"' data-type='"+value.type+"' data-showInChart='"+showInChart+"' style='width:80%'>"+value.name+"</a>";
					var item = $('<div/>').html(string).contents();
					item.data('showInChart', showInChart);
					item.addClass('activatedClass');
					
					$('#sceneitemslist').append(item);
				});
			}
			
			if (action=='edit') {
				$('#scene_name').val(sensorName);
			}
			
			drawChart();
        });
		//$(".modal-body-scene").html("");
		
		$('#showScene').modal('show');
	});
	
	$(document).on('click', '.toggleChartContent', function(event) {
		var objectID = $(this).data('id');
		var objectType = $(this).data('type');
		var showInChart = $(this).data('showInChart');
		if (typeof showInChart === "undefined" || showInChart=='false') {
			$(this).data('showInChart', 'true');
			$(this).addClass('badge-warning');
		} else if (showInChart=='true'){
			$(this).data('showInChart', 'false');
			$(this).removeClass('badge-warning');
		}
		drawChart();
	});	
	

	function drawChart() {
		var sceneDataArray = "[";
		var first = true;
		var count = 0;
		$('.toggleChartContent').each(function(i, obj) {
			var objectID = $(this).data('id');
			var objectType = $(this).data('type');
			var objectName = $(this).html();
			var showInChart = $(this).data('showInChart');
			if (showInChart=='true') {
				if (first==false) {
					sceneDataArray += ",";
				} else {
					first=false;
				}
				sceneDataArray += "{ \"type\":\""+objectType+"\", \"id\":\""+objectID+"\", \"name\":\""+objectName+"\"}";
				count++;
			}
		});
		sceneDataArray += "]";
		
		if (count>0) {
			$.ajax({
			  url: "inc/charts/getChart.php?data="+sceneDataArray,
			  dataType: "text",
			  method: "get",
			  cache: false,
			  success: function(data) {
				$("#model-body-scene-content").html(data);
			  },
			  error: function(xhr, status, err) {
				$("#model-body-scene-content").html("<p>Error: Status = " + status + ", err = " + err + "</p>");
			  }
			});
		} else {
			$("#model-body-scene-content").html("");
		}
		
	}
	
	function setSceneTable() {
			$(scene_table).html("<tr><td><img style='height:15px; margin-right:8px;' src='images/ajax-loader2.gif'/></td><td></td><td></td></tr>");
			var userID = $('#user_id').val();
			
			$.getJSON('inc/charts/getScenesJSON.php?user_id='+userID, function(data) {
				var content= "";
			
				$.each(data, function(index, value){
					content+= "<tr>";
					content+= "<td>"+value.name+"</td>";
					content+= "<td>"+value.included+"</td>";
					content+= "<td>";
						content+= "<div style='float:right'>";
							content+= "<button class='btn btn-warning btn-mini edit_scene showScene' href='#showScene' data-action='edit' data-id='"+value.id+"' data-name='"+value.name+"' title='edit the scene'><i class='icon-white icon-pencil'></i></button>&nbsp;";
							content+= "<button class='btn btn-danger btn-mini delete_scene' data-id='"+value.id+"' title='delete the scene'><i class='icon-white icon-trash'></i></button>&nbsp;";
							content+= "<button class='btn btn-info btn-mini showScene' href='#showScene' data-action='showScene' data-id='"+value.id+"' data-name='"+value.name+"' title='show the chart'><i class='icon-white icon-signal'></i></button>";
						content+= "</div>";
						
					content+= "</td>";
					content+= "</tr>";
				});
				
				$(scene_table).hide();
				$(scene_table).html(content);
				$(scene_table).slideDown();
			});
	}
	
	function saveScene() {
		var sceneName = $('#scene_name').val();
		var sceneID = $('#scene_name').data('id');
		var userID = $('#user_id').val();
		
		var sceneDataArray = "[";
		var first = true;
		$('.toggleChartContent').each(function(i, obj) {
			var objectID = $(this).data('id');
			var objectType = $(this).data('type');
			var showInChart = $(this).data('showInChart');
			if (showInChart=='true') {
				if (first==false) {
					sceneDataArray += ",";
				} else {
					first=false;
				}
				sceneDataArray += "{ \"type\":\""+objectType+"\", \"id\":\""+objectID+"\"}";
			}
		});
		sceneDataArray += "]";
		
		var action = "";
		if (sceneID<0) {
			action='insert';
		} else {
			action='update';
		}
		$.ajax({
		  url: "inc/charts/saveScene.php?action="+action+"&id="+sceneID+"&userid="+userID+"&name="+sceneName+"&scenedata="+sceneDataArray+"",
		  dataType: "text",
		  method: "get",
		  cache: false,
		  success: function(data) {
			if (data==1) {
				setSceneTable();
				$('#showScene').modal('hide');
			}
		  },
		  error: function(xhr, status, err) {
			$("#model-body-scene-content").html("<p>Error: Status = " + status + ", err = " + err + "</p>");
		  }
		});
	}
	
	$(document).on('keyup', '#scene_name', function(event) {
		var val = $.trim( this.value );
		$('#header-scene-text').text(val);
	});
	
	$(document).on('click', '.delete_scene', function(event) {
		var sceneID = $(this).data('id');
		$.ajax({
		  url: "inc/charts/saveScene.php?action=delete&id="+sceneID+"",
		  dataType: "text",
		  method: "get",
		  cache: false,
		  success: function(data) {
			setSceneTable();
		  },
		  error: function(xhr, status, err) {
			$(scene_table).html("<div class='alert alert-error'><b>Error! </b>"+err+"<br />Please reload the page</div>");
		  }
		});
	});


</script>
