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
	
	namespace Items\Members;
	
	use Exception;
	use Database;
	use DateTime;
	use Items\Interfaces\Item;
	use Items\Member;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Wallet implements Item {
		use Traits\Item;
		
		private int     $member_id;
		private ?Member $member;
		private string  $account_number;
		private string  $account_type;
		private string  $expiration_date;
		private string  $customer_vault_id;
		private string  $payment_profile_id;
		private string  $customer_profile_id;
		private bool    $default;
		private string  $billing_id;
		private ?string $billing_address_line_1;
		private ?string $billing_address_line_2;
		private ?string $billing_city;
		private ?string $billing_company;
		private ?string $billing_country;
		private ?string $billing_email;
		private ?string $billing_fax;
		private ?string $billing_first_name;
		private ?string $billing_last_name;
		private ?string $billing_phone;
		private ?string $billing_state;
		private ?string $billing_zip_code;
		private float   $points;
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|self
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
		 * @return null|Member
		 */
		public function getMember(): ?Member {
			!isset($this->member) && $this->setMember();
			return $this->member;
		}
		
		/**
		 * @return void
		 */
		private function setMember(): void {
			$this->member = Member::Init($this->getMembersId());
		}
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `member_wallets` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @return int
		 */
		public function getMembersId(): int {
			return $this->member_id;
		}
		
		/**
		 * @param bool $last_4
		 *
		 * @return string
		 */
		public function getAccountNumber(bool $last_4 = FALSE): string {
			return $last_4 ? substr($this->account_number, -4) : $this->account_number;
		}
		
		/**
		 * @return string
		 */
		public function getAccountType(): string {
			return $this->account_type;
		}
		
		/**
		 * @return string
		 */
		public function getCustomerVaultId(): string {
			return $this->customer_vault_id;
		}
		
		/**
		 * @return string
		 */
		public function getPaymentProfileId(): string {
			return $this->payment_profile_id;
		}
		
		/**
		 * @return bool
		 */
		public function isDefault(): bool {
			return $this->default;
		}
		
		/**
		 * @return string
		 */
		public function getBillingId(): string {
			return $this->billing_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingAddressLine1(): ?string {
			return $this->billing_address_line_1;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingAddressLine2(): ?string {
			return $this->billing_address_line_2;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingCity(): ?string {
			return $this->billing_city;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingCompany(): ?string {
			return $this->billing_company;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingCountry(): ?string {
			return $this->billing_country;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingEmail(): ?string {
			return $this->billing_email;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingFax(): ?string {
			return $this->billing_fax;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingFirstName(): ?string {
			return $this->billing_first_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingLastName(): ?string {
			return $this->billing_last_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingPhone(): ?string {
			return $this->billing_phone;
		}
		
		/**
		 * @return null|string
		 */
		public function getBillingState(): ?string {
			return $this->billing_state;
		}
		
		/**
		 * @return null|int
		 */
		public function getBillingZipCode(): ?int {
			return $this->billing_zip_code;
		}
		
		/**
		 * @return DateTime
		 */
		public function getExpirationDate(): DateTime {
			return date_create($this->expiration_date);
		}
		
		/**
		 * @return int
		 */
		public function getCustomerProfileId(): int {
			return $this->customer_profile_id;
		}
		
		/**
		 * @return float
		 */
		public function getPoints(): float {
			return $this->points;
		}
		
		/**
		 * Decrease wallet points for a member
		 *
		 * @param float $points
		 * @return bool
		 */
		public function deductPoints(float $points): bool {
			// Sanitize
			$points = round($points, 2);
			
			Database::Action("UPDATE member_wallets SET points = points - :points WHERE member_id = :member_id AND points >= :points", array(
				'points'    => $points,
				'member_id' => $this->member_id
			));
			
			return true;
		}
		
		/**
		 * Decrease wallet points for a member
		 *
		 * @param float $points
		 * @return bool
		 */
		public function increasePoints(float $points): bool {
			// Sanitize
			$points = round($points, 2);
			
			Database::Action("UPDATE member_wallets SET points = points + :points WHERE member_id = :member_id AND points >= :points", array(
				'points'    => $points,
				'member_id' => $this->member_id
			));
			
			return true;
		}
		
		/**
		 * @param string $modifier A date/time string.
		 *
		 * @return bool
		 *
		 * @throws \DateMalformedStringException
		 * @link https://secure.php.net/manual/en/datetime.formats.php Date and Time Formats
		 */
		public function isExpiring(string $modifier = '+3 Months'): bool {
			return !$this->isExpired() && date_create()->modify($modifier) > date_create($this->expiration_date);
		}
		
		/**
		 * @return bool
		 */
		public function isExpired(): bool {
			return date_create() > date_create($this->expiration_date);
		}
	}