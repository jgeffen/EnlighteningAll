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
	use Items\Collections;
	use Items\Enums\Options;
	use Items\Enums\Sizes;
	use Items\Enums\Types;
	use Items\Members\Posts;
	use Members\QRCode;
	
	try {
		// Set QR Code
		$qr_code = Items\Members\QRCode::Fetch(Database::Action("SELECT * FROM `member_qr_codes` WHERE `type` = :type AND `hash` = :hash", array(
			'type' => QRCode\AccountApproval::TYPE,
			'hash' => filter_input(INPUT_POST, 'data')
		)));
		
		// Check QR Code
		if(is_null($qr_code)) throw new Exception('Invalid QR code.');
		
		// Check Member
		if($qr_code->getMember()->isApproved()) throw new Exception('Member already approved.');
		
		// Check Expired QR Code
		if($qr_code->isExpired()) throw new Exception('This QR code has expired.');
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'data'   => $qr_code->getMember()->toArray(),
			'modal'  => Template::Render('admin/items/members/account-approval/account.twig', array(
				'member' => array(
					'alt'      => $qr_code->getMember()->getAlt('avatar'),
					'avatar'   => $qr_code->getMember()->getAvatar()?->getImage(Sizes\Avatar::XL, TRUE) ?? Items\Defaults::AVATAR_XL,
					'id'       => $qr_code->getMember()->getId(),
					'name'     => $qr_code->getMember()->getFullName(),
					'username' => $qr_code->getMember()->getUsername(),
					'link'     => $qr_code->getMember()->getLink(),
					'partner'  => $qr_code->getMember()->getPartnerFirstName()
				),
				'avatar' => array(
					'alt'      => $qr_code->getMember()->getAlt('avatar'),
					'id'       => $qr_code->getMember()->getAvatar()?->getId(),
					'image'    => $qr_code->getMember()->getAvatar()?->getImageSource(),
					'thumb'    => $qr_code->getMember()->getAvatar()?->getImage(Sizes\Avatar::XS, TRUE),
					'date'     => $qr_code->getMember()->getAvatar()?->getTimestamp()->format('M jS, Y, g:iA'),
					'approved' => $qr_code->getMember()->getAvatar()?->isApproved(FALSE)
				),
				'posts'  => array_map(fn(Posts\Types\Social $post) => array(
					'alt'      => $qr_code->getMember()->getAlt('post image'),
					'id'       => $post->getId(),
					'image'    => $post->getImageSource(),
					'thumb'    => $post->getThumb(Items\Defaults::NO_IMAGE_SQUARE),
					'title'    => $post->getTitle(),
					'date'     => $post->getTimestamp()->format('M jS, Y, g:iA'),
					'approved' => $post->isApproved(FALSE),
					'link'     => $post->getLink(),
					'private'  => $post->getVisibility()?->is(Options\Visibility::FRIENDS)
				), iterator_to_array(new Collections\Posts(Database::Action("SELECT * FROM `member_posts` JOIN `member_post_type_social` ON `member_post_id` = `id` WHERE `type` = :type AND `member_id` = :member_id ORDER BY `timestamp` DESC", array(
					'type'      => Types\Post::SOCIAL->getValue(),
					'member_id' => $qr_code->getMember()->getId()
				)), Types\Post::SOCIAL)))
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