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
	use Items\Enums\Tables;
	use PDO;
	use PDOStatement;
	
	class Service extends Abstracts\PageType {
		use Traits\Category, Traits\Image, Traits\Gallery, Traits\PDFs;
		
		protected Tables\Website $table = Tables\Website::SERVICES;
		protected ?string $icon;
		protected ?string $label;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|static
		 */
		public static function Init(?int $id): ?static {
			return Database::Action("SELECT * FROM `services` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(static::class) ?: NULL;
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
		 * @param PDOStatement $statement
		 *
		 * @return static[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
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
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
		
		/**
		 * @return null|string
		 */
		public function getIcon(): ?string {
			return $this->icon;
		}
		
		/**
		 * @return null|string
		 */
		public function getLabel(): ?string {
			return $this->label;
		}
		
		/**
		 * @return string
		 */
		public function getLink(): string {
			return sprintf("/services/%s", $this->getPageUrl());
		}
	}