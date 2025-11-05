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
	
	// Imports
	use Items\Enums\Tables;
	use Items\Enums\Types;
	
	try {
		// Fetch Item
		$item = Secrets\Photographers\Package::Init($dispatcher->getTableId());
		
		// Check Required Data
		if(is_null($item)) throw new Exception('Item not found in database.');
		
		// Remove File
		Admin\File::Remove($item->getFilename(), Tables\Secrets\Photographers::PACKAGES, $item->getId(), NULL);
		
		// Remove Images
		array_map(function($image) use ($admin) {
			// Remove File
			Admin\File::Remove($image->getFilename(), Tables\Website::IMAGES, $image->getId(), NULL);
		}, Items\Image::FetchAll(Database::Action("SELECT * FROM `images` WHERE `table_name` = :table_name AND `table_id` = :table_id", array(
			'table_name' => 'photographer_packages',
			'table_id'   => $item->getId()
		))));
		
		// Remove PDFs
		array_map(function($pdf) use ($admin) {
			// Remove File
			Admin\File::Remove($pdf->getFilename(), Tables\Website::PDFS, $pdf->getId(), NULL);
		}, Items\PDF::FetchAll(Database::Action("SELECT * FROM `pdfs` WHERE `table_name` = :table_name AND `table_id` = :table_id", array(
			'table_name' => 'photographer_packages',
			'table_id'   => $item->getId()
		))));
		
		// Update Database
		Database::Action("DELETE FROM `pdfs` WHERE `table_name` = :table_name AND `table_id` = :table_id", array(
			'table_name' => 'photographer_packages',
			'table_id'   => $item->getId()
		));
		Database::Action("DELETE FROM `images` WHERE `table_name` = :table_name AND `table_id` = :table_id", array(
			'table_name' => 'photographer_packages',
			'table_id'   => $item->getId()
		));
		Database::Action("DELETE FROM `routes` WHERE `table_name` = :table_name AND `table_id` = :table_id", array(
			'table_name' => 'photographer_packages',
			'table_id'   => $item->getId()
		));
		Database::Action("DELETE FROM `photographer_packages` WHERE `id` = :id", array('id' => $item->getId()));
		
		// Log Action
		$admin->log(
			type       : Types\Log::DELETE,
			table_name : Tables\Secrets\Photographers::PACKAGES,
			table_id   : $item->getId(),
			payload    : $_POST
		);
		
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'Deleted successfully.'
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