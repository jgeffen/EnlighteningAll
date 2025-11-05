<?php
	/*
	Copyright (c) 2021, 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	namespace Items\Abstracts;
	
	use Database;
	use Helpers;
	use Items;
	use Items\Abstracts;
	use Items\Collections;
	use Items\Enums\Types;
	use Items\Enums\Options;
	use Items\Interfaces;
	use Items\Traits;
	
	abstract class Post implements Interfaces\Post {
		use Traits\Item;
		
		protected Collections\Comments $comments;
		protected Collections\Likes    $likes;
		protected Collections\Reports  $reports;
		protected Collections\Settings $settings;
		protected ?Abstracts\Member    $member;
		
		protected string  $type;
		protected string  $visibility;
		protected int     $member_id;
		protected string  $heading;
		protected string  $content;
		protected string  $posted_by;
		protected ?string $filename;
		protected bool    $approved;
		protected int     $position;
		
		/**
		 * @return Collections\Comments
		 */
		public function comments(): Collections\Comments {
			return $this->comments ??= new Collections\Comments(Database::Action("SELECT * FROM `member_post_comments` WHERE `member_post_id` = :post_id", array(
				'post_id' => $this->getId()
			)));
		}
		
		/**
		 * @return Collections\Likes
		 */
		public function likes(): Collections\Likes {
			return $this->likes ??= new Collections\Likes(Database::Action("SELECT * FROM `member_post_likes` WHERE `member_post_id` = :post_id", array(
				'post_id' => $this->getId()
			)));
		}
		
		/**
		 * @return Collections\Reports
		 */
		public function reports(): Collections\Reports {
			return $this->reports ??= new Collections\Reports(Database::Action("SELECT * FROM `member_post_reports` WHERE `member_post_id` = :post_id", array(
				'post_id' => $this->getId()
			)));
		}
		
		/**
		 * @return Collections\Settings
		 */
		public function settings(): Collections\Settings {
			return $this->settings ??= new Collections\Settings(Database::Action("SELECT * FROM `member_settings`"));
		}
		
		/**
		 * @return string
		 */
		public function getTitle(): string {
			return $this->getHeading();
		}
		
		/**
		 * @return string
		 */
		public function getDescription(): string {
			return Helpers::Truncate($this->getContent(), 180);
		}
		
		/**
		 * @param null|int $length
		 *
		 * @return string
		 */
		public function getHeading(?int $length = NULL): string {
			return is_null($length) ? $this->heading : Helpers::Truncate($this->heading, $length);
		}
		
		/**
		 * @return string
		 */
		public function getAlt(): string {
			return htmlspecialchars($this->getHeading(), ENT_COMPAT);
		}
		
		/**
		 * @return string
		 */
		public function getContent(): string {
			return strip_tags($this->content, array('p', 'br', 'strong', 'em', 'u'));
		}
		
		/**
		 * @return string
		 */
		public function getPostedBy(): string {
			return $this->posted_by;
		}
		
		/**
		 * @return null|Types\Post
		 */
		public function getType(): ?Types\Post {
			return Types\Post::lookup($this->type);
		}
		
		/**
		 * @return null|Options\Visibility
		 */
		public function getVisibility(): ?Options\Visibility {
			return Options\Visibility::lookup($this->visibility);
		}
		
		/**
		 * @return int
		 */
		public function getMemberId(): int {
			return $this->member_id;
		}
		
		/**
		 * @return Abstracts\Member
		 */
		public function getMember(): Abstracts\Member {
			return $this->member ??= Items\Member::Init($this->getMemberId());
		}
		
		/**
		 * @param null|Abstracts\Member $member
		 *
		 * @return $this
		 */
		protected function setMember(?Abstracts\Member $member): self {
			!is_null($member) && $this->member = $member;
			return $this;
		}
		
		/**
		 * @return null|string
		 */
		public function getFilename(): ?string {
			return $this->filename;
		}
		
		/**
		 * @param null|string $default
		 *
		 * @return null|string
		 */
		public function getImageSource(?string $default = NULL): ?string {
			$filepath = sprintf("%s/files/members/%d/%s", dirname(__DIR__, 4), $this->getMemberId(), $this->getFilename());
			
			return Helpers::WebRelativeFile($filepath) ?? $default;
		}
		
		/**
		 * @param null|string $default
		 *
		 * @return null|string
		 */
		public function getImage(?string $default = NULL): ?string {
			$filepath = sprintf("%s/files/members/%d/featured/%s", dirname(__DIR__, 4), $this->getMemberId(), $this->getFilename());
			
			return Helpers::WebRelativeFile($filepath) ?? $default;
		}
		
		/**
		 * @param null|string $default
		 *
		 * @return null|string
		 */
		public function getThumb(?string $default = NULL): ?string {
			$filepath = sprintf("%s/files/members/%d/thumbs/%s", dirname(__DIR__, 4), $this->getMemberId(), $this->getFilename());
			
			return Helpers::WebRelativeFile($filepath) ?? $default;
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
				? !$this->settings()->getValue('post_approval_required') || $this->approved
				: $this->approved;
		}
		
		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
		
		/**
		 * @return string
		 */
		public function getHash(): string {
			return md5($this->getId());
		}
		
		/**
		 * @return string
		 */
		public function getLink(): string {
			return sprintf("/members/profile/%s/%s", strtolower($this->getMember()->getUsername()), $this->getHash());
		}
	}