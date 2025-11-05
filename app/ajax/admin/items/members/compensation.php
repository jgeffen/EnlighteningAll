<?php
	/*
	Copyright (c) 2022 Daerik.com
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
	use Items\Enums\Statuses;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	// Variable Defaults
	$subscription_id = 25187; // TODO: Allow admin to select which subscription when multiple.
	
	try {
		// Check Access Level
		if(!Admin\Privilege(2)) throw new Exception('You do not have sufficient privilege to access this account.');
		
		// Variable Defaults
		$member       = Membership::Init($dispatcher->getId());
		$subscription = Items\Subscription::Init($subscription_id);
		
		// Check Member
		if(is_null($member)) throw new Exception('Member cannot be found.');
		
		// Check Subscription
		if(is_null($subscription)) throw new Exception('Subscription not found in database. Please refresh your page.');
		
		// Check Member Subscription
		if($member->subscription()?->isPaid()) throw new Exception('Member already has an active subscription.');
		
		// Update Member Subscription
		Database::Action("INSERT INTO `member_subscriptions` SET `status` = :status, `member_id` = :member_id, `subscription_id` = :subscription_id, `transaction_id` = :transaction_id, `date_start` = :date_start, `date_renewal` = :date_renewal, `date_cancellation` = :date_cancellation, `details` = :details, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address ON DUPLICATE KEY UPDATE `status` = :status, `transaction_id` = :transaction_id, `date_start` = :date_start, `date_renewal` = :date_renewal, `date_cancellation` = :date_cancellation, `details` = :details, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'status'            => Statuses\Subscription::ACTIVE->getValue(),
			'member_id'         => $member->getId(),
			'subscription_id'   => $subscription->getId(),
			'transaction_id'    => NULL,
			'date_start'        => date_create()->format('Y-m-d'),
			'date_renewal'      => Helpers::MonthShifter(date_create(), 1)->format('Y-m-d'),
			'date_cancellation' => Helpers::MonthShifter(date_create(), 1)->format('Y-m-d'),
			'details'           => 'Compensated by Admin',
			'author'            => $admin->getId(),
			'user_agent'        => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'        => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		));
		
		// Log Action
		$admin->log(
			type       : Types\Log::COMPENSATE,
			table_name : Tables\Secrets::MEMBERS,
			table_id   : $member->getId(),
			payload    : array_merge($_POST, array(
				'subscription_id' => $subscription->getId()
			))
		);
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully compensated account.',
			'data'    => $member->toArray()
		);
	} catch(Error $exception) {
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