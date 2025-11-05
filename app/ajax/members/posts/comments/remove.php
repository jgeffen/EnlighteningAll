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
	use Items\Members\Posts;
	
	try {
		// Variable Defaults
		$comment = Posts\Comment::Init($dispatcher->getId());
		
		// Check Comment
		if(is_null($comment)) throw new Exception('Comment not found.');
		
		// Check Post
		if(is_null($comment->getPost())) throw new Exception('Post no longer exists.');
		
		// Check Ownership
		if($comment->getMemberId() != $member->getId()) throw new Exception('You can only remove your own comments.');
		
		// Send Report
		$member->comment($comment->getPost())->setComment($comment)->setAction(Requests\Comment::REMOVE)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully removed comment.',
			'html'    => Render::GetTemplate('members/posts/toolbar/buttons/comment.twig', array(
				'action'     => 'comment-toggle',
				'active'     => $comment->getPost()->comments()->lookup($member),
				'collapse'   => FALSE,
				'count'      => $comment->getPost()->comments()->count(),
				'scrollable' => FALSE
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