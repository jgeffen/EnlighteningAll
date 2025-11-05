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
	 * @var Membership        $member
	 */
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Variable Defaults
		$subscription = Items\Subscription::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Subscription
		if(is_null($subscription)) throw new Exception('Subscription not found in database. Please refresh your page.');
		
		// Check Active Subscription
		if($member->subscription()?->getSubscriptionId() != $subscription->getId()) throw new Exception('This subscription is not active on your account.');
		
		// Check Cancellation
		if($member->subscription()->getCancellationDate()) {
			throw new Exception(sprintf("This subscription will already cancel on %s", $member->subscription()->getCancellationDate()->format('n/j/y')));
		}
		
		// Update Database
		Database::Action("UPDATE `member_subscriptions` SET `date_cancellation` = :date_cancellation WHERE `id` = :id", array(
			'date_cancellation' => $member->subscription()->getRenewalDate()->format('Y-m-d'),
			'id'                => $member->subscription()->getId()
		));
		
		// Log Action
		$member->log()->setData(
			type       : Types\Log::CANCEL,
			table_name : Tables\Members::SUBSCRIPTIONS,
			table_id   : $member->subscription()->getId()
		)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Subscription set for cancellation.'
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