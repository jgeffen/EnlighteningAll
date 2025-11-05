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
	
	use Closure;
	use Database;
	use DateInterval;
	use DatePeriod;
	use DateTime;
	use Items;
	use Items\Enums\Tables;
	use Items\Events\Package;
	use Items\Members\Reservation;
	use PDO;
	use PDOStatement;
	use Render;
	
	class Event implements Interfaces\PageType {
		use Traits\Category, Traits\Item, Traits\Image, Traits\Gallery, Traits\Page, Traits\PDFs;
		
		public const BUTTON_BOOKING   = 'booking';
		public const BUTTON_EDIT      = 'edit';
		public const BUTTON_PASS      = 'pass';
		public const BUTTON_PASS_AUTH = 'pass_auth';
		
		protected Tables\Website $table = Tables\Website::EVENTS;
		
		private ?Items\Page $price_breakout;
		
		private string $class_type;
		
		private string  $event_dates;
		private ?string $event_times;
		private string  $date_start;
		private string  $date_end;
		private ?string $location;
		private ?string $price_text;
		private string  $event_package_ids;
		private bool    $accepting_rsvp;
		private bool    $bookable;
		private bool    $display_rsvps;
		private array   $packages;
		private array   $reservations;
		private ?Member $teacher;
		private ?int    $teacher_id;
        public ?int $sort_order = null;


        /**
		 * @param PDOStatement $statement
		 *
		 * @return null|static
		 */
		public static function Fetch(PDOStatement $statement): ?self {
			return $statement->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @return array
		 */
		public static function getApprovedTeachers(): array {
			return Database::Action("SELECT `id`, `first_name`, `last_name` FROM `members` WHERE `teacher_approved` IS TRUE")->fetchAll(PDO::FETCH_ASSOC);
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
		 * @param string      $option
		 * @param string|NULL $key
		 *
		 * @return null|array|string
		 */
		public static function Options(string $option, string $key = NULL): array|string|null {
			$options = match ($option) {
				'class_types' => array(
					'yoga'                 => 'Yoga',
					'ai'                   => 'AI',
					'web_design'           => 'Web Design',
					'enlightening_singles' => 'Enlightening Singles',
					'enlightening_couples' => 'Enlightening Couples',
					'tropical_influencers' => 'Tropical Influencers',
					'fitness'              => 'Fitness / Personal Training',
					'nutrition'            => 'Nutrition / Health Coaching',
					'business'             => 'Business / Entrepreneurship',
					'coaching'             => 'Life Coaching / Mentorship',
					'arts'                 => 'Creative Arts Instruction',
					'other'                => 'Other Teaching Role'
				),
				'image_types' => array(
					'yoga'                 => '/images/event-types/yoga.png',
					'ai'                   => '/images/event-types/ai.png',
					'web_design'           => '/images/event-types/fencl-web-design.png',
					'enlightening_singles' => '/images/event-types/enlightening-singles.png',
					'enlightening_couples' => '/images/event-types/enlightening-couples.png',
					'tropical_influencers' => '/images/event-types/tropical-influencers.png',
					'fitness'              => '/images/event-types/fitness.png',
					'nutrition'            => '/images/event-types/nutrition.png',
					'business'             => '/images/event-types/business.png',
					'coaching'             => '/images/event-types/coaching.png',
					'arts'                 => '/images/event-types/arts.png',
					'other'                => '/images/event-types/main-logo.png',
				),
				'hours'       => array(
					'01' => '1',
					'02' => '2',
					'03' => '3',
					'04' => '4',
					'05' => '5',
					'06' => '6',
					'07' => '7',
					'08' => '8',
					'09' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12'
				),
				'minutes'     => array(
					'00' => '00',
					'15' => '15',
					'30' => '30',
					'45' => '45'
				),
				'meridians'   => array(
					'am' => 'AM',
					'pm' => 'PM'
				),
				default       => array()
			};
			
			return is_null($key) ? $options : ($options[$key] ?? NULL);
		}
		
		/**
		 * @return void
		 */
		public function setImages(): void {
			$this->images = Render::Images(array(
				'source'          => sprintf("/files/events/%s", $this->getFilename()),
				'square'          => sprintf("/files/events/square/%s", $this->getFilename()),
				'square_thumb'    => sprintf("/files/events/square/thumbs/%s", $this->getFilename()),
				'landscape'       => sprintf("/files/events/landscape/%s", $this->getFilename()),
				'landscape_thumb' => sprintf("/files/events/landscape/thumbs/%s", $this->getFilename()),
				'portrait'        => sprintf("/files/events/poster/%s", $this->getFilename()),
				'portrait_thumb'  => sprintf("/files/events/poster/thumbs/%s", $this->getFilename()),
				'poster'          => sprintf("/files/events/poster/%s", $this->getFilename()),
				'poster_thumb'    => sprintf("/files/events/poster/thumbs/%s", $this->getFilename())
			));
		}
		
		/**
		 * @param string $button "booking", "edit", "pass"
		 *
		 * @return null|string
		 */
		public function getButton(string $button): ?string {
			return match ($button) {
				static::BUTTON_BOOKING   => '/book-now',
				static::BUTTON_EDIT      => sprintf("/user/edit/events/%d", $this->getId()),
				static::BUTTON_PASS      => sprintf("/events/%s/purchase-pass", $this->getPageUrl()),
				static::BUTTON_PASS_AUTH => sprintf("/events/%s/purchase-pass-auth", $this->getPageUrl()),
				default                  => NULL
			};
		}
		
		/**
		 * @return int
		 */
		public function getTotalReservations(): int {
			return Database::Action("SELECT COUNT(DISTINCT(`member_id`)) FROM `member_reservations` WHERE `event_id` = :event_id", array(
				'event_id' => $this->getId()
			))->fetchColumn();
		}
		
		/**
		 * @param null|int $id
		 *
		 * @return null|\Items\Member
		 */
		public function getTeacher(?int $id): ?Member {
			return Member::Init($id)->get ?? NULL;
		}
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `events` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param null|int $id
		 *
		 * @return null|string
		 */
		public function getTeacherName(?int $id): ?string {
			$name = Member::Init($id);
			return $name->getFullName();
		}
		
		/**
		 * @return ?int
		 */
		public function getTeacherId(): ?int {
			return $this->teacher_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getPosterImage(): ?string {
			return $this->getImages()['poster'] ?? NULL;
		}
		
		/**
		 * @return null|string
		 */
		public function getPosterThumb(): ?string {
			return $this->getImages()['poster_thumb'] ?? NULL;
		}
		
		/**
		 * @return string
		 */
		public function getEventDates(): string {
			return $this->event_dates;
		}
		
		/**
		 * @return null|string
		 */
		public function getEventTimes(): ?string {
			return $this->event_times;
		}
		
		/**
		 * @return DatePeriod
		 */
		public function getDates(): DatePeriod {
			return new DatePeriod($this->getStartDate(), new DateInterval('P1D'), $this->getEndDate()->modify('+1 Day'));
		}

		/**
		 * @return DateTime
		 */
		public function getStartDate(): DateTime {
			return date_create($this->date_start);
		}
		
		/**
		 * @return DateTime
		 */
		public function getEndDate(): DateTime {
			return date_create($this->date_end);
		}
		
		/**
		 * @param string $format
		 *
		 * @return string
		 */
		public function getTime(string $format = 'g:ia'): string {
			return sprintf("%s - %s", $this->getStartDate()->format($format), $this->getEndDate()->format($format));
		}
		
		/**
		 * @return string
		 */
		public function getDate(): string {
			return $this->getStartDate()->format('Y-m-d') == $this->getEndDate()->format('Y-m-d')
				? $this->getStartDate()->format('M d')
				: sprintf("%s - %s", $this->getStartDate()->format('M d'), $this->getEndDate()->format('M d Y'));
		}
		
		/**
		 * @return null|string
		 */
		public function getLocation(): ?string {
			return $this->location;
		}
		
		/**
		 * @return null|string
		 */
		public function getPriceText(): ?string {
			return $this->price_text;
		}
		
		/**
		 * @param int $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 *
		 * @return string
		 */
		public function getPackagesIdsJson(int $flags = JSON_ERROR_NONE): string {
			return match ($flags) {
				JSON_HEX_APOS => htmlspecialchars($this->event_package_ids, ENT_QUOTES),
				JSON_HEX_QUOT => htmlspecialchars($this->event_package_ids, ENT_COMPAT),
				default       => $this->event_package_ids,
			};
		}
		
		/**
		 * @return Package[]
		 */
		public function getPackages(): array {
			return $this->packages ??= Database::Action("SELECT * FROM `event_packages` WHERE JSON_CONTAINS(:package_ids, JSON_QUOTE(CAST(`id` AS CHAR))) ORDER BY `name`, `price`", array(
				'package_ids' => json_encode($this->getPackagesIds())
			))->fetchAll(PDO::FETCH_CLASS, Package::class);
		}
		
		/**
		 * @return array
		 */
		public function getPackagesIds(): array {
			return json_decode($this->event_package_ids);
		}
		
		/**
		 * @return Reservation[]
		 */
		public function getReservations(): array {
			!isset($this->reservations) && $this->setReservations();
			return $this->reservations;
		}
		
		/**
		 * @return void
		 */
		private function setReservations(): void {
			$this->reservations = Database::Action("SELECT * FROM `member_reservations` WHERE `event_id` = :event_id GROUP BY `member_id` ORDER BY `timestamp` DESC", array(
				'event_id' => $this->getId()
			))->fetchAll(PDO::FETCH_CLASS, Reservation::class);
		}
		
		/**
		 * @param Closure $callback
		 *
		 * @return void
		 */
		public function sortReservations(Closure $callback): void {
			!isset($this->reservations) && $this->setReservations();
			usort($this->reservations, $callback);
		}
		
		/**
		 * @return string
		 */
		public function getClassType(): string {
			return $this->class_type;
		}
		
		/**
		 * @return bool
		 */
		public function isAcceptingRsvp(): bool {
			return $this->accepting_rsvp;
		}
		
		/**
		 * @return bool
		 */
		public function isBookable(): bool {
			return $this->bookable;
		}
		
		/**
		 * @return bool
		 */
		public function isDisplayRsvps(): bool {
			return $this->display_rsvps;
		}
		
		/**
		 * @return bool
		 */
		public function isPastEvent(): bool {
			return $this->getEndDate() < date_create();
		}
		
		/**
		 * @return bool
		 */
		public function isUpcomingEvent(): bool {
			return $this->getStartDate() > date_create();
		}
		
		/**
		 * @return bool
		 */
		public function isCurrentEvent(): bool {
			return $this->getStartDate() <= date_create() && $this->getEndDate() >= date_create();
		}
		
		/**
		 * @return null|Items\Page
		 */
		public function getPriceBreakout(): ?Items\Page {
			return $this->price_breakout ??= Database::Action("SELECT `heading`, `content` FROM `pages` WHERE `page_url` = 'prices'")->fetchObject(Items\Page::class);
		}
		
		/**
		 * @return string
		 */
		public function getLink(): string {
			return sprintf("/%s.event", $this->getId());
		}
	}