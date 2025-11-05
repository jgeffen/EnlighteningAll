<?php
	/*
	Copyright (c) 2022 Daerik.com
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
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members;
	
	try {
		// Variable Defaults
		$_POST = filter_var_array(json_decode(file_get_contents('php://input'), TRUE), array(
			'notes' => FILTER_DEFAULT
		));
		
		// Check Access Level
		if(!Admin\Privilege(2)) throw new Exception('You do not have sufficient privilege to access this account.');
		
		// Variable Defaults
		$item = Members\Post::Init($dispatcher->getTableId());
		
		// Check Post
		if(is_null($item)) throw new Exception('Post cannot be found.');
		
		// Set Directory
		$directory = sprintf("%s/files/members/%d", filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'), $item->getMemberId());
		
		// Remove Image
		Helpers::RemoveFile($directory, $item->getFilename());
		
		// Update Database
		Database::Action("DELETE FROM `member_posts` WHERE `id` = :id AND `member_id` = :member_id", array(
			'id'        => $item->getId(),
			'member_id' => $item->getMemberId()
		));
		
		// Check Notes
		if(!empty($_POST['notes'])) {
			// Create Ticket
			Database::Action("INSERT INTO `member_tickets` SET `member_ticket_id` = :member_ticket_id, `member_id` = :member_id, `content` = :content, `read` = :read, `initiated_by` = :initiated_by, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'member_ticket_id' => NULL,
				'member_id'        => $item->getMemberId(),
				'content'          => $_POST['notes'],
				'read'             => FALSE,
				'initiated_by'     => 'admin',
				'author'           => $admin->getId(),
				'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			));
		}
		
		// Log Action
		$admin->log(
			type       : Types\Log::DELETE,
			table_name : Tables\Members::POSTS,
			table_id   : $item->getId(),
			payload    : $_POST,
			notes      : $_POST['notes']
		);
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Successfully deleted post.',
			'data'    => $item->toArray()
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