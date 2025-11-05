<?php
	/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single web site may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	// TODO: Handle calendar errors
	
	try {
		// Fetch/Set Items
		$items = array_map(function($item) {
			// Modify Item
			$item = array_merge($item, array(
				'alt'   => htmlentities($item['filename_alt'] ?: $item->getTitle(), ENT_QUOTES),
				'text'  => shortdesc($item->getContent(), 400),
				'link'  => sprintf("/club-swinkster/%s.html", $item['page_url']),
				'thumb' => Render::Images(sprintf("/files/swinkster_events/thumbs/%s", $item['filename'])),
				'time'  => !empty($item['all_day']) ? 'All Day' : sprintf("%s - %s", date('g:ia', strtotime($item['date_start'])), date('g:ia', strtotime($item['date_end']))),
				'date'  => call_user_func(function($start, $end) {
					return date('Y-m-d', $start) == date('Y-m-d', $end) ? date('d M', $start) : sprintf("%s - %s", date('d M', $start), date('d M', $end));
				}, strtotime($item['date_start']), strtotime($item['date_end']))
			));
			
			// Return Item
			return array(
				'id'     => $item['id'],
				'title'  => $item->getTitle(),
				'allDay' => TRUE,
				'start'  => date('c', strtotime($item['date_start'])),
				'end'    => date('c', strtotime('+1 Day', strtotime($item['date_end']))),
				'url'    => $item['link'],
				'modal'  => include('html/modal.php')
			);
		}, Database::Action("SELECT * FROM `swinkster_events` WHERE (`date_start` BETWEEN :date_start AND :date_end) OR (`date_end` BETWEEN :date_start AND :date_end) ORDER BY `date_start`, `date_end` DESC, `page_title`", array(
			'date_start' => filter_input(INPUT_POST, 'start'),
			'date_end'   => filter_input(INPUT_POST, 'end')
		))->fetchAll(PDO::FETCH_ASSOC));
		
		// Set Response
		$json_response = $items;
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage()
		);
	}
	
	// Output Response
	echo json_encode($json_response);