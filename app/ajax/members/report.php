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
	
	// TODO: Check if user is blocked
	// TODO: Check if private photo is friend
	
	// Imports
	use Items\Enums\Sizes;
	use Items\Enums\Statuses;
	use Items\Members;
	
	try {
		// Variable Defaults
		$profile = Items\Member::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		$type    = filter_input(INPUT_POST, 'type');
		$message    = filter_input(INPUT_POST, 'message');
		
		// Check Member
		if(is_null($profile)) throw new Exception('Member not found.');
		
		// Check Type
		if(empty($type)) throw new Exception('No type given.');
		
		// Check Member's ID
		if($profile->getId() == $member->getId()) throw new Exception('You cannot report your own profile.');
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_report')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Set Report
		$report = Members\Report::Init(
			id         : NULL,
			profile_id : $profile->getId(),
			member_id  : $member->getId()
		);
		
		// Check Report
		if(!is_null($report)) throw new Exception(sprintf("Your report has already been made and it is currently: %s", $report->getStatus()?->getValue()));
		
		// Update Database
		Database::Action("INSERT INTO `member_reports` SET `status` = :status, `type` = :type, `profile_id` = :profile_id, `member_id` = :member_id, `dataset` = :dataset, `message` = :message, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'status'     => Statuses\Report::PENDING->getValue(),
			'type'       => $type,
			'profile_id' => $profile->getId(),
			'member_id'  => $member->getId(),
			'dataset'    => $profile->toJson(),
			'message'    => $message,
			'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		));
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully reported profile.',
			'modal'   => Template::Render('members/suggestions/block-member.twig', array(
				'alt'      => $profile->getAlt('avatar'),
				'avatar'   => $profile->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
				'id'       => $profile->getId(),
				'username' => $profile->getUsername(),
				'message'  => 'Successfully reported profile.'
			))
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
	
	// Output JSON
	echo json_encode($json_response);