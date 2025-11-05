<?php
	/*
	Copyright (c) 2022 Daerik.com
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
	
	/* Check for Error */
	if(!isset($_FILES['image']['error'])) {
		/* Response is Error */
		$response = array(
			'status'  => 'error',
			'message' => 'No data sent.'
		);
		
		/* Print JSON Response */
		echo json_encode($response);
		exit();
	}
	
	/* Set Image Path */
	$imagePath = create_directory($_SERVER['DOCUMENT_ROOT'] . '/files/tinymce/');
	
	/* Allowed Extension */
	$allowedExtensions = array('jpeg', 'jpg', 'png');
	$extension         = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
	
	/* Check Allowed Extensions */
	if(in_array($extension, $allowedExtensions)) {
		/* Check File for Errors */
		if($_FILES['image']['error'] === 0) {
			/* Set File Name */
			$fileName = @$_POST['rename_file'] ?: pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
			$fileName = preg_replace('/[^A-Za-z0-9]/', ' ', $fileName);
			$fileName = preg_replace('/\s+/', ' ', $fileName);
			$fileName = str_replace(' ', '-', trim($fileName));
			$fileName = strtolower($fileName);
			
			/* Use Unique File Name */
			while(file_exists($imagePath . $fileName . '.' . $extension)) {
				$fileCount       = isset($fileCount) ? ++$fileCount : 1;
				$currentFileName = @$currentFileName ?: $fileName;
				$fileName        = $currentFileName . '-' . $fileCount;
			}
			
			/* Add Extension */
			$fileName = $fileName . '.' . $extension;
			
			/* Move Uploaded File */
			move_uploaded_file($_FILES['image']['tmp_name'], $imagePath . $fileName);
			
			/* New Imagick Class */
			try {
				$image = new Imagick($imagePath . $fileName);
				
				/* Fix Orientation */
				switch($image->getImageOrientation()) {
					case Imagick::ORIENTATION_TOPLEFT:
						break;
					case Imagick::ORIENTATION_TOPRIGHT:
						$image->flopImage();
						break;
					case Imagick::ORIENTATION_BOTTOMRIGHT:
						$image->rotateImage('#000', 180);
						break;
					case Imagick::ORIENTATION_BOTTOMLEFT:
						$image->flopImage();
						$image->rotateImage('#000', 180);
						break;
					case Imagick::ORIENTATION_LEFTTOP:
						$image->flopImage();
						$image->rotateImage('#000', -90);
						break;
					case Imagick::ORIENTATION_RIGHTTOP:
						$image->rotateImage('#000', 90);
						break;
					case Imagick::ORIENTATION_RIGHTBOTTOM:
						$image->flopImage();
						$image->rotateImage('#000', 90);
						break;
					case Imagick::ORIENTATION_LEFTBOTTOM:
						$image->rotateImage('#000', -90);
						break;
					default: // Invalid orientation
						break;
				}
				$image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
				
				/* Use Imagick to Get Dimensions */
				list($width, $height) = array_values($image->getImageGeometry());
				
				/* Maximum Width for Original */
				if($width > 1024) {
					/* Resize and Get New Dimensions */
					$image->resizeImage(1024, 0, Imagick::FILTER_LANCZOS, 1);
					list($width, $height) = array_values($image->getImageGeometry());
				}
				
				/* Check for JPEG */
				if(in_array($extension, array('jpeg', 'jpg', 'png'))) {
					/* Compress Image */
					$image->setImageCompression(Imagick::COMPRESSION_JPEG);
					$image->setImageCompressionQuality(60);
				}
				
				/* Write Image */
				$image->writeImage($imagePath . $fileName);
				
				/* Response is Success */
				$response = array(
					'status' => 'success',
					'url'    => str_replace($_SERVER['DOCUMENT_ROOT'], '', $imagePath) . $fileName
				);
			} catch(ImagickException $error) {
				/* Response is Error */
				$response = array(
					'status'  => 'error',
					'message' => $error->getMessage() ?: 'ImagickException: This image may be corrupted.'
				);
			}
		} else {
			/* Response is Error */
			$response = array(
				'status'  => 'error',
				'message' => 'ERROR Return Code: ' . $_FILES['image']['error'] . ' - '
			);
			
			/* Set Error Code */
			switch($_FILES['image']['error']) {
				case 1:
					$response['message'] .= 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
					break;
				case 2:
					$response['message'] .= 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
					break;
				case 3:
					$response['message'] .= 'The uploaded file was only partially uploaded.';
					break;
				case 4:
					$response['message'] .= 'No file was uploaded.';
					break;
				case 6:
					$response['message'] .= 'Missing a temporary folder.';
					break;
				case 7:
					$response['message'] .= 'Failed to write file to disk.';
					break;
				case 8:
					$response['message'] .= 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.';
					break;
				default:
					$response['message'] .= 'An unknown error has occurred.';
					break;
			}
		}
	} else {
		/* Response is Error */
		$response = array(
			'status'  => 'error',
			'message' => 'Please only use ' . strtoupper(implode(', ', $allowedExtensions)) . ' type images. ' . strtoupper($extension) . ' is not allowed.'
		);
	}
	
	/* Return Response */
	echo json_encode($response);