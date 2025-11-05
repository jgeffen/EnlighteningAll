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
	
	// TODO: Show country flag with IP [https://github.com/chrislim2888/IP2Location-PHP-Module]
	
	// Imports
	use Jenssegers\Agent\Agent;
	
	// Output Json
	echo json_encode(array(
		'data' => array_map(function($item) {
			return array(
				'id'         => $item['id'],
				'action'     => $item['action'],
				'table_name' => $item['table_name'],
				'table_id'   => $item['table_id'],
				'user'       => call_user_func(function($user_id) {
					try {
						$user = new Admin($user_id);
						
						return $user->getProperties(array(
							'id',
							'first_name',
							'last_name',
							'email',
							'full_name',
							'full_name_last'
						));
					} catch(Exception $exception) {
						return array(
							'id'             => NULL,
							'first_name'     => NULL,
							'last_name'      => NULL,
							'email'          => NULL,
							'full_name'      => 'USER NOT FOUND',
							'full_name_last' => 'USER NOT FOUND'
						);
					}
				}, $item['user_id']),
				'user_agent' => call_user_func(function($agent) {
					return implode(' | ', array_filter(array(
						'browser'  => implode(' ', array_filter(array($agent->browser(), $agent->version($agent->browser())))),
						'device'   => $agent->device(),
						'platform' => implode(' ', array_filter(array($agent->platform(), $agent->version($agent->platform())))),
						'language' => !empty($agent->languages()[0]) ? Locale::getDisplayLanguage($agent->languages()[0]) : ''
					)));
				}, new Agent(NULL, $item['user_agent'])),
				'ip_address' => $item['ip_address'],
				'timestamp'  => $item['timestamp']
			);
		}, Database::Action("SELECT * FROM `users_logs` ORDER BY `timestamp` DESC")->fetchAll(PDO::FETCH_ASSOC))
	));