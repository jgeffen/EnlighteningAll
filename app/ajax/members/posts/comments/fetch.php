<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/
	
	// TODO: Check if user is blocked
	// TODO: Check if private post is friend
	
	// Imports
	use Items\Members\Posts\Types;
	
	try {
		// Check Logged In
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Variable Defaults
		$post = Types\Social::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Set Response
		$json_response = array(
			'status'   => 'success',
			'message'  => 'Successfully fetched comments.',
			'comments' => $post->comments()->renderAll()
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