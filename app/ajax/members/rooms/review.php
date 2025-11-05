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
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Validate Form
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'review' => FILTER_DEFAULT
			);
			
			// Check Required Fields
			foreach($required as $field => $validation) {
				if(!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
					$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
				}
			}
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_rooms')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Set Room
		$room = Items\Room::Init($dispatcher->getId());
		
		// Check Room
		if(is_null($room)) Render::ErrorCode(HttpStatusCode::NOT_FOUND);
		
		// Variable Defaults
		$content = trim(strip_tags(filter_input(INPUT_POST, 'review'), array('p', 'br', 'strong', 'em', 'u')));
		$review  = $member->getRoom($room->getId())?->getReview();
		
		// Check Review
		if(is_null($review)) {
			// Update Database
			$member_room_review_id = Database::Action("INSERT INTO `member_room_reviews` SET `room_id` = :room_id, `member_id` = :member_id, `content` = :content, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'room_id'    => $room->getId(),
				'member_id'  => $member->getId(),
				'content'    => $content,
				'author'     => NULL,
				'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			), TRUE);
			
			// Update Reference Table
			Database::Action("INSERT INTO `member_rooms` SET `member_id` = :member_id, `room_id` = :room_id, `review_id` = :review_id, `favorite` = :favorite, `notification` = :notification, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address ON DUPLICATE KEY UPDATE `review_id` = :review_id, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'member_id'    => $member->getId(),
				'room_id'      => $room->getId(),
				'review_id'    => $member_room_review_id,
				'favorite'     => FALSE,
				'notification' => FALSE,
				'author'       => NULL,
				'user_agent'   => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'   => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			));
			
			// Log Action
			$member->log()->setData(
				type       : Types\Log::CREATE,
				table_name : Tables\Members\Rooms::REVIEWS,
				table_id   : $member_room_review_id
			)->execute();
		} else {
			// Update Database
			Database::Action("UPDATE `member_room_reviews` SET `content` = :content, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `id` = :id", array(
				'id'         => $review->getId(),
				'content'    => $content,
				'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			));
			
			// Log Action
			$member->log()->setData(
				type       : Types\Log::UPDATE,
				table_name : Tables\Members\Rooms::REVIEWS,
				table_id   : $review->getId()
			)->execute();
		}
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'html'   => Render::GetTemplate('/members/rooms/reviews/success.twig', array(
				'redirect' => $room->getReviewLink()
			))
		);
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status' => 'error',
			'errors' => $exception->getErrors()
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