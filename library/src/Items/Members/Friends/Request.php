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
	
	namespace Items\Members\Friends;
	
	use Database;
	use Items;
	use Items\Enums\Statuses;
	use Items\Interfaces;
	use Items\Traits;
	
	class Request implements Interfaces\Item {
		use Traits\Item;
		
		private string $status;
		private int    $member_1;
		private int    $member_2;
		private int    $initiated_by;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `member_friend_requests` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @return Statuses\Friend
		 */
		public function getStatus(): Statuses\Friend {
			return Statuses\Friend::lookup($this->status);
		}
		
		/**
		 * @return int
		 */
		public function getMember1(): int {
			return $this->member_1;
		}
		
		/**
		 * @return int
		 */
		public function getMember2(): int {
			return $this->member_2;
		}
		
		/**
		 * @return int
		 */
		public function getInitiatedBy(): int {
			return $this->initiated_by;
		}
	}