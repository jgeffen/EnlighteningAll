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
	
	try {
		// Variable Defaults
		$member = Items\Member::Init(filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT));
		$post   = Items\Members\Posts\Types\Social::Init(filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT), $member);
		
		// Check Member
		if(is_null($member)) throw new Exception('Member not found in database.');
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found in database.');
		
		// Check Ownership
		if($member->getId() != $post->getMemberId()) throw new Exception('This member does not own the post.');
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully fetched post.',
			'preview' => Template::Render('admin/items/members/account-approval/posts/preview.twig', array(
				'image'      => $post->getImage(Items\Defaults::SQUARE),
				'title'      => $post->getTitle(),
				'content'    => $post->getContent(),
				'date'       => $post->getDate(),
				'visibility' => $post->getVisibility()?->getLabel()
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