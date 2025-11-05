<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	namespace Items\Traits;
	
	use DateTime;
	use Debug;
	use Exception;
	use Helpers;
	use IPv4Address;
	
	trait Item {
		protected int     $id;
		protected ?int    $author;
		protected ?string $user_agent;
		protected ?string $ip_address;
		protected string  $timestamp;
		protected string  $last_timestamp;
		
		/**
		 * @return int
		 */
		public function getId(): int {
			return $this->id;
		}
		
		/**
		 * @return null|int
		 */
		public function getAuthor(): ?int {
			return $this->author;
		}
		
		/**
		 * @param bool       $format
		 * @param null|array $options 'browser', 'device', 'platform', 'language'
		 * @param string     $separator
		 *
		 * @return null|string
		 */
		public function getUserAgent(bool $format = FALSE, ?array $options = NULL, string $separator = ' | '): ?string {
			return $format ? Helpers::FormatUserAgent($this->user_agent, $options, $separator) : $this->user_agent;
		}
		
		/**
		 * @return IPv4Address
		 */
		public function getIpAddress(): IPv4Address {
			return new IPv4Address($this->ip_address);
		}
		
		/**
		 * @return DateTime
		 */
		public function getTimestamp(): DateTime {
			return date_create($this->timestamp);
		}
		
		/**
		 * @return DateTime
		 */
		public function getLastTimestamp(): DateTime {
			return date_create(max($this->timestamp, $this->last_timestamp));
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
		 * @param array $intersect
		 *
		 * @return array
		 */
		public function toArray(array $intersect = array()): array {
			return array_diff_key(empty($intersect) ? get_object_vars($this) : array_intersect_key(get_object_vars($this), array_flip($intersect)), array_flip(array(
				'author',
				'ip_address',
				'user_agent'
			)));
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
		 *
		 * @return mixed
		 */
		public function __get(string $property): mixed {
			return $this->$property;
		}
		
		/**
		 * @param string $property
		 *
		 * @return bool
		 */
		public function __isset(string $property): bool {
			return isset($this->$property);
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
	}