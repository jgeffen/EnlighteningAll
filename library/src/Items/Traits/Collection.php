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
	
	namespace Items\Traits;
	
	/**
	 * @template TValue
	 */
	trait Collection {
		/**
		 * @var array<int, TValue>
		 */
		public array $items;
		private int  $position = 0;
		
		/**
		 * @return int
		 */
		public function count(): int {
			return count($this->items);
		}
		
		/**
		 * Rewind the Iterator to the first element
		 *
		 * @return void
		 *
		 * @link https://php.net/manual/en/iterator.rewind.php
		 */
		public function rewind(): void {
			$this->position = 0;
		}
		
		/**
		 * Return the current element
		 *
		 * @return TValue
		 *
		 * @link https://php.net/manual/en/iterator.current.php
		 */
		public function current(): mixed {
			return $this->items[$this->position];
		}
		
		/**
		 * Return the key of the current element
		 *
		 * @return int
		 *
		 * @link https://php.net/manual/en/iterator.key.php
		 */
		public function key(): int {
			return $this->position;
		}
		
		/**
		 * Move forward to next element
		 *
		 * @return void
		 *
		 * @link https://php.net/manual/en/iterator.next.php
		 */
		public function next(): void {
			++$this->position;
		}
		
		/**
		 * Checks if current position is valid
		 *
		 * @return bool
		 *
		 * @link https://php.net/manual/en/iterator.valid.php
		 */
		public function valid(): bool {
			return isset($this->items[$this->position]);
		}
	}