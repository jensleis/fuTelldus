<?php

	//echo "<h4>".$lang['General settings']."</h4>";

	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success autohide'>{$lang['Data saved']}</div>";
	}
?>



<form class="form-horizontal" action='?page=settings_exec&action=saveGeneralSettings' method='POST'>


	<fieldset>
		<legend><?php echo $lang['Page settings']; ?></legend>

		<div class="row">
			<br />
			<label class="control-label col-md-3" for="pageTitle"><?php echo $lang['Page title']; ?></label>
			<div class="input-group col-md-5">
				<input type="text" class="form-control" name='pageTitle' id="pageTitle" placeholder="<?php echo $lang['Page title']; ?>" value='<?php  if (isset($config['pagetitle']))echo $config['pagetitle']; ?>'>
			</div>
		</div>

		<div class="row">
			<br />
			<label class="control-label col-md-3" for="mail_from"><?php echo $lang['Outgoing mailaddress']; ?></label>
			<div class="input-group col-md-5">
				<input type="text" class="form-control" name='mail_from' id="mail_from" placeholder="<?php echo $lang['Outgoing mailaddress']; ?>" value='<?php if (isset($config['mail_from'])) echo $config['mail_from']; ?>'>
			</div>
		</div>

		<div class="row">
			<br />
			<label class="control-label col-md-3" for="chart_max_days"><?php echo $lang['Chart max days']; ?></label>
			<div class="input-group col-md-5">
				<input type="text" class="form-control" name='chart_max_days' id="chart_max_days" placeholder="<?php echo $lang['Chart max days']; ?>" value='<?php if (isset($config['chart_max_days'])) echo $config['chart_max_days']; ?>'>
			</div>
		</div>
		
		<div class="row">
			<br />
			<label class="control-label col-md-3" for="pushover_api_token"><?php echo $lang['Pushover API token']; ?></label>
			<div class="input-group col-md-5">
				<input type="text" class="form-control" name='pushover_api_token' id="pushover_api_token" placeholder="<?php echo $lang['Pushover API token']; ?>" value='<?php if (isset($config['pushover_api_token'])) echo $config['pushover_api_token']; ?>'>
			</div>
		</div>

		<?php
			echo "<div class='row'><br />";
				echo "<label class='control-label col-md-3' for='language'>{$lang['Public']} ".strtolower($lang['Language'])."</label>";
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
									if ($config['public_page_language'] == $filename)
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
			<button type="submit" class="btn btn-primary"><?php echo $lang['Save data']; ?></button>
		</div>
	</div>


</form>