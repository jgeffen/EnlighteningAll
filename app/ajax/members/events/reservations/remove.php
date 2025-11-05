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
	use Items\Enums\Statuses;
	use Items\Enums\Tables;
	use Items\Enums\Types;

	try {
		// Check Member
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');

		// Variable Defaults
		$member      = new Membership();
		$event       = Items\Event::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		$reservation = $member->reservations()->lookup($event);

		// Check Items
		if(is_null($event)) throw new Exception('Event not found in database.');
		if(is_null($reservation)) throw new Exception('RSVP not found in database.');

		// Check Published
		if(!$event->isPublished()) throw new Exception('This event is unpublished.');

		// Check RSVP
		if($reservation->isPaid()) throw new Exception('You can only remove unpaid RSVPs.');

		// Remove RSVP
		Database::Action("DELETE FROM `member_reservations` WHERE `status` = :status AND `member_id` = :member_id AND `event_id` = :event_id", array(
			'status'    => Statuses\Reservation::UNPAID->getValue(),
			'member_id' => $member->getId(),
			'event_id'  => $event->getId()
		));

		// Log Action
		$this->getMember()->log()->setData(
			type       : Types\Log::DELETE,
			table_name : Tables\Members::RESERVATIONS,
			table_id   : $reservation->getId()
		)->execute();

		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'RSVP successfully removed.'
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