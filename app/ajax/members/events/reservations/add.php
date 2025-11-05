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
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Enums\Statuses;
	
	try {
		// Check Member
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Variable Defaults
		$member = new Membership();
		$event  = Items\Event::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Item
		if(is_null($event)) throw new Exception('Event not found in database.');
		
		// Check Published
		if(!$event->isPublished()) throw new Exception('This event is unpublished.');
		
		// Check Pay Later
		if(!$event->isAcceptingRsvp()) throw new Exception('You must purchase a pass to RSVP to this event.');
		
		// Check RSVP
		if($member->reservations()->lookup($event)) throw new Exception('You have already RSVP\'d for this event.');
		
		// Log Action
		$this->getMember()->log()->setData(
			type       : Types\Log::CREATE,
			table_name : Tables\Members::RESERVATIONS,
			table_id   : Database::Action("INSERT INTO `member_reservations` SET `status` = :status, `member_id` = :member_id, `event_id` = :event_id, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'status'     => Statuses\Reservation::UNPAID->getValue(),
				'member_id'  => $member->getId(),
				'event_id'   => $event->getId(),
				'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			), TRUE)
		)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'RSVP successfully added.'
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