<?php
	/*
	 * Copyright (c) 2025 FenclWebDesign.com
	 * This script may not be copied, reproduced or altered in whole or in part.
	 * We check the Internet regularly for illegal copies of our scripts.
	 * Do not edit or copy this script for someone else, because you will be held responsible as well.
	 * This copyright shall be enforced to the full extent permitted by law.
	 * Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	 * @Author: Deryk
	 */
	
	namespace Items\Interfaces;
	
	use Items;
	
	interface Sellable extends Item {
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return float|string
		 */
		public function getPrice(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string;
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return float|string
		 */
		public function getShipping(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string;
		
		/**
		 * @return bool
		 */
		public function isShippable(): bool;
		
		/**
		 * @return bool
		 */
		public function isOutOfStock(): bool;
		
		/**
		 * @return int
		 */
		public function getAvailableQuantity(): int;
		
		/**
		 * @return int
		 */
		public function getStockQuantity(): int;
		
		/**
		 * @return int
		 */
		public function getSoldQuantity(): int;
		
		/**
		 * @return bool
		 */
		public function isInStock(): bool;
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return float|string
		 */
		public function getSalesTax(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string;
		
		/**
		 * @return bool
		 */
		public function isTaxable(): bool;
		
		/**
		 * @return string
		 */
		public function getLabel(): string;
	}