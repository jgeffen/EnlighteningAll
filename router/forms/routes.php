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
	$route->add('/forms/{form}', new Router\Handler('forms'), Router\Method::GET);
	$route->add('/forms/{form}', new Router\Handler('forms/ajax'), Router\Method::POST);