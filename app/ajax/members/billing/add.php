<?php
	/*
	Copyright (c) 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Daerik
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
		
		// Init MobiusPay Vault
		$mobiusPayVault = new MobiusPay\Vault();
		
		// Set Credit Card
		$mobiusPayVault->setCreditCard(
			account    : filter_input(INPUT_POST, 'cc_number'),
			type       : filter_input(INPUT_POST, 'cc_type'),
			expiration : filter_input(INPUT_POST, 'cc_expiry_month') . filter_input(INPUT_POST, 'cc_expiry_year'),
			cvv        : filter_input(INPUT_POST, 'cc_cvv')
		);
		
		// Set Billing
		$mobiusPayVault->setBilling(array(
			'first_name'     => filter_input(INPUT_POST, 'first_name'),
			'last_name'      => filter_input(INPUT_POST, 'last_name'),
			'email'          => filter_input(INPUT_POST, 'email'),
			'phone'          => filter_input(INPUT_POST, 'phone'),
			'address_line_1' => filter_input(INPUT_POST, 'address_line_1'),
			'address_line_2' => filter_input(INPUT_POST, 'address_line_2'),
			'city'           => filter_input(INPUT_POST, 'address_city'),
			'state'          => filter_input(INPUT_POST, 'address_state'),
			'zip_code'       => filter_input(INPUT_POST, 'address_zip_code'),
			'country'        => filter_input(INPUT_POST, 'address_country')
		));
		
		// Set Order
		$mobiusPayVault->setOrder(array(
			'ip_address'  => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'description' => 'Customer Added by enlighteningall.com'
		));
		
		// Process Transaction
		$mobiusPayVault->setType(MobiusPay\Vault::TYPE_ADD_CUSTOMER)->doTransaction();
		
		// Record Transaction Details
		$form_values = $mobiusPayVault->getTransactionDetails(array(
			// Record Billing Details
			'billing_first_name'      => $mobiusPayVault->getBilling('first_name'),
			'billing_last_name'       => $mobiusPayVault->getBilling('last_name'),
			'billing_email'           => $mobiusPayVault->getBilling('email'),
			'billing_phone'           => $mobiusPayVault->getBilling('phone'),
			'billing_fax'             => $mobiusPayVault->getBilling('fax'),
			'billing_company'         => $mobiusPayVault->getBilling('company'),
			'billing_address_line_1'  => $mobiusPayVault->getBilling('address_line_1'),
			'billing_address_line_2'  => $mobiusPayVault->getBilling('address_line_2'),
			'billing_city'            => $mobiusPayVault->getBilling('city'),
			'billing_state'           => $mobiusPayVault->getBilling('state'),
			'billing_zip_code'        => $mobiusPayVault->getBilling('zip_code'),
			'billing_country'         => $mobiusPayVault->getBilling('country'),
			
			// Record Shipping Details
			'shipping_first_name'     => $mobiusPayVault->getShipping('first_name'),
			'shipping_last_name'      => $mobiusPayVault->getShipping('last_name'),
			'shipping_company'        => $mobiusPayVault->getShipping('company'),
			'shipping_address_line_1' => $mobiusPayVault->getShipping('address_line_1'),
			'shipping_address_line_2' => $mobiusPayVault->getShipping('address_line_2'),
			'shipping_city'           => $mobiusPayVault->getShipping('city'),
			'shipping_state'          => $mobiusPayVault->getShipping('state'),
			'shipping_zip_code'       => $mobiusPayVault->getShipping('zip_code'),
			'shipping_country'        => $mobiusPayVault->getShipping('country'),
			'shipping_email'          => $mobiusPayVault->getShipping('email'),
			
			// Record Basic Data
			'member_id'               => $member->getId(),
			'merchant'                => MobiusPay\Client::MERCHANT_NAME,
			'form'                    => pathinfo(__FILE__, PATHINFO_FILENAME),
			'type'                    => $mobiusPayVault->getType(),
			'comments'                => $mobiusPayVault->getOrder('comments'),
			'captcha'                 => filter_input(INPUT_POST, 'captcha'),
			'user_agent'              => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'              => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'               => date('Y-m-d H:i:s'),
			
			// Record Vault Data
			'customer_vault_id'       => $mobiusPayVault->getTransaction('customer_vault_id'),
			'billing_id'              => $mobiusPayVault->getTransaction('billing_id'),
			'shipping_id'             => $mobiusPayVault->getTransaction('shipping_id'),
			
			// Record Sale Data
			'table_name'              => 'members',
			'table_id'                => $member->getId(),
			'payment_status'          => $mobiusPayVault->getPaymentStatus(),
			'transaction_id'          => $mobiusPayVault->getTransaction('transactionid'),
			'account_number'          => $mobiusPayVault->getAccountNumber(),
			'account_type'            => $mobiusPayVault->getAccountType(),
			'expiration_date'         => $mobiusPayVault->getExpirationDate(),
			'invoice'                 => MobiusPay\Client::GenerateInvoice('SFL', ...$mobiusPayVault->getBilling()),
			
			// Record Errors
			'response'                => $mobiusPayVault->getTransaction('response'),
			'response_code'           => $mobiusPayVault->getTransaction('response_code'),
			'response_text'           => $mobiusPayVault->getTransaction('responsetext'),
		));
		
		// Update Database
		Database::ArrayInsert('transactions', $form_values, TRUE);
		
		// Switch Response
		switch($mobiusPayVault->getTransaction('response')) {
			case MobiusPay\Client::RESPONSE_APPROVED:
				// Remove Current Default
				Database::Action("UPDATE `member_wallets` SET `default` = FALSE WHERE `member_id` = :member_id", array(
					'member_id' => $member->getId()
				));
				
				// Log Action
				$member->log()->setData(
					type       : Types\Log::CREATE,
					table_name : Tables\Members::WALLETS,
					table_id   : Database::ArrayInsert('member_wallets', array(
						'member_id'               => $member->getId(),
						'account_number'          => $form_values['account_number'],
						'account_type'            => $form_values['account_type'],
						'billing_address_line_1'  => $form_values['billing_address_line_1'],
						'billing_address_line_2'  => $form_values['billing_address_line_2'],
						'billing_city'            => $form_values['billing_city'],
						'billing_company'         => $form_values['billing_company'],
						'billing_country'         => $form_values['billing_country'],
						'billing_email'           => $form_values['billing_email'],
						'billing_fax'             => $form_values['billing_fax'],
						'billing_first_name'      => $form_values['billing_first_name'],
						'billing_id'              => $form_values['billing_id'],
						'billing_last_name'       => $form_values['billing_last_name'],
						'billing_phone'           => $form_values['billing_phone'],
						'billing_state'           => $form_values['billing_state'],
						'billing_zip_code'        => $form_values['billing_zip_code'],
						'customer_vault_id'       => $form_values['customer_vault_id'],
						'default'                 => TRUE,
						'expiration_date'         => $form_values['expiration_date'],
						'shipping_address_line_1' => $form_values['shipping_address_line_1'],
						'shipping_address_line_2' => $form_values['shipping_address_line_2'],
						'shipping_city'           => $form_values['shipping_city'],
						'shipping_company'        => $form_values['shipping_company'],
						'shipping_country'        => $form_values['shipping_country'],
						'shipping_email'          => $form_values['shipping_email'],
						'shipping_first_name'     => $form_values['shipping_first_name'],
						'shipping_id'             => $form_values['shipping_id'],
						'shipping_last_name'      => $form_values['shipping_last_name'],
						'shipping_state'          => $form_values['shipping_state'],
						'shipping_zip_code'       => $form_values['shipping_zip_code'],
						'user_agent'              => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
						'ip_address'              => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
						'timestamp'               => date('Y-m-d H:i:s')
					))
				)->execute();
				
				// Set Response
				$json_response = array(
					'status' => 'success',
					'html'   => Template::Render('members/billing/success.twig')
				);
				break;
			case MobiusPay\Client::RESPONSE_DECLINED:
			case MobiusPay\Client::RESPONSE_ERROR:
				// Set Response
				$json_response = array(
					'status'  => 'error',
					'message' => sprintf("%s: %s", $form_values['response_code'], $form_values['response_text'])
				);
				break;
			default:
				// Set Response
				$json_response = array(
					'status'  => 'error',
					'message' => 'An unknown error has occurred. Please refresh the page.'
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