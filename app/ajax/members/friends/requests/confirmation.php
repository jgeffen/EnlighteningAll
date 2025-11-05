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
	
	// Imports
	use Items\Enums\Requests;
	use Items\Enums\Statuses;
	
	try {
		$friend = Items\Member::Init(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
		
		// Check Member
		if(is_null($friend)) throw new Exception('Member not found in database.');
		
		//get the post data
		$questions = filter_input(INPUT_POST, 'questions',  FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$answer = filter_input(INPUT_POST, 'answer',  FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$memberId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
		// Delete the old data 
		Database::Action("DELETE FROM `member_confirmation_friend_request` 
                 WHERE `member_id` = :member_id 
                 AND `initiated_by` = :initiated_by", 
				array(
					'member_id'     => $memberId,
					'initiated_by' => $member->getId()
				));
		// Insert the member confirmation friend request
		foreach($answer as $key=>$val){
			
			Database::Action("INSERT INTO `member_confirmation_friend_request` SET `member_id` = :member_id, `confirmation_questions` = :confirmation_questions, `confirmation_answer` = :confirmation_answer, `initiated_by` = :initiated_by", 
						 array(
						'member_id'     => $memberId,
						'confirmation_questions' => $questions[$key],
						'confirmation_answer' => $val,
						'initiated_by' => $member->getId()
					));
		}
		
		$json_response = array(
			'status'  => 'success',
			'message' => "Confirmation sent friend request"
		);
		// Variable Defaults
		
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