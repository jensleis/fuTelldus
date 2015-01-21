<?php
 

  	/* Get parameters
  	--------------------------------------------------------------------------- */
  	if (isset($_GET['id'])) 		  $getID 	= clean($_GET['id']);
  	if (isset($_GET['action'])) 	$action = clean($_GET['action']);
  	

    if (isset($_GET['view'])) {
      $view = clean($_GET['view']);
    } else {
      header("Location: ?page=settings&view=user&action=edit&id={$user['user_id']}");
      exit();
    }

  ?>

<!--   <div class='hidden-xs' style='height:30px;'> -->
<!--   </div> -->

<!--   <div class="container"> -->
<!--     <div class="row"> -->
      <div class="col-md-2" style="margin-top:15px;">
        <div class="sidebar-nav">
          <ul class="nav nav-stacked">
            <li class="nav-header disabled"><p><?php echo $lang['Settings']; ?></p></li>

            <?php
			  $vActive_virtualsensors = "";
			  $vActive_virtualdevices = "";
			  $vActive_general = "";
			  $vActive_user = "";
			  $vActive_notifications = "";
			  $vActive_schedule = "";
			  $vActive_display = "";
			  $vActive_cron = "";
			  $vActive_plugins = "";
			  $vActive_telldusTest = "";
			  $vActive_users = "";
			  
              if ($view == "general") $vActive_general = "active";
              if ($view == "user") $vActive_user = "active";
			  if ($view == "virtualsensors") $vActive_virtualsensors = "active";
			  if ($view == "virtualdevices") $vActive_virtualdevices = "active";
              if ($view == "notifications") $vActive_notifications = "active";
              if ($view == "schedule") $vActive_schedule = "active";
              if ($view == "display") $vActive_display = "active";
              if ($view == "cron") $vActive_cron = "active";
			  if ($view == "plugins") $vActive_plugins = "active";
              if (substr($view, 0, 12) == "telldus_test") $vActive_telldusTest = "active";
              if (substr($view, 0, 5) == "users") $vActive_users = "active";


              echo "<li class='$vActive_user'><a href='?page=settings&view=user&action=edit&id={$user['user_id']}'>{$lang['Userprofile']}</a></li>";
			  echo "<li class='$vActive_virtualsensors'><a href='?page=settings&view=virtualsensors'>{$lang['Sensors']}</a></li>";
			  echo "<li class='$vActive_virtualdevices'><a href='?page=settings&view=virtualdevices'>{$lang['Devices']}</a></li>";
              echo "<li class='$vActive_schedule'><a href='?page=settings&view=schedule'>{$lang['Schedule']}</a></li>";
              echo "<li class='$vActive_display'><a href='?page=settings&view=display&id={$user['user_id']}'>Display</a></li>";
              //echo "<li class='$vActive_telldusTest'><a href='?page=settings&view=telldus_test'>{$lang['Telldus connection test']}</a></li>";

              echo " <li class='nav-divider'></li><li class='nav-header disabled'><p>Plugin User Settings</p></li>";
              $plugins = getActivatedAvailablePlugins();
              foreach (array_keys($plugins) as $key) {
              	$name=$plugins[$key]["name"];
              	$pluginID = getPluginIDToPluginPath($plugins[$key]["directory"]);
              	$pluginConfig = getPluginUserSettingsPath($pluginID);
              	if (! empty($pluginConfig)) {
              		$activated = "";
              		if (isset($_GET['pluginID']) && $_GET['pluginID'] == $pluginID) {
              			$activated="active";
              		}
              		echo "<li class='$activated'><a href='?page=settings&view=plugin&pluginID=".$pluginID."'>".$name."</a></li>";
              	}
              }
              
              if ($user['admin'] == 1) {
                echo "<li class='nav-divider'><li class='nav-header disabled'><p>Admin</p></li>";

                echo "<li class='$vActive_general'><a href='?page=settings&view=general'>".$lang['Page settings']."</a></li>";
                echo "<li class='$vActive_users'><a href='?page=settings&view=users'>".$lang['Users']."</a></li>";
                echo "<li class='$vActive_cron'><a href='?page=settings&view=cron'>Batch processing</a></li>";
				              
				echo "<li class='nav-divider'><li class='nav-header disabled'><p>System configuration</p></li>";
				echo "<li class='$vActive_plugins'><a href='?page=settings&view=plugins'>".$lang['Plugins']."</a></li>";
              }
            ?>



          </ul>
        </div><!--/.well -->
      </div><!--/span-->


      <div class="col-md-1">
       </div>


      <div class="col-md-7" id="settingsmain" name="settingsmain">
      	<div class='hidden-xs' style='height:30px;'></div>
      	<?php
      		if (isset($_GET['view'])) {
				if (isset($_GET['pluginID'])){
					$pluginID = $_GET['pluginID'];
					$pluginConfigPath = getPluginUserSettingsPath($pluginID);
					$pluginConfig = getPluginUserConfigArrayWithValues($user['user_id'], $pluginID);
					
					if (isset($_GET['msg'])) {
						if ($_GET['msg'] == 01) echo "<div class='alert alert-success autohide'>User settings for plugin updated</div>";
					}
					
					echo "<form class='form-horizontal' action='?page=settings_exec&action=savePluginUserConfig&pluginID=".$pluginID."&id=".$user['user_id']."' method='POST'>";
					include($pluginConfigPath);
					
					echo "	<div class='form-group'>";
					echo "		<div class='controls pull-right'>";
					echo "			<button type='submit' class='btn btn-primary'>Save Settings</button>";
					echo "		</div>";
					echo "	</div>";
					echo "</form>";
				} else {
      				include("inc/settings_" . $view . ".php");
      			}
			} else {
      			include("inc/settings_general.php");
      		}
      	?>
<!--       </div> -->
<!--     </div> -->
	  </div>
	  
	   <div class="col-md-2">
       </div>