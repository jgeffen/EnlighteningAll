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
 * @var TravelAffiliateMembership $member
 * @var Admin\User $admin
 */

// Imports
use Items\Enums\Sizes;
use Items\Enums\Tables;
use Items\Enums\Types;
use Items\TravelAffiliateMembers;


try {
    $member = TravelAffiliateMembership::Init($dispatcher->getId());

    if (is_null($member)) throw new Exception('Member cannot be found.');

    $jsonInput = file_get_contents('php://input');

    error_log("Received JSON: " . $jsonInput);

    $newData = json_decode($jsonInput, true); // New data for a specific month

    if (!$newData) {
        throw new Exception('Invalid JSON');
    }

    $existingNotesJson = Items\TravelAffiliateMember::Fetch(
        Database::Action(
            "SELECT `admin_commission_notes` FROM `travel_affiliate_members` WHERE `id` = :id",
            array(
                'id' => $member->getId()
            )
        )
    )->toArray();


    if (is_null($existingNotesJson)) throw new Exception('ID does not exist.');

    $existingNotes = json_decode($existingNotesJson['admin_commission_notes'], true);

    // Merge the new data with the existing notes
    foreach ($newData as $monthYear => $details) {
        $existingNotes[$monthYear] = $details; // Update or add the new month-year details
    }

    // Convert the updated notes back to JSON
    $updatedNotesJson = json_encode($existingNotes);

    // Update the database
    Database::Action(
        "UPDATE `travel_affiliate_members` SET `admin_commission_notes` = :admin_commission_notes WHERE `id` = :id",
        array(
            'admin_commission_notes' => $updatedNotesJson,
            'id' => $member->getId()
        ),
        TRUE
    );

    // Set success response
    $json_response = array(
        'status' => 'success',
        'message' => 'travel_affiliate_members table has been updated successfully with Admin Commission Notes',
        'data' => $newData,

    );
} catch (Exception $exception) { // Catching generic exceptions for simplicity
    // Set error response
    $json_response = array(
        'status'  => 'error',
        'message' => $exception->getMessage()
    );
}

// Output JSON
echo json_encode($json_response);
