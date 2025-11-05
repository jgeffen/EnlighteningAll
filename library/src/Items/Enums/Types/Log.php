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
	
	namespace Items\Enums\Types;
	
	use JetBrains\PhpStorm\Pure;
	
	enum Log: string {
		case ACCESS     = 'ACCESS';
		case APPROVE    = 'APPROVE';
		case BAN        = 'BAN';
		case CANCEL     = 'CANCEL';
		case CAPTURE    = 'CAPTURE';
		case COMMENT    = 'COMMENT';
		case COMPENSATE = 'COMPENSATE';
		case CREATE     = 'CREATE';
		case DEACTIVATE = 'DEACTIVATE';
		case DELETE     = 'DELETE';
		case ERROR      = 'ERROR';
		case LIKE       = 'LIKE';
		case LOGIN      = 'LOGIN';
		case LOGOUT     = 'LOGOUT';
		case MESSAGE    = 'MESSAGE';
		case PURCHASE   = 'PURCHASE';
		case REGISTER   = 'REGISTER';
		case REJECT     = 'REJECT';
		case RENEW      = 'RENEW';
		case REPORT     = 'REPORT';
		case SUBSCRIBE  = 'SUBSCRIBE';
		case UNBAN      = 'UNBAN';
		case UNLIKE     = 'UNLIKE';
		case UPDATE     = 'UPDATE';
		case VERIFY     = 'VERIFY';
		case VOID       = 'VOID';
		
		/**
		 * @param string $value
		 *
		 * @return null|$this
		 */
		public static function lookup(string $value): ?self {
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
		 * @param self ...$haystack
		 *
		 * @return bool
		 */
		#[Pure] public function is(self ...$haystack): bool {
			return in_array($this, $haystack);
		}
	}