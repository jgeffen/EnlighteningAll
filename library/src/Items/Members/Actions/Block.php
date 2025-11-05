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
	use Items\Enums\Requests;
	use Items\Interfaces;
	use JetBrains\PhpStorm\Pure;
	use Membership;
	use PDOException;
	use UnhandledMatchError;
	
	class Block implements Interfaces\Action {
		private Membership       $member_1;
		private Abstracts\Member $member_2;
		private Requests\Block   $action;
		
		/**
		 * @param Membership       $member_1
		 * @param Abstracts\Member $member_2
		 */
		public function __construct(Membership $member_1, Abstracts\Member $member_2) {
			$this->member_1 = $member_1;
			$this->member_2 = $member_2;
		}
		
		/**
		 * @param Membership       $member_1
		 * @param Abstracts\Member $member_2
		 *
		 * @return $this
		 */
		#[Pure] public static function Init(Membership $member_1, Abstracts\Member $member_2): self {
			return new self($member_1, $member_2);
		}
		
		/**
		 * @param Requests\Block $action
		 *
		 * @return $this
		 */
		public function setAction(Requests\Block $action): self {
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
				Requests\Block::ADD    => $this->add(),
				Requests\Block::REMOVE => $this->remove()
			};
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function add(): bool {
			$this->getMember1()->friend($this->getMember2())->setAction(Requests\Friend::REMOVE)->execute();
			
			return Database::Action("INSERT IGNORE INTO `member_blocks` SET `member_1` = :member_1, `member_2` = :member_2, `initiated_by` = :initiated_by", array(
				'member_1'     => min($this->getMember1()->getId(), $this->getMember2()->getId()),
				'member_2'     => max($this->getMember1()->getId(), $this->getMember2()->getId()),
				'initiated_by' => $this->getMember1()->getId()
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function remove(): bool {
			return Database::Action("DELETE FROM `member_blocks` WHERE `member_1` = :member_1 AND `member_2` = :member_2 AND `initiated_by` = :initiated_by", array(
				'member_1'     => min($this->getMember1()->getId(), $this->getMember2()->getId()),
				'member_2'     => max($this->getMember1()->getId(), $this->getMember2()->getId()),
				'initiated_by' => $this->getMember1()->getId()
			))->rowCount();
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
		 * @return Requests\Block
		 */
		private function getAction(): Requests\Block {
			return $this->action;
		}
	}