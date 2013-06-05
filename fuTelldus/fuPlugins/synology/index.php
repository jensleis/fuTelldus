<?php
namespace synology\owl_energy_monitor;
/*
 Needs:
- ssh activated in the NAS
- php-curl
 - ssh2 extension for PHP: apt-get install libssh2-1-dev libssh2-php 
*/

/*	$paras = array(
 		'nas_ip' => '192.168.1.101', // IP of the NAS
 		'nas_port' => '22', // SSH-Port
 		'nas_admin_port' => '5000', // Port of Admin Console
 		'nas_user' => 'root', // username, should be one with correct priveliges
		'nas_password' => 'xxx' //password to username
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
				'nas_ip' => array('IP of the NAS' => 'text'),
				'nas_port' => array('SSH Port of the NAS' => 'text'),
				'nas_admin_port' => array('Port of the admin-console' => 'text'),
				'nas_username' => array('Username' => 'text'),
				'nas_password' => array('Password' => 'password')
		);
	}
	
	// contains the logic to turn the device on
	// return 1 on success, 0 on error
	function switchOn($parameter, $deviceID) {
		// nothing todo. The NAS should turn on automatically
		// when it gets power
		
		return 1;
	}
	
	// should return 1 if the device is on and 0 if off
	function getStatus($parameter, $deviceID) {
		$host = $parameter['nas_ip'];
		$adminport = $parameter['nas_admin_port'];
		
		$handle = curl_init($host);
		curl_setopt($handle, CURLOPT_PORT, $adminport);
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		
		/* Get the HTML or whatever is linked in $url. */
		$response = curl_exec($handle);
		
		/* Check for 404 (file not found). */
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		curl_close($handle);
		
		if($httpCode == 0) {
			return 1;
		} else {
			return 0;
		}
	}
	
	// contains the logic to turn the device off
	// return 1 on success, 0 on error
	function switchOff($parameter, $deviceID) {
		$host = $parameter['nas_ip'];
		$sshport = $parameter['nas_port'];
		$user = $parameter['nas_username'];
		$password = $parameter['nas_password'];
		
		$connection = ssh2_connect($host, $sshport);
		ssh2_auth_password($connection, $user, $password);
		
		// send nas to safe mode
		$stdout = ssh2_exec($connection, '/usr/syno/bin/synologset1 sys warn 0x11300011');
		$stdout = ssh2_exec($connection, '/usr/syno/bin/syno_poweroff_task -s');
		
		$stdout = ssh2_exec($connection, 'exit');
		
		return 1;
	}


?>