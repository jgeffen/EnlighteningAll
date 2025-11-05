<?php
	/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single web site may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	try {
		// Update Database
		Database::Action("INSERT INTO `analytics` SET `url` = :url, `banner` = :banner, `referer` = :referer, `hits` = 1, `user_agent` = :user_agent, `ip_address` = :ip_address, `date` = :date, `timestamp` = :timestamp, `last_timestamp` = :last_timestamp ON DUPLICATE KEY UPDATE `hits` = `hits` + 1, `last_timestamp` = :last_timestamp", array(
			'url'            => filter_input(INPUT_POST, 'url'),
			'banner'         => filter_input(INPUT_POST, 'banner'),
			'referer'        => filter_input(INPUT_POST, 'referer'),
			'user_agent'     => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'     => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'date'           => date('Y-m-d'),
			'timestamp'      => date('Y-m-d H:i:s'),
			'last_timestamp' => date('Y-m-d H:i:s')
		));
	} catch(Exception $exception) {
		// Log Error
		error_log($exception->getMessage());
	}