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
	// TODO: Check if private photo is friend
	
	try {
		// Check Logged In
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Variable Defaults
		$contact = Items\Member::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Member
		if(is_null($contact)) throw new Exception('Member not found in database.');
		
		// Check Member's ID
		if($member->getId() == $contact->getId()) throw new Exception('You cannot send a message to yourself!');
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully fetched modal.',
			'modal'   => Template::Render('members/messages/modal.twig', array(
				'contact' => $contact->toArray()
			))
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