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
	 */
	
	// Imports
	use Items\Enums\Types;
	
	// Variable Defaults
	$email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
	$password = filter_input(INPUT_POST, 'password');
	
	try {
		// Set Errors
		$errors = call_user_func(function() {
			// Variable Defaults
			$required_fields = array(
				'email'    => 'Please tell us your email address.',
				'password' => 'Password cannot be empty.'
			);
			
			// Check Required Fields
			if(!empty($required_fields)) {
				foreach($required_fields as $field => $message) {
					if(!filter_input(INPUT_POST, $field)) {
						$errors[$field] = $message;
					}
				}
			}
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// Fetch/Set User
		$user = Admin\User::FromEmail($email);
		
		// More Checks
		if(!is_null($user)) {
			if(password_verify($password, $user->getPasswordHash()) || md5($password) == $user->getPasswordHash()) {
				// Check Old Password Hash
				if(!str_starts_with($user->getPasswordHash(), '$2y$')) {
					// Update Password
					Database::Action("UPDATE `users` SET `password` = :password WHERE `id` = :id", array(
						'password' => password_hash($password, PASSWORD_DEFAULT),
						'id'       => $user->getId()
					));
				}
				
				// Set Session
				$_SESSION['admin'] = array_merge($user->toArray(array('id', 'email')), array(
					'settings' => array(
						'tables' => Database::Action("SELECT `table_name`, `active`, `categories` FROM `table_settings` ORDER BY `table_name`")->fetchAll(PDO::FETCH_UNIQUE)
					)
				));
				
				// Log Action
				$user->log(Types\Log::LOGIN);
				
				// Set Response
				$json_response = array(
					'status'  => 'success',
					'message' => 'You have been successfully logged in.'
				);
			} else throw new Exception('The email/password provided does not match in the database.');
		} else throw new Exception('The email/password provided is not recognized in our system.');
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage(),
			'errors'  => $exception->getErrors()
		);
	} catch(Error|Exception $exception) {
		error_log(sprintf("user %s: authentication failure for \"%s/\": Password Mismatch", $email, filter_input(INPUT_SERVER, 'REQUEST_URI')));
		
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output JSON
	echo json_encode($json_response);