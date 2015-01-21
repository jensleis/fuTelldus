<!-- 
	all user configurations are sotred within the array $pluginConfig 
	this array is also including the data from the database
	all fields which have the key of the config value set, will saved to the db automatically

-->

<fieldset id="netatmo_login">
		<legend>Netatmo login</legend>
		Credentials won't be saved in this application
		<div class="form-group">

			<div class="row" style='margin-top:15px'>
				<label class="control-label col-md-3" for="netatmo_user">Login</label>
				<div class="input-group col-md-5">
					<input class='form-control' style='width:350px;' type="text" name='netatmo_user' id="netatmo_user" placeholder="Login" value=''>
				</div>
			</div>
			
			<div class="row" style='margin-top:15px'>
				<label class="control-label col-md-3" for="netatmo_passwd">Password</label>
				<div class="input-group col-md-9">
					<div class="row">
						<div class="col-md-8">
							<input class='form-control' style='width:350px;' type="password" name='netatmo_passwd' id="netatmo_passwd" placeholder="Password">
						</div>
					
						<div class="col-md-3">
							<div class='controls pull-right '>
								<a href="#" id="btn_authenticate" class='btn btn-success'>Authenticate with Netatmo</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
</fieldset>
<br />

<fieldset id="netatmo_result">
		<legend>Netatmo settings</legend>
		<div class="form-group">

			<div class="row" style='margin-top:15px'>
				<label class="control-label col-md-3" for="netatmo_refresh_token"><?php echo $pluginConfig['netatmo_refresh_token']['description']; ?></label>
				<div class="input-group col-md-9">
					<div class="row">
						<div class="col-md-7">
							<input class='form-control' style='width:350px;' type="text" name='netatmo_refresh_token' id="netatmo_refresh_token" placeholder="<?php echo $pluginConfig['netatmo_refresh_token']['description']; ?>" value='<?php echo $pluginConfig['netatmo_refresh_token']['value']; ?>'>
					    </div>
					    
					    <div class="col-md-2">
					      	<div class='input-group col-md-4'>
								<a class='btn btn-success' style='' href='#' id="btn_test">Test API</a>
							</div>
						</div>
					    
					    <div class="col-md-2">
					      	<div class='input-group col-md-4'>
								<a href="#" id="btn_disconnect" class='btn btn-danger'>Disconnect API</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row" style='margin-top:15px' id="apiResult">
				<label class="control-label col-md-3">API Result</label>
				<div class="col-md-5">
					<div id="apiResultValue"></div>
				</div>
			</div>
			
			
			
		</div>
	</fieldset>
	<br />
	
	<script type="text/javascript">
	$(function() {
		$("#apiResult").hide();
		
		var netatmo_refresh_token = $("#netatmo_refresh_token").val();
		if (netatmo_refresh_token!=null && netatmo_refresh_token.length>0) {
			$("#netatmo_result").show();
			$("#netatmo_login").hide();
		} else {
			$("#netatmo_login").show();
			$("#netatmo_result").hide();
		}
		
	});
		
	$( "#btn_authenticate" ).click(function() {
		var user = $("#netatmo_user").val();
		var passwd = $("#netatmo_passwd").val();
		$.post( "fuPlugins/netatmo_sensor/authenticate.php",
				{ netatmo_user: user, netatmo_passwd: passwd },
			  function(data) {
				$("#netatmo_result").show();
				$("#netatmo_login").hide();
				
				$("#netatmo_refresh_token").val(data);
			  }
		);
	});
	
	$( "#btn_disconnect" ).click(function() {
		$("#netatmo_refresh_token").val("");
		$("#netatmo_login").show();
		$("#netatmo_result").hide();
	});
		
	$( "#btn_test" ).click(function() {
		var refresh_token = $("#netatmo_refresh_token").val();
		$.post( "fuPlugins/netatmo_sensor/refreshToken.php",
				{ refresh_token: refresh_token},
			  function(accessToken) {
					$.post( "fuPlugins/netatmo_sensor/getStations.php",
							{ access_token: accessToken},
						  function(data) {
								var apiResultText = "Found " + data.devices.length + " device(s) ";
								apiResultText += "and " + data.modules.length + " module(s)<br />"; 

								for (var count = 0; count < data.devices.length; count++) {
									apiResultText+= "<br /> Device " + count + ": " + data.devices[count].station_name;
								}
								  
// 							  var apiResultText = data.devices[0].station_name;
							  
							  $("#apiResult").show();								
							  $("#apiResultValue").html(apiResultText);
							  
						  }, "json"
					);
			  }
		);
		
	});		
	</script>
 