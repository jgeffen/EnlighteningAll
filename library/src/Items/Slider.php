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
	
	namespace Items;
	
	use Database;
	use DateTime;
	use Helpers;
	use Items\Enums\Tables;
	use PDO;
	use PDOStatement;
	use Render;
	
	class Slider {
		use Traits\Image, Traits\Item;
		
		protected Tables\Website $table = Tables\Website::SLIDERS;
		
		protected string  $page_url;
		protected ?string $heading;
		protected ?string $content;
		protected ?string $content_position;
		protected ?string $link;
		protected ?string $link_text;
		protected int     $position;
		protected bool    $analytics;
		protected bool    $published;
		protected string  $published_date;
		protected bool    $expiration;
		protected ?string $expiration_date;
		protected bool    $delete_on_expiration;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `sliders` WHERE `id` = :id", array(
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
		public function getPageUrl(): string {
			return $this->page_url;
		}
		
		/**
		 * @param null|int $length
		 *
		 * @return string
		 */
		public function getHeading(?int $length = NULL): string {
			return is_null($length) ? $this->heading : Helpers::Truncate($this->heading, $length);
		}
		
		/**
		 * @param null|int $length
		 *
		 * @return null|string
		 */
		public function getContent(?int $length = NULL): ?string {
			return is_null($length) ? $this->content : Helpers::Truncate($this->content, $length);
		}
		
		/**
		 * @return null|string
		 */
		public function getContentPosition(): ?string {
			return $this->content_position;
		}
		
		/**
		 * @return null|string
		 */
		public function getLink(): ?string {
			return $this->link;
		}
		
		/**
		 * @return null|string
		 */
		public function getLinkText(): ?string {
			return $this->link_text;
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
		public function hasAnalytics(): bool {
			return $this->analytics;
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
		 * @return bool
		 */
		public function hasExpiration(): bool {
			return $this->expiration;
		}
		
		/**
		 * @return null|DateTime
		 */
		public function getExpirationDate(): ?DateTime {
			return !is_null($this->expiration_date) ? date_create($this->expiration_date) : NULL;
		}
		
		/**
		 * @return bool
		 */
		public function isDeleteOnExpiration(): bool {
			return $this->delete_on_expiration;
		}
		
		/**
		 * @return void
		 */
		public function setImages(): void {
			$this->images = Render::Images(array(
				'source'      => sprintf("/files/%s/%s", $this->table->getValue(), $this->getFilename()),
				'slide'       => sprintf("/files/%s/slide/%s", $this->table->getValue(), $this->getFilename()),
				'slide_thumb' => sprintf("/files/%s/slide/thumbs/%s", $this->table->getValue(), $this->getFilename())
			));
		}
		
		/**
		 * @return null|string
		 */
		public function getSlideImage(): ?string {
			return $this->getImage('slide');
		}
		
		/**
		 * @return null|string
		 */
		public function getSlideThumb(): ?string {
			return $this->getImage('slide_thumb');
		}
	}