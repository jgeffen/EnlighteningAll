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
		$type    = filter_input(INPUT_POST, 'type');
		$message    = filter_input(INPUT_POST, 'message');
		
		// Check Comment
		if(is_null($comment)) throw new Exception('Comment not found.');
		
		// Check Post
		if(is_null($comment->getPost())) throw new Exception('Post no longer exists.');
		
		// Check Type
		if(empty($type)) throw new Exception('No type given.');
		
		// Check Ownership
		if($comment->getMemberId() == $member->getId()) throw new Exception('You cannot report your own comment.');
		
		// Send Report
		$member->comment($comment->getPost())->setComment($comment)->setAction(Requests\Comment::REPORT)->setType($type)->setMessage($message)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully reported comment.'
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