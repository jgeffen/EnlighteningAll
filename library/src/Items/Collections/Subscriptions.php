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
	
	namespace Items\Collections;
	
	use ArrayIterator;
	use Items;
	use PDOStatement;
	
	class Subscriptions extends ArrayIterator {
		/**
		 * @param PDOStatement $statement
		 */
		public function __construct(PDOStatement $statement) {
			parent::__construct(Items\Subscription::FetchAll($statement));
		}
		
		/**
		 * @return Items\Subscription
		 */
		public function current(): Items\Subscription {
			return parent::current();
		}
		
		/**
		 * @return bool
		 */
		public function empty(): bool {
			return $this->count() === 0;
		}
		
		/**
		 * @return Items\Subscription[]
		 */
		public function toArray(): array {
			return iterator_to_array($this);
		}
	}