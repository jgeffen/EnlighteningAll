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
	
	namespace Locations;
	
	use Database;
	use DateTime;
	use Debug;
	use Exception;
	use Items\Enums\Tables;
	use PDO;
	use PDOStatement;
	
	class Country {
		private array $states;
		private array $cities;
		
		private int     $id;
		private string  $name;
		private ?string $iso3;
		private ?string $numeric_code;
		private ?string $iso2;
		private ?string $phonecode;
		private ?string $capital;
		private ?string $currency;
		private ?string $currency_name;
		private ?string $currency_symbol;
		private ?string $tld;
		private ?string $native;
		private ?string $region;
		private ?string $subregion;
		private ?string $timezones;
		private ?string $translations;
		private ?float  $latitude;
		private ?float  $longitude;
		private ?string $emoji;
		private ?string $emojiU;
		private ?string $created_at;
		private string  $updated_at;
		private bool    $flag;
		private ?string $wikiDataId;
		
		protected Tables\Location $table = Tables\Location::COUNTRIES;
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `location_countries` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param PDOStatement $statement
		 *
		 * @return null|static
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
		 * @return State[]
		 */
		public function getStates(): array {
			return $this->states ??= State::FetchAll(Database::Action("SELECT * FROM `location_states` WHERE `country_id` = :id", array(
				'id' => $this->getId()
			)));
		}
		
		/**
		 * @return City[]
		 */
		public function getCities(): array {
			return $this->cities ??= City::FetchAll(Database::Action("SELECT * FROM `location_cities` WHERE `country_id` = :id", array(
				'id' => $this->getId()
			)));
		}
		
		/**
		 * @return int
		 */
		public function getId(): int {
			return $this->id;
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
		public function getIso3(): ?string {
			return $this->iso3;
		}
		
		/**
		 * @return null|string
		 */
		public function getNumericCode(): ?string {
			return $this->numeric_code;
		}
		
		/**
		 * @return null|string
		 */
		public function getIso2(): ?string {
			return $this->iso2;
		}
		
		/**
		 * @return null|string
		 */
		public function getPhonecode(): ?string {
			return $this->phonecode;
		}
		
		/**
		 * @return null|string
		 */
		public function getCapital(): ?string {
			return $this->capital;
		}
		
		/**
		 * @return null|string
		 */
		public function getCurrency(): ?string {
			return $this->currency;
		}
		
		/**
		 * @return null|string
		 */
		public function getCurrencyName(): ?string {
			return $this->currency_name;
		}
		
		/**
		 * @return null|string
		 */
		public function getCurrencySymbol(): ?string {
			return $this->currency_symbol;
		}
		
		/**
		 * @return null|string
		 */
		public function getTld(): ?string {
			return $this->tld;
		}
		
		/**
		 * @return null|string
		 */
		public function getNative(): ?string {
			return $this->native;
		}
		
		/**
		 * @return null|string
		 */
		public function getRegion(): ?string {
			return $this->region;
		}
		
		/**
		 * @return null|string
		 */
		public function getSubregion(): ?string {
			return $this->subregion;
		}
		
		/**
		 * @return null|string
		 */
		public function getTimezones(): ?string {
			return $this->timezones;
		}
		
		/**
		 * @return null|string
		 */
		public function getTranslations(): ?string {
			return $this->translations;
		}
		
		/**
		 * @return null|float
		 */
		public function getLatitude(): ?float {
			return $this->latitude;
		}
		
		/**
		 * @return null|float
		 */
		public function getLongitude(): ?float {
			return $this->longitude;
		}
		
		/**
		 * @return null|string
		 */
		public function getEmoji(): ?string {
			return $this->emoji;
		}
		
		/**
		 * @return null|string
		 */
		public function getEmojiU(): ?string {
			return $this->emojiU;
		}
		
		/**
		 * @return null|DateTime
		 */
		public function getCreatedAt(): ?DateTime {
			return !is_null($this->created_at) ? date_create($this->created_at) : NULL;
		}
		
		/**
		 * @return DateTime
		 */
		public function getUpdatedAt(): DateTime {
			return date_create($this->updated_at);
		}
		
		/**
		 * @return bool
		 */
		public function isFlag(): bool {
			return $this->flag;
		}
		
		/**
		 * @return null|string
		 */
		public function getWikiDataId(): ?string {
			return $this->wikiDataId;
		}
		
		/**
		 * @param int   $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 * @param array $itersect
		 * @param int   $depth Set the maximum depth. Must be greater than zero.
		 *
		 * @return string
		 */
		public function toJson(int $flags = JSON_ERROR_NONE, array $itersect = array(), int $depth = 512): string {
			return match ($flags) {
				JSON_HEX_APOS => htmlspecialchars(json_encode($this->toArray($itersect), JSON_ERROR_NONE, $depth), ENT_QUOTES),
				JSON_HEX_QUOT => htmlspecialchars(json_encode($this->toArray($itersect), JSON_ERROR_NONE, $depth), ENT_COMPAT),
				default       => json_encode($this->toArray($itersect), $flags, $depth),
			};
		}
		
		/**
		 * @param array $itersect
		 *
		 * @return array
		 */
		public function toArray(array $itersect = array()): array {
			return empty($intersect) ? get_object_vars($this) : array_intersect_key(get_object_vars($this), array_flip($itersect));
		}
		
		/**
		 * Converts special characters of property to HTML entities
		 *
		 * @param string $property
		 * @param int    $flags Available {@link https://www.php.net/manual/en/function.htmlspecialchars.php#refsect1-function.htmlspecialchars-parameters flags} constants
		 *
		 * @return null|string
		 */
		public function getEncoded(string $property, int $flags = ENT_COMPAT): ?string {
			try {
				if(property_exists($this, $property)) {
					return isset($this->$property) ? htmlspecialchars((string)$this->$property, $flags) : NULL;
				} else throw new Exception(sprintf("Invalid property %s for class %s", $property, get_class($this)));
			} catch(Exception $exception) {
				Debug::Exception($exception);
			}
			
			return NULL;
		}
		
		/**
		 * @param string $property
		 * @param mixed  $value
		 *
		 * @return void
		 */
		public function __set(string $property, mixed $value): void {
			error_log(sprintf("Unhandled property %s for class %s", $property, get_class($this)));
		}
		
		/**
		 * @param PDOStatement $statement
		 * @param string       $value
		 * @param string       $label
		 *
		 * @return array
		 */
		public static function Options(PDOStatement $statement, string $value = 'iso2', string $label = 'name'): array {
			return array_reduce($statement->fetchAll(PDO::FETCH_CLASS, self::class), function(array $carry, self $item) use ($value, $label) {
				$carry[$item->getEncoded($value)] = $item->getEncoded($label);
				return $carry;
			}, array());
		}
	}