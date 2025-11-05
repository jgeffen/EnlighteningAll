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
	
	namespace Swinkster;
	
	use Database;
	use Items\Abstracts;
	use Items\Enums\Tables;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Page extends Abstracts\PageType {
		use Traits\Gallery, Traits\PDFs;
		
		protected Tables\Swinkster $table = Tables\Swinkster::PAGES;
		
		private array   $events;
		private ?string $hours;
		private ?string $rentals;
		private array   $sponsors;
		
		/**
		 * @param null|int|string $identifier
		 *
		 * @return null|$this
		 */
		public static function Init(null|int|string $identifier): ?static {
			return match (gettype($identifier)) {
				'integer' => Database::Action("SELECT * FROM `swinkster_pages` WHERE `id` = :identifier", array(
					'identifier' => $identifier
				))->fetchObject(static::class),
				'string'  => Database::Action("SELECT * FROM `swinkster_pages` WHERE `page_url` = :identifier", array(
					'identifier' => $identifier
				))->fetchObject(static::class),
				default   => NULL
			} ?: NULL;
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
		 * @param string $query
		 * @param array  $params
		 */
		public function setEvents(string $query, array $params = array()): void {
			$this->events = Event::FetchAll(Database::Action($query, $params));
		}
		
		/**
		 * @return null|string
		 */
		public function getHours(): ?string {
			return $this->hours;
		}
		
		/**
		 * @return null|string
		 */
		public function getRentals(): ?string {
			return $this->rentals;
		}
		
		/**
		 * @return Sponsor[]
		 */
		public function getSponsors(): array {
			return $this->sponsors;
		}
		
		/**
		 * @param string $query
		 * @param array  $params
		 */
		public function setSponsors(string $query, array $params = array()): void {
			$this->sponsors = Sponsor::FetchAll(Database::Action($query, $params));
		}
	}