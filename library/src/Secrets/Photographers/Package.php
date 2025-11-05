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
	
	namespace Secrets\Photographers;
	
	use Database;
	use Helpers;
	use Items\Abstracts;
	use Items\Traits;
	use Items\Enums\Tables;
	use JetBrains\PhpStorm\Pure;
	use PDO;
	use PDOStatement;
	use Secrets;
	
	class Package extends Abstracts\PageType {
		use Traits\Gallery, Traits\Image, Traits\PDFs;
		
		protected Tables\Secrets\Photographers $table = Tables\Secrets\Photographers::PACKAGES;
		
		private ?Secrets\Photographer $photographer;
		
		private int   $photographer_id;
		private float $price;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `photographer_packages` WHERE `id` = :id", array(
				'id' => $id
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
		 * @param PDOStatement $statement
		 *
		 * @return self[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, self::class);
		}
		
		/**
		 * @return null|Secrets\Photographer
		 */
		public function getPhotographer(): ?Secrets\Photographer {
			return $this->photographer ??= Secrets\Photographer::Init($this->getPhotographerId());
		}
		
		/**
		 * @return int
		 */
		public function getPhotographerId(): int {
			return $this->photographer_id;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return float|string
		 */
		#[Pure] public function getPrice(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			return !$format ? $this->price : Helpers::FormatCurrency($this->price, $currency, $locale);
		}
	}