<?php
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members;
	
	try {
		// Check Logged In
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Variable Defaults
		$member  = new Membership();
		$cropper = filter_input(INPUT_POST, 'cropper', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		
		// Check Required Data
		if(empty($cropper['id'])) throw new Exception('Post ID is missing.');
		
		// Set Post
		$post = Members\Post::Init($cropper['id']);
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Check Member
		if($post->getMemberId() != $member->getId()) throw new Exception('You do not have permission to modify this post.');
		
		// Set Directory
		$directory = sprintf("%s/files/members/%d", filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'), $member->getId());
		
		// Remove Image
		Helpers::RemoveFile($directory, $post->getFilename());
		
		// Update Database
		Database::Action("UPDATE `member_posts` SET `filename` = :filename WHERE `id` = :id AND `member_id` = :member_id", array(
			'filename'  => NULL,
			'id'        => $post->getId(),
			'member_id' => $member->getId()
		));
		
		// Log Action
		$member->log()->setData(
			type         : Types\Log::DELETE,
			table_name   : Tables\Members::POSTS,
			table_id     : $post->getId(),
			table_column : Types\Column::FILENAME,
			filename     : $post->getFilename()
		)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Post image has been removed.'
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