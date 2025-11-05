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
	
	namespace Items\Members\Rooms;
	
	use Database;
	use Items;
	use Items\Interfaces;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Review implements Interfaces\Item {
		use Traits\Item;
		
		private Items\Room   $room;
		private Items\Member $member;
		
		private int    $room_id;
		private int    $member_id;
		private string $content;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|static
		 */
		public static function Init(?int $id): ?static {
			return Database::Action("SELECT * FROM `member_room_reviews` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(static::class) ?: NULL;
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
		 * @return int
		 */
		public function getRoomId(): int {
			return $this->room_id;
		}
		
		/**
		 * @return Items\Room
		 */
		public function getRoom(): Items\Room {
			return $this->room ??= Items\Room::Init($this->getRoomId());
		}
		
		/**
		 * @return int
		 */
		public function getMemberId(): int {
			return $this->member_id;
		}
		
		/**
		 * @return Items\Member
		 */
		public function getMember(): Items\Member {
			return $this->member ??= Items\Member::Init($this->getMemberId());
		}
		
		/**
		 * @return string
		 */
		public function getContent(): string {
			return strip_tags($this->content, array('p', 'br', 'strong', 'em', 'u'));
		}
	}