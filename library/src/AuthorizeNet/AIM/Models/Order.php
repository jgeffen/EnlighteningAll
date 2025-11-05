<?php
	/*
	Copyright (c) 2023 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	namespace AuthorizeNet\AIM\Models;
	
	use Helpers;
	
	class Order {
		protected float   $amount          = 0.00;
		protected ?string $description     = NULL;
		protected ?string $id              = NULL;
		protected ?string $ip_address      = NULL;
		protected ?string $po_number       = NULL;
		protected float   $shipping        = 0.00;
		protected float   $sales_tax       = 0.00;
		protected float   $discount        = 0.00;
		protected ?string $comments        = NULL;
		protected ?string $invoice         = NULL;
		protected ?int    $duplicateWindow = NULL;
		
		/**
		 * @param float       $amount
		 * @param null|string $description
		 * @param null|string $id
		 * @param null|string $ip_address
		 * @param null|string $po_number
		 * @param float       $shipping
		 * @param float       $sales_tax
		 * @param float       $discount
		 * @param null|string $comments
		 * @param null|string $invoice
		 * @param null|int    $duplicateWindow
		 */
		public function __construct(
			float   $amount = 0.00,
			?string $description = NULL,
			?string $id = NULL,
			?string $ip_address = NULL,
			?string $po_number = NULL,
			float   $shipping = 0.00,
			float   $sales_tax = 0.00,
			float   $discount = 0.00,
			?string $comments = NULL,
			?string $invoice = NULL,
			?int    $duplicateWindow = NULL
		) {
			$this->amount          = $amount;
			$this->description     = $description;
			$this->id              = $id;
			$this->ip_address      = $ip_address;
			$this->po_number       = $po_number;
			$this->shipping        = $shipping;
			$this->sales_tax       = $sales_tax;
			$this->discount        = $discount;
			$this->comments        = $comments;
			$this->invoice         = $invoice;
			$this->duplicateWindow = $duplicateWindow;
		}
		
		/**
		 * @return null|string
		 */
		public function getDescription(): ?string {
			return $this->description;
		}
		
		/**
		 * @return null|string
		 */
		public function getId(): ?string {
			return $this->id;
		}
		
		/**
		 * @return null|string
		 */
		public function getIpAddress(): ?string {
			return $this->ip_address;
		}
		
		/**
		 * @return null|string
		 */
		public function getPoNumber(): ?string {
			return $this->po_number;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency Locale in which the number would be formatted (locale name, e.g. en_CA).
		 * @param string $locale   The 3-letter ISO 4217 currency code indicating the currency to use.
		 *
		 * @return float|string
		 */
		public function getShipping(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): string|float {
			$shipping = $this->shipping ?? 0.00;
			
			return $format
				? Helpers::FormatCurrency($shipping, $currency, $locale)
				: $shipping;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency Locale in which the number would be formatted (locale name, e.g. en_CA).
		 * @param string $locale   The 3-letter ISO 4217 currency code indicating the currency to use.
		 *
		 * @return float|string
		 */
		public function getSalesTax(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			return $format ? Helpers::FormatCurrency($this->sales_tax, $currency, $locale) : number_format($this->sales_tax, 2, '.', '');
		}
		
		/**
		 * @param bool $nl2br
		 *
		 * @return null|string
		 */
		public function getComments(bool $nl2br = FALSE): ?string {
			return $nl2br && !is_null($this->comments) ? nl2br($this->comments) : $this->comments;
		}
		
		/**
		 * @return null|string
		 */
		public function getInvoice(): ?string {
			return $this->invoice;
		}
		
		/**
		 * @param int $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 * @param int $depth Set the maximum depth. Must be greater than zero.
		 *
		 * @return string
		 */
		public function toJson(int $flags = JSON_ERROR_NONE, int $depth = 512): string {
			return match ($flags) {
				JSON_HEX_APOS => htmlspecialchars(json_encode($this->toArray(), JSON_ERROR_NONE, $depth), ENT_QUOTES),
				JSON_HEX_QUOT => htmlspecialchars(json_encode($this->toArray(), JSON_ERROR_NONE, $depth), ENT_COMPAT),
				default       => json_encode($this->toArray(), $flags, $depth),
			};
		}
		
		/**
		 * @return array
		 */
		public function toArray(): array {
			return get_object_vars($this);
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale *
		 *
		 * @return float
		 */
		public function getAmount(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float {
			return $this->amount;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale *
		 *
		 * @return float
		 */
		public function getDiscount(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float {
			return $this->discount;
		}
	}