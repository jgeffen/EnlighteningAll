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
	 * @var Admin\User        $admin
	 */
	
	// Imports
	use Items\Enums\Options;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members\Posts\Types as Posts;
	
	try {
		// Variable Defaults
		$item = filter_input(INPUT_POST, 'item', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$item = Posts\Social::Init($item['id']);
		
		// Check Item
		if(is_null($item)) throw new Exception('Item not found in database');
		
		// Check Errors
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'heading' => FILTER_DEFAULT,
				'content' => FILTER_DEFAULT
			);
			
			// Check Required Fields
			foreach($required as $field => $validation) {
				// Switch Validation
				switch($validation) {
					case FILTER_VALIDATE_INT:
						if(is_null(filter_input(INPUT_POST, $field, $validation)) || filter_input(INPUT_POST, $field, $validation) === FALSE) {
							$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
						}
						break;
					case FILTER_DEFAULT:
					default:
						if(!filter_input(INPUT_POST, $field, $validation)) {
							$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
						}
				}
			}
			
			// Check Image Alt
			if(filter_input(INPUT_POST, 'filename') && !filter_input(INPUT_POST, 'filename_alt')) {
				$errors[] = 'Image alt is required.';
			}
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// List Dates
		list($date_start, $date_end) = explode(' to ', filter_input(INPUT_POST, 'dates')) + array_fill(0, 2, NULL);
		
		// Variable Defaults
		$content = trim(strip_tags(filter_input(INPUT_POST, 'content'), array('p', 'br', 'strong', 'em', 'u')));
		
		// Update Post
		Database::Action("UPDATE `member_posts` SET `visibility` = :visibility, `heading` = :heading, `content` = :content WHERE `id` = :post_id", array(
			'visibility' => Options\Visibility::lookup(filter_input(INPUT_POST, 'visibility'))?->getValue(),
			'heading'    => filter_input(INPUT_POST, 'heading'),
			'content'    => $content,
			'post_id'    => $item->getId()
		), TRUE);
		
		// Update Data in Social Data
		Database::Action("UPDATE `member_post_type_social` SET `date_start` = :date_start, `date_end` = :date_end WHERE `member_post_id` = :post_id", array(
			'date_start' => $date_start,
			'date_end'   => $date_end ?? $date_start,
			'post_id'    => $item->getId()
		));
		
		// Check Notes
		if(filter_input(INPUT_POST, 'notes')) {
			// Create Ticket
			Database::Action("INSERT INTO `member_tickets` SET `member_ticket_id` = :member_ticket_id, `member_id` = :member_id, `content` = :content, `read` = :read, `initiated_by` = :initiated_by, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'member_ticket_id' => NULL,
				'member_id'        => $item->getMemberId(),
				'content'          => filter_input(INPUT_POST, 'notes'),
				'read'             => FALSE,
				'initiated_by'     => 'admin',
				'author'           => $admin->getId(),
				'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			));
		}
		
		// Log Action
		$admin->log(
			type       : Types\Log::UPDATE,
			table_name : Tables\Members::POSTS,
			table_id   : $item->getId(),
			payload    : $_POST,
			notes      : filter_input(INPUT_POST, 'notes')
		);
		
		// Set Message
		Admin\SetMessage('Updated database successfully.', 'success');
		
		// Set Response
		$json_response = array(
			'status'   => 'success',
			'message'  => Admin\GetMessage(),
			'table_id' => $item->getId()
		);
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status' => 'error',
			'errors' => $exception->getErrors()
		);
	} catch(Error|PDOException $exception) {
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