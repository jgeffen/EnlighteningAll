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
	
	/** @noinspection PhpUnused */
	
	namespace Items\Collections;
	
	use Items\Members;
	use ArrayIterator;
	use PDOStatement;
	
	class Settings extends ArrayIterator {
		/**
		 * @param PDOStatement $statement
		 */
		public function __construct(PDOStatement $statement) {
			parent::__construct(Members\Setting::FetchAll($statement));
		}
		
		/**
		 * @return Members\Setting
		 */
		public function current(): Members\Setting {
			return parent::current();
		}
		
		/**
		 * @return bool
		 */
		public function empty(): bool {
			return $this->count() === 0;
		}
		
		/**
		 * @param string $name
		 *
		 * @return null|bool|array|string
		 */
		public function getValue(string $name): null|bool|array|string {
			return $this->lookup($name)?->getValue();
		}
		
		/**
		 * @return array
		 */
		public function getArrayCopy(): array {
			return array_map(fn(Members\Setting $item): array => $item->toArray(), iterator_to_array($this));
		}
		
		/**
		 * @param string $name
		 *
		 * @return null|Members\Setting
		 */
		public function lookup(string $name): ?Members\Setting {
			return $this->offsetGet(array_search($name, array_column($this->getArrayCopy(), 'name')));
		}
		
		/**
		 * @return Members\Setting[]
		 */
		public function toArray(): array {
			return iterator_to_array($this);
		}
	}