<?php
namespace virtual_devices\network_scan;

/*
 Needs:
	nmap tool on console with privileges
*/

/*	$paras = array(
		'device' => '58:1F:AA:B0:31:2A', 
		'host' => '192.168.1.1',
		'timeout' => '10'
	);
*/

	function activateHook() {
		// return an array with the description, and all fields needed for determining to correct sensor state
		return getConfigArray();
	}
	
	function disableHook() {
		// nothing todo for this plugin
	}
	
	function updateHook() {
		return getConfigArray();
	}
	
	function getConfigArray() {
		return $configs = array(
				array('key' => 'mac_client',
						'type' => 'text',
						'description' => 'Mac Address'),
				array('key' => 'snmp_host',
						'type' => 'text',
						'description' => 'Router IP'),
				array('key' => 'timeout',
						'type' => 'text',
						'description' => 'Timeout in minutes'),
				array('key' => 'addSNMPCheck',
						'type' => 'boolean',
						'description' => 'Perform SNMP check'),
				array('key' => 'wakeupWithLastIP',
						'type' => 'boolean',
						'description' => 'Send wakeup signal'),
				array('key' => 'available',
						'type' => 'return',
						'description' => 'Available'),
				array('key' => 'ip',
						'type' => 'return',
						'description'  => 'IP address')
		);
	}
	
	function getDashBoardWidget($lastLogValues, $virtualSensorID) {
		$myPath = getPluginPathToVSensorId($virtualSensorID);
	
		$widget = "";

			$widget.= "<div class='sensor-name'>";
				$widget.= getVirtualSensorDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-location'>";
				$widget.= getVirtualSensorTypeDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-temperature'>";
				$widget.= "<img src='". $myPath."/host.png' alt='icon' />";
				if (isset($lastLogValues['available']) and $lastLogValues['available']==1) {
					$widget.= "&nbsp;yes&nbsp;<span style='font-size:small'>".$lastLogValues['ip']."</span>";	
				} else {
					$widget.= "&nbsp;no&nbsp;";	
				}
			$widget.= "</div>";

			$widget.= "<div class='sensor-timeago'>";
				$timeUpdatedByInsertedLog = getLastVirtualSensorLogTimestamp($virtualSensorID);
				if ($timeUpdatedByInsertedLog==0){
					$widget.= "<abbr class=\"timeago\" title=''>never</abbr>";
				} else {
					$lastLogsTimestamp = getVirtualSensorTmpVal($virtualSensorID, "lastOfflineState");
					$timeStyle = "";
					if ($lastLogsTimestamp>0){
						$timeStyle = "style='color:#580000;'";
					}

					$widget.= "<abbr class=\"timeago\" title='".date("c", $timeUpdatedByInsertedLog)."' ".$timeStyle.">".date("d-m-Y H:i", $timeUpdatedByInsertedLog)."</abbr>";
				}
				
				
				
				
			$widget.= "</div>";
		
		return $widget;
	}
	
	function getVirtualSensorVal($parameter, $virtualSensorID) {
		$device = $parameter['mac_client']['value']; //"58:1F:AA:B0:31:2A";
		$host = $parameter['snmp_host']['value']; //"192.168.1.1";
		$timeout = $parameter['timeout']['value']; //"timeout in minutes";
		$addSNMPCheck = $parameter['addSNMPCheck']['value']; //"check via SNMP?";
		$wakeupWithLastIP = $parameter['wakeupWithLastIP']['value']; //"send NMAP package on port 62078 before testing? --> wakeup iPhone's wifi";

		$available = 0;
		$ip;
		$returnLastLog = false;
		

		// fastpath - ping the ip
		$available = tryPing($parameter, $virtualSensorID);
		if ($available==1){
			$ip = getLastIP($virtualSensorID);
			$returnValArr = array(
					'available'=>$available,
					'ip'=>$ip
			);
			return $returnValArr;
		}
		
	
		if ($wakeupWithLastIP=='true') {
			tryWakeUpWithOldIP($virtualSensorID);
		}
		
		if ($available<=0) {
			$ip = getDeviceIP($host, $device);
			if (isset($ip) and strlen(trim($ip))>0) {
				$available=1;
			}
		}


		if ($available<=0) {
			if ($addSNMPCheck=='true') {
				$available = trySNMPCheck($parameter, $virtualSensorID);
				// if snmp is enabled, we might get true back, but the IP is blank. But this
				// don't effect the availability ... maybe we have no IP for minute or so.
				if($available==1) {
					$returnLastLog = true;
				}
			}
		}
		
		if ($returnLastLog){
			$returnValArr = getLastVirtualSensorLog($virtualSensorID);
		} else {
			$returnValArr = array(
					'available'=>$available,
					'ip'=>$ip
			);
		}
		
		$tmpValKey="lastOfflineState";	
		if ($available == 0) {
			// handle timeout only when go offline
			$returnValArr = checkTimeOut($virtualSensorID, $returnValArr, $timeout, $tmpValKey);
		} else {
			deleteVirtualSensorTmpVal($virtualSensorID, $tmpValKey);
		}
		return $returnValArr;
	}
	
	function tryPing($parameter, $virtualSensorID) {
		$lastIP = getLastIP($virtualSensorID);
	
		$shellCommand = "sudo /usr/bin/nmap -sP --max-retries=1 --host-timeout=125ms ".$lastIP." | grep  'Host is up' | wc -l 2>&1";
		
		$output = shell_exec($shellCommand);
		
		if (strlen(trim($output))>0) {
			if (startsWith($output, '1') == TRUE){
				return 1;
			} else {
				return 0;
			}
			
		}
		return 0;
	}
	
	function checkTimeOut($virtualSensorID, $currentState, $timeout, $tmpValKey) {
		$lastLogsTimestamp = getVirtualSensorTmpVal($virtualSensorID, $tmpValKey);
		$actualTimeStamp = time();
		if (!isset($lastLogsTimestamp)) {
			$lastLogsTimestamp = $actualTimeStamp;
			storeVirtualSensorTmpVal($virtualSensorID, $tmpValKey, $actualTimeStamp);
		}
		
		$timeoutMilliseconds = 60*$timeout;
		$differenceMillis = time() - $lastLogsTimestamp;
		
		//echo "diff = ".$differenceMillis."; act: ".$actualTimeStamp." last: ".$lastLogsTimestamp."\n";
		//echo "timeout: ".$timeoutMilliseconds.", currentDiff: ".$differenceMillis.".\n";
		
		if ($differenceMillis > $timeoutMilliseconds){
			//echo "returning new state\n";
			deleteVirtualSensorTmpVal($virtualSensorID, $tmpValKey);
			return $currentState;
		}
		//echo "returning old state\n";
		
		return $lastLogs = getLastVirtualSensorLog($virtualSensorID);
	}
	
	
	function getDeviceIP($host, $device) {
		$shellCommand = "sudo /usr/bin/nmap -sP ".$host."/24 | grep -B2 -i ".$device." 2>&1";
		$output = shell_exec($shellCommand);
		
		if (strlen(stristr($output,$device))>0) {
			preg_match("/\(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\)/", $output, $matches); 
			$deviceIP = $matches[0];
			
			// remove first and last character because we added them to our regExp
			$deviceIP = substr($deviceIP, 1 , strlen($deviceIP)-2); 

			return $deviceIP;
		} else {
			return null;
		}
	}
	
	function wakeup($ip) {
		// contact the iphone on port 62078 to keep wifi on
		$command = "sudo /usr/bin/nmap -P0 -sT -p62078 ".$ip." 2>&1";
		shell_exec($command);
	}

	function tryWakeUpWithOldIP($virtualSensorID) {
		$ip = getLastIP($virtualSensorID);
		wakeup($ip);
		
		//wait a second
		sleep(3);
	}
	
	function getLastIP($virtualSensorID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select lv.value from ".$db_prefix."virtual_sensors_log l, ".$db_prefix."virtual_sensors_log_values lv where l.id = lv.log_id and lv.value_key='ip' and l.sensor_id=".$virtualSensorID." and lv.value is not null and LENGTH(lv.value)>0 order by l.time_updated desc limit 1";
		$result = $mysqli->query($query);
		$ip_arr = $result->fetch_assoc();
		
		if (sizeof($ip_arr)==1) {
			return $ip_arr['value'];
		} else {
			return "";
		}
	}
	
	
	function trySNMPCheck($parameter, $virtualSensorID) {
		$device = $parameter['mac_client']; //"58:1F:AA:B0:31:2A";
		$host = $parameter['snmp_host']; //"192.168.1.1";
		$community = "public";
		$snmpObj = "1.3.6.1.2.1.3.1.1.2";

		$snmpReturnVals = snmp2_walk($host, $community, $snmpObj);

 		$contains = containsDevice($device, $snmpReturnVals);
		
 		return $contains;
	}	
	
	function containsDevice($device, $snmpReturnVals) {
		foreach ($snmpReturnVals as $returnVal) {
			$hex = getHexCode($returnVal);
			if (strcasecmp($device, $hex)==0) {
				return 1;
			}
		}
		return 0;
	}
	
	function getHexCode($snmpReutnVal) {
		if (startsWith($snmpReutnVal, 'Hex-STRING: ') == TRUE){
			$hex = str_ireplace('Hex-STRING: ', '', $snmpReutnVal);
			$hex = trim($hex);
			$hex = str_ireplace(' ', ':', $hex);
			return $hex;
		} else {
			return null;
		}
	}
	
	function startsWith($haystack, $needle) {
		return !strncmp($haystack, $needle, strlen($needle));
	}

	// marks that the plugin is supporting charts
	// maybe it will transform the data before it will be shown in
	// UI
	function editChartData($virtualSensorID, $chartDataArray) {
		// round each value and convert it into Watt
		$newChartDataArray = array();
		while (list($returnKey, $returnValues) = each($chartDataArray)) { 
			
			//if returnKey == 'available' --> convert and round
			if ($returnKey=='available') {
				$first=true;
				$lastValue = 0;
				while (list($timestamp, $value) = each($returnValues)) { 
					// add extra values to get the on and off-range correctly (last -1 millisecond with old state)
					$timeJS = $sensorData["time_updated"] * 1000;
					if (!$first) {
						$timeEndLast = $timestamp - 1;
						if ($lastValue != $value) {
							$newChartDataArray[$returnKey][$timeEndLast] = $lastValue;
							$newChartDataArray[$returnKey][$timestamp] = $value;
						}
						
					} else {
						$first=false;
						$newChartDataArray[$returnKey][$timestamp] = $value;
					}
					
					$lastValue = $value;
				}
				$newChartDataArray[$returnKey][time()] = $lastValue;
			} 
		}
		return $newChartDataArray;
	}

	// chance to redefine the description and the suffix for the axis
	// like they will shown on the UI
	// --> iterate over the array, included is another array, keys:
	// 0 --> position, don't change
	// 1 --> description
	// 2 --> suffix
	function overwriteChartAxisDefinition($axisDefinition) {
		$newAxisDefinition = array();
		while (list($value_key, $configArray) = each($axisDefinition)) { 
			if ($value_key == 'available') {
				$configArray[2] = "";
				$newAxisDefinition[$value_key] = $configArray;
			}
		}
		return $newAxisDefinition;
	}	
	
	// the value of the returning string will be added to the series 
	// configuration of the HighCharts config Javascript array
	// this is chance to add some configurations to change how
	// the series will be painted 
	function addAxisConfigForView() {
		return "type: 'area',";
	}
	
	// return true if the data should grouped according to the
	// time range selected in the chart to get a better performance
	function groupChartData() {
		return false;
	}

?>
