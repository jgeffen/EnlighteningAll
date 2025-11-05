<?php
/*
	Copyright (c) 2021, 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
    Search expanded to include email, first and last name and partner first name by jgeffen.
	@Author: Daerik
    @Re-Authored: Jgeffen
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Membership        $member
 */

try {
    // Sanitize input
    $input = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // Perform expanded lookup
    $lookup = Items\Member::Fetch(Database::Action("
		SELECT *
		FROM `members`
		WHERE 
			`username` = :exact
			OR `email` = :exact
			OR `first_name` = :exact
			OR `last_name` = :exact
			OR `partner_first_name` = :exact
			OR `username` LIKE :partial
			OR `email` LIKE :partial
			OR `first_name` LIKE :partial
			OR `last_name` LIKE :partial
			OR `partner_first_name` LIKE :partial
		LIMIT 1
	", [
        'exact'   => $input,
        'partial' => "%$input%"
    ]));

    // Checkup Lookup
    if (is_null($lookup)) {
        throw new Exception('No matching member found.');
    }

    // Set Response
    $json_response = [
        'status'   => 'success',
        'message'  => 'Successfully found member.',
        'redirect' => $lookup->getLink()
    ];

} catch (Error | PDOException $exception) {
    $json_response = [
        'status'  => 'error',
        'message' => Debug::Exception($exception)
    ];
} catch (Exception $exception) {
    $json_response = [
        'status'  => 'error',
        'message' => $exception->getMessage()
    ];
}

// Output JSON
echo json_encode($json_response);
