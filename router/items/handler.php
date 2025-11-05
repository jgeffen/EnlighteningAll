<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
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
		// Import Root
		$dispatcher->setRoute($dispatcher->getOption('child_url'), $dispatcher->getOption('parent_url'), $dispatcher->getOption('grandparent_url'));
		
		// Match Intent
		if(!match ($dispatcher->getIntent()) {
			'event'  => Render::Event($dispatcher),
			'forms'  => match ($dispatcher->getIntent(1)) {
				'events' => match ($dispatcher->getIntent(2)) {
					'purchase-pass'      => Render::Form('app/items/events/forms/purchase-pass', $dispatcher),
					'purchase-pass-auth' => Render::Form('app/items/events/forms/purchase-pass-auth', $dispatcher),
					default              => throw new Exception('Matching arm yet to be implemented')
				},
				default  => throw new Exception('Matching arm yet to be implemented')
			},
			'parent' => match ($dispatcher->getIntent()) {
				'paginated' => throw new Exception('Matching arm yet to be implemented'),
				default     => Render::Page($dispatcher->getOption('child_url'), 'app/pages', $dispatcher) || Render::Route($dispatcher)
			},
			default  => match ($dispatcher->getIntent()) {
				'paginated' => throw new Exception('Matching arm yet to be implemented'),
				default     => Render::Route($dispatcher)
			}
		}) Render::ErrorDocument(404);
	} catch(Exception|UnhandledMatchError $exception) {
		Debug::Exception($exception);
		Render::ErrorDocument(501);
	}