<script>

  $(function() {

	  // for each li-elemnt
	  $(".sensor-blocks").each(function() {
		  var listElement = $(this);
		  var sensorID = listElement.data('id');
		  $.post( "inc/main/getSensorWidget.php",
				{ id:sensorID },
				function(data) {
					listElement.html(data);
					jQuery("abbr.timeago").timeago();
				}
			);
	  });

	
	  
    $( "#sortable" ).sortable({
    	update: function (event, ui) {
	         var new_pos = ui.item.index()+1;
	         var id = ui.item.data('id');
	         // update new pos into db
	         $.ajax({
	   		  	url: "inc/main/updatePosition.php?id="+id+"&pos="+new_pos+"",
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
	echo "<div class='hidden-xs' style='height:30px;'></div>";
	echo "<div class='col-md-1'></div>";
	
	echo "<div class='col-md-10'>";
		// Sensors
		echo "<div class='sensors-wrap'>";
		echo "<ul id='sortable'>";
			/* My sensors
	   		--------------------------------------------------------------------------- */
			$query = "SELECT vs.id as id, vs.show_in_main as show_in_main FROM ".$db_prefix."virtual_sensors vs WHERE vs.user_id='{$user['user_id']}' AND vs.show_in_main>='1' order by vs.show_in_main ASC ";
					
			//echo $query;
		    $result = $mysqli->query($query);
	
		    while ($row = $result->fetch_array()) {
		    	echo "<li class='sensor-blocks well' data-id='".$row['id']."'><img src='images/ajax-loader2.gif'/>";
		    	echo "</li>";
		    	
		    }
		echo "</ul>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='col-md-1'></div>";
?>