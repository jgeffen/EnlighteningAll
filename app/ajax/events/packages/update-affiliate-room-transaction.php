<?php
/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/

/**
 * @var Router\Dispatcher $dispatcher
 */

// Imports
use Items\Enums\Sizes;
use Items\Enums\Tables;
use Items\Enums\Types;

try {


    // Set Errors
    $errors = call_user_func(function () {
        // Variable Defaults
        $required = array(
            'affiliate_id' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'amount' => FILTER_VALIDATE_FLOAT,
            'ticket_commission_rate' => FILTER_VALIDATE_FLOAT,
            'purchaser_email'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'booking_dates'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'room_name'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'date_end'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,

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

    // Update Database
    Database::Action(
        "INSERT INTO `affiliate_room_transactions` SET `affiliate_id` =:affiliate_id, `amount` =:amount,  `ticket_commission_rate` =:ticket_commission_rate, `purchaser_social_member_id` =:purchaser_social_member_id, `purchaser_email` =:purchaser_email, `booking_dates` =:booking_dates, `room_name` =:room_name, `date_end` =:date_end, `user_agent` =:user_agent, `ip_address` =:ip_address",
        array(
            'affiliate_id'               => filter_input(INPUT_POST, 'affiliate_id'),

            'amount'                     => filter_input(INPUT_POST, 'amount'),

            'ticket_commission_rate'     => filter_input(INPUT_POST, 'ticket_commission_rate'),

            'purchaser_social_member_id' => filter_input(INPUT_POST, 'purchaser_social_member_id'),

            'purchaser_email'            => filter_input(INPUT_POST, 'purchaser_email'),

            'booking_dates'              => filter_input(INPUT_POST, 'booking_dates'),

            'room_name'                  => filter_input(INPUT_POST, 'room_name'),

            'date_end'                   => filter_input(INPUT_POST, 'date_end'),

            'user_agent'                 => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),

            'ip_address'                 => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
        ),
        TRUE
    );

    // Set Response
    $json_response = array(
        'status' => 'success',
        'message' => 'affiliate_room_transations table has been updated successfully',
    );
} catch (FormException $exception) {
    // Set Response
    $json_response = array(
        'status' => 'error',
        'errors' => $exception->getErrors()
    );
} catch (PDOException $exception) {
    // Log Error
    Debug::Exception($exception);

    // Set Response
    $json_response = array(
        'status'  => 'error',
        'message' => 'There was an issue communicating with the database. Please try again later.'
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
