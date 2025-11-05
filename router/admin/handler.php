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
	
	// Handle Basic Authentication
	if(ADMIN_AUTH_ENABLED) {
		if(!Admin\BasicAuth(ADMIN_AUTH_USER, ADMIN_AUTH_PASS, FALSE)) {
			if(!Admin\BasicAuth(ADMIN_AUTH_USER_2, ADMIN_AUTH_PASS_2)) {
				if(!Admin\BasicAuth(ADMIN_AUTH_USER_3, ADMIN_AUTH_PASS_3)) {
					header('WWW-Authenticate: Basic realm="Authorized Use Only"');
					Render::ErrorDocument(HttpStatusCode::UNAUTHORIZED);
				}
			}
		}
	}
	
	// Set Include Path
	set_include_path('app/admin');
	
	try {
		// Match Intent
		match ($dispatcher->getIntent()) {
			'asset'      => Admin\Render::Asset($dispatcher),
			'management' => match (Admin\LoggedIn()) {
				Admin\LOGGED_OUT => Admin\Redirect('/user/login'),
				Admin\LOGGED_IN  => match ($dispatcher->getIntent(1)) {
					'toggle' => Admin\Render::Toggle($dispatcher),
					default  => match ($dispatcher->getOption('type')) {
						'member-reports' => Admin\Render::Report($dispatcher),
						default          => Admin\Render::Management($dispatcher)
					}
				}
			},
			'page'       => match (Admin\LoggedIn()) {
				Admin\LOGGED_IN  => match ($dispatcher->getIntent(1)) {
					'dashboard' => Admin\Render::Dashboard(),
					'login'     => Admin\Redirect('/user'),
					default     => Admin\Render::Page($dispatcher)
				},
				Admin\LOGGED_OUT => match ($dispatcher->getIntent(1)) {
					'login' => Render::Page('admin/login'),
					default => Admin\Redirect('/user/login')
				}
			}
		};
	} catch(Exception|UnhandledMatchError $exception) {
		Debug::Exception($exception);
		Render::ErrorDocument(HttpStatusCode::NOT_IMPLEMENTED);
	}