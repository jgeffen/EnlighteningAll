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
	use Items\Enums\Statuses;
	use Items\Enums\Types;
	use Items\Interfaces\Item;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Report implements Item {
		use Traits\Item;
		
		private ?Items\Member $profile;
		private ?Items\Member $member;
		
		private string $status;
		private string $type;
		private int    $profile_id;
		private int    $member_id;
		private string $dataset;
		
		/**
		 * @param null|int $id
		 * @param null|int $profile_id
		 * @param null|int $member_id
		 *
		 * @return null|static
		 */
		public static function Init(?int $id, ?int $profile_id = NULL, ?int $member_id = NULL): ?static {
			return Database::Action("SELECT * FROM `member_reports` WHERE `id` = :id OR (:id IS NULL AND `profile_id` = :profile_id AND `member_id` = :member_id)", array(
				'id'         => $id,
				'profile_id' => $profile_id,
				'member_id'  => $member_id
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
		 * @return null|Statuses\Report
		 */
		public function getStatus(): ?Statuses\Report {
			return Statuses\Report::lookup($this->status);
		}
		
		/**
		 * @return null|Types\Report
		 */
		public function getType(): ?Types\Report {
			return Types\Report::lookup($this->type);
		}
		
		/**
		 * @return int
		 */
		public function getProfileId(): int {
			return $this->profile_id;
		}
		
		/**
		 * @return null|Items\Member
		 */
		public function getProfile(): ?Items\Member {
			return $this->profile ??= Items\Member::Init($this->getProfileId());
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
			return $this->member ??= Items\Member::Init($this->getMemberId());
		}
		
		/**
		 * @return array
		 */
		public function getDataset(): array {
			return array_filter(json_decode($this->dataset, TRUE) ?? array());
		}
	}