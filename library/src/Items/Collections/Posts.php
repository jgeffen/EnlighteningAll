<?php
	/*
	Copyright (c) 2021 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	namespace Items\Collections;
	
	use ArrayIterator;
	use Items\Enums\Types;
	use Items\Members\Post;
	use Items\Members\Posts\Types\Social;
	use PDO;
	use PDOStatement;
	
	/**
	 * @template TValue
	 */
	class Posts extends ArrayIterator {
		/**
		 * @param PDOStatement       $query
		 * @param Types\Post<TValue> $class
		 */
		public function __construct(PDOStatement $query, Types\Post $class) {
			parent::__construct($query->fetchAll(PDO::FETCH_CLASS, $class->getValue()) ?: array());
		}
		
		/**
		 * @return null|Social|Post
		 */
		public function current(): null|Social|Post {
			return parent::current();
		}
		
		/**
		 * @return bool
		 */
		public function empty(): bool {
			return $this->count() === 0;
		}
		
		/**
		 * @return Social[]|Post[]
		 */
		public function toArray(): array {
			return iterator_to_array($this);
		}
	}