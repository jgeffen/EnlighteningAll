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
	// TODO: Check if private post is friend
	// TODO: Combine friend status and block status
	
	// Imports
	use Items\Enums\Requests;
	use Items\Enums\Sizes;
	use Items\Enums\Statuses;
	
	try {
		if(filter_input(INPUT_POST, 'type') == "private"){
			// Check Subscription
			if(!$member->subscription()?->isPaid()) {
				throw new Suggestion(
					message    : 'Private photos are only available for premium members.',
					suggestion : Template::Render('members/suggestions/messages/free-cannot-initiate.twig', array(
						'alt'      => $member->getAlt('avatar'),
						'avatar'   => $member->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
						'username' => $member->getUsername(),
						'type' => "private"
					))
				);
			}
		}
		
		if(filter_input(INPUT_POST, 'type') == "custom-message"){
			// Check Subscription
			if(!$member->subscription()?->isPaid()) {
				throw new Suggestion(
					message    : 'Private photos are only available for premium members.',
					suggestion : Template::Render('members/suggestions/messages/free-cannot-initiate.twig', array(
						'alt'      => $member->getAlt('avatar'),
						'avatar'   => $member->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
						'username' => $member->getUsername(),
						'type' => "custom-message"
					))
				);
			}
		}
		// Variable Defaults
		$contact = Items\Member::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE));
		$message = trim(strip_tags(filter_input(INPUT_POST, 'message'), array('br', 'strong', 'em', 'u')));
		
		// Check Member's ID
		if($member->getId() == $contact->getId()) throw new Exception('You cannot send a message to yourself!');
		
		// Check Block Status
		if($member->getBlockStatus($contact)->is(Statuses\Block::BLOCKED)) throw new Exception('You are not able to message this user.');
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_message')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Check Friend Status
		if(!$member->getFriendStatus($contact)->is(Statuses\Friend::APPROVED)) {
			throw new Suggestion(
				message    : 'This is not your friend.',
				suggestion : Template::Render('members/suggestions/not-friends.twig', array(
					'alt'      => $contact->getAlt('avatar'),
					'avatar'   => $contact->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
					'link'     => $contact->getLink(),
					'username' => $contact->getUsername()
				))
			);
		}
		
		// Check Subscription or Daily Messages
		if(!$member->subscription()?->isPaid() && $member->getDailyMessageCount() >= 3) {
			throw new Suggestion(
				message    : 'Reached maximum free messages.',
				suggestion : Template::Render('members/suggestions/messages/max-reached.twig', array(
					'alt'      => $member->getAlt('avatar'),
					'avatar'   => $member->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
					'username' => $member->getUsername()
				))
			);
		}
		
		// Check Subscription or Existing Thread
		// if(!$member->subscription()?->isPaid() && $member->messages($contact)->empty()) {
		// 	throw new Suggestion(
		// 		message    : 'Cannot initate converation with free account.',
		// 		suggestion : Template::Render('members/suggestions/messages/free-cannot-initiate.twig', array(
		// 			'alt'      => $member->getAlt('avatar'),
		// 			'avatar'   => $member->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
		// 			'username' => $member->getUsername()
		// 		))
		// 	);
		// }
		
		// Check Message
		if(empty($message)) throw new Exception('Message cannot be empty.');
		
		// Check Message Filtering: Badwords
		if($member->settings()->getValue('message_filter_badwords')) {
			$message = ConsoleTVs\Profanity\Builder::blocker($message)->dictionary(array_map(fn($word) => array(
				'language' => 'en',
				'word'     => preg_replace('/[^A-Za-z0-9]/', '', $word)
			), $member->settings()->getValue('badwords_list')))->filter();
		}
		
		// Check Message Filtering: Links
		if($member->settings()->getValue('message_filter_links')) {
			$message = preg_replace('/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/', '', $message);
		}
		
		// Send Message
		$member->message($contact)->setAction(Requests\Message::SEND)->setContent($message)->execute();
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully messaged member.'
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