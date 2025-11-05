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
	
	namespace Items\Collections;
	
	use ArrayIterator;
	use Database;
	use Items\Abstracts;
	use Items\Members;
	use PDOStatement;
	
	class Tickets extends ArrayIterator {
		private ?Abstracts\Member $member;
		
		/**
		 * @param PDOStatement          $statement
		 * @param null|Abstracts\Member $member
		 */
		public function __construct(PDOStatement $statement, ?Abstracts\Member $member = NULL) {
			$this->member = $member;
			
			parent::__construct(Members\Ticket::FetchAll($statement));
		}
		
		/**
		 * @return Members\Ticket
		 */
		public function current(): Members\Ticket {
			return parent::current();
		}
		
		/**
		 * @return Members\Ticket
		 */
		public function newest(): Members\Ticket {
			return array_reduce(iterator_to_array($this), function(?Members\Ticket $a, ?Members\Ticket $b) {
				return $a ? ($a?->getTimestamp() > $b?->getTimestamp() ? $a : $b) : $b;
			});
		}
		
		/**
		 * @return Members\Ticket
		 */
		public function oldest(): Members\Ticket {
			return array_reduce(iterator_to_array($this), function(?Members\Ticket $a, ?Members\Ticket $b) {
				return $a ? ($a?->getTimestamp() < $b?->getTimestamp() ? $a : $b) : $b;
			});
		}
		
		/**
		 * @return bool
		 */
		public function empty(): bool {
			return $this->count() === 0;
		}
		
		/**
		 * @return null|Abstracts\Member
		 */
		public function getMember(): ?Abstracts\Member {
			return $this->member;
		}
		
		/**
		 * @return array
		 */
		public function getArrayCopy(): array {
			return array_map(fn(Members\Ticket $item): array => $item->toArray(), iterator_to_array($this));
		}
		
		/**
		 * @return void
		 */
		public function markRead(): void {
			Database::Action("UPDATE `member_tickets` SET `read` = TRUE WHERE `member_id` != :member_id AND JSON_CONTAINS(:json_array, `id`)", array(
				'member_id'  => $this->getMember()?->getId(),
				'json_array' => json_encode(array_column($this->getArrayCopy(), 'id'))
			));
		}
		
		/**
		 * @return string
		 */
		public function renderAll(): string {
			return implode(PHP_EOL, array_map(fn(Members\Ticket $ticket): ?string => $ticket->renderHTML(), iterator_to_array($this)));
		}
		
		/**
		 * @return Members\Ticket[]
		 */
		public function toArray(): array {
			return iterator_to_array($this);
		}
		
		/**
		 * @param bool $admin
		 *
		 * @return int
		 */
		public function unread(bool $admin = FALSE): int {
			return count(array_filter($this->toArray(), fn(Members\Ticket $ticket) => !$ticket->isRead($admin)));
		}
	}