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
 */

// Imports
use Items\Enums\Tables;
use Items\Enums\Types;

try {
    // Set Errors
    $errors = call_user_func(function () use ($member) {
        // Required Fields
        $required = array(
            'first_name' => FILTER_SANITIZE_SPECIAL_CHARS,
            'last_name'  => FILTER_SANITIZE_SPECIAL_CHARS,
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
                    if (!filter_input(INPUT_POST, $field, $validation)) {
                        $errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
                    }
            }
        }



        return $errors ?? FALSE;
    });

    // Check Errors
    if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');

    // Check Account Approval
    if (!$member->isApproved() && $member->settings()->getValue('account_approval_required_settings')) {
        throw new Exception('Your account is pending approval.');
    }

    // Update Member Data
    Database::Action(
        "UPDATE `travel_affiliate_members` SET `username` = :username, `address_line_1` = :address_line_1, `address_line_2` = :address_line_2, `address_city` = :address_city, `address_country` = :address_country, `address_state` = :address_state, `address_zip_code` = :address_zip_code, `first_name` = :first_name, `last_name` = :last_name, `phone` = :phone, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `id` = :member_id",
        array(
            'username'               => filter_input(INPUT_POST, 'username'),
            'address_line_1'         => filter_input(INPUT_POST, 'address_line_1'),
            'address_line_2'         => filter_input(INPUT_POST, 'address_line_2'),
            'address_city'           => filter_input(INPUT_POST, 'address_city'),
            'address_country'        => filter_input(INPUT_POST, 'address_country'),
            'address_state'          => filter_input(INPUT_POST, 'address_state'),
            'address_zip_code'       => filter_input(INPUT_POST, 'address_zip_code'),
            'first_name'             => filter_input(INPUT_POST, 'first_name'),
            'last_name'              => filter_input(INPUT_POST, 'last_name'),
            'phone'                  => filter_input(INPUT_POST, 'phone'),
            'user_agent'             => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
            'ip_address'             => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
            'member_id'              => $member->getId()
        )
    );

    // Log Action
    $member->log()->setData(
        type: Types\Log::UPDATE,
        table_name: Tables\Secrets::TRAVEL_AFFILIATE_MEMBERS,
        table_id: $member->getId()
    )->execute();

    // Set Response
    $json_response = array(
        'status' => 'success',
        'html'   => Template::Render('travel-affiliate-members/settings/success.twig')
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

// Output JSON
echo json_encode($json_response);
