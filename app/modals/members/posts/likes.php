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
	use Items\Enums\Sizes;
	use Items\Members\Posts;
	use Items\Members\Posts\Types;
	
	try {
		// Fetch/Set Post
		$post = Types\Social::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Post
		if(is_null($post)) throw new Exception('Post not found.');
		
		// Check Likes
		if($post->likes()->count() == 0) {
			throw new Suggestion(
				message    : 'This post has no likes.',
				suggestion : Template::Render('members/suggestions/posts/no-likes.twig', array(
					'alt'      => $post->getMember()?->getAlt('avatar'),
					'avatar'   => $post->getMember()?->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
					'link'     => $post->getMember()?->getLink(),
					'username' => $post->getMember()?->getUsername()
				))
			);
		}
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully fetched post.',
			'modal'   => Render::GetTemplate('members/posts/likes.twig', array(
				'members' => array_map(fn(Posts\Like $like) => array(
					'alt'      => $like->getMember()?->getAlt('avatar'),
					'avatar'   => $like->getMember()?->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
					'link'     => $like->getMember()?->getLink(),
					'username' => $like->getMember()?->getUsername()
				), iterator_to_array($post->likes()->sort('username')))
			))
		);
	} catch(Suggestion $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'suggestion',
			'message' => $exception->getMessage(),
			'modal'   => $exception->getSuggestion()
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