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
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Check Logged In
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Variable Defaults
		$member = new Membership();
		
		// Check Existing Billing
		if(!$member->wallet()) throw new Exception('You do not have a billing account set up. Please refresh your page.');
		
		// Init MobiusPay Vault
		$mobiusPayVault = new MobiusPay\Vault();
		
		// Set Identifiers
		$mobiusPayVault->setVaultId($member->wallet()->getCustomerVaultId());
		
		// Process Transaction
		$mobiusPayVault->setType(MobiusPay\Vault::TYPE_DELETE_CUSTOMER)->doTransaction();
		
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
				// Remove Payment
				Database::Action("DELETE FROM `member_wallets` WHERE `id` = :id AND `member_id` = :member_id", array(
					'id'        => $member->wallet()->getId(),
					'member_id' => $member->getId()
				));
				
				// Log Action
				$member->log()->setData(
					type       : Types\Log::DELETE,
					table_name : Tables\Members::WALLETS,
					table_id   : $member->wallet()->getId()
				)->execute();
				
				// Set Response
				$json_response = array(
					'status' => 'success',
					'html'   => Template::Render('members/billing/delete.twig')
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
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output JSON
	echo json_encode($json_response);