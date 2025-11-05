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
	
	namespace Items\Members;
	
	use Database;
	use Helpers;
	use Items;
	use Items\Abstracts;
	use Items\Collections;
	use Items\Enums\Sizes;
	use Items\Interfaces;
	use Items\Traits;
	
	class Avatar implements Interfaces\Item {
		use Traits\Item;
		
		private Abstracts\Member     $member;
		private Collections\Settings $settings;
		
		private int     $member_id;
		private ?string $filename;
		private bool    $approved;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `member_avatars` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param null|Abstracts\Member $member
		 *
		 * @return null|$this
		 */
		public static function InitFromMember(?Abstracts\Member $member): ?self {
			$instance = Database::Action("SELECT * FROM `member_avatars` WHERE `member_id` = :member_id", array(
				'member_id' => $member->getId()
			))->fetchObject(self::class) ?: NULL;
			
			return $instance?->setMember($member);
		}
		
		/**
		 * @return Collections\Settings
		 */
		public function settings(): Collections\Settings {
			return $this->settings ??= new Collections\Settings(Database::Action("SELECT * FROM `member_settings`"));
		}
		
		/**
		 * @return int
		 */
		public function getMemberId(): int {
			return $this->member_id;
		}
		
		/**
		 * @return null|Abstracts\Member
		 */
		public function getMember(): ?Abstracts\Member {
			return $this->member ??= Items\Member::Init($this->getMemberId());
		}
		
		/**
		 * @return null|string
		 */
		public function getFilename(): ?string {
			return $this->filename;
		}
		
		/**
		 * @return null|string
		 */
		public function getImageSource(): ?string {
			$filepath = sprintf("%s/files/members/%d/avatar/%s", dirname(__DIR__, 4), $this->getMemberId(), $this->getFilename());
			
			return Helpers::WebRelativeFile($filepath);
		}
		
		/**
		 * @param Sizes\Avatar $size
		 * @param bool         $skip_approval
		 *
		 * @return string
		 */
		public function getImage(Sizes\Avatar $size, bool $skip_approval = FALSE): string {
			$filepath = sprintf("%s/files/members/%d/avatar/%d/%s", dirname(__DIR__, 4), $this->getMemberId(), $size->getValue(), $this->getFilename());
			
			return $skip_approval || $this->isApproved()
				? Helpers::WebRelativeFile($filepath, Items\Defaults::AVATAR)
				: match ($size) {
					Sizes\Avatar::XS => Items\Defaults::AVATAR_XS,
					Sizes\Avatar::SM => Items\Defaults::AVATAR_SM,
					Sizes\Avatar::MD => Items\Defaults::AVATAR_MD,
					Sizes\Avatar::LG => Items\Defaults::AVATAR_LG,
					Sizes\Avatar::XL => Items\Defaults::AVATAR_XL
				};
		}
		
		/**
		 * @return bool
		 */
		public function hasImage(): bool {
			return !is_null($this->getImageSource());
		}
		
		/**
		 * @param bool $check_settings
		 *
		 * @return bool
		 */
		public function isApproved(bool $check_settings = TRUE): bool {
			return $check_settings
				? !$this->settings()->getValue('avatar_approval_required') || $this->approved
				: $this->approved;
		}
		
		/**
		 * @param Abstracts\Member $member
		 *
		 * @return $this
		 */
		private function setMember(Abstracts\Member $member): self {
			$this->member = $member;
			return $this;
		}
	}