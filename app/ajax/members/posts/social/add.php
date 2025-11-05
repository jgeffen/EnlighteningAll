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
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Validate Form
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'posted-by' => FILTER_DEFAULT,
				'heading'   => FILTER_DEFAULT,
				'content'   => FILTER_DEFAULT
			);
			
			// Check Required Fields
			foreach($required as $field => $validation) {
				if(!filter_input(INPUT_POST, $field, $validation[0] ?? $validation, $validation[1] ?? 0)) {
					$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
				}
			}
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_post')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Set Contest
		$contest = Items\Contest::Init(filter_input(INPUT_POST, 'contest', FILTER_VALIDATE_INT));
		
		if(filter_input(INPUT_POST, 'visibility') == "PRIVATE"){
			$visibility = filter_input(INPUT_POST, 'visibility');
		}else{
			$visibility = Options\Visibility::lookup(filter_input(INPUT_POST, 'visibility'))?->getValue();
		}
		// Check Contest Rules
		if(!is_null($contest)) {
			if($member->contests()->contains($contest)) throw new Exception('You have already entered this contest.');
			if(empty(filter_input(INPUT_POST, 'filename'))) throw new Exception('All contest entries must contain a photo.');
			if(!Options\Visibility::lookup(filter_input(INPUT_POST, 'visibility'))?->is(Options\Visibility::MEMBERS)) {
				throw new Exception('All contest entries must be public.');
			}
		}
		
		// List Dates
		list($date_start, $date_end) = explode(' to ', filter_input(INPUT_POST, 'dates')) + array_fill(0, 2, NULL);
		
		// Variable Defaults
		$heading = filter_input(INPUT_POST, 'heading');
		$content = trim(strip_tags(filter_input(INPUT_POST, 'content'), array('p', 'br', 'strong', 'em', 'u')));
		
		// Check Message Filtering: Badwords
		if($member->settings()->getValue('post_filter_badwords')) {
			// Set Dictionary
			$dictionary = array_map(fn($word) => array(
				'language' => 'en',
				'word'     => preg_replace('/[^A-Za-z0-9]/', '', $word)
			), $member->settings()->getValue('badwords_list'));
			
			// Check for Rejection
			if($member->settings()->getValue('post_filter_reject')) {
				if(!ConsoleTVs\Profanity\Builder::blocker($heading)->dictionary($dictionary)->clean()) {
					throw new Exception('Your post contains inappropriate words.');
				}
				
				if(!ConsoleTVs\Profanity\Builder::blocker($content)->dictionary($dictionary)->clean()) {
					throw new Exception('Your post contains inappropriate words.');
				}
			}
			
			// Filter Content for Badwords
			$heading = ConsoleTVs\Profanity\Builder::blocker($heading)->dictionary($dictionary)->filter();
			$content = ConsoleTVs\Profanity\Builder::blocker($content)->dictionary($dictionary)->filter();
		}
		
		// Check Message Filtering: Links
		if($member->settings()->getValue('post_filter_links')) {
			// Set Pattern
			$pattern = '/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/';
			
			// Check for Rejection
			if($member->settings()->getValue('post_filter_reject')) {
				if(preg_match($pattern, $heading)) {
					throw new Exception('Your post contains links to other websites.');
				}
				
				if(preg_match($pattern, $content)) {
					throw new Exception('Your post contains links to other websites.');
				}
			}
			
			// Filter Content for LInks
			$heading = preg_replace($pattern, '', $heading);
			$content = preg_replace($pattern, '', $content);
		}
		
		// Check Auto Approval
		if($member->settings()->getValue('post_approval_automatic')) {
			if($member->isApproved() || !$member->settings()->getValue('account_approval_required_post_approval')) {
				$auto_approve = TRUE;
			}
		}
		
		// Add Post
		$member_post_id = Database::Action("INSERT INTO `member_posts` SET `type` = :type, `visibility` = :visibility, `member_id` = :member_id, `heading` = :heading, `content` = :content, `posted_by` = :posted_by, `filename` = :filename, `approved` = :approved, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'type'       => Types\Post::SOCIAL->getValue(),
			'visibility' => $visibility,
			'member_id'  => $member->getId(),
			'heading'    => $heading,
			'content'    => $content,
			'posted_by'  => filter_input(INPUT_POST, 'posted-by'),
			'filename'   => filter_input(INPUT_POST, 'filename'),
			'approved'   => $auto_approve ?? FALSE,
			'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		), TRUE);
		
		// Check Contest Entry
		if(!is_null($contest)) {
			$member_contest_id = Database::Action("INSERT INTO `member_contests` SET `member_id` = :member_id, `member_post_id` = :member_post_id, `contest_id` = :contest_id, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'member_id'      => $member->getId(),
				'member_post_id' => $member_post_id,
				'contest_id'     => $contest->getId(),
				'user_agent'     => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'     => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			), TRUE);
		}
		
		// Insert Data in Social Data
		Database::Action("INSERT INTO `member_post_type_social` SET `member_post_id` = :member_post_id, `member_contest_id` = :member_contest_id, `date_start` = :date_start, `date_end` = :date_end", array(
			'member_post_id'    => $member_post_id,
			'member_contest_id' => $member_contest_id ?? NULL,
			'date_start'        => $date_start,
			'date_end'          => $date_end ?? $date_start
		));
		
		// Log Action
		$member->log()->setData(
			type       : Types\Log::CREATE,
			table_name : Tables\Members::POSTS,
			table_id   : $member_post_id
		)->execute();
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'html'   => Render::GetTemplate('/members/posts/social/add/success.twig')
		);
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status' => 'error',
			'errors' => $exception->getErrors()
		);
	} catch(PDOException $exception) {
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