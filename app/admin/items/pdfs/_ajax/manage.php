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
		// Check Require Data
		if(!filter_input(INPUT_POST, 'table_name')) throw new Exception('Table Name is required');
		if(!filter_input(INPUT_POST, 'table_id')) throw new Exception('Table ID is required');
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'DataTables loaded successfully.',
			'data'    => array_map(fn(Items\PDF $item) => array(
				'id'          => $item->getId(),
				'filename'    => $item->getFilename(),
				'title'       => $item->getTitle(50),
				'description' => $item->getDescription(50),
				'timestamp'   => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
				'published'   => $item->isPublished(),
				'item'        => $item->toArray(),
				'options'     => Render::GetTemplate('admin/items/pdfs/options.twig')
			), Items\PDF::FetchAll(Database::Action("SELECT * FROM `pdfs` WHERE `table_name` = :table_name AND `table_id` = :table_id ORDER BY `position` DESC", array(
				'table_name' => filter_input(INPUT_POST, 'table_name'),
				'table_id'   => filter_input(INPUT_POST, 'table_id'),
			))))
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