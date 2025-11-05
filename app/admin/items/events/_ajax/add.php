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
		$page_url = Helpers::FormatPageURL(filter_input(INPUT_POST, 'page_title'), TRUE);
		$errors   = call_user_func(function() use ($page_url) {
			// Required Fields
			$required = array(
				'class_type'     => FILTER_DEFAULT,
				'page_title'     => FILTER_DEFAULT,
				'heading'        => FILTER_DEFAULT,
				'event_dates'    => FILTER_DEFAULT,
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
		
		// Time From
		$hour_from     = $_POST['event_hour_from']     ?? '';
		$minute_from   = $_POST['event_minute_from']   ?? '';
		$meridian_from = $_POST['event_meridian_from'] ?? '';
		
		// Time To
		$hour_to     = $_POST['event_hour_to']     ?? '';
		$minute_to   = $_POST['event_minute_to']   ?? '';
		$meridian_to = $_POST['event_meridian_to'] ?? '';
		
		// Assemble time ranges if valid
		$event_time_from = ($hour_from && $minute_from && $meridian_from)
			? sprintf('%s:%s %s', $hour_from, $minute_from, strtoupper($meridian_from))
			: NULL;
		
		$event_time_to = ($hour_to && $minute_to && $meridian_to)
			? sprintf('%s:%s %s', $hour_to, $minute_to, strtoupper($meridian_to))
			: NULL;
		
		// Optional: Combine into a single display value
		$event_time_display = ($event_time_from && $event_time_to)
			? $event_time_from . ' - ' . $event_time_to
			: ($event_time_from ?: $event_time_to ?: NULL);
		
		// Check Errors
		if(!empty($errors)) throw new FormException($errors, 'You are missing required fields.');
		
		// List Dates
		list($date_start, $date_end) = explode(' to ', filter_input(INPUT_POST, 'event_dates')) + array_fill(0, 2, filter_input(INPUT_POST, 'event_dates'));
		
		// Update Database
		$table_id = Database::Action("INSERT INTO `events` SET `category_id` = :category_id, `class_type` = :class_type, `page_title` = :page_title, `page_description` = :page_description, `heading` = :heading, `content` = :content, `youtube_id` = :youtube_id, `event_dates` = :event_dates, `event_times` = :event_times, `date_start` = :date_start, `date_end` = :date_end, `location` = :location, `price_text` = :price_text, `event_package_ids` = :event_package_ids, `filename` = :filename, `filename_alt` = :filename_alt, `page_url` = :page_url, `teacher_id` = :teacher_id, `accepting_rsvp` = :accepting_rsvp, `bookable` = :bookable, `display_rsvps` = :display_rsvps, `published` = :published, `published_date` = :published_date, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'category_id'       => filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT),
			'class_type'        => filter_input(INPUT_POST, 'class_type'),
			'page_title'        => filter_input(INPUT_POST, 'page_title'),
			'page_description'  => filter_input(INPUT_POST, 'page_description'),
			'heading'           => filter_input(INPUT_POST, 'heading'),
			'content'           => filter_input(INPUT_POST, 'content'),
			'youtube_id'        => Helpers::ExtractYouTubeID(filter_input(INPUT_POST, 'youtube_id')),
			'event_dates'       => filter_input(INPUT_POST, 'event_dates'),
			'event_times'       => $event_time_display,
			'date_start'        => $date_start,
			'date_end'          => $date_end,
			'location'          => filter_input(INPUT_POST, 'location'),
			'price_text'        => filter_input(INPUT_POST, 'price_text'),
			'event_package_ids' => json_encode(filter_input(INPUT_POST, 'event_package_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? array()),
			'filename'          => filter_input(INPUT_POST, 'filename'),
			'filename_alt'      => filter_input(INPUT_POST, 'filename_alt'),
			'page_url'          => $page_url,
			'teacher_id'        => filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT),
			'accepting_rsvp'    => filter_input(INPUT_POST, 'accepting_rsvp', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
			'bookable'          => filter_input(INPUT_POST, 'bookable', FILTER_VALIDATE_INT, array('options' => array('default' => 0))),
			'display_rsvps'     => filter_input(INPUT_POST, 'display_rsvps', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
			'published'         => filter_input(INPUT_POST, 'published', FILTER_VALIDATE_INT, array('options' => array('default' => 1))),
			'published_date'    => filter_input(INPUT_POST, 'published_date'),
			'author'            => $admin->getId(),
			'user_agent'        => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
			'ip_address'        => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
		), TRUE);
		
		// Log Action
		$admin->log(
			type       : Types\Log::CREATE,
			table_name : Tables\Website::EVENTS,
			table_id   : $table_id,
			payload    : $_POST
		);
		
		// Set Route(s)
		$parent_route ??= Router\Route::Init('categories', filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT));
		$parent_route ??= Router\Route::Init('events');
		
		// Update Routes
		$route_id = Database::Action("INSERT INTO `routes` SET `parent_route_id` = :parent_route_id, `table_name` = :table_name, `table_id` = :table_id, `page_url` = :page_url, `category` = :category, `categories` = :categories, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
			'parent_route_id' => $parent_route?->getId(),
			'table_name'      => 'events',
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