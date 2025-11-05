<?php
	/*
	Copyright (c) 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	/**
	 * @var Membership $member
	 */
	
	use Items\Forms\Intake as Intake;
	
	try {
		// Variable Defaults
		$subject = sprintf("Intake Form - %s", SITE_NAME);
		//$to_email = SITE_EMAIL; /* LIVE EMAIL */
		$to_email = DEV_EMAIL; /* DEV EMAIL */
		
		// Validate Form
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'first_name' => FILTER_DEFAULT,
				'last_name'  => FILTER_DEFAULT,
				'phone'      => FILTER_DEFAULT,
				'email'      => FILTER_VALIDATE_EMAIL,
				'captcha'    => array(FILTER_CALLBACK, array('options' => 'verifyHash'))
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
		$mailer->addReplyTo(filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'first_name'));
		$mailer->addAddress($to_email, SITE_NAME);
		$mailer->setSubject($subject);
		$mailer->setBody('forms/intake/notification.twig', array(
			'email'               => filter_input(INPUT_POST, 'email'),
			'first_name'          => filter_input(INPUT_POST, 'first_name'),
			'last_name'           => filter_input(INPUT_POST, 'last_name'),
			'phone'               => filter_input(INPUT_POST, 'phone'),
			'status'              => 'New Lead',
			'yoga'                => Intake::FormatRankedOptions($_POST['yoga'] ?? array(), Intake::Options('yoga_styles')),
			'teacher'             => ($_POST['teacher'] ?? 0) == 1 ? 'Yes' : 'No',
			'teacher_roles'       => Intake::FormatCheckboxInput('teacher_roles', Intake::Options('teacher_roles')),
			'music'               => Intake::FormatRankedOptions($_POST['music'] ?? array(), Intake::Options('music_genres')),
			'core_practices'      => Intake::FormatRankedOptions($_POST['core_practices'] ?? array(), Intake::Options('core_practices')),
			'dance_movement'      => Intake::FormatRankedOptions($_POST['dance_movement'] ?? array(), Intake::Options('dance_movement')),
			'community_interests' => Intake::FormatRankedOptions($_POST['community_interests'] ?? array(), Intake::Options('community')),
			'influencer_goals'    => Intake::FormatRankedOptions($_POST['influencer_goals'] ?? array(), Intake::Options('influencers')),
			'education_business'  => Intake::Options('education_business', filter_input(INPUT_POST, 'education_business'))
		))->send();
		
		// Update Database
		Database::Action("INSERT INTO `forms` SET `type` = :type, `captcha` = :captcha, `email` = :email, `classes_taught` = :classes_taught, `first_name` = :first_name, `last_name` = :last_name, `phone` = :phone, `status` = :status, `user_agent` = :user_agent, `ip_address` = :ip_address, `timestamp` = :timestamp, `yoga` = :yoga, `teacher` = :teacher, `teacher_roles` = :teacher_roles, `music` = :music, `core_practices` = :core_practices, `dance_movement` = :dance_movement, `community_interests` = :community_interests, `influencer_goals` = :influencer_goals, `education_business` = :education_business", array(
			'type'                => 'intake',
			'captcha'             => filter_input(INPUT_POST, 'captcha'),
			'email'               => filter_input(INPUT_POST, 'email'),
			'classes_taught'      => filter_input(INPUT_POST, 'classes_taught'),
			'first_name'          => filter_input(INPUT_POST, 'first_name'),
			'last_name'           => filter_input(INPUT_POST, 'last_name'),
			'phone'               => filter_input(INPUT_POST, 'phone'),
			'status'              => 'New Lead',
			'user_agent'          => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'          => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'           => date('Y-m-d H:i:s'),
			'yoga'                => json_encode($_POST['yoga'] ?? array()),
			'teacher'             => (int)($_POST['teacher'] ?? 0),
			'teacher_roles'       => implode(',', $_POST['teacher_roles'] ?? array()),
			'music'               => json_encode($_POST['music'] ?? array()),
			'core_practices'      => json_encode($_POST['core_practices'] ?? array()),
			'dance_movement'      => json_encode($_POST['dance_movement'] ?? array()),
			'community_interests' => json_encode($_POST['community_interests'] ?? array()),
			'influencer_goals'    => json_encode($_POST['influencer_goals'] ?? array()),
			'education_business'  => filter_input(INPUT_POST, 'education_business')
		));
		
		Database::Action("UPDATE `members` SET `intake_survey` = :intake_survey, `teacher` = :teacher WHERE `id` = :id", array(
			'intake_survey' => 1,
			'teacher'       => (int)($_POST['teacher'] ?? 0),
			'id'            => $member->getId()
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