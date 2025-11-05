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
	use Items\Interfaces;
	use Items\Members;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Contest implements Interfaces\Item {
		use Traits\Item;
		
		private ?Items\Member  $member;
		private ?Members\Post  $post;
		private ?Items\Contest $contest;
		
		protected int  $member_id;
		protected int  $member_post_id;
		protected int  $contest_id;
		protected bool $winner;
		
		/**
		 * @param null|int $identifier
		 *
		 * @return null|$this
		 */
		public static function Init(null|int $identifier): ?static {
			return match (gettype($identifier)) {
				'integer' => Database::Action("SELECT * FROM `member_contests` WHERE `id` = :identifier", array(
					'identifier' => $identifier
				))->fetchObject(static::class),
				default   => NULL
			} ?: NULL;
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
		 * @return int
		 */
		public function getMemberPostId(): int {
			return $this->member_post_id;
		}
		
		/**
		 * @return null|Post
		 */
		public function getPost(): ?Post {
			return $this->post ??= Members\Post::Init($this->getMemberPostId());
		}
		
		/**
		 * @return int
		 */
		public function getContestId(): int {
			return $this->contest_id;
		}
		
		/**
		 * @return null|Items\Contest
		 */
		public function getContest(): ?Items\Contest {
			return $this->contest ??= Items\Contest::Init($this->getContestId());
		}
		
		/**
		 * @return bool
		 */
		public function isWinner(): bool {
			return $this->winner;
		}
	}