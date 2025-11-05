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
	use Items\Enums\Requests;
	use Items\Interfaces;
	use Items\Members;
	use JetBrains\PhpStorm\Pure;
	use Membership;
	use PDOException;
	use UnhandledMatchError;
	
	class Polling implements Interfaces\Action {
		private ?Members\Polling $polling;
		private Membership       $member;
		private Requests\Polling $action;
		
		/**
		 * @param null|Members\Polling $polling
		 * @param Membership           $member
		 */
		public function __construct(?Members\Polling $polling, Membership $member) {
			$this->polling = $polling;
			$this->member  = $member;
		}
		
		/**
		 * @param Membership $member
		 *
		 * @return $this
		 */
		#[Pure] public static function Init(Membership $member): self {
			return new self(NULL, $member);
		}
		
		/**
		 * @param Requests\Polling $action
		 *
		 * @return $this
		 */
		public function setAction(Requests\Polling $action): self {
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
				Requests\Polling::DESTROY => $this->destroy(),
				Requests\Polling::UPDATE  => $this->update()
			};
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function destroy(): bool {
			return Database::Action("DELETE FROM `member_polling` WHERE `id` = :id AND `member_id` = :member_id", array(
				'id'        => $this->getPolling()?->getId(),
				'member_id' => $this->getMember()->getId()
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function update(): bool {
			return Database::Action("INSERT INTO `member_polling` SET `member_id` = :member_id, `user_agent` = :user_agent, `ip_address` = :ip_address ON DUPLICATE KEY UPDATE `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'member_id'  => $this->getMember()->getId(),
				'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			))->rowCount();
		}
		
		/**
		 * @return null|Members\Polling
		 */
		public function getPolling(): ?Members\Polling {
			return $this->polling;
		}
		
		/**
		 * @return Membership
		 */
		private function getMember(): Membership {
			return $this->member;
		}
		
		/**
		 * @return Requests\Polling
		 */
		private function getAction(): Requests\Polling {
			return $this->action;
		}
	}