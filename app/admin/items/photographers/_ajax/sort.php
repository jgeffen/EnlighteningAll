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
		// Check Rows
		if(filter_input(INPUT_POST, 'rows', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)) {
			// Iterate Over Values
			foreach($_POST['rows'] as $data) {
				// Update Database
				Database::Action("UPDATE `photographers` SET `position` = :position WHERE `id` = :id", array(
					'position' => $data['position'],
					'id'       => $data['id']
				));
			}
			
			// Set Response
			$json_response = array(
				'status'  => 'success',
				'message' => 'Database successfully updated.'
			);
		} else {
			// Set Response
			$json_response = array(
				'status'  => 'error',
				'message' => 'No row data sent.'
			);
		}
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);