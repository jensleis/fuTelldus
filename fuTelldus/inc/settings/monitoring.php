<script type="text/javascript">
$(function() {
	$("#enableMonitoring").bootstrapToggle('off');	
	$("#monitoringData").hide();

	$("#enableMonitoring").change(function() {
		var on = $(this).prop('checked');

		if (on) {
			$("#monitoringData").show();
		} else {
			$("#monitoringData").hide();
		}
	});
});

$( document.body ).on( 'click', '.dropdown-menu li', function( event ) {
	   var $target = $( event.currentTarget );
	 
	   $target.closest( '.btn-group' ).find( '[data-bind="label"]' ).text( $target.text() ).end().children( '.dropdown-toggle' ).dropdown( 'toggle' );

	   $("#repeatEvery").empty();
	   
		if ($target.text() == "Hourly") {
			$("#repeatEvery").append("<li><a href='#'>every hour</a></li>");
			$("#repeatEvery").append("<li><a href='#'>every 2nd hour</a></li>");
			$("#repeatEvery").append("<li><a href='#'>every 3rd hour</a></li>");

			for (i = 4; i < 24; i++) {
				$("#repeatEvery").append("<li><a href='#'>every "+i+"th hour</a></li>");
			}

			$('#repeatAtLabel').text("at minute");
			for (i = 0; i < 60; i++) {
				$("#repeatAt").append("<li><a href='#'>"+i+"</a></li>");
			}

			$('#repeatAtLabelSelect').show();
		}

		if ($target.text() == "Minutely") {
			$("#repeatEvery").append("<li><a href='#'>every minute</a></li>");
			$("#repeatEvery").append("<li><a href='#'>every 2nd minute</a></li>");
			$("#repeatEvery").append("<li><a href='#'>every 3rd minute</a></li>");

			for (i = 4; i < 60; i++) {
				$("#repeatEvery").append("<li><a href='#'>every "+i+"th minute</a></li>");
			}

			$('#repeatAtLabelSelect').hide();
		}

		if ($target.text() == "Daily") {
			$("#repeatEvery").append("<li><a href='#'>every day</a></li>");
			$("#repeatEvery").append("<li><a href='#'>every 2nd day</a></li>");
			$("#repeatEvery").append("<li><a href='#'>every 3rd day</a></li>");

			for (i = 4; i < 31; i++) {
				$("#repeatEvery").append("<li><a href='#'>every "+i+"th day</a></li>");
			}

			$('#repeatAtLabel').text("at hour");
			for (i = 0; i < 24; i++) {
				$("#repeatAt").append("<li><a href='#'>"+i+":00</a></li>");
			}

			$('#repeatAtLabelSelect').show();
		}
		 
	   return false;
	 
	});


</script>

<div class="container">

	<fieldset>
		<div class="form-group">
			<div class="row" style='margin-top: 15px'>
				<label class="control-label col-md-3" for="enableMonitoring">Monitoring</label>
				<div class="input-group col-md-5">
					<input type="checkbox" checked data-toggle="toggle"
						id="enableMonitoring">
				</div>
			</div>

			<div id="monitoringData">
				<div class="row" style='margin-top: 15px'>
					<label class="control-label col-md-3" for="cycle">Cycle</label>
					<div class="input-group col-md-5">
						<div class="btn-group btn-input clearfix">
							<button type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown">
								<span data-bind="label">Select One</span> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Minutely</a></li>
								<li><a href="#">Hourly</a></li>
								<li><a href="#">Daily</a></li>
							</ul>
						</div>
					</div>
				</div>
				
				
				<div class="row" style='margin-top: 15px' >
					<label class="control-label col-md-3" for="repeatEvery">Repeat every</label>
					<div class="input-group col-md-5">
						<div class="btn-group btn-input clearfix">
								<button type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown">
									<span data-bind="label">Select One</span> <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu" id="repeatEvery">
								</ul>
							</div>
					</div>
				</div>
				
				<div class="row" style='margin-top: 15px' id="repeatAtLabelSelect">
					<label class="control-label col-md-3" for="repeatAt" id="repeatAtLabel">at</label>
					<div class="input-group col-md-5">
						<div class="btn-group btn-input clearfix">
								<button type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown">
									<span data-bind="label">Select One</span> <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu" id="repeatAt">
								</ul>
							</div>
					</div>
				</div>				
			</div>


		</div>
	</fieldset>
</div>

<script>
	
</script>
<?php
echo "";

?>