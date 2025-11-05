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
	
	// TODO: Create uploader class
	// TODO: Save image in temp directory until save.
	// TODO: Centralized verbiage used
	
	try {
		// Variable Defaults
		$data  = json_decode(filter_input(INPUT_POST, 'data'), TRUE);
		$types = array('jpeg', 'jpg', 'png', 'gif');
		
		// Check Required Data
		if(empty($data['table_name'])) throw new Exception('Table name not set.');
		
		// Set Path
		$path = Helpers::CreateDirectory(sprintf("/files/%s", $data['table_name']));
		
		// Check Upload
		if(empty($_FILES['image'])) throw new Exception('No image provided.');
		if(!is_file($_FILES['image']['tmp_name'])) throw new Exception('Image did not upload.');
		
		// Variable Defaults
		$pathinfo = pathinfo($_FILES['image']['name']);
		$datetime = @exif_read_data($_FILES['image']['tmp_name'])['DateTimeOriginal'] ?? date('Y-m-d H:i:s');
		
		// Check Extension
		if(!in_array(strtolower($pathinfo['extension']), $types)) throw new Exception('This file type is not allowed.');
		
		// Check File for Errors
		switch($_FILES['image']['error']) {
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
				
				// Set Imagick
				$imagick = new Imagick($_FILES['image']['tmp_name']);
				
				// Switch MIME Type
				switch($imagick->getImageMimeType()) {
					case 'image/gif':
						// Write Image
						move_uploaded_file($_FILES['image']['tmp_name'], sprintf("%s/%s", $path, $filename));
						break;
					default:
						// Fix Orientation
						switch($imagick->getImageOrientation()) {
							case Imagick::ORIENTATION_TOPRIGHT:
								$imagick->flopImage();
								break;
							case Imagick::ORIENTATION_BOTTOMRIGHT:
								$imagick->rotateImage('#000', 180);
								break;
							case Imagick::ORIENTATION_BOTTOMLEFT:
								$imagick->flopImage();
								$imagick->rotateImage('#000', 180);
								break;
							case Imagick::ORIENTATION_LEFTTOP:
								$imagick->flopImage();
								$imagick->rotateImage('#000', -90);
								break;
							case Imagick::ORIENTATION_RIGHTTOP:
								$imagick->rotateImage('#000', 90);
								break;
							case Imagick::ORIENTATION_RIGHTBOTTOM:
								$imagick->flopImage();
								$imagick->rotateImage('#000', 90);
								break;
							case Imagick::ORIENTATION_LEFTBOTTOM:
								$imagick->rotateImage('#000', -90);
								break;
							case Imagick::ORIENTATION_TOPLEFT:
							default:
								break;
						}
						
						// Write Image
						$imagick->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
						$imagick->resizeImage(min(1600, $imagick->getImageWidth()), min(1200, $imagick->getImageHeight()), Imagick::FILTER_LANCZOS, 1, TRUE);
						$imagick->setImageCompressionQuality(80);
						$imagick->writeImage(sprintf("%s/%s", $path, $filename));
				}
				
				// Check Item
				if(!empty($data['item'])) {
					// Update Database
					Database::Action(sprintf("UPDATE `%s` SET `filename` = :filename WHERE `id` = :id", $data['table_name']), array(
						'filename' => $filename,
						'id'       => $data['item']['id']
					));
				}
				
				// Log Action
				$admin->log(
					type       : Types\Log::CREATE,
					table_name : Helpers::TableLookup($data['table_name']),
					table_id   : $data['item']['id'] ?? NULL,
					filename   : $filename
				);
				
				// Set Response
				$json_response = array(
					'status'   => 'success',
					'message'  => sprintf("%s uploaded successfully.", $filename),
					'datetime' => $datetime,
					'edit'     => !empty($data['item']),
					'filename' => $filename,
					'source'   => Helpers::WebRelativeFile(sprintf("%s/%s", $path, $filename)),
					'title'    => $data['title'] ?? ''
				);
				break;
			default:
				throw new Exception(sprintf("Upload Error: %s", match ($_FILES['image']['error']) {
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
	} catch(Exception|ImagickException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);