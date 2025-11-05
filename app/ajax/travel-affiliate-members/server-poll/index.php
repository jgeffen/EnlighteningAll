<?php
/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/

// Imports
use Items\Enums\Sizes;
use Items\TravelAffiliateMembers;

// Variable Defaults
$debug      = FALSE;
$delay      = rand(1, 1);
$timestamp  = filter_input(INPUT_POST, 'timestamp', FILTER_VALIDATE_INT);
$timeout    = 45;
$start_time = microtime(TRUE);

// Check Member
if (TravelAffiliateMembership::LoggedIn()) {
	// Variable Defaults
	$member  = new TravelAffiliateMembership();

	// Close Session to Prevent Hanging
	session_write_close();

	// Set Time Limit
	set_time_limit(0);

	try {
		// Long Poll
		while (TRUE) {
			// Check Timeout
			if ($timeout <= microtime(TRUE) - $start_time) {
				// Debug
				$debug && error_log(sprintf("Poll: %s %s [%s]", filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP), $member->getUsername(), 'TIMEOUT'));

				// Set Response
				$json_response = array(
					'status'  => 'timeout',
					'message' => 'Retry connection.'
				);
				break;
			}

			// Debug
			$debug && error_log(sprintf("Poll: %s %s [%s]", filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP), $member->getUsername(), 'DEBUG'));

			// Set Updated Timestamp
			$updated = $member->poll();

			// Check Timestamps
			if ($updated > $timestamp) {
				// Set Response
				$json_response = array(
					'status'       => 'success',
					'message'      => 'Polling Success',
					'data'         => array(
						'member'        => $member->toArray(array('id', 'first_name', 'last_name', 'email')),
						'timestamp'     => $updated
					),
				);
				break;
			}

			// Set Retry Timestamp
			$retry = Database::Action("SELECT UNIX_TIMESTAMP(`timestamp`) FROM `travel_affiliate_member_polling` WHERE `member_id` = :member_id", array(
				'member_id' => $member->getId()
			))->fetch(PDO::FETCH_COLUMN);

			// Check Timestamps
			if (!is_null($retry) && $retry > $timestamp) {
				// Debug
				$debug && error_log(sprintf("Poll: %s %s [%s]", filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP), $member->getUsername(), 'RETRY'));

				// Set Response
				$json_response = array(
					'status'  => 'retry',
					'message' => 'Retry connection.',
					'data'    => array(
						'timestamp' => $retry
					)
				);
				break;
			}

			// Sleep
			sleep($delay);
		}
	} catch (Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
} else {
	// Set Response
	$json_response = array(
		'status'  => 'info',
		'message' => 'User not logged in.'
	);
}

// Output JSON
echo json_encode(array_merge($json_response, array(
	'debug' => !empty($debug)
)));
