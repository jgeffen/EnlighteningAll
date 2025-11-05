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
	use Items;
	use Items\Abstracts;
	use Items\Abstracts\Member;
	use Items\Enums\Requests;
	use Items\Enums\Types;
	use Items\Interfaces;
	use Items\Members;
	use JetBrains\PhpStorm\Pure;
	use Membership;
	use PDOException;
	use UnhandledMatchError;
	
	class Notification implements Interfaces\Action {
		private ?Members\Notification $notification;
		private Membership            $member_1;
		private ?Abstracts\Member     $member_2;
		private Requests\Notification $request;
		private ?Abstracts\Post       $post = NULL;
		private ?Types\Notification   $type = NULL;
		
		/**
		 * @param null|Members\Notification $notification
		 * @param Membership                $member_1
		 * @param null|Abstracts\Member     $member_2
		 */
		public function __construct(?Members\Notification $notification, Membership $member_1, ?Abstracts\Member $member_2 = NULL) {
			$this->notification = $notification;
			$this->member_1     = $member_1;
			$this->member_2     = $member_2;
		}
		
		/**
		 * @param Membership                $member_1
		 * @param null|Member               $member_2
		 * @param null|Members\Notification $notification
		 *
		 * @return $this
		 */
		#[Pure] public static function Init(Membership $member_1, ?Abstracts\Member $member_2 = NULL, ?Members\Notification $notification = NULL): self {
			return new self($notification, $member_1, $member_2);
		}
		
		/**
		 * @param Requests\Notification $request
		 *
		 * @return $this
		 */
		public function setRequest(Requests\Notification $request): self {
			$this->request = $request;
			return $this;
		}
		
		/**
		 * @param null|Abstracts\Post $post
		 *
		 * @return Notification
		 */
		public function setPost(?Abstracts\Post $post): Notification {
			$this->post = $post;
			return $this;
		}
		
		/**
		 * @param Types\Notification $type
		 *
		 * @return Notification
		 */
		public function setType(Types\Notification $type): Notification {
			$this->type = $type;
			return $this;
		}
		
		/**
		 * @return bool
		 *
		 * @throws Error|PDOException|UnhandledMatchError
		 */
		public function execute(): bool {
			return match ($this->getAction()) {
				Requests\Notification::ADD    => $this->add(),
				Requests\Notification::REMOVE => $this->remove(),
				Requests\Notification::SEEN   => $this->seen()
			};
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function add(): bool {
			if($this->getMember1()->getId() == $this->getMember2()->getId()) return FALSE;
			
			return Database::Action("INSERT INTO `member_notifications` SET `type` = :type, `member_1` = :member_1, `member_2` = :member_2, `member_post_id` = :member_post_id, `initiated_by` = :initiated_by, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'type'           => $this->getType()?->getValue(),
				'member_1'       => min($this->getMember1()->getId(), $this->getMember2()->getId()),
				'member_2'       => max($this->getMember1()->getId(), $this->getMember2()->getId()),
				'member_post_id' => $this->getPost()?->getId(),
				'initiated_by'   => $this->getMember1()->getId(),
				'user_agent'     => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'     => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function remove(): bool {
			if($this->getNotification()) {
				return Database::Action("DELETE FROM `member_notifications` WHERE `id` = :id AND :member_id IN (`member_1`, `member_2`)", array(
					'id'        => $this->getNotification()?->getId(),
					'member_id' => $this->getMember1()->getId()
				))->rowCount();
			} else {
				return match ($this->getType()) {
					Types\Notification::ACCOUNT => FALSE,
					Types\Notification::COMMENT => Database::Action("DELETE FROM `member_notifications` WHERE `type` = :type AND `member_1` = :member_1 AND `member_2` = :member_2 AND `member_post_id` = :member_post_id AND `initiated_by` = :initiated_by", array(
						'type'           => $this->getType()?->getValue(),
						'member_1'       => min($this->getMember1()->getId(), $this->getMember2()->getId()),
						'member_2'       => max($this->getMember1()->getId(), $this->getMember2()->getId()),
						'member_post_id' => $this->getPost()?->getId(),
						'initiated_by'   => $this->getMember1()->getId()
					))->rowCount(),
					Types\Notification::LIKE    => Database::Action("DELETE FROM `member_notifications` WHERE `type` = :type AND `member_1` = :member_1 AND `member_2` = :member_2 AND `member_post_id` = :member_post_id AND `initiated_by` = :initiated_by", array(
						'type'           => $this->getType()?->getValue(),
						'member_1'       => min($this->getMember1()->getId(), $this->getMember2()->getId()),
						'member_2'       => max($this->getMember1()->getId(), $this->getMember2()->getId()),
						'member_post_id' => $this->getPost()?->getId(),
						'initiated_by'   => $this->getMember1()->getId()
					))->rowCount(),
					Types\Notification::MESSAGE => FALSE,
					Types\Notification::REQUEST => Database::Action("DELETE FROM `member_notifications` WHERE `type` = :type AND `member_1` = :member_1 AND `member_2` = :member_2 AND `initiated_by` = :initiated_by", array(
						'type'         => $this->getType()?->getValue(),
						'member_1'     => min($this->getMember1()->getId(), $this->getMember2()->getId()),
						'member_2'     => max($this->getMember1()->getId(), $this->getMember2()->getId()),
						'initiated_by' => $this->getMember1()->getId()
					))->rowCount(),
					default                     => FALSE
				};
			}
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function seen(): bool {
			return Database::Action("UPDATE `member_notifications` SET `seen` = :seen WHERE `id` = :id AND :member_id IN (`member_1`, `member_2`)", array(
				'seen'      => TRUE,
				'id'        => $this->getNotification()?->getId(),
				'member_id' => $this->getMember1()->getId()
			))->rowCount();
		}
		
		/**
		 * @return null|Members\Notification
		 */
		private function getNotification(): ?Members\Notification {
			return $this->notification;
		}
		
		/**
		 * @return Membership
		 */
		private function getMember1(): Membership {
			return $this->member_1;
		}
		
		/**
		 * @return null|Abstracts\Member
		 */
		private function getMember2(): ?Abstracts\Member {
			return $this->member_2;
		}
		
		/**
		 * @return Requests\Notification
		 */
		private function getAction(): Requests\Notification {
			return $this->request;
		}
		
		/**
		 * @return null|Abstracts\Post
		 */
		private function getPost(): ?Abstracts\Post {
			return $this->post;
		}
		
		/**
		 * @return null|Types\Notification
		 */
		private function getType(): ?Types\Notification {
			return $this->type;
		}
	}