<?php
	// TODO: Create Cropper class
	// TODO: Save image in temp directory until save.
	// TODO: Centralized verbiage used
	
	try {
		// Variable Defaults
		$member = new Membership();
		$data   = json_decode(filter_input(INPUT_POST, 'data'), TRUE);
		$types  = array('jpeg', 'jpg', 'png');
		
		// Set Type/Format
		$data['type']   = 'featured';
		$data['format'] = array('width' => 900, 'height' => 900);
		
		// Check Member
		if($member::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Set Path
		$path = Helpers::CreateDirectory(sprintf("%s/files/members/%d", filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'), $member->getId()));
		
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
				
				// Optimize Image
				$imagick->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
				$imagick->setImageCompressionQuality(50);
				$imagick->resizeImage(min(1200, $imagick->getImageWidth()), 0, Imagick::FILTER_LANCZOS, 1);
				
				// Switch Image Mime Type
				switch($imagick->getImageMimeType()) {
					case 'image/jpeg':
						$imagick->setImageFormat('jpeg');
						$imagick->setSamplingFactors(array('2x2', '1x1', '1x1'));
						
						$profiles = $imagick->getImageProfiles('icc');
						
						$imagick->stripImage();
						
						!empty($profiles) && $imagick->profileImage('icc', $profiles['icc']);
						
						$imagick->setInterlaceScheme(Imagick::INTERLACE_JPEG);
						$imagick->setColorspace(Imagick::COLORSPACE_SRGB);
						break;
					case 'image/png':
						$imagick->setImageFormat('png');
						break;
				}
				
				// Write Image
				$imagick->writeImage(sprintf("%s/%s", $path, $filename));
				
				// Clone Imagick
				$thumb = clone $imagick;
				
				// Crop Thumb
				$thumb->cropThumbnailImage($data['format']['width'], $data['format']['height']);
				$thumb->setImageBackgroundColor(new ImagickPixel(filter_input(INPUT_POST, 'backgroundColor') ?? '#FFFFFF'));
				$thumb->extentImage($data['format']['width'], $data['format']['height'], 0, 0);
				
				// Write Thumb
				$thumb->writeImage(sprintf("%s/%s", Helpers::CreateDirectory(sprintf("%s/%s", $path, $data['type'])), $filename));
				
				// Write Small Thumb
				$thumb->thumbnailImage($data['format']['width'] * 0.5, $data['format']['height'] * 0.5);
				$thumb->writeImage(sprintf("%s/%s", Helpers::CreateDirectory(sprintf("%s/thumbs", $path)), $filename));
				
				// Set Response
				$json_response = array(
					'status'   => 'success',
					'message'  => sprintf("%s uploaded successfully.", $filename),
					'datetime' => $datetime,
					'edit'     => FALSE,
					'filename' => $filename,
					'sizes'    => array(
						array(
							'aspect' => 1,
							'format' => $data['format'],
							'source' => Helpers::WebRelativeFile(sprintf("%s/%s", $path, $filename)),
							'title'  => '',
							'thumb'  => Helpers::WebRelativeFile(sprintf("%s/%s/%s", $path, $data['type'], $filename)),
							'type'   => $data['type']
						)
					)
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
					default => 'An unknown error has occurred.'
				}));
		}
	} catch(Exception|ImagickException|ImagickPixelException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);