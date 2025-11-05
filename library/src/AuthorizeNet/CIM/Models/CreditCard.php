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
	
	use DateTime;
	class CreditCard {
		protected ?string $account;
		protected ?string $expiration;
		protected ?string $cvv;
		protected ?string $type;
		
		/**
		 * @param null|string $account
		 * @param null|string $expiration
		 * @param null|string $cvv
		 * @param null|string $type
		 */
		public function __construct(?string $account = NULL, ?string $expiration = NULL, ?string $cvv = NULL, ?string $type = NULL) {
			$this->account    = $account;
			$this->expiration = $expiration;
			$this->cvv        = $cvv;
			$this->type       = $type;
		}
		
		/**
		 * @return null|string
		 */
		public function getAccount(): ?string {
			return $this->account;
		}
		
		/**
		 * @param string $format
		 *
		 * @return null|string
		 */
		public function getExpiration(string $format = 'm/y'): ?string {
			$expiry = DateTime::createFromFormat('my', $this->expiration);
			return $expiry ? $expiry->format($format) : NULL;
		}
		
		/**
		 * @return null|string
		 */
		public function getCvv(): ?string {
			return $this->cvv;
		}
		
		/**
		 * @return null|string
		 */
		public function getType(): ?string {
			return $this->type;
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
	}