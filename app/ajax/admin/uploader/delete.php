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
	
	try {
		// Variable Defaults
		$data = json_decode(filter_input(INPUT_POST, 'data'), TRUE);
		
		// Check Required Data
		if(empty($data['item']['filename'])) throw new Exception('Filename is missing.');
		if(empty($data['table_name'])) throw new Exception('Table name is missing.');
		if(empty($data['item']['id'])) throw new Exception('Table ID is missing.');
		if(empty($data['column'])) throw new Exception('Table column is missing.');
		
		// Remove File
		Admin\File::Remove($data['item']['filename'], Helpers::TableLookup($data['table_name']), $data['item']['id'], $data['column'] ?? 'filename');
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Removed file successfully.'
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);