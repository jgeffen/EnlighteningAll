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
	use Items\Members\Posts\Types;
	
	try {
		// Variable Defaults
		$post    = Types\Social::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		$comment = trim(strip_tags(filter_input(INPUT_POST, 'comment'), array('br', 'strong', 'em', 'u')));
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_comment')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Required Checks
		if(empty($comment)) throw new Exception('Comment cannot be empty.');
		
		// Check Comment Filtering: Badwords
		if($member->settings()->getValue('comment_filter_badwords')) {
			$comment = ConsoleTVs\Profanity\Builder::blocker($comment)->dictionary(array_map(fn($word) => array(
				'language' => 'en',
				'word'     => preg_replace('/[^A-Za-z0-9]/', '', $word)
			), $member->settings()->getValue('badwords_list')))->filter();
		}
		
		// Check Message Filtering: Links
		if($member->settings()->getValue('comment_filter_links')) {
			$comment = preg_replace('/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/', '', $comment);
		}
		
		// Send Comment
		$member->comment($post)->setAction(Requests\Comment::ADD)->setContent($comment)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully commented on post.',
			'html'    => Render::GetTemplate('members/posts/toolbar/buttons/comment.twig', array(
				'action'     => 'comment-toggle',
				'active'     => $post->comments()->lookup($member),
				'collapse'   => FALSE,
				'count'      => $post->comments()->count(),
				'scrollable' => FALSE
			))
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