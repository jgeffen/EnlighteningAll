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
	
	// TODO: Create Cropper class
	// TODO: Save image in temp directory until save.
	// TODO: Centralized verbiage used
	
	try {
		// Variable Defaults
		$cropper = filter_input(INPUT_POST, 'cropper', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$data    = json_decode(filter_input(INPUT_POST, 'data'), TRUE);
		
		// Check Required Data
		if(empty($data['table_name'])) throw new Exception('Table name not set.');
		if(empty($cropper['data'])) throw new Exception('No cropper data sent.');
		if(empty($cropper['source'])) throw new Exception('No source sent.');
		if(empty($cropper['format'])) throw new Exception('No format sent.');
		
		// Set Path & Filename
		$path     = Helpers::CreateDirectory(sprintf("/files/%s", $data['table_name']));
		$filename = basename($cropper['source']);
		
		// Set Imagick
		$imagick = new Imagick(sprintf("%s/%s", $path, $filename));
		$imagick->rotateImage(new ImagickPixel('transparent'), $cropper['data']['rotate']);
		$imagick->setImageBackgroundColor(new ImagickPixel(filter_input(INPUT_POST, 'backgroundColor') ?? '#FFFFFF'));
		$imagick->extentImage($cropper['data']['width'], $cropper['data']['height'], $cropper['data']['x'], $cropper['data']['y']);
		$imagick->setImageCompressionQuality(80);
		$imagick->setGravity(Imagick::GRAVITY_CENTER);
		
		// Variable Croppable
		$croppable = ($cropper['format']['width'] && $cropper['format']['height']);
		
		// Write Thumb
		$thumb = clone $imagick;
		$thumb->thumbnailImage($cropper['format']['width'], $cropper['format']['height'], $croppable);
		$thumb->writeImage(sprintf("%s/%s", Helpers::CreateDirectory(sprintf("%s/%s", $path, $cropper['type'])), $filename));
		
		// Write Small Thumb
		$thumb->thumbnailImage(floor($cropper['format']['width'] * 0.5), floor($cropper['format']['height'] * 0.5), $croppable);
		$thumb->writeImage(sprintf("%s/%s", Helpers::CreateDirectory(sprintf("%s/%s/thumbs", $path, $cropper['type'])), $filename));
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'source' => Helpers::WebRelativeFile(sprintf("%s/%s/%s", $path, $cropper['type'], $filename)),
			'thumb'  => Helpers::WebRelativeFile(sprintf("%s/%s/thumbs/%s", $path, $cropper['type'], $filename))
		);
	} catch(Exception $exception) {
		// Error Log
		Debug::Exception($exception);
		
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);