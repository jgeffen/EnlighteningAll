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
	
	namespace Items;
	
	use Database;
	use DateTime;
	use Items\Abstracts\Member;
	use Items\Collections;
	use Items\Collections\Posts;
	use Items\Enums\Options;
	use Items\Enums\Tables;
	use Items\Enums\Types;
	use Membership;
	use PDO;
	use PDOStatement;
	
	class Contest extends Abstracts\PageType {
		use Traits\Image;
		
		private Collections\Posts $posts;
		private Collections\Posts $winners;
		
		protected Tables\Secrets $table = Tables\Secrets::CONTESTS;
		
		protected string $date_start;
		protected string $date_end;
		protected int    $number_of_winners;
		
		/**
		 * @param null|int|string $identifier
		 *
		 * @return null|$this
		 */
		public static function Init(null|int|string $identifier): ?static {
			return match (gettype($identifier)) {
				'integer' => Database::Action("SELECT * FROM `contests` WHERE `id` = :identifier", array(
					'identifier' => $identifier
				))->fetchObject(static::class),
				'string'  => Database::Action("SELECT * FROM `contests` WHERE `page_url` = :identifier", array(
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
		 * @param null|PDOStatement $statement
		 *
		 * @return static[]
		 */
		public static function FetchAll(?PDOStatement $statement = NULL): array {
			$statement ??= Database::Action("SELECT * FROM `contests` ORDER BY CURDATE() BETWEEN `date_start` AND `date_end` DESC, `date_start` DESC, `date_end`");
			return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
		}
		
		/**
		 * @param null|Member $member
		 *
		 * @return Posts
		 */
		public function getPosts(Abstracts\Member $member = NULL): Collections\Posts {
			return $this->posts ??= new Collections\Posts(Database::Action("SELECT * FROM `member_posts` JOIN `member_post_type_social` ON `member_post_id` = `id` WHERE `id` IN (SELECT `member_post_id` FROM `member_contests` WHERE `contest_id` = :contest_id) AND `visibility` = :visibility AND `type` = :type AND (`approved` = :approved OR :approved IS FALSE) AND NOT JSON_CONTAINS(:blocked_ids, `member_id`) ORDER BY `timestamp` DESC", array(
				'contest_id'  => $this->getId(),
				'type'        => Types\Post::SOCIAL->getValue(),
				'visibility'  => Options\Visibility::MEMBERS->getValue(),
				'approved'    => Membership::FetchSettings()->getValue('post_approval_required'),
				'blocked_ids' => json_encode($member?->getBlockedIds() ?? array())
			)), Types\Post::SOCIAL);
		}
		
		/**
		 * @return Posts
		 */
		public function getWinners(): Collections\Posts {
			return $this->winners ??= new Collections\Posts(Database::Action("SELECT * FROM `member_posts` JOIN `member_post_type_social` ON `member_post_id` = `id` WHERE `id` IN (SELECT `member_post_id` FROM `member_contests` WHERE `contest_id` = :contest_id AND `winner` IS TRUE) AND `type` = :type AND (`approved` = :approved OR :approved IS FALSE) AND `visibility` = :visibility ORDER BY `timestamp` DESC", array(
				'contest_id' => $this->getId(),
				'type'       => Types\Post::SOCIAL->getValue(),
				'visibility' => Options\Visibility::MEMBERS->getValue(),
				'approved'   => Membership::FetchSettings()->getValue('post_approval_required')
			)), Types\Post::SOCIAL);
		}
		
		/**
		 * @return DateTime
		 */
		public function getDateStart(): DateTime {
			return date_create($this->date_start);
		}
		
		/**
		 * @return DateTime
		 */
		public function getDateEnd(): DateTime {
			return date_create($this->date_end);
		}
		
		/**
		 * @return int
		 */
		public function getNumberOfWinners(): int {
			return $this->number_of_winners;
		}
		
		/**
		 * @return bool
		 */
		public function isAwaiting(): bool {
			return $this->isPast() && $this->getWinners()->count() < $this->getNumberOfWinners();
		}
		
		/**
		 * @return bool
		 */
		public function isPast(): bool {
			return $this->getDateEnd() < date_create('Midnight');
		}
		
		/**
		 * @return bool
		 */
		public function isUpcoming(): bool {
			return $this->getDateStart() > date_create('Midnight');
		}
		
		/**
		 * @return bool
		 */
		public function isCurrent(): bool {
			return $this->getDateStart() <= date_create('Midnight') && $this->getDateEnd() >= date_create('Midnight');
		}
		
		/**
		 * @return string
		 */
		public function getLink(): string {
			return sprintf("/members/contests/%s", $this->getPageUrl());
		}
	}