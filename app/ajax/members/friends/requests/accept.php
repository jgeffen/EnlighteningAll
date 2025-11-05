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
		$verify = filter_input(INPUT_POST, 'verify');
		if ($verify == "true") {
			$verified = 1;
		} else if($verify == "false"){
			$verified = 0;
		}
		
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
			Statuses\Friend::APPROVED  => throw new Exception('This user is already your friend.'),
			Statuses\Friend::CANCELLED => throw new Exception('This request was already cancelled.'),
			Statuses\Friend::DECLINED  => throw new Exception('This request was already declined.'),
			Statuses\Friend::NONE      => throw new Exception('Something went wrong. Please try refreshing your page.'),
			Statuses\Friend::PENDING   => call_user_func(function() use ($friend, $member, $verified) {
				
				Database::Action("UPDATE `member_confirmation_friend_request` SET `verified` = :verified WHERE `member_id` = :member_id AND `initiated_by` = :initiated_by", array(
						'verified'     => $verified,
						'member_id' => $member->getId(),
						'initiated_by' => $friend->getId()
					));
				// Execute Request
				if($member->friend($friend)->setAction(Requests\Friend::APPROVE)->execute()) {
					// Remove Notifcation
					
					
				Database::Action("DELETE FROM `member_notifications` WHERE `type` = :type AND `member_1` = :member_1 AND `member_2` = :member_2", array(
						'type'     => Types\Notification::REQUEST->getValue(),
						'member_1' => min($member->getId(), $friend->getId()),
						'member_2' => max($member->getId(), $friend->getId())
					));
					
					// Success Response
					return array(
						'status'  => 'success',
						'message' => 'Accepted friend request.'
					);
				}
				
				// Default Repsonse
				return array(
					'status'  => 'error',
					'message' => 'Failed to accept friend request. Please refresh your page.'
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