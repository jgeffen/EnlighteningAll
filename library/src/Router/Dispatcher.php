<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	namespace Router;
	
	use Database;
	use Debug;
	use Exception;
	use FastRoute;
	use Render;
	
	class Dispatcher extends FastRoute\Dispatcher\GroupCountBased {
		private Collector $collector;
		private Handler   $handler;
		private ?Route    $route = NULL;
		
		private array $options;
		private array $route_info;
		
		/**
		 * @param callable $callback
		 */
		public function __construct(callable $callback) {
			$this->collector = new Collector();
			
			$callback($this->collector);
			
			parent::__construct($this->getCollector()->getData());
			
			$this->route_info = $this->dispatch(Request::GetMethod()->getValue(), Request::GetUri());
			
			// DEBUG
			// error_log(print_r($this->route_info, TRUE));
			
			switch($this->route_info[0]) {
				case Dispatcher::NOT_FOUND:
					// Render Error Document
					Render::ErrorDocument(404);
				case Dispatcher::METHOD_NOT_ALLOWED:
					// Render Error Document
					Render::ErrorDocument(405);
				case Dispatcher::FOUND:
					// Variable Defaults
					$this->handler = $this->route_info[1];
					$this->options = $this->route_info[2];
					
					// DEBUG
					// error_log($this->getHandler()->getPath());
					
					// Render View
					Render::RouteHandler($this) || Render::ErrorDocument(404);
			}
		}
		
		/**
		 * @return Collector
		 */
		public function getCollector(): Collector {
			return $this->collector;
		}
		
		/**
		 * @param int $index
		 *
		 * @return null|string
		 */
		public function getIntent(int $index = 0): ?string {
			return $this->getHandler()->getIntent($index);
		}
		
		/**
		 * @return mixed|Handler
		 */
		public function getHandler(): mixed {
			return $this->handler;
		}
		
		/**
		 * @param string $property
		 * @param mixed  $value
		 *
		 * @return void
		 */
		public function addOption(string $property, mixed $value): void {
			$this->options[$property] = $value;
		}
		
		/**
		 * @return array
		 */
		public function getRouteInfo(): array {
			return $this->route_info;
		}
		
		/**
		 * @return string
		 */
		public function getSection(): string {
			return $this->getHandler()->getSection();
		}
		
		/**
		 * @return null|Route
		 */
		public function getRoute(): ?Route {
			return $this->route;
		}
		
		/**
		 * @param null|string $child_url
		 * @param null|string $parent_url
		 * @param null|string $grandparent_url
		 */
		public function setRoute(?string $child_url = NULL, ?string $parent_url = NULL, ?string $grandparent_url = NULL): void {
			$this->route = Database::Action("SELECT `child`.* FROM `routes` AS `child` LEFT JOIN `routes` AS `parent` ON `child`.`parent_route_id` = `parent`.`id` LEFT JOIN `routes` AS `grandparent` ON `parent`.`parent_route_id` = `grandparent`.`id` WHERE `child`.`page_url` = :child_url AND `parent`.`page_url` = :parent_url AND `grandparent`.`page_url` = :grandparent_url", array(
				'child_url'       => $child_url,
				'parent_url'      => $parent_url,
				'grandparent_url' => $grandparent_url
			))->fetchObject(Route::class) ?: NULL;
		}
		
		/**
		 * @param null|\Router\Route $route
		 *
		 * @return void
		 */
		public function overrideRoute(?Route $route): void {
			$this->route = $route;
		}
		
		/**
		 * Alias for $this->getOption('id')
		 *
		 * @return null|int
		 */
		public function getId(): ?int {
			return (int)$this->getOption('id');
		}
		
		/**
		 * @param string     $property
		 * @param null|mixed $default
		 *
		 * @return mixed
		 */
		public function getOption(string $property, mixed $default = NULL): mixed {
			return $this->options[$property] ?? $default;
		}
		
		/**
		 * Alias for $this->getOption('category_url')
		 *
		 * @return null|string
		 */
		public function getCategoryUrl(): ?string {
			return (string)$this->getOption('category_url');
		}
		
		/**
		 * Alias for $this->getOption('page_url') with nullable default
		 *
		 * @param null|string $default
		 *
		 * @return null|string
		 */
		public function getPageUrl(?string $default = NULL): ?string {
			return (string)$this->getOption('page_url') ?: $default;
		}
		
		/**
		 * Alias for $this->getOption('table_id')
		 *
		 * @return null|int
		 */
		public function getTableId(): ?int {
			return !is_null($this->getOption('table_id')) ? (int)$this->getOption('table_id') : NULL;
		}
		
		/**
		 * Alias for $this->getOption('table_name')
		 *
		 * @return null|string
		 */
		public function getTableName(): ?string {
			return str_replace('-', '_', (string)$this->getOption('table_name'));
		}
		
		/**
		 * @param int $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 * @param int $depth Set the maximum depth. Must be greater than zero.
		 *
		 * @return string
		 */
		public function toJson(int $flags = JSON_ERROR_NONE, int $depth = 512): string {
			return match ($flags) {
				JSON_HEX_APOS => htmlspecialchars(json_encode($this->getOptions(), JSON_ERROR_NONE, $depth), ENT_QUOTES),
				JSON_HEX_QUOT => htmlspecialchars(json_encode($this->getOptions(), JSON_ERROR_NONE, $depth), ENT_COMPAT),
				default       => json_encode($this->getOptions(), $flags, $depth),
			};
		}
		
		/**
		 * @return array
		 */
		public function getOptions(): array {
			return $this->options;
		}
		
		/**
		 * Converts special characters of property to HTML entities
		 *
		 * @param string $property
		 * @param int    $flags Available {@link https://www.php.net/manual/en/function.htmlspecialchars.php#refsect1-function.htmlspecialchars-parameters flags} constants
		 *
		 * @return null|string
		 */
		public function getEncoded(string $property, int $flags = ENT_COMPAT): ?string {
			try {
				if(isset($this->options[$property])) {
					return htmlspecialchars((string)$this->options[$property], $flags);
				} else throw new Exception(sprintf("Invalid option %s for class %s", $property, get_class($this)));
			} catch(Exception $exception) {
				Debug::Exception($exception);
			}
			
			return NULL;
		}
	}