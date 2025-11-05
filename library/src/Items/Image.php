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
	use Items\Enums\Tables;
	use PDO;
	use PDOStatement;
	
	class Image implements Interfaces\Item {
		use Traits\Image, Traits\Item;
		
		protected Tables\Website $table = Tables\Website::IMAGES;
		
		protected string  $table_name;
		protected int     $table_id;
		protected ?string $youtube_id;
		protected int     $position;
		protected bool    $published;
		protected string  $published_date;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `images` WHERE `id` = :id", array(
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
		public function getTableName(): string {
			return $this->table_name;
		}
		
		/**
		 * @return int
		 */
		public function getTableId(): int {
			return $this->table_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getYoutubeId(): ?string {
			return $this->youtube_id;
		}
		
		/**
		 * @return string
		 */
		public function getYoutubeEmbed(): string {
			return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $this->youtube_id . '" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
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
	}