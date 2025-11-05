<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	namespace Items;
	
	use Database;
	use PDO;
	use PDOStatement;
	
	class Form implements Interfaces\Item {
		use Traits\Item;
		protected string  $type;
		protected ?string $address;
		protected ?string $captcha;
		protected ?string $career;
		protected ?string $city;
		protected ?string $comments;
		protected ?string $contact_comments;
		protected ?string $contact_email;
		protected ?string $contact_name;
		protected ?string $contact_phone;
		protected ?string $email;
		protected ?string $filename;
		protected ?string $first_name;
		protected ?string $last_name;
		protected ?string $how_many_people;
		protected ?string $name;
		protected ?string $payment_method;
		protected ?string $phone;
		protected ?string $provisions;
		protected int     $rating_bar_service;
		protected ?string $rating_bar_service_comments;
		protected int     $rating_check_in_process;
		protected ?string $rating_check_in_process_comments;
		protected int     $rating_clean_room_arrival;
		protected ?string $rating_clean_room_arrival_comments;
		protected int     $rating_food;
		protected ?string $rating_food_comments;
		protected int     $rating_likely_to_return;
		protected ?string $rating_likely_to_return_comments;
		protected int     $rating_room_amentities;
		protected ?string $rating_room_amentities_comments;
		protected int     $rating_staff_members;
		protected ?string $rating_staff_members_comments;
		protected ?string $reason;
		protected ?string $state;
		protected ?string $status;
		protected ?string $when_renting;
		protected ?string $who_is_renting;
		protected ?string $zip_code;
		protected string  $original;
		protected ?string $yoga;
		protected ?bool    $teacher;
		protected ?string $teacher_roles;
		protected ?string $music;
		protected ?string $core_practices;
		protected ?string $dance_movement;
		protected ?string $community_interests;
		protected ?string $influencer_goals;
		protected ?string $education_business;
		protected ?string $classes_taught;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `forms` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|static
		 */
		public static function Fetch(PDOStatement $statement): ?static {
			return $statement->fetchObject(static::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return static[]
		 */
		public static function FetchAll(PDOStatement $statement): array {
			return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
		}
		
		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}
		
		/**
		 * @return array
		 */
		public function getOriginal(): array {
			return json_decode($this->original, TRUE);
		}
	}