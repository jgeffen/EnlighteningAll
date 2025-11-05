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

		'page'     => match ($dispatcher->getIntent()) {
			'paginated' => throw new Exception('Matching arm yet to be implemented'),
			default     => TravelAffiliateMembers\Render::Page($dispatcher)
		},
	};
} catch (Exception | UnhandledMatchError $exception) {
	Debug::Exception($exception);
	Render::ErrorDocument(501, $exception);
}
