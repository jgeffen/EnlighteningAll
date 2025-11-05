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
	 * @var Router\Collector $route
	 */
	
	// Forms
	$route->add(
		'/events/{child_url}/purchase-pass',
		new Router\Handler('items/forms/events/purchase-pass'),
		Router\Method::GET
	);
	
	// Forms
	$route->add(
		'/events/{child_url}/purchase-pass-auth',
		new Router\Handler('items/forms/events/purchase-pass-auth'),
		Router\Method::GET
	);
	
	// Items
	$route->add(
		'/{table_id:\d+}.event',
		new Router\Handler('items/event'),
		Router\Method::GET
	);
	$route->add(
		'/{child_url}',
		new Router\Handler('items/parent'),
		Router\Method::GET
	);
	$route->add(
		'/{child_url}/page-{page:(?:[2-9]|\d\d\d*)}',
		new Router\Handler('items/parent/paginated'),
		Router\Method::GET
	);
	$route->add(
		'/{parent_url}/{child_url}',
		new Router\Handler('items/child'),
		Router\Method::GET
	);
	$route->add(
		'/{parent_url}/{child_url}/page-{page:(?:[2-9]|\d\d\d*)}',
		new Router\Handler('items/child/paginated'),
		Router\Method::GET
	);
	$route->add(
		'/{grandparent_url}/{parent_url}/{child_url}',
		new Router\Handler('items/grandchild'),
		Router\Method::GET
	);
	$route->add(
		'/{grandparent_url}/{parent_url}/{child_url}/page-{page:(?:[2-9]|\d\d\d*)}',
		new Router\Handler('items/grandchild/paginated'),
		Router\Method::GET
	);
