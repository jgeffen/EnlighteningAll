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
	
	namespace Items;
	
	use Database;
	use DateTime;
	use Debug;
	use GuzzleHttp;
	use Items\Abstracts;
	use Items\Enums\Tables;
	use Items\Members\Rooms;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class Room extends Abstracts\Listed {
		use Traits\Item, Traits\Gallery, Traits\Image;
		
		protected Tables\Secrets $table = Tables\Secrets::ROOMS;
		
		protected int $iqware_id;
		protected int $position;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `rooms` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|self
		 */
		public static function Fetch(PDOStatement $statement): ?self {
			return $statement->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return self[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, self::class);
		}
		
		/**
		 * @param DateTime $start_date
		 * @param DateTime $end_date
		 *
		 * @return array
		 */
		public function getAvailability(DateTime $start_date, DateTime $end_date): array {
			try {
				$client   = new GuzzleHttp\Client();
				$response = $client->get('https://reservations.iqwareinc.com/SecretsHideawayResortAndSpa/9287/en/room/unitInventory', array(
					'headers' => array('User-Agent' => 'Secrets-PHP/1.0', 'Accept' => 'application/json'),
					'query'   => array(
						'webnode'   => '1171-1',
						'startDate' => $start_date->format('Y/n/j'),
						'endDate'   => $end_date->format('Y/n/j'),
						'unitIds'   => $this->getIQwareId()
					)
				));
				
				return array_reduce(GuzzleHttp\Utils::jsonDecode($response->getBody(), TRUE), function($items, $item) {
					$items[] = array(
						'available' => boolval($item['Available']),
						'date'      => date_create($item['InventoryDate']),
						'unit'      => $item['UnitId']
					);
					return $items;
				}, array());
			} catch(GuzzleHttp\Exception\GuzzleException|GuzzleHttp\Exception\InvalidArgumentException $exception) {
				Debug::Exception($exception);
			}
			
			return array();
		}
		
		/**
		 * @return int
		 */
		public function getIQwareId(): int {
			return $this->iqware_id;
		}
		
		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}
		
		/**
		 * Overwrite to default to the first gallery image
		 *
		 * @return null|string
		 */
		public function getSquareImage(): ?string {
			return $this->hasImage() ? $this->getImage('landscape') : $this->getGallery()[0]['featured'] ?? $this->getDefaultImage('landscape');
		}
		
		/**
		 * @return string
		 */
		public function getReviewLink(): string {
			return sprintf("/members/rooms/review/%s", $this->getId());
		}
		
		/**
		 * @return string
		 */
		public function getReviewsLink(): string {
			return sprintf("/members/rooms/reviews/%s", $this->getId());
		}
		
		/**
		 * @return Rooms\Review[]
		 */
		public function getReviews(?Abstracts\Member $member = NULL): array {
			return Rooms\Review::FetchAll(Database::Action("SELECT * FROM `member_room_reviews` WHERE `room_id` = :room_id AND (:friend_ids IS NULL OR (JSON_CONTAINS(:friend_ids, `member_id`) OR `member_id` = :member_id)) ORDER BY `timestamp` DESC", array(
				'room_id'    => $this->getId(),
				'member_id'  => $member?->getId(),
				'friend_ids' => $member?->getFriendIdsJson()
			)));
		}
	}