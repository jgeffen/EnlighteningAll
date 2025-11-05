<?php
	/*
	Copyright (c) 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	namespace Items\Members;
	
	use Database;
	use DateTime;
	use Items;
	use Items\Enums\Statuses;
	use Items\Interfaces;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Subscription implements Interfaces\Item {
		use Traits\Item;
		
		private ?Items\Member       $member;
		private ?Items\Subscription $subscription;
		private ?Items\Transaction  $transaction;
		
		private string  $status;
		private int     $member_id;
		private int     $subscription_id;
		private ?int    $transaction_id;
		private string  $date_start;
		private string  $date_renewal;
		private ?string $date_cancellation;
		private ?string $details;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `subscriptions` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
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
		 * @return null|Statuses\Subscription
		 */
		public function getStatus(): ?Statuses\Subscription {
			return Statuses\Subscription::lookup($this->status);
		}
		
		/**
		 * @return int
		 */
		public function getMemberId(): int {
			return $this->member_id;
		}
		
		/**
		 * @return null|Items\Member
		 */
		public function getMember(): ?Items\Member {
			!isset($this->member) && $this->setMember();
			return $this->member;
		}
		
		/**
		 * @return void
		 */
		public function setMember(): void {
			$this->member = Items\Member::Init($this->getMemberId());
		}
		
		/**
		 * @return int
		 */
		public function getSubscriptionId(): int {
			return $this->subscription_id;
		}
		
		/**
		 * @return null|Items\Subscription
		 */
		public function getSubscription(): ?Items\Subscription {
			return $this->subscription ??= Items\Subscription::Init($this->getSubscriptionId());
		}
		
		/**
		 * @return null|int
		 */
		public function getTransactionId(): ?int {
			return $this->transaction_id;
		}
		
		/**
		 * @return null|Items\Transaction
		 */
		public function getTransaction(): ?Items\Transaction {
			return $this->transaction ??= Items\Transaction::Init($this->getTransactionId());
		}
		
		/**
		 * @return DateTime
		 */
		public function getStartDate(): DateTime {
			return date_create($this->date_start);
		}
		
		/**
		 * @return DateTime
		 */
		public function getRenewalDate(): DateTime {
			return date_create($this->date_renewal);
		}
		
		/**
		 * @return null|DateTime
		 */
		public function getCancellationDate(): ?DateTime {
			return is_null($this->date_cancellation) ? NULL : date_create($this->date_cancellation);
		}
		
		/**
		 * @return null|string
		 */
		public function getDetails(): ?string {
			return $this->details;
		}
		
		/**
		 * @return bool
		 */
		public function isFree(): bool {
			return (bool)$this->getSubscription()?->isFree();
		}
		
		/**
		 * @return bool
		 */
		public function isPaid(): bool {
			return (bool)$this->getSubscription()?->isPaid();
		}
	}