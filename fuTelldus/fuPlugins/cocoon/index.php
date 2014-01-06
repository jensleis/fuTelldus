<?php
namespace cocoon\control_cocoon;
/*
 Needs:
*/


/*	$paras = array(
 		'cocoon_mac' => 00-19-66-89-F8-60', // Mac of the PC:  
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
				'cocoon_mac' => array('Cocoon device MAC' => 'text'),
				'snmp_host' => array('SNMP Host IP' => 'text'),
		);
	}
	
	// contains the logic to turn the device on
	// return 1 on success, 0 on error
	function switchOn($parameter, $deviceID) {
		$cocoonMac = $parameter['cocoon_mac'];
		$routerIP = $parameter['snmp_host'];
		
		//getDeviceIP
		$cocoonIP = getDeviceIP($routerIP, $cocoonMac);
		
		//send th request
		$cmdValue = '<control>SetPower</control><value>ON</value>';
		$postCmd = buildPostParameters($cmdValue);
		$result = sendPostCommandToCocoon($cocoonIP, $postCmd);
		
 		//parse the xml result
 		$xml = simplexml_load_string($result);
 		$cocoonResponse = (string) $xml->cmd[0]->value[0];

  		if(strcasecmp($cocoonResponse, "OK") == 0) {
  			return 1;
  		}
		
  		return 0;
	}
	
	// should return 1 if the device is on and 0 if off
	function getStatus($parameter, $deviceID) {
		$cocoonMac = $parameter['cocoon_mac'];
		$routerIP = $parameter['snmp_host'];
		
		//getDeviceIP
		$cocoonIP = getDeviceIP($routerIP, $cocoonMac);

		//send th request
		$result =sendPostCommandToCocoon($cocoonIP, buildPostParameters("GetPowerStatus"));

		//parse the xml result
 		$xml = simplexml_load_string($result);
 		$cocoonstate = (string) $xml->cmd[0]->status[0];
 		
		return $cocoonstate;
	}
	
	function sendPostCommandToCocoon($cocoonIP, $request) {
		$url = "http://".$cocoonIP.":4001/goform/formAppCommand.xml";
		
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
	
	function buildPostParameters($command) {
		return "<?xml version=\"1.0\" encoding=\"utf-8\" ?><tx><cmd id=\"1\">".$command."</cmd></tx>";
	}
	
	// contains the logic to turn the device off
	// return 1 on success, 0 on error
	function switchOff($parameter, $deviceID) {
		$cocoonMac = $parameter['cocoon_mac'];
		$routerIP = $parameter['snmp_host'];
		
		//getDeviceIP
		$cocoonIP = getDeviceIP($routerIP, $cocoonMac);
		
		//send th request
		$cmdValue = '<control>SetPower</control><value>STANDBY</value>';
		$postCmd = buildPostParameters($cmdValue);
		$result = sendPostCommandToCocoon($cocoonIP, $postCmd);
		
 		//parse the xml result
 		$xml = simplexml_load_string($result);
 		$cocoonResponse = (string) $xml->cmd[0]->value[0];
		
 		if(strcasecmp($cocoonResponse, "OK") == 0) {
 			return 1;
 		}
		
 		return 0;
	}

	function getDeviceIP($host, $device) {
		$shellCommand = "sudo nmap -sP ".$host."/24 | grep -B2 -i ".$device." 2>&1";
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