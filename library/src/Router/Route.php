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
	use Helpers;
	use Items\Interfaces\Item;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Route implements Item {
		use Traits\Item;
		
		private ?Route $parent_route;
		
		private ?int    $parent_route_id;
		private string  $table_name;
		private ?int    $table_id;
		private ?string $page_url;
		private bool    $category;
		private bool    $categories;
		
		/**
		 * @param string   $table_name
		 * @param null|int $table_id
		 *
		 * @return null|self
		 */
		public static function Init(string $table_name, ?int $table_id = NULL): ?self {
			return Database::Action("SELECT * FROM `routes` WHERE `table_name` = :table_name AND `table_id` = :table_id", array(
				'table_name' => $table_name,
				'table_id'   => $table_id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|self
		 */
		public static function Fetch(PDOStatement $statement): ?self {
			return $statement->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param string $query
		 * @param array  $params
		 *
		 * @return null|static
		 */
		public static function FetchString(string $query, array $params = array()): ?static {
			return Database::Action($query, $params)->fetchObject(static::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return self[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, self::class);
		}
		
		/**
		 * @return null|Route
		 */
		public function getParentRoute(): ?Route {
			return $this->parent_route ??= Database::Action("SELECT * FROM `routes` WHERE `id` = :id", array(
				'id' => $this->getParentRouteId()
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @return null|int
		 */
		public function getParentRouteId(): ?int {
			return $this->parent_route_id;
		}
		
		/**
		 * @return array
		 */
		public function getParentRouteParts(): array {
			return $this->getParentRoute()?->getParts() ?? array();
		}
		
		/**
		 * @return string
		 */
		public function getTableName(): string {
			return $this->table_name;
		}
		
		/**
		 * @return null|int
		 */
		public function getTableId(): ?int {
			return $this->table_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getPageUrl(): ?string {
			return $this->page_url;
		}
		
		/**
		 * @return bool
		 */
		public function hasCategories(): bool {
			return $this->categories;
		}
		
		/**
		 * @return bool
		 */
		public function isCategory(): bool {
			return $this->category;
		}
		
		/**
		 * @return bool
		 */
		public function hasItems(): bool {
			return is_null($this->table_id);
		}
		
		/**
		 * @return bool
		 */
		public function isItem(): bool {
			return !is_null($this->table_id);
		}
		
		/**
		 * @return array
		 */
		public function getParts(): array {
			return array_merge($this->getParentRouteParts(), array($this->getPageUrl()));
		}
		
		/**
		 * @return string
		 */
		public function getLink(): string {
			return Helpers::LinkBuilder(...$this->getParts());
		}
	}