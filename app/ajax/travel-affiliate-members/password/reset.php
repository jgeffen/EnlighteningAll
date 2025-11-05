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

// TODO: Expire hash to create new one

// Imports
use Items\Enums\Requests;
use Items\Enums\Tables;
use Items\Enums\Types;

try {
	// Variable Defaults
	$subject = sprintf("Password Reset for %s Membership", SITE_NAME);

	// Set Errors
	$errors = call_user_func(function () {
		// Variable Defaults
		$required_fields = array(
			'captcha' => 'CAPTCHA cannot be blank.'
		);

		// Check Required Fields
		if (!empty($required_fields)) {
			foreach ($required_fields as $field => $message) {
				if (!filter_input(INPUT_POST, $field)) {
					$errors[$field] = $message;
				}
			}
		}

		// Validate CAPTCHA
		if (filter_input(INPUT_POST, 'captcha')) {
			if (rpHash($_POST['captcha']) != filter_input(INPUT_POST, 'captchaHash')) {
				$errors['captcha'] = 'CAPTCHA does NOT Match.';
			}
		}

		// Validate Password
		if (filter_input(INPUT_POST, 'new_password')) {
			if (filter_input(INPUT_POST, 'retype_password')) {
				if (!strcmp($_POST['new_password'], $_POST['retype_password'])) {
					if (!preg_match(Types\Regex::PASSWORD->getValue(), $_POST['new_password'])) {
						$errors['new_password'] = 'Password is not strong enough.';
					}
				} else $errors['new_password'] = 'Passwords do not match.';
			} else $errors['new_password'] = 'Please re-type the password.';
		} else $errors['new_password'] = 'Password cannot be empty.';

		return $errors ?? FALSE;
	});

	// Check Errors
	if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');

	// Set Member
	$member = TravelAffiliateMembership::FromHash(filter_input(INPUT_POST, 'hash'), FALSE);

	// Check Member
	if (is_null($member)) throw new Exception('The hash provided is not recognized in our system.');

	// Update Password
	$member->account()->setAction(Requests\Account::UPDATE)->setColumn(Types\Column::PASSWORD)->setValue(filter_input(INPUT_POST, 'new_password'))->execute();

	// Log Action
	$member->log()->setData(
		type: Types\Log::UPDATE,
		table_name: Tables\Secrets::TRAVEL_AFFILIATE_MEMBERS,
		table_id: $member->getId(),
		table_column: Types\Column::PASSWORD,
	)->execute();

	// Init Mailer
	$mailer = new Mailer(TRUE);
	$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
	$mailer->addAddress($member->getEmail(), $member->getFullNameLast());
	$mailer->setSubject($subject);
	$mailer->setBody('travel-affiliate-members/password/reset/notification.twig', array(
		'names' => $member->getFirstName()
	))->send();

	// Set Response
	$json_response = array(
		'status' => 'success',
		'html'   => Template::Render('travel-affiliate-members/password/reset/success.twig')
	);
} catch (FormException $exception) {
	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => $exception->getMessage(),
		'errors'  => $exception->getErrors()
	);
} catch (Error | Exception $exception) {
	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => $exception->getMessage()
	);
}

// Output JSON
echo json_encode($json_response);
