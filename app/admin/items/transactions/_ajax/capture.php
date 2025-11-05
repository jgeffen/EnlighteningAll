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
	 * @var Admin\User        $admin
	 */
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	// TODO: Make dynamic for multiple transaction forms.
	
	try {
		// Variable Defaults
		$amount   = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT, array('options' => array('default' => 0.00)));
		$to_email = SITE_EMAIL;
		$subjects = array(
			'success' => sprintf("Booking Capture Success - %s", SITE_NAME),
			'failure' => sprintf("Booking Capture Failure - %s", SITE_NAME),
			'receipt' => sprintf("Booking Capture Receipt - %s", SITE_NAME)
		);
		
		// Set Transaction
		$transaction = Items\Transaction::Fetch(Database::Action("SELECT * FROM `transactions` WHERE `table_name` = :table_name AND `table_id` = :table_id AND `id` = :id", array(
			'table_name' => $dispatcher->getTableName(),
			'table_id'   => $dispatcher->getTableId(),
			'id'         => $dispatcher->getId()
		)));
		
		// Check Transaction
		if(is_null($transaction)) throw new Exception('Transaction not found in the database. Please try again or refresh your page.');
		
		// Check Amount
		if($amount <= 0) throw new Exception('Amount must be greater than $0.00');
		if($amount > $transaction->getAmount()) throw new Exception(sprintf("Amount must be less than or equal to %s", $transaction->getAmount(TRUE)));
		
		// Set Item
		$item = match ($transaction->getTableName()) {
			'photographer_packages' => Secrets\Photographers\Package::Init($transaction->getTableId()),
			default                 => NULL
		};
		
		// Check Item
		if(is_null($item)) throw new Exception('Item not found in the database. Please try again or refresh your page.');
		
		// Init MobiusPay Client
		$mobiusPayClient = new MobiusPay\Client();
		
		// Check Sandbox
		if($mobiusPayClient->isSandbox()) $to_email = DEV_EMAIL;
		
		// Set Transaction ID
		$mobiusPayClient->setTransactionId($transaction->getTransactionId());
		
		// Process Transaction
		$mobiusPayClient->setType(MobiusPay\Client::TYPE_CAPTURE)->doTransaction($amount);
		
		// Record Transaction Details
		$form_values = $mobiusPayClient->getTransactionDetails(array(
			// Record Billing Details
			'billing_first_name'      => $transaction->getBillingFirstName(),
			'billing_last_name'       => $transaction->getBillingLastName(),
			'billing_email'           => $transaction->getBillingEmail(),
			'billing_phone'           => $transaction->getBillingPhone(),
			'billing_fax'             => $transaction->getBillingFax(),
			'billing_company'         => $transaction->getBillingCompany(),
			'billing_address_line_1'  => $transaction->getBillingAddressLine1(),
			'billing_address_line_2'  => $transaction->getBillingAddressLine2(),
			'billing_city'            => $transaction->getBillingCity(),
			'billing_state'           => $transaction->getBillingState(),
			'billing_zip_code'        => $transaction->getBillingZipCode(),
			'billing_country'         => $transaction->getBillingCountry(),
			
			// Record Shipping Details
			'shipping_first_name'     => $transaction->getShippingFirstName(),
			'shipping_last_name'      => $transaction->getShippingLastName(),
			'shipping_company'        => $transaction->getShippingCompany(),
			'shipping_address_line_1' => $transaction->getShippingAddressLine1(),
			'shipping_address_line_2' => $transaction->getShippingAddressLine2(),
			'shipping_city'           => $transaction->getShippingCity(),
			'shipping_state'          => $transaction->getShippingState(),
			'shipping_zip_code'       => $transaction->getShippingZipCode(),
			'shipping_country'        => $transaction->getShippingCountry(),
			'shipping_email'          => $transaction->getShippingEmail(),
			
			// Record Basic Data
			'merchant'                => $transaction->getMerchant(),
			'form'                    => $transaction->getForm(),
			'type'                    => $mobiusPayClient->getType(),
			'comments'                => $transaction->getComments(),
			'captcha'                 => filter_input(INPUT_POST, 'captcha'),
			'user_agent'              => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'              => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			
			// Record Sale Data
			'table_name'              => $transaction->getTableName(),
			'table_id'                => $transaction->getTableId(),
			'ref_transaction_id'      => $transaction->getTransactionId(),
			'amount'                  => $mobiusPayClient->getAmount(),
			'payment_status'          => $mobiusPayClient->getPaymentStatus(),
			'transaction_id'          => $mobiusPayClient->getTransaction('transactionid'),
			'account_number'          => $transaction->getAccountNumber(),
			'account_type'            => $transaction->getAccountType(),
			'invoice'                 => $transaction->getInvoice(),
			
			// Record Errors
			'response'                => $mobiusPayClient->getTransaction('response'),
			'response_code'           => $mobiusPayClient->getTransaction('response_code'),
			'response_text'           => $mobiusPayClient->getTransaction('responsetext'),
		));
		
		// Update Database
		Database::ArrayInsert('transactions', $form_values, TRUE);
		
		// Log Action
		$admin->log(
			type       : Types\Log::CAPTURE,
			table_name : Tables\Website::TRANSACTIONS,
			table_id   : $transaction->getId(),
			payload    : $_POST
		);
		
		// Switch Response
		switch($mobiusPayClient->getTransaction('response')) {
			case MobiusPay\Client::RESPONSE_APPROVED:
				// Email Owner
				$mailer = new Mailer(TRUE);
				$mailer->setAdmin(TRUE);
				$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
				$mailer->addAddress($to_email, SITE_NAME);
				$mailer->setSubject($subjects['success']);
				$mailer->setBgColor('#28a745');
				$mailer->setBody(sprintf("admin/items/transactions/forms/%s/capture/notifications/success.twig", $transaction->getForm()), match ($transaction->getForm()) {
					'photographer-package-booking' => array(
						'heading'        => $subjects['success'],
						'invoice'        => $form_values['invoice'],
						'amount'         => $form_values['amount'],
						'account_number' => $form_values['account_number'],
						'billing'        => array(
							'name'           => sprintf("%s %s", $form_values['billing_first_name'], $form_values['billing_last_name']),
							'phone'          => $form_values['billing_phone'],
							'email'          => $form_values['billing_email'],
							'address_line_1' => $form_values['billing_address_line_1'],
							'address_line_2' => $form_values['billing_address_line_2'],
							'city'           => $form_values['billing_city'],
							'state'          => MobiusPay\Client::FormOptions('states', $form_values['billing_country'], $form_values['billing_state']),
							'country'        => MobiusPay\Client::FormOptions('countries', NULL, $form_values['billing_country']),
							'zip_code'       => $form_values['billing_zip_code']
						),
						'package'        => array(
							'photographer' => $item->getPhotographer()?->getName(),
							'name'         => $item->getHeading(),
							'description'  => $item->getContent(180),
						),
						'comments'       => nl2br($form_values['comments'])
					),
					default                        => array(
						'heading'        => $subjects['success'],
						'invoice'        => $form_values['invoice'],
						'amount'         => $form_values['amount'],
						'account_number' => $form_values['account_number'],
						'billing'        => array(
							'name'           => sprintf("%s %s", $form_values['billing_first_name'], $form_values['billing_last_name']),
							'phone'          => $form_values['billing_phone'],
							'email'          => $form_values['billing_email'],
							'address_line_1' => $form_values['billing_address_line_1'],
							'address_line_2' => $form_values['billing_address_line_2'],
							'city'           => $form_values['billing_city'],
							'state'          => MobiusPay\Client::FormOptions('states', $form_values['billing_country'], $form_values['billing_state']),
							'country'        => MobiusPay\Client::FormOptions('countries', NULL, $form_values['billing_country']),
							'zip_code'       => $form_values['billing_zip_code']
						),
						'comments'       => nl2br($form_values['comments'])
					)
				})->send();
				
				// Check Email Receipt
				if(filter_input(INPUT_POST, 'email_receipt', FILTER_VALIDATE_INT)) {
					// Email Receipt
					$mailer = new Mailer(TRUE);
					$mailer->setAdmin(FALSE);
					$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
					$mailer->addAddress($form_values['billing_email']);
					$mailer->setSubject($subjects['receipt']);
					$mailer->setBody(sprintf("admin/items/transactions/forms/%s/capture/receipt.twig", $transaction->getForm()), match ($transaction->getForm()) {
						'photographer-package-booking' => array(
							'heading'        => $subjects['receipt'],
							'invoice'        => $form_values['invoice'],
							'amount'         => $form_values['amount'],
							'account_number' => $form_values['account_number'],
							'billing'        => array(
								'name'           => sprintf("%s %s", $form_values['billing_first_name'], $form_values['billing_last_name']),
								'phone'          => $form_values['billing_phone'],
								'email'          => $form_values['billing_email'],
								'address_line_1' => $form_values['billing_address_line_1'],
								'address_line_2' => $form_values['billing_address_line_2'],
								'city'           => $form_values['billing_city'],
								'state'          => MobiusPay\Client::FormOptions('states', $form_values['billing_country'], $form_values['billing_state']),
								'country'        => MobiusPay\Client::FormOptions('countries', NULL, $form_values['billing_country']),
								'zip_code'       => $form_values['billing_zip_code']
							),
							'package'        => array(
								'photographer' => $item->getPhotographer()?->getName(),
								'name'         => $item->getHeading(),
								'description'  => $item->getContent(180),
							),
							'comments'       => nl2br($form_values['comments']),
							'errors'         => array(
								'status'        => $form_values['payment_status'],
								'response'      => $form_values['response'],
								'response_code' => $form_values['response_code'],
								'response_text' => $form_values['response_text']
							)
						),
						default                        => array(
							'heading'        => $subjects['receipt'],
							'invoice'        => $form_values['invoice'],
							'amount'         => $form_values['amount'],
							'account_number' => $form_values['account_number'],
							'billing'        => array(
								'name'           => sprintf("%s %s", $form_values['billing_first_name'], $form_values['billing_last_name']),
								'phone'          => $form_values['billing_phone'],
								'email'          => $form_values['billing_email'],
								'address_line_1' => $form_values['billing_address_line_1'],
								'address_line_2' => $form_values['billing_address_line_2'],
								'city'           => $form_values['billing_city'],
								'state'          => MobiusPay\Client::FormOptions('states', $form_values['billing_country'], $form_values['billing_state']),
								'country'        => MobiusPay\Client::FormOptions('countries', NULL, $form_values['billing_country']),
								'zip_code'       => $form_values['billing_zip_code']
							),
							'comments'       => nl2br($form_values['comments']),
							'errors'         => array(
								'status'        => $form_values['payment_status'],
								'response'      => $form_values['response'],
								'response_code' => $form_values['response_code'],
								'response_text' => $form_values['response_text']
							)
						)
					})->send();
				}
				
				// Set Message
				Admin\SetMessage('Captured transaction successfully.', 'success');
				
				// Set Response
				$json_response = array(
					'status'   => 'success',
					'message'  => Admin\GetMessage(),
					'table_id' => $transaction->getId()
				);
				break;
			case MobiusPay\Client::RESPONSE_DECLINED:
			case MobiusPay\Client::RESPONSE_ERROR:
				// Email Owner
				$mailer = new Mailer(TRUE);
				$mailer->setAdmin(TRUE);
				$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
				$mailer->addAddress($to_email, SITE_NAME);
				$mailer->setSubject($subjects['failure']);
				$mailer->setBgColor('#dc3545');
				$mailer->setBody(sprintf("admin/items/transactions/forms/%s/capture/notifications/declined.twig", $transaction->getForm()), match ($transaction->getForm()) {
					'photographer-package-booking' => array(
						'heading'        => $subjects['failure'],
						'invoice'        => $form_values['invoice'],
						'amount'         => $form_values['amount'],
						'account_number' => $form_values['account_number'],
						'billing'        => array(
							'name'           => sprintf("%s %s", $form_values['billing_first_name'], $form_values['billing_last_name']),
							'phone'          => $form_values['billing_phone'],
							'email'          => $form_values['billing_email'],
							'address_line_1' => $form_values['billing_address_line_1'],
							'address_line_2' => $form_values['billing_address_line_2'],
							'city'           => $form_values['billing_city'],
							'state'          => MobiusPay\Client::FormOptions('states', $form_values['billing_country'], $form_values['billing_state']),
							'country'        => MobiusPay\Client::FormOptions('countries', NULL, $form_values['billing_country']),
							'zip_code'       => $form_values['billing_zip_code']
						),
						'package'        => array(
							'photographer' => $item->getPhotographer()?->getName(),
							'name'         => $item->getHeading(),
							'description'  => $item->getContent(180),
						),
						'comments'       => nl2br($form_values['comments']),
					),
					default                        => array(
						'heading'        => $subjects['failure'],
						'invoice'        => $form_values['invoice'],
						'amount'         => $form_values['amount'],
						'account_number' => $form_values['account_number'],
						'billing'        => array(
							'name'           => sprintf("%s %s", $form_values['billing_first_name'], $form_values['billing_last_name']),
							'phone'          => $form_values['billing_phone'],
							'email'          => $form_values['billing_email'],
							'address_line_1' => $form_values['billing_address_line_1'],
							'address_line_2' => $form_values['billing_address_line_2'],
							'city'           => $form_values['billing_city'],
							'state'          => MobiusPay\Client::FormOptions('states', $form_values['billing_country'], $form_values['billing_state']),
							'country'        => MobiusPay\Client::FormOptions('countries', NULL, $form_values['billing_country']),
							'zip_code'       => $form_values['billing_zip_code']
						),
						'comments'       => nl2br($form_values['comments']),
					)
				})->send();
				
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
			'message' => Debug::Exception($exception)
		);
	}
	
	// Output Response
	echo json_encode($json_response);