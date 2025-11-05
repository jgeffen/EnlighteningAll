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
	
	namespace Members\Messages;
	
	use Database;
	use DateTime;
	use Items\Abstracts;
	use Items\Defaults;
	use Items\Enums\Sizes;
	use Membership;
	use Template;
	
	class Contact extends Abstracts\Member {
		protected string $recent_timestamp;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `members` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param Membership $member
		 *
		 * @return string
		 */
		public function contactListItem(Membership $member): string {
			$message = $member->messages($this)->newest();
			$sender  = $message->getInitiatedBy() == $member->getId();
			$read    = $sender || $message->isRead();
			$badge   = match (TRUE) {
				$message->getTimestamp() >= date_create('Today')     => 'Today',
				$message->getTimestamp() >= date_create('Last Week') => $message->getTimestamp()->format('D'),
				$message->getTimestamp()->format('Y') >= date('Y')   => $message->getTimestamp()->format('n/j'),
				default                                              => $message->getTimestamp()->format('n/j/y')
			};
			
			return Template::Render('members/messages/contact-list/item.twig', array(
				'badge'   => $badge,
				'preview' => $message->getContent(250),
				'read'    => $read,
				'contact' => array(
					'alt'      => $this->getAlt('avatar'),
					'avatar'   => $this->getAvatar()?->getImage(Sizes\Avatar::XS) ?? Defaults::AVATAR_XS,
					'id'       => $this->getId(),
					'username' => $this->getUsername()
				)
			));
		}
		
		/**
		 * @return DateTime
		 */
		public function getRecentTimestamp(): DateTime {
			return date_create($this->recent_timestamp);
		}
	}