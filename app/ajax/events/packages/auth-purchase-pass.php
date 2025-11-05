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
	 * @var null|Membership   $member
	 */
	
	// Imports
	use AuthorizeNet\AIM\Client;
	use Items\Enums\Statuses;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use PHPMailer\PHPMailer\Exception as PHPMailerException;
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	
	try {
		// Variable Defaults
		$to_email = SITE_EMAIL;
		$subjects = array(
			'success' => sprintf("Purchase Pass Sale Success - %s", SITE_NAME),
			'failure' => sprintf("Purchase Pass Sale Failure - %s", SITE_NAME),
			'receipt' => sprintf("Purchase Pass Sale Receipt - %s", SITE_NAME)
		);
		
		$payment_method = filter_input(INPUT_POST, 'payment_method');
		
		// Validate Form
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'name_on_pass' => FILTER_DEFAULT,
				'first_name'   => FILTER_DEFAULT,
				'last_name'    => FILTER_DEFAULT,
				'phone'        => FILTER_DEFAULT,
				'email'        => FILTER_VALIDATE_EMAIL,
				'captcha'      => array(FILTER_CALLBACK, array('options' => 'verifyHash'))
			);
			
			if(filter_input(INPUT_POST, 'payment_method') !== 'points') {
				// Only require billing + cc if paying by card
				$required = array_merge($required, array(
					'address_line_1'   => FILTER_DEFAULT,
					'address_city'     => FILTER_DEFAULT,
					'address_state'    => FILTER_DEFAULT,
					'address_zip_code' => FILTER_DEFAULT,
					'address_country'  => FILTER_DEFAULT,
					'cc_type'          => FILTER_DEFAULT,
					'cc_number'        => FILTER_DEFAULT,
					'cc_expiry_month'  => FILTER_DEFAULT,
					'cc_expiry_year'   => FILTER_DEFAULT,
					'cc_cvv'           => FILTER_DEFAULT
				));
			}
			
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
		
		// Set Packages
		$packages = filter_input(INPUT_POST, 'event_packages', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		
		// Validate: If any selected package is seatable, it must have a seat selected
		foreach($packages as $package_id => $quantity) {
			$package = Items\Events\Package::Init($package_id);
			
			if($package && $package->isSeatable()) {
				if(!$package->isBogo()) {
					$seat = filter_input(INPUT_POST, 'seat_number');
					if(empty($seat)) {
						$errors[] = sprintf("Please choose a seat from the dropdown menu for: %s", $package->getName());
					}
				} else {
					// Expecting an array for BOGO
					$seats = $_POST['seat_number'] ?? array();
					if(empty($seats) || !is_array($seats)) {
						$errors[] = sprintf("Please choose seats from the dropdown menu for: %s", $package->getName());
					} elseif(count($seats) > 2) {
						$errors[] = sprintf("You may only select up to 2 seats for: %s", $package->getName());
					}
				}
			}
		}
		
		// Re-throw form exception if seat validation failed
		if(!empty($errors)) {
			throw new FormException($errors, 'You are missing required fields.');
		}
		
		// Check Packages
		if(empty($packages)) throw new Exception('You must select a package to continue.');
		
		// Filter Packages
		$packages = array_filter($packages);
		
		// Get discount amount
		$discount = filter_input(INPUT_POST, 'discount') ?: 0.00;
		
		// Fetch/Set Item
		$event = Items\Event::Init(filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT));
		
		// Check Item
		if(!$event?->isPublished() || !$event->isUpcomingEvent()) throw new Exception('Event not found in the database. Please try again or refresh your page.');
		
		// Calculate Amount
		$amount = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
		
		// Process payment with member points
		if($payment_method === 'points') {
			// Make sure member is logged in and has enough points
			if(!$member || $member->wallet()->getPoints() < $amount) {
				throw new Exception('You do not have enough points to complete this purchase.');
			}
			
			// Deduct points
			$member->wallet()->deductPoints($amount);
			
			// Record transaction directly
			$form_values = array(
				'member_id'      => $member->getId(),
				'type'           => 'points',
				'merchant'       => 'Members Points',
				'form'           => pathinfo(__FILE__, PATHINFO_FILENAME),
				'name_on_pass'   => Items\Member::Init(filter_input(INPUT_POST, 'name_on_pass'))->getFullName(),
				'table_name'     => 'events',
				'table_id'       => $event->getId(),
				'discount'       => $discount,
				'event_name'     => $event->getHeading(),
				'amount'         => $amount,
				'payment_status' => 'Approved',
				'transaction_id' => sprintf("POINTS-%d-%s", $member->getId(), time()),
				'user_agent'     => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'     => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
				'timestamp'      => date('Y-m-d H:i:s')
			);
			
			// Insert form values into transactions table
			$transaction_id = Database::ArrayInsert('transactions', $form_values, TRUE);
			
			// Add Reservation(s)
			foreach($packages as $package_id => $quantity) {
				$package = Items\Events\Package::Init($package_id);
				
				// Check if there is assigned seating
				if($package?->isSeatable()) {
					// Get the seat number(s)
					$seat_numbers = filter_input(INPUT_POST, 'seat_number', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
					
					// If not BOGO, seat_number will just be a single value, wrap in array for consistency
					if(!$package->isBogo()) {
						$seat_numbers = array(filter_input(INPUT_POST, 'seat_number'));
					}
					
					// Decrease stock by the total reserved
					$package->decreaseStock(count($seat_numbers));
					
					// Loop through chosen seats and reserve each
					foreach(array_values($seat_numbers) as $i => $seat_number) {
						$package->reserveSeat($seat_number);
						
						Database::Action("INSERT INTO `member_reservations` SET `status` = :status, `member_id` = :member_id, `event_id` = :event_id, `event_package_id` = :event_package_id, `song_selected` = :song_selected,  `seat_selected` = :seat_selected,  `name_on_pass` = :name_on_pass, `phone` = :phone, `item_count` = :item_count, `item_count_total` = :item_count_total, `package_amount` = :package_amount, `package_name` = :package_name, `total_amount` = :total_amount, `total_discount` = :total_discount, `total_paid` = :total_paid, `comments` = :comments, `transaction_id` = :transaction_id, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
							'status'           => Statuses\Reservation::PAID->getValue(),
							'member_id'        => filter_input(INPUT_POST, 'name_on_pass'),
							'event_id'         => $event->getId(),
							'event_package_id' => $package_id,
							'song_selected'    => filter_input(INPUT_POST, 'song_request'),
							'seat_selected'    => $seat_number,
							'name_on_pass'     => Items\Member::Init(filter_input(INPUT_POST, 'name_on_pass'))->getFullName(),
							'phone'            => filter_input(INPUT_POST, 'phone'),
							'item_count'       => $i === 0 ? 2 : 0,
							'item_count_total' => array_sum($packages),
							'package_amount'   => $i === 0 ? $package->getPrice() : 0.00,
							'package_name'     => $package->getName(),
							'total_amount'     => $amount,
							'total_discount'   => $discount,
							'total_paid'       => $i === 0 ? $amount : 0,
							'comments'         => filter_input(INPUT_POST, 'comments'),
							'transaction_id'   => $transaction_id,
							'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
							'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
						));
					}
				} else {
					// Decrease stock
					$package?->decreaseStock(array_sum($packages));
					// Save data and make reservation
					for($i = 1; $i <= $quantity; $i++) {
						Database::Action("INSERT INTO `member_reservations` SET `status` = :status, `member_id` = :member_id, `event_id` = :event_id, `event_package_id` = :event_package_id, `song_selected` = :song_selected, `seat_selected` = :seat_selected, `name_on_pass` = :name_on_pass, `phone` = :phone, `item_count` = :item_count, `item_count_total` = :item_count_total, `package_amount` = :package_amount, `package_name` = :package_name, `total_amount` = :total_amount, `total_discount` = :total_discount, `total_paid` = :total_paid, `comments` = :comments, `transaction_id` = :transaction_id, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
							'status'           => Statuses\Reservation::PAID->getValue(),
							'member_id'        => filter_input(INPUT_POST, 'name_on_pass'),
							'event_id'         => $event->getId(),
							'event_package_id' => $package_id,
							'song_selected'    => filter_input(INPUT_POST, 'song_request'),
							'seat_selected'    => filter_input(INPUT_POST, 'seat_number') ?? NULL,
							'name_on_pass'     => Items\Member::Init(filter_input(INPUT_POST, 'name_on_pass'))->getFullName(),
							'phone'            => filter_input(INPUT_POST, 'phone'),
							'item_count'       => array_sum($packages),
							'item_count_total' => array_sum($packages),
							'package_amount'   => $package?->getPrice(),
							'package_name'     => $package?->getName(),
							'total_amount'     => $amount,
							'total_discount'   => 0,
							'total_paid'       => $amount,
							'comments'         => filter_input(INPUT_POST, 'comments'),
							'transaction_id'   => $transaction_id,
							'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
							'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
						));
					}
				}
			}
			
			// Log Action
			$member?->log()->setData(
				type       : Types\Log::CREATE,
				table_name : Tables\Members::RESERVATIONS
			)->execute();
			
			// Response
			$json_response = array(
				'status'         => 'success',
				'transaction_id' => $form_values['transaction_id'],
				'html'           => Render::GetTemplate('events/purchase-pass/success.twig')
			);
			
			echo json_encode($json_response);
			exit;
		}
		
		// Set Description
		$description = array_filter(array_map(function($quantity, $package_id) {
			return sprintf("%dx Package ID #%d", $quantity, $package_id);
		}, $packages, array_keys($packages)));
		
		// Init AuthNet Client
		$authnetClient = new Client('development');
		
		// Check Sandbox
		if($authnetClient->isSandbox()) $to_email = DEV_EMAIL;
		
		// Set Credit Card
		$authnetClient->setCreditCard(
			account    : filter_input(INPUT_POST, 'cc_number'),
			expiration : filter_input(INPUT_POST, 'cc_expiry_month') . filter_input(INPUT_POST, 'cc_expiry_year'),
			cvv        : filter_input(INPUT_POST, 'cc_cvv')
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
			amount          : $amount,
			description     : sprintf("Event Pass: %s (%s)", $event->getHeading(), implode('|', $description)),
			id              : filter_input(INPUT_POST, 'package_id'),
			ip_address      : filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			po_number       : NULL,
			shipping        : 0.00,
			sales_tax       : 0.00,
			discount        : $discount,
			comments        : filter_input(INPUT_POST, 'comments'),
			invoice         : $authnetClient->getInvoice('AN', ...$authnetClient->getBilling()->toArray()),
			duplicateWindow : 0
		);
		
		// Process Transaction
		$authnetClient->setType(Client::TYPE_PURCHASE)->doTransaction($amount);
		
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
			'merchant'                => Client::MERCHANT_NAME,
			'form'                    => pathinfo(__FILE__, PATHINFO_FILENAME),
			'type'                    => $authnetClient->getType(),
			'comments'                => $authnetClient->getOrder()->getComments(),
			'captcha'                 => filter_input(INPUT_POST, 'captcha'),
			'user_agent'              => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'              => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'               => date('Y-m-d H:i:s'),
			
			// Record Sale Data
			'name_on_pass'            => Items\Member::Init(filter_input(INPUT_POST, 'name_on_pass'))->getFullName(),
			'table_name'              => 'events',
			'table_id'                => $event->getId(),
			'amount'                  => $authnetClient->getAmount(),
			'sales_tax'               => $authnetClient->getOrder()->getSalesTax(),
			'payment_status'          => $authnetClient->getPaymentStatus(),
			'transaction_id'          => $authnetClient->getTransaction()->getTransId(),
			'account_number'          => $authnetClient->getAccountNumber(),
			'account_type'            => $authnetClient->getAccountType(),
			'invoice'                 => Client::GenerateInvoice('AN', $event->getId(), ...$authnetClient->getBilling()->toArray()),
			'expiration_date'         => $authnetClient->getCreditCard()->getExpiration(),
			
			// Record Errors
			'referrer_id'             => !empty($_SESSION['referrer_id'] ?: NULL),
			'response'                => $authnetClient->getTransaction()->getCavvResultCode(),
			'response_code'           => $authnetClient->getTransaction()->getResponseCode(),
			'response_text'           => $authnetClient->getTransaction()->getMessages()
		));
		
		// Update Database
		$transaction_id = Database::ArrayInsert('transactions', $form_values, TRUE);
		
		// Handle referral points
		if(!empty($_SESSION['referrer_id'])) {
			$referrer_id = (int)$_SESSION['referrer_id'];
			
			// Prevent self-referrals
			if($member && $referrer_id !== $member->getId()) {
				// Award 1 point to referrer
				$referrerWallet = Membership::Init($referrer_id);
				$referrerWallet?->wallet()->increasePoints(1.00);
				
				// Log referral
				Database::ArrayInsert('member_referrals', array(
					'referrer_id'    => $referrer_id,
					'referee_id'     => $member?->getId(),
					'event_id'       => $event->getId(),
					'transaction_id' => $transaction_id,
					'points_awarded' => 1.00,
					'timestamp'      => date('Y-m-d H:i:s')
				), TRUE);
			}
			
			// Clear session so it doesn’t trigger again
			unset($_SESSION['referrer_id']);
		}
		
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
		switch($authnetClient->getTransaction()->getResponseCode()) {
			case Client::RESPONSE_OK:
				// Remove Non-Paid RSVP
				Database::Action("DELETE FROM `member_reservations` WHERE `status` = :status AND `member_id` = :member_id AND `event_id` = :event_id", array(
					'status'    => Statuses\Reservation::UNPAID->getValue(),
					'member_id' => $member?->getId(),
					'event_id'  => $event->getId()
				));
				
				// Add Reservation(s)
				foreach($packages as $package_id => $quantity) {
					$package = Items\Events\Package::Init($package_id);
					
					if($package?->isSeatable()) {
						$seat_numbers = filter_input(INPUT_POST, 'seat_number', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
						
						// If not BOGO, seat_number will just be a single value, wrap in array for consistency
						if(!$package->isBogo()) {
							$seat_numbers = array(filter_input(INPUT_POST, 'seat_number'));
						}
						
						// Decrease stock by the total reserved
						$package->decreaseStock(count($seat_numbers));
						
						// Loop through chosen seats and reserve each
						foreach(array_values($seat_numbers) as $i => $seat_number) {
							$package->reserveSeat($seat_number);
							
							Database::Action("INSERT INTO `member_reservations` SET `status` = :status, `member_id` = :member_id, `event_id` = :event_id, `event_package_id` = :event_package_id, `song_selected` = :song_selected,  `seat_selected` = :seat_selected,  `name_on_pass` = :name_on_pass, `phone` = :phone, `item_count` = :item_count, `item_count_total` = :item_count_total, `package_amount` = :package_amount, `package_name` = :package_name, `total_amount` = :total_amount, `total_discount` = :total_discount, `total_paid` = :total_paid, `comments` = :comments, `transaction_id` = :transaction_id, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
								'status'           => Statuses\Reservation::PAID->getValue(),
								'member_id'        => filter_input(INPUT_POST, 'name_on_pass'),
								'event_id'         => $event->getId(),
								'event_package_id' => $package_id,
								'song_selected'    => filter_input(INPUT_POST, 'song_request'),
								'seat_selected'    => $seat_number,
								'name_on_pass'     => Items\Member::Init(filter_input(INPUT_POST, 'name_on_pass'))->getFullName(),
								'phone'            => $authnetClient->getBilling()->getPhone(),
								'item_count'       => $i === 0 ? 2 : 0,
								'item_count_total' => array_sum($packages),
								'package_amount'   => $i === 0 ? $package->getPrice() : 0.00,
								'package_name'     => $package->getName(),
								'total_amount'     => $authnetClient->getAmount(),
								'total_discount'   => $discount,
								'total_paid'       => $i === 0 ? $authnetClient->getAmount() : 0,
								'comments'         => $authnetClient->getOrder()->getComments(),
								'transaction_id'   => $transaction_id,
								'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
								'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
							));
						}
					} else {
						$package?->decreaseStock(array_sum($packages));
						for($i = 1; $i <= $quantity; $i++) {
							Database::Action("INSERT INTO `member_reservations` SET `status` = :status, `member_id` = :member_id, `event_id` = :event_id, `event_package_id` = :event_package_id, `song_selected` = :song_selected, `seat_selected` = :seat_selected, `name_on_pass` = :name_on_pass, `phone` = :phone, `item_count` = :item_count, `item_count_total` = :item_count_total, `package_amount` = :package_amount, `package_name` = :package_name, `total_amount` = :total_amount, `total_discount` = :total_discount, `total_paid` = :total_paid, `comments` = :comments, `transaction_id` = :transaction_id, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
								'status'           => Statuses\Reservation::PAID->getValue(),
								'member_id'        => filter_input(INPUT_POST, 'name_on_pass'),
								'event_id'         => $event->getId(),
								'event_package_id' => $package_id,
								'song_selected'    => filter_input(INPUT_POST, 'song_request'),
								'seat_selected'    => filter_input(INPUT_POST, 'seat_number') ?? NULL,
								'name_on_pass'     => Items\Member::Init(filter_input(INPUT_POST, 'name_on_pass'))->getFullName(),
								'phone'            => $authnetClient->getBilling()->getPhone(),
								'item_count'       => array_sum($packages),
								'item_count_total' => array_sum($packages),
								'package_amount'   => Items\Events\Package::Init($package_id)?->getPrice(),
								'package_name'     => Items\Events\Package::Init($package_id)?->getName(),
								'total_amount'     => $authnetClient->getAmount(),
								'total_discount'   => $discount,
								'total_paid'       => $authnetClient->getAmount() - (1 * $discount),
								'comments'         => $authnetClient->getOrder()->getComments(),
								'transaction_id'   => $transaction_id,
								'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
								'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
							));
						}
					}
				}
				
				// Log Action
				$member?->log()->setData(
					type       : Types\Log::CREATE,
					table_name : Tables\Members::RESERVATIONS
				)->execute();
				
				// Email Owner
				$mailer = new Mailer(TRUE);
				$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
				$mailer->addAddress($to_email, SITE_COMPANY);
				$mailer->setSubject($subjects['success']);
				$mailer->setAdmin(TRUE);
				$mailer->setBgColor('#198754');
				$mailer->setBody('events/purchase-pass/notifications/success.twig', array(
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
					'event'    => array(
						'heading'      => $event->getHeading(),
						'date'         => $event->getDate(),
						'name_on_pass' => $form_values['name_on_pass'],
						'packages'     => array_map(function(int $quantity, int $package_id) {
							$package = Items\Events\Package::Init($package_id);
							return array(
								'quantity' => $quantity,
								'text'     => sprintf("[%s] %s", $package?->getPrice(TRUE), $package?->getName())
							);
						}, $packages, array_keys($packages))
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
				$mailer->setBody('events/purchase-pass/notifications/receipt.twig', array(
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
					'event'    => array(
						'heading'      => $event->getHeading(),
						'date'         => $event->getDate(),
						'name_on_pass' => $form_values['name_on_pass'],
						'packages'     => array_map(function(int $quantity, int $package_id) {
							$package = Items\Events\Package::Init($package_id);
							return array(
								'quantity' => $quantity,
								'text'     => sprintf("[%s] %s", $package?->getPrice(TRUE), $package?->getName())
							);
						}, $packages, array_keys($packages))
					),
					'comments' => $form_values['comments']
				))->send();
				
				// Set Response
				$json_response = array(
					'status'         => 'success',
					'transaction_id' => $authnetClient->getTransaction()->getTransId(),
					'html'           => Render::GetTemplate('events/purchase-pass/success.twig')
				);
				break;
			case Client::RESPONSE_DECLINED:
			case Client::RESPONSE_ERROR:
				// Email Owner
				$mailer = new Mailer(TRUE);
				$mailer->setFrom(SITE_EMAIL, SITE_COMPANY);
				$mailer->addAddress($to_email, SITE_COMPANY);
				$mailer->setSubject($subjects['failure']);
				$mailer->setAdmin(TRUE);
				$mailer->setBgColor('#dc3545');
				$mailer->setBody('events/purchase-pass/notifications/declined.twig', array(
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
					'event'    => array(
						'heading'      => $event->getHeading(),
						'date'         => $event->getDate(),
						'name_on_pass' => $form_values['name_on_pass'],
						'packages'     => array_map(function(int $quantity, int $package_id) {
							$package = Items\Events\Package::Init($package_id);
							return array(
								'quantity' => $quantity,
								'text'     => sprintf("[%s] %s", $package?->getPrice(TRUE), $package?->getName())
							);
						}, $packages, array_keys($packages))
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
					'transaction_id' => $authnetClient->getTransaction()->getTransId(),
					'message'        => sprintf(
						"%s: %s",
						$form_values['response_code'],
						is_array($form_values['response_text'])
							? implode('; ', $form_values['response_text'])
							: $form_values['response_text']
					)
				);
				break;
			default:
				// Set Response
				$json_response = array(
					'status'         => 'error',
					'transaction_id' => $authnetClient->getTransaction()->getTransId(),
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