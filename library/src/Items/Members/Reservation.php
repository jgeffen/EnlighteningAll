<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	/** @noinspection PhpUnused */
	
	namespace Items\Members;
	
	use Database;
	use Helpers;
	use Items\Enums\Statuses;
	use Items\Event;
	use Items\Events\Package;
	use Items\Interfaces\Item;
	use Items\Traits;
	use Items\Transaction;
	use Items\Member;
	use PDO;
	use PDOStatement;
	
	class Reservation implements Item {
		use Traits\Item;
		
		private ?Event       $event;
		private ?Package     $package;
		private ?Member      $member;
		private ?Transaction $transaction;
		
		protected string  $status;
		protected int     $event_id;
		protected ?int    $event_package_id;
		protected ?int    $member_id;
		protected ?int    $transaction_id;
		protected ?string $name_on_pass;
		protected ?string $phone;
		protected ?int    $item_count;
		protected ?int    $item_count_total;
		protected ?string $seat_selected;
		protected ?string $song_selected;
		protected ?float  $package_amount;
		protected ?string $package_name;
		protected float   $total_amount;
		protected float   $total_discount;
		protected float   $total_paid;
		protected ?string $comments;
		
		/**
		 * @param null|int $identifier
		 *
		 * @return null|$this
		 */
		static function Init(?int $identifier): ?self {
			return Database::Action("SELECT * FROM `member_reservations` WHERE `id` = :id", array(
				'id' => $identifier
			))->fetchObject(self::class) ?: NULL;
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
		 * @param PDOStatement $statement
		 *
		 * @return static[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
		}
		
		/**
		 * @return null|Statuses\Reservation
		 */
		public function getStatus(): ?Statuses\Reservation {
			return Statuses\Reservation::lookup($this->status);
		}
		
		/**
		 * @return int
		 */
		public function getEventId(): int {
			return $this->event_id;
		}
		
		/**
		 * @return null|Event
		 */
		public function getEvent(): ?Event {
			!isset($this->event) && $this->setEvent();
			return $this->event;
		}
		
		/**
		 * @return void
		 */
		private function setEvent(): void {
			$this->event = Event::Init($this->getEventId());
		}
		
		/**
		 * @return null|int
		 */
		public function getPackageId(): ?int {
			return $this->event_package_id;
		}
		
		/**
		 * @return null|Package
		 */
		public function getPackage(): ?Package {
			return $this->package ??= Package::Init($this->getPackageId());
		}
		
		/**
		 * @return null|int
		 */
		public function getMemberId(): ?int {
			return $this->member_id;
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
			$this->member = Member::Init($this->getMemberId());
		}
		
		/**
		 * @return null|string
		 */
		public function getSeatSelected(): ?string {
			return $this->seat_selected;
		}
		
		/**
		 * @return null|string
		 */
		public function getSongSelected(): ?string {
			return $this->song_selected;
		}
		
		/**
		 * @return null|string
		 */
		public function getTransactionId(): ?string {
			return $this->transaction_id;
		}
		
		/**
		 * @return null|Transaction
		 */
		public function getTransaction(): ?Transaction {
			!isset($this->transaction) && $this->setTransaction();
			return $this->transaction;
		}
		
		/**
		 * @return void
		 */
		public function setTransaction(): void {
			$this->transaction = Transaction::Init($this->getTransactionId());
		}
		
		/**
		 * @return null|string
		 */
		public function getNameOnPass(): ?string {
			return $this->name_on_pass;
		}
		
		/**
		 * @return null|string
		 */
		public function getPhone(): ?string {
			return $this->phone;
		}
		
		/**
		 * @return null|int
		 */
		public function getItemCount(): ?int {
			return $this->item_count;
		}
		
		/**
		 * @return null|int
		 */
		public function getItemCountTotal(): ?int {
			return $this->item_count_total;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return null|float|string
		 */
		public function getPackageAmount(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string|null {
			return !$format || is_null($this->package_amount) ? $this->package_amount : Helpers::FormatCurrency($this->package_amount, $currency, $locale);
		}
		
		/**
		 * @return null|string
		 */
		public function getPackageName(): ?string {
			return $this->package_name;
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return float|string
		 */
		public function getTotalAmount(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			return !$format ? $this->total_amount : Helpers::FormatCurrency($this->total_amount, $currency, $locale);
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return float|string
		 */
		public function getTotalDiscount(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			return !$format ? $this->total_discount : Helpers::FormatCurrency($this->total_discount, $currency, $locale);
		}
		
		/**
		 * @param bool   $format
		 * @param string $currency
		 * @param string $locale
		 *
		 * @return float|string
		 */
		public function getTotalPaid(bool $format = FALSE, string $currency = 'USD', string $locale = 'en_US'): float|string {
			return !$format ? $this->total_paid : Helpers::FormatCurrency($this->total_paid, $currency, $locale);
		}
		
		/**
		 * @return null|string
		 */
		public function getComments(): ?string {
			return $this->comments;
		}
		
		/**
		 * @return bool
		 */
		public function isPaid(): bool {
			return $this->getStatus() == Statuses\Reservation::PAID;
		}
	}