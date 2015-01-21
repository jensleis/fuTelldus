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
				array('key' => 'edimax_2101_mac',
						'type' => 'text',
						'description' => 'Edimax SP2101W device MAC'),
				array('key' => 'edimax_user',
						'type' => 'text',
						'description' => 'User'),
				array('key' => 'edimax_password',
						'type' => 'password',
						'description' => 'Password'),
				array('key' => 'snmp_host',
						'type' => 'text',
						'description' => 'SNMP Host IP')
		);
	}
	
	// contains the logic to turn the device on
	// return 1 on success, 0 on error
	function switchOn($parameter, $deviceID) {
		$edimaxMac = $parameter['edimax_2101_mac']['value'];
		$edimaxUser = $parameter['edimax_user']['value'];
		$edimaxPassword = $parameter['edimax_password']['value'];
		$routerIP = $parameter['snmp_host']['value'];
		
		//getDeviceIP
		$edimaxIP = getDeviceIP($routerIP, $edimaxMac);

		//send th request
		$command="Device.System.Power.State";
		$requestXML = buildPostParameters($command, "setup" ,"ON");
		$result =sendPostCommandToEdimax($edimaxIP, $edimaxUser, $edimaxPassword, $requestXML);
		
 		//parse the xml result
 		$xml = simplexml_load_string($result);
 		$edimaxResponse = (string) $xml->CMD;

  		if(strcasecmp($edimaxResponse, "OK") == 0) {
  			return 1;
  		}
		
  		return 0;
	}
	
	// should return 1 if the device is on and 0 if off
	function getStatus($parameter, $deviceID) {
		$edimaxUser = $parameter['edimax_user']['value'];
		$edimaxPassword = $parameter['edimax_password']['value'];
		$edimaxMac = $parameter['edimax_2101_mac']['value'];
		$routerIP = $parameter['snmp_host']['value'];

		//getDeviceIP
		$edimaxIP = getDeviceIP($routerIP, $edimaxMac);
		
		//send the request
		$command="Device.System.Power.State";
		$requestXML = buildPostParameters($command, "get", "");
		$result =sendPostCommandToEdimax($edimaxIP, $edimaxUser, $edimaxPassword, $requestXML);

		//parse the xml result
 		$xml = simplexml_load_string($result);
 		$edimaxstate = $xml->CMD->$command;

 		if (strcmp($edimaxstate, "ON")==0) {
 			return 1;
 		} else if (strcmp($edimaxstate, "OFF")==0) {
 			return 0;
 		}
 		
 		return -1;
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
	
	// contains the logic to turn the device off
	// return 1 on success, 0 on error
	function switchOff($parameter, $deviceID) {
				$edimaxMac = $parameter['edimax_2101_mac']['value'];
		$edimaxUser = $parameter['edimax_user']['value'];
		$edimaxPassword = $parameter['edimax_password']['value'];
		$routerIP = $parameter['snmp_host']['value'];
		
		//getDeviceIP
		$edimaxIP = getDeviceIP($routerIP, $edimaxMac);

		//send th request
		$command="Device.System.Power.State";
		$requestXML = buildPostParameters($command, "setup" ,"OFF");
		$result =sendPostCommandToEdimax($edimaxIP, $edimaxUser, $edimaxPassword, $requestXML);
		
 		//parse the xml result
 		$xml = simplexml_load_string($result);
 		$edimaxResponse = (string) $xml->CMD;

  		if(strcasecmp($edimaxResponse, "OK") == 0) {
  			return 1;
  		}
		
  		return 0;
	}

	function getDeviceIP($host, $device) {
// return "192.168.0.13";

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

?>