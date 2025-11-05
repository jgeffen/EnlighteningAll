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
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var null|Membership   $member
	 */
	
	// Imports
	use PHPMailer\PHPMailer\Exception as PHPMailerException;
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	
	try {
		// Set Packages
		$product_id       = filter_input(INPUT_POST, 'product_id');
		$product_quantity = filter_input(INPUT_POST, 'product_quantity') ?? 1;
		$product          = Items\Product::Init($product_id);
		$to_email         = SITE_EMAIL;
		$subjects         = array(
			'success' => sprintf("Product Sale Success - %s", SITE_NAME),
			'failure' => sprintf("Product Sale Failure - %s", SITE_NAME),
			'receipt' => sprintf("Product Sale Receipt - %s", SITE_NAME)
		);
		
		// Check Wallet
		if(!$member->wallet()) throw new Exception('No method of payment found for this account.');
		
		// Check Expiration
		if($member->wallet()->isExpired()) throw new Exception('Credit card has expired.');
		
		// Init AuthNet Vault
		$vault = new AuthorizeNet\CIM\Client();
		
		// Calculate base price
		$base_price = $product->getPrice() * $product_quantity;
		
		// Final amount with tax
		$amount = round($base_price + $product->getSalesTax(), 2);
		
		if($member->wallet()->getPoints() >= $amount) {
			// Deduct points
			$member->wallet()->deductPoints($amount);
			
			// Record transaction directly
			$form_values = array(
				'member_id'      => $member->getId(),
				'member_name'    => $member->getFullName(),
				'type'           => 'points',
				'merchant'       => 'Members Points',
				'form'           => pathinfo(__FILE__, PATHINFO_FILENAME),
				'table_name'     => 'products',
				'table_id'       => $product->getId(),
				'product'        => $product->getLabel(),
				'product_id'     => $product->getId(),
				'quantity'       => $product_quantity,
				'points_used'    => $amount,
				'amount'         => $amount,
				'sales_tax'      => $product->getSalesTax(),
				'payment_status' => 'Approved',
				'transaction_id' => sprintf("POINTS-%d-%s", $member->getId(), time()),
				'user_agent'     => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'     => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
				'timestamp'      => date('Y-m-d H:i:s')
			);
			
			$transaction_id = Database::ArrayInsert('transactions', $form_values, TRUE);
			
			$product->removeUPC(filter_input(INPUT_POST, 'upc_code'));
			$product->decreaseStock($product_quantity);
			// Email Owner
			$mailer = new Mailer(TRUE);
			$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
			$mailer->addAddress($to_email, SITE_COMPANY);
			$mailer->setSubject($subjects['success']);
			$mailer->setAdmin(TRUE);
			$mailer->setBgColor('#198754');
			$mailer->setBody('products/purchase/notifications/success.twig', array(
				'sale'           => array(
					'amount' => Helpers::FormatCurrency($form_values['amount']),
					'tax'    => Helpers::FormatCurrency($form_values['sales_tax'])
				),
				'product'        => array(
					'label'    => $form_values['product'],
					'id'       => $form_values['product_id'],
					'quantity' => $form_values['quantity'],
				),
				'transaction_id' => $transaction_id
			))->send();
			
			// Email Receipt
			$mailer = new Mailer(TRUE);
			$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
			$mailer->addAddress($member->getEmail());
			$mailer->setSubject($subjects['receipt']);
			$mailer->setAdmin(FALSE);
			$mailer->setBgColor('#d8bb66');
			$mailer->setBody('products/purchase/notifications/receipt.twig', array(
				'sale'           => array(
					'amount' => Helpers::FormatCurrency($form_values['amount']),
					'tax'    => Helpers::FormatCurrency($form_values['sales_tax'])
				),
				'product'        => array(
					'label'    => $form_values['product'],
					'id'       => $form_values['product_id'],
					'quantity' => $form_values['quantity'],
				),
				'transaction_id' => $transaction_id
			))->send();
			
			// Set Response
			$json_response = array(
				'status'         => 'success',
				'transaction_id' => $transaction_id,
				'html'           => Render::GetTemplate('products/purchase/success.twig')
			);
			
			echo json_encode($json_response);
			exit;
		}
		
		// Set Order
		$vault->setOrder(
			amount            : $amount,
			description       : sprintf("Product Purchase: %s (%s)", $product->getLabel(), $member->getFullName()),
			id                : $product->getId(),
			ip_address        : filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			po_number         : NULL,
			shipping          : 0.00,
			tax               : $product->getSalesTax(),
			discount          : $product->getDiscount() ?: 0,
			comments          : filter_input(INPUT_POST, 'comments') ?? '',
			invoice           : $vault->getInvoice('AN', ...$vault->getBilling()->toArray()),
			customer_vault_id : $member->wallet()->getCustomerVaultId(),
			billing_id        : $member->wallet()->getBillingId(),
		);
		
		// Process Transaction
		$vault->setType(AuthorizeNet\CIM\Client::TYPE_PURCHASE)->doTransaction($amount);
		
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
			'member_name'            => $member->getFullName(),
			'merchant'               => AuthorizeNet\CIM\Client::MERCHANT_NAME,
			'form'                   => pathinfo(__FILE__, PATHINFO_FILENAME),
			'type'                   => $vault->getType(),
			'comments'               => $vault->getOrder()->getComments() ?? '',
			'user_agent'             => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'             => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			
			// Record Vault Data
			'customer_vault_id'      => $vault->getTransaction()->getCustomerVaultId(),
			'billing_id'             => $member->wallet()->getBillingId(),
			'product_id'             => $vault->getOrder()->getId(),
			'quantity'               => $product_quantity,
			
			// Record Sale Data
			'table_name'             => 'products',
			'table_id'               => $vault->getOrder()->getId(),
			'product'                => $product->getLabel(),
			'payment_status'         => $vault->getTransaction()->getPaymentStatus(),
			'transaction_id'         => $vault->getTransaction()->getTransId(),
			'amount'                 => $vault->getOrder()->getAmount(),
			'sales_tax'              => $vault->getOrder()->getTax(),
			'discount'               => $vault->getOrder()->getDiscount(),
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
		if($vault->getCimResponse()->isSuccessful()) {
			$product->removeUPC(filter_input(INPUT_POST, 'upc_code'));
			$product->decreaseStock($product_quantity);
			// Email Owner
			$mailer = new Mailer(TRUE);
			$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
			$mailer->addAddress($to_email, SITE_COMPANY);
			$mailer->setSubject($subjects['success']);
			$mailer->setAdmin(TRUE);
			$mailer->setBgColor('#198754');
			$mailer->setBody('products/purchase/notifications/success.twig', array(
				'sale'           => array(
					'invoice' => $form_values['invoice'],
					'amount'  => Helpers::FormatCurrency($form_values['amount']),
					'tax'     => Helpers::FormatCurrency($form_values['sales_tax']),
					'account' => $form_values['account_number']
				),
				'billing'        => array(
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
				'product'        => array(
					'label'    => $form_values['product'],
					'id'       => $form_values['product_id'],
					'quantity' => $form_values['quantity'],
				),
				'transaction_id' => $transaction_id,
				'comments'       => $form_values['comments']
			))->send();
			
			// Email Receipt
			$mailer = new Mailer(TRUE);
			$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
			$mailer->addAddress($form_values['billing_email']);
			$mailer->setSubject($subjects['receipt']);
			$mailer->setAdmin(FALSE);
			$mailer->setBgColor('#d8bb66');
			$mailer->setBody('products/purchase/notifications/receipt.twig', array(
				'sale'           => array(
					'invoice' => $form_values['invoice'],
					'amount'  => Helpers::FormatCurrency($form_values['amount']),
					'tax'     => Helpers::FormatCurrency($form_values['sales_tax']),
					'account' => $form_values['account_number']
				),
				'billing'        => array(
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
				'product'        => array(
					'label'    => $form_values['product'],
					'id'       => $form_values['product_id'],
					'quantity' => $form_values['quantity'],
				),
				'transaction_id' => $transaction_id,
				'comments'       => $form_values['comments']
			))->send();
			
			// Set Response
			$json_response = array(
				'status'         => 'success',
				'transaction_id' => $vault->getTransaction()->getTransId(),
				'html'           => Render::GetTemplate('products/purchase/success.twig')
			);
		} else {
			// Email Owner
			$mailer = new Mailer(TRUE);
			$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
			$mailer->addAddress($to_email, SITE_COMPANY);
			$mailer->setSubject($subjects['failure']);
			$mailer->setAdmin(TRUE);
			$mailer->setBgColor('#dc3545');
			$mailer->setBody('products/purchase/notifications/declined.twig', array(
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
				'product'  => array(
					'label'    => $form_values['product'],
					'id'       => $form_values['product_id'],
					'quantity' => $form_values['quantity'],
				),
				'comments' => $form_values['comments'],
				'payment'  => array(
					'status'        => $form_values['payment_status'],
					'response'      => $form_values['response'],
					'response_code' => $form_values['response_code']
				)
			))->send();
			
			// Set Response
			$json_response = array(
				'status'         => 'error',
				'transaction_id' => $vault->getTransaction()->getTransId(),
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
			'status'  => 'error',
			'message' => Debug::Exception($exception)
		);
	} catch(Exception|PHPMailerException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);
	