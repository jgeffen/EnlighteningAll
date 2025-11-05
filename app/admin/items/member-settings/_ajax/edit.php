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
	use Items\Enums\Options;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Items\Members;
	
	try {
		// Variable Defaults
		$item = filter_input(INPUT_POST, 'item', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$item = Members\Setting::Init((int)$item['id']);
		
		// Check Item
		if(is_null($item)) throw new Exception('Item not found in database.');
		
		// Update Database
		Database::Action("UPDATE `member_settings` SET `value` = :value, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `id` = :id", array(
			'author'     => $admin->getId(),
			'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'id'         => $item->getId(),
			'value'      => match ($item->getType()) {
				Types\Setting::BOOLEAN => Options\OnOff::lookup(filter_input(INPUT_POST, 'value', FILTER_VALIDATE_INT))?->getLabel(),
				Types\Setting::INTEGER => filter_input(INPUT_POST, 'value', FILTER_VALIDATE_INT),
				Types\Setting::STRING  => filter_input(INPUT_POST, 'value'),
				Types\Setting::JSON    => call_user_func(function(array $value) {
					$value = array_map('strtolower', $value);
					$value = array_unique($value);
					$value = array_filter($value);
					
					asort($value);
					
					return json_encode($value);
				}, explode(PHP_EOL, filter_input(INPUT_POST, 'value')))
			}
		));
		
		// Log Action
		$admin->log(
			type       : Types\Log::UPDATE,
			table_name : Tables\Members::SETTINGS,
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
	} catch(FormException $exception) {
		// Set Response
		$json_response = array(
			'status' => 'error',
			'errors' => $exception->getErrors()
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