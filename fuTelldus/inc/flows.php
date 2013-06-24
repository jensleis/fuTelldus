<style>
	.helperPick {border:1px dashed #000; opacity:0.5}
	.drag {cursor:default;}
	.dragged {cursor:default; display:block; float:left;}
</style>
<script src="../../lib/packages/jsPlumb/jquery.jsPlumb-1.4.1-all.js "></script>
<script>
var elementID = 0;

/*var curColourIndex = 1, maxColourIndex = 24, nextColour = function() {
		var R,G,B;
		R = parseInt(128+Math.sin((curColourIndex*3+0)*1.3)*128);
		G = parseInt(128+Math.sin((curColourIndex*3+1)*1.3)*128);
		B = parseInt(128+Math.sin((curColourIndex*3+2)*1.3)*128);
		curColourIndex = curColourIndex + 1;
		if (curColourIndex > maxColourIndex) curColourIndex = 1;
		return "rgb(" + R + "," + G + "," + B + ")";
};
nextColour*/


$(document).ready(function () {
	var $stretch_parent = $("#droppable-container-parent");
    $stretch_parent.css({ height: $stretch_parent.parent().height() });
	
    var $stretch = $("#droppable-container");
    $stretch.css({ height: $stretch.parent().height() });
	
	//jsPlump defaults
	jsPlumb.Defaults.Endpoint = ["Dot", {radius:1}];                                
	jsPlumb.Defaults.EndpointStyle = {fillStyle: "#696969"},
	jsPlumb.Defaults.PaintStyle = {strokeStyle: "#696969", lineWidth: 2},
	jsPlumb.Defaults.Connector = ["Flowchart", {cornerRadius :10 } ],
	//jsPlumb.Defaults.Connector = ["Bezier"],
	//jsPlumb.Defaults.Connector = ["StateMachine", {curviness : 50}],
	
	jsPlumb.Defaults.ConnectorStyle = {lineWidth: 2, strokeStyle: "#696969"},
	jsPlumb.Defaults.HoverPaintStyle = {strokeStyle:"#696969", lineWidth:2 };
    jsPlumb.Defaults.ConnectionOverlays = [ [ "Arrow", { location:1,
		id:"arrow",
        length:15,
		width: 12,
        foldback:0.7} ], [ "Label", { label:"", id:"label" }]
	];
	
	
	// add start element
	var newElem = $('<span></span>');
	newElem.attr('id', 'start_element');
	newElem.addClass('alert').addClass('dragged').addClass('alert-error');
	var uiOffset = $('#droppable-container').offset();
	newElem.css( {position:"absolute", top: uiOffset.top+15, left:uiOffset.left+15});
	newElem.html("Start");
	$stretch.append(newElem);
	// add endpoints
	jsPlumb.makeSource("start_element", {
		anchor:[ "Right"  ],
	});
	
	jsPlumb.bind("jsPlumbConnection", function(info) {
	});
	
});


$(function() {
  
  $('#flow_name').keyup(function(event) {
	  var val = $.trim( this.value );
	  $('#start_element').html(val);
	})
  
  	$(document).bind("contextmenu",function(e){
        return false;
    });
  
	$( ".drag" ).draggable({
			//appendTo: "body",
			//helper: 'clone',
			helper: function(ev, ui){
				var classVal = $(this).attr('class');
				var width = $(this).width();
				var height = $(this).height();
				return "<span id='draggingObject' class='helperPick "+classVal+"' style='width:"+width+"px; height:"+height+"px'>"+$(this).html()+"</span>"
			},
	});
	
	$("#droppable-container").droppable({
		accept:".drag",
		/*drop: function(event, ui) {
            $(this).append($(ui.draggable).clone());
            $("#droppable-container .drag").addClass("item");
            $(".item").removeClass("ui-draggable drag");
            $(".item").draggable({
                containment: 'parent',
                //grid: [150,150]
            });
			var width = $(ui.draggable).width();
			var height = $(ui.draggable).height();
			$(".item").css("height", height);
			$(".item").css("with",width);
        }*/
		
		drop: function(event, ui){
			var id = ui.draggable.data('id');
			var name = ui.draggable.data('name');
			var type = ui.draggable.data('type');
			var content = ui.draggable.html();
			var classVal = ui.draggable.attr('class');
			var divID = ui.draggable.attr('id');
			var width = $(ui.draggable).width();
			var height = $(ui.draggable).height();

			var newID = id+"_"+elementID++;
			
			var newElem = $('<span></span>');
			newElem.attr('id', newID);
			newElem.css('height', height);
			newElem.css('width', width);
			 
			newElem.attr('class', classVal);
			newElem.addClass('dragged');
			newElem.html(content);

			newElem.removeClass("ui-draggable drag");
			
			var parentOffset = $(this).offset(); 
			var uiOffset = $('#draggingObject').offset();
			var posX = uiOffset.left;
			var posY = uiOffset.top;
			newElem.css( {position:"absolute", top:posY, left: posX});
			newElem.bind('mousedown', function(e) {
				if( e.button == 2 ) { 
					jsPlumb.remove(newElem);
					jsPlumb.repaintEverything();
					//source.
					return false; 
				} 
			});
			
			$(this).append(newElem);
			
			// jsplump configs
			// ----------------------------
			// add endpoints
			jsPlumb.makeTarget(newElem, {
				anchor:[ "Continuous", { shape:"Rectangle" } ],
			});
			var endpointOptions1 = {
				endpoint: ["Dot", {radius: 4}],
				isSource:true,				
				anchor:[  [0.25, 1, 0, 0] ],
			};
			var endpointOptions2 = {
				endpoint: ["Dot", {radius: 4}],
				isSource:true,
				anchor:[  [0.75, 1, 0, 0] ],
			};
			jsPlumb.addEndpoint(newID, endpointOptions1);
			jsPlumb.addEndpoint(newID, endpointOptions2);
			
			// make draggable
			jsPlumb.draggable($(newElem), {
				containment:'parent'
			})
			jsPlumb.repaint(newElem);
		}
    });
  });
  </script>
  
<?php
    /* Headline
    --------------------------------------------------------------------------- */
    echo "<h3>{$lang['Flows']}</h3>";
    echo "<fieldset>";
		echo "<legend>Existing flows</legend>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th width='50%'>Name</th>";
					echo "<th width='35%'>Flow type</th>";
					echo "<th width='15%' style='float:right'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
			
			
			$query = "select * from ".$db_prefix."flows f
				where f.user_id='".$user['user_id']."'";
			$result = $mysqli->query($query);
			
			while($row = $result->fetch_array()) {
				echo "<tr>";
					echo "<td>".$row['name']."</td>";
					echo "<td>".$row['type']."</td>";
					echo "<td>";
						echo "<div style='float:right'>";
							echo "<button class='btn btn-warning btn-mini' title='edit the flow' ><i class='icon-white icon-pencil'></i></button>&nbsp;";
							echo "<button class='btn btn-danger btn-mini' title='delete the flow' ><i class='icon-white icon-trash'></i></button>&nbsp;";
						echo "</div>";
					echo "</td>";
				echo "</tr>";
			}
			
			echo "</tbody>";
		echo "</table>";
		echo "<div style='text-align:right;'>";
		echo "<a class='btn btn-primary' href='#'>Create flow</a>";		
		echo "</div>";
	echo "</fieldset>";

    echo "<fieldset>";
    echo "<legend>Flow data</legend>";
        
	    echo "<form action='?page=settings_exec&action=undefined' method='POST'>";
		//echo "<div class='well'>";
			echo "<div class='container-fluid well' >";
				echo "<div class='row-fluid'>";
					echo "<div class='span4'>";
						echo "<label>Name of the flow</label>";
						echo "<input type='text' name='flow_name' id='flow_name' value='' style='width:100%' placeholder='Name of the flow'/>";    
					echo "</div>";
					echo "<div class='span2 offset1'>";
						echo "<label>Flow type</label>";
						echo "<select>";
							echo "<option selected='selected' value='-1'></option>";
							echo "<option>Schedule</option>";
							echo "<option>Event</option>";
							echo "<option>Device</option>";
							echo "<option>Sensor</option>";
						echo "</select>";
					echo "</div>";
					echo "<div class='span2' style='float:right'>";
						echo "<label>&nbsp;</label>";
						echo "<input class='btn btn-success' type='submit' name='submit' value='Save' style='' />&nbsp;";
						echo "<input class='btn btn-primary' type='submit' name='cancel' value='Cancel' style='' />";
					echo "</div>";
				echo "</div>";
				echo "<div class='row-fluid' style='min-height:750px'>";
					echo "<div class='span3'>";
						
						//echo "<ul class='nav nav-list'>";
						echo "<li class='nav-header'>Devices</li>";
						$devices = getDevices($user['user_id']);
						while ($device = $devices->fetch_array()) {
							echo "<div class='drag alert alert-success' id='device_".$device['id']."' data-id='".$device['id']."' data-type='device' name='device_".$device['id']."'>".$device['description']."</div>"; 
						}
						echo "<br />";
						
						
						echo "<li class='nav-header'>Sensors</li>";
						$sensors = getSensors($user['user_id']);
						while ($sensor = $sensors->fetch_array()) {
							echo "<div id='device_".$sensor['id']."' class='drag alert alert-info' data-id='".$sensor['id']."' data-type='sensor' name='device_".$sensor['id']."'>".$sensor['description']."</div>"; 
						}
						echo "<br />";
						
						
						echo "<li class='nav-header'>Actions</li>";
						$actions = getActions($user['user_id']);
						foreach ($actions as &$action) {
							echo "<div id='device_".$action['id']."' class='drag alert alert-warning' data-id='".$action['id']."' data-type='action' name='device_".$action['id']."'>".$action['description']."</div>"; 
						}
						//echo "</ul>";
					echo "</div>";
					
					echo "<div class='span9' id='droppable-container-parent'>";
						echo "<div class='ui-widget-content' id='droppable-container' ></div>";
					echo "</div>";
				echo "</div>";
			//echo "</div>";	
	    echo "</form>";
	    
	echo "</fieldset>";
?>