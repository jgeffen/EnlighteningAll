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
	
	// Imprts
	use Items\Enums\Options;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members;
	
	try {
		// Variable Defaults
		$item   = Members\Setting::Init($dispatcher->getTableId());
		$status = Options\OnOff::lookup(1 - filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT));
		
		// Check Item
		if(is_null($item)) throw new Exception('Setting not found in database.');
		
		// Check Type
		if(!$item->getType()->is(Types\Setting::BOOLEAN)) throw new Exception('Only type BOOLEAN is allowed to do this.');
		
		// Check Status
		if(is_null($status)) throw new Exception('Status not supported by system.');
		
		// Update Database
		Database::Action("UPDATE `member_settings` SET `value` = :value WHERE `id` = :id", array(
			'value' => $status->getLabel(),
			'id'    => $item->getId()
		));
		
		// Log Action
		$admin->log(
			type       : Types\Log::UPDATE,
			table_name : Tables\Members::SETTINGS,
			table_id   : $item->getId(),
			payload    : $_POST
		);
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Database successfully updated.'
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