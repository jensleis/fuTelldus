<?php
	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);


	/* Check access
	--------------------------------------------------------------------------- */
	if ($user['admin'] != 1) {
		if ($getID != $user['user_id']) {
			header("Location: ?page=settings&view=user&action=edit&id={$user['user_id']}");
			exit();
		}
	}

	// Check for action or user is
	if (!isset($_GET['id'])) {
		if (!isset($_GET['action'])) {
			header("Location: ?page=settings&view=users");
			exit();
		}
	}

	
	if (isset($_GET['id'])) {
		/* Get userdata
		 --------------------------------------------------------------------------- */
		$result = $mysqli->query("SELECT * FROM ".$db_prefix."users WHERE user_id='".$getID."'");
		$selectedUser = $result->fetch_array();
	}


	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success autohide'>".$lang['Userdata updated']."</div>";
		elseif ($_GET['msg'] == 02) echo "<div class='alert alert-danger autohide'>".$lang['Old password is wrong']."</div>";
		elseif ($_GET['msg'] == 03) echo "<div class='alert alert-danger autohide'>".$lang['New password does not match']."</div>";
		elseif ($_GET['msg'] == 04) echo "<div class='alert alert-info autohide'>".$lang['Test message sent']."</div>";
	}


	echo "<h4>".$lang['Usersettings']."</h4>";
	

	if ($action == "edit")
		echo "<form class='form-horizontal' action='?page=settings_exec&action=userSave&id=$getID' method='POST'>";
	else
		echo "<form class='form-horizontal' action='?page=settings_exec&action=userAdd' method='POST'>";
?>



	<fieldset>
		<legend><?php echo $lang['Login']; ?></legend>
	
				<div class="row">
					<label class="control-label col-md-3" for="inputEmail"><?php echo $lang['Email']; ?></label>
					<div class="input-group col-md-5">
						<input type="text" class="form-control" name='inputEmail' id="inputEmail" placeholder="<?php echo $lang['Email']; ?>" value='<?php if (isset($selectedUser)) echo $selectedUser['mail']; ?>'>
					</div>
				</div>
				
				<div class="row">
					<br />
					<div class="col-md-5 col-md-push-3"> 
						<i><?php echo $lang['Leave field to keep current']; ?></i>
					</div>
				</div>
				
				<div class="row">
					<label class="control-label col-md-3" for="inputPassword"><?php echo $lang['New'] . " " . strtolower($lang['Password']); ?> </label>
					<div class="input-group col-md-5">
						<input type="password" class="form-control" name='newPassword' id="newPassword" placeholder="<?php echo $lang['New'] . " " . strtolower($lang['Password']); ?>" autocomplete="off">
					</div>
				</div>
				
				<div class="row">
					<br />
					<label class="control-label col-md-3" for="'newCPassword'"><?php echo $lang['Repeat'] . " " . strtolower($lang['Password']); ?></label>
					<div class="input-group col-md-5">
						<input type="password" class="form-control" name='newCPassword' id="newCPassword" placeholder="<?php echo $lang['Repeat'] . " " . strtolower($lang['Password']); ?>" autocomplete="off">
					</div>	
				</div>


		<?php
			if ($user['admin'] == 1) {
				echo "<div class='row'><br />";
					echo "<label class='control-label col-md-3'>Admin</label>";
					
						echo "<div class='input-group col-md-5' style='display: inline-block !important; vertical-align: middle !important;'>";
							$adminChecked="";
							if (isset($selectedUser)) {
								if ($selectedUser['admin'] == 1) $adminChecked = "checked='checked'";
							}
							
				          echo "<input type='checkbox' name='admin' value='1'". $adminChecked." >";
				        echo "</div>";
				echo "</div>";
			}
		?>

	</fieldset>
	<br />
	
	<fieldset>
		<legend><?php echo $lang['Notification']; ?></legend>
			<div class="row">
				<label class="control-label col-md-3" for="'pushover_key'"><?php echo $lang['Pushover key']; ?></label>
				<div class="input-group col-md-5">
					<input type="text" class="form-control" name="pushover_key" id="pushover_key" placeholder="<?php echo $lang['Pushover key']; ?>"  value='<?php if (isset($selectedUser)) echo $selectedUser['pushover_key']; ?>'>
					<span class="input-group-btn">
			        	<a class='btn btn-success' style='' href='#test_notification' data-toggle='modal'>Test notification</a>
			      </span>
				</div>
			</div>
	</fieldset>
	<br />
	
	<!-- The modal test dialog for notifications -->
	<div class="modal fade" id="test_notification" >
	 	<div class="modal-dialog">
	 		<div class="modal-content">
				<div class="modal-header">
					<a class="close" data-dismiss="modal">&times;</a>
					<h3><?php echo $lang['Send Notification'] ?></h3>
				</div>
				<div class="modal-body">
					<p><b><?php echo $lang['Your Pushover key'] ?>:</b> <?php if (isset($selectedUser)) echo $selectedUser['pushover_key'] ?> </p>
					<!--<p><b><?php echo $lang['Select device'] ?>:</b> -->
				</div>
				<div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
         		 	<a class="btn btn-success" id="send_notification" href="?page=settings_exec&action=sendTestNotification&pushover_key=<?php if (isset($selectedUser)) echo $selectedUser['pushover_key'] ?>&subject=Test&message=Test notification&id=<?php echo $getID ?>"><?php echo $lang['Send'] ?></a>
				
				</div>
			</div>
		</div>
	</div>
	
	<!-- sendNotification($selectedUser['pushover_key'], "Test", "Test message")  -->
	<fieldset>
		<legend><?php echo $lang['Language']; ?></legend>
		<?php
			echo "<div class='row'>";
				echo "<label class='control-label col-md-3' for='language'>".$lang['User language']."</label>";
				echo "<div class='input-group col-md-5'>";

					echo "<label class='language'>";
						$sourcePath = "lib/languages/";
						$sourcePath = utf8_decode($sourcePath); // Encode for æøå-characters
						$handler = opendir($sourcePath);
						
						echo "<select name='language' class='form-control'>";
							while ($file = readdir($handler)) {
								$file = utf8_encode($file); // Encode for æøå-characters
								
								list($filename, $ext) = explode(".", $file);

								if ($ext == "php") {
									if ($defaultLang == $filename)
										echo "<option value='$filename' selected='selected'>$filename</option>";

									else
										echo "<option value='$filename'>$filename</option>";
								}
							}
			      	  	echo "</select>";
			        echo "</label>";

				echo "</div>";
			echo "</div>";
		?>
	</fieldset>
	<br />

	<div class="form-group">
		<div class="controls pull-right">
			<?php
				if (isset($getID) && $getID == $user['user_id']) {
					echo "<a class='btn btn-warning' style='margin-right:15px;' href='login/logout.php' onclick=\"return confirm('Are you sure?')\">".$lang['Log out']."</a>";
				}

				if ($action == "edit") {
					echo "<button type='submit' class='btn btn-primary'>".$lang['Save data']."</button>";
				} else {
					echo "<button type='submit' class='btn btn-success'>".$lang['Create user']."</button>";
				}
			?>	
		</div>
	</div>


</form>