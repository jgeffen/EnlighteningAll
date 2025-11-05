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
	
	try {
		// Variable Defaults
		$path  = Helpers::CreateDirectory('/files/documents');
		$types = array('php');
		
		// Check Deletion
		if(!filter_input(INPUT_POST, 'delete')) {
			// Check Upload
			if(empty($_FILES['file'])) throw new Exception('No file provided.');
			if(!is_file($_FILES['file']['tmp_name'])) throw new Exception('File did not upload.');
			
			// Variable Defaults
			$pathinfo = pathinfo($_FILES['file']['name']);
			
			// Check Extension
			if(in_array(strtolower($pathinfo['extension']), $types)) throw new Exception('This file type is not allowed.');
			
			// Check File for Errors
			switch($_FILES['file']['error']) {
				case 0:
					// Set Filename
					$filename = $pathinfo['filename'];
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
					
					// Move File
					move_uploaded_file($_FILES['file']['tmp_name'], sprintf("%s/%s", $path, $filename));
					
					// Set Response
					$json_response = array(
						'status'  => 'success',
						'message' => sprintf("%s uploaded successfully.", $filename)
					);
					break;
				default:
					throw new Exception(sprintf("Upload Error: %s", match ($_FILES['file']['error']) {
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
		} else {
			// Variable Defaults
			$filename = filter_input(INPUT_POST, 'filename');
			
			// Check Upload
			if(empty($filename)) throw new Exception('No file provided.');
			
			Helpers::RemoveFile($path, $filename);
			
			// Set Response
			$json_response = array(
				'status'  => 'success',
				'message' => sprintf("%s deleted successfully.", $filename)
			);
		}
	} catch(Error|Exception $exception) {
		// Error Log
		error_log(print_r($exception, TRUE));
		
		// Set Response
		http_response_code(500);
		
		// Output Message
		exit($exception->getMessage() ?: 'An unknown error has occurred.');
	}
	
	// Output Response
	echo json_encode($json_response);