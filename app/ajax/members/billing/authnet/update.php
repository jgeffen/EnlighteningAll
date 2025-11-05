<?php
/*
	Copyright (c) 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Membership        $member
 */

// Imports
use Items\Enums\Tables;
use Items\Enums\Types;

try {
    // Check Logged In
    if (Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');

    // Check Existing Billing
    if (!$member->wallet()) throw new Exception('You do not have a billing account set up. Please refresh your page.');

    // Validate Form
    $errors = call_user_func(function() {
        $required = [
            'first_name'       => FILTER_DEFAULT,
            'last_name'        => FILTER_DEFAULT,
            'phone'            => FILTER_DEFAULT,
            'email'            => FILTER_VALIDATE_EMAIL,
            'address_line_1'   => FILTER_DEFAULT,
            'address_city'     => FILTER_DEFAULT,
            'address_state'    => FILTER_DEFAULT,
            'address_zip_code' => FILTER_DEFAULT,
            'address_country'  => FILTER_DEFAULT,
            'cc_number'        => FILTER_DEFAULT,
            'cc_type'          => FILTER_DEFAULT,
            'cc_expiry_month'  => FILTER_DEFAULT,
            'cc_expiry_year'   => FILTER_DEFAULT,
            'cc_cvv'           => FILTER_DEFAULT
        ];

        foreach ($required as $field => $validation) {
            if (!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
                $errors[] = sprintf("%s is missing or invalid.", Helpers::PrettyTitle($field));
            }
        }
        return $errors ?? FALSE;
    });

    if (!empty($errors)) throw new FormException($errors, 'You are missing required fields.');

    // Init AuthorizeNet Vault
    $authnetClient = new AuthorizeNet\CIM\Client();

    // Set Credit Card
    $authnetClient->setCreditCard(
        account    : filter_input(INPUT_POST, 'cc_number'),
        expiration : filter_input(INPUT_POST, 'cc_expiry_month') . filter_input(INPUT_POST, 'cc_expiry_year'),
        cvv        : filter_input(INPUT_POST, 'cc_cvv'),
        type       : filter_input(INPUT_POST, 'cc_type')
    );

    // Set Billing
    $authnetClient->setBilling(
        address_line_1 : filter_input(INPUT_POST, 'address_line_1'),
        address_line_2 : filter_input(INPUT_POST, 'address_line_2'),
        city           : filter_input(INPUT_POST, 'address_city'),
        company        : filter_input(INPUT_POST, 'company'),
        country        : filter_input(INPUT_POST, 'address_country'),
        email          : filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
        fax            : filter_input(INPUT_POST, 'fax'),
        first_name     : filter_input(INPUT_POST, 'first_name'),
        last_name      : filter_input(INPUT_POST, 'last_name'),
        phone          : filter_input(INPUT_POST, 'phone'),
        state          : filter_input(INPUT_POST, 'address_state'),
        website        : filter_input(INPUT_POST, 'website', FILTER_VALIDATE_URL),
        zip_code       : filter_input(INPUT_POST, 'address_zip_code')
    );

    // Set Order
    $authnetClient->setOrder(
        amount            : 0.00,
        description       : sprintf("Update Subscription: %s (%s)", $member->getId(), $member->getFullName()),
        id                : filter_input(INPUT_POST, 'package_id'),
        ip_address        : filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
        po_number         : NULL,
        shipping          : 0.00,
        tax               : 0.00,
        discount          : 0.00,
        comments          : filter_input(INPUT_POST, 'comments'),
        invoice           : $authnetClient->getInvoice('AN', ...$authnetClient->getBilling()->toArray()),
        customer_vault_id : $member->wallet()->getCustomerVaultId(),
        billing_id        : $member->wallet()->getBillingId()
    );

    // Process Transaction
    $authnetClient->setType(AuthorizeNet\CIM\Client::TYPE_UPDATE)->doTransaction();

    try {
        // Record Transaction Details
        $form_values = $authnetClient->getTransactionDetails([
            'billing_first_name'     => $authnetClient->getBilling()->getFirstName(),
            'billing_last_name'      => $authnetClient->getBilling()->getLastName(),
            'billing_email'          => $authnetClient->getBilling()->getEmail(),
            'billing_phone'          => $authnetClient->getBilling()->getPhone(),
            'billing_fax'            => $authnetClient->getBilling()->getFax(),
            'billing_company'        => $authnetClient->getBilling()->getCompany(),
            'billing_address_line_1' => $authnetClient->getBilling()->getAddressLine1(),
            'billing_address_line_2' => $authnetClient->getBilling()->getAddressLine2(),
            'billing_city'           => $authnetClient->getBilling()->getCity(),
            'billing_state'          => $authnetClient->getBilling()->getState(),
            'billing_zip_code'       => $authnetClient->getBilling()->getZipCode(),
            'billing_country'        => $authnetClient->getBilling()->getCountry(),
            'member_id'              => $member->getId(),
            'merchant'               => AuthorizeNet\CIM\Client::MERCHANT_NAME,
            'form'                   => pathinfo(__FILE__, PATHINFO_FILENAME),
            'type'                   => $authnetClient->getType(),
            'comments'               => $authnetClient->getOrder()->getComments(),
            'captcha'                => filter_input(INPUT_POST, 'captcha'),
            'user_agent'             => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
            'ip_address'             => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
            'timestamp'              => date('Y-m-d H:i:s'),
            'customer_vault_id'      => $authnetClient->getTransaction()->getCustomerVaultId(),
            'billing_id'             => $authnetClient->getTransaction()->getBillingId(),
            'payment_profile_id'     => $authnetClient->getTransaction()->getPaymentProfileId(),
            'customer_profile_id'    => $authnetClient->getTransaction()->getCustomerProfileId(),
            'table_name'             => 'update card',
            'table_id'               => $member->getId(),
            'payment_status'         => $authnetClient->getTransaction()->getPaymentStatus(),
            'transaction_id'         => $authnetClient->getTransaction()->getTransId(),
            'expiration_date'        => $authnetClient->getTransaction()->getExpirationDate(),
            'account_number'         => $authnetClient->getAccountNumber(),
            'account_type'           => $authnetClient->getAccountType(),
            'invoice'                => $authnetClient->getInvoice(),
            'response'               => $authnetClient->getCimResponse()
        ]);

        Database::ArrayInsert('transactions', $form_values, TRUE);

        $responseObj = $authnetClient->getCimResponse();

        // Log raw gateway info for diagnostics
        if ($responseObj) {
            error_log("AUTHNET RAW RESPONSE CLASS: " . get_class($responseObj));
            if (method_exists($responseObj, 'getData')) {
                error_log("AUTHNET RAW RESPONSE DATA:\n" . print_r($responseObj->getData(), true));
            }
        }

        if (is_object($responseObj) && method_exists($responseObj, 'isSuccessful') && $responseObj->isSuccessful()) {
            $member->log()->setData(
                type       : Types\Log::CREATE,
                table_name : Tables\Members::WALLETS,
                table_id   : Database::ArrayUpdate('member_wallets', [
                    'account_number'         => $form_values['account_number'],
                    'account_type'           => $form_values['account_type'],
                    'billing_address_line_1' => $form_values['billing_address_line_1'],
                    'billing_address_line_2' => $form_values['billing_address_line_2'],
                    'billing_city'           => $form_values['billing_city'],
                    'billing_company'        => $form_values['billing_company'],
                    'billing_country'        => $form_values['billing_country'],
                    'billing_email'          => $form_values['billing_email'],
                    'billing_fax'            => $form_values['billing_fax'],
                    'billing_first_name'     => $form_values['billing_first_name'],
                    'billing_id'             => $form_values['billing_id'],
                    'billing_last_name'      => $form_values['billing_last_name'],
                    'billing_phone'          => $form_values['billing_phone'],
                    'billing_state'          => $form_values['billing_state'],
                    'billing_zip_code'       => $form_values['billing_zip_code'],
                    'customer_vault_id'      => $form_values['customer_vault_id'],
                    'expiration_date'        => $form_values['expiration_date'],
                    'user_agent'             => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
                    'ip_address'             => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
                    'last_timestamp'         => date('Y-m-d H:i:s')
                ], ['id' => $member->wallet()->getId()])
            )->execute();

            $json_response = [
                'status' => 'success',
                'html'   => Template::Render('members/billing/success.twig')
            ];
        } else {
            // Safely extract message and raw data
            $data = method_exists($responseObj, 'getData') ? $responseObj->getData() : [];
            $message = method_exists($responseObj, 'getMessage') ? $responseObj->getMessage() : null;
            $message = $message
                ?: ($data['messages']['message'][0]['text'] ?? 'No response text provided.');

            error_log("AUTHNET_ERROR: " . $message);
            error_log("AUTHNET_ERROR DATA:\n" . print_r($data, true));

            $json_response = [
                'status'  => 'error',
                'message' => 'AUTHNET_ERROR: ' . $message
            ];
        }
    } catch (FormException $exception) {
        $json_response = [
            'status' => 'error',
            'errors' => $exception->getErrors()
        ];
    } catch (PDOException $exception) {
        $json_response = [
            'status'  => 'error',
            'message' => Debug::Exception($exception)
        ];
    }
} catch (Exception $exception) {
    $json_response = [
        'status'  => 'error',
        'message' => $exception->getMessage()
    ];
}

// Output JSON
echo json_encode($json_response);