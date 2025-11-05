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
	
	/** @noinspection PhpUnused */
	
	namespace Items\Members\Posts;
	
	use Database;
	use Items;
	use Items\Enums\Statuses;
	use Items\Enums\Types;
	use Items\Interfaces\Item;
	use Items\Members;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Report implements Item {
		use Traits\Item;
		
		private ?Members\Post $post;
		private ?Items\Member $member;
		
		private string $status;
		private string $type;
		private  $message;
		private int    $member_post_id;
		private int    $member_id;
		private string $dataset;
		
		/**
		 * @param null|int $id
		 * @param null|int $member_post_id
		 * @param null|int $member_id
		 *
		 * @return null|static
		 */
		public static function Init(?int $id, ?int $member_post_id = NULL, ?int $member_id = NULL): ?static {
			return Database::Action("SELECT * FROM `member_post_reports` WHERE `id` = :id OR (:id IS NULL AND `member_post_id` = :member_post_id AND `member_id` = :member_id)", array(
				'id'             => $id,
				'member_post_id' => $member_post_id,
				'member_id'      => $member_id
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
		public function getPostId(): int {
			return $this->member_post_id;
		}
		
		/**
		 * @return string
		 */
		public function getMessage() {
			return $this->message;
		}
		
		/**
		 * @return null|Members\Post
		 */
		public function getPost(): ?Members\Post {
			return $this->post ??= Members\Post::Init($this->getPostId());
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