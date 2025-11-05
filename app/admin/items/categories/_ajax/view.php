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
			'data'       => array_map(fn(Items\Category $item) => array(
				'id'         => $item->getId(),
				'page_title' => Helpers::Truncate($item->getTitle(), 50),
				'content'    => Helpers::Truncate($item->getContent(), 50),
				'timestamp'  => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
				'published'  => $item->isPublished(),
				'item'       => $item->toArray(),
				'options'    => Render::GetTemplate('admin/items/categories/options.twig', array('id' => $item->getId()))
			), Items\Category::FetchAll(Database::Action("SELECT * FROM `categories` ORDER BY `position` DESC"))),
			'categories' => call_user_func(fn($categories) => array(
				'data'   => $categories,
				'html'   => Render::GetTemplate('admin/items/categories/categories.twig', array('categories' => $categories)),
				'filter' => 'table_name'
			), Database::Action("SELECT `table_name` AS `value`, `table_title` AS `text` FROM `table_settings` WHERE `categories` IS TRUE ORDER BY `table_title`")->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE))
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception),
			'data'    => array()
		);
	}
	
	// Output Response
	echo json_encode($json_response);