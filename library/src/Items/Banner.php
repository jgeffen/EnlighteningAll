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
	use Helpers;
	use Items\Enums\Tables;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Banner {
		use Traits\Item;
		
		protected ?string $label;
		protected ?string $filename;
		protected int     $position;
		
		protected Tables\Secrets $table = Tables\Secrets::BANNERS;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `banners` WHERE `id` = :id", array(
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
		 * @return bool
		 */
		public function hasImage(): bool {
			return !is_null($this->getFilename());
		}
		
		/**
		 * @return null|string
		 */
		public function getFilePath(): ?string {
			return $this->hasImage() ? sprintf("/files/banners/%s", $this->getFilename()) : NULL;
		}
		
		/**
		 * Alias for getFilePath()
		 *
		 * @return null|string
		 */
		public function getImage(): ?string {
			return $this->getFilePath();
		}
		
		/**
		 * @return int
		 */
		public function getImageWidth(): int {
			return $this->hasImage() ? Helpers::GetImageDimension($this->getFilePath(), 'width') : 0;
		}
		
		/**
		 * @return int
		 */
		public function getImageHeight(): int {
			return $this->hasImage() ? Helpers::GetImageDimension($this->getFilePath(), 'height') : 0;
		}
		
		/**
		 * @return null|string
		 */
		public function getLabel(): ?string {
			return $this->label;
		}
		
		/**
		 * @return null|string
		 */
		public function getFilename(): ?string {
			return $this->filename;
		}
		
		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
		
		/**
		 * @return string
		 */
		public function getLink(): string {
			return Helpers::CurrentWebsite(sprintf("?club=%s", filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: '(insert id here)'));
		}
	}