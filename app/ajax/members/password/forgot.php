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
	
	try {
		// Variable Defaults
		$subject = sprintf("Forgot Password for %s Membership", SITE_NAME);
		
		// Set Errors
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'email'   => FILTER_VALIDATE_EMAIL,
				'captcha' => array(FILTER_CALLBACK, array('options' => 'verifyHash'))
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
		
		// Set Member
		$member = Membership::FromEmail(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
		
		// Check Member
		if(is_null($member)) throw new Exception('The email provided is not recognized in our system.');
		
		// Init Mailer
		$mailer = new Mailer(TRUE);
		$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
		$mailer->addAddress($member->getEmail(), $member->getFullNameLast());
		$mailer->setSubject($subject);
		$mailer->setBody('members/password/forgot/notification.twig', array(
			'names' => $member->getFirstNames(),
			'link'  => $member->getPasswordResetLink()
		))->send();
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'html'   => Template::Render('members/password/forgot/success.twig', array(
				'email' => $member->getEmail()
			))
		);
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
	
	// Output Response
	echo json_encode($json_response);