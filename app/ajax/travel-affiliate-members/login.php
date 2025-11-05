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
use Items\Enums\Requests;
use Items\Enums\Types;

try {
	// Set Errors
	$errors = call_user_func(function () {
		// Variable Defaults
		$required_fields = array(
			'email'    => 'Please tell us your email address.',
			'password' => 'Password cannot be empty.'
		);

		// Check Required Fields
		if (!empty($required_fields)) {
			foreach ($required_fields as $field => $message) {
				if (!filter_input(INPUT_POST, $field)) {
					$errors[$field] = $message;
				}
			}
		}

		return $errors ?? FALSE;
	});

	// Check Errors
	if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');

	// Variable Defaults
	$member   = TravelAffiliateMembership::FromEmail(filter_input(INPUT_POST, 'email'), FALSE);
	$password = filter_input(INPUT_POST, 'password');

	// More Checks
	if (!is_null($member)) {
		if (password_verify($password, $member->getPasswordHash()) || md5($password) == $member->getPasswordHash()) {
			if (!$member->isBanned()) {
				if ($member->isVerified()) {
					if ($member->isApproved()) {
						// Update Password
						if (!str_starts_with($member->getPasswordHash(), '$2y$')) {
							$member->account()->setAction(Requests\Account::UPDATE)->setColumn(Types\Column::PASSWORD)->setValue($password);
						}

						// Set Session
						$_SESSION['travel_affiliate_member'] = $member->toArray(array('id', 'username', 'email'));

						// Log Action
						$member->log()->setData(Types\Log::LOGIN)->execute();

						// Set Response
						$json_response = array(
							'status'  => 'success',
							'message' => 'You have been successfully logged in.',
							'html'    => Render::GetTemplate('travel-affiliate-members/login/success.twig')
						);
					} else throw new Exception('Your account is pending approval.');
				} else throw new Exception('Your email address still needs to be verified.');
			} else throw new Exception('Your account has been banned.');
		} else throw new Exception('Your email/password provided does not match in the database.');
	} else throw new Exception('The email/password provided is not recognized in our system.');
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
