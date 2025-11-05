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
		'admin'      => match (Admin\LoggedIn()) {
			Admin\LOGGED_IN  => Admin\Render::Ajax($dispatcher),
			Admin\LOGGED_OUT => match ($dispatcher->getIntent(1)) {
				'login' => Admin\Render::Login(),
				default => Render::ErrorDocument(HttpStatusCode::UNAUTHORIZED)
			}
		},
		'components' => Render::Component(sprintf("%s/%s/ajax/%s", $dispatcher->getOption('component'), $dispatcher->getOption('type'), $dispatcher->getOption('script'))),
		'members'    => Members\Render::Ajax($dispatcher),
		'travel-affiliate-members' => TravelAffiliateMembers\Render::Ajax($dispatcher),
		default      => Render::Ajax($dispatcher)
	};
} catch (Exception | UnhandledMatchError $exception) {
	Debug::Exception($exception);
	Render::ErrorDocument(501);
}
