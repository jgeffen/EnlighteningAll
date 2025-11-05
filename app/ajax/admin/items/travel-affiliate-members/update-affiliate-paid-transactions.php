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
            'affiliate_id' => FILTER_VALIDATE_INT,
            'transaction_id' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'purchase_type' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'amount' => FILTER_VALIDATE_FLOAT,
            'commission_rate' => FILTER_VALIDATE_FLOAT,
            'commission' => FILTER_VALIDATE_FLOAT,
            'purchase_date' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'date_end' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
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


    $checkQuery = "SELECT COUNT(*) FROM `affiliate_paid_transactions` WHERE `transaction_id` = :transaction_id";

    $checkParams = array(
        'transaction_id' => filter_input(INPUT_POST, 'transaction_id')
    );

    $exists = Database::Action($checkQuery, $checkParams)->fetchColumn() > 0;

    // Prepare SQL query and parameters based on the existence of transaction_id
    if ($exists) {
        // If exists, prepare an UPDATE query
        $sql = "UPDATE `affiliate_paid_transactions` SET 
            `affiliate_id` = :affiliate_id,  
            `amount` = :amount,  
            `purchase_type` = :purchase_type,  
            `commission_rate` = :commission_rate,  
            `commission` = :commission,  
            `purchase_date` = :purchase_date,  
            `date_end` = :date_end 
            WHERE `transaction_id` = :transaction_id";
    } else {
        // If not exists, prepare an INSERT query
        $sql = "INSERT INTO `affiliate_paid_transactions` SET  
            `affiliate_id` = :affiliate_id,  
            `transaction_id` = :transaction_id,
            `purchase_type` = :purchase_type,    
            `amount` = :amount,  
            `commission_rate` = :commission_rate,  
            `commission` = :commission,  
            `purchase_date` = :purchase_date,  
            `date_end` = :date_end";
    }

    // Execute the query
    $params = array(
        'affiliate_id' => filter_input(INPUT_POST, 'affiliate_id'),
        'transaction_id' => filter_input(INPUT_POST, 'transaction_id'),
        'purchase_type' => filter_input(INPUT_POST, 'purchase_type'),
        'amount' => filter_input(INPUT_POST, 'amount'),
        'commission_rate' => filter_input(INPUT_POST, 'commission_rate'),
        'commission' => filter_input(INPUT_POST, 'commission'),
        'purchase_date' => filter_input(INPUT_POST, 'purchase_date'),
        'date_end' => filter_input(INPUT_POST, 'date_end'),
    );
    Database::Action($sql, $params);

    // Set Response
    $json_response = array(
        'status' => 'success',
        'message' => 'affiliate_paid_transations table has been updated successfully',
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
