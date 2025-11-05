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
	
	// Imports
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	
	try {
		// Variable Defaults
		$to_email = SITE_EMAIL;
		$subjects = array(
			'success' => sprintf("Booking Authorization Success - %s", SITE_NAME),
			'failure' => sprintf("Booking Authorization Failure - %s", SITE_NAME),
			'receipt' => sprintf("Booking Authorization Receipt - %s", SITE_NAME)
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
		
		// Fetch/Set Item
		$page = Database::Action("SELECT `photographers`.`name` AS `photographer`, `photographer_packages`.* FROM `photographer_packages` JOIN `photographers` ON (`photographers`.`published` = :published AND `photographers`.`id` = `photographer_packages`.`photographer_id`) WHERE `photographer_packages`.`published` = :published AND `photographer_packages`.`id` = :id", array(
			'published' => 1,
			'id'        => filter_input(INPUT_POST, 'photographer_packages_id', FILTER_VALIDATE_INT)
		))->fetch(PDO::FETCH_ASSOC);
		
		// Check Item
		if(empty($page)) throw new Exception('Package not found in the database. Please try again or refresh your page.');
		
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
			'description' => sprintf("Photographer Package: %s (%s)", $page->getHeading(), $page['photographer']),
			'comments'    => filter_input(INPUT_POST, 'comments')
		));
		
		// Process Transaction
		$mobiusPayClient->setType(MobiusPay\Client::TYPE_AUTH)->doTransaction($page['price']);
		
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
			'merchant'                => MobiusPay\Client::MERCHANT_NAME,
			'form'                    => pathinfo(__FILE__, PATHINFO_FILENAME),
			'type'                    => $mobiusPayClient->getType(),
			'comments'                => $mobiusPayClient->getOrder('comments'),
			'captcha'                 => filter_input(INPUT_POST, 'captcha'),
			'user_agent'              => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'              => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'               => date('Y-m-d H:i:s'),
			
			// Record Sale Data
			'table_name'              => 'photographer_packages',
			'table_id'                => $page['id'],
			'amount'                  => $mobiusPayClient->getAmount(),
			'payment_status'          => $mobiusPayClient->getPaymentStatus(),
			'transaction_id'          => $mobiusPayClient->getTransaction('transactionid'),
			'account_number'          => $mobiusPayClient->getAccountNumber(),
			'account_type'            => $mobiusPayClient->getAccountType(),
			'invoice'                 => MobiusPay\Client::GenerateInvoice('SFL', $page['id'], ...$mobiusPayClient->getBilling()),
			
			// Record Errors
			'response'                => $mobiusPayClient->getTransaction('response'),
			'response_code'           => $mobiusPayClient->getTransaction('response_code'),
			'response_text'           => $mobiusPayClient->getTransaction('responsetext'),
		));
		
		// Update Database
		Database::ArrayInsert('transactions', $form_values, TRUE);
		
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
				$phpMailer->addAddress($to_email, SITE_COMPANY);
				$phpMailer->isHTML();
				$phpMailer->Subject = $subjects['success'];
				$phpMailer->Body    = include('html/notifications/success.php');
				$phpMailer->AltBody = strip_tags($phpMailer->Body);
				$phpMailer->send();
				
				// Clear Recipients
				$phpMailer->clearAllRecipients();
				
				// Email Receipt
				$phpMailer->addAddress($form_values['billing_email']);
				$phpMailer->isHTML();
				$phpMailer->Subject = $subjects['receipt'];
				$phpMailer->Body    = include('html/receipt.php');
				$phpMailer->AltBody = strip_tags($phpMailer->Body);
				$phpMailer->send();
				
				// Set Response
				$json_response = array(
					'status' => 'success',
					'html'   => include('html/success.php')
				);
				break;
			case MobiusPay\Client::RESPONSE_DECLINED:
			case MobiusPay\Client::RESPONSE_ERROR:
				// Email Owner
				$phpMailer->addAddress($to_email, SITE_COMPANY);
				$phpMailer->isHTML();
				$phpMailer->Subject = $subjects['failure'];
				$phpMailer->Body    = include('html/notifications/declined.php');
				$phpMailer->AltBody = strip_tags($phpMailer->Body);
				$phpMailer->send();
				
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
	
	// Output Response
	echo json_encode($json_response);