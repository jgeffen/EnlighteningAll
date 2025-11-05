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
	
	namespace Items\Enums\Options;
	
	use JetBrains\PhpStorm\Pure;
	
	enum Visibility: string {
		case MEMBERS = 'All Members';
		case FRIENDS = 'Friends Only';
		
		/**
		 * @param null|string $value
		 *
		 * @return null|$this
		 */
		public static function lookup(?string $value): ?self {
			return match ($value) {
				self::FRIENDS->getValue() => self::FRIENDS,
				self::MEMBERS->getValue() => self::MEMBERS,
				default                   => NULL
			};
		}
		
		/**
		 * @return string
		 */
		public function getValue(): string {
			return $this->name;
		}
		
		/**
		 * @return string
		 */
		public function getLabel(): string {
			return $this->value;
		}
		
		/**
		 * @param self ...$haystack
		 *
		 * @return bool
		 */
		#[Pure] public function is(self ...$haystack): bool {
			return in_array($this, $haystack);
		}
		
		/**
		 * @return array
		 */
		public static function options(): array {
			return array_reduce(self::cases(), function(array $carry, self $item) {
				$carry[$item->getValue()] = $item->getLabel();
				return $carry;
			}, array());
		}
	}