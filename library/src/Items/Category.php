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
	
	namespace Items;
	
	use Database;
	use Helpers;
	use Items\Enums\Tables;
	use PDO;
	use PDOStatement;
	
	class Category extends Abstracts\PageType {
		use Traits\Image, Traits\Gallery, Traits\PDFs;
		
		protected Tables\Website $table = Tables\Website::CATEGORIES;
		
		protected array   $items = array();
		protected string  $table_name;
		protected string  $name;
		protected ?string $label;
		protected ?string $icon;
		
		/**
		 * @param null|int|string $identifier
		 * @param null|string     $table_name
		 *
		 * @return null|$this
		 */
		public static function Init(null|int|string $identifier, ?string $table_name = NULL): ?static {
			return match (gettype($identifier)) {
				'integer' => Database::Action("SELECT * FROM `categories` WHERE `id` = :identifier", array(
					'identifier' => $identifier
				))->fetchObject(static::class),
				'string'  => Database::Action("SELECT * FROM `categories` WHERE `page_url` = :identifier AND `table_name` = :table_name", array(
					'identifier' => $identifier,
					'table_name' => $table_name
				))->fetchObject(static::class),
				default   => NULL
			} ?: NULL;
		}
		
		/**
		 * @param null|Interfaces\TableEnum $table
		 *
		 * @return array
		 */
		public static function Options(?Interfaces\TableEnum $table): array {
			return Database::Action("SELECT `id`, `label` FROM `categories` WHERE `table_name` = :table_name ORDER BY `label`", array(
				'table_name' => $table?->getValue()
			))->fetchAll(PDO::FETCH_KEY_PAIR);
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|static
		 */
		public static function Fetch(PDOStatement $statement): ?static {
			return $statement->fetchObject(static::class) ?: NULL;
		}
		
		/**
		 * @param string $query
		 * @param array  $params
		 *
		 * @return static[]
		 */
		public static function FetchAllString(string $query, array $params = array()): array {
			return Database::Action($query, $params)->fetchAll(PDO::FETCH_CLASS, static::class);
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return static[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
		}
		
		/**
		 * @return array
		 */
		public function getItems(): array {
			return $this->items;
		}
		
		/**
		 * @param array $items
		 */
		public function setItems(array $items): void {
			$this->items = $items;
		}
		
		/**
		 * @return string
		 */
		public function getTableName(): string {
			return $this->table_name;
		}
		
		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}
		
		/**
		 * @return null|string
		 */
		public function getIcon(): ?string {
			return $this->icon;
		}
		
		/**
		 * @return ?string
		 */
		public function getLabel(): ?string {
			return $this->label;
		}
		
		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
		
		/**
		 * @return null|Interfaces\TableEnum
		 */
		public function getTableEnum(): ?Interfaces\TableEnum {
			return Helpers::TableLookup($this->getTableName());
		}
	}