<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 */
	
	// Imports
	use Items\Enums\Requests;
	use Items\Enums\Types;
	
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
		
		// Variable Defaults
		$member   = Membership::FromEmail(filter_input(INPUT_POST, 'email'), FALSE);
		$password = filter_input(INPUT_POST, 'password');
		
		// More Checks
		if(!is_null($member)) {
			if(password_verify($password, $member->getPasswordHash()) || md5($password) == $member->getPasswordHash()) {
				if(!$member->isBanned()) {
					if($member->isVerified() || !$member->settings()->getValue('email_verification_required')) {
						if($member->isApproved() || !$member->settings()->getValue('account_approval_required')) {
							// Update Password
							if(!str_starts_with($member->getPasswordHash(), '$2y$')) {
								$member->account()->setAction(Requests\Account::UPDATE)->setColumn(Types\Column::PASSWORD)->setValue($password)->execute();
							}
							
							// Set Session
							$_SESSION['member'] = $member->toArray(array('id', 'username', 'email'));
							
							// Log Action
							$member->log()->setData(Types\Log::LOGIN)->execute();
							
							
							if(!$member->isIntakeSurvey()) {
								// Set Response
								$json_response = array(
									'status'  => 'success',
									'message' => 'You have been successfully logged in.',
									'html'    => Render::GetTemplate('members/login/intake-survey.twig')
								);
							} else {
								// Set Response
								$json_response = array(
									'status'  => 'success',
									'message' => 'You have been successfully logged in.',
									'html'    => Render::GetTemplate('members/login/success.twig')
								);
							}
							
						} else throw new Exception('Your account is pending approval.');
					} else throw new Exception('Your email address still needs to be verified.');
				} else throw new Exception('Your account has been banned.');
			} else throw new Exception('Your email/password provided does not match in the database.');
		} else throw new Exception('The email/password provided is not recognized in our system.');
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage(),
			'errors'  => $exception->getErrors()
		);
	} catch(Error|Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output JSON
	echo json_encode($json_response);