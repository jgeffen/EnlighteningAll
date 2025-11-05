<?php
	/*
	Copyright (c) 2023 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	namespace Items\Forms;
	
	use Database;
	use Items\Form;
	
	class FeedbackSurvey extends Form {
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `forms` WHERE `type` = 'feedback-survey' AND `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @return null|string
		 */
		public function getCaptcha(): ?string {
			return $this->captcha;
		}
		
		/**
		 * @return null|string
		 */
		public function getComments(): ?string {
			return $this->comments;
		}
		
		/**
		 * @return null|string
		 */
		public function getContactComments(): ?string {
			return $this->contact_comments;
		}
		
		/**
		 * @return null|string
		 */
		public function getContactEmail(): ?string {
			return $this->contact_email;
		}
		
		/**
		 * @return null|string
		 */
		public function getContactName(): ?string {
			return $this->contact_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getContactPhone(): ?string {
			return $this->contact_phone;
		}
		
		/**
		 * @return int
		 */
		public function getRatingBarService(): int {
			return $this->rating_bar_service;
		}
		
		/**
		 * @return null|string
		 */
		public function getRatingBarServiceComments(): ?string {
			return $this->rating_bar_service_comments;
		}
		
		/**
		 * @return int
		 */
		public function getRatingCheckInProcess(): int {
			return $this->rating_check_in_process;
		}
		
		/**
		 * @return null|string
		 */
		public function getRatingCheckInProcessComments(): ?string {
			return $this->rating_check_in_process_comments;
		}
		
		/**
		 * @return int
		 */
		public function getRatingCleanRoomArrival(): int {
			return $this->rating_clean_room_arrival;
		}
		
		/**
		 * @return null|string
		 */
		public function getRatingCleanRoomArrivalComments(): ?string {
			return $this->rating_clean_room_arrival_comments;
		}
		
		/**
		 * @return int
		 */
		public function getRatingFood(): int {
			return $this->rating_food;
		}
		
		/**
		 * @return null|string
		 */
		public function getRatingFoodComments(): ?string {
			return $this->rating_food_comments;
		}
		
		/**
		 * @return int
		 */
		public function getRatingLikelyToReturn(): int {
			return $this->rating_likely_to_return;
		}
		
		/**
		 * @return null|string
		 */
		public function getRatingLikelyToReturnComments(): ?string {
			return $this->rating_likely_to_return_comments;
		}
		
		/**
		 * @return int
		 */
		public function getRatingRoomAmentities(): int {
			return $this->rating_room_amentities;
		}
		
		/**
		 * @return null|string
		 */
		public function getRatingRoomAmentitiesComments(): ?string {
			return $this->rating_room_amentities_comments;
		}
		
		/**
		 * @return int
		 */
		public function getRatingStaffMembers(): int {
			return $this->rating_staff_members;
		}
		
		/**
		 * @return null|string
		 */
		public function getRatingStaffMembersComments(): ?string {
			return $this->rating_staff_members_comments;
		}
		
		/**
		 * @return float
		 */
		public function getAverageRating(): float {
			$ratings = array(
				$this->rating_bar_service,
				$this->rating_check_in_process,
				$this->rating_clean_room_arrival,
				$this->rating_food,
				$this->rating_likely_to_return,
				$this->rating_room_amentities,
				$this->rating_staff_members
			);
			
			return round(array_sum($ratings) / count($ratings), 2);
		}
	}