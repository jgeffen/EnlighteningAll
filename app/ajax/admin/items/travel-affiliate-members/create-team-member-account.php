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

    $errors = call_user_func(function () use ($member) {
        // Variable Defaults
        $required = array(
            'first_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'last_name'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
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

        return $errors ?? FALSE;
    });

    // Check Errors
    if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');


    // Update Member Data
    Database::Action(
        "INSERT INTO `travel_affiliate_members` SET 

			`username` =:username, 

			`email` =:email, 

            `password` =:password,

	        `travel_agency` =:travel_agency, 

            `ein_number` =:ein_number, 

            `address_line_1` =:address_line_1, 

			`address_line_2` =:address_line_2, 

			`address_city` =:address_city, 

			`address_country` =:address_country, 

			`address_state` =:address_state, 

			`address_zip_code` =:address_zip_code, 

			`first_name` =:first_name, 

			`last_name` =:last_name, 

			`phone` =:phone, 

            `notes` =:notes,

		    `ticket_commission_rate` =:ticket_commission_rate, 

			`room_commission_rate` =:room_commission_rate,  

            `admin_commission_notes` =:admin_commission_notes,

			`approved` =:approved, 

			`banned` =:banned, 

			`verified` =:verified, 

			`is_employee` =:is_employee, 

			`terms_privacy_signature` =:terms_privacy_signature, 

            `affiliate_terms_conditions_signature`=:affiliate_terms_conditions_signature, 

			`admin_approval_signature` =:admin_approval_signature,  

			`author` =:author,

            `user_agent` =:user_agent, 

            `ip_address` =:ip_address",
        array(

            'username'               => filter_input(INPUT_POST, 'first_name') . filter_input(INPUT_POST, 'last_name'),

            'email'                  => filter_input(INPUT_POST, 'first_name') . filter_input(INPUT_POST, 'last_name') . "@enlighteningall.com",

            'password'               => password_hash("enlighteningallJS@34744", PASSWORD_DEFAULT),

            'travel_agency'          => "Employee",

            'ein_number'             => "000000000",

            'address_line_1'         => "1272 Sarno Rd",

            'address_line_2'         => "",

            'address_city'           => "Melbourne",

            'address_country'        => "US",

            'address_state'          => "FL",

            'address_zip_code'       => "32935",

            'first_name'             => filter_input(INPUT_POST, 'first_name'),

            'last_name'              => filter_input(INPUT_POST, 'last_name'),

            'phone'                  => "(321) 255-4242)",

            'notes'                   => "Employee Notes",

            'ticket_commission_rate' => 0,

            'room_commission_rate'   => 0,

            'admin_commission_notes' => "{}",

            'approved'               => 1,

            'banned'                 => 0,

            'verified'               => 1,

            'is_employee'            => 1,

            'terms_privacy_signature' => filter_input(INPUT_POST, 'first_name') . " " . filter_input(INPUT_POST, 'last_name'),

            'affiliate_terms_conditions_signature' => filter_input(INPUT_POST, 'first_name') . " " . filter_input(INPUT_POST, 'last_name'),

            'admin_approval_signature' => "Admin Approved",

            'author'                 => $admin->getId(),

            'user_agent'              => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),

            'ip_address'              => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),

        )
    );


    // Set Response
    $json_response = array(
        'status'   => 'success',
        'message'  => "Employee Member successfully created",
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
