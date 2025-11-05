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
	use Items\Enums\Types;
	
	try {
		// Variable Defaults
		$friend = Items\Member::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Member
		if(is_null($friend)) throw new Exception('Member not found in database.');
		
		// Check Member's ID
		if($member->getId() == $friend->getId()) throw new Exception('You cannot send a request to yourself!');
		
		// Variable Defaults
		$status = $member->getFriendStatus($friend);
		
		// Set Response
		$json_response = match ($status) {
			Statuses\Friend::APPROVED  => throw new Exception('This user is already your friend.'),
			Statuses\Friend::CANCELLED => throw new Exception('This request was already cancelled.'),
			Statuses\Friend::DECLINED  => throw new Exception('This request was already declined.'),
			Statuses\Friend::NONE      => throw new Exception('Something went wrong. Please try refreshing your page.'),
			Statuses\Friend::PENDING   => call_user_func(function() use ($friend, $member) {
				// Execute Request
				if($member->friend($friend)->setAction(Requests\Friend::DECLINE)->execute()) {
					// Remove Notifcation
					Database::Action("DELETE FROM `member_notifications` WHERE `type` = :type AND `member_1` = :member_1 AND `member_2` = :member_2", array(
						'type'     => Types\Notification::REQUEST->getValue(),
						'member_1' => min($member->getId(), $friend->getId()),
						'member_2' => max($member->getId(), $friend->getId())
					));
					
					// Success Response
					return array(
						'status'  => 'success',
						'message' => 'Declined friend request.'
					);
				}
				
				// Default Repsonse
				return array(
					'status'  => 'error',
					'message' => 'Failed to decline friend request. Please refresh your page.'
				);
			})
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