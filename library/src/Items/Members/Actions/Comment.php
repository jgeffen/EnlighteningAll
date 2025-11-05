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
	use Items\Members\Posts\Comments;
	use JetBrains\PhpStorm\Pure;
	use Membership;
	use PDOException;
	use UnhandledMatchError;
	
	class Comment implements Interfaces\Action {
		private ?Posts\Comment   $comment;
		private Membership       $member;
		private ?Abstracts\Post  $post;
		private Requests\Comment $action;
		
		private string $content;
		private string $type;
		
		/**
		 * @param null|Posts\Comment  $comment
		 * @param Membership          $member
		 * @param null|Abstracts\Post $post
		 */
		public function __construct(?Posts\Comment $comment, Membership $member, ?Abstracts\Post $post = NULL) {
			$this->comment = $comment;
			$this->member  = $member;
			$this->post    = $post;
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
		 * @param Requests\Comment $action
		 *
		 * @return $this
		 */
		public function setAction(Requests\Comment $action): self {
			$this->action = $action;
			return $this;
		}
		
		/**
		 * @param null|Posts\Comment $comment
		 *
		 * @return $this
		 */
		public function setComment(?Posts\Comment $comment): self {
			$this->comment = $comment;
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
		 * @param string $content
		 *
		 * @return $this
		 */
		public function setContent(string $content): self {
			$this->content = $content;
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
		 * @return bool
		 *
		 * @throws Exception|Error|PDOException|UnhandledMatchError
		 */
		public function execute(): bool {
			return match ($this->getAction()) {
				Requests\Comment::ADD    => $this->add(),
				Requests\Comment::REMOVE => $this->remove(),
				Requests\Comment::REPORT => $this->report()
			};
		}
		
		/**
		 * @return bool
		 *
		 * @throws PDOException
		 */
		private function add(): bool {
			return Database::Action("INSERT INTO `member_post_comments` SET `member_post_id` = :member_post_id, `member_id` = :member_id, `content` = :content, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'member_post_id' => $this->getPost()?->getId(),
				'member_id'      => $this->getMember()->getId(),
				'content'        => $this->getContent(),
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
			return Database::Action("DELETE FROM `member_post_comments` WHERE `id` = :id AND `member_id` = :member_id", array(
				'id'        => $this->getComment()?->getId(),
				'member_id' => $this->getMember()->getId()
			))->rowCount();
		}
		
		/**
		 * @return bool
		 *
		 * @throws Exception|PDOException
		 */
		private function report(): bool {
			if(is_null($this->getComment())) throw new Exception('No comment set.');
			
			$report = Comments\Report::Init(
				id                     : NULL,
				member_post_comment_id : $this->getComment()->getId(),
				member_id              : $this->getMember()->getId()
			);
			
			if(!is_null($report)) throw new Exception(sprintf("Your report has already been made and it is currently: %s", $report->getStatus()?->getValue()));
			
			return Database::Action("INSERT INTO `member_post_comment_reports` SET `status` = :status, `type` = :type, `member_post_comment_id` = :member_post_comment_id, `member_id` = :member_id, `dataset` = :dataset,`message` = :message, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'status'                 => Statuses\Report::PENDING->getValue(),
				'type'                   => $this->getType(),
				'member_post_comment_id' => $this->getComment()->getId(),
				'member_id'              => $this->getMember()->getId(),
				'dataset'                => $this->getComment()->toJson(),
				'message'                => $this->getMessage(),
				'user_agent'             => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'             => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			))->rowCount();
		}
		
		/**
		 * @return null|Posts\Comment
		 */
		private function getComment(): ?Posts\Comment {
			return $this->comment;
		}

		/**
		 * @return null|Posts\Comment
		 */
		private function getMessage(): string {
			return $this->message;
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
			return $this->getComment()?->getPost() ?? Members\Post::Init($this->post?->getId());
		}
		
		/**
		 * @return Requests\Comment
		 */
		private function getAction(): Requests\Comment {
			return $this->action;
		}
		
		/**
		 * @return string
		 */
		private function getContent(): string {
			return $this->content;
		}
		
		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}
	}