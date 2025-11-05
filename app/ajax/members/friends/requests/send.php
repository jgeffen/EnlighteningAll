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
	use Items\Enums\Requests;
	use Items\Enums\Statuses;
	
	try {
		// Variable Defaults
		$friend = Items\Member::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Member
		if(is_null($friend)) throw new Exception('Member not found in database.');
		
		// Check Member's ID
		if($member->getId() == $friend->getId()) throw new Exception('You cannot send a request to yourself!');
		
		// Check Account Approval (Member)
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_friends')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Check Account Approval (Friend)
		if(!$friend->isApproved() && $member->settings()->getValue('account_approval_required_friends')) {
			throw new Exception('This account is pending approval.');
		}
		
		// Variable Defaults
		$status = $member->getFriendStatus($friend);
		
		// Set Response
		$json_response = match ($status) {
			Statuses\Friend::APPROVED => throw new Exception('This user is already your friend.'),
			Statuses\Friend::PENDING  => throw new Exception('Your request is already pending.'),
			Statuses\Friend::CANCELLED,
			Statuses\Friend::DECLINED,
			Statuses\Friend::NONE     => $member->friend($friend)->setAction(Requests\Friend::SEND)->execute() ? array(
				'status'  => 'success',
				'message' => 'Sent friend request.'
			) : array(
				'status'  => 'error',
				'message' => 'Failed to send friend request. Please refresh your page.'
			)
		};
	} catch(PDOException $exception) {
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
	
	// Output JSON
	echo json_encode($json_response);