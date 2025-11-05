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
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class QRCode implements Interfaces\Item {
		use Traits\Item;
		
		protected Tables\Members $table = Tables\Members::QRCODES;
		
		private Items\Member $member;
		
		protected string $type;
		protected int    $member_id;
		protected string $hash;
		
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
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}
		
		/**
		 * @return int
		 */
		public function getMemberId(): int {
			return $this->member_id;
		}
		
		/**
		 * @return bool
		 */
		public function isExpired(): bool {
			$difference = date_create()->diff($this->getLastTimestamp());
			$minutes    = $difference->days * 24 * 60;
			$minutes    += $difference->h * 60;
			$minutes    += $difference->i;
			
			return $minutes > 60;
		}
		
		/**
		 * @return string
		 */
		public function getHash(): string {
			return $this->hash;
		}
	}