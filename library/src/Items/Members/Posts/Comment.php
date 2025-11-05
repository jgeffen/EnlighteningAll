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
	
	namespace Items\Members\Posts;
	
	use Database;
	use Items;
	use Items\Enums\Sizes;
	use Items\Interfaces\Item;
	use Items\Members;
	use Items\Traits;
	use Membership;
	use PDO;
	use PDOStatement;
	use Template;
	
	class Comment implements Item {
		use Traits\Item;
		
		private ?Members\Post $post;
		private ?Items\Member $member;
		
		private int     $member_post_id;
		private int     $member_id;
		private ?string $content;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `member_post_comments` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|static
		 */
		public static function Fetch(PDOStatement $statement): ?static {
			return $statement->fetchObject(static::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return static[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
		}
		
		/**
		 * @return null|string
		 */
		public function renderHTML(): ?string {
			$membership = new Membership();
			
			return $this->getMember() ? Template::Render('members/posts/comment.twig', array(
				'comment' => array(
					'id'      => $this->getId(),
					'content' => $this->getContent(),
					'date'    => $this->getTimestamp()->format('Y-m-d H:i:s')
				),
				'member'  => array(
					'avatar'     => $this->getMember()->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Items\Defaults::AVATAR_XS,
					'descriptor' => 'member avatar',
					'link'       => $this->getMember()->getLink(),
					'username'   => $this->getMember()->getUsername(),
					'self'       => $this->getMemberId() == $membership->getId()
				)
			)) : NULL;
		}
		
		/**
		 * @return int
		 */
		public function getMemberId(): int {
			return $this->member_id;
		}
		
		/**
		 * @return null|Items\Member
		 */
		public function getMember(): ?Items\Member {
			return $this->member ??= Items\Member::Init($this->getMemberId());
		}
		
		/**
		 * @return int
		 */
		public function getPostId(): int {
			return $this->member_post_id;
		}
		
		/**
		 * @return null|Members\Post
		 */
		public function getPost(): ?Members\Post {
			return $this->post ??= Members\Post::Init($this->getPostId());
		}
		
		/**
		 * @return null|string
		 */
		public function getContent(): ?string {
			return strip_tags($this->content, array('br', 'strong', 'em', 'u'));
		}
	}