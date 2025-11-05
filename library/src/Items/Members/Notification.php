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
	use Items\Abstracts;
	use Items\Enums\Types;
	use Items\Interfaces;
	use Items\Members;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Notification implements Interfaces\Item {
		use Traits\Item;
		
		private ?Items\Member $member;
		private ?Members\Post $post;
		
		protected string $type;
		protected int    $member_1;
		protected ?int   $member_2;
		protected ?int   $member_post_id;
		protected bool   $seen;
		protected ?int   $initiated_by;
		
		/**
		 * @param int $id
		 *
		 * @return null|$this
		 */
		public static function Init(int $id): ?self {
			return Database::Action("SELECT * FROM `member_notifications` WHERE `id` = :id", array(
				'id' => $id
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
		 * @return null|Types\Notification
		 */
		public function getType(): ?Types\Notification {
			return Types\Notification::lookup($this->type);
		}
		
		/**
		 * @return int
		 */
		public function getMember1(): int {
			return $this->member_1;
		}
		
		/**
		 * @return null|int
		 */
		public function getMember2(): ?int {
			return $this->member_2;
		}
		
		/**
		 * @return null|int
		 */
		public function getPostId(): ?int {
			return $this->member_post_id;
		}
		
		/**
		 * @return null|Members\Post
		 */
		public function getPost(): ?Members\Post {
			return $this->post ??= Members\Post::Init($this->getPostId());
		}
		
		/**
		 * @return null|Items\Member
		 */
		public function getMember(): ?Items\Member {
			return $this->member ??= Items\Member::Init($this->getInitiatedBy());
		}
		
		/**
		 * @return bool
		 */
		public function isSeen(): bool {
			return $this->seen;
		}
		
		/**
		 * @param Abstracts\Member $member
		 *
		 * @return bool
		 */
		public function isOwner(Abstracts\Member $member): bool {
			return in_array($member->getId(), array($this->getMember1(), $this->getMember2()))
			       && $member->getId() != $this->getInitiatedBy();
		}
		
		/**
		 * @return null|int
		 */
		public function getInitiatedBy(): ?int {
			return $this->initiated_by;
		}
	}