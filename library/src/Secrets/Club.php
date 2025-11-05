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
	
	namespace Secrets;
	
	use Database;
	use Items\Abstracts;
	use Items\Traits;
	use Items\Enums\Tables;
	use PDO;
	use PDOStatement;
	
	class Club extends Abstracts\PageType {
		use Traits\Gallery, Traits\Image;
		
		protected Tables\Secrets $table = Tables\Secrets::CLUBS;
		
		private Clubs\State $state;
		
		protected string  $name;
		protected ?string $address_line_1;
		protected ?string $address_line_2;
		protected ?string $address_city;
		protected string  $address_state;
		protected ?string $address_zip;
		protected ?string $phone;
		protected ?string $email;
		protected array   $email_parts;
		protected ?string $website;
		protected ?int    $year_established;
		protected ?string $premise_determination;
		protected string  $hours_of_operation;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `clubs` WHERE `id` = :id", array(
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
		 * @return Clubs\State
		 */
		public function getState(): Clubs\State {
			return $this->state ??= Clubs\State::InitFromState($this->getAddressState());
		}
		
		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
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
		 * @return string
		 */
		public function getAddressState(): string {
			return $this->address_state;
		}
		
		/**
		 * @return null|string
		 */
		public function getAddressZip(): ?string {
			return $this->address_zip;
		}
		
		/**
		 * @return null|string
		 */
		public function getMapLink(): ?string {
			if(is_null($this->getAddressLine1())) return NULL;
			
			return 'https://maps.google.com/?q=' . urlencode(implode(', ', array_filter(array(
					$this->getAddressLine1(),
					$this->getAddressLine2(),
					$this->getAddressCity(),
					$this->getAddressState(),
					$this->getAddressZip()
				))));
		}
		
		/**
		 * @return null|string
		 */
		public function getPhone(): ?string {
			return $this->phone;
		}
		
		/**
		 * @return null|string
		 */
		public function getEmail(): ?string {
			return $this->email;
		}
		
		/**
		 * @param string $part
		 *
		 * @return null|string
		 */
		public function getEmailParts(string $part): ?string {
			if(is_null($this->getEmail())) return NULL;
			
			$this->email_parts ??= explode('@', $this->getEmail());
			
			return match ($part) {
				'user'   => $this->email_parts[0] ?? NULL,
				'domain' => $this->email_parts[1] ?? NULL,
				default  => NULL
			};
		}
		
		/**
		 * @return null|string
		 */
		public function getWebsite(): ?string {
			return $this->website;
		}
		
		/**
		 * @return null|int
		 */
		public function getYearEstablished(): ?int {
			return $this->year_established;
		}
		
		/**
		 * @return null|string
		 */
		public function getPremiseDetermination(): ?string {
			return $this->premise_determination;
		}
		
		/**
		 * @param null|string $property
		 * @param null|int    $flags Available {@link https://www.php.net/manual/en/function.htmlspecialchars.php#refsect1-function.htmlspecialchars-parameters flags} constants
		 *
		 * @return null|array|string
		 */
		public function getHoursOfOperation(?string $property = NULL, ?int $flags = NULL): null|array|string {
			$array = json_decode($this->hours_of_operation, TRUE);
			
			return match (TRUE) {
				is_null($property)       => $array,
				isset($array[$property]) => match (TRUE) {
					!is_null($flags) => htmlspecialchars((string)$array[$property], $flags),
					default          => $array[$property]
				},
				default                  => NULL
			};
		}
	}