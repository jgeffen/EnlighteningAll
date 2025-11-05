<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
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
		$item   = Items\Events\Package::Init($item['id']);
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'name'           => FILTER_DEFAULT,
				'price'          => FILTER_DEFAULT,
				'published'      => FILTER_VALIDATE_INT,
				'published_date' => FILTER_DEFAULT
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
			
			// Check Image Alt
			if(filter_input(INPUT_POST, 'filename') && !filter_input(INPUT_POST, 'filename_alt')) {
				$errors[] = 'Image alt is required.';
			}
			
			return $errors ?? FALSE;
		});
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// Check Item
		if(is_null($item)) throw new Exception('Item not found in database');
		
		// Update Database
		Database::Action("UPDATE `event_packages` SET `stock_quantity` = :stock_quantity, `is_bogo` = :is_bogo, `musical` = :musical, `seatable` = :seatable, `seats` = :seats, `merchant` = :merchant, `taxable` = :taxable, `name` = :name, `price` = :price, `published` = :published, `published_date` = :published_date, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address  WHERE `id` = :id", array(
			'stock_quantity' => filter_input(INPUT_POST, 'stock-quantity', FILTER_VALIDATE_INT),
			'is_bogo'        => filter_input(INPUT_POST, 'musical', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
			'musical'        => filter_input(INPUT_POST, 'musical', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
			'seatable'       => filter_input(INPUT_POST, 'seatable', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
			'seats'          => json_encode(range(1, filter_input(INPUT_POST, 'stock-quantity', FILTER_VALIDATE_INT))),
			'merchant'       => filter_input(INPUT_POST, 'merchant'),
			'taxable'        => filter_input(INPUT_POST, 'taxable', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
			'name'           => filter_input(INPUT_POST, 'name'),
			'price'          => filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, array('options' => array('default' => 0.00), 'flags' => FILTER_FLAG_ALLOW_FRACTION)),
			'published'      => filter_input(INPUT_POST, 'published', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
			'published_date' => filter_input(INPUT_POST, 'published_date'),
			'author'         => $admin->getId(),
			'user_agent'     => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'     => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'id'             => $item->getId()
		));
		
		// Log Action
		$admin->log(
			type       : Types\Log::UPDATE,
			table_name : Tables\Website\Events::PACKAGES,
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