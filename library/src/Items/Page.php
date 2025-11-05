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
	
	class Page extends Abstracts\PageType {
		use Traits\Gallery, Traits\PDFs;
		
		protected Tables\Website $table = Tables\Website::PAGES;
		
		private array $events;
		private array $news;
		
		/**
		 * @param null|int|string $identifier
		 *
		 * @return null|static
		 */
		public static function Init(null|int|string $identifier): ?static {
			return match (gettype($identifier)) {
				'integer' => Database::Action("SELECT * FROM `pages` WHERE `id` = :identifier", array(
					'identifier' => $identifier
				))->fetchObject(self::class) ?: NULL,
				'string'  => Database::Action("SELECT * FROM `pages` WHERE `page_url` = :identifier", array(
					'identifier' => $identifier
				))->fetchObject(self::class) ?: NULL,
				default   => NULL
			};
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|static
		 */
		public static function Fetch(PDOStatement $statement): ?self {
			return $statement->fetchObject(self::class) ?: NULL;
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
		 * @param null|string $page_url
		 *
		 * @return null|$this
		 */
		static function FromPageUrl(?string $page_url): ?self {
			return Database::Action("SELECT * FROM `pages` WHERE `page_url` = :page_url", array(
				'page_url' => $page_url
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @return Event[]
		 */
		public function getEvents(): array {
			return $this->events;
		}
		
		/**
		 * @param PDOStatement $query
		 */
		public function setEvents(PDOStatement $query): void {
			$this->events = Event::FetchAll($query);
		}
		
		/**
		 * @return News[]
		 */
		public function getNews(): array {
			return $this->news;
		}
		
		/**
		 * @param PDOStatement $query
		 */
		public function setNews(PDOStatement $query): void {
			$this->news = News::FetchAll($query);
		}
	}