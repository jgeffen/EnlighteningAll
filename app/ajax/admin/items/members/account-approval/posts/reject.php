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
		
		// Check Image
		if($post->getFilename()) {
			// Set Directory
			$directory = sprintf("%s/files/members/%d", filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'), $member->getId());
			
			// Remove Image
			Helpers::RemoveFile($directory, $post->getFilename());
		}
		
		// Update Database
		Database::Action("DELETE FROM `member_posts` WHERE `id` = :id AND `member_id` = :member_id", array(
			'id'        => $post->getId(),
			'member_id' => $member->getId()
		));
		
		// Log Action
		$admin->log(
			type       : Types\Log::REJECT,
			table_name : Tables\Members::POSTS,
			table_id   : $post->getId(),
			filename   : $post->getFilename(),
			payload    : $_POST
		);
		
		// Create Ticket
		Database::Action("INSERT INTO `member_tickets` SET `member_ticket_id` = :member_ticket_id, `member_id` = :member_id, `content` = :content, `read` = :read, `initiated_by` = :initiated_by, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'member_ticket_id' => NULL,
			'member_id'        => $post->getMemberId(),
			'content'          => 'Your post has been rejected because it violated our community standards.',
			'read'             => FALSE,
			'initiated_by'     => 'admin',
			'author'           => $admin->getId(),
			'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		));
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully rejected post.'
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