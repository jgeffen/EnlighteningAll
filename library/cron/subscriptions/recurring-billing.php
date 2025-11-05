<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	// Required Files
	require(sprintf("%s/vendor/autoload.php", dirname(__DIR__, 2)));
	
	// Imports
	use gfaugere\Monolog\Formatter\ColoredLineFormatter;
	use Items\Enums\Statuses;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Monolog\Handler\StreamHandler;
	use Monolog\Logger;
	
	// Set Line Formatter
	$lineFormatter = new ColoredLineFormatter("[%datetime%] %color_start%%level_name%%color_end%: %message%\n", 'Y-m-d H:i:s', FALSE, FALSE, array(
		Logger::DEBUG     => "\033[0;37m",
		Logger::INFO      => "\033[0;36m",
		Logger::NOTICE    => "\033[1;34m",
		Logger::WARNING   => "\033[1;33m",
		Logger::ERROR     => "\033[1;91m",
		Logger::CRITICAL  => "\033[0;31m",
		Logger::ALERT     => "\033[0;31m",
		Logger::EMERGENCY => "\033[1;35m"
	));
	
	// Set Stream Handler
	$streamHandler = new StreamHandler('php://stdout', Logger::INFO);
	$streamHandler->setFormatter($lineFormatter);
	
	// Set Logger
	$logger = new Logger(AuthorizeNet\CIM\Client::MERCHANT_NAME);
	$logger->pushHandler($streamHandler);
	
	// Output Message
	$logger->debug(sprintf("Start: %s", date_create()->format('Y-m-d H:i:s')));
	
	try {
		// Variable Defaults
		$transaction_id = NULL;
		$members        = Membership::FetchAll(Database::Action("SELECT * FROM `members` WHERE `id` IN (SELECT `member_id` FROM `member_subscriptions` WHERE `status` = :status AND `date_renewal` <= :date_renewal) ORDER BY `last_name`, `first_name`", array(
			'status'       => Statuses\Subscription::ACTIVE->getValue(),
			'date_renewal' => date_create()->format('Y-m-d')
		)));
		
		// Iterate Over Members
		foreach($members as $member) {
			// Variable Defaults
			$sleep = 5; // Seconds
			
			// Output Message
			$logger->info(sprintf("Member #%d %s (%s)", $member->getId(), $member->getFullNameLast(), $member->getEmail()));
			
			try {
				// Check Subscription
				if(!$member->subscription()?->getSubscription()) throw new Exception('Subscription not found in database.');
				
				// Check Expiration
				if($member->subscription()->getCancellationDate()) {
					// Output Message
					$logger->alert('Cancelled by customer.');
					
					// Update Database
					Database::Action("UPDATE `member_subscriptions` SET `status` = :status, `details` = :details WHERE `id` = :id", array(
						'status'  => Statuses\Subscription::CANCELLED->getValue(),
						'details' => 'Cancelled by customer.',
						'id'      => $member->subscription()->getId()
					));
					continue;
				}
				
				// Check Wallet
				if(!$member->wallet()) throw new Exception('No method of payment found for this account.');
				
				// Check Expiration
				if($member->wallet()->isExpired()) throw new Exception('Credit card has expired.');
				
				// Init AuthNet Vault
				$vault = new AuthorizeNet\CIM\Client();
				
				// Set Order
				$vault->setOrder(
					amount            : $member->subscription()->getSubscription()->getPrice(),
					description       : sprintf("Add Subscription: %s (%s)", $member->getId(), $member->getFullName()),
					id                : filter_input(INPUT_POST, 'package_id'),
					ip_address        : filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
					po_number         : NULL,
					shipping          : 0.00,
					tax               : 0.00,
					discount          : 0.00,
					comments          : filter_input(INPUT_POST, 'comments'),
					invoice           : $vault->getInvoice('AN', ...$vault->getBilling()->toArray()),
					customer_vault_id : $member->wallet()->getCustomerVaultId(),
					billing_id        : $member->wallet()->getBillingId(),
				);
				
				// Process Transaction
				$vault->setType(AuthorizeNet\CIM\Client::TYPE_PURCHASE)->doTransaction($member->subscription()->getSubscription()->getPrice());
				
				// Record Transaction Details
				$form_values = $vault->getTransactionDetails(array(
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
					'type'                   => $vault->getType(),
					'comments'               => $vault->getOrder()->getComments(),
					'user_agent'             => 'System',
					'ip_address'             => '127.0.0.1',
					
					// Record Vault Data
					'customer_vault_id'      => $vault->getTransaction()->getCustomerVaultId(),
					'billing_id'             => $member->wallet()->getBillingId(),
					
					// Record Sale Data
					'table_name'             => Tables\Website::SUBSCRIPTIONS->getValue(),
					'table_id'               => $member->subscription()->getId(),
					'payment_status'         => $vault->getTransaction()->getPaymentStatus(),
					'transaction_id'         => $vault->getTransaction()->getTransId(),
					'amount'                 => $member->subscription()->getSubscription()->getPrice(),
					'account_number'         => $member->wallet()->getAccountNumber(),
					'account_type'           => $member->wallet()->getAccountType(),
					'expiration_date'        => $member->wallet()->getExpirationDate()->format('Y-m-d'),
					'invoice'                => AuthorizeNet\CIM\Client::GenerateInvoice('AN', ...$vault->getBilling()->toArray()),
					
					// Record Errors
					'response'               => $vault->getCimResponse(),
					'response_code'          => $vault->getTransaction()->getResponseCode()
				));
				
				// Update Database
				$transaction_id = Database::ArrayInsert('transactions', $form_values, TRUE);
				
				// Switch Response
				if($vault->getCimResponse()->isSuccessful()) {
					
					// Update Database
					Database::Action("UPDATE `member_subscriptions` SET `transaction_id` = :transaction_id, `date_renewal` = :date_renewal WHERE `id` = :id", array(
						'transaction_id' => $transaction_id,
						'date_renewal'   => Helpers::MonthShifter(date_create(), 1)->format('Y-m-d'),
						'id'             => $member->subscription()->getId()
					));
					
					if($member->getReferredBy()) {
						Database::Action("UPDATE `member_wallets` SET `points` = points + 1 WHERE `member_id` = :member_id", array(
							'member_id' => $member->getReferredBy()
						));
					}
					
					// Log Action
					$member->log()->setData(
						type       : Types\Log::UPDATE,
						table_name : Tables\Members::SUBSCRIPTIONS,
						table_id   : $member->subscription()->getId()
					)->execute();
					
					// Output Message
					$logger->notice("Transaction approved.");
				} else {
					throw new Exception(sprintf("%s", $form_values['response_code']));
				}
				
				// Pause
				sleep($sleep);
			} catch(Exception $exception) {
				// Output Message
				$logger->alert($exception->getMessage());
				
				// Update Database
				Database::Action("UPDATE `member_subscriptions` SET `status` = :status, `transaction_id` = :transaction_id, `details` = :details WHERE `id` = :id", array(
					'status'         => Statuses\Subscription::INACTIVE->getValue(),
					'transaction_id' => $transaction_id,
					'details'        => $exception->getMessage(),
					'id'             => $member->subscription()?->getId()
				));
			}
		}
	} catch(Exception $exception) {
		// Output Message
		$logger->critical($exception->getMessage());
		
		// Mail Developer
		mail(DEV_EMAIL, DEV_SUBJ, Debug::Exception($exception, $logger), array(sprintf("From: %s", DEV_FROM)));
	}
	
	// Output Message
	$logger->debug(sprintf("End: %s", date_create()->format('Y-m-d H:i:s')));