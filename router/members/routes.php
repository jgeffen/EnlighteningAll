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
	'/members/contests[/page-{page:(?:[2-9]|\d\d\d*)}]',
	new Router\Handler('members/contests/items'),
	Router\Method::GET
);
$route->add(
	'/members/contests/{page_url}',
	new Router\Handler('members/contests/item'),
	Router\Method::GET
);
$route->add(
	'/members/faqs',
	new Router\Handler('members/faqs/categories'),
	Router\Method::GET
);
$route->add(
	'/members/faqs/{category_url}',
	new Router\Handler('members/faqs/items'),
	Router\Method::GET
);
$route->add(
	'/members/faqs/{category_url}/{page_url}',
	new Router\Handler('members/faqs/item'),
	Router\Method::GET
);
$route->add(
	'/members/posts/{type}/{action}[/{id:\d+}]',
	new Router\Handler('members/posts')
);
$route->add(
	'/members/profile/{username:[a-z0-9\._]{3,20}}',
	new Router\Handler('members/profile')
);
$route->add(
	'/members/profile/{username:[a-z0-9\._]{3,20}}/{post_id_hash:[a-f0-9]{32}}',
	new Router\Handler('members/profile/post')
);
$route->add(
	'/members/rooms',
	new Router\Handler('members/rooms/favorites'),
	Router\Method::GET
);
$route->add(
	'/members/rooms/page-{page:(?:[2-9]|\d\d\d*)}',
	new Router\Handler('members/rooms/favorites'),
	Router\Method::GET
);
$route->add(
	'/members/rooms/review/{id:\d+}',
	new Router\Handler('members/rooms/review'),
	Router\Method::GET
);
$route->add(
	'/members/rooms/reviews/{id:\d+}',
	new Router\Handler('members/rooms/reviews'),
	Router\Method::GET
);
$route->add(
	'/members/tickets/{id:\d+}',
	new Router\Handler('members/tickets'),
	Router\Method::GET
);
$route->add(
	'/members/walls/{page_url}',
	new Router\Handler('members/wall'),
	Router\Method::GET
);
$route->add(
	'/members/{page_url:reset-password}/{hash:[a-f0-9]{32}}',
	new Router\Handler('members/page'),
	Router\Method::GET
);
$route->add(
	'/members/{page_url:verify-email}/{hash:[a-f0-9]{32}}',
	new Router\Handler('members/page'),
	Router\Method::GET
);
$route->add(
	'/members/{page_url}',
	new Router\Handler('members/page'),
	Router\Method::GET
);
$route->add(
	'/members/{page_url}/page-{page:(?:[2-9]|\d\d\d*)}',
	new Router\Handler('members/page/paginated'),
	Router\Method::GET
);
