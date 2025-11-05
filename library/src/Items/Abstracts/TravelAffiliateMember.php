<?php
	/*
		Copyright (c) 2021 FenclWebDesign.com
		This script may not be copied, reproduced or altered in whole or in part.
		We check the Internet regularly for illegal copies of our scripts.
		Do not edit or copy this script for someone else, because you will be held responsible as well.
		This copyright shall be enforced to the full extent permitted by law.
		Licenses to use this script on a single website may be purchased from FenclWebDesign.com
		@Author: Daerik
		*/
	
	/** @noinspection PhpUnused */
	
	namespace Items\Abstracts;
	
	use Database;
	use DateTime;
	use Items\Interfaces;
	use Items\Traits;
	use Items\TravelAffiliateMembers;
	
	abstract class TravelAffiliateMember implements Interfaces\TravelAffiliateMember {
		use Traits\Item;
		
		protected ?DateTime $last_online;
		protected string    $username;
		protected string    $email;
		protected string    $password;
		protected string    $first_name;
		protected string    $first_names;
		protected string    $full_name;
		protected string    $full_name_last;
		protected string    $last_name;
		protected ?string   $phone;
		protected string    $travel_agency;
		protected string    $ein_number;
		protected ?string   $address_line_1;
		protected ?string   $address_line_2;
		protected ?string   $address_city;
		protected ?string   $address_country;
		protected ?string   $address_state;
		protected ?string   $address_zip_code;
		protected string    $notes;
		protected float     $ticket_commission_rate;
		protected float     $room_commission_rate;
		protected string    $admin_commission_notes;
		protected ?string   $terms_privacy_signature;
		protected ?string   $affiliate_terms_conditions_signature;
		protected ?string   $admin_approval_signature;
		protected bool      $approved;
		protected bool      $banned;
		protected bool      $verified;
		protected bool      $is_employee;
		
		/**
		 * @param null|string $descriptor
		 *
		 * @return string
		 */
		public function getAlt(?string $descriptor = NULL): string {
			return htmlspecialchars($this->getTitle($descriptor), ENT_COMPAT);
		}
		
		/**
		 * @param null|string $descriptor
		 *
		 * @return string
		 */
		public function getTitle(?string $descriptor = NULL): string {
			return $descriptor ? sprintf("%s's %s", $this->getUsername(), $descriptor) : $this->getUsername();
		}
		
		/**
		 * @return string
		 */
		public function getUsername(): string {
			return $this->username;
		}
		
		/**
		 * @return string
		 */
		public function getEmail(): string {
			return $this->email;
		}
		
		/**
		 * @return string
		 */
		public function getPasswordHash(): string {
			return $this->password;
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
		public function getAddressCity(): ?string {
			return $this->address_city;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressCountry(): ?string {
			return $this->address_country;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressState(): ?string {
			return $this->address_state;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressZipCode(): ?string {
			return $this->address_zip_code;
		}
		
		/**
		 * @return string
		 */
		public function getFullName(): string {
			!isset($this->full_name) && $this->setFullName();
			return $this->full_name;
		}
		
		/**
		 * @return void
		 */
		private function setFullName(): void {
			$this->full_name = sprintf("%s %s", $this->getFirstName(), $this->getLastName());
		}
		
		/**
		 * @return string
		 */
		public function getFirstName(): string {
			return $this->first_name;
		}
		
		/**
		 * @return string
		 */
		public function getLastName(): string {
			return $this->last_name;
		}
		
		/**
		 * @return string
		 */
		public function getFullNameLast(): string {
			!isset($this->full_name_last) && $this->setFullNameLast();
			return $this->full_name_last;
		}
		
		/**
		 * @return void
		 */
		private function setFullNameLast(): void {
			$this->full_name_last = sprintf("%s, %s", $this->getLastName(), $this->getFirstName());
		}
		
		/**
		 * @return null|string
		 */
		public function getPhone(): ?string {
			return $this->phone;
		}
		
		/**
		 * @return string
		 */
		public function getTravelAgency(): string {
			return $this->travel_agency;
		}
		
		/**
		 * @return string
		 */
		public function getTravelAgencyEinNumber(): string {
			return $this->ein_number;
		}
		
		/**
		 * @return string
		 */
		public function getNotes(): string {
			return $this->notes;
		}
		
		/**
		 * @return float
		 */
		public function getTicketCommisionRate(): float {
			return $this->ticket_commission_rate;
		}
		
		/**
		 * @return float
		 */
		public function getRoomCommisionRate(): float {
			return $this->room_commission_rate;
		}
		
		/**
		 * Returns the admin commission notes as an associative array.
		 *
		 * @return array
		 */
		public function getAdminCommissionNote(): array {
			return json_decode($this->admin_commission_notes, TRUE);
		}
		
		/**
		 * @return bool
		 */
		public function isApproved(): bool {
			return $this->approved;
		}
		
		/**
		 * @return bool
		 */
		public function isBanned(): bool {
			return $this->banned;
		}
		
		/**
		 * @return bool
		 */
		public function isVerified(): bool {
			return $this->verified;
		}
		
		/**
		 * @return null|string
		 */
		public function getTermsPrivacySignature(): ?string {
			return $this->terms_privacy_signature;
		}
		
		/**
		 * @return null|string
		 */
		public function getAffiliateTermsConditionsSignature(): ?string {
			return $this->affiliate_terms_conditions_signature;
		}
		
		/**
		 * @return null|string
		 */
		public function getAdminApprovalSignature(): ?string {
			return $this->admin_approval_signature;
		}
		
		/**
		 * @return null|DateTime
		 */
		public function getLastOnline(): ?DateTime {
			return $this->last_online ??= TravelAffiliateMembers\Polling::Fetch(Database::Action("SELECT * FROM `travel_affiliate_member_polling` WHERE `member_id` = :member_id", array(
				'member_id' => $this->getId()
			)))?->getLastTimestamp();
		}
	}
