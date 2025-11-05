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
	 * @var Membership        $member
	 */
	
	// Imports
	use Items\Enums\Statuses;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Variable Defaults
		$subscription = Items\Subscription::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Subscription
		if(is_null($subscription)) throw new Exception('Subscription not found in database. Please refresh your page.');
		
		// Check Active Subscription
		if($member->subscription()?->getSubscriptionId() == $subscription->getId()) throw new Exception('This subscription is already active on your account.');
		
		// Check Wallet
		if(is_null($member->wallet())) throw new Exception('No method of payment found for this account.');
		
		// Check Expiration
		if($member->wallet()->isExpired()) throw new Exception('Your card has expired. Please update your method of payment.');
		
		// Has the member ever subscribed to THIS plan before?
		$free_trial_used = (int)Database::Action("SELECT COUNT(*) FROM `member_subscriptions` WHERE `member_id` = :member_id AND `subscription_id` = :subscription_id", array(
				'member_id'       => $member->getId(),
				'subscription_id' => $subscription->getId()
			))->fetchColumn() > 0;
		
		// Init AuthorizeNet Vault
		$authnetClient = new AuthorizeNet\CIM\Client();
		
		// Set Order
		$authnetClient->setOrder(
			amount            : !$free_trial_used ? 0.00 : $subscription->getPrice(),
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
			billing_id        : $member->wallet()->getBillingId(),
		);
		
		$amount = !$free_trial_used ? 0.00 : $subscription->getPrice();
		
		if($amount > 0.00) {
			$authnetClient->setType(AuthorizeNet\CIM\Client::TYPE_PURCHASE)->doTransaction($amount);
			
			// Record Transaction Details
			$form_values = $authnetClient->getTransactionDetails(array(
				// Record Billing Details
				'billing_first_name'     => $member->wallet()->getBillingFirstName(),
				'billing_last_name'      => $member->wallet()->getBillingLastName(),
				'billing_email'          => $member->wallet()->getBillingEmail(),
				'billing_phone'          => $member->wallet()->getBillingPhone(),
				'billing_fax'            => $member->wallet()->getBillingFax(),
				'billing_company'        => $member->wallet()->getBillingCompany(),
				'billing_address_line_1' => $member->wallet()->getBillingAddressLine1(),
				'billing_address_line_2' => $member->wallet()->getBillingAddressLine2(),
				'billing_city'           => $member->wallet()->getBillingCity(),
				'billing_state'          => $member->wallet()->getBillingState(),
				'billing_zip_code'       => $member->wallet()->getBillingZipCode(),
				'billing_country'        => $member->wallet()->getBillingCountry(),
				
				// Record Basic Data
				'member_id'              => $member->getId(),
				'merchant'               => AuthorizeNet\CIM\Client::MERCHANT_NAME,
				'form'                   => pathinfo(__FILE__, PATHINFO_FILENAME),
				'type'                   => $authnetClient->getType(),
				'comments'               => $authnetClient->getOrder()->getComments(),
				'user_agent'             => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'             => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
				
				// Record Vault Data
				'customer_vault_id'      => $authnetClient->getTransaction()->getCustomerVaultId(),
				'customer_profile_id'    => $authnetClient->getTransaction()->getCustomerProfileId(),
				'payment_profile_id'     => $authnetClient->getTransaction()->getPaymentProfileId(),
				'billing_id'             => $authnetClient->getTransaction()->getBillingId(),
				
				// Record Sale Data
				'table_name'             => Tables\Website::SUBSCRIPTIONS->getValue(),
				'table_id'               => $subscription->getId(),
				'payment_status'         => $authnetClient->getTransaction()->getPaymentStatus(),
				'transaction_id'         => $authnetClient->getTransaction()->getTransId(),
				'amount'                 => $subscription->getPrice(),
				'account_number'         => $member->wallet()->getAccountNumber(),
				'account_type'           => $member->wallet()->getAccountType(),
				'expiration_date'        => $member->wallet()->getExpirationDate()->format('Y-m-d'),
				'invoice'                => AuthorizeNet\CIM\Client::GenerateInvoice('AN', ...$authnetClient->getBilling()->toArray()),
				
				'response' => $authnetClient->getTransaction()->getResponseCode()
			));
			
			// Update Database
			$transaction_id = Database::ArrayInsert('transactions', $form_values, TRUE);
		} else {
			// Manually insert a dummy transaction row for internal tracking
			$transaction_id = Database::ArrayInsert('transactions', array(
				'member_id'        => $member->getId(),
				'merchant'         => AuthorizeNet\CIM\Client::MERCHANT_NAME,
				'type'             => AuthorizeNet\CIM\Client::TYPE_VALIDATE,
				'form'             => pathinfo(__FILE__, PATHINFO_FILENAME),
				'table_name'       => Tables\Website::SUBSCRIPTIONS->getValue(),
				'table_id'         => $subscription->getId(),
				'amount'           => 0.00,
				'payment_status'   => 'validated',
				'account_number'   => $member->wallet()->getAccountNumber(),
				'account_type'     => $member->wallet()->getAccountType(),
				'expiration_date'  => $member->wallet()->getExpirationDate()->format('Y-m-d'),
				'invoice'          => AuthorizeNet\CIM\Client::GenerateInvoice('AN', ...$member->wallet()->toArray()),
				'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
				'response'         => 'success'
			), TRUE);
		}
		// Switch Response
		if($amount <= 0.00 || $authnetClient->getCimResponse()->isSuccessful()) {
			// Check Subscription
			if($member->hasSubscription($subscription)) {
				// Update Database
				Database::Action("UPDATE `member_subscriptions` SET `status` = :status, `transaction_id` = :transaction_id, `date_start` = :date_start, `date_renewal` = :date_renewal, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `member_id` = :member_id AND `subscription_id` = :subscription_id", array(
					'status'          => Statuses\Subscription::ACTIVE->getValue(),
					'transaction_id'  => $transaction_id,
					'date_start'      => date_create()->format('Y-m-d'),
					'date_renewal'    => Helpers::MonthShifter(date_create(), 1)->format('Y-m-d'),
					'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
					'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
					'member_id'       => $member->getId(),
					'subscription_id' => $subscription->getId()
				));
				
				// Log Action
				$member->log()->setData(
					type       : Types\Log::UPDATE,
					table_name : Tables\Members::SUBSCRIPTIONS,
					table_id   : $member->subscription()?->getId()
				)->execute();
			} else {
				// Update Database
				$member_subscription_id = Database::Action("INSERT INTO `member_subscriptions` SET `status` = :status, `member_id` = :member_id, `subscription_id` = :subscription_id, `transaction_id` = :transaction_id, `date_start` = :date_start, `date_renewal` = :date_renewal, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
					'status'          => Statuses\Subscription::ACTIVE->getValue(),
					'member_id'       => $member->getId(),
					'subscription_id' => $subscription->getId(),
					'transaction_id'  => $transaction_id,
					'date_start'      => date_create()->format('Y-m-d'),
					'date_renewal'    => !$free_trial_used ? date_create()->add(new DateInterval('P90D'))->format('Y-m-d') : Helpers::MonthShifter(date_create(), 1)->format('Y-m-d'),
					'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
					'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
				), TRUE);
				
				// Log Action
				$member->log()->setData(
					type       : Types\Log::CREATE,
					table_name : Tables\Members::SUBSCRIPTIONS,
					table_id   : $member_subscription_id
				)->execute();
			}
			
			// Set Response
			$json_response = array(
				'status'  => 'success',
				'message' => sprintf("Successfully subscribed to %s!", $subscription->getName())
			);
		} else {
			// Set Response
			$json_response = array(
				'status'  => 'error',
				'message' => sprintf("%s", $form_values['response'])
			);
		}
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status' => 'error',
			'errors' => $exception->getErrors()
		);
	} catch(PDOException $exception) {
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