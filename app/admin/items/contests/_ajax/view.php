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
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'DataTables loaded successfully.',
			'data'    => array_map(fn(Items\Contest $item) => array(
				'id'         => $item->getId(),
				'page_title' => Helpers::Truncate($item->getTitle(), 50),
				'content'    => Helpers::Truncate($item->getContent(), 50),
				'entries'    => $item->getPosts()->count(),
				'winners'    => $item->getWinners()->count() . ' / ' . $item->getNumberOfWinners(),
				'timestamp'  => array(
					'value' => $item->getLastTimestamp()->format('Y-m-d H:i:s'),
					'label' => $item->getLastTimestamp()->format('Y-m-d H:i:s')
				),
				'published'  => $item->isPublished(),
				'item'       => $item->toArray(),
				'options'    => Render::GetTemplate('admin/items/contests/options.twig', array('id' => $item->getId())),
				'status'     => array(
					'awaiting' => $item->isAwaiting(),
					'upcoming' => $item->isUpcoming(),
					'past'     => $item->isPast(),
					'current'  => $item->isCurrent()
				)
			), Items\Contest::FetchAll(Database::Action("SELECT * FROM `contests` ORDER BY CURDATE() BETWEEN `date_start` AND `date_end` DESC, `date_start` DESC, `date_end`")))
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception),
			'data'    => array()
		);
	}
	
	// Output Response
	echo json_encode($json_response);