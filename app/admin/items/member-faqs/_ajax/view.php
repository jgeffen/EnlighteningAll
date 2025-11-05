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
			'data'       => array_map(fn(Members\Faq $item) => array(
				'id'        => $item->getId(),
				'question'  => Helpers::Truncate($item->getQuestion(), 50),
				'answer'    => Helpers::Truncate($item->getAnswer(), 50),
				'timestamp' => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
				'published' => $item->isPublished(),
				'item'      => $item->toArray(),
				'options'   => Render::GetTemplate('admin/items/member_faqs/options.twig', array('id' => $item->getId()))
			), Members\Faq::FetchAll(Database::Action("SELECT * FROM `member_faqs` ORDER BY `position` DESC"))),
			'categories' => call_user_func(fn($categories) => array(
				'data'   => $categories,
				'html'   => Render::GetTemplate('admin/items/member_faqs/categories.twig', array('categories' => $categories)),
				'filter' => 'category_id'
			), Database::Action("SELECT `id` AS `value`, `name` AS `text` FROM `categories` WHERE `table_name` = :table_name ORDER BY `name`", array(
				'table_name' => 'member_faqs'
			))->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE))
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