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
	
	namespace Items\Members;
	
	use Database;
	use Helpers;
	use Items\Defaults;
	use Items\Enums\Sizes;
	use Members\Messages;
	use Items\Interfaces;
	use Items\Traits;
	use Membership;
	use PDO;
	use PDOStatement;
	use Template;
	
	class Message implements Interfaces\Item {
		use Traits\Item;
		
		private ?Messages\Contact $contact;
		
		protected int    $member_1;
		protected int    $member_2;
		protected string $content;
		protected bool   $read;
		protected int    $initiated_by;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `member_messages` WHERE `id` = :id", array(
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
		 * @return string
		 */
		public function renderHTML(): string {
			$member = new Membership();
			
			return Template::Render('members/messages/message-pane/item.twig', array(
				'alt'       => $this->getContact()->getAlt('avatar'),
				'avatar'    => $this->getContact()->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Defaults::AVATAR_XS,
				'direction' => $this->getInitiatedBy() == $member->getId() ? 'to' : 'from',
				'text'      => $this->getContent(),
				'updated'   => $this->getTimestamp()->format('F j, Y, g:i a')
			));
		}
		
		/**
		 * @return null|Messages\Contact
		 */
		public function getContact(): ?Messages\Contact {
			return $this->contact ??= Messages\Contact::Init($this->getInitiatedBy());
		}
		
		/**
		 * @return int
		 */
		public function getMember1(): int {
			return $this->member_1;
		}
		
		/**
		 * @return int
		 */
		public function getMember2(): int {
			return $this->member_2;
		}
		
		/**
		 * @param null|int $length
		 *
		 * @return null|string
		 */
		public function getContent(?int $length = NULL): ?string {
			return strip_tags(is_null($length) ? $this->content : Helpers::Truncate($this->content, $length), array('br', 'strong', 'em', 'u'));
		}
		
		/**
		 * @return bool
		 */
		public function isRead(): bool {
			return $this->read;
		}
		
		/**
		 * @return int
		 */
		public function getInitiatedBy(): int {
			return $this->initiated_by;
		}
	}