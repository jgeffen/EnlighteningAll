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
	$route->add('/modals/admin/{modal:[^/]+}', new Router\Handler('modals/admin'), Router\Method::GET);
	$route->add('/modals/admin/{modal:[^/]+\/[^/]+}', new Router\Handler('modals/admin'), Router\Method::POST);
	$route->add('/modals/admin/{modal:[^/]+\/[^/]+\/[^/]+}[/{id:\d+}]', new Router\Handler('modals/admin'), Router\Method::GET);
	$route->add('/modals/members/{modal:[^/]+}', new Router\Handler('modals/members'), Router\Method::BOTH);
	$route->add('/modals/members/{modal:[^/]+\/[^/]+}', new Router\Handler('modals/members'), Router\Method::POST);
	$route->add('/modals/members/{modal:[^/]+\/[^/]+\/[^/]+}', new Router\Handler('modals/members'), Router\Method::GET);
	$route->add('/modals/{modal:[^/]+}', new Router\Handler('modals/default'), Router\Method::GET);
	$route->add('/modals/{modal:[^/]+\/[^/]+}', new Router\Handler('modals/default'), Router\Method::POST);
	$route->add('/modals/{modal:[^/]+\/[^/]+}/{id:\d+}', new Router\Handler('modals/default'), Router\Method::GET);
	$route->add('/modals/{modal:[^/]+\/[^/]+\/[^/]+}', new Router\Handler('modals/default'), Router\Method::GET);
	$route->add('/modals/{modal:[^/]+\/[^/]+\/[^/]+\/[^/]+}', new Router\Handler('modals/default'), Router\Method::POST);