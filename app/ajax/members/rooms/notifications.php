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
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// Variable Defaults
	$item = Items\Room::Init($dispatcher->getId());
	
	// Check Item
	if(is_null($item)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
	
	try {
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_rooms')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Update Database
		Database::Action("INSERT INTO `member_rooms` SET `member_id` = :member_id, `room_id` = :room_id, `review_id` = :review_id, `favorite` = :favorite, `notification` = :notification, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address ON DUPLICATE KEY UPDATE `notification` = :notification, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'member_id'    => $member->getId(),
			'room_id'      => $item->getId(),
			'review_id'    => NULL,
			'favorite'     => FALSE,
			'notification' => !$member->getRoom($item->getId())?->isNotification(),
			'author'       => NULL,
			'user_agent'   => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'   => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		));
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully marked room.'
		);
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
	
	// Output Response
	echo json_encode($json_response);