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
		// Variable Defaults
		$member = Items\Member::Init($dispatcher->getId());
		$status = 1 - filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
		
		// Update Database
		Database::Action("UPDATE `members` SET `banned` = :banned WHERE `id` = :id", array(
			'banned' => $status,
			'id'     => $member->getId()
		));
		
		// Log Action
		$admin->log(
			type       : $status ? Types\Log::BAN : Types\Log::UNBAN,
			table_name : Tables\Secrets::MEMBERS,
			table_id   : $member->getId(),
			payload    : $_POST
		);
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Database successfully updated.'
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception)
		);
	}
	
	// Output Response
	echo json_encode($json_response);