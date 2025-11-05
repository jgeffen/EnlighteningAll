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
		// Validate Form
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'captcha' => array(FILTER_CALLBACK, array('options' => 'verifyHash'))
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
		
		// Set Rating Options
		$rating_options = array('options' => array('default' => 0, 'min_range' => 0, 'max_range' => 5));
		
		// Update Database
		Database::Action("INSERT INTO `forms` SET `type` = :type, `captcha` = :captcha, `comments` = :comments, `contact_comments` = :contact_comments, `contact_email` = :contact_email, `contact_name` = :contact_name, `contact_phone` = :contact_phone, `rating_bar_service_comments` = :rating_bar_service_comments, `rating_bar_service` = :rating_bar_service, `rating_check_in_process_comments` = :rating_check_in_process_comments, `rating_check_in_process` = :rating_check_in_process, `rating_clean_room_arrival_comments` = :rating_clean_room_arrival_comments, `rating_clean_room_arrival` = :rating_clean_room_arrival, `rating_food_comments` = :rating_food_comments, `rating_food` = :rating_food, `rating_likely_to_return_comments` = :rating_likely_to_return_comments, `rating_likely_to_return` = :rating_likely_to_return, `rating_room_amentities_comments` = :rating_room_amentities_comments, `rating_room_amentities` = :rating_room_amentities, `rating_staff_members_comments` = :rating_staff_members_comments, `rating_staff_members` = :rating_staff_members, `original` = :original, `user_agent` = :user_agent, `ip_address` = :ip_address, `timestamp` = :timestamp", array(
			'type'                               => 'feedback-survey',
			'captcha'                            => filter_input(INPUT_POST, 'captcha'),
			'comments'                           => filter_input(INPUT_POST, 'comments'),
			'contact_comments'                   => filter_input(INPUT_POST, 'contact_comments'),
			'contact_email'                      => filter_input(INPUT_POST, 'contact_email', FILTER_VALIDATE_EMAIL),
			'contact_name'                       => filter_input(INPUT_POST, 'contact_name'),
			'contact_phone'                      => filter_input(INPUT_POST, 'contact_phone'),
			'rating_bar_service'                 => filter_input(INPUT_POST, 'rating_bar_service', FILTER_VALIDATE_INT, $rating_options),
			'rating_bar_service_comments'        => filter_input(INPUT_POST, 'rating_bar_service_comments'),
			'rating_check_in_process'            => filter_input(INPUT_POST, 'rating_check_in_process', FILTER_VALIDATE_INT, $rating_options),
			'rating_check_in_process_comments'   => filter_input(INPUT_POST, 'rating_check_in_process_comments'),
			'rating_clean_room_arrival'          => filter_input(INPUT_POST, 'rating_clean_room_arrival', FILTER_VALIDATE_INT, $rating_options),
			'rating_clean_room_arrival_comments' => filter_input(INPUT_POST, 'rating_clean_room_arrival_comments'),
			'rating_food'                        => filter_input(INPUT_POST, 'rating_food', FILTER_VALIDATE_INT, $rating_options),
			'rating_food_comments'               => filter_input(INPUT_POST, 'rating_food_comments'),
			'rating_likely_to_return'            => filter_input(INPUT_POST, 'rating_likely_to_return', FILTER_VALIDATE_INT, $rating_options),
			'rating_likely_to_return_comments'   => filter_input(INPUT_POST, 'rating_likely_to_return_comments'),
			'rating_room_amentities'             => filter_input(INPUT_POST, 'rating_room_amentities', FILTER_VALIDATE_INT, $rating_options),
			'rating_room_amentities_comments'    => filter_input(INPUT_POST, 'rating_room_amentities_comments'),
			'rating_staff_members'               => filter_input(INPUT_POST, 'rating_staff_members', FILTER_VALIDATE_INT, $rating_options),
			'rating_staff_members_comments'      => filter_input(INPUT_POST, 'rating_staff_members_comments'),
			'original'                           => json_encode($_POST),
			'user_agent'                         => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'                         => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'                          => date('Y-m-d H:i:s')
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