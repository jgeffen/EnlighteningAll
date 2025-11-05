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
	use Items\Enums\Requests;
	use Items\Enums\Statuses;
	
	try {
		// Variable Defaults
		$profile = Items\Member::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Profile
		if(is_null($profile)) throw new Exception('Profile not found in database.');
		
		// Check Member's ID
		if($member->getId() == $profile->getId()) throw new Exception('You cannot send a request to yourself!');
		
		// Variable Defaults
		$status = $member->getBlockStatus($profile);
		
		// Set Response
		$json_response = match ($status) {
			Statuses\Block::BLOCKED => throw new Exception('This user is already blocked.'),
			Statuses\Block::NONE    => $member->block($profile)->setAction(Requests\Block::ADD)->execute() ? array(
				'status'  => 'success',
				'message' => 'Blocked user.'
			) : array(
				'status'  => 'error',
				'message' => 'Failed to block user. Please refresh your page.'
			)
		};
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