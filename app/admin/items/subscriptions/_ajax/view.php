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
			'status'  => 'success',
			'message' => 'DataTables loaded successfully.',
			'data'    => array_map(fn(Items\Subscription $item): array => array(
				'id'        => $item->getId(),
				'name'      => $item->getName(),
				'default'   => $item->isDefault() ? '<i class="fa-solid fa-check fa-2x"></i>' : '<i class="fa-thin fa-hyphen fa-2x"></i>',
				'content'   => $item->getContent(length : 100),
				'icon'      => sprintf("<i class=\"fa-light %s fa-2x\"></i>", $item->getIcon()),
				'price'     => $item->getPrice() ? $item->getPrice(TRUE) : 'FREE',
				'members'   => 0, // TODO: Show number of members on subscription
				'timestamp' => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
				'item'      => $item->toArray(),
				'options'   => Render::GetTemplate('admin/items/subscriptions/options.twig', array(
					'id'      => $item->getId(),
					'default' => $item->isDefault()
				))
			), Items\Subscription::FetchAll(Database::Action("SELECT * FROM `subscriptions` ORDER BY `position` DESC")))
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