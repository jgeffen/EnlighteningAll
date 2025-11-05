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
	
	namespace Router;
	
	use FastRoute;
	
	class Collector extends FastRoute\RouteCollector {
		public function __construct() {
			parent::__construct(new Parser(), new Generator());
		}
		
		/**
		 * Create a route group with a common prefix.
		 *
		 * All routes created in the passed callback will have the given group prefix prepended.
		 *
		 * @param string   $prefix
		 * @param callable $callback
		 */
		public function group(string $prefix, callable $callback): void {
			parent::addGroup(sprintf("/%s", ltrim($prefix, '/')), $callback);
		}
		
		/**
		 * Adds a route to the collection.
		 *
		 * The syntax used in the $route string depends on the used route parser.
		 *
		 * @param string  $route
		 * @param Handler $handler
		 * @param Method  $method
		 */
		public function add(string $route, Handler $handler, Method $method = Method::ANY): void {
			parent::addRoute($method->getValue(), $route, $handler);
		}
		
		/**
		 * @param string $section
		 */
		public function import(string $section): void {
			$route      = $this;
			$route_path = sprintf("%s/router", dirname(__DIR__, 3));
			$file_path  = sprintf("%s/%s/routes.php", $route_path, $section);
			
			if($file_path && str_contains($file_path, $route_path)) {
				require($file_path);
			}
		}
	}