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
	
	// TODO: Build out class to include HTML snippets
	
	try {
		// Set Response
		$json_response = array(
			'status'     => 'success',
			'message'    => 'DataTables loaded successfully.',
			'data'       => array_map(fn(Admin\User $item) => array(
				'id'         => $item->getId(),
				'user_type'  => $item->getUserType()?->getTitle(),
				'name'       => $item->getFullName(),
				'email'      => $item->getEmail(),
				'item'       => $item->toArray(),
				'options'    => Render::GetTemplate('admin/items/users/options.twig', array('id' => $item->getId())),
				'last_login' => array(
					'timestamp' => $item->getLastLogin()?->format('Y-m-d H:i:s'),
					'display'   => $item->getLastLogin()?->format('Y-m-d H:i:s')
				)
			), Admin\User::FetchAll(Database::Action("SELECT * FROM `users` ORDER BY `first_name`, `last_name`, `email`"))),
			'categories' => call_user_func(fn($categories) => !$categories['USER TYPES'] ? array() : array(
				'data'   => $categories['USER TYPES'],
				'html'   => Render::GetTemplate('admin/items/users/categories.twig', array('categories' => $categories)),
				'filter' => 'user_type'
			), array(
				'DEFAULTS'   => array('default.show_all' => 'SHOW ALL'),
				'USER TYPES' => Database::Action("SELECT `user_type` AS `value`, `title` AS `text` FROM `user_types` WHERE `user_type` >= :user_type ORDER BY `user_type`", array(
					'user_type' => $admin->getUserType()?->getUserType()
				))->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE)
			))
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