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
	 * @var Membership        $member
	 */
	
	// Imports
	use Items\Enums\Requests;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members;
	use Items\Members\Actions;
	
	try {
		// Variable Defaults
		$notification = Members\Notification::Init(filter_input(INPUT_POST, 'notify_id', FILTER_VALIDATE_INT));
		
		// Check Notification
		if(is_null($notification)) throw new Exception('Notification not found.');
		
		// Check Member
		if(!$notification->isOwner($member)) throw new Exception('You do not have permission to modify this notification.');
		
		// Delete Notifcation
		Actions\Notification::Init($member, NULL, $notification)->setRequest(Requests\Notification::REMOVE)->execute();
		
		// Log Action
		$member->log()->setData(
			type       : Types\Log::DELETE,
			table_name : Tables\Members::NOTIFICATIONS,
			table_id   : $notification->getId()
		)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Notification successfuly deleted.'
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output JSON
	echo json_encode($json_response);