<?php
	// TODO: Create Cropper class
	// TODO: Save image in temp directory until save.
	// TODO: Centralized verbiage used
	
	// Imports
	use Items\Enums\Sizes;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Check Logged In
		if(Membership::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Variable Defaults
		$member = new Membership();
		$types  = array('jpeg', 'jpg', 'png');
		
		// Check Account Approval
		if(!$member->isApproved() && $member->settings()->getValue('account_approval_required_avatar')) {
			throw new Exception('Your account is pending approval.');
		}
		
		// Set Path
		$image_path = Helpers::CreateDirectory(sprintf("%s/files/members/%d/avatar", dirname(__DIR__, 5), $member->getId()));
		
		// Check Upload
		if(empty($_FILES['image'])) throw new Exception('No image provided.');
		if(!is_file($_FILES['image']['tmp_name'])) throw new Exception('Image did not upload.');
		
		// Check Extension
		$pathinfo = pathinfo($_FILES['image']['name']);
		if(!in_array(strtolower($pathinfo['extension']), $types)) throw new Exception('This file type is not allowed.');
		
		// Set Exif
		$datetime = exif_read_data($_FILES['image']['tmp_name'])['DateTimeOriginal'] ?? date('Y-m-d H:i:s');
		
		// Check File for Errors
		switch($_FILES['image']['error']) {
			case 0:
				$filename = strtolower(uniqid());
				
				// Check Filename
				while(file_exists(sprintf("%s/%s.%s", $image_path, $filename, $pathinfo['extension']))) {
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
				$imagick->resizeImage(min(1200, $imagick->getImageWidth()), 0, Imagick::FILTER_LANCZOS, 1);
				$imagick->setImageCompressionQuality(80);
				$imagick->writeImage(sprintf("%s/%s", $image_path, $filename));
				
				// Iterate Over Sizes
				foreach(Sizes\Avatar::options() as $size) {
					// Clone Imagick
					$thumb      = clone $imagick;
					$thumb_path = Helpers::CreateDirectory(sprintf("%s/%d", $image_path, $size));
					
					// Crop/Write Thumb
					$thumb->cropThumbnailImage($size, $size);
					$thumb->setImageCompressionQuality(80);
					$thumb->writeImage(sprintf("%s/%s", $thumb_path, $filename));
				}
				
				// Check Auto Approval
				if($member->settings()->getValue('avatar_approval_automatic')) {
					if($member->isApproved() || !$member->settings()->getValue('account_approval_required_avatar_approval')) {
						$auto_approve = TRUE;
					}
				}
				
				// Log Action
				$member->log()->setData(
					type       : Types\Log::CREATE,
					table_name : Tables\Members::AVATARS,
					table_id   : Database::Action("INSERT INTO `member_avatars` SET `member_id` = :member_id, `filename` = :filename, `approved` = :approved, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
						'member_id'  => $member->getId(),
						'filename'   => $filename,
						'approved'   => $auto_approve ?? FALSE,
						'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
						'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
					), TRUE)
				)->execute();
				
				// Set Response
				$json_response = array(
					'status'   => 'success',
					'message'  => sprintf("%s uploaded successfully.", $filename),
					'datetime' => $datetime,
					'edit'     => TRUE,
					'filename' => $filename,
					'sizes'    => array(
						array(
							'aspect' => 1,
							'format' => array('width' => Sizes\Avatar::LG->getValue(), 'height' => Sizes\Avatar::LG->getValue()),
							'source' => Helpers::WebRelativeFile(sprintf("%s/%s", $image_path, $filename)),
							'title'  => '',
							'thumb'  => Helpers::WebRelativeFile(sprintf("%s/%d/%s", $image_path, Sizes\Avatar::LG->getValue(), $filename)),
							'type'   => 'avatar'
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
	} catch(Exception|ImagickException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);