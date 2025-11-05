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
            'transaction_id'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'amount' => FILTER_VALIDATE_FLOAT,
            'ticket_commission_rate' => FILTER_VALIDATE_FLOAT,
            'purchaser_email'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'transactions_table_id'  => FILTER_VALIDATE_INT,
            'confirmed_payment'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'type_table_name'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'type_table_id'  => FILTER_VALIDATE_INT,

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
        "INSERT INTO `affiliate_transactions` SET `affiliate_id` =:affiliate_id, `transaction_id` =:transaction_id, `amount` =:amount,  `ticket_commission_rate` =:ticket_commission_rate, `purchaser_social_member_id` =:purchaser_social_member_id, `purchaser_email` =:purchaser_email, `transactions_table_id` =:transactions_table_id, `confirmed_payment` =:confirmed_payment, `type_table_name` =:type_table_name, `type_table_id` =:type_table_id, `date_end` =:date_end, `user_agent` =:user_agent, `ip_address` =:ip_address",
        array(
            'affiliate_id'               => filter_input(INPUT_POST, 'affiliate_id'),

            'transaction_id'             => filter_input(INPUT_POST, 'transaction_id'),

            'amount'                     => filter_input(INPUT_POST, 'amount'),

            'ticket_commission_rate'     => filter_input(INPUT_POST, 'ticket_commission_rate'),

            'purchaser_social_member_id' => filter_input(INPUT_POST, 'purchaser_social_member_id'),

            'purchaser_email'            => filter_input(INPUT_POST, 'purchaser_email'),

            'transactions_table_id'      => filter_input(INPUT_POST, 'transactions_table_id'),

            'confirmed_payment'          => filter_input(INPUT_POST, 'confirmed_payment'),

            'type_table_name'            => filter_input(INPUT_POST, 'type_table_name'),

            'type_table_id'              => filter_input(INPUT_POST, 'type_table_id'),

            'date_end'                   => (Database::Action("SELECT `date_end` FROM `events` WHERE `id` = :id", array(
                'id' => filter_input(INPUT_POST, 'type_table_id')
            ))->fetchAll(PDO::FETCH_COLUMN, 0)[0]),

            'user_agent'                 => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),

            'ip_address'                 => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
        ),
        TRUE
    );

    // Set Response
    $json_response = array(
        'status' => 'success',
        'message' => 'affiliate_transations table has been updated successfully',
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
