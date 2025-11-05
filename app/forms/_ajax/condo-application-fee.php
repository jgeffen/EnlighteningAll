<?php
	/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var null|Membership   $member
	 */
	
	// Imports
	use PHPMailer\PHPMailer\Exception as PHPMailerException;
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	
	try {
		// Variable Defaults
		$to_email = SITE_EMAIL;
		$subjects = array(
			'success' => sprintf("Condo Application Fee Success - %s", SITE_NAME),
			'failure' => sprintf("Condo Application Fee Failure - %s", SITE_NAME),
			'receipt' => sprintf("Condo Application Fee Receipt - %s", SITE_NAME)
		);
		
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
				'cc_type'          => FILTER_DEFAULT,
				'cc_number'        => FILTER_DEFAULT,
				'cc_expiry_month'  => FILTER_DEFAULT,
				'cc_expiry_year'   => FILTER_DEFAULT,
				'cc_cvv'           => FILTER_DEFAULT,
				'captcha'          => array(FILTER_CALLBACK, array('options' => 'verifyHash'))
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
		
		// Calculate Amount
		$amount = 150;
		
		// Calculate Tax
		$tax = 0;
		
		// Set Description
		$description = 'Condo Application Fee';
		
		// Init MobiusPay Client
		$mobiusPayClient = new MobiusPay\Client();
		
		// Check Sandbox
		if($mobiusPayClient->isSandbox()) $to_email = DEV_EMAIL;
		
		// Set Credit Card
		$mobiusPayClient->setCreditCard(
			account    : filter_input(INPUT_POST, 'cc_number'),
			type       : filter_input(INPUT_POST, 'cc_type'),
			expiration : filter_input(INPUT_POST, 'cc_expiry_month') . filter_input(INPUT_POST, 'cc_expiry_year'),
			cvv        : filter_input(INPUT_POST, 'cc_cvv')
		);
		
		// Set Billing
		$mobiusPayClient->setBilling(array(
			'first_name'     => filter_input(INPUT_POST, 'first_name'),
			'last_name'      => filter_input(INPUT_POST, 'last_name'),
			'email'          => filter_input(INPUT_POST, 'email'),
			'phone'          => filter_input(INPUT_POST, 'phone'),
			'fax'            => filter_input(INPUT_POST, 'fax'),
			'company'        => filter_input(INPUT_POST, 'company'),
			'address_line_1' => filter_input(INPUT_POST, 'address_line_1'),
			'address_line_2' => filter_input(INPUT_POST, 'address_line_2'),
			'city'           => filter_input(INPUT_POST, 'address_city'),
			'state'          => filter_input(INPUT_POST, 'address_state'),
			'zip_code'       => filter_input(INPUT_POST, 'address_zip_code'),
			'country'        => filter_input(INPUT_POST, 'address_country')
		));
		
		// Set Order
		$mobiusPayClient->setOrder(array(
			'ip_address'  => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'description' => $description,
			'comments'    => filter_input(INPUT_POST, 'comments'),
			'tax'         => $tax
		));
		
		// Process Transaction
		$mobiusPayClient->setType(MobiusPay\Client::TYPE_SALE)->doTransaction($amount);
		
		// Record Transaction Details
		$form_values = $mobiusPayClient->getTransactionDetails(array(
			// Record Billing Details
			'billing_first_name'      => $mobiusPayClient->getBilling('first_name'),
			'billing_last_name'       => $mobiusPayClient->getBilling('last_name'),
			'billing_email'           => $mobiusPayClient->getBilling('email'),
			'billing_phone'           => $mobiusPayClient->getBilling('phone'),
			'billing_fax'             => $mobiusPayClient->getBilling('fax'),
			'billing_company'         => $mobiusPayClient->getBilling('company'),
			'billing_address_line_1'  => $mobiusPayClient->getBilling('address_line_1'),
			'billing_address_line_2'  => $mobiusPayClient->getBilling('address_line_2'),
			'billing_city'            => $mobiusPayClient->getBilling('city'),
			'billing_state'           => $mobiusPayClient->getBilling('state'),
			'billing_zip_code'        => $mobiusPayClient->getBilling('zip_code'),
			'billing_country'         => $mobiusPayClient->getBilling('country'),
			
			// Record Shipping Details
			'shipping_first_name'     => $mobiusPayClient->getShipping('first_name'),
			'shipping_last_name'      => $mobiusPayClient->getShipping('last_name'),
			'shipping_company'        => $mobiusPayClient->getShipping('company'),
			'shipping_address_line_1' => $mobiusPayClient->getShipping('address_line_1'),
			'shipping_address_line_2' => $mobiusPayClient->getShipping('address_line_2'),
			'shipping_city'           => $mobiusPayClient->getShipping('city'),
			'shipping_state'          => $mobiusPayClient->getShipping('state'),
			'shipping_zip_code'       => $mobiusPayClient->getShipping('zip_code'),
			'shipping_country'        => $mobiusPayClient->getShipping('country'),
			'shipping_email'          => $mobiusPayClient->getShipping('email'),
			
			// Record Basic Data
			'member_id'               => $member?->getId(),
			'merchant'                => MobiusPay\Client::MERCHANT_NAME,
			'form'                    => pathinfo(__FILE__, PATHINFO_FILENAME),
			'type'                    => $mobiusPayClient->getType(),
			'comments'                => $mobiusPayClient->getOrder('comments'),
			'captcha'                 => filter_input(INPUT_POST, 'captcha'),
			'user_agent'              => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'              => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'               => date('Y-m-d H:i:s'),
			
			// Record Sale Data
			'amount'                  => $mobiusPayClient->getAmount(),
			'sales_tax'               => $mobiusPayClient->getOrder('tax'),
			'payment_status'          => $mobiusPayClient->getPaymentStatus(),
			'transaction_id'          => $mobiusPayClient->getTransaction('transactionid'),
			'account_number'          => $mobiusPayClient->getAccountNumber(),
			'account_type'            => $mobiusPayClient->getAccountType(),
			'invoice'                 => MobiusPay\Client::GenerateInvoice('SFL', ...$mobiusPayClient->getBilling()),
			
			// Record Errors
			'response'                => $mobiusPayClient->getTransaction('response'),
			'response_code'           => $mobiusPayClient->getTransaction('response_code'),
			'response_text'           => $mobiusPayClient->getTransaction('responsetext'),
		));
		
		// Update Database
		$transaction_id = Database::ArrayInsert('transactions', $form_values, TRUE);
		
		// Init PHP Mailer
		$phpMailer = new PHPMailer(TRUE);
		$phpMailer->setFrom(SMTP_AUTH ? SMTP_USER : SITE_EMAIL, SITE_COMPANY);
		
		// Check SMTP
		if(SMTP_AUTH) {
			$phpMailer->isSMTP();
			$phpMailer->Host       = SMTP_HOST;
			$phpMailer->SMTPAuth   = SMTP_AUTH;
			$phpMailer->Username   = SMTP_USER;
			$phpMailer->Password   = SMTP_PASS;
			$phpMailer->SMTPAuth   = TRUE;
			$phpMailer->SMTPDebug  = SMTP::DEBUG_OFF;
			$phpMailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$phpMailer->Port       = SMTP_PORT;
		}
		
		// Switch Response
		switch($mobiusPayClient->getTransaction('response')) {
			case MobiusPay\Client::RESPONSE_APPROVED:
				// Email Owner
				$mailer = new Mailer(TRUE);
				$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
				$mailer->addAddress($to_email, SITE_COMPANY);
				$mailer->setSubject($subjects['success']);
				$mailer->setAdmin(TRUE);
				$mailer->setBgColor('#198754');
				$mailer->setBody('forms/condo-application-fee/notifications/success.twig', array(
					'sale'     => array(
						'invoice' => $form_values['invoice'],
						'amount'  => Helpers::FormatCurrency($form_values['amount']),
						'tax'     => Helpers::FormatCurrency($form_values['sales_tax']),
						'account' => $form_values['account_number']
					),
					'billing'  => array(
						'name'           => sprintf("%s, %s", $form_values['billing_last_name'], $form_values['billing_first_name']),
						'phone'          => $form_values['billing_phone'],
						'email'          => $form_values['billing_email'],
						'address_line_1' => $form_values['billing_address_line_1'],
						'address_line_2' => $form_values['billing_address_line_2'],
						'city'           => $form_values['billing_city'],
						'state'          => $form_values['billing_state'],
						'country'        => $form_values['billing_country'],
						'zip_code'       => $form_values['billing_zip_code']
					),
					'comments' => $form_values['comments']
				))->send();
				
				// Email Receipt
				$mailer = new Mailer(TRUE);
				$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
				$mailer->addAddress($form_values['billing_email']);
				$mailer->setSubject($subjects['receipt']);
				$mailer->setAdmin(FALSE);
				$mailer->setBgColor('#198754');
				$mailer->setBody('forms/condo-application-fee/notifications/receipt.twig', array(
					'sale'     => array(
						'invoice' => $form_values['invoice'],
						'amount'  => Helpers::FormatCurrency($form_values['amount']),
						'tax'     => Helpers::FormatCurrency($form_values['sales_tax']),
						'account' => $form_values['account_number']
					),
					'billing'  => array(
						'name'           => sprintf("%s, %s", $form_values['billing_last_name'], $form_values['billing_first_name']),
						'phone'          => $form_values['billing_phone'],
						'email'          => $form_values['billing_email'],
						'address_line_1' => $form_values['billing_address_line_1'],
						'address_line_2' => $form_values['billing_address_line_2'],
						'city'           => $form_values['billing_city'],
						'state'          => $form_values['billing_state'],
						'country'        => $form_values['billing_country'],
						'zip_code'       => $form_values['billing_zip_code']
					),
					'comments' => $form_values['comments']
				))->send();
				
				// Set Response
				$json_response = array(
					'status'         => 'success',
					'transaction_id' => $mobiusPayClient->getTransaction('transactionid'),
					'html'           => Render::GetTemplate('forms/condo-application-fee/success.twig')
				);
				break;
			case MobiusPay\Client::RESPONSE_DECLINED:
			case MobiusPay\Client::RESPONSE_ERROR:
				// Email Owner
				$mailer = new Mailer(TRUE);
				$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
				$mailer->addAddress($to_email, SITE_COMPANY);
				$mailer->setSubject($subjects['failure']);
				$mailer->setAdmin(TRUE);
				$mailer->setBgColor('#dc3545');
				$mailer->setBody('forms/condo-application-fee/notifications/declined.twig', array(
					'sale'     => array(
						'invoice' => $form_values['invoice'],
						'amount'  => Helpers::FormatCurrency($form_values['amount']),
						'tax'     => Helpers::FormatCurrency($form_values['sales_tax']),
						'account' => $form_values['account_number']
					),
					'billing'  => array(
						'name'           => sprintf("%s, %s", $form_values['billing_last_name'], $form_values['billing_first_name']),
						'phone'          => $form_values['billing_phone'],
						'email'          => $form_values['billing_email'],
						'address_line_1' => $form_values['billing_address_line_1'],
						'address_line_2' => $form_values['billing_address_line_2'],
						'city'           => $form_values['billing_city'],
						'state'          => $form_values['billing_state'],
						'country'        => $form_values['billing_country'],
						'zip_code'       => $form_values['billing_zip_code']
					),
					'comments' => $form_values['comments'],
					'payment'  => array(
						'status'        => $form_values['payment_status'],
						'response'      => $form_values['response'],
						'response_code' => $form_values['response_code'],
						'response_text' => $form_values['response_text']
					)
				))->send();
				
				// Set Response
				$json_response = array(
					'status'         => 'error',
					'transaction_id' => $mobiusPayClient->getTransaction('transactionid'),
					'message'        => sprintf("%s: %s", $form_values['response_code'], $form_values['response_text'])
				);
				break;
			default:
				// Set Response
				$json_response = array(
					'status'         => 'error',
					'transaction_id' => $mobiusPayClient->getTransaction('transactionid'),
					'message'        => 'An unknown error has occurred. Please refresh the page.'
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
			'status' => 'error',
			
			'message' => Debug::Exception($exception)
		);
	} catch(Exception|PHPMailerException $exception) {
		// Set Response
		$json_response = array(
			'status' => 'error',
			
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);