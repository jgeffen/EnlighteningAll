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
	
	namespace Items\Interfaces;
	
	use DateTime;
	use IPv4Address;
	
	interface Item {
		/**
		 * @return int
		 */
		public function getId(): int;
		
		/**
		 * @return null|int
		 */
		public function getAuthor(): ?int;
		
		/**
		 * @param bool       $format
		 * @param null|array $options 'browser', 'device', 'platform', 'language'
		 * @param string     $separator
		 *
		 * @return null|string
		 */
		public function getUserAgent(bool $format = FALSE, ?array $options = NULL, string $separator = ' | '): ?string;
		
		/**
		 * @return IPv4Address
		 */
		public function getIpAddress(): IPv4Address;
		
		/**
		 * @return DateTime
		 */
		public function getTimestamp(): DateTime;
		
		/**
		 * @return DateTime
		 */
		public function getLastTimestamp(): DateTime;
		
		/**
		 * @param int   $flags The behaviour of these constants is described on the {@link https://www.php.net/manual/en/json.constants.php JSON constants page}.
		 * @param array $itersect
		 * @param int   $depth Set the maximum depth. Must be greater than zero.
		 *
		 * @return string
		 */
		public function toJson(int $flags = JSON_ERROR_NONE, array $itersect = array(), int $depth = 512): string;
		
		/**
		 * @param array $itersect
		 *
		 * @return array
		 */
		public function toArray(array $itersect = array()): array;
		
		/**
		 * Converts special characters of property to HTML entities
		 *
		 * @param string $property
		 * @param int    $flags Available {@link https://www.php.net/manual/en/function.htmlspecialchars.php#refsect1-function.htmlspecialchars-parameters flags} constants
		 *
		 * @return null|string
		 */
		public function getEncoded(string $property, int $flags = ENT_COMPAT): ?string;
		
		/**
		 * @param string $property
		 * @param mixed  $value
		 *
		 * @return void
		 */
		public function __set(string $property, mixed $value): void;
	}