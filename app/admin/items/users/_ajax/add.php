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
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Set Errors
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'user-type'  => FILTER_VALIDATE_INT,
				'first-name' => FILTER_DEFAULT,
				'last-name'  => FILTER_DEFAULT,
				'email'      => FILTER_VALIDATE_EMAIL,
				'password'   => FILTER_DEFAULT
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
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// Update Database
		$table_id = Database::Action("INSERT INTO `users` SET `user_type` = :user_type, `first_name` = :first_name, `last_name` = :last_name, `email` = :email, `password` = :password, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'user_type'  => filter_input(INPUT_POST, 'user-type', FILTER_VALIDATE_INT),
			'first_name' => filter_input(INPUT_POST, 'first-name'),
			'last_name'  => filter_input(INPUT_POST, 'last-name'),
			'email'      => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
			'password'   => password_hash(filter_input(INPUT_POST, 'password'), PASSWORD_DEFAULT),
			'author'     => $admin->getId(),
			'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		), TRUE);
		
		// Log Action
		$admin->log(
			type       : Types\Log::CREATE,
			table_name : Tables\Website::USERS,
			table_id   : $table_id,
			payload    : $_POST
		);
		
		// Set Message
		Admin\SetMessage('Updated database successfully.', 'success');
		
		// Set Response
		$json_response = array(
			'status'   => 'success',
			'message'  => Admin\GetMessage(),
			'table_id' => $table_id
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