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
	
	namespace Items\Members;
	
	use Database;
	use Helpers;
	use Items\Defaults;
	use Items\Enums\Sizes;
	use Items\Interfaces;
	use Items\Member;
	use Items\Traits;
	use Membership;
	use PDO;
	use PDOStatement;
	use Template;
	
	class Ticket implements Interfaces\Item {
		use Traits\Item;
		
		private ?Member $member;
		private ?Ticket $member_ticket;
		private array   $member_tickets;
		
		protected ?int   $member_ticket_id;
		protected int    $member_id;
		protected string $content;
		protected bool   $read;
		protected string $initiated_by;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `member_tickets` WHERE `id` = :id", array(
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
			
			return Template::Render('members/tickets/message-pane/item.twig', array(
				'alt'       => $member->getAlt('avatar'),
				'avatar'    => $member->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Defaults::AVATAR_XS,
				'direction' => $this->getInitiatedBy() == 'member' ? 'to' : 'from',
				'text'      => $this->getContent(),
				'ticket'    => $this->getMemberTicket()?->toArray() ?? $this->toArray(),
				'updated'   => $this->getTimestamp()->format('F j, Y, g:i a')
			));
		}
		
		/**
		 * @return Ticket[]
		 */
		public function getThread(): array {
			return $this->member_tickets ?? Ticket::FetchAll(Database::Action("SELECT * FROM `member_tickets` WHERE :member_ticket_id IN (`id`, `member_ticket_id`) ORDER BY `timestamp`", array(
				'member_ticket_id' => $this->getId()
			)));
		}
		
		/**
		 * @return null|\Items\Members\Ticket
		 */
		public function getMemberTicket(): ?Ticket {
			return $this->member_ticket ?? Ticket::Init($this->getMemberTicketId());
		}
		
		/**
		 * @return null|int
		 */
		public function getMemberTicketId(): ?int {
			return $this->member_ticket_id;
		}
		
		/**
		 * @return null|\Items\Member
		 */
		public function getMember(): ?Member {
			return $this->member ??= Member::Init($this->getMemberId());
		}
		
		/**
		 * @return int
		 */
		public function getMemberId(): int {
			return $this->member_id;
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
		 * @param bool $admin
		 *
		 * @return bool
		 */
		public function isRead(bool $admin = FALSE): bool {
			$initiated_by = $admin ? 'admin' : 'member';
			
			return $this->read || $this->getInitiatedBy() == $initiated_by;
		}
		
		/**
		 * @param bool $admin
		 *
		 * @return void
		 */
		public function markRead(bool $admin = FALSE): void {
			$initiated_by = !$admin ? 'admin' : 'member';
			
			Database::Action("UPDATE `member_tickets` SET `read` = TRUE WHERE `initiated_by` = :initiated_by AND :member_ticket_id IN (`id`, `member_ticket_id`)", array(
				'initiated_by'     => $initiated_by,
				'member_ticket_id' => $this->getId()
			));
		}
		
		/**
		 * @return string "member" | "admin"
		 */
		public function getInitiatedBy(): string {
			return $this->initiated_by;
		}
		
		/**
		 * @return string
		 */
		public function getLink(): string {
			return sprintf("/members/tickets/%s", $this->getMemberTicketId() ?? $this->getId());
		}
	}