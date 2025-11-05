<?php
	// TODO: Create Cropper class
	// TODO: Save image in temp directory until save.
	// TODO: Centralized verbiage used
	
	try {
		// Variable Defaults
		$member  = new Membership();
		$cropper = filter_input(INPUT_POST, 'cropper', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		
		// Check Required Data
		if(empty($cropper['data'])) throw new Exception('No cropper data sent.');
		if(empty($cropper['source'])) throw new Exception('No source sent.');
		
		// Set Type/Format
		$cropper['type']   = 'featured';
		$cropper['format'] = array('width' => 900, 'height' => 900);
		
		// Check Member
		if($member::LoggedIn(FALSE)) throw new Exception('You must be logged in to perform this action.');
		
		// Set Path & Filename
		$path     = Helpers::CreateDirectory(sprintf("%s/files/members/%d", filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'), $member->getId()));
		$filename = basename($cropper['source']);
		
		// Set Imagick
		$imagick = new Imagick(sprintf("%s/%s", $path, $filename));
		$imagick->rotateImage(new ImagickPixel('transparent'), $cropper['data']['rotate']);
		$imagick->setImageBackgroundColor(new ImagickPixel(filter_input(INPUT_POST, 'backgroundColor') ?? '#FFFFFF'));
		$imagick->extentImage($cropper['data']['width'], $cropper['data']['height'], $cropper['data']['x'], $cropper['data']['y']);
		$imagick->setImageCompressionQuality(80);
		$imagick->setGravity(Imagick::GRAVITY_CENTER);
		
		// Write Thumb
		$thumb = clone $imagick;
		$thumb->thumbnailImage($cropper['format']['width'], $cropper['format']['height'], TRUE);
		$thumb->writeImage(sprintf("%s/%s", Helpers::CreateDirectory(sprintf("%s/%s", $path, $cropper['type'])), $filename));
		
		// Write Small Thumb
		$thumb->thumbnailImage($cropper['format']['width'] * 0.5, $cropper['format']['height'] * 0.5, TRUE);
		$thumb->writeImage(sprintf("%s/%s", Helpers::CreateDirectory(sprintf("%s/thumbs", $path)), $filename));
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'source' => Helpers::WebRelativeFile(sprintf("%s/%s", $path, $filename)),
			'thumb'  => Helpers::WebRelativeFile(sprintf("%s/%s/%s", $path, $cropper['type'], $filename))
		);
	} catch(Exception | ImagickException | ImagickPixelException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);