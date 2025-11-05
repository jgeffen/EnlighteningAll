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
	use Items\Enums\Options;
	use Items\Enums\Sizes;
	use Items\Enums\Statuses;
	use Items\Members\Posts\Types;
	
	try {
		// Fetch/Set Post
		$post = Types\Social::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Check Block
		if($member->getBlockStatus($post->getMember())->is(Statuses\Block::BLOCKED)) {
			throw new Exception('This profile is blocked.');
		}
		
		// Check Visibility
		if($post->getVisibility()->is(Options\Visibility::FRIENDS)) {
			// Check Ownership
			if($member->getId() != $post->getMember()?->getId()) {
				// Check Friend Status
				if(!$member->getFriendStatus($post->getMember())->is(Statuses\Friend::APPROVED)) {
					throw new Exception('This post is for friends only.');
				}
			}
		}
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully fetched post.',
			'modal'   => Render::GetTemplate('members/posts/modal.twig', array(
				'post'    => array(
					'id'       => $post->getId(),
					'image'    => $post->getImageSource(),
					'link'     => $post->getLink(),
					'title'    => $post->getTitle(),
					'content'  => $post->getContent(),
					'date'     => $post->getDate(),
					'comments' => $post->comments()->renderAll(),
					'member'   => array(
						'link'     => $post->getMember()?->getLink(),
						'avatar'   => $post->getMember()?->getAvatar()?->getImage(Sizes\Avatar::SM) ?? Items\Defaults::AVATAR_SM,
						'username' => $post->getMember()?->getUsername()
					)
				),
				'member'  => array(
					'alt'    => $member->getAlt('profile image'),
					'avatar' => $member->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
				),
				'toolbar' => array(
					'like'    => Render::GetTemplate('members/posts/toolbar/buttons/like.twig', array(
						'count'  => $post->likes()->count(),
						'active' => $post->likes()->lookup($member)
					)),
					'comment' => Render::GetTemplate('members/posts/toolbar/buttons/comment.twig', array(
						'count'    => $post->comments()->count(),
						'active'   => $post->comments()->lookup($member),
						'collapse' => TRUE,
						'action'   => 'comment-toggle'
					)),
					'report'  => Render::GetTemplate('members/posts/toolbar/buttons/report.twig', array(
						'active' => $post->reports()->lookup($member)
					))
				)
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