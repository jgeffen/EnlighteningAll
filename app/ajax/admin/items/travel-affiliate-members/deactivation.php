<?php
/*
	Copyright (c) 2022 Daerik.com
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
	// Check Access Level
	if (!Admin\Privilege(2)) throw new Exception('You do not have sufficient privilege to access this account.');

	// Fetch Member
	$member = TravelAffiliateMembership::Init($dispatcher->getId());

	// Check Member
	if (is_null($member)) throw new Exception('Member cannot be found.');

	// Remove Member Files
	System::RemoveDirectory(sprintf("%s/files/travel-affiliate-members/%d", dirname(__DIR__, 5), $member->getId()));

	// Remove Member Data
	Database::Action("DELETE FROM `travel_affiliate_members` WHERE `id` = :id", array(
		'id' => $member->getId()
	));

	// Log Action
	$admin->log(
		type: Types\Log::DEACTIVATE,
		table_name: Tables\Secrets::TRAVEL_AFFILIATE_MEMBERS,
		table_id: $member->getId(),
		payload: $_POST
	);

	// Set Response
	$json_response = array(
		'status'  => 'success',
		'message' => 'Successfully deactiaved account.',
		'data'    => $member->toArray()
	);
} catch (Error $exception) {
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

// Output JSON
echo json_encode($json_response);
