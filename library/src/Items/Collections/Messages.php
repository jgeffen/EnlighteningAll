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
	
	class Messages extends ArrayIterator {
		private ?Abstracts\Member $member;
		
		/**
		 * @param PDOStatement          $statement
		 * @param null|Abstracts\Member $member
		 */
		public function __construct(PDOStatement $statement, ?Abstracts\Member $member = NULL) {
			$this->member = $member;
			
			parent::__construct(Members\Message::FetchAll($statement));
		}
		
		/**
		 * @return Members\Message
		 */
		public function current(): Members\Message {
			return parent::current();
		}
		
		/**
		 * @return Members\Message
		 */
		public function newest(): Members\Message {
			return array_reduce(iterator_to_array($this), function(?Members\Message $a, ?Members\Message $b) {
				return $a ? ($a?->getTimestamp() > $b?->getTimestamp() ? $a : $b) : $b;
			});
		}
		
		/**
		 * @return Members\Message
		 */
		public function oldest(): Members\Message {
			return array_reduce(iterator_to_array($this), function(?Members\Message $a, ?Members\Message $b) {
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
			return array_map(fn(Members\Message $item): array => $item->toArray(), iterator_to_array($this));
		}
		
		/**
		 * @return void
		 */
		public function markRead(): void {
			Database::Action("UPDATE `member_messages` SET `read` = TRUE WHERE `initiated_by` != :member_id AND JSON_CONTAINS(:json_array, `id`)", array(
				'member_id'  => $this->getMember()?->getId(),
				'json_array' => json_encode(array_column($this->getArrayCopy(), 'id'))
			));
		}
		
		/**
		 * @return string
		 */
		public function renderAll(): string {
			return implode(PHP_EOL, array_map(fn(Members\Message $message): ?string => $message->renderHTML(), iterator_to_array($this)));
		}
		
		/**
		 * @return Members\Message[]
		 */
		public function toArray(): array {
			return iterator_to_array($this);
		}
	}