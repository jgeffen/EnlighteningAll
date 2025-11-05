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
	
	try {
		// Fetch Item
		$item = Items\Contest::Init($dispatcher->getTableId());
		
		// Check Required Data
		if(is_null($item)) throw new Exception('Item not found in database.');
		
		// Remove File
		Admin\File::Remove($item->getFilename(), Tables\Secrets::CONTESTS, $item->getId(), NULL);
		
		// Update Database
		Database::Action("DELETE FROM `contests` WHERE `id` = :id", array('id' => $item->getId()));
		
		// Log Action
		$admin->log(
			type       : Types\Log::DELETE,
			table_name : Tables\Secrets::CONTESTS,
			table_id   : $item->getId(),
			payload    : $_POST
		);
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Deleted successfully.'
		);
	} catch(Error|PDOException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception)
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