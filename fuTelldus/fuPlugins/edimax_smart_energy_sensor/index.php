<?php
namespace edimax_smart_energy\edimax_smart_energy_device;
/*
 Needs:
*/


/*	$paras = array(
 		'edimax_mac' => 00-19-66-89-F8-60', // Mac of the PC:  
 		'snmp_host' => '192.168.1.1', IP of the router, 255.255.255.255 for broadcast
);
*/
	function activateHook() {
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
				array('key' => 'device_id',
						'type' => 'callBackMethodReturnList;listExistingDevices',
						'description' => 'Edimax device'),
				array('key' => 'last_toggle',
						'type' => 'return',
						'description' => 'Last toggle'),		
				array('key' => 'now_ampere',
						'type' => 'return',
						'description' => 'Ampere'),
				array('key' => 'now_watt',
						'type' => 'return',
						'description' => 'Watt'),
				array('key' => 'now_energy_day',
						'type' => 'return',
						'description' => 'Today kw/h'),
				array('key' => 'now_energy_week',
						'type' => 'return',
						'description' => 'Week kw/h'), 
				array('key' => 'now_energy_month',
						'type' => 'return',
						'description' => 'Month kw/h')
		);
	}

	function listExistingDevices($params) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select vd.id as id, vd.description as name from ".$db_prefix."plugins p INNER join ".$db_prefix."virtual_devices vd on vd.plugin_id = p.type_int where type_description='Edimax Smart Plug SP 2101W Device' and plugin_type='device'";
		
		$result = $mysqli->query($query);
		
		$returnVal = array();
		while ($row = $result->fetch_array()) {
			$id = $row['id'];
			$name = $row['name'];
			array_push($returnVal, array('id' => $id, 'name' => $name));
		}
		
		return $returnVal;
	}
	
	function getDashBoardWidget($lastLogValues, $virtualSensorID) {
		$myPath = getPluginPathToVSensorId($virtualSensorID);
	
		$consumptionAmpere = $lastLogValues['now_ampere'];
		$consumptionWatt = $lastLogValues['now_watt'];

		$widget = "";

			$widget.= "<div class='sensor-name'>";
				$widget.= getVirtualSensorDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-location'>";
				$widget.= getVirtualSensorTypeDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-temperature'>";
					$widget.= "<img src='". $myPath."/icon.png' alt='icon' />";
					$widget.= "&nbsp;".$consumptionWatt."&nbsp;W&nbsp;<span style='font-size:small'>".$consumptionAmpere."&nbsp;A</span>";	
			$widget.= "</div>";

			$widget.= "<div class='sensor-timeago'>";
				$timeUpdatedByInsertedLog = getLastVirtualSensorLogTimestamp($virtualSensorID);
				if ($timeUpdatedByInsertedLog==0){
					$timeUpdatedByInsertedLog = time();
				}
				
				$widget.= "<abbr class=\"timeago\" title='".date("c", $timeUpdatedByInsertedLog)."'>".date("d-m-Y H:i", $timeUpdatedByInsertedLog)."</abbr>";
			$widget.= "</div>";	
		
		return $widget;
	}
	
	function getVirtualSensorVal($parameter, $virtualSensorID) {
		$device = $parameter['device_id']['value'];
	
		$deviceparameter = getPluginParametersWithoutUser($device, 'device', $userID);
		
		$edimaxMac = $deviceparameter['edimax_2101_mac']['value'];
		$edimaxUser = $deviceparameter['edimax_user']['value'];
		$edimaxPassword = $deviceparameter['edimax_password']['value'];
		$routerIP = $deviceparameter['snmp_host']['value'];
		
		$edimaxIP = getDeviceIP($routerIP, $edimaxMac);
		
		//send the request
		$requestXML = buildPostParameters("NOW_POWER", "get", "");
		$result =sendPostCommandToEdimax($edimaxIP, $edimaxUser, $edimaxPassword, $requestXML);
		
		//parse the xml result
		$xml = simplexml_load_string($result);

		$command_toggle="Device.System.Power.LastToggleTime";
		$command_ampere="Device.System.Power.NowCurrent";
		$command_watt="Device.System.Power.NowPower";
		$command_day="Device.System.Power.NowEnergy.Day";
		$command_week="Device.System.Power.NowEnergy.Week";
		$command_month="Device.System.Power.NowEnergy.Month";

		
		$lastToggleDate = date_create_from_format('YmdGis', (string) $xml->CMD->NOW_POWER->$command_toggle);
		$lastToggleFormatted = date_format($lastToggleDate, 'd.m.Y G:i:s');
		
		$returnValArr = array(
				'last_toggle'=> $lastToggleFormatted,
				'now_ampere'=> (string) $xml->CMD->NOW_POWER->$command_ampere,
				'now_watt'=> (string) $xml->CMD->NOW_POWER->$command_watt,
				'now_energy_day'=> (string) $xml->CMD->NOW_POWER->$command_day,
				'now_energy_week'=> (string) $xml->CMD->NOW_POWER->$command_day,
				'now_energy_month'=> (string) $xml->CMD->NOW_POWER->$command_month	
		);

 		return $returnValArr;
	}
	
	function sendPostCommandToEdimax($edimaxIP, $edimaxUser, $edimaxPassword, $request) {
		$url = "http://".$edimaxUser.":".$edimaxPassword."@".$edimaxIP.":10000/smartplug.cgi";

		// create and send the HTTPPost Request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "XML=".$request."");
		$result=curl_exec($ch);
		
		return $result;
	}
	
	function buildPostParameters($command, $action, $statuse) {
		$xml = "<?xml version=\"1.0\" encoding=\"UTF8\"?>";
		$xml = $xml."<SMARTPLUG id=\"edimax\">";
		$xml = $xml."<CMD id=\"".$action."\"><".$command.">".$statuse."</".$command.">";
		$xml = $xml."</CMD></SMARTPLUG>";
		
		return $xml;
	}
	

	function getDeviceIP($host, $device) {
// 		return "192.168.0.13";
		$shellCommand = "sudo /usr/bin/nmap -sP ".$host."/24 | grep -B2 -i ".$device." 2>&1";
		$output = shell_exec($shellCommand);
	
		if (strlen(stristr($output,$device))>0) {
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $output, $matches);
			$deviceIP = $matches[0];
	
			return $deviceIP;
		} else {
			return null;
		}
	}
	
	// marks that the plugin is supporting charts
	// maybe it will transform the data before it will be shown in
	// UI
	function editChartData($virtualSensorID, $chartDataArray) {
		return $chartDataArray;
	}
	
	// chance to redefine the description and the suffix for the axis
	// like they will shown on the UI
	// --> iterate over the array, included is another array, keys:
	// 0 --> position, don't change
	// 1 --> description
	// 2 --> suffix
	function overwriteChartAxisDefinition($axisDefinition) {
		return $axisDefinition;
	}
	
	// the value of the returning string will be added to the series
	// configuration of the HighCharts config Javascript array
	// this is chance to add some configurations to change how
	// the series will be painted
	function addAxisConfigForView() {
		return "type: 'area',";
	}
?>