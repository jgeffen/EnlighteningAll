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
	
	namespace Items\Enums\Sizes;
	
	use JetBrains\PhpStorm\Pure;
	
	enum Avatar: int {
		case XS = 100;
		case SM = 200;
		case MD = 450;
		case LG = 600;
		case XL = 900;
		
		/**
		 * @param int $value
		 *
		 * @return null|$this
		 */
		public static function lookup(int $value): ?self {
			return self::tryFrom($value);
		}
		
		/**
		 * @return int
		 */
		public function getValue(): int {
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
		
		/**
		 * @return int[] Label => Value
		 */
		public static function options(): array {
			return array_reduce(self::cases(), function(array $carry, self $item) {
				$carry[$item->getLabel()] = $item->getValue();
				return $carry;
			}, array());
		}
	}