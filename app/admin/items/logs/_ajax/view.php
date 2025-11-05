<?php
	/*
	Copyright (c) 2021, 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	try {
		// Set Response
		$json_response = array(
			'status'     => 'success',
			'message'    => 'DataTables loaded successfully.',
			'data'       => array_map(fn(Admin\UserLog $item) => array(
				'id'         => $item->getId(),
				'action'     => $item->getType(),
				'user'       => $item->getUser()?->toArray(),
				'full_name'  => $item->getUser()?->getFullName(),
				'item'       => $item->toArray(),
				'user_agent' => array(
					'value' => $item->getUserAgent(),
					'label' => $item->getUserAgent(TRUE, array('browser'))
				),
				'ip_address' => array(
					'value' => $item->getIpAddress(),
					'label' => Render::GetTemplate('admin/ip-address.twig', array(
						'ip_address'   => $item->getIpAddress(),
						'country_flag' => $item->getIpAddress()->getCountry()?->getEmoji(),
						'link'         => $item->getIpAddress()->getLink()
					))
				),
				'timestamp'  => array(
					'value' => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
					'label' => $item->getLastTimestamp()->format('Y-m-d H:i:s')
				)
			), Admin\UserLog::FetchAll(Database::Action("SELECT * FROM `user_logs` ORDER BY `timestamp` DESC"))),
			'categories' => call_user_func(fn($categories) => !$categories['TABLE NAMES'] ? array() : array(
				'data'   => $categories['TABLE NAMES'],
				'html'   => Render::GetTemplate('admin/items/logs/categories.twig', array('categories' => $categories)),
				'filter' => 'table_name'
			), array(
				'DEFAULTS'    => array('default.show_all' => 'SHOW ALL'),
				'ACTIONS'     => Database::Action("SELECT CONCAT('action.', `type`) AS `value`, UPPER(`type`) AS `text` FROM `user_logs` GROUP BY `type` ORDER BY `type`")->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE),
				'TABLE NAMES' => Database::Action("SELECT `table_name` AS `value`, UPPER(REPLACE(`table_name`, '_', ' ')) AS `text` FROM `information_schema`.`tables` WHERE `table_schema` = :table_schema ORDER BY `table_name`", array(
					'table_schema' => Database::Action("SELECT IFNULL(DATABASE(), '_') AS `table_schema`")->fetch(PDO::FETCH_COLUMN)
				))->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE),
				'USERS'       => array_merge(Database::Action("SELECT CONCAT('user.', `id`) AS `value`, UPPER(TRIM(CONCAT_WS(' ', `first_name`, `last_name`))) AS `text` FROM `users` ORDER BY `first_name`, `last_name`")->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE), array('user.0' => 'SYSTEM'))
			))
		);
	} catch(Error|PDOException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception),
			'data'    => array()
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage(),
			'data'    => array()
		);
	}
	
	// Output Response
	echo json_encode($json_response);