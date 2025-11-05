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
	
	namespace AuthorizeNet\CIM\Models;
	
	class Order {
		protected float   $amount;
		protected ?string $description;
		protected ?string $id;
		protected ?string $ip_address;
		protected ?string $po_number;
		protected float   $shipping;
		protected float   $tax;
		protected float   $discount;
		protected ?string $comments;
		protected ?string $invoice;
		protected ?string $customer_vault_id;
		protected ?string $billing_id;
		
		/**
		 * @param float       $amount
		 * @param null|string $description
		 * @param null|string $id
		 * @param null|string $ip_address
		 * @param null|string $po_number
		 * @param float       $shipping
		 * @param float       $tax
		 * @param float       $discount
		 * @param null|string $comments
		 * @param null|string $invoice
		 * @param null|string $customer_vault_id
		 * @param null|string $billing_id
		 */
		public function __construct(float $amount = 0.00, ?string $description = NULL, ?string $id = NULL, ?string $ip_address = NULL, ?string $po_number = NULL, float $shipping = 0.00, float $tax = 0.00, float $discount = 0.00, ?string $comments = NULL, ?string $invoice = NULL, ?string $customer_vault_id = NULL, ?string $billing_id = NULL) {
			$this->amount            = $amount;
			$this->description       = $description;
			$this->id                = $id;
			$this->ip_address        = $ip_address;
			$this->po_number         = $po_number;
			$this->shipping          = $shipping;
			$this->tax               = $tax;
			$this->discount          = $discount;
			$this->comments          = $comments;
			$this->invoice           = $invoice;
			$this->customer_vault_id = $customer_vault_id;
			$this->billing_id        = $billing_id;
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
		 * @return float
		 */
		public function getShipping(): float {
			return $this->shipping;
		}
		
		/**
		 * @return float
		 */
		public function getTax(): float {
			return $this->tax;
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
		 * @return float
		 */
		public function getAmount(): float {
			return $this->amount;
		}
		
		/**
		 * @return float
		 */
		public function getDiscount(): float {
			return $this->discount;
		}
		
		/**
		 * @return null|string
		 */
		public function getCustomerVaultId(): ?string {
			return $this->customer_vault_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingId(): ?string {
			return $this->billing_id;
		}
	}