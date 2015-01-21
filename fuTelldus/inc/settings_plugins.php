<?php
	
	/* Messages
	--------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success autohide'>Plugin activated</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-success autohide'>Plugin disabled</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-success autohide'>Plugin visiblaty changed</div>";
		if ($_GET['msg'] == 04) echo "<div class='alert alert-success autohide'>Plugin updated</div>";
	}
	
	echo "<h4>Virtual sensor plugin configuration</h4>";
		
	/* Form
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>Available plugins for sensors</legend>";
		$plugins = getAvailablePlugins("sensor");
		printPluginInfos($plugins, "sensor");

	echo "</fieldset>";
	echo "<br /><br />";
	echo "<fieldset>";
		echo "<legend>Available plugins for devices</legend>";
		$plugins = getAvailablePlugins("device");
		printPluginInfos($plugins, "device");
		
	echo "</fieldset>";
	
	function printPluginInfos($plugins, $pluginType){
		global $mysqli;
		global $db_prefix;
		global $lang;
		
		// for every plugin
		foreach (array_keys($plugins) as $key) {
			$activated = isPluginActivated($plugins[$key]["directory"]);
			$name=$plugins[$key]["name"];
			$version=$plugins[$key]["version"];
		
			$query = "select * from ".$db_prefix."plugins  where plugin_path='".$plugins[$key]["directory"]."' and type_description='".$name."'";
			$result = $mysqli->query($query);
			$pluginConfig = $result->fetch_assoc();
		
		
			echo "<div style='border-bottom:1px solid #eaeaea; margin-left:15px; padding:10px;'>";
			echo "<div style='float:right;'>";
		
			echo "<div class='btn-group'>";
			echo "<a class='btn dropdown-toggle btn-success' data-toggle='dropdown' href='#''>";
			echo "{$lang['Action']}";
			echo "<span class='caret'></span>";
						echo "</a>";
		
			echo "<ul class='dropdown-menu'>";
							if ($activated) {
								echo "<li><a href='?page=plugins_exec&action=disablePlugin&id=".$pluginConfig['type_int']."'>Disable</a></li>";
		
								if ($pluginConfig['hidden']==1){
									echo "<li><a href='?page=plugins_exec&action=hidePlugin&id=".$pluginConfig['type_int']."&toHide=0'>Show user configuration</a></li>";
								} else {
									echo "<li><a href='?page=plugins_exec&action=hidePlugin&id=".$pluginConfig['type_int']."&toHide=1'>Hide user configuration</a></li>";
								}
							} else {
								$settings_page = null;
								if (array_key_exists("user-settings-page", $plugins[$key])) {
									$settings_page = $plugins[$key]["user-settings-page"];
								}
								echo "<li><a href='?page=plugins_exec&action=activatePlugin&id=".$plugins[$key]["directory"]."&plugin_name=".$name."&plugin_type=".$pluginType."&version=".$version."&plugin_user_settings_path=".$settings_page."'>Activate</a></li>";
			}
												echo "</ul>";
					echo "</div>";
		
			echo "</div>";
		
		
			$newVersionAvailable=($pluginConfig['activated_version']<$version and $activated);
			$newVersion = $version;
			if (isset($newVersion)and $activated) {
			$version = $pluginConfig['activated_version'];
			}
		
			echo "<p><div style='font-size:20px;'>".$name."</div>";
			if ($activated) {
					echo "<div style='color:green;'>activated";
			if ($pluginConfig['hidden']==1){
						echo "&nbsp;(hidden)";
			}
				echo "</div>";
			} else {
				echo "<div style='color:red;'>disabled</div>";
			}
			echo "</p>";
			// list options
				echo "<div style='font-size:11px;'>";
					$description=$plugins[$key]["description"];
			$author=$plugins[$key]["author"];
					echo "<b>Author:</b> ".$author. "<br />";
					echo "<b>Version:</b> ".$version. "";
							if ($newVersionAvailable){
						echo "<a href='?page=plugins_exec&action=updatePlugin&id=".$pluginConfig['type_int']."&version=".$newVersion."'>";
						echo "<span style='color:blue'> (New version - ".$newVersion." - available. Click to update.) </span>";
			echo "</a>";
					}
					echo "<br />";
					echo "<b>Description:</b> ".$description. "<br />";
									echo "<b>Plugin path:</b> ".$plugins[$key]["directory"]. "<br />";
				echo "</div>";
		
						echo "</div>";
		}	
	
	}

?>
	
</script>


