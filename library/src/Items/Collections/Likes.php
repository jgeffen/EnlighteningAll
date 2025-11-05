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
	
	use Items;
	use Items\Members\Posts;
	use Items\Members\Posts\Types;
	use ArrayIterator;
	use Membership;
	use PDOStatement;
	
	class Likes extends ArrayIterator {
		/**
		 * @param PDOStatement $statement
		 */
		public function __construct(PDOStatement $statement) {
			parent::__construct(Posts\Like::FetchAll($statement));
		}
		
		/**
		 * @return Posts\Like
		 */
		public function current(): Posts\Like {
			return parent::current();
		}
		
		/**
		 * @return bool
		 */
		public function empty(): bool {
			return $this->count() === 0;
		}
		
		/**
		 * @param Items\Member|Types\Social $instance
		 *
		 * @return null|Posts\Like
		 */
		public function lookup(mixed $instance): ?Posts\Like {
			$offset = match (get_class($instance)) {
				Membership::class   => array_search($instance->getId(), array_column($this->getArrayCopy(), 'member_id')),
				Items\Member::class => array_search($instance->getId(), array_column($this->getArrayCopy(), 'member_id')),
				Types\Social::class => array_search($instance->getId(), array_column($this->getArrayCopy(), 'member_post_id'))
			};
			
			return is_int($offset) && $this->offsetExists($offset) ? $this->offsetGet($offset) : NULL;
		}
		
		/**
		 * @param string $property
		 *
		 * @return self
		 */
		public function sort(string $property): self {
			static::uasort(function(Posts\Like $a, Posts\Like $b) use ($property) {
				return match ($property) {
					'timestamp' => $a->getTimestamp()->format('U') - $b->getTimestamp()->format('U'),
					'username'  => strcasecmp($a->getMember()?->getUsername(), $b->getMember()?->getUsername()),
					default     => $a->getTimestamp()->diff($b->getTimestamp())->s
				};
			});
			
			return $this;
		}
		
		/**
		 * @param string $sort
		 *
		 * @return string[]
		 */
		public function usernames(string $sort = 'timestamp'): array {
			return array_values(array_map(fn(Posts\Like $like) => $like->getMember()?->getUsername(), $this->sort($sort)->toArray()));
		}
		
		/**
		 * @return Posts\Like[]
		 */
		public function toArray(): array {
			return iterator_to_array($this);
		}
	}