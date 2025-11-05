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
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Variable Defaults
		$member = Items\Member::Init(filter_input(INPUT_POST, 'member_id', FILTER_VALIDATE_INT));
		$post   = Items\Members\Post::Init(filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT));
		
		// Check Member
		if(is_null($member)) throw new Exception('Member not found in database.');
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found in database.');
		
		// Check Ownership
		if($member->getId() != $post->getMemberId()) throw new Exception('This member does not own the post.');
		
		// Check Approval
		if($post->isApproved(FALSE)) throw new Exception('This post is already approved.');
		
		// Update Database
		Database::Action("UPDATE `member_posts` SET `approved` = :approved, `author` = :author WHERE `id` = :id", array(
			'approved' => TRUE,
			'author'   => $admin->getId(),
			'id'       => $post->getId()
		));
		
		// Log Action
		$admin->log(
			type       : Types\Log::APPROVE,
			table_name : Tables\Members::POSTS,
			table_id   : $post->getId(),
			filename   : $post->getFilename(),
			payload    : $_POST
		);
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully approved post.'
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