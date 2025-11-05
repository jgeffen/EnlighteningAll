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
	 * @var Admin\User        $admin
	 */
	
	// Imports
	use Items\Members\Posts\Types as Posts;
	
	try {
		// Variable Defaults
		$post   = Posts\Social::Init($dispatcher->getId());
		$status = 1 - filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found in database.');
		
		// Update Database
		Database::Action("UPDATE `member_posts` SET `approved` = :approved WHERE `id` = :id", array(
			'approved' => $status,
			'id'       => $post->getId()
		));
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Database successfully updated.'
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