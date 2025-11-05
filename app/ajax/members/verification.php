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
	use PHPMailer\PHPMailer\Exception as PHPMailerException;
	use PHPMailer\PHPMailer\PHPMailer;
	
	try {
		// Variable Defaults
		$subject = sprintf("Confirm Email for %s Membership", SITE_NAME);
		$member  = Membership::FromEmail(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
		
		// Set Errors
		$errors = call_user_func(function() use ($member) {
			// Variable Defaults
			$required_fields = array(
				'email'   => 'Please tell us your email address.',
				'captcha' => 'CAPTCHA cannot be blank.'
			);
			
			// Check Required Fields
			if(!empty($required_fields)) {
				foreach($required_fields as $field => $message) {
					if(!filter_input(INPUT_POST, $field)) {
						$errors[$field] = $message;
					}
				}
			}
			
			// Validate Email
			if(filter_input(INPUT_POST, 'email')) {
				if(!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
					$errors['email'] = 'Email address is invalid.';
				} elseif($member->isVerified()) {
					$errors['email'] = 'Email already verified.';
				}
			}
			
			// Validate CAPTCHA
			if(filter_input(INPUT_POST, 'captcha')) {
				if(rpHash($_POST['captcha']) != filter_input(INPUT_POST, 'captchaHash')) {
					$errors['captcha'] = 'CAPTCHA does NOT Match.';
				}
			}
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(empty($errors)) {
			// Style Message
			$message   = array();
			$message[] = sprintf("<b>%s</b>", $subject);
			$message[] = sprintf("Name : %s", $member->getFullName());
			$message[] = sprintf("Email : %s", $member->getEmail());
			$message[] = '----------------------------------------------------------';
			$message[] = "<b>Please follow the link below to verify your email address.</b>";
			$message[] = '----------------------------------------------------------';
			$message[] = sprintf('<a href="%1$s?%2$s">%1$s?%2$s</a>', curSiteUrl('/members/verify-email.html'), http_build_query(array('hash' => md5($member->getEmail()))));
			$message[] = '----------------------------------------------------------';
			$message[] = sprintf("User Agent : %s", filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'));
			$message[] = sprintf("IP Address : %s", filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP));
			$message[] = sprintf("Date: %s", date_create()->format('l, F jS, Y, g:ia T'));
			array_walk($message, function(&$value) { $value = '<p>' . $value . '</p>'; });
			
			// Init PHP Mailer
			$phpMailer = new PHPMailer(TRUE);
			
			try {
				// Set Recipients (Receipt)
				$phpMailer->setFrom(strtolower(SITE_EMAIL), SITE_NAME);
				$phpMailer->addAddress($member->getEmail(), $member->getFullNameLast());
				
				// Set Content (Receipt)
				$phpMailer->isHTML(TRUE);
				$phpMailer->Subject = $subject;
				$phpMailer->Body    = implode(PHP_EOL, $message);
				$phpMailer->AltBody = strip_tags(implode(str_repeat(PHP_EOL, 2), $message));
				
				// Send Email (Receipt)
				$phpMailer->send();
			} catch(PHPMailerException $exception) {
				// Email Error to Developers
				mail(DEV_EMAIL, DEV_SUBJ, $exception->getMessage(), DEV_FROM);
			}
			
			// Set Response
			$json_response = array(
				'status' => 'success',
				'html'   => Template::Render('members/verification/resend/success.twig', array('email' => $member->getEmail()))
			);
		} else {
			// Set Response
			$json_response = array(
				'status' => 'error',
				'errors' => $errors
			);
		}
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output JSON
	echo json_encode($json_response);