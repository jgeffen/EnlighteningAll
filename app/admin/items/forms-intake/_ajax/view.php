<?php
	/*
	Copyright (c) 2021, 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	// TODO: Build out class to include HTML snippets
	
	try {
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'DataTables loaded successfully.',
			'data'    => array_map(fn(Items\Forms\Intake $item) => array(
				'id'         => $item->getId(),
				'first_name' => $item->getFirstName(),
				'last_name'  => $item->getLastName(),
				'email'      => $item->getEmail(),
				'timestamp'  => $item->getLastTimestamp()->format('F j, Y, g:ia'),
				'item'       => $item->toArray(),
				'options'    => Render::GetTemplate('admin/items/forms-intake/options.twig', array('id' => $item->getId()))
			), Items\Forms\Intake::FetchAll(Database::Action("SELECT * FROM `forms` WHERE `type` = 'intake' ORDER BY `timestamp` DESC")))
		);
	} catch(Error|Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage(),
			'data'    => array()
		);
	}
	
	// Output Response
	echo json_encode($json_response);