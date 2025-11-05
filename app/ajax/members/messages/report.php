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
	// TODO: Check if private post is friend
	// TODO: Combine friend status and block status
	
	// Imports
	use Items\Enums\Requests;
	use Items\Enums\Sizes;
	use Items\Enums\Statuses;
	
	try {
		// Variable Defaults
		$contact = Items\Member::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE));
		
		// Check Member's ID
		if($member->getId() == $contact->getId()) throw new Exception('You cannot report your own conversation!');
		
		// Check Block Status
		if($member->getBlockStatus($contact)->is(Statuses\Block::BLOCKED)) throw new Exception('You are not able to report this conversation.');
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_report')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Send Message
		$member->message($contact)->setAction(Requests\Message::REPORT)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully reported conversation.',
			'modal'   => Template::Render('members/suggestions/block-member.twig', array(
				'alt'      => $contact->getAlt('avatar'),
				'avatar'   => $contact->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
				'id'       => $contact->getId(),
				'username' => $contact->getUsername(),
				'message'  => 'Successfully reported conversation.'
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