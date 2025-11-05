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
	
	// Imports
	use Items\Members;
	
	try {
		// Variable Defaults
		$item = Members\Post::Init($dispatcher->getId());
		
		// Check Post
		if(is_null($item)) throw new Exception('Post cannot be found.');
		
		// Set Response
		$json_response = array(
			'status' => 'success',
			'data'   => $item->toArray(),
			'modal'  => Template::Render('admin/items/member-posts/modals/delete.twig')
		);
	} catch(Error $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception)
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output JSON
	echo json_encode($json_response);