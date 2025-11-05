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
	
	try {
		// Variable Defaults
		$item   = filter_input(INPUT_POST, 'item', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$item   = Items\Subscription::Init($item['id']);
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'name'    => FILTER_DEFAULT,
				'icon'    => FILTER_DEFAULT,
				'price'   => FILTER_VALIDATE_FLOAT,
				'default' => FILTER_VALIDATE_INT
			);
			
			// Check Required Fields
			foreach($required as $field => $validation) {
				// Switch Validation
				switch($validation) {
					case FILTER_VALIDATE_FLOAT:
					case FILTER_VALIDATE_INT:
						if(is_null(filter_input(INPUT_POST, $field, $validation)) || filter_input(INPUT_POST, $field, $validation) === FALSE) {
							$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
						}
						break;
					case FILTER_DEFAULT:
					default:
						if(!filter_input(INPUT_POST, $field, $validation)) {
							$errors[] = sprintf("%s is missing or invalid.", ucwords(str_replace('_', ' ', $field)));
						}
				}
			}
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// Check Item
		if(is_null($item)) throw new Exception('Item not found in database');
		
		// Update Database
		Database::Action("UPDATE `subscriptions` SET `name` = :name, `benefits` = :benefits, `content` = :content, `icon` = :icon, `price` = :price, `default` = :default, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `id` = :id", array(
			'name'       => filter_input(INPUT_POST, 'name'),
			'benefits'   => filter_input(INPUT_POST, 'benefits'),
			'content'    => filter_input(INPUT_POST, 'content'),
			'icon'       => filter_input(INPUT_POST, 'icon'),
			'price'      => filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT),
			'default'    => $item->isDefault(),
			'author'     => $admin->getId(),
			'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'id'         => $item->getId()
		));
		
		// Log Action
		$admin->log(
			type       : Types\Log::UPDATE,
			table_name : Tables\Website::SUBSCRIPTIONS,
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
	} catch(PDOException $exception) {
		// Log Error
		Debug::Exception($exception);
		
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
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