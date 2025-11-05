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
	use Exception;
	use Items;
	use Items\Abstracts;
	use Items\Enums\Requests;
	use Items\Enums\Statuses;
	use Items\Interfaces;
	use Items\Members;
	use JetBrains\PhpStorm\Pure;
	use Members\Messages;
	use Membership;
	use PDOException;
	use UnhandledMatchError;
	
	class Message implements Interfaces\Action {
		private ?Members\Message  $message;
		private Membership        $member_1;
		private ?Abstracts\Member $member_2;
		private Requests\Message  $action;
		
		private string $content;
		
		/**
		 * @param null|Members\Message  $message
		 * @param Membership            $member_1
		 * @param null|Abstracts\Member $member_2
		 */
		public function __construct(?Members\Message $message, Membership $member_1, ?Abstracts\Member $member_2) {
			$this->message  = $message;
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
			return new self(NULL, $member_1, $member_2);
		}
		
		/**
		 * @param Requests\Message $action
		 *
		 * @return $this
		 */
		public function setAction(Requests\Message $action): self {
			$this->action = $action;
			return $this;
		}
		
		/**
		 * @param string $content
		 *
		 * @return $this
		 */
		public function setContent(string $content): self {
			$this->content = $content;
			return $this;
		}
		
		/**
		 * @return bool
		 *
		 * @throws Exception|Error|PDOException|UnhandledMatchError
		 */
		public function execute(): bool {
			return match ($this->getAction()) {
				Requests\Message::REMOVE => $this->remove(),
				Requests\Message::REPORT => $this->report(),
				Requests\Message::SEND   => $this->send()
			};
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function remove(): bool {
			return Database::Action("DELETE FROM `member_messages` WHERE `id` = :id AND :member_id IN (`member_1`, `member_2`)", array(
				'id'        => $this->getMessage()?->getId(),
				'member_id' => $this->getMember1()->getId()
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws Exception|PDOException
		 */
		private function report(): bool {
			$report = Messages\Report::Init(
				id                 : NULL,
				member_id          : $this->getMember1()->getId(),
				member_reported_id : $this->getMember2()?->getId()
			);
			
			if(!is_null($report)) throw new Exception(sprintf("Your report has already been made and it is currently: %s", $report->getStatus()?->getValue()));
			
			return Database::Action("INSERT INTO `member_message_reports` SET `status` = :status, `member_id` = :member_id, `member_reported_id` = :member_reported_id, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'status'             => Statuses\Report::PENDING->getValue(),
				'member_id'          => $this->getMember1()->getId(),
				'member_reported_id' => $this->getMember2()?->getId(),
				'user_agent'         => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'         => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function send(): bool {
			return Database::Action("INSERT IGNORE INTO `member_messages` SET `member_1` = :member_1, `member_2` = :member_2, `content` = :content, `initiated_by` = :initiated_by, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'member_1'     => min($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'member_2'     => max($this->getMember1()->getId(), $this->getMember2()?->getId()),
				'content'      => $this->getContent(),
				'initiated_by' => $this->getMember1()->getId(),
				'user_agent'   => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'   => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			))->rowCount();
		}
		
		/**
		 * @return null|Members\Message
		 */
		private function getMessage(): ?Members\Message {
			return $this->message;
		}
		
		/**
		 * @return Membership
		 */
		private function getMember1(): Membership {
			return $this->member_1;
		}
		
		/**
		 * @return null|Items\Member
		 */
		private function getMember2(): ?Items\Member {
			return Items\Member::Init($this->member_2?->getId());
		}
		
		/**
		 * @return Requests\Message
		 */
		private function getAction(): Requests\Message {
			return $this->action;
		}
		
		/**
		 * @return string
		 */
		private function getContent(): string {
			return $this->content;
		}
	}