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
	 */
	
	try {
		// Match Intent
		match ($dispatcher->getIntent()) {
			'contests' => match (Membership::LoggedIn()) {
				Membership::LOGGED_OUT => Helpers::Redirect('/members/login', TRUE),
				Membership::LOGGED_IN  => Members\Render::Contest($dispatcher)
			},
			'faqs'     => match (Membership::LoggedIn()) {
				Membership::LOGGED_OUT => Helpers::Redirect('/members/login', TRUE),
				Membership::LOGGED_IN  => Members\Render::FAQ($dispatcher)
			},
			'page'     => match ($dispatcher->getIntent()) {
				'paginated' => throw new Exception('Matching arm yet to be implemented'),
				default     => Members\Render::Page($dispatcher)
			},
			'posts'    => match ($dispatcher->getOption('type')) {
				'images' => throw new Exception('Post type not yet implemented.'),
				'social' => match ($dispatcher->getOption('action')) {
					'add'    => Members\Render::Management($dispatcher),
					'private-add'    => Members\Render::Management($dispatcher),
					'edit'   => Members\Render::Management($dispatcher),
					'manage' => Members\Render::Management($dispatcher),
					default  => throw new Exception('Action not supported')
				},
				default  => throw new Exception('Type not supported.')
			},
			
			'profile'  => match (Membership::LoggedIn()) {
				Membership::LOGGED_OUT => Helpers::Redirect('/members/login', TRUE),
				Membership::LOGGED_IN  => match ($dispatcher->getIntent(1)) {
					'post'  => Members\Render::Post($dispatcher),
					default => Members\Render::Profile($dispatcher)
				}
			},
			'rooms'    => match (Membership::LoggedIn()) {
				Membership::LOGGED_OUT => Helpers::Redirect('/members/login', TRUE),
				Membership::LOGGED_IN  => Members\Render::Rooms($dispatcher, $dispatcher->getIntent(1))
			},
			'tickets'  => match (Membership::LoggedIn()) {
				Membership::LOGGED_OUT => Helpers::Redirect('/members/login', TRUE),
				Membership::LOGGED_IN  => Members\Render::Ticket($dispatcher)
			},
			'wall'     => Members\Render::Wall($dispatcher),
			'images'     => Members\Render::PrivateImages($dispatcher)
		};
	} catch(Exception|UnhandledMatchError $exception) {
		Debug::Exception($exception);
		Render::ErrorDocument(501, $exception);
	}