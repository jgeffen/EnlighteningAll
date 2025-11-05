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
	
	// Variable Defaults
	$width     = filter_input(INPUT_GET, 'width', FILTER_VALIDATE_INT, array('options' => array('min_range' => 50, 'max_range' => 2400, 'default' => 900)));
	$height    = filter_input(INPUT_GET, 'height', FILTER_VALIDATE_INT, array('options' => array('min_range' => 50, 'max_range' => 2400, 'default' => 600)));
	$pixel     = filter_input(INPUT_GET, 'pixel') ?? '#FFFFFF';
	$text      = filter_input(INPUT_GET, 'text') ?? 'Cover the Entire Image';
	$fillColor = filter_input(INPUT_GET, 'fillColor') ?? '#000000';
	$font      = filter_input(INPUT_GET, 'color') ?? 'Bookman-DemiItalic';
	$fontSize  = filter_input(INPUT_GET, 'fontSize', FILTER_VALIDATE_INT, array('options' => array('min_range' => 4, 'max_range' => 72, 'default' => 36)));
	
	try {
		// Set Text
		$imagickDraw = new ImagickDraw();
		$imagickDraw->setFillColor($fillColor);
		$imagickDraw->setFont($font);
		$imagickDraw->setFontSize($fontSize);
		
		// Set Image
		$imagick = new Imagick();
		$imagick->newImage($width, $height, new ImagickPixel($pixel));
		$imagick->setImageFormat('png');
		
		// Set Metrics
		$fontMetrics = $imagick->queryFontMetrics($imagickDraw, $text);
		
		// Annotate Image
		$imagick->annotateImage(
			$imagickDraw,
			$width / 2 - $fontMetrics['textWidth'] / 2,
			$height / 2 + $fontMetrics['textHeight'] / 2 + $fontMetrics['descender'],
			0,
			$text
		);
		
		header('Content-type: image/png');
		echo $imagick;
	} catch(Exception $exception) {
		echo $exception->getMessage();
	}