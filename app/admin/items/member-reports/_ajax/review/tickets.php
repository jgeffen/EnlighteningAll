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
	 * @var Admin\User        $admin
	 */
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members;
	
	try {
		// Variable Defaults
		$item    = Members\Ticket::Init($dispatcher->getTableId());
		$message = trim(strip_tags(filter_input(INPUT_POST, 'message'), array('br', 'strong', 'em', 'u')));
		
		// Check Message
		if(empty($message)) throw new Exception('Message cannot be empty.');
		
		// Check Item
		if(is_null($item)) throw new Exception('Item not found in database.');
		
		// Update Database
		Database::Action("INSERT INTO `member_tickets` SET `member_ticket_id` = :member_ticket_id, `member_id` = :member_id, `content` = :content, `read` = :read, `initiated_by` = :initiated_by, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'member_ticket_id' => $item->getId(),
			'member_id'        => $item->getMemberId(),
			'content'          => $message,
			'read'             => FALSE,
			'initiated_by'     => 'admin',
			'author'           => $admin->getId(),
			'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		));
		
		// Mark As Read
		$item->markRead(TRUE);
		
		// Log Action
		$admin->log(
			type       : Types\Log::UPDATE,
			table_name : Tables\Members::TICKETS,
			table_id   : $item->getId(),
			payload    : $_POST
		);
		
		// Set Message
		Admin\SetMessage('Updated database successfully.', 'success');
		
		// Set Response
		$json_response = array(
			'status'   => 'success',
			'message'  => Admin\GetMessage(),
			'table_id' => $item->getId()
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
	
	// Output Response
	echo json_encode($json_response);