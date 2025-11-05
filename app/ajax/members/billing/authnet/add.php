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
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Check Existing Billing
		if($member->wallet()) throw new Exception('You already have a billing account set up. Please refresh your page.');
		
		// Validate Form
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
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
			);
			
			// Check Required Fields
			foreach($required as $field => $validation) {
				if(!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
					$errors[] = sprintf("%s is missing or invalid.", Helpers::PrettyTitle($field));
				}
			}
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// Init AuthorizeNet Vault
		$authnetClient = new AuthorizeNet\CIM\Client();
		
		// Set Credit Card
		$authnetClient->setCreditCard(
			account    : filter_input(INPUT_POST, 'cc_number'),
			expiration : filter_input(INPUT_POST, 'cc_expiry_month') . filter_input(INPUT_POST, 'cc_expiry_year'),
			cvv        : filter_input(INPUT_POST, 'cc_cvv'),
			type       : filter_input(INPUT_POST, 'cc_type')
		);
		
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
			description       : sprintf("Add Subscription: %s (%s)", $member->getId(), $member->getFullName()),
			id                : filter_input(INPUT_POST, 'package_id'),
			ip_address        : filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			po_number         : NULL,
			shipping          : 0.00,
			tax               : 0.00,
			discount          : 0.00,
			comments          : filter_input(INPUT_POST, 'comments'),
			invoice           : $authnetClient->getInvoice('AN', ...$authnetClient->getBilling()->toArray()),
			customer_vault_id : NULL,
			billing_id        : NULL
		);
		
		// Process Transaction
		$authnetClient->setType(AuthorizeNet\CIM\Client::TYPE_CREATE_CARD)->doTransaction();
		
		// Record Transaction Details
		$form_values = $authnetClient->getTransactionDetails(array(
			// Record Billing Details
			'billing_first_name'      => $authnetClient->getBilling()->getFirstName(),
			'billing_last_name'       => $authnetClient->getBilling()->getLastName(),
			'billing_email'           => $authnetClient->getBilling()->getEmail(),
			'billing_phone'           => $authnetClient->getBilling()->getPhone(),
			'billing_fax'             => $authnetClient->getBilling()->getFax(),
			'billing_company'         => $authnetClient->getBilling()->getCompany(),
			'billing_address_line_1'  => $authnetClient->getBilling()->getAddressLine1(),
			'billing_address_line_2'  => $authnetClient->getBilling()->getAddressLine2(),
			'billing_city'            => $authnetClient->getBilling()->getCity(),
			'billing_state'           => $authnetClient->getBilling()->getState(),
			'billing_zip_code'        => $authnetClient->getBilling()->getZipCode(),
			'billing_country'         => $authnetClient->getBilling()->getCountry(),
			
			// Record Shipping Details
			'shipping_first_name'     => $authnetClient->getShipping()->getFirstName(),
			'shipping_last_name'      => $authnetClient->getShipping()->getLastName(),
			'shipping_email'          => $authnetClient->getShipping()->getEmail(),
			'shipping_phone'          => $authnetClient->getShipping()->getPhone(),
			'shipping_fax'            => $authnetClient->getShipping()->getFax(),
			'shipping_company'        => $authnetClient->getShipping()->getCompany(),
			'shipping_address_line_1' => $authnetClient->getShipping()->getAddressLine1(),
			'shipping_address_line_2' => $authnetClient->getShipping()->getAddressLine2(),
			'shipping_city'           => $authnetClient->getShipping()->getCity(),
			'shipping_state'          => $authnetClient->getShipping()->getState(),
			'shipping_zip_code'       => $authnetClient->getShipping()->getZipCode(),
			'shipping_country'        => $authnetClient->getShipping()->getCountry(),
			
			// Record Basic Data
			'member_id'               => $member?->getId(),
			'billing_id'              => $authnetClient->getTransaction()->getBillingId(),
			'customer_vault_id'       => $authnetClient->getTransaction()->getCustomerVaultId(),
			'customer_profile_id'     => $authnetClient->getTransaction()->getCustomerProfileId(),
			'payment_profile_id'      => $authnetClient->getTransaction()->getPaymentProfileId(),
			'expiration_date'         => $authnetClient->getTransaction()->getExpirationDate(),
			'merchant'                => AuthorizeNet\CIM\Client::MERCHANT_NAME,
			'form'                    => pathinfo(__FILE__, PATHINFO_FILENAME),
			'type'                    => $authnetClient->getType(),
			'comments'                => $authnetClient->getOrder()->getComments(),
			'captcha'                 => filter_input(INPUT_POST, 'captcha'),
			'user_agent'              => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'              => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'               => date('Y-m-d H:i:s'),
			
			// Record Sale Data
			'name'                    => filter_input(INPUT_POST, 'first_name') . ' ' . filter_input(INPUT_POST, 'last_name'),
			'table_name'              => 'create card',
			'table_id'                => $member->getId(),
			'amount'                  => 0.00,
			'sales_tax'               => 0.00,
			'account_number'          => $authnetClient->getAccountNumber(),
			'account_type'            => $authnetClient->getAccountType(),
			'invoice'                 => $authnetClient->getInvoice(),
			'response'                => $authnetClient->getCimResponse()
		));
		
		// Update Database
		$transaction_id = Database::ArrayInsert('transactions', $form_values, TRUE);
		
		if($authnetClient->getCimResponse()->isSuccessful()) {
			Database::Action("UPDATE `member_wallets` SET `default` = FALSE WHERE `member_id` = :member_id", array(
				'member_id' => $member->getId()
			));
			// Log Action
			$member->log()->setData(
				type       : Types\Log::CREATE,
				table_name : Tables\Members::WALLETS,
				table_id   : Database::ArrayInsert('member_wallets', array(
					'member_id'              => $member->getId(),
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
					'customer_profile_id'    => $form_values['customer_profile_id'],
					'payment_profile_id'     => $form_values['payment_profile_id'],
					'default'                => TRUE,
					'expiration_date'        => $form_values['expiration_date'],
					'user_agent'             => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
					'ip_address'             => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
					'timestamp'              => date('Y-m-d H:i:s')
				))
			)->execute();
			
			// Set Response
			$json_response = array(
				'status' => 'success',
				'html'   => Template::Render('members/billing/success.twig')
			);
		} else {
			$json_response = array(
				'status'   => 'error',
				'response' => $authnetClient->getCimResponse()->getData(),
				'message'  => 'An unknown error has occurred. Please refresh the page.'
			);
		}
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status' => 'error',
			'errors' => $exception->getErrors()
		);
	} catch(Error|PDOException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception)
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output JSON
	echo json_encode($json_response);