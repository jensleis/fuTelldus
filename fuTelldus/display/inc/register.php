<script type="text/javascript">
$(function() {
	$("#register").hide();
	$("#link").hide();
	$("#choose").show();
	$("#waitRegister").hide();

	$("#submitRegistration").click (function() {
		$("#register").hide();
		$("#waitRegister").show();
			
		var user_id=$("#registerButton").data("user");
		var name = $("#registerName").val();
		
		$.ajax( "../api/register/registerNewDevice.php?user_id="+user_id+"&name="+name)
		 .done(function(returnVal) {
			 $("#waitRegister").hide();
			 $("#register").show();
			 $("#register").html("Success. You will be redirected ...");

			 $.ajax( "../api/logout.php").done(function(returnValLogout) {
				 localStorage.setItem("myhopo_device_id", returnVal);
				 setTimeout(function(){}, 3000);

				 window.location = "../index.php";
			 });
		 });
	});

	$("#registerButton").click(function() {
		$("#choose").hide();
		$("#link").hide();

		$("#register").show();
	});


	$( document ).on( 'click', '.submitLink', function () {
		var user_id=$(this).data("user");
		var display_id=$(this).data("display");
		
		 $("#link").html("Success. You will be redirected ...");

		 $.ajax( "../api/logout.php").done(function(returnVal) {
			 localStorage.setItem("myhopo_device_id", display_id);
			 setTimeout(function(){}, 3000);

			 window.location = "../index.php";
		 });


	});

// 	$( ".submitLink" ).click( function () {
// // 		var user_id=event.target.data("user");
// 	   	alert( $(this).attr("data") );
// 	});
		
	$("#linkButton").click(function() {
		$("#choose").hide();
		$("#register").hide();
		
		$("#waitRegister").show();

		var user_id=$("#registerButton").data("user");
		
		$.ajax( "../api/register/getExistingDevices.php?user_id="+user_id)
		 .done(function(returnVal) {
			 $("#waitRegister").hide();
			 $("#link").show();

			 var jsonResult = jQuery.parseJSON(returnVal);

			 for (var i = 0; i < jsonResult.length; i++) {
			    var device = jsonResult[i];
			    var displayName = device.name;
			    var displayID = device.display_id;
			    var userID = device.user_id;

			    var deviceLink = "<a href='#' class='btn-white btn-block submitLink' data-user='"+userID+"' data-display='"+displayID+"'>"+displayName+"</a>";
			    $("#link").append(deviceLink);
			}
		 });
	});
});
</script>

<div id="waitRegister">
	<img src='../../images/ajax-loader2.gif'/>
</div>

<div id="choose">
	<a href="#" class="btn-white btn-block" id="registerButton" data-user="<?php echo $user_id; ?>">Register new</a>
	<a href="#" class="btn-white btn-block" id="linkButton" data-user="<?php echo $user_id; ?>">Link to existing</a>
</div>

<div id="register" style="">
	<form>
		<input type="text" class="form-control" name='registerName' id="registerName" placeholder="<?php echo $lang['Name']; ?>">
		<a href="#" class="btn-white btn-block" id="submitRegistration" data-user="<?php echo $user_id; ?>" style="margin-top:15px">Submit</a>
	</form>
</div>

<div id="link">
	
</div>

<form class='form-horizontal' action='?page=settings_exec&action=userSave&id=$getID' method='POST'>
</form>