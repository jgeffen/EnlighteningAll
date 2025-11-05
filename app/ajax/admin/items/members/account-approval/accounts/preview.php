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
	use Items\Enums\Options;
	use Items\Enums\Sizes;
	
	try {
		// Variable Defaults
		$member = Items\Member::Init(filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT));
		
		// Check Member
		if(is_null($member)) throw new Exception('Member not found in database.');
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully fetched account.',
			'preview' => Template::Render('admin/items/members/account-approval/accounts/preview.twig', array(
				'image'    => $member->getAvatar()?->getImage(Sizes\Avatar::XL, TRUE) ?? Items\Defaults::AVATAR_XL,
				'name'     => $member->getFullName(),
				'bio'      => $member->getBio(),
				'beads'    => array_map(fn(Options\BeadColors $bead) => $bead->getLabel(), $member->getBeadColors()),
				'necklace' => $member->getNecklaceColor()?->getLabel(),
				'partner'  => array(
					'name'     => $member->getPartnerFirstName(),
					'beads'    => array_map(fn(Options\BeadColors $bead) => $bead->getLabel(), $member->getPartnerBeadColors()),
					'necklace' => $member->getPartnerNecklaceColor()?->getLabel()
				)
			))
		);
	} catch(Error|Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception)
		);
	}
	
	// Output JSON
	echo json_encode($json_response);