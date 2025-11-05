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
	// Variable Defaults
	$item   = filter_input(INPUT_POST, 'item', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	$member = Items\TravelAffiliateMember::Init($item['id']);
	$errors = call_user_func(function () use ($member) {
		// Variable Defaults
		$required = array(
			'first_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'last_name'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'address_state'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'email'      => FILTER_VALIDATE_EMAIL,
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
			if (TravelAffiliateMembership::EmailExists(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), $member->getEmail())) {
				$errors['email'] = 'Email already exists.';
			}
		}

		// Validate Username
		if (filter_input(INPUT_POST, 'username')) {
			if (!TravelAffiliateMembership::UsernameExists(filter_input(INPUT_POST, 'username'), $member->getUsername())) {
				if (!preg_match(Types\Regex::USERNAME->getValue(), $_POST['username'])) {
					$errors['username'] = 'Username is not acceptable.';
				}
			} else {
				$errors['username'] = 'Username already exists.';
			}
		}

		return $errors ?? FALSE;
	});

	// Check Errors
	if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');

	// Check Member
	if (is_null($member)) throw new Exception('Member not found in database.');

	// Update Member Data
	Database::Action(
		"UPDATE `travel_affiliate_members` SET 

			`username` =:username, 

			`email` =:email, 

			`first_name` =:first_name, 

			`last_name` =:last_name, 

			`phone` =:phone, 

			`travel_agency` =:travel_agency, 

			`ein_number` =:ein_number, 

			`approved` =:approved, 

			`banned` =:banned, 

			`verified` =:verified, 

			`address_line_1` =:address_line_1, 

			`address_line_2` =:address_line_2, 

			`address_city` =:address_city, 

			`address_country` =:address_country, 

			`address_state` =:address_state, 

			`address_zip_code` =:address_zip_code, 

			`ticket_commission_rate` =:ticket_commission_rate, 

			`room_commission_rate` =:room_commission_rate,  

			`admin_approval_signature` =:admin_approval_signature,  

			`notes` =:notes, 

			`author` =:author 

		WHERE `id` =:member_id",
		array(

			'username'               => filter_input(INPUT_POST, 'username'),

			'email'                  => filter_input(INPUT_POST, 'email'),

			'first_name'             => filter_input(INPUT_POST, 'first_name'),

			'last_name'              => filter_input(INPUT_POST, 'last_name'),

			'phone'                  => filter_input(INPUT_POST, 'phone'),

			'travel_agency'          => filter_input(INPUT_POST, 'travel_agency'),

			'ein_number'          	 => filter_input(INPUT_POST, 'ein_number'),

			'approved'               => filter_input(INPUT_POST, 'approved', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),

			'banned'                 => filter_input(INPUT_POST, 'banned', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),

			'verified'               => filter_input(INPUT_POST, 'verified', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),

			'address_line_1'         => filter_input(INPUT_POST, 'address_line_1'),

			'address_line_2'         => filter_input(INPUT_POST, 'address_line_2') ?: NULL,

			'address_city'           => filter_input(INPUT_POST, 'address_city'),

			'address_country'        => filter_input(INPUT_POST, 'address_country'),

			'address_state'          => filter_input(INPUT_POST, 'address_state'),

			'address_zip_code'       => filter_input(INPUT_POST, 'address_zip_code'),

			'ticket_commission_rate' => filter_input(INPUT_POST, 'ticket_commission_rate'),

			'room_commission_rate'   => filter_input(INPUT_POST, 'room_commission_rate'),

			'admin_approval_signature' => filter_input(INPUT_POST, 'admin_approval_signature') ?: NULL,

			'notes'      			 => filter_input(INPUT_POST, 'notes') ?: NULL,

			'author'                 => $admin->getId(),

			'member_id'              => $member->getId()
		)
	);

	// Log Action
	$admin->log(
		type: Types\Log::UPDATE,
		table_name: Tables\Secrets::TRAVEL_AFFILIATE_MEMBERS,
		table_id: $member->getId(),
		payload: $_POST
	);

	// Set Message
	Admin\SetMessage('Updated database successfully.', 'success');

	// Set Response
	$json_response = array(
		'status'   => 'success',
		'message'  => Admin\GetMessage(),
		'table_id' => $member->getId()
	);
} catch (FormException $exception) {
	// Set Response
	$json_response = array(
		'status' => 'error',
		'errors' => $exception->getErrors()
	);
} catch (PDOException $exception) {
	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => Debug::Exception($exception)
	);
} catch (Exception $exception) {
	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => $exception->getMessage()
	);
}

// Output Response
echo json_encode($json_response);
