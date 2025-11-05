<?php
	/*
		Copyright (c) 2021 FenclWebDesign.com
		This script may not be copied, reproduced or altered in whole or in part.
		We check the Internet regularly for illegal copies of our scripts.
		Do not edit or copy this script for someone else, because you will be held responsible as well.
		This copyright shall be enforced to the full extent permitted by law.
		Licenses to use this script on a single website may be purchased from FenclWebDesign.com
		@Author: Deryk
		*/
	
	/** @noinspection PhpUnused */
	
	namespace Items\Abstracts;
	
	use Database;
	use DateTime;
	use Helpers;
	use Items\Abstracts;
	use Items\Collections;
	use Items\Enums\Options;
	use Items\Enums\Statuses;
	use Items\Interfaces;
	use Items\Members;
	use Items\Traits;
	use PDO;
	
	abstract class Member implements Interfaces\Member {
		use Traits\Item;
		
		protected Collections\Friends $friends;
		protected ?Members\Avatar     $avatar;
		protected ?Members\CheckIn    $check_in;
		protected ?Members\FreeDrink  $free_drink;
		protected ?DateTime           $last_online;
		protected array               $block_status;
		protected array               $block_initiator;
		protected array               $friend_status;
		protected array               $friend_initiator;
		protected array               $friend_ids;
		protected string              $username;
		protected string              $email;
		protected string              $password;
		protected ?string             $address_line_1;
		protected ?string             $address_line_2;
		protected ?string             $address_city;
		protected ?string             $address_country;
		protected ?string             $address_state;
		protected ?string             $address_zip_code;
		protected string              $bead_colors;
		protected ?string             $bio;
		protected string              $first_name;
		protected string              $first_names;
		protected string              $full_name;
		protected string              $full_name_last;
		protected string              $last_name;
		protected ?string             $necklace_color;
		protected string              $partner_bead_colors;
		protected ?string             $partner_first_name;
		protected ?string             $partner_necklace_color;
		protected ?string             $phone;
		protected bool                $display_rsvps;
		protected bool                $intake_survey;
		protected bool                $approved;
		protected bool                $banned;
		protected bool                $couple;
		protected bool                $verified;
		protected bool                $is_staff;
		protected bool                $teacher;
		protected bool                $teacher_approved;
		protected bool                $is_id_verified;
		protected ?string             $id_verified_admin_approval;
		protected ?string             $id_verified_timestamp;
		protected ?string             $id_verified_ip_address;
		protected ?int                $referred_by;
		
		/**
		 * @return Collections\Friends
		 */
		public function friends(): Collections\Friends {
			return $this->friends ??= new Collections\Friends(Database::Action("SELECT * FROM `member_friends` JOIN `members` ON `id` = IF(`member_1` != :member_id, `member_1`, `member_2`) WHERE :member_id IN (`member_1`, `member_2`)", array(
				'member_id' => $this->getId()
			)));
		}
		
		/**
		 * @param null|Abstracts\Member $contact
		 * @param null|bool             $unread
		 *
		 * @return Collections\Messages
		 */
		public function messages(?Abstracts\Member $contact = NULL, ?bool $unread = NULL): Collections\Messages {
			return new Collections\Messages(Database::Action("SELECT * FROM `member_messages` WHERE :member_id IN (`member_1`, `member_2`) AND (:contact_id IN (`member_1`, `member_2`) OR ISNULL(:contact_id)) AND ((`read` != :unread AND `initiated_by` != :member_id) OR ISNULL(:unread)) ORDER BY `timestamp`", array(
				'member_id'  => $this->getId(),
				'contact_id' => $contact?->getId(),
				'unread'     => $unread
			)), $this);
		}
		
		/**
		 * @param int      $days
		 * @param null|int $author
		 *
		 * @return void
		 */
		public function checkIn(int $days = 1, ?int $author = NULL): void {
			Database::Action("INSERT INTO `member_check_ins` SET `member_id` = :member_id, `expiration_date` = :expiration_date, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address ON DUPLICATE KEY UPDATE `expiration_date` = :expiration_date, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'member_id'       => $this->getId(),
				'expiration_date' => date_create()->setTime(11, 0)->modify(sprintf("+%d Days", $days))->format('Y-m-d H:i:s'),
				'author'          => $author,
				'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			));
		}
		
		/**
		 * @param null|int $author
		 *
		 * @return void
		 */
		public function checkOut(?int $author = NULL): void {
			Database::Action("UPDATE `member_check_ins` SET `expiration_date` = :expiration_date, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address WHERE `member_id` = :member_id", array(
				'member_id'       => $this->getId(),
				'expiration_date' => date_create()->format('Y-m-d H:i:s'),
				'author'          => $author,
				'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			));
		}
		
		/**
		 * @param null|self $member
		 *
		 * @return Statuses\Block
		 */
		public function getBlockStatus(?self $member): Statuses\Block {
			return $this->block_status[$member?->getId()] ??= Database::Action("SELECT COUNT(*) FROM `member_blocks` WHERE `member_1` = :member_1 AND `member_2` = :member_2", array(
				'member_1' => min($this->getId(), $member?->getId()),
				'member_2' => max($this->getId(), $member?->getId())
			))->fetchColumn()
				? Statuses\Block::BLOCKED
				: Statuses\Block::NONE;
		}
		
		/**
		 * @param null|self $member
		 * @param null|self $initiator
		 *
		 * @return bool
		 */
		public function getBlockInitiatedBy(?self $member, ?self $initiator = NULL): bool {
			$initiator ??= $member;
			return $initiator?->getId() == $this->getBlockInitiator($member)?->getid();
		}
		
		/**
		 * @param null|self $member
		 *
		 * @return null|self
		 */
		public function getBlockInitiator(?self $member): ?self {
			return $this->block_initiator[$member?->getId()] ??= Database::Action("SELECT * FROM `members` WHERE `id` = (SELECT `initiated_by` FROM `member_blocks` WHERE `member_1` = :member_1 AND `member_2` = :member_2)", array(
				'member_1' => min($this->getId(), $member?->getId()),
				'member_2' => max($this->getId(), $member?->getId())
			))->fetchObject(get_class($member)) ?: NULL;
		}
		
		/**
		 * @return int[]
		 */
		public function getBlockedIds(): array {
			return Database::Action("SELECT IF(`member_1` != :member_id, `member_1`, `member_2`) AS `member_id` FROM `member_blocks` WHERE :member_id IN (`member_1`, `member_2`) ORDER BY `member_id`", array(
				'member_id' => $this->getId()
			))->fetchAll(PDO::FETCH_COLUMN);
		}
		
		/**
		 * @param null|self $member
		 *
		 * @return Statuses\Friend
		 */
		public function getFriendStatus(?self $member): Statuses\Friend {
			return $this->friend_status[$member?->getId()] ??= Statuses\Friend::lookup(Database::Action("SELECT `status` FROM `member_friend_requests` WHERE `member_1` = :member_1 AND `member_2` = :member_2", array(
				'member_1' => min($this->getId(), $member?->getId()),
				'member_2' => max($this->getId(), $member?->getId())
			))->fetchColumn() ?: Statuses\Friend::NONE->getValue());
		}
		
		/**
		 * @param null|self $member
		 * @param null|self $initiator
		 *
		 * @return bool
		 */
		public function getFriendInitiatedBy(?self $member, ?self $initiator = NULL): bool {
			$initiator ??= $member;
			return $initiator?->getId() == $this->getFriendInitiator($member)?->getid();
		}
		
		/**
		 * @param null|self $member
		 *
		 * @return null|self
		 */
		public function getFriendInitiator(?self $member): ?self {
			return $this->friend_initiator[$member?->getId()] ??= Database::Action("SELECT * FROM `members` WHERE `id` = (SELECT `initiated_by` FROM `member_friend_requests` WHERE `member_1` = :member_1 AND `member_2` = :member_2)", array(
				'member_1' => min($this->getId(), $member?->getId()),
				'member_2' => max($this->getId(), $member?->getId())
			))->fetchObject(get_class($member)) ?: NULL;
		}
		
		/**
		 * @return array
		 */
		public function getFriendsCount(): array {
			return Database::Action("SELECT COUNT(id) FROM `member_friend_requests` WHERE `timestamp` > CURDATE() AND `initiated_by` = :member_id", array(
				'member_id' => $this->getId()
			))->fetchAll(PDO::FETCH_COLUMN);
		}
		
		/**
		 * @return array
		 */
		public function getSubscriptionsID(): array {
			return Database::Action("SELECT id FROM `member_subscriptions` WHERE `date_cancellation` > CURDATE() AND `member_id` = :member_id", array(
				'member_id' => $this->getId()
			))->fetchAll(PDO::FETCH_COLUMN);
		}
		
		/**
		 * @param int $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 *
		 * @return string
		 */
		public function getFriendIdsJson(int $flags = JSON_ERROR_NONE): string {
			return match ($flags) {
				JSON_HEX_APOS => htmlspecialchars(json_encode($this->getFriendIds(), JSON_ERROR_NONE), ENT_QUOTES),
				JSON_HEX_QUOT => htmlspecialchars(json_encode($this->getFriendIds(), JSON_ERROR_NONE), ENT_COMPAT),
				default       => json_encode($this->getFriendIds(), $flags),
			};
		}
		
		/**
		 * @return int[]
		 */
		public function getFriendIds(): array {
			return $this->friend_ids ??= Database::Action("SELECT IF(`member_1` = :member_id, `member_2`, `member_1`) FROM `member_friends` WHERE :member_id IN(`member_1`, `member_2`)", array(
				'member_id' => $this->getId()
			))->fetchAll(PDO::FETCH_COLUMN);
		}
		
		/**
		 * @param null|string $descriptor
		 *
		 * @return string
		 */
		public function getAlt(?string $descriptor = NULL): string {
			return htmlspecialchars($this->getTitle($descriptor), ENT_COMPAT);
		}
		
		/**
		 * @param null|string $descriptor
		 *
		 * @return string
		 */
		public function getTitle(?string $descriptor = NULL): string {
			return $descriptor ? sprintf("%s's %s", $this->getUsername(), $descriptor) : $this->getUsername();
		}
		
		/**
		 * @return string
		 */
		public function getUsername(): string {
			return $this->username;
		}
		
		/**
		 * @return null|Members\Avatar
		 */
		public function getAvatar(): ?Members\Avatar {
			return $this->avatar ??= Members\Avatar::InitFromMember($this);
		}
		
		/**
		 * @return string
		 */
		public function getEmail(): string {
			return $this->email;
		}
		
		/**
		 * @return string
		 */
		public function getPasswordHash(): string {
			return $this->password;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressLine1(): ?string {
			return $this->address_line_1;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressLine2(): ?string {
			return $this->address_line_2;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressCity(): ?string {
			return $this->address_city;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressCountry(): ?string {
			return $this->address_country;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressState(): ?string {
			return $this->address_state;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressZipCode(): ?string {
			return $this->address_zip_code;
		}
		
		/**
		 * @return bool
		 */
		public function isStaff(): bool {
			return $this->is_staff;
		}
		
		/**
		 * @return Options\BeadColors[]
		 */
		public function getBeadColors(): array {
			return array_filter(array_map(fn(string $bead_color) => Options\BeadColors::lookup($bead_color), json_decode($this->bead_colors)));
		}
		
		/**
		 * @param int $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 *
		 * @return string
		 */
		public function getBeadColorsJson(int $flags = JSON_ERROR_NONE): string {
			return match ($flags) {
				JSON_HEX_APOS => htmlspecialchars($this->bead_colors, ENT_QUOTES),
				JSON_HEX_QUOT => htmlspecialchars($this->bead_colors, ENT_COMPAT),
				default       => $this->bead_colors,
			};
		}
		
		/**
		 * @return null|string
		 */
		public function getBio(): ?string {
			return is_null($this->bio) ? $this->bio : trim(strip_tags($this->bio, array('p', 'br', 'strong', 'em', 'u')));
		}
		
		/**
		 * @return string
		 */
		public function getFirstNames(): string {
			!isset($this->first_names) && $this->setFirstNames();
			return $this->first_names;
		}
		
		/**
		 * @return void
		 */
		private function setFirstNames(): void {
			$this->first_names = $this->isCouple()
				? implode(' & ', array_filter(array($this->getFirstName(), $this->getPartnerFirstName())))
				: $this->getFirstName();
		}
		
		/**
		 * @return bool
		 */
		public function isCouple(): bool {
			return $this->couple;
		}
		
		/**
		 * @return string
		 */
		public function getFirstName(): string {
			return $this->first_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getPartnerFirstName(): ?string {
			return $this->partner_first_name;
		}
		
		/**
		 * @return string
		 */
		public function getFullName(): string {
			!isset($this->full_name) && $this->setFullName();
			return $this->full_name;
		}
		
		/**
		 * @return void
		 */
		private function setFullName(): void {
			$this->full_name = sprintf("%s %s", $this->getFirstName(), $this->getLastName());
		}
		
		/**
		 * @return string
		 */
		public function getLastName(): string {
			return $this->last_name;
		}
		
		/**
		 * @return string
		 */
		public function getFullNameLast(): string {
			!isset($this->full_name_last) && $this->setFullNameLast();
			return $this->full_name_last;
		}
		
		/**
		 * @return void
		 */
		private function setFullNameLast(): void {
			$this->full_name_last = sprintf("%s, %s", $this->getLastName(), $this->getFirstName());
		}
		
		/**
		 * @return null|Options\NecklaceColors
		 */
		public function getNecklaceColor(): ?Options\NecklaceColors {
			return Options\NecklaceColors::lookup($this->necklace_color);
		}
		
		/**
		 * @return Options\BeadColors[]
		 */
		public function getPartnerBeadColors(): array {
			return array_filter(array_map(fn(string $bead_color) => Options\BeadColors::lookup($bead_color), json_decode($this->partner_bead_colors)));
		}
		
		/**
		 * @param int $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 *
		 * @return string
		 */
		public function getPartnerBeadColorsJson(int $flags = JSON_ERROR_NONE): string {
			return match ($flags) {
				JSON_HEX_APOS => htmlspecialchars($this->partner_bead_colors, ENT_QUOTES),
				JSON_HEX_QUOT => htmlspecialchars($this->partner_bead_colors, ENT_COMPAT),
				default       => $this->partner_bead_colors,
			};
		}
		
		/**
		 * @return null|Options\NecklaceColors
		 */
		public function getPartnerNecklaceColor(): ?Options\NecklaceColors {
			return Options\NecklaceColors::lookup($this->partner_necklace_color);
		}
		
		/**
		 * @return null|string
		 */
		public function getPhone(): ?string {
			return $this->phone;
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
		public function isIntakeSurvey(): bool {
			return $this->intake_survey;
		}
		
		/**
		 * @return bool
		 */
		public function isApproved(): bool {
			return $this->approved;
		}
		
		/**
		 * @return bool
		 */
		public function isBanned(): bool {
			return $this->banned;
		}
		
		/**
		 * @return bool
		 */
		public function isTeacher(): bool {
			return $this->teacher;
		}
		
		/**
		 * @return bool
		 */
		public function isTeacherApproved(): bool {
			return $this->teacher_approved;
		}
		
		/**
		 * @return bool
		 */
		public function isVerified(): bool {
			return $this->verified;
		}
		
		/**
		 * @return bool
		 */
		public function isIdVerified(): bool {
			return $this->is_id_verified;
		}
		
		/**
		 * @return null|string
		 */
		public function getIdVerifiedAdminApproval(): ?string {
			return $this->id_verified_admin_approval;
		}
		
		/**
		 * @return null|string
		 */
		public function getIdVerifiedTimestamp(): ?string {
			return $this->id_verified_timestamp;
		}
		
		/**
		 * @return null|string
		 */
		public function getIdVerifiedIpAddress(): ?string {
			return $this->id_verified_ip_address;
		}
		
		/**
		 * @param bool $fqdn
		 *
		 * @return string
		 */
		public function getLink(bool $fqdn = FALSE): string {
			$link = sprintf("/members/profile/%s", strtolower($this->getUsername()));
			
			return $fqdn ? Helpers::CurrentWebsite($link) : $link;
		}
		
		/**
		 * @return null|DateTime
		 */
		public function getLastOnline(): ?DateTime {
			return $this->last_online ??= Members\Polling::Fetch(Database::Action("SELECT * FROM `member_polling` WHERE `member_id` = :member_id", array(
				'member_id' => $this->getId()
			)))?->getLastTimestamp();
		}
		
		/**
		 * @return null|Members\CheckIn
		 */
		public function getCheckIn(): ?Members\CheckIn {
			return $this->check_in ??= Members\CheckIn::Fetch(Database::Action("SELECT * FROM `member_check_ins` WHERE `expiration_date` >= NOW() AND `member_id` = :member_id", array(
				'member_id' => $this->getId()
			)));
		}
		
		/**
		 * @return null|Members\FreeDrink
		 */
		public function getFreeDrink(): ?Members\FreeDrink {
			return $this->free_drink ??= Members\FreeDrink::Fetch(Database::Action("SELECT * FROM `member_free_drinks` WHERE `expiration_date` > NOW() AND `member_id` = :member_id", array(
				'member_id' => $this->getId()
			)));
		}
		
		/**
		 * @return null|int
		 */
		public function getReferredBy(): ?int {
			return $this->referred_by;
		}
	}
