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
	use DateInterval;
	use DatePeriod;
	use DateTime;
	use Items\Enums\Tables;
	use Items\Interfaces;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Event implements Interfaces\PageType {
		use Traits\Category, Traits\Item, Traits\Image, Traits\Gallery, Traits\Page, Traits\PDFs;
		
		protected Tables\Swinkster $table = Tables\Swinkster::EVENTS;
		
		protected string  $event_dates;
		protected string  $date_start;
		protected string  $date_end;
		protected ?string $location;
		protected ?string $price_text;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `swinkster_events` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
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
		 * @return string
		 */
		public function getEventDates(): string {
			return $this->event_dates;
		}
		
		/**
		 * @return DateTime
		 */
		public function getStartDate(): DateTime {
			return date_create($this->date_start);
		}
		
		/**
		 * @return DateTime
		 */
		public function getEndDate(): DateTime {
			return date_create($this->date_end);
		}
		
		/**
		 * @return DatePeriod
		 */
		public function getDates(): DatePeriod {
			return new DatePeriod($this->getStartDate(), new DateInterval('P1D'), $this->getEndDate()->modify('+1 Day'));
		}
		
		/**
		 * @param string $format
		 *
		 * @return string
		 */
		public function getTime(string $format = 'g:ia'): string {
			return sprintf("%s - %s", $this->getStartDate()->format($format), $this->getEndDate()->format($format));
		}
		
		/**
		 * @return string
		 */
		public function getDate(): string {
			return $this->getStartDate()->format('Y-m-d') == $this->getEndDate()->format('Y-m-d')
				? $this->getStartDate()->format('M d')
				: sprintf("%s - %s", $this->getStartDate()->format('M d'), $this->getEndDate()->format('M d Y'));
		}
		
		/**
		 * @return null|string
		 */
		public function getLocation(): ?string {
			return $this->location;
		}
		
		/**
		 * @return null|string
		 */
		public function getPriceText(): ?string {
			return $this->price_text;
		}
	}