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
	 *
	 * @noinspection SqlResolve
	 */
	
	// Imports
	use Items\Enums\Types;
	
	// TODO: Save files in temp directory until save.
	// TODO: Centralized verbiage used
	
	try {
		// Variable Defaults
		$table_name = filter_input(INPUT_POST, 'table_name');
		$table_id   = filter_input(INPUT_POST, 'table_id', FILTER_VALIDATE_INT);
		$types      = array('pdf');
		
		// Check Required Data
		if(empty($table_name)) throw new Exception('Table name not set.');
		if(empty($table_id)) throw new Exception('Table name not set.');
		if(empty($_FILES['pdf'])) throw new Exception('No PDF provided.');
		if(!is_file($_FILES['pdf']['tmp_name'])) throw new Exception('PDF did not upload.');
		
		// Variable Defaults
		$path     = Helpers::CreateDirectory("/files/pdfs");
		$pathinfo = pathinfo($_FILES['pdf']['name']);
		$position = Database::Action("SELECT IFNULL(MAX(`position`), 0) + 1 FROM `pdfs` WHERE `table_name` = :table_name AND `table_id` = :table_id", array(
			'table_name' => $table_name,
			'table_id'   => $table_id
		))->fetch(PDO::FETCH_COLUMN);
		
		// Check Extension
		if(!in_array(strtolower($pathinfo['extension']), $types)) throw new Exception('This file type is not allowed.');
		
		// Check File for Errors
		switch($_FILES['pdf']['error']) {
			case 0:
				// Set Filename
				$filename = filter_input(INPUT_POST, 'rename', FILTER_SANITIZE_SPECIAL_CHARS) ?? $pathinfo['filename'];
				$filename = preg_replace('/[^A-Za-z0-9]/', ' ', $filename);
				$filename = preg_replace('/\s+/', ' ', $filename);
				$filename = str_replace(' ', '-', trim($filename));
				$filename = strtolower($filename);
				
				// Check Filename
				while(file_exists(sprintf("%s/%s.%s", $path, $filename, $pathinfo['extension']))) {
					$counter  = ($counter ?? 0) + 1;
					$current  = $current ?? $filename;
					$filename = sprintf("%s-%d", $current, $counter);
				}
				
				// Add Extension
				$filename = sprintf("%s.%s", $filename, $pathinfo['extension']);
				
				// Move Uploaded File
				move_uploaded_file($_FILES['pdf']['tmp_name'], sprintf("%s/%s", $path, $filename));
				
				// Update Database
				Database::Action("INSERT INTO `pdfs` SET `table_name` = :table_name, `table_id` = :table_id, `title` = :title, `description` = :description, `filename` = :filename, `position` = :position, `published` = :published, `published_date` = :published_date, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
					'table_name'     => $table_name,
					'table_id'       => $table_id,
					'title'          => ucwords(str_replace('-', ' ', pathinfo($filename, PATHINFO_FILENAME))),
					'description'    => NULL,
					'filename'       => $filename,
					'position'       => $position,
					'published'      => 1,
					'published_date' => date('Y-m-d'),
					'author'         => $admin->getId(),
					'user_agent'     => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
					'ip_address'     => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
				));
				
				// Log Action
				$admin->log(
					type         : Types\Log::CREATE,
					table_name   : Helpers::TableLookup($table_name),
					table_id     : $table_id,
					table_column : 'filename',
					filename     : $filename
				);
				
				// Set Response
				$json_response = array(
					'status'  => 'success',
					'message' => sprintf("%s uploaded successfully.", $filename)
				);
				break;
			default:
				throw new Exception(sprintf("Upload Error: %s", match ($_FILES['pdf']['error']) {
					1       => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
					2       => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
					3       => 'The uploaded file was only partially uploaded.',
					4       => 'No file was uploaded.',
					6       => 'Missing a temporary folder.',
					7       => 'Failed to write file to disk.',
					8       => 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.',
					default => 'An unknown error has occurred.',
				}));
		}
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception)
		);
	}
	
	// Output Response
	echo json_encode($json_response);