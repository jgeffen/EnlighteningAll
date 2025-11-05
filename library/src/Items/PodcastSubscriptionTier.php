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
	use Items\Interfaces\Item;
	use JetBrains\PhpStorm\Pure;
	use PDO;
	use PDOStatement;
	
	class PodcastSubscriptionTier implements Item {
		use Traits\Item;
		
		protected string  $name;
		protected ?string $benefits;
		protected ?string $content;
		protected string  $icon;
		protected float   $price;
		protected int     $position;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `podcast_subscription_tiers` WHERE `id` = :id", array(
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
		public function getName(): string {
			return $this->name;
		}
		
		/**
		 * @return null|string
		 */
		public function getBenefits(): ?string {
			return $this->benefits;
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
		 * @param string $fa_class
		 *
		 * @return string
		 */
		public function renderIcon(string $fa_class = 'fa-solid'): string {
			return sprintf("<i class=\"%s %s\"></i>", $fa_class, $this->getIcon());
		}
		
		/**
		 * @return string
		 */
		public function getIcon(): string {
			return $this->icon;
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
		
		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
	}