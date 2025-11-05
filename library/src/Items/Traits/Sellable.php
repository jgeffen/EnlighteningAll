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
	
	namespace Items\Traits;
	
	use Helpers;
	use Items\Traits\Discountable;
	use Items\Traits\Priceable;
	use Items\Traits\Stockable;
	use Items\Traits\Taxable;
	
	trait Sellable {
		use Discountable;
		use Priceable;
		use Taxable;
		
		protected string $label;
		
		private int $quantity = 1;
		
		/**
		 * @param null|int $length
		 *
		 * @return string
		 */
		public function getLabel(?int $length = NULL): string {
			return is_null($length) ? $this->label : Helpers::Truncate($this->label, $length);
		}
		
		/**
		 * @return int
		 */
		public function getQuantity(): int {
			return $this->quantity;
		}
		
		/**
		 * @param int $quantity
		 *
		 * @return static
		 */
		public function setQuantity(int $quantity): static {
			$this->quantity = $quantity;
			
			return $this;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency Locale in which the number would be formatted (locale name, e.g. en_CA).
		 * @param string $locale   The 3-letter ISO 4217 currency code indicating the currency to use.
		 *
		 * @return float|string
		 */
		public function getTotal(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			$total = max(0, $this->getSubtotal() + $this->getSalesTax() + $this->getShipping() - $this->getDiscount());
			
			return !$format ? $total : Helpers::FormatCurrency($total, $currency, $locale);
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency Locale in which the number would be formatted (locale name, e.g. en_CA).
		 * @param string $locale   The 3-letter ISO 4217 currency code indicating the currency to use.
		 *
		 * @return float|string
		 */
		public function getSubtotal(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			$subtotal = $this->getPrice() * $this->quantity;
			
			return !$format ? $subtotal : Helpers::FormatCurrency($subtotal, $currency, $locale);
		}
	}
	