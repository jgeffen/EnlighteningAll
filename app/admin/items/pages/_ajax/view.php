<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	// TODO: Build out class to include HTML snippets
	
	try {
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'DataTables loaded successfully.',
			'data'    => array_map(fn(Items\Page $item) => array(
				'id'         => $item->getId(),
				'page_url'   => $item->getPageUrl() === 'homepage' ? '/' : ($item->getRoute()?->getLink() ?? $item->getPageUrl()),
				'page_title' => Helpers::Truncate($item->getTitle(), 50),
				'content'    => Helpers::Truncate($item->getContent(), 50),
				'timestamp'  => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
				'published'  => $item->isPublished(),
				'item'       => $item->toArray(),
				'options'    => Render::GetTemplate('admin/items/pages/options.twig', array('id' => $item->getId()))
			), Items\Page::FetchAll(Database::Action("SELECT * FROM `pages` ORDER BY `page_url`, `timestamp` DESC")))
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