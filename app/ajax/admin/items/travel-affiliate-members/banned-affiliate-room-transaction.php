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
use Items\Enums\Sizes;
use Items\Enums\Tables;
use Items\Enums\Types;

try {


    // Set Errors
    $errors = call_user_func(function () {
        // Variable Defaults
        $required = array(
            'is_banned' => FILTER_VALIDATE_INT,
            'id' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
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
        "UPDATE `affiliate_room_transactions` SET `is_banned` = :is_banned WHERE `id` = :id",
        array(
            'is_banned' => filter_input(INPUT_POST, 'is_banned'),
            'id' => filter_input(INPUT_POST, 'id')
        ),
        TRUE
    );

    // Set Response
    $json_response = array(
        'status' => 'success',
        'message' => 'affiliate_room_transations table has been updated successfully with admin approval or disapproval',
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
