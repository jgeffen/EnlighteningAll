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
	
	class Billing {
		protected ?string $address_line_1 = NULL;
		protected ?string $address_line_2 = NULL;
		protected ?string $city           = NULL;
		protected ?string $company        = NULL;
		protected ?string $country        = NULL;
		protected ?string $email          = NULL;
		protected ?string $fax            = NULL;
		protected ?string $first_name     = NULL;
		protected ?string $last_name      = NULL;
		protected ?string $phone          = NULL;
		protected ?string $state          = NULL;
		protected ?string $website        = NULL;
		protected ?string $zip_code       = NULL;
		
		/**
		 * @param null|string $address_line_1
		 * @param null|string $address_line_2
		 * @param null|string $city
		 * @param null|string $company
		 * @param null|string $country
		 * @param null|string $email
		 * @param null|string $fax
		 * @param null|string $first_name
		 * @param null|string $last_name
		 * @param null|string $phone
		 * @param null|string $state
		 * @param null|string $website
		 * @param null|string $zip_code
		 */
		public function __construct(
			?string $address_line_1 = null,
			?string $address_line_2 = null,
			?string $city = null,
			?string $company = null,
			?string $country = null,
			?string $email = null,
			?string $fax = null,
			?string $first_name = null,
			?string $last_name = null,
			?string $phone = null,
			?string $state = null,
			?string $website = null,
			?string $zip_code = null
		) {
			$this->address_line_1 = $address_line_1;
			$this->address_line_2 = $address_line_2;
			$this->city           = $city;
			$this->company        = $company;
			$this->country        = $country;
			$this->email          = $email;
			$this->fax            = $fax;
			$this->first_name     = $first_name;
			$this->last_name      = $last_name;
			$this->phone          = $phone;
			$this->state          = $state;
			$this->website        = $website;
			$this->zip_code       = $zip_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressLine1(): ?string {
			return $this->address_line_1;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressLine2(): ?string {
			return $this->address_line_2;
		}
		
		/**
		 * @return null|string
		 */
		public function getCity(): ?string {
			return $this->city;
		}
		
		/**
		 * @return null|string
		 */
		public function getCompany(): ?string {
			return $this->company;
		}
		
		/**
		 * @return null|string
		 */
		public function getCountry(): ?string {
			return $this->country;
		}
		
		/**
		 * @return null|string
		 */
		public function getEmail(): ?string {
			return $this->email;
		}
		
		/**
		 * @return null|string
		 */
		public function getFax(): ?string {
			return $this->fax;
		}
		
		/**
		 * @return null|string
		 */
		public function getFirstName(): ?string {
			return $this->first_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getLastName(): ?string {
			return $this->last_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getPhone(): ?string {
			return $this->phone;
		}
		
		/**
		 * @return null|string
		 */
		public function getState(): ?string {
			return $this->state;
		}
		
		/**
		 * @return null|string
		 */
		public function getWebsite(): ?string {
			return $this->website;
		}
		
		/**
		 * @return null|string
		 */
		public function getZipCode(): ?string {
			return $this->zip_code;
		}
		
		/**
		 * @return string
		 */
		public function getFullName(): string {
			return implode(' ', array_filter(array($this->first_name, $this->last_name)));
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