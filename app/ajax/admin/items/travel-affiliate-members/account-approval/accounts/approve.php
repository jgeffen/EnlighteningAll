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
	// Variable Defaults
	$member = Items\TravelAffiliateMember::Init(filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT));

	// Check Member
	if (is_null($member)) throw new Exception('Travel Affiliate Member not found in database.');

	// Check Approval
	if ($member->isApproved()) throw new Exception('This account is already approved.');

	// Update Database
	Database::Action("UPDATE `travel_affiliate_members` SET `approved` = :approved, `author` = :author WHERE `id` = :id", array(
		'approved' => TRUE,
		'author'   => $admin->getId(),
		'id'       => $member->getId()
	));

	// Log Action
	$admin->log(
		type: Types\Log::APPROVE,
		table_name: Tables\Secrets::TRAVEL_AFFILIATE_MEMBERS,
		table_id: $member->getId(),
		payload: $_POST
	);

	// Set Response
	$json_response = array(
		'status'  => 'success',
		'message' => 'Successfully approved account.'
	);
} catch (Error | Exception $exception) {
	// Set Response
	$json_response = array(
		'status'  => 'error',
		'message' => Debug::Exception($exception)
	);
}

// Output JSON
echo json_encode($json_response);
