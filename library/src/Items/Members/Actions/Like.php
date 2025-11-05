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
	
	namespace Items\Members\Actions;
	
	use Database;
	use Error;
	use Items\Abstracts;
	use Items\Enums\Requests;
	use Items\Interfaces;
	use JetBrains\PhpStorm\Pure;
	use Membership;
	use PDOException;
	use UnhandledMatchError;
	
	class Like implements Interfaces\Action {
		private Membership     $member;
		private Abstracts\Post $post;
		private Requests\Like  $action;
		
		/**
		 * @param Membership     $member
		 * @param Abstracts\Post $post
		 */
		public function __construct(Membership $member, Abstracts\Post $post) {
			$this->member = $member;
			$this->post   = $post;
		}
		
		/**
		 * @param Membership     $member
		 * @param Abstracts\Post $post
		 *
		 * @return $this
		 */
		#[Pure] public static function Init(Membership $member, Abstracts\Post $post): self {
			return new self($member, $post);
		}
		
		/**
		 * @param Requests\Like $action
		 *
		 * @return $this
		 */
		public function setAction(Requests\Like $action): self {
			$this->action = $action;
			return $this;
		}
		
		/**
		 * @return bool
		 *
		 * @throws Error|PDOException|UnhandledMatchError
		 */
		public function execute(): bool {
			return match ($this->getAction()) {
				Requests\Like::ADD    => $this->add(),
				Requests\Like::REMOVE => $this->remove()
			};
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function add(): bool {
			return Database::Action("INSERT INTO `member_post_likes` SET `member_post_id` = :member_post_id, `member_id` = :member_id", array(
				'member_post_id' => $this->getPost()->getId(),
				'member_id'      => $this->getMember()->getId()
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function remove(): bool {
			return Database::Action("DELETE FROM `member_post_likes` WHERE `member_post_id` = :member_post_id AND `member_id` = :member_id", array(
				'member_post_id' => $this->getPost()->getId(),
				'member_id'      => $this->getMember()->getId()
			))->rowCount();
		}
		
		/**
		 * @return Membership
		 */
		private function getMember(): Membership {
			return $this->member;
		}
		
		/**
		 * @return Abstracts\Post
		 */
		private function getPost(): Abstracts\Post {
			return $this->post;
		}
		
		/**
		 * @return Requests\Like
		 */
		private function getAction(): Requests\Like {
			return $this->action;
		}
	}