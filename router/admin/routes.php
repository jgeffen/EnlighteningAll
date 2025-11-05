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

// ===========================================================
// ADMIN ROUTES
// ===========================================================

// Dashboard, login, and utility routes
$route->add('/user[/]', new Router\Handler('admin/page/dashboard'), Router\Method::GET);
$route->add('/user/login', new Router\Handler('admin/page/login'), Router\Method::GET);
$route->add('/user/assets/{asset:.*}', new Router\Handler('admin/asset'), Router\Method::GET);
$route->add('/user/toggle/{type}/{action}/{id:\d+}', new Router\Handler('admin/management/toggle'), Router\Method::POST);

// ===========================================================
// STANDARD ADMIN ROUTES
// ===========================================================

$route->add('/user/{page_url}', new Router\Handler('admin/page'), Router\Method::GET);
$route->add('/user/{action:delete}/{type}/{table_id:\d+}', new Router\Handler('admin/management'), Router\Method::DELETE);
$route->add('/user/{action}/{type}', new Router\Handler('admin/management'), Router\Method::BOTH);
$route->add('/user/{action}/{type}/{table_id:\d+}', new Router\Handler('admin/management'), Router\Method::BOTH);
$route->add('/user/{action}/{type}/{table_name}[/{table_id:\d+}]', new Router\Handler('admin/management'), Router\Method::BOTH);
$route->add('/user/{action}/{type}/{table_name}/{table_id:\d+}/{id:\d+}', new Router\Handler('admin/management'), Router\Method::BOTH);

// ===========================================================
// END OF FILE
// ===========================================================
