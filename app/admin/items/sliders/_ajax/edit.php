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
		$item   = Items\Slider::Init($item['id']);
		$errors = call_user_func(function() {
			// Required Fields
			$required = array(
				'page_url'       => FILTER_DEFAULT,
				'filename'       => FILTER_DEFAULT,
				'published'      => FILTER_VALIDATE_INT,
				'published_date' => FILTER_DEFAULT,
				'analytics'      => FILTER_VALIDATE_INT
			);
			
			// Check Required Fields
			foreach($required as $field => $validation) {
				// Switch Validation
				switch($validation) {
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
		Database::Action("UPDATE `sliders` SET `page_url` = :page_url, `heading` = :heading, `content` = :content, `content_position` = :content_position, `link` = :link, `link_text` = :link_text, `filename` = :filename, `filename_alt` = :filename_alt, `delete_on_expiration` = :delete_on_expiration, `published` = :published, `expiration_date` = :expiration_date, `published_date` = :published_date, `analytics` = :analytics, `expiration` = :expiration, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `id` = :id", array(
			'page_url'             => filter_input(INPUT_POST, 'page_url'),
			'heading'              => filter_input(INPUT_POST, 'heading'),
			'content'              => filter_input(INPUT_POST, 'content'),
			'content_position'     => filter_input(INPUT_POST, 'content_position'),
			'link'                 => filter_input(INPUT_POST, 'link'),
			'link_text'            => filter_input(INPUT_POST, 'link_text'),
			'filename'             => filter_input(INPUT_POST, 'filename'),
			'filename_alt'         => filter_input(INPUT_POST, 'filename_alt'),
			'delete_on_expiration' => filter_input(INPUT_POST, 'delete_on_expiration', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
			'published'            => filter_input(INPUT_POST, 'published', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
			'expiration_date'      => filter_input(INPUT_POST, 'expiration_date'),
			'published_date'       => filter_input(INPUT_POST, 'published_date'),
			'analytics'            => filter_input(INPUT_POST, 'analytics', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
			'expiration'           => filter_input(INPUT_POST, 'expiration', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
			'author'               => $admin->getId(),
			'user_agent'           => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'           => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'id'                   => $item->getId()
		));
		
		// Log Action
		$admin->log(
			type       : Types\Log::UPDATE,
			table_name : Tables\Website::SLIDERS,
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