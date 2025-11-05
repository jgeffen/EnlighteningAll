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
	// Check Logged In
	if (Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');

	// Variable Defaults
	$member  = new Membership();
	$subject = sprintf("Password Changed for %s Membership", SITE_NAME);

	// Set Errors
	$errors = call_user_func(function () {
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

	if (password_verify(filter_input(INPUT_POST, 'old_password'), $member->getPasswordHash())) {
		// Update Password
		$member->account()->setAction(Requests\Account::UPDATE)->setColumn(Types\Column::PASSWORD)->setValue(filter_input(INPUT_POST, 'new_password'))->execute();

		// Log Action
		$member->log()->setData(
			type: Types\Log::UPDATE,
			table_name: Tables\Secrets::MEMBERS,
			table_id: $member->getId(),
			table_column: Types\Column::PASSWORD,
		)->execute();

		// Init Mailer
		$mailer = new Mailer(TRUE);
		$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
		$mailer->addAddress($member->getEmail(), $member->getFullNameLast());
		$mailer->setSubject($subject);
		$mailer->setBody('members/password/change/notification.twig', array(
			'names' => $member->getFirstNames()
		))->send();

		// Set Response
		$json_response = array(
			'status' => 'success',
			'html'   => Template::Render('members/password/change/success.twig')
		);
	} else throw new Exception('The password provided does not match in the database.');
} catch (FormException $exception) {
	// Set Response
	$json_response = array(
		'status' => 'error',
		'errors' => $exception->getErrors()
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
