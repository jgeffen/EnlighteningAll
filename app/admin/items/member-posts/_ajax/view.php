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
	use Items\Members;
	
	try {
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'DataTables loaded successfully.',
			'data'    => array_map(fn(Members\Post $item) => array(
				'id'         => $item->getId(),
				'type'       => $item->getType()?->getLabel(),
				'visibility' => $item->getVisibility()?->getLabel(),
				'heading'    => $item->getHeading(50),
				'username'   => $item->getMember()?->getUsername(),
				'image'      => $item->hasImage() ? '<i class="fa-solid fa-badge-check fa-2x text-success"></i>' : '<i class="fa-solid fa-2x fa-badge text-muted"></i>',
				'timestamp'  => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
				'item'       => $item->toArray(),
				'options'    => Render::GetTemplate('admin/items/member-posts/options.twig', array('id' => $item->getId()))
			), Members\Post::FetchAll(Database::Action("SELECT * FROM `member_posts` WHERE `member_id` = :member_id OR ISNULL(:member_id) ORDER BY `timestamp` DESC", array(
				'member_id' => $dispatcher->getTableId()
			))))
		);
	} catch(Error|PDOException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception),
			'data'    => array()
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage(),
			'data'    => array()
		);
	}
	
	// Output Response
	echo json_encode($json_response);