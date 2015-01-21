<?php

	function getActions($userID) {
		$returnVal = array(
					0 => array('id' => '0', 'description' => 'Send push notification'),
					1 => array('id' => '1', 'description' => 'Send eMail')		
				);	
		return $returnVal;
	}

?>