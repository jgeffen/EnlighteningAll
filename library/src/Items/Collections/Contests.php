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
	
	use Items;
	use Items\Members;
	use ArrayIterator;
	use PDOStatement;
	
	class Contests extends ArrayIterator {
		/**
		 * @param PDOStatement $statement
		 */
		public function __construct(PDOStatement $statement) {
			parent::__construct(Members\Contest::FetchAll($statement));
		}
		
		/**
		 * @return Members\Contest
		 */
		public function current(): Members\Contest {
			return parent::current();
		}
		
		/**
		 * @return bool
		 */
		public function empty(): bool {
			return $this->count() === 0;
		}
		
		/**
		 * @param Items\Contest $instance
		 *
		 * @return bool
		 */
		public function contains(Items\Contest $instance): bool {
			$offset = array_search($instance->getId(), array_column($this->getArrayCopy(), 'contest_id'));
			
			return is_int($offset) && $this->offsetExists($offset);
		}
		
		/**
		 * @param Items\Contest $instance
		 *
		 * @return null|Members\Contest
		 */
		public function lookup(Items\Contest $instance): ?Members\Contest {
			$offset = array_search($instance->getId(), array_column($this->getArrayCopy(), 'contest_id'));
			
			return is_int($offset) && $this->offsetExists($offset) ? $this->offsetGet($offset) : NULL;
		}
		
		/**
		 * @return Items\Contest[]
		 */
		public function toArray(): array {
			return iterator_to_array($this);
		}
	}