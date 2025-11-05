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
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members;
	
	try {
		// Variable Defaults
		$post = Members\Post::Init($dispatcher->getId());
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Check Member
		if($post->getMemberId() != $member->getId()) throw new Exception('You do not have permission to modify this post.');
		
		// Set Directory
		$directory = sprintf("%s/files/members/%d", filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'), $member->getId());
		
		// Remove Image
		if($post->hasImage()) {
			Helpers::RemoveFile($directory, $post->getFilename());
		}
		
		// Update Database
		Database::Action("DELETE FROM `member_posts` WHERE `id` = :id AND `member_id` = :member_id", array(
			'id'        => $post->getId(),
			'member_id' => $member->getId()
		));
		
		// Log Action
		$member->log()->setData(
			type       : Types\Log::DELETE,
			table_name : Tables\Members::POSTS,
			table_id   : $post->getId()
		)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Post has been removed.'
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
	
	// Output Response
	echo json_encode($json_response);