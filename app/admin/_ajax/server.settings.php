<?php
	// Variable Defaults
	$bytes = max_file_upload_in_bytes(FALSE);
	
	// Output Response
	echo json_encode(array(
		'maxFilesize' => array(
			'B'  => floor($bytes),
			'KB' => floor($bytes / pow(1024, 1)),
			'MB' => floor($bytes / pow(1024, 2)),
			'GB' => floor($bytes / pow(1024, 3))
		)
	));