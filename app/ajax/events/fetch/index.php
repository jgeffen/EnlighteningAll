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
	 */
	
	try {
		// Set Response
		$json_response = array_map(fn(Items\Event $item) => array(
			'id'     => $item->getId(),
			'title'  => $item->getHeading(),
			'allDay' => TRUE,
			'start'  => $item->getStartDate()->format('Y-m-d'),
			'end'    => $item->getEndDate()->format('Y-m-d'),
			'url'    => $item->getLink(),
			'modal'  => Render::GetTemplate('events/calendar/modal.twig', array(
				'alt'         => $item->getAlt(),
				'date'        => $item->getDate(),
				'heading'     => $item->getHeading(),
				'link'        => $item->getLink(),
				'location'    => $item->getLocation(),
				'event_times' => $item->getEventTimes(),
				'price_text'  => $item->getPriceText(),
				'text'        => $item->getContentPreview()
			))
		), Database::Action("SELECT * FROM `events` WHERE `published` IS TRUE AND (`date_start` BETWEEN :date_start AND :date_end) OR (`date_end` BETWEEN :date_start AND :date_end) ORDER BY `date_start`, `date_end` DESC, `page_title`", array(
			'date_start' => filter_input(INPUT_POST, 'start'),
			'date_end'   => filter_input(INPUT_POST, 'end')
		))->fetchAll(PDO::FETCH_CLASS, Items\Event::class));
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);