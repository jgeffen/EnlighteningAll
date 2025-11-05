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
	use Items\Members\Posts\Types as Posts;
	
	try {
		// Variable Defaults
		$post = Posts\Social::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		$type = filter_input(INPUT_POST, 'type');
		$message = filter_input(INPUT_POST, 'message');
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Check Type
		if(empty($type)) throw new Exception('No type given.');
		
		// Check Member's ID
		if($post->getMemberId() == $member->getId()) throw new Exception('You cannot report your own post.');
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_report')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Handle Report
		$member->report($post)->setAction(Requests\Report::ADD)->setType($type)->setMessage($message)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully reported post.',
			'html'    => Render::GetTemplate('members/posts/toolbar/buttons/report.twig', array(
				'active' => $post->reports()->lookup($member),
				'postId' =>$post->getId()
			))
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