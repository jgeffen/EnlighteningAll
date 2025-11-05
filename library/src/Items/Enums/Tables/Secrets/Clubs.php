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
	
	namespace Items\Enums\Tables\Secrets;
	
	use Items\Interfaces;
	use JetBrains\PhpStorm\Pure;
	
	enum Clubs: string implements Interfaces\TableEnum {
		case STATES = 'club_states';
		
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