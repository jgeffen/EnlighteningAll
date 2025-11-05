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
	use Members\Messages;
	
	try {
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'DataTables loaded successfully.',
			'data'    => array_map(fn(Messages\Report $item) => array(
				'status'      => $item->getStatus()?->getValue(),
				'id'          => $item->getId(),
				'messages'    => $item->getMember()?->messages($item->getMemberReported())->count(),
				'member'      => $item->getMemberReported()?->getUsername(),
				'reported_by' => $item->getMember()?->getUsername(),
				'timestamp'   => $item->getLastTimestamp()->format('F j, Y, g:ia'),
				'item'        => $item->toArray(),
				'options'     => Render::GetTemplate('admin/items/member-reports/messages/options.twig', array(
					'id' => $item->getId()
				))
			), Messages\Report::FetchAll(Database::Action("SELECT * FROM `member_message_reports` ORDER BY `timestamp`")))
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