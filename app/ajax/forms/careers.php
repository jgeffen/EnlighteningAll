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
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var null|Membership   $member
	 */
	
	if(session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	
	try {
		$subject  = "Career Form Submission";
		
		// Validate Form
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'captcha'          => array(FILTER_CALLBACK, array('options' => 'verifyHash'))
			);
			
			// Check Required Fields
			foreach($required as $field => $validation) {
				if(!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
					$errors[$field] = sprintf("%s is missing or invalid.", Helpers::PrettyTitle($field));
				}
			}
			
			// Validate Resume
			if(!empty($_FILES['resume']['name'])) {
				if(!is_file($_FILES['resume']['tmp_name'])) {
					$errors['resume'] = 'Resume did not upload properly.';
				} elseif(!in_array(strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION)), array('doc', 'docx', 'pdf', 'txt'))) {
					$errors['resume'] = 'This file type of your resume is not allowed.';
				} elseif(!empty($_FILES['resume']['error'])) {
					$errors['resume'] = sprintf("Resume upload error: %s", match ($_FILES['resume']['error']) {
						1       => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
						2       => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
						3       => 'The uploaded file was only partially uploaded.',
						4       => 'No file was uploaded.',
						6       => 'Missing a temporary folder.',
						7       => 'Failed to write file to disk.',
						8       => 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.',
						default => 'An unknown error has occurred.',
					});
				}
			}
			
			return $errors ?? FALSE;
		});
		
		$mailer = new Mailer(TRUE);
		$mailer->setAdmin(TRUE);
		$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
		$mailer->addReplyTo(filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'name'));
		$mailer->addAddress('bret@fenclwebdesign.com', 'Owner');
		$mailer->addAddress('jon@fencl.org', 'Audio/Video Director');
		$mailer->setSubject($subject);
		$mailer->setBody('forms/career/notification.twig', array(
			'career'   => filter_input(INPUT_POST, 'career'),
			'comments' => nl2br(filter_input(INPUT_POST, 'comments')),
			'name'     => filter_input(INPUT_POST, 'name'),
			'email'    => filter_input(INPUT_POST, 'email'),
			'phone'    => filter_input(INPUT_POST, 'phone')
		))->send();
		
		// Update Database
		$form_id = Database::Action("INSERT INTO `forms` SET `type` = :type, `captcha` = :captcha, `career` = :career, `comments` = :comments, `email` = :email, `name` = :name, `phone` = :phone, `original` = :original, `user_agent` = :user_agent, `ip_address` = :ip_address, `timestamp` = :timestamp", array(
			'type'       => 'careers',
			'captcha'    => filter_input(INPUT_POST, 'captcha'),
			'career'     => filter_input(INPUT_POST, 'career'),
			'comments'   => filter_input(INPUT_POST, 'comments'),
			'email'      => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
			'name'       => filter_input(INPUT_POST, 'name'),
			'phone'      => filter_input(INPUT_POST, 'phone'),
			'original'   => json_encode($_POST),
			'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'  => date('Y-m-d H:i:s')
		), TRUE);
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'html'   => Template::Render('forms/career/success.twig')
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
	
	// Check Status for Upload
	if($json_response['status'] == 'success' && !empty($_FILES['resume']['name'])) {
		try {
			// Variable Defaults
			$filepath = Helpers::CreateDirectory('/files/resumes');
			$filename = strtolower(uniqid());
			$pathinfo = pathinfo($_FILES['resume']['name']);
			
			// Check Filename
			while(file_exists(sprintf("%s/%s.%s", $filepath, $filename, $pathinfo['extension']))) {
				$counter  = ($counter ?? 0) + 1;
				$current  = $current ?? $filename;
				$filename = sprintf("%s-%d", $current, $counter);
			}
			
			// Add Extension
			$filename = sprintf("%s.%s", $filename, $pathinfo['extension']);
			
			// Move File
			move_uploaded_file($_FILES['resume']['tmp_name'], sprintf("%s/%s", $filepath, $filename));
			
			// Check Form
			if(!empty($form_id)) {
				// Update Database
				Database::Action("UPDATE `forms` SET `filename` = :filename WHERE `id` = :id", array(
					'filename' => $filename,
					'id'       => $form_id
				));
			}
		} catch(Exception $exception) {
			Debug::Exception($exception);
		}
	}
	
	// Output Response
	echo json_encode($json_response);