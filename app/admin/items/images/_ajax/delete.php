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
		// Variable Defaults
		$_POST = filter_var_array(json_decode(file_get_contents('php://input'), TRUE), array(
			'table_name' => FILTER_DEFAULT,
			'table_id'   => FILTER_VALIDATE_INT
		));
		
		// Fetch Item
		$item = Items\Image::Fetch(Database::Action("SELECT `id`, `table_name`, `table_id`, `filename` FROM `images` WHERE `id` = :id AND `table_name` = :table_name AND `table_id` = :table_id", array(
			'id'         => $dispatcher->getTableId(),
			'table_name' => Helpers::TableLookup($_POST['table_name'])?->getValue(),
			'table_id'   => $_POST['table_id']
		)));
		
		// Check Required Data
		if(empty($item)) throw new Exception('Item not found in database.');
		
		// Remove File
		Admin\File::Remove($item->getFilename(), Tables\Website::IMAGES, $item->getId(), NULL);
		
		// Update Database
		Database::Action("DELETE FROM `images` WHERE `id` = :id", array('id' => $item->getId()));
		
		// Log Action
		$admin->log(
			type         : Types\Log::DELETE,
			table_name   : Helpers::TableLookup($_POST['table_name']),
			table_id     : $_POST['table_id'],
			table_column : 'filename',
			filename     : $item->getFilename()
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