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
	use Items\Abstracts;
	use Items\Enums\Requests;
	use Items\Enums\Statuses;
	use Items\Interfaces;
	use Items\Members;
	use Items\Members\Posts;
	use JetBrains\PhpStorm\Pure;
	use Membership;
	use PDOException;
	use UnhandledMatchError;
	
	class Report implements Interfaces\Action {
		private ?Posts\Report   $report;
		private Membership      $member;
		private ?Abstracts\Post $post;
		private Requests\Report $action;
		
		private string $type;
		private string $message;
		
		/**
		 * @param null|Posts\Report   $report
		 * @param Membership          $member
		 * @param null|Abstracts\Post $post
		 */
		public function __construct(?Posts\Report $report, Membership $member, ?Abstracts\Post $post = NULL) {
			$this->report = $report;
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
			return new self(NULL, $member, $post);
		}
		
		/**
		 * @param Requests\Report $action
		 *
		 * @return $this
		 */
		public function setAction(Requests\Report $action): self {
			$this->action = $action;
			return $this;
		}
		
		/**
		 * @param string $type
		 *
		 * @return $this
		 */
		public function setType(string $type): self {
			$this->type = $type;
			return $this;
		}

		/**
		 * @param string $message
		 *
		 * @return $this
		 */
		public function setMessage(string $message): self {
			$this->message = $message;
			return $this;
		}
		
		/**
		 * @return bool
		 *
		 * @throws Error|Exception|PDOException|UnhandledMatchError
		 */
		public function execute(): bool {
			return match ($this->getAction()) {
				Requests\Report::ADD    => $this->add(),
				Requests\Report::REMOVE => $this->remove()
			};
		}
		
		/**
		 * @return bool
		 *
		 * @throws Exception|PDOException
		 */
		private function add(): bool {
			$report = Posts\Report::Init(
				id             : NULL,
				member_post_id : $this->getPost()->getId(),
				member_id      : $this->getMember()->getId()
			);
			
			if(!is_null($report)) throw new Exception(sprintf("Your report has already been made and it is currently: %s", $report->getStatus()?->getValue()));
			
			return Database::Action("INSERT INTO `member_post_reports` SET `status` = :status, `type` = :type, `member_post_id` = :member_post_id, `member_id` = :member_id, `dataset` = :dataset, `message` = :message, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'status'         => Statuses\Report::PENDING->getValue(),
				'type'           => $this->getType(),
				'member_post_id' => $this->getPost()?->getId(),
				'member_id'      => $this->getMember()->getId(),
				'dataset'        => $this->getPost()?->toJson() ?? '[]',
				'message'        => $this->getMessage(),
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
			return Database::Action("DELETE FROM `member_post_reports` WHERE `id` = :id AND `member_id` = :member_id", array(
				'id'        => $this->getReport()?->getId(),
				'member_id' => $this->getMember()->getId()
			))->rowCount();
		}
		
		/**
		 * @return null|Posts\Report
		 */
		private function getReport(): ?Posts\Report {
			return $this->report;
		}
		
		/**
		 * @return Membership
		 */
		private function getMember(): Membership {
			return $this->member;
		}
		
		/**
		 * @return null|Members\Post
		 */
		private function getPost(): ?Members\Post {
			return $this->getReport()?->getPost() ?? Members\Post::Init($this->post?->getId());
		}
		
		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @return string
		 */
		public function getMessage(): string {
			return $this->message;
		}
		
		/**
		 * @return Requests\Report
		 */
		private function getAction(): Requests\Report {
			return $this->action;
		}
	}