<?php
	/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	namespace Items\Members\Posts\Types;
	
	use Database;
	use DateTime;
	use Exception;
	use Items;
	use Items\Abstracts;
	use Items\Members;
	
	class Social extends Abstracts\Post {
		private ?Members\Contest $member_contest;
		
		private int    $member_post_id;
		private ?int   $member_contest_id;
		private string $date_start;
		private string $date_end;
		
		/**
		 * @param null|int              $id
		 * @param null|Abstracts\Member $member
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id, ?Abstracts\Member $member = NULL): ?self {
			$instance = Database::Action("SELECT * FROM `member_post_type_social` JOIN `member_posts` ON `id` = `member_post_id` WHERE `type` = :type AND `member_post_id` = :id AND (`member_id` = :member_id OR ISNULL(:member_id))", array(
				'type'      => self::class,
				'id'        => $id,
				'member_id' => $member?->getId()
			))->fetchObject(self::class) ?: NULL;
			
			return $instance?->setMember($member);
		}
		
		/**
		 * @param null|string           $hash
		 * @param null|Abstracts\Member $member
		 * @param bool                  $throwable
		 *
		 * @return null|$this
		 *
		 * @throws Exception
		 */
		public static function FromHash(?string $hash, ?Abstracts\Member $member, bool $throwable = TRUE): ?self {
			$instance = Database::Action("SELECT * FROM `member_post_type_social` JOIN `member_posts` ON `id` = `member_post_id` WHERE `type` = :type AND MD5(`member_post_id`) = :hash AND `member_id` = :member_id", array(
				'type'      => self::class,
				'hash'      => $hash,
				'member_id' => $member?->getId()
			))->fetchObject(self::class) ?: NULL;
			
			if($throwable && is_null($instance)) throw new Exception('Post not found.');
			
			return $instance?->setMember($member);
		}
		
		/**
		 * @return int
		 */
		public function getMemberPostId(): int {
			return $this->member_post_id;
		}
		
		/**
		 * @return null|int
		 */
		public function getMemberContestId(): ?int {
			return $this->member_contest_id;
		}
		
		/**
		 * @return null|Members\Contest
		 */
		public function getMemberContest(): ?Members\Contest {
			return $this->member_contest ??= Members\Contest::Init($this->getMemberContestId());
		}
		
		/**
		 * @return DateTime
		 */
		public function getStartDate(): DateTime {
			return date_create($this->date_start);
		}
		
		/**
		 * @return DateTime
		 */
		public function getEndDate(): DateTime {
			return date_create($this->date_end);
		}
		
		/**
		 * @return string
		 */
		public function getDate(): string {
			return $this->getStartDate()->format('Y-m-d') == $this->getEndDate()->format('Y-m-d')
				? $this->getStartDate()->format('M d')
				: sprintf("%s - %s", $this->getStartDate()->format('M d'), $this->getEndDate()->format('M d Y'));
		}
		
		/**
		 * @param int $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 * @param int $depth Set the maximum depth. Must be greater than zero.
		 *
		 * @return string
		 */
		public function getDateJson(int $flags = JSON_ERROR_NONE, int $depth = 512): string {
			$array = array($this->date_start, $this->date_end);
			
			return match ($flags) {
				JSON_HEX_APOS => htmlspecialchars(json_encode($array, JSON_ERROR_NONE, $depth), ENT_QUOTES),
				JSON_HEX_QUOT => htmlspecialchars(json_encode($array, JSON_ERROR_NONE, $depth), ENT_COMPAT),
				default       => json_encode($array, $flags, $depth),
			};
		}
	}