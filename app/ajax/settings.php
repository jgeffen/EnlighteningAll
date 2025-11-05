<?php
	/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single web site may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	// Variable Defaults
	$bytes = maxFileUpload(FALSE);
	
	// Output Json
	echo json_encode(array(
		'settings' => array(
			'maxFilesize' => array(
				'B'  => floor($bytes),
				'KB' => floor($bytes / pow(1024, 1)),
				'MB' => floor($bytes / pow(1024, 2)),
				'GB' => floor($bytes / pow(1024, 3))
			)
		)
	));