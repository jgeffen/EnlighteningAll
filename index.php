<?php
// Required Files
require('library/vendor/autoload.php');

// Ensure backward compatibility for legacy Membership class references
if (!class_exists('Membership')) {
    class_alias(\Items\Member::class, 'Membership');
}
try {

	// Init Dispatcher
	new Router\Dispatcher(function (Router\Collector $route) {
		// Default Routes
		$route->add('/', new Router\Handler('homepage'), Router\Method::GET);
		$route->add('/index2', new Router\Handler('placeholder'), Router\Method::GET);

		// Import Routes
		$route->import('ajax');
		$route->import('admin');
		$route->import('forms');
		$route->import('modals');
		$route->import('members');
		$route->import('travel-affiliate-members');
		$route->import('popovers');
		$route->import('items');
	});
} catch (FastRoute\BadRouteException $exception) {
	Debug::ShowData($exception->getMessage(), $exception->getTrace())->exit();
}
