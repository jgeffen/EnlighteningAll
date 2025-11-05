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
	use Config;
	
	trait Taxable {
		protected float $sales_tax;
		protected bool  $taxable;
		protected float $price;
		protected float $discount = 0.00;
		
		/**
		 * @param bool   $format
		 * @param string $currency Locale in which the number would be formatted (locale name, e.g. en_CA).
		 * @param string $locale   The 3-letter ISO 4217 currency code indicating the currency to use.
		 *
		 * @return float|string
		 */
		public function getSalesTax(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			$config = new Config\Store();
			
			if(!$this->isTaxable()) {
				$this->sales_tax = 0.00;
			} elseif(!isset($this->sales_tax)) {
				$this->sales_tax = max(0, $this->price - $this->discount) * $config->getSalesTaxRate();
			}
			
			return !$format ? $this->sales_tax : Helpers::FormatCurrency($this->sales_tax, $currency, $locale);
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency Locale in which the number would be formatted (locale name, e.g. en_CA).
		 * @param string $locale   The 3-letter ISO 4217 currency code indicating the currency to use.
		 *
		 * @return float|string
		 */
		public function getDiscount(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			return !$format ? $this->discount : Helpers::FormatCurrency($this->discount, $currency, $locale);
		}
		
		/**
		 * @param null|float $discount
		 *
		 * @return static
		 */
		public function setDiscount(?float $discount): static {
			$this->discount = $discount ?? 0.00;
			
			return $this;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency Locale in which the number would be formatted (locale name, e.g. en_CA).
		 * @param string $locale   The 3-letter ISO 4217 currency code indicating the currency to use.
		 *
		 * @return float|string
		 */
		public function getPrice(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			return !$format ? $this->price : Helpers::FormatCurrency($this->price, $currency, $locale);
		}
		
		/**
		 * @return bool
		 */
		public function isTaxable(): bool {
			return $this->taxable;
		}
	}
	