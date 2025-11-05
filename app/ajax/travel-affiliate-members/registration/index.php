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
use Items\Enums\Sizes;
use Items\Enums\Tables;
use Items\Enums\Types;

try {
	// Variable Defaults
	$subject = sprintf("Confirmation Email for %s Membership", SITE_NAME);

	// Set Errors
	$errors = call_user_func(function () {
		// Variable Defaults
		$required = array(
			'first_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'last_name'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'email' => FILTER_VALIDATE_EMAIL,
			'travel_agency'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'ein_number'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'address_line_1'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'address_country'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'address_state'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'address_city'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'terms_privacy_signature' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'affiliate_terms_conditions_signature' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'address_zip_code'  => FILTER_VALIDATE_INT,
			'captcha' => array(FILTER_CALLBACK, array('options' => 'verifyHash'))
		);

		// Check Required Fields
		foreach ($required as $field => $validation) {
			// Switch Validation
			switch ($validation) {
				case FILTER_VALIDATE_INT:
					if (is_null(filter_input(INPUT_POST, $field, $validation)) || filter_input(INPUT_POST, $field, $validation) === FALSE) {
						$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
					}
					break;
				case FILTER_DEFAULT:
				default:
					if (!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
						$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
					}
			}
		}

		// Validate Email
		if (filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
			if (TravelAffiliateMembership::EmailExists(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL))) {
				$errors['email'] = 'Email already exists.';
			}
		}

		// Validate Username
		if (filter_input(INPUT_POST, 'username')) {
			if (!TravelAffiliateMembership::UsernameExists(filter_input(INPUT_POST, 'username'))) {
				if (!preg_match(Types\Regex::USERNAME->getValue(), $_POST['username'])) {
					$errors['username'] = 'Username is not acceptable.';
				}
			} else {
				$errors['username'] = 'Username already exists.';
			}
		}

		// Validate Password
		if (filter_input(INPUT_POST, 'password')) {
			if (filter_input(INPUT_POST, 'retype_password')) {
				if (!strcmp($_POST['password'], $_POST['retype_password'])) {
					if (!preg_match(Types\Regex::PASSWORD->getValue(), $_POST['password'])) {
						$errors['password'] = 'Password is not strong enough.';
					}
				} else {
					$errors['password'] = 'Passwords do not match.';
				}
			} else {
				$errors['password'] = 'Please re-type the password.';
			}
		}

		return $errors ?? FALSE;
	});

	// Check Errors
	if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');

	// Update Database
	$member_id = Database::Action(
		"INSERT INTO `travel_affiliate_members` SET 
		`username` =:username, 
		`first_name` =:first_name, 
		`last_name` =:last_name, 
		`email` =:email, 
		`phone` =:phone, 
		`travel_agency` =:travel_agency, 
		`ein_number` =:ein_number, 
		`password` =:password, 
		`address_line_1` =:address_line_1, 
		`address_line_2` =:address_line_2, 
		`address_city` =:address_city, 
		`address_country` =:address_country, 
		`address_state` =:address_state, 
		`address_zip_code` =:address_zip_code, 
		`terms_privacy_signature` =:terms_privacy_signature, 
		`affiliate_terms_conditions_signature` =:affiliate_terms_conditions_signature, 
		`ticket_commission_rate` =:ticket_commission_rate, 
		`room_commission_rate` =:room_commission_rate, 
		`admin_commission_notes` =:admin_commission_notes, 
		`user_agent` =:user_agent, 
		`ip_address` =:ip_address",
		array(
			'username'               				=> filter_input(INPUT_POST, 'username'),
			'password'               				=> password_hash(filter_input(INPUT_POST, 'password'), PASSWORD_DEFAULT),
			'first_name'             				=> filter_input(INPUT_POST, 'first_name'),
			'last_name'              				=> filter_input(INPUT_POST, 'last_name'),
			'email'                  				=> filter_input(INPUT_POST, 'email'),
			'phone'                  				=> filter_input(INPUT_POST, 'phone'),
			'travel_agency'          				=> filter_input(INPUT_POST, 'travel_agency'),
			'ein_number'          	 				=> filter_input(INPUT_POST, 'ein_number'),
			'address_line_1'         				=> filter_input(INPUT_POST, 'address_line_1'),
			'address_line_2'         				=> filter_input(INPUT_POST, 'address_line_2'),
			'address_city'           				=> filter_input(INPUT_POST, 'address_city'),
			'address_country'        				=> filter_input(INPUT_POST, 'address_country'),
			'address_state'          				=> filter_input(INPUT_POST, 'address_state'),
			'address_zip_code'       				=> filter_input(INPUT_POST, 'address_zip_code'),
			'terms_privacy_signature'				=> filter_input(INPUT_POST, 'terms_privacy_signature'),
			'affiliate_terms_conditions_signature'  => filter_input(INPUT_POST, 'affiliate_terms_conditions_signature'),
			'ticket_commission_rate' 				=> 7.00,
			'room_commission_rate'   				=> 7.00,
			'admin_commission_notes' 				=> '{}',
			'user_agent'             				=> filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'             				=> filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		),
		TRUE
	);

	// Init Member
	$member = TravelAffiliateMembership::Init($member_id);

	// Check Member
	if (is_null($member)) throw new Exception('Something went wrong with your registration. Please try again.');

	// Log Action
	$member->log()->setType(Types\Log::REGISTER)->execute();

	// Init Mailer
	$mailer = new Mailer(TRUE);
	$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
	$mailer->addAddress($member->getEmail(), $member->getFullNameLast());
	$mailer->setSubject($subject);
	$mailer->setBody('travel-affiliate-members/registration/notification.twig', array(
		'names'    => $member->getFirstName(),
		'username' => $member->getUsername(),
		'link'     => $member->getVerificationLink()
	))->send();

	// Set Response
	$json_response = array(
		'status' => 'success',
		'html'   => Template::Render('travel-affiliate-members/registration/success.twig', array('email' => $member->getEmail()))
	);
} catch (FormException $exception) {
	// Set Response
	$json_response = array(
		'status' => 'error',
		'errors' => $exception->getErrors()
	);
} catch (PDOException $exception) {
	// Log Error
	Debug::Exception($exception);

	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => 'There was an issue communicating with the database. Please try again later.'
	);
} catch (Exception $exception) {
	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => $exception->getMessage()
	);
}


// Output JSON
echo json_encode($json_response);
