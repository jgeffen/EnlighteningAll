<?php
	/*
	Copyright (c) 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/
	
	// Check Logged In
	if(Membership::LoggedIn()) {
		// Variable Defaults
		$member = new Membership();
		
		// Update Database
		Database::Action("INSERT INTO `member_polling` SET `member_id` = :member_id, `user_agent` = :user_agent, `ip_address` = :ip_address, `timestamp` = :timestamp ON DUPLICATE KEY UPDATE `user_agent` = :user_agent, `ip_address` = :ip_address, `timestamp` = :timestamp", array(
			'member_id' => $member->getId(),
			'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'  => date('Y-m-d H:i:s')
		));
	}