<?php
	/*
	Copyright (c) 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var null|Membership   $member
	 */
	
	// Variable Defaults
	$item = Items\Room::Init($dispatcher->getId());
	
	// Check Item
	if(is_null($item)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
	
	try {
		// Set Response
		$json_response = array_map(fn($item) => array(
			'allDay'     => TRUE,
			'available'  => $item['available'],
			'title'      => $item['available'] ? 'Available' : 'Unavailable',
			'className'  => $item['available'] ? 'available' : 'unavailable',
			'date'       => $item['date']->format('Y-m-d'),
			'dateString' => $item['date']->format('Y-m-d')
		), $item->getAvailability(date_create(filter_input(INPUT_POST, 'start')), date_create(filter_input(INPUT_POST, 'end'))));
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);