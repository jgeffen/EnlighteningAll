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
			'data'       => array_map(fn(Secrets\Photographers\Package $item) => array(
				'id'           => $item->getId(),
				'photographer' => $item->getPhotographer()?->toArray(array('id', 'name')) ?? array('id' => NULL, 'name' => 'Unknown'),
				'page_title'   => $item->getTitle(length : 50),
				'content'      => $item->getContent(length : 50),
				'price'        => $item->getPrice(TRUE),
				'timestamp'    => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
				'published'    => $item->isPublished(),
				'item'         => $item->toArray(),
				'options'      => Render::GetTemplate('admin/items/photographer_packages/options.twig', array('id' => $item->getId()))
			), Secrets\Photographers\Package::FetchAll(Database::Action("SELECT * FROM `photographer_packages` ORDER BY `position` DESC"))),
			'categories' => call_user_func(function($categories) {
				return !$categories['PHOTOGRAPHERS'] ? array() : array(
					'data'   => $categories['PHOTOGRAPHERS'],
					'html'   => Render::GetTemplate('admin/items/photographer_packages/categories.twig', array('categories' => $categories)),
					'filter' => 'photographer_id'
				);
			}, array(
				'PHOTOGRAPHERS' => Database::Action("SELECT `id` AS `value`, `name` AS `text` FROM `photographers` ORDER BY `name`")->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE)
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