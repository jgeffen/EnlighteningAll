<?php
	/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	try {
		// Variable Defaults
		$subject  = sprintf("Club Swinkster Contact Request - %s", SITE_NAME);
		$to_email = SITE_EMAIL; /* LIVE EMAIL */
		// $to_email = DEV_EMAIL; /* DEV EMAIL */
		
		// Validate Form
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'name'            => FILTER_DEFAULT,
				'phone'           => FILTER_DEFAULT,
				'email'           => FILTER_VALIDATE_EMAIL,
				'reason'          => FILTER_DEFAULT,
				'who_is_renting'  => FILTER_DEFAULT,
				'how_many_people' => FILTER_DEFAULT,
				'captcha'         => array(FILTER_CALLBACK, array('options' => 'verifyHash'))
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
		
		// Init Mailer
		$mailer = new Mailer(TRUE);
		$mailer->setAdmin(TRUE);
		$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
		$mailer->addReplyTo(filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'name'));
		$mailer->addAddress($to_email, SITE_NAME);
		$mailer->setSubject($subject);
		$mailer->setBody('club-swinkster/forms/contact/notification.twig', array(
			'email'           => filter_input(INPUT_POST, 'email'),
			'how_many_people' => filter_input(INPUT_POST, 'how_many_people'),
			'name'            => filter_input(INPUT_POST, 'name'),
			'payment_method'  => filter_input(INPUT_POST, 'payment_method'),
			'phone'           => filter_input(INPUT_POST, 'phone'),
			'provisions'      => filter_input(INPUT_POST, 'provisions'),
			'when_renting'    => filter_input(INPUT_POST, 'when_renting'),
			'who_is_renting'  => filter_input(INPUT_POST, 'who_is_renting')
		))->send();
		
		// Update Database
		Database::Action("INSERT INTO `forms` SET `type` = :type, `captcha` = :captcha, `email` = :email, `how_many_people` = :how_many_people, `name` = :name, `payment_method` = :payment_method, `phone` = :phone, `provisions` = :provisions, `reason` = :reason, `status` = :status, `when_renting` = :when_renting, `who_is_renting` = :who_is_renting, `original` = :original, `user_agent` = :user_agent, `ip_address` = :ip_address, `timestamp` = :timestamp", array(
			'type'            => 'club-swinkster',
			'captcha'         => filter_input(INPUT_POST, 'captcha'),
			'email'           => filter_input(INPUT_POST, 'email'),
			'how_many_people' => filter_input(INPUT_POST, 'how_many_people'),
			'name'            => filter_input(INPUT_POST, 'name'),
			'payment_method'  => filter_input(INPUT_POST, 'payment_method'),
			'phone'           => filter_input(INPUT_POST, 'phone'),
			'provisions'      => filter_input(INPUT_POST, 'provisions'),
			'reason'          => filter_input(INPUT_POST, 'reason'),
			'status'          => 'New Lead',
			'when_renting'    => filter_input(INPUT_POST, 'when_renting'),
			'who_is_renting'  => filter_input(INPUT_POST, 'who_is_renting'),
			'original'        => json_encode($_POST),
			'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'       => date('Y-m-d H:i:s')
		));
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'html'   => include('html/success.php')
		);
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status' => 'error',
			'errors' => $exception->getErrors()
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