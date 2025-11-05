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
	use Items\Enums\Options;
	use Items\Members\Posts\Types;
	
	try {
		// Variable Defaults
		$post = Types\Social::Init($dispatcher->getId());
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Update Post
		Database::Action("UPDATE `member_posts` SET `visibility` = :visibility WHERE `id` = :post_id AND `member_id` = :member_id", array(
			'visibility' => Options\Visibility::lookup(filter_input(INPUT_POST, 'visibility'))?->getValue(),
			'post_id'    => $post->getId(),
			'member_id'  => $member->getId()
		));
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Database updated successfully.'
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