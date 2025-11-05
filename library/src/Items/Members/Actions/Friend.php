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
	
	namespace Items\Members\Actions;
	
	use Database;
	use Error;
	use Items;
	use Items\Abstracts;
	use Items\Enums\Statuses;
	use Items\Enums\Requests;
	use Items\Enums\Types;
	use Items\Interfaces;
	use Items\Members\Friends;
	use JetBrains\PhpStorm\Pure;
	use Membership;
	use PDOException;
	use UnhandledMatchError;
	
	class Friend implements Interfaces\Action {
		private ?Friends\Request $request;
		private Membership       $member_1;
		private ?Items\Member    $member_2;
		private Requests\Friend  $action;
		
		/**
		 * @param null|Friends\Request  $request
		 * @param Membership            $member_1
		 * @param null|Abstracts\Member $member_2
		 */
		public function __construct(?Friends\Request $request, Membership $member_1, ?Abstracts\Member $member_2) {
			$this->request  = $request;
			$this->member_1 = $member_1;
			$this->member_2 = $member_2;
		}
		
		/**
		 * @param Membership   $member_1
		 * @param Items\Member $member_2
		 *
		 * @return $this
		 */
		#[Pure] public static function Init(Membership $member_1, Abstracts\Member $member_2): self {
			return new self(NULL, $member_1, $member_2);
		}
		
		/**
		 * @param Requests\Friend $action
		 *
		 * @return $this
		 */
		public function setAction(Requests\Friend $action): self {
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
				Requests\Friend::APPROVE => $this->approve() && $this->bind(),
				Requests\Friend::CANCEL  => $this->cancel(),
				Requests\Friend::DECLINE => $this->decline(),
				Requests\Friend::REMOVE  => $this->remove() && $this->unbind(),
				Requests\Friend::SEND    => $this->send()
			};
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function approve(): bool {
			return Database::Action("UPDATE `member_friend_requests` SET `status` = :status WHERE `member_1` = :member_1 AND `member_2` = :member_2 AND `initiated_by` = :initiated_by", array(
				'status'       => Statuses\Friend::APPROVED->getValue(),
				'member_1'     => min($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'member_2'     => max($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'initiated_by' => $this->getMember2()?->getId()
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function cancel(): bool {
			return Database::Action("UPDATE `member_friend_requests` SET `status` = :status WHERE `member_1` = :member_1 AND `member_2` = :member_2 AND `initiated_by` = :initiated_by", array(
				'status'       => Statuses\Friend::CANCELLED->getValue(),
				'member_1'     => min($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'member_2'     => max($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'initiated_by' => $this->getMember1()->getId()
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function decline(): bool {
			return Database::Action("UPDATE `member_friend_requests` SET `status` = :status WHERE `member_1` = :member_1 AND `member_2` = :member_2 AND `initiated_by` = :initiated_by", array(
				'status'       => Statuses\Friend::DECLINED->getValue(),
				'member_1'     => min($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'member_2'     => max($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'initiated_by' => $this->getMember2()?->getId()
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function remove(): bool {
			return Database::Action("UPDATE `member_friend_requests` SET `status` = :status WHERE `member_1` = :member_1 AND `member_2` = :member_2", array(
				'status'   => Statuses\Friend::NONE->getValue(),
				'member_1' => min($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'member_2' => max($this->getMember1()->getId(), $this->getMember2()?->getId())
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function send(): bool {
			$this->getMember1()->block($this->getMember2())->setAction(Requests\Block::REMOVE)->execute();
			$this->getMember1()->notify($this->getMember2())->setRequest(Requests\Notification::ADD)->setType(Types\Notification::REQUEST)->execute();
			
			return Database::Action("INSERT INTO `member_friend_requests` SET `status` = :status, `member_1` = :member_1, `member_2` = :member_2, `initiated_by` = :initiated_by ON DUPLICATE KEY UPDATE `status` = :status, `initiated_by` = :initiated_by", array(
				'status'       => Statuses\Friend::PENDING->getValue(),
				'member_1'     => min($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'member_2'     => max($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'initiated_by' => $this->getMember1()->getId()
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function bind(): bool {
			return Database::Action("INSERT INTO `member_friends` SET `member_1` = :member_1, `member_2` = :member_2", array(
				'member_1' => min($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'member_2' => max($this->getMember1()->getId(), $this->getMember2()?->getId())
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function unbind(): bool {
			return Database::Action("DELETE FROM `member_friends` WHERE `member_1` = :member_1 AND `member_2` = :member_2", array(
				'member_1' => min($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'member_2' => max($this->getMember1()->getId(), $this->getMember2()?->getId())
			))->rowCount();
		}
		
		/**
		 * @return null|Friends\Request
		 */
		private function getRequest(): ?Friends\Request {
			return $this->request;
		}
		
		/**
		 * @return Membership
		 */
		private function getMember1(): Membership {
			return $this->member_1;
		}
		
		/**
		 * @return Items\Member
		 */
		private function getMember2(): Items\Member {
			return Items\Member::Init($this->member_2->getId());
		}
		
		/**
		 * @return Requests\Friend
		 */
		private function getAction(): Requests\Friend {
			return $this->action;
		}
	}