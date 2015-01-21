<!-- 
	all user configurations are sotred within the array $pluginConfig 
	this array is also including the data from the database
	all fields which have the key of the config value set, will saved to the db automatically

-->

<fieldset>
		<legend>Telldus devices settings</legend>

		<div class="form-group">

			<div class="row" style='margin-top:15px'>
				<label class="control-label col-md-3" for="telldus_public_key"><?php echo $pluginConfig['telldus_public_key']['description']; ?></label>
				<div class="input-group col-md-5">
					<input class='form-control' style='width:350px;' type="text" name='telldus_public_key' id="telldus_public_key" placeholder="<?php echo $pluginConfig['telldus_public_key']['description']; ?>" value='<?php echo $pluginConfig['telldus_public_key']['value']; ?>'>
				</div>
			</div>

			<div class="row" style='margin-top:15px'>
				<label class="control-label col-md-3" for="telldus_private_key"><?php echo $pluginConfig['telldus_private_key']['description']; ?></label>
				<div class="input-group col-md-5">
					<input class='form-control' style='width:350px;' type="text" name='telldus_private_key' id="telldus_private_key" placeholder="<?php echo $pluginConfig['telldus_private_key']['description']; ?>" value='<?php echo $pluginConfig['telldus_private_key']['value']; ?>'>
				</div>
			</div>

			<div class="row" style='margin-top:15px'>
				<label class="control-label col-md-3" for="telldus_token"><?php echo $pluginConfig['telldus_token']['description']; ?></label>
				<div class="input-group col-md-5">
					<input class='form-control' style='width:350px;' type="text" name='telldus_token' id="telldus_token" placeholder="<?php echo $pluginConfig['telldus_token']['description']; ?>" value='<?php echo $pluginConfig['telldus_token']['value']; ?>'>
				</div>
			</div>

			<div class="row" style='margin-top:15px'>
				<label class="control-label col-md-3" for="telldus_token_secret"><?php echo $pluginConfig['telldus_token_secret']['description']; ?></label>
				<div class="input-group col-md-5">
					<input class='form-control' style='width:350px;' type="text" name='telldus_token_secret' id="telldus_token_secret" placeholder="<?php echo $pluginConfig['telldus_token_secret']['description']; ?>" value='<?php echo $pluginConfig['telldus_token_secret']['value']; ?>'>
				</div>
			</div>
		</div>

	</fieldset>
	
 