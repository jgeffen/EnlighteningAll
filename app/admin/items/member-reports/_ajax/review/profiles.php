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
	use Items\Enums\Statuses;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members;
	
	try {
		// Variable Defaults
		$item = filter_input(INPUT_POST, 'item', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$item = Members\Report::Init($item['id']);
		
		// Check Item
		if(is_null($item)) throw new Exception('Item not found in database.');
		
		// Check Post
		if(is_null($item->getProfile())) throw new Exception('Profile no longer exists in database.');
		
		// Set Errors
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'status' => array(FILTER_CALLBACK, array('options' => fn(?string $value) => (bool)Statuses\Report::lookup($value)))
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
						if(!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
							$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
						}
				}
			}
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// Update Database
		Database::Action("UPDATE `member_reports` SET `status` = :status, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `id` = :id", array(
			'status'     => filter_input(INPUT_POST, 'status'),
			'author'     => $admin->getId(),
			'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'id'         => $item->getId()
		));
		
		// Log Action
		$admin->log(
			type       : Types\Log::UPDATE,
			table_name : Tables\Secrets::MEMBERS,
			table_id   : $item->getId(),
			payload    : $_POST,
			notes      : filter_input(INPUT_POST, 'notes')
		);
		
		// Check Removed
		if(Statuses\Report::lookup(filter_input(INPUT_POST, 'status'))?->is(Statuses\Report::REMOVED)) {
			// Update Database
			Database::Action("UPDATE `members` SET `banned` = :banned WHERE `id` = :id", array(
				'banned' => TRUE,
				'id'     => $item->getProfileId()
			));
			
			// Log Action
			$admin->log(
				type       : Types\Log::BAN,
				table_name : Tables\Secrets::MEMBERS,
				table_id   : $item->getProfileId(),
				payload    : $_POST,
				notes      : filter_input(INPUT_POST, 'notes')
			);
		}
		
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