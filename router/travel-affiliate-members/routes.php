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

// Members


$route->add(
	'/travel-affiliate-members/{page_url:reset-password}/{hash:[a-f0-9]{32}}',
	new Router\Handler('travel-affiliate-members/page'),
	Router\Method::GET
);
$route->add(
	'/travel-affiliate-members/{page_url:verify-email}/{hash:[a-f0-9]{32}}',
	new Router\Handler('travel-affiliate-members/page'),
	Router\Method::GET
);
$route->add(
	'/travel-affiliate-members/{page_url}',
	new Router\Handler('travel-affiliate-members/page'),
	Router\Method::GET
);
$route->add(
	'/travel-affiliate-members/{page_url}/page-{page:(?:[2-9]|\d\d\d*)}',
	new Router\Handler('travel-affiliate-members/page/paginated'),
	Router\Method::GET
);
