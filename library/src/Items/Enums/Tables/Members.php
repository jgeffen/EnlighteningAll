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
	
	namespace Items\Enums\Tables;
	
	use Items\Interfaces;
	use JetBrains\PhpStorm\Pure;
	
	enum Members: string implements Interfaces\TableEnum {
		case AVATARS       = 'member_avatars';
		case BLOCKS        = 'member_blocks';
		case CHECKINS      = 'member_check_ins';
		case COMMENTS      = 'member_post_comments';
		case FAQS          = 'member_faqs';
		case FREEDRINKS    = 'member_free_drinks';
		case FRIENDS       = 'member_friends';
		case LIKES         = 'member_post_likes';
		case LOGS          = 'member_logs';
		case MESSAGES      = 'member_messages';
		case NOTIFICATIONS = 'member_notifications';
		case POLLING       = 'member_polling';
		case POSTS         = 'member_posts';
		case QRCODES       = 'member_qr_codes';
		case REPORTS       = 'member_post_reports';
		case REQUESTS      = 'member_friend_requests';
		case RESERVATIONS  = 'member_reservations';
		case ROOMS         = 'member_rooms';
		case SETTINGS      = 'member_settings';
		case SUBSCRIPTIONS = 'member_subscriptions';
		case TICKETS       = 'member_tickets';
		case WALLETS       = 'member_wallets';
		
		/**
		 * @param null|string $value
		 *
		 * @return null|$this
		 */
		public static function lookup(?string $value): ?self {
			return self::tryFrom($value);
		}
		
		/**
		 * @return string
		 */
		public function getValue(): string {
			return $this->value;
		}
		
		/**
		 * @return string
		 */
		public function getLabel(): string {
			return $this->name;
		}
		
		/**
		 * @param Interfaces\TableEnum ...$haystack
		 *
		 * @return bool
		 */
		#[Pure] public function is(Interfaces\TableEnum ...$haystack): bool {
			return in_array($this, $haystack);
		}
	}