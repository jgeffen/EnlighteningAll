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
	
	// TODO: Check if user is blocked
	
	// Imports
	use Items\Members;
	use Items\Members\Types;
	
	try {
		
		$contact = Types\Contact::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		$default_message = Database::Action("SELECT * FROM `member_default_message` WHERE `type` = :type", array('type' => $member->isCouple() ? "COUPLES" : "SINGLES"))->fetchAll(PDO::FETCH_ASSOC);
		
		// Check Mark as Read
		if(filter_input(INPUT_POST, 'is_read', FILTER_VALIDATE_BOOL)) $member->messages($contact)->markRead();
		
		// Set Response
		$json_response = array(
			'status'   => 'success',
			'message'  => 'Successfully fetched message pane.',
			'pane'     => Template::Render('members/messages/message-pane/container.twig', array(
				'contact' => $contact->toArray(),
				'isPaid' => $member->subscription()?->isPaid(),
				'default_message' => $default_message,
			)),
			'messages' => $member->messages($contact)->renderAll(),
			'isPaid' => $member->subscription()?->isPaid()
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