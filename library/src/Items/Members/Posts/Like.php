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
	
	use DateTime;
	use Items;
	use Items\Members;
	use PDO;
	use PDOStatement;
	
	class Like {
		private ?Members\Post $post;
		private ?Items\Member $member;
		
		public int     $member_post_id;
		public int     $member_id;
		private string $timestamp;
		
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
		 * @return null|Members\Post
		 */
		public function getPost(): ?Members\Post {
			return $this->post ??= Members\Post::Init($this->getMemberPostId());
		}
		
		/**
		 * @return DateTime
		 */
		public function getTimestamp(): DateTime {
			return date_create($this->timestamp);
		}
		
		/**
		 * @param string $property
		 * @param mixed  $value
		 *
		 * @return void
		 */
		public function __set(string $property, mixed $value): void {
			error_log(sprintf("Unhandled property %s for class %s", $property, get_class($this)));
		}
	}