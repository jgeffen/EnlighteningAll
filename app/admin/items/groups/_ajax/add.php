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
		$page_url = Helpers::FormatPageURL(filter_input(INPUT_POST, 'page_title'), TRUE);
		$errors   = call_user_func(function() use ($page_url) {
			// Required Fields
			$required = array(
				'page_title'     => FILTER_DEFAULT,
				'heading'        => FILTER_DEFAULT,
				'published'      => FILTER_VALIDATE_INT,
				'published_date' => FILTER_DEFAULT
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
		
		// Update Database
		$table_id = Database::Action("INSERT INTO `groups` SET `category_id` = :category_id, `page_title` = :page_title, `page_description` = :page_description, `heading` = :heading, `content` = :content, `youtube_id` = :youtube_id, `filename` = :filename, `filename_alt` = :filename_alt, `page_url` = :page_url, `position` = :position, `published` = :published, `published_date` = :published_date, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address, `timestamp` = :timestamp, `last_timestamp` = :last_timestamp", array(
			'category_id'      => filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT),
			'page_title'       => filter_input(INPUT_POST, 'page_title'),
			'page_description' => filter_input(INPUT_POST, 'page_description'),
			'heading'          => filter_input(INPUT_POST, 'heading'),
			'content'          => filter_input(INPUT_POST, 'content'),
			'youtube_id'       => Helpers::ExtractYouTubeID(filter_input(INPUT_POST, 'youtube_id')),
			'filename'         => filter_input(INPUT_POST, 'filename'),
			'filename_alt'     => filter_input(INPUT_POST, 'filename_alt'),
			'page_url'         => $page_url,
			'position'         => Database::Action("SELECT IFNULL(MAX(`position`), 0) + 1 FROM `groups`")->fetch(PDO::FETCH_COLUMN),
			'published'        => filter_input(INPUT_POST, 'published', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
			'published_date'   => filter_input(INPUT_POST, 'published_date'),
			'author'           => $admin->getId(),
			'user_agent'       => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'       => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP),
			'timestamp'        => date('Y-m-d H:i:s'),
			'last_timestamp'   => date('Y-m-d H:i:s')
		), TRUE);
		
		// Log Action
		$admin->log(
			type       : Types\Log::CREATE,
			table_name : Tables\Website::GROUPS,
			table_id   : $table_id,
			payload    : $_POST
		);
		
		// Set Route(s)
		$parent_route ??= Router\Route::Init('categories', filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT));
		$parent_route ??= Router\Route::Init('groups');
		
		// Update Routes
		$route_id = Database::Action("INSERT INTO `routes` SET `parent_route_id` = :parent_route_id, `table_name` = :table_name, `table_id` = :table_id, `page_url` = :page_url, `category` = :category, `categories` = :categories, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'parent_route_id' => $parent_route?->getId(),
			'table_name'      => 'groups',
			'table_id'        => $table_id,
			'page_url'        => $page_url,
			'category'        => FALSE,
			'categories'      => FALSE,
			'author'          => $admin->getId(),
			'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		), TRUE);
		
		// Log Action
		$admin->log(
			type       : Types\Log::CREATE,
			table_name : Tables\Website::ROUTES,
			table_id   : $route_id,
			payload    : $_POST
		);
		
		// Set Message
		Admin\SetMessage('Updated database successfully.', 'success');
		
		// Set Response
		$json_response = array(
			'status'   => 'success',
			'message'  => Admin\GetMessage(),
			'table_id' => $table_id
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