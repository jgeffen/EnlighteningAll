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
	
	class Product extends Abstracts\PageType {
		use Traits\Category, Traits\Image, Traits\Gallery, Traits\PDFs, Traits\Stockable, Traits\Taxable;
		
		protected Tables\Website $table = Tables\Website::PRODUCTS;
		protected ?string        $icon;
		protected ?string        $label;
		protected ?string        $merchant;
		protected bool           $is_tip;
		protected bool           $is_fridge;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|static
		 */
		public static function Init(?int $id): ?static {
			return Database::Action("SELECT * FROM `products` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(static::class) ?: NULL;
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
		 * @param PDOStatement $statement
		 *
		 * @return null|static
		 */
		public static function Fetch(PDOStatement $statement): ?static {
			return $statement->fetchObject(static::class) ?: NULL;
		}
		
		/**
		 * @param string $query
		 * @param array  $params
		 *
		 * @return static[]
		 */
		public static function FetchAllString(string $query, array $params = array()): array {
			return Database::Action($query, $params)->fetchAll(PDO::FETCH_CLASS, static::class);
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return static[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
		}
		
		/**
		 * @return bool
		 */
		public function isTip(): bool {
			return $this->is_tip;
		}
		
		/**
		 * @return bool
		 */
		public function isFridge(): bool {
			return $this->is_fridge;
		}
		
		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
		
		/**
		 * @return null|string
		 */
		public function getIcon(): ?string {
			return $this->icon;
		}
		
		/**
		 * @return null|string
		 */
		public function getLabel(): ?string {
			return $this->label;
		}
		
		/**
		 * @return string
		 */
		public function getLink(): string {
			return sprintf("/products/%s", $this->getPageUrl());
		}
		
		/**
		 * @return null|string
		 */
		public function getMerchant(): ?string {
			return $this->merchant;
		}
		
		/**
		 * @return string[]
		 */
		public function getUPCs(): array {
			return Database::Action("SELECT upc_code FROM product_upcs WHERE product_id = :id", array(
				'id' => $this->getId()
			))->fetchAll(PDO::FETCH_COLUMN);
		}
		
		/**
		 * @param string $upc
		 *
		 * @return bool
		 */
		public function addUPC(string $upc): bool {
			return Database::Action("INSERT IGNORE INTO product_upcs (product_id, upc_code) VALUES (:product_id, :upc)", array(
					'product_id' => $this->getId(),
					'upc'        => $upc
				))->rowCount() > 0;
		}
		
		/**
		 * @param string $upc
		 *
		 * @return bool
		 */
		public function removeUPC(string $upc): bool {
			return Database::Action("DELETE FROM product_upcs WHERE product_id = :product_id AND upc_code = :upc", array(
					'product_id' => $this->getId(),
					'upc'        => $upc
				))->rowCount() > 0;
		}
		
		/**
		 * @param int $quantity
		 *
		 * @return bool
		 */
		public function decreaseStock(int $quantity = 1): bool {
			$newQuantity = max(0, $this->getAvailableQuantity() - $quantity);
			
			return Database::Action("UPDATE `products` SET `stock_quantity` = :quantity WHERE `id` = :id", array(
					'quantity' => $newQuantity,
					'id'       => $this->getId()
				))->rowCount() > 0;
		}
	}