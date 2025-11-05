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
	use Items\Members;
	use PDOStatement;
	
	class Reservations extends ArrayIterator {
		/**
		 * @param PDOStatement $statement
		 */
		public function __construct(PDOStatement $statement) {
			parent::__construct(Members\Reservation::FetchAll($statement));
		}
		
		/**
		 * @return Members\Reservation
		 */
		public function current(): Members\Reservation {
			return parent::current();
		}
		
		/**
		 * @return bool
		 */
		public function empty(): bool {
			return $this->count() === 0;
		}
		
		/**
		 * @param Items\Event $event
		 *
		 * @return null|Members\Reservation
		 */
		public function lookup(Items\Event $event): ?Members\Reservation {
			$offset = array_search($event->getId(), array_column($this->getArrayCopy(), 'event_id'));
			
			return is_int($offset) && $this->offsetExists($offset) ? $this->offsetGet($offset) : NULL;
		}
		
		/**
		 * @return Members\Reservation[]
		 */
		public function toArray(): array {
			return iterator_to_array($this);
		}
	}