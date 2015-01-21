<div class="container">

	<fieldset>
		<div class="form-group">

			<div class="row" style='margin-top:15px'>
				<label class="control-label col-md-3" for="userEmail">User eMail</label>
				<div class="input-group col-md-5">
					<input class='form-control' style='width:350px;' type="text" name='userEmail' id="userEmail" placeholder="User eMail" value=''>
					<span style="margin-left:15px" id="userinfo"></span>
				</div>
			</div>
			
		</div>
	</fieldset>
</div>

<script>
	$( "#userEmail" ).blur(function() {
		var userMail = $( "#userEmail" ).val();

		$.getJSON('inc/settings/getUser.php?email='+userMail, function(data) {
			  if (data!=null && data.mail!=null && data.mail.toLowerCase() ===  userMail.toLowerCase()) {
				  $( "#userinfo" ).html("OK");
			  } else {
				  $( "#userinfo" ).html("not OK");
			  }
			  
		});
    	
  	})
</script>
<?php

echo "";

?>