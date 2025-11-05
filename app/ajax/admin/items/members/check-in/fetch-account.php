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
	use Members\QRCode;
	
	try {
		// Set QR Code
		$qr_code = Items\Members\QRCode::Fetch(Database::Action("SELECT * FROM `member_qr_codes` WHERE `type` = :type AND `hash` = :hash", array(
			'type' => QRCode\CheckIn::TYPE,
			'hash' => filter_input(INPUT_POST, 'data')
		)));
		
		// Check QR Code
		if(is_null($qr_code)) throw new Exception('Invalid QR code.');
		
		// Check Member
		if($qr_code->getMember()->getCheckIn()) throw new Exception('Member already checked-in.');
		
		// Check Expired QR Code
		if($qr_code->isExpired()) throw new Exception('This QR code has expired.');
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'data'   => $qr_code->getMember()->toArray(),
			'html'   => Template::Render('admin/items/members/check-in/account.twig', array(
				'id'       => $qr_code->getMemberId(),
				'image'    => $qr_code->getMember()->getAvatar()?->getImage(Sizes\Avatar::XL, TRUE) ?? Items\Defaults::AVATAR_XL,
				'username' => $qr_code->getMember()->getUsername(),
				'name'     => $qr_code->getMember()->getFullName(),
				'bio'      => $qr_code->getMember()->getBio(),
				'beads'    => array_map(fn(Options\BeadColors $bead) => $bead->getLabel(), $qr_code->getMember()->getBeadColors()),
				'necklace' => $qr_code->getMember()->getNecklaceColor()?->getLabel(),
				'partner'  => array(
					'name'     => $qr_code->getMember()->getPartnerFirstName(),
					'beads'    => array_map(fn(Options\BeadColors $bead) => $bead->getLabel(), $qr_code->getMember()->getPartnerBeadColors()),
					'necklace' => $qr_code->getMember()->getPartnerNecklaceColor()?->getLabel()
				)
			))
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