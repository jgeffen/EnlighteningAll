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
	
	/** @noinspection PhpUnused */
	
	namespace Items\Events;
	
	use Database;
	use DateTime;
	use Helpers;
	use Items\Interfaces\Item;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Package implements Item {
		use Traits\Item;
		use Traits\Stockable;
		
		protected string  $name;
		protected float   $price;
		protected ?bool   $taxable  = NULL;
		protected ?bool   $seatable = NULL;
		protected ?string $merchant = NULL;
		protected ?bool   $musical  = NULL;
		protected ?string $seats    = NULL;
		protected ?bool   $is_bogo  = NULL;
		protected bool    $published;
		protected string  $published_date;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `event_packages` WHERE `id` = :id", array(
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
		 * Gets form options for Merchants
		 *
		 * @param string|null $type
		 * @param string|null $sub_type
		 * @param string|null $key
		 *
		 * @return array|string|null
		 */
		public static function FormOptions(?string $type = NULL, ?string $sub_type = NULL, ?string $key = NULL): array|string|null {
			$array = match ($type) {
				'merchants' => array(
					'MobiusPay'    => 'Mobius Pay',
					'AuthorizeNet' => 'Authorize.Net'
				),
				default     => array(),
			};
			
			return is_null($key) ? $array : $array[$key] ?? NULL;
		}
		
		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}
		
		/**
		 * @return bool
		 */
		public function isBogo(): bool {
			return $this->is_bogo;
		}
		
		/**
		 * @return null|bool
		 */
		public function isTaxable(): ?bool {
			return $this->taxable;
		}
		
		/**
		 * @return null|bool
		 */
		public function isSeatable(): ?bool {
			return $this->seatable;
		}
		
		/**
		 * @return null|string
		 */
		public function getMerchant(): ?string {
			return $this->merchant;
		}
		
		/**
		 * @return null|bool
		 */
		public function isMusical(): ?bool {
			return $this->musical;
		}
		
		/**
		 * @return array
		 */
		public function getAvailableSeats(): array {
			return $this->getSeats();
		}
		
		/**
		 * @return array
		 */
		public function getSeats(): array {
			$seats = json_decode($this->seats ?? '[]', TRUE);
			return is_array($seats) ? $seats : array();
		}
		
		/**
		 * @param string $seat
		 *
		 * @return bool
		 */
		public function reserveSeat(string $seat): bool {
			$seats = $this->getSeats();
			
			if(!in_array($seat, $seats)) {
				return FALSE;
			}
			
			// Remove the reserved seat
			$updatedSeats = array_values(array_diff($seats, array($seat)));
			
			return Database::Action("UPDATE `event_packages` SET `seats` = :seats WHERE `id` = :id", array(
					'seats' => json_encode($updatedSeats),
					'id'    => $this->getId()
				))->rowCount() > 0;
		}
		
		/**
		 * @param int $quantity
		 *
		 * @return bool
		 */
		public function decreaseStock(int $quantity = 1): bool {
			$newQuantity = max(0, $this->getAvailableQuantity() - $quantity);
			
			return Database::Action("UPDATE `event_packages` SET `stock_quantity` = :quantity WHERE `id` = :id", array(
					'quantity' => $newQuantity,
					'id'       => $this->getId()
				))->rowCount() > 0;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return float|string
		 */
		//public function getPrice(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
		//	return !$format ? $this->price : Helpers::FormatCurrency($this->price, $currency, $locale);
		//}
        public function getPrice(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
            // Prevent negative or unset values, allow zero (free)
            $price = $this->price ?? 0.00;
            if ($price < 0) $price = 0.00;

            return !$format ? $price : Helpers::FormatCurrency($price, $currency, $locale);
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