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
	 * @var Membership        $member
	 */
	
	// TODO: Check if user is blocked
	// TODO: Check if private photo is friend
	
	// Imports
	use Items\Members\Posts\Types as Posts;
	
	try {
		// Variable Defaults
		$post = Posts\Social::Init(filter_input(INPUT_POST, 'postId', FILTER_VALIDATE_INT));
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'users'  => $post->likes()->usernames()
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