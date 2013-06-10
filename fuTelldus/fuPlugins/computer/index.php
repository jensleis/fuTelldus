<?php
namespace computer\owl_energy_monitor;
/*
 Needs:
 apt-get install samba-common
 plugin: network_scan
 
 windows 7
 	http://www.howtogeek.com/howto/windows-vista/enable-mapping-to-hostnamec-share-on-windows-vista/
 
 windows 8:
 	sc config RemoteRegistry start= auto
	sc start RemoteRegistry
*/

/*
 * Works only on linux-machines to control windows machines
 * 
 */

/*	$paras = array(
 		'pc_mac' => 00-19-66-89-F8-60', // Mac of the PC:  
 		'ip_router' => '192.168.1.1', IP of the router, 255.255.255.255 for broadcast
);
*/
	function activateHook() {
		/*$dirs = getPluginDirs();
		foreach (array_keys($dirs) as $key) {
			$name = $dirs[$key];
			if (strpos($name, "network_scan") !=false) {
			echo $isPluginActivated($name);
				if ($isPluginActivated($name)==1) {
					return getConfigArray();
				}	
			}
		}
		// throw error
		throw new Exception("Dependend plugin 'network_scan' not activated");*/
		
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
				'device' => array('Network device' => 'plugin;sensor;network_scan'),
				'remote_user' => array('Remote user' => 'text'),
				'remote_password' => array('Remote password' => 'password'),
		);
	}
	
	// contains the logic to turn the device on
	// return 1 on success, 0 on error
	function switchOn($parameter, $deviceID) {
		$pluginID = $parameter['device'];
		$mac = getPluginParameters($pluginID)['mac_client'];
		$return = wake(null, $mac);
		return $return;
	}
	
	// should return 1 if the device is on and 0 if off
	function getStatus($parameter, $deviceID) {
		$pluginID = $parameter['device'];
		
		// overwrite parameter timout
		$parameter = getPluginParameters($pluginID);
		$parameter['timeout'] = 0;
		
		$currentState = getCurrentVirtualSensorStateWithParameter($pluginID, $parameter);
		return $currentState['available'];
	}
	
	// contains the logic to turn the device off
	// return 1 on success, 0 on error
	function switchOff($parameter, $deviceID) {
		$pluginID = $parameter['device'];
		$mac = getPluginParameters($pluginID)['mac_client'];
		$ip = getPluginParameters($pluginID)['snmp_host'];
		$remoteUser = $parameter['remote_user'];
		$remotePassword = $parameter['remote_password'];
		$deviceIP = getDeviceIP($ip, $mac);
		
		shell_exec("net rpc shutdown -I ".$deviceIP." -U ".$remoteUser."%".$remotePassword."");
		return 1;
	}
	
	function wake($addr, $mac) { 
		$addr_byte = explode(':', $mac); 
		$hw_addr = ''; 

		for ($a=0; $a < 6; $a++) $hw_addr .= chr(hexdec($addr_byte[$a])); 

		$msg = chr(255).chr(255).chr(255).chr(255).chr(255).chr(255); 

		for ($a = 1; $a <= 16; $a++)    $msg .= $hw_addr; 

		 // send it to the broadcast address using UDP 
		 // SQL_BROADCAST option isn't help!! 
		$s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); 
		if ($s == false) { 
			//echo "Error creating socket!\n"; 
			//echo "Error code is '".socket_last_error($s)."' - " . socket_strerror(socket_last_error($s)); 
			return 0;
		} else { 
			// setting a broadcast option to socket: 
			$opt_ret =  socket_set_option($s, 1, 6, TRUE); 
			if($opt_ret < 0) { 
				//echo "setsockopt() failed, error: " . strerror($opt_ret) . "\n"; 
				return 0;
			} 
			if (empty($addr)) {
				$addr = "255.255.255.255";
			}
			$e = socket_sendto($s, $msg, strlen($msg), 0, $addr, 2050); 
			socket_close($s); 
			//echo "Magic Packet sent (".$e.") to ".$addr.", MAC=".$mac; 
			return 1;
		} 
	}

	function getDeviceIP($host, $device) {
		$shellCommand = "sudo nmap -sP ".$host."/24 | grep -B2 -i ".$device." 2>&1";
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

?>