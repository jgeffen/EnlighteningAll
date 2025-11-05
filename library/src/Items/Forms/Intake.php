<?php
	/*
	Copyright (c) 2023 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	namespace Items\Forms;
	
	use Database;
	use Helpers;
	use Items\Form;
	
	class Intake extends Form {
		private ?string $resume;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `forms` WHERE `type` = 'intake' AND `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * Formats ranked input data into "Label (Rank)" strings
		 *
		 * @param array $submitted_ranks
		 * @param array $options
		 * @return string
		 */
		public static function FormatRankedOptions(array $submitted_ranks, array $options): string {
			$output = array();
			foreach($submitted_ranks as $key => $rank) {
				if(strlen(trim($rank))) {
					$label = $options[$key] ?? ucfirst($key);
					$output[] = sprintf('%s (%s)', $label, $rank);
				}
			}
			return implode(', ', $output);
		}
		
		/**
		 * @param string $input_name
		 * @param array $options
		 *
		 * @return string
		 */
		public static function FormatCheckboxInput(string $input_name, array $options): string {
			$data = $_POST[$input_name] ?? array();
			$formatted = array();
			foreach($data as $key) {
				if(isset($options[$key])) {
					$formatted[] = $options[$key];
				}
			}
			return implode(', ', $formatted);
		}
		
		/**
		 * Formats a checkbox array into label string
		 *
		 * @param array $data
		 * @param array $options
		 * @return string
		 */
		public static function FormatCheckboxInputFromArray(array $data, array $options): string {
			$output = array();
			foreach($data as $key) {
				$key = trim($key);
				if(isset($options[$key])) {
					$output[] = $options[$key];
				}
			}
			return implode(', ', $output);
		}
		
		/**
		 * @param string      $option
		 * @param null|string $key
		 *
		 * @return null|array|string
		 */
		public static function Options(string $option, string $key = null): array|string|null {
			$options = match ($option) {
				'yoga_styles' => array(
					'yin'         => 'Yin Yoga',
					'hot'         => 'Hot Yoga',
					'kundalini'   => 'Kundalini Yoga',
					'vinyasa'     => 'Flow / Vinyasa Yoga',
					'hatha'       => 'Hatha Yoga',
					'power'       => 'Power Yoga',
					'restorative' => 'Restorative Yoga',
					'iyengar'     => 'Iyengar Yoga',
					'ashtanga'    => 'Ashtanga Yoga',
					'other'       => 'Other / Specialty Yoga'
				),
				'teacher_roles' => array(
					'yoga'       => 'Yoga',
					'breathwork' => 'Breathwork',
					'meditation' => 'Meditation',
					'massage'    => 'Massage',
					'fitness'    => 'Fitness / Personal Training',
					'nutrition'  => 'Nutrition / Health Coaching',
					'business'   => 'Business / Entrepreneurship',
					'coaching'   => 'Life Coaching / Mentorship',
					'arts'       => 'Creative Arts Instruction',
					'other'      => 'Other Teaching Role'
				),
				'music_genres' => array(
					'pop'          => 'Top 40 / Pop',
					'country'      => 'Country',
					'edm'          => 'EDM / Electronic',
					'rock_80s'     => '80s Rock',
					'rock_90s'     => '90s Rock',
					'rock_classic' => 'Classic Rock (60s–70s)',
					'hip_hop'      => 'Hip Hop / R&B',
					'jazz'         => 'Jazz / Blues',
					'acoustic'     => 'Acoustic / Folk',
					'latin'        => 'Latin / World Music'
				),
				'core_practices' => array(
					'breathwork'        => 'Breathwork',
					'meditation'        => 'Meditation',
					'sound_healing'     => 'Sound Healing / Singing Bowls',
					'ai_beginner'       => 'AI Beginner Classes',
					'ai_advanced'       => 'AI Advanced Groups',
					'songwriting'       => 'Songwriting / Music Creation',
					'creative_writing'  => 'Creative Writing / Journaling',
					'public_speaking'   => 'Public Speaking / Training',
					'wellness'          => 'General Wellness Practices',
					'other'             => 'Other Practice'
				),
				'dance_movement' => array(
					'line_dancing'   => 'Line Dancing',
					'tango'          => 'Tango',
					'ballroom'       => 'Ballroom',
					'salsa'          => 'Salsa',
					'bachata'        => 'Bachata',
					'swing'          => 'Swing',
					'zumba'          => 'Zumba',
					'ecstatic_dance' => 'Ecstatic Dance',
					'tai_chi'        => 'Tai Chi',
					'qigong'         => 'Qigong'
				),
				'community' => array(
					'singles'           => 'ENLIGHTENING SINGLES',
					'couples'           => 'ENLIGHTENING COUPLES',
					'parents_kids'      => 'Parents & Kids Yoga / Family Nights',
					'wellness_weekends' => 'Wellness Weekends',
					'beach_meetups'     => 'Beach Meetups / Pavilions',
					'hiking'            => 'Hiking / Nature Walks',
					'group_meditation'  => 'Group Meditation Gatherings',
					'volunteer_events'  => 'Volunteer & Charity Events',
					'book_club'         => 'Book Club / Study Groups',
					'other'             => 'Other Community Activity'
				),
				'influencers' => array(
					'photo_shoots'       => 'Photo Shoots',
					'collaborations'     => 'Collaborations',
					'musician'           => 'Musician',
					'songwriter'         => 'Songwriter',
					'dancer'             => 'Dancer',
					'fitness_influencer' => 'Fitness Influencer',
					'product_influencer' => 'Product Influencer',
					'travel_influencer'  => 'Travel Influencer',
					'wellness_blogger'   => 'Health & Wellness Blogger / Vlogger',
					'ai_influencer'      => 'AI / Tech Influencer'
				),
				'education_business' => array(
					'edu_taking_ce'   => 'Taking Continuing Education Classes',
					'edu_teaching_ce' => 'Teaching Continuing Education Classes',
					'mind_body_net'   => 'MIND, BODY & BUSINESS Network',
					'mind_body_bar'   => 'MIND, BODY & BUSINESS Bar (Weekdays 11:45–1:00)',
					'business_skills' => 'Learning Business Skills',
					'social_skills'   => 'Learning Social Media Skills',
					'podcasting'      => 'Podcasting',
					'branding'        => 'Website & Branding Basics',
					'ai_business'     => 'AI for Business Growth',
					'finance'         => 'Finance & Investing Skills'
				),
				'teacher'            => array(
					1 => 'Yes',
					0 => 'No'
				),
				default              => array()
			};
			
			return is_null($key) ? $options : ($options[$key] ?? null);
		}
		
		/**
		 * Outputs ranked input data in a two-column table
		 *
		 * @param array $submitted_ranks
		 * @param array $options
		 * @return string
		 */
		public static function FormatRankedOptionsTable(array $submitted_ranks, array $options): string {
			if(empty($submitted_ranks)) {
				return '<p class="form-control-plaintext"><em>No selections made.</em></p>';
			}
			
			// Sort by rank ascending
			asort($submitted_ranks);
			
			$output = '<table class="table table-sm table-borderless mb-0">';
			$output .= '<thead><tr><th>Selection</th><th>Rank</th></tr></thead><tbody>';
			
			foreach($submitted_ranks as $key => $rank) {
				if(strlen(trim($rank))) {
					$label = $options[$key] ?? ucfirst($key);
					$output .= sprintf('<tr><td>%s</td><td>%s</td></tr>', htmlspecialchars($label), htmlspecialchars($rank));
				}
			}
			
			$output .= '</tbody></table>';
			
			return $output;
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
		public function getCareer(): ?string {
			return $this->career;
		}
		
		/**
		 * @return null|string
		 */
		public function getClassesTaught(): ?string {
			return $this->classes_taught;
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
		public function getCommunityInterests(): ?string {
			return $this->community_interests;
		}
		
		/**
		 * @return null|string
		 */
		public function getCorePractices(): ?string {
			return $this->core_practices;
		}
		
		/**
		 * @return null|string
		 */
		public function getDanceMovement(): ?string {
			return $this->dance_movement;
		}
		
		/**
		 * @return null|string
		 */
		public function getEducationBusiness(): ?string {
			return $this->education_business;
		}
		
		/**
		 * @return null|string
		 */
		public function getEmail(): ?string {
			return $this->email;
		}
		
		/**
		 * @return null|string
		 */
		public function getFilename(): ?string {
			return $this->filename;
		}
		
		/**
		 * @return null|string
		 */
		public function getFirstName(): ?string {
			return $this->first_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getInfluencerGoals(): ?string {
			return $this->influencer_goals;
		}
		
		/**
		 * @return null|string
		 */
		public function getLastName(): ?string {
			return $this->last_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getMusic(): ?string {
			return $this->music;
		}
		
		/**
		 * @return null|string
		 */
		public function getPhone(): ?string {
			return $this->phone;
		}
		
		/**
		 * @return null|bool
		 */
		public function getTeacher(): ?bool {
			return $this->teacher;
		}
		
		/**
		 * @return null|string
		 */
		public function getTeacherRoles(): ?string {
			return $this->teacher_roles;
		}
		
		/**
		 * @return null|string
		 */
		public function getResume(): ?string {
			$file = sprintf("/files/resumes/%s", $this->filename);
			
			return $this->resume ??= is_file(Helpers::PathAbsolute($file)) ? $file : NULL;
		}
		
		/**
		 * @return null|string
		 */
		public function getYoga(): ?string {
			return $this->yoga;
		}
	}