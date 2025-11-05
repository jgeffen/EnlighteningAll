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
	use Items\Enums\Requests;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members\Posts\Types as Posts;
	
	try {
		// Variable Defaults
		$post = Posts\Social::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_like')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Handle Like
		if($member->like($post)->setAction(Requests\Like::ADD)->execute()) {
			// Handle Notification
			$member->notify($post->getMember())->setPost($post)->setRequest(Requests\Notification::ADD)->setType(Types\Notification::LIKE)->execute();
		}
		
		// Log Action
		$member->log()->setData(
			type       : Types\Log::LIKE,
			table_name : Tables\Members::POSTS,
			table_id   : $post->getId()
		)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully updated post.',
			'html'    => Render::GetTemplate('members/posts/toolbar/buttons/like.twig', array(
				'count'  => $post->likes()->count(),
				'active' => $post->likes()->lookup($member)
			))
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