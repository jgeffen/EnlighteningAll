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
	
	class Options implements Iterator {
		private array $options;
		
		/**
		 * @param null|string $type
		 *
		 * @return $this
		 */
		public static function Init(?string $type): self {
			$instace          = new self();
			$instace->options = match ($type) {
				// Page => Redirect
				'non_member_pages' => array(
					'forgot-password'     => TRUE,
					'login'               => TRUE,
					'register'            => TRUE,
					'resend-verification' => TRUE,
					'reset-password'      => TRUE,
					'verify-email'        => TRUE
				),
				default            => array()
			};
			
			return $instace;
		}
		
		/**
		 * @param null|string $key
		 *
		 * @return mixed
		 */
		public function getValue(?string $key): mixed {
			return $this->options[$key] ?? NULL;
		}
		
		/**
		 * @return array
		 */
		public function getKeys(): array {
			return array_keys($this->options);
		}
		
		/**
		 * @param null|string $key
		 *
		 * @return bool
		 */
		public function hasKey(?string $key): bool {
			return array_key_exists($key, $this->options);
		}
		
		/**
		 * Return the current element
		 *
		 * @return mixed
		 */
		public function current(): mixed {
			return current($this->options);
		}
		
		/**
		 * Move forward to next element
		 *
		 * @return void
		 */
		public function next(): void {
			next($this->options);
		}
		
		/**
		 * Return the key of the current element
		 *
		 * @return null|int|string
		 */
		public function key(): null|int|string {
			return key($this->options);
		}
		
		/**
		 * Checks if current position is valid
		 *
		 * @return bool
		 */
		public function valid(): bool {
			return $this->key() !== NULL;
		}
		
		/**
		 * Rewind the Iterator to the first element
		 *
		 * @return void
		 */
		public function rewind(): void {
			reset($this->options);
		}
	}