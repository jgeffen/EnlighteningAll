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
	
	namespace Admin;
	
	use Database;
	use Debug;
	use Exception;
	use PDO;
	use PDOStatement;
	
	class UserType {
		private int    $user_type;
		private string $title;
		
		/**
		 * @param null|int $user_type
		 *
		 * @return null|$this
		 */
		public static function Init(?int $user_type): ?self {
			return Database::Action("SELECT * FROM `user_types` WHERE `user_type` = :user_type", array(
				'user_type' => $user_type
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
		 * @return int
		 */
		public function getUserType(): int {
			return $this->user_type;
		}
		
		/**
		 * @return string
		 */
		public function getTitle(): string {
			return $this->title;
		}
		
		/**
		 * @param PDOStatement $statement
		 * @param string       $value
		 * @param string       $label
		 *
		 * @return array
		 */
		public static function Options(PDOStatement $statement, string $value = 'user_type', string $label = 'title'): array {
			return array_reduce($statement->fetchAll(PDO::FETCH_CLASS, self::class), function(array $carry, self $item) use ($value, $label) {
				$carry[$item->getEncoded($value)] = $item->getEncoded($label);
				return $carry;
			}, array());
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
	}