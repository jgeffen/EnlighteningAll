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
	use Items;
	use Items\Enums\Tables;
	use Items\Interfaces;
	use Items\Members\Rooms;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Room implements Interfaces\Item {
		use Traits\Item;
		
		protected Tables\Members $table = Tables\Members::ROOMS;
		
		private Items\Member  $member;
		private Items\Room    $room;
		private ?Rooms\Review $review;
		
		protected int  $member_id;
		protected int  $room_id;
		protected ?int $review_id;
		protected bool $favorite;
		protected bool $notification;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `member_rooms` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|static
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
		 * @return Items\Member
		 */
		public function getMember(): Items\Member {
			return $this->member ??= Items\Member::Init($this->getMemberId());
		}
		
		/**
		 * @return Items\Room
		 */
		public function getRoom(): Items\Room {
			return $this->room ??= Items\Room::Init($this->getRoomId());
		}
		
		/**
		 * @return null|Rooms\Review
		 */
		public function getReview(): ?Rooms\Review {
			return $this->review ??= Rooms\Review::Init($this->getReviewId());
		}
		
		/**
		 * @return int
		 */
		public function getMemberId(): int {
			return $this->member_id;
		}
		
		/**
		 * @return int
		 */
		public function getRoomId(): int {
			return $this->room_id;
		}
		
		/**
		 * @return null|int
		 */
		public function getReviewId(): ?int {
			return $this->review_id;
		}
		
		/**
		 * @return bool
		 */
		public function isFavorite(): bool {
			return $this->favorite;
		}
		
		/**
		 * @return bool
		 */
		public function isNotification(): bool {
			return $this->notification;
		}
	}