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
	
	try {
		// Variable Defaults
		$post_ids = filter_input(INPUT_POST, 'post_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		
		// Check IDs
		if(empty($post_ids)) throw new Exception('No data sent.');
		
		// Sort Posts
		foreach($post_ids as $post_id) {
			Database::Action("UPDATE `member_posts` SET `position` = :position WHERE `id` = :id AND `member_id` = :member_id", array(
				'position'  => $position = ($position ?? 0) + 1,
				'id'        => $post_id,
				'member_id' => $member->getId()
			));
		}
		
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