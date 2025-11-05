<?php
	// TODO: Create Cropper class
	// TODO: Save image in temp directory until save.
	// TODO: Centralized verbiage used
	
	// Imports
	use Items\Enums\Sizes;
	
	try {
		// Variable Defaults
		$member  = new Membership();
		$cropper = filter_input(INPUT_POST, 'cropper', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		
		// Check Required Data
		if(empty($cropper['data'])) throw new Exception('No cropper data sent.');
		if(empty($cropper['source'])) throw new Exception('No source sent.');
		
		// Check Member
		if($member::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Set Path & Filename
		$image_path = Helpers::CreateDirectory(sprintf("%s/files/members/%d/avatar", dirname(__DIR__, 5), $member->getId()));
		$filename   = basename($cropper['source']);
		
		// Set Imagick
		$imagick = new Imagick(sprintf("%s/%s", $image_path, $filename));
		$imagick->rotateImage(new ImagickPixel('transparent'), $cropper['data']['rotate']);
		$imagick->setImageBackgroundColor(new ImagickPixel(filter_input(INPUT_POST, 'backgroundColor') ?? '#FFFFFF'));
		$imagick->extentImage($cropper['data']['width'], $cropper['data']['height'], $cropper['data']['x'], $cropper['data']['y']);
		$imagick->setImageCompressionQuality(80);
		$imagick->setGravity(Imagick::GRAVITY_CENTER);
		
		// Iterate Over Sizes
		foreach(Sizes\Avatar::options() as $size) {
			// Write Thumb
			$thumb_path = Helpers::CreateDirectory(sprintf("%s/%d", $image_path, $size));
			$thumb      = clone $imagick;
			$thumb->thumbnailImage($size, $size, TRUE);
			$thumb->writeImage(sprintf("%s/%s", $thumb_path, $filename));
		}
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'source' => Helpers::WebRelativeFile(sprintf("%s/%s", $image_path, $filename)),
			'thumb'  => Helpers::WebRelativeFile(sprintf("%s/%d/%s", $image_path, Sizes\Avatar::LG->getValue(), $filename))
		);
	} catch(Exception|ImagickException|ImagickPixelException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);