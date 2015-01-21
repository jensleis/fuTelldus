<?php


	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success autohide'>".$lang['User added']."</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-info autohide'>".$lang['Userdata updated']."</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-info autohide'>".$lang['User deleted']."</div>";
	}
	echo "<h3>".$lang['Users']."</h3>";

	echo "<div class='row'>";
		echo "<div class='col-md-10'>";
			echo "Configuragion page for all users. Users tagged with 'Google' are verified by Google and are able to use further functionalities (e.g. Google Drive).";
		echo "<div>";
	echo "</div>";
		
	echo "<div class='row'>";
		echo "<div class='' style='float:right;'>";
			echo "<a class='btn btn-success' href='?page=settings&view=user&action=create'>".$lang['Create user']."</a>";
		echo "</div>";
	echo "</div>";

	echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>#</th>";
					echo "<th>".$lang['Email']."</th>";
					echo "<th>".$lang['Admin']."</th>";
					echo "<th>Google</th>";
					echo "<th></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
	
				$query = "SELECT * FROM ".$db_prefix."users ORDER BY mail ASC";
			    $result = $mysqli->query($query);
			    while($row = $result->fetch_array()) {
					echo "<tr>";
	
						echo "<td>".$row['user_id']."</td>";
						echo "<td>".$row['mail']."</td>";
						
						echo "<td>";
							if ($row['admin'] == 1) echo "<img style='height:16px;' src='images/metro_black/check.png' alt='icon' />";
						echo "</td>";
	
						echo "<td>";
							if ($row['account_type'] == 1) echo "<img style='height:16px;' src='images/metro_black/check.png' alt='icon' />";
						echo "</td>";
						
						echo "<td style='text-align:right;'>";
							echo "<a class='btn btn-default' href='?page=settings&view=user&action=edit&id={$row['user_id']}'>".$lang['Edit']."</a> &nbsp; ";
							echo "<a class='btn btn-danger' href='?page=settings_exec&action=userDelete&id={$row['user_id']}' onclick=\"return confirm('".$lang['Are you sure you want to delete']."')\">".$lang['Delete']."</a>";
						echo "</td>";

						
					echo "</tr>";
				}
	
	
			echo "</tbody>";
		echo "</table>";
		echo "</div>";
	echo "</div>";
?>