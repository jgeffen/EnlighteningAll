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
	use Items\Members\Types;
	use PDO;
	use PDOStatement;
	
	class Friends extends ArrayIterator {
		/**
		 * @param PDOStatement $query
		 */
		public function __construct(PDOStatement $query) {
			parent::__construct($query->fetchAll(PDO::FETCH_CLASS, Types\Friend::class) ?: array());
		}
		
		/**
		 * @return Types\Friend
		 */
		public function current(): Types\Friend {
			return parent::current();
		}
		
		/**
		 * @return bool
		 */
		public function empty(): bool {
			return $this->count() === 0;
		}
		
		/**
		 * @return Types\Friend[]
		 */
		public function toArray(): array {
			return iterator_to_array($this);
		}
	}