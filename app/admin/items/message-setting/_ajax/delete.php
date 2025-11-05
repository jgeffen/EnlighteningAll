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
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members;

	try {
	
		// Fetch Item
		$item = Members\DefaultMessage::Init($dispatcher->getTableId());
		
		// Check Required Data
		if(is_null($item)) throw new Exception('Item not found in database.');
		
		// Update Database
		Database::Action("DELETE FROM `member_default_message` WHERE `id` = :id", array('id' => $item->getId()));
		
		
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Deleted successfully.'
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);