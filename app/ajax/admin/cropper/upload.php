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
	
	// TODO: Create Cropper class
	// TODO: Save image in temp directory until save.
	// TODO: Centralized verbiage used
	
	try {
		// Variable Defaults
		$data     = json_decode(filter_input(INPUT_POST, 'data'), TRUE);
		$template = json_decode(filter_input(INPUT_POST, 'template'), TRUE);
		$types    = array('jpeg', 'jpg', 'png');
		
		// Check Required Data
		if(empty($data['sizes'])) throw new Exception('No sizes were set.');
		if(empty($data['table_name'])) throw new Exception('Table name not set.');
		if(empty($template)) throw new Exception('Template not set.');
		
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
				$imagick->resizeImage(min(1600, $imagick->getImageWidth()), 0, Imagick::FILTER_LANCZOS, 1);
				$imagick->setImageCompressionQuality(80);
				$imagick->writeImage(sprintf("%s/%s", $path, $filename));
				
				// Create Thumbs
				foreach($data['sizes'] as $type => $format) {
					// Variable Croppable
					$croppable = ($format['width'] && $format['height']);
					
					// Clone Imagick
					$thumb = clone $imagick;
					
					// Check Croppable
					if($croppable) {
						// Crop Thumb
						$thumb->cropThumbnailImage(min($format['width'], $thumb->getImageWidth()), min($format['height'], $thumb->getImageHeight()));
						
						// List Dimensions
						list($width, $height) = array_values($thumb->getImageGeometry());
						
						// Center Thumb
						$thumb->setImageBackgroundColor(new ImagickPixel(filter_input(INPUT_POST, 'backgroundColor') ?? '#FFFFFF'));
						$thumb->extentImage($format['width'], $format['height'], floor(($width - $format['width']) / 2), floor(($height - $format['height']) / 2));
						$thumb->setImageCompressionQuality(80);
					} else {
						// Crop Thumb
						$thumb->thumbnailImage(min($format['width'], $thumb->getImageWidth()), 0);
					}
					
					// Write Thumb
					$thumb->writeImage(sprintf("%s/%s", Helpers::CreateDirectory(sprintf("%s/%s", $path, $type)), $filename));
					
					// Write Small Thumb
					$thumb->thumbnailImage(floor($format['width'] * 0.5), floor($format['height'] * 0.5), $croppable);
					$thumb->writeImage(sprintf("%s/%s", Helpers::CreateDirectory(sprintf("%s/%s/thumbs", $path, $type)), $filename));
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
					'sizes'    => array_map(function($type, $format) use ($path, $filename) {
						// Variable Croppable
						$croppable = ($format['width'] && $format['height']);
						
						return array(
							'aspect' => $croppable ? ($format['width'] / $format['height']) : 0,
							'format' => $format,
							'source' => Helpers::WebRelativeFile(sprintf("%s/%s", $path, $filename)),
							'title'  => Helpers::PrettyTitle($type),
							'thumb'  => Helpers::WebRelativeFile(sprintf("%s/%s/%s", $path, $type, $filename)),
							'type'   => $type
						);
					}, array_keys($data['sizes']), $data['sizes'])
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
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception)
		);
	}
	
	// Output Response
	echo json_encode($json_response);