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
 * @var Router\Collector $route
 */

// Ajax
$route->add(
	'/ajax/admin/login',
	new Router\Handler('ajax/admin/login'),
	Router\Method::POST
);
$route->add(
	'/ajax/admin/{script:[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/admin'),
	Router\Method::POST
);
$route->add(
	'/ajax/admin/{script:[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/admin'),
	Router\Method::POST
);
$route->add(
	'/ajax/admin/{script:[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/admin'),
	Router\Method::POST
);
$route->add(
	'/ajax/admin/{script:[^/]+\/[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/admin'),
	Router\Method::POST
);
$route->add(
	'/ajax/admin/{script:[^/]+\/[^/]+\/[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/admin'),
	Router\Method::POST
);
$route->add(
	'/ajax/components/{component}/{type}/{script}',
	new Router\Handler('ajax/components'),
	Router\Method::POST
);
$route->add(
	'/ajax/members/{script:[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/members'),
	Router\Method::POST
);
$route->add(
	'/ajax/members/{script:[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/members'),
	Router\Method::POST
);
$route->add(
	'/ajax/members/{script:[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/members'),
	Router\Method::POST
);
$route->add(
	'/ajax/members/{script:[^/]+\/[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/members'),
	Router\Method::POST
);
$route->add(
	'/ajax/members/{script:[^/]+\/[^/]+\/[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/members'),
	Router\Method::POST
);
///////////////////////////////////
$route->add(
	'/ajax/travel-affiliate-members/{script:[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/travel-affiliate-members'),
	Router\Method::POST
);
$route->add(
	'/ajax/travel-affiliate-members/{script:[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/travel-affiliate-members'),
	Router\Method::POST
);
$route->add(
	'/ajax/travel-affiliate-members/{script:[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/travel-affiliate-members'),
	Router\Method::POST
);
$route->add(
	'/ajax/travel-affiliate-members/{script:[^/]+\/[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/travel-affiliate-members'),
	Router\Method::POST
);
$route->add(
	'/ajax/travel-affiliate-members/{script:[^/]+\/[^/]+\/[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/travel-affiliate-members'),
	Router\Method::POST
);
//////////////////////////////////
$route->add(
	'/ajax/{script:tooltips}/{type:[^/]+}',
	new Router\Handler('ajax/default'),
	Router\Method::GET
);
$route->add(
	'/ajax/{script:[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/default'),
	Router\Method::POST
);
$route->add(
	'/ajax/{script:[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/default'),
	Router\Method::POST
);
$route->add(
	'/ajax/{script:[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/default'),
	Router\Method::POST
);
$route->add(
	'/ajax/{script:[^/]+\/[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/default'),
	Router\Method::POST
);
$route->add(
	'/ajax/{script:[^/]+\/[^/]+\/[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]',
	new Router\Handler('ajax/default'),
	Router\Method::POST
);
