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
	
	namespace Members;
	
	use Database;
	use DateTime;
	use Items\Interfaces;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Faq implements Interfaces\Item {
		use Traits\Category, Traits\Item;
		
		protected string $question;
		protected string $answer;
		protected string $page_url;
		protected int    $position;
		protected bool   $published;
		protected string $published_date;
		
		/**
		 * @param null|int|string $identifier
		 *
		 * @return null|$this
		 */
		public static function Init(null|int|string $identifier): ?static {
			return match (gettype($identifier)) {
				'integer' => Database::Action("SELECT * FROM `member_faqs` WHERE `id` = :identifier", array(
					'identifier' => $identifier
				))->fetchObject(static::class),
				'string'  => Database::Action("SELECT * FROM `member_faqs` WHERE `page_url` = :identifier", array(
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
		 * @return null|Category
		 */
		public function getCategory(): ?Category {
			return $this->category ??= Category::Init($this->getCategoryId());
		}
		
		/**
		 * @return string
		 */
		public function getQuestion(): string {
			return $this->question;
		}
		
		/**
		 * @param bool $nl2br
		 *
		 * @return string
		 */
		public function getAnswer(bool $nl2br = FALSE): string {
			return $nl2br ? sprintf("<p>%s</p>", nl2br($this->answer)) : $this->answer;
		}
		
		/**
		 * @return string
		 */
		public function getPageUrl(): string {
			return $this->page_url;
		}
		
		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
		
		/**
		 * @return bool
		 */
		public function isPublished(): bool {
			return $this->published;
		}
		
		/**
		 * @return DateTime
		 */
		public function getPublishedDate(): DateTime {
			return date_create($this->published_date);
		}
		
		/**
		 * @return string
		 */
		public function getAlt(): string {
			return htmlspecialchars($this->getQuestion(), ENT_COMPAT);
		}
		
		/**
		 * @return null|string
		 */
		public function getLink(): ?string {
			return $this->getPageUrl() && $this->getCategory() ? sprintf("/members/faqs/%s/%s", $this->getCategory()->getPageUrl(), $this->getPageUrl()) : NULL;
		}
	}