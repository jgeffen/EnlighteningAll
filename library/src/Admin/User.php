<?php
	/*
	Copyright (c) 2020, 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/
	
	namespace Admin;
	
	use Database;
	use DateTime;
	use Exception;
	use Items\Enums\Types;
	use Items\Enums\Types\Log;
	use Items\Interfaces;
	use Items\Interfaces\TableEnum;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class User {
		use Traits\Item;
		
		private UserLog   $log;
		private ?DateTime $last_login;
		
		protected int    $user_type = 9999;
		protected string $first_name;
		protected string $full_name;
		protected string $full_name_last;
		protected string $last_name;
		protected string $email;
		private string   $password;
		
		/**
		 * @throws Exception
		 */
		public function __construct() {
			$this->log = new UserLog($this);
			
			if(isset($this->id)) return;
			
			$user = self::Init($_SESSION['admin']['id'] ?? NULL)?->toArray();
			is_array($user) && array_map(fn(string $property, mixed $value) => $this->$property = $value, array_keys($user), $user);
		}
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `users` WHERE `id` = :id", array(
				'id' => $id
			))->fetchObject(self::class) ?: NULL;
		}
		
		/**
		 * @param null|string $email
		 *
		 * @return null|$this
		 */
		public static function FromEmail(?string $email): ?self {
			return Database::Action("SELECT * FROM `users` WHERE `email` = :email", array(
				'email' => $email
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
		 * @param Log            $type
		 * @param null|TableEnum $table_name
		 * @param null|int       $table_id
		 * @param null|string    $table_column
		 * @param null|string    $filename
		 * @param array          $payload
		 * @param string         $notes
		 *
		 * @return void
		 */
		public function log(
			Types\Log             $type,
			?Interfaces\TableEnum $table_name = NULL,
			?int                  $table_id = NULL,
			?string               $table_column = NULL,
			?string               $filename = NULL,
			array                 $payload = array(),
			string                $notes = ''
		): void {
			$this->log->add(
				type         : $type,
				table_name   : $table_name,
				table_id     : $table_id,
				table_column : $table_column,
				filename     : $filename,
				payload      : $payload,
				notes        : $notes
			);
		}
		
		/**
		 * @return null|UserType
		 */
		public function getUserType(): ?UserType {
			return UserType::Init($this->user_type);
		}
		
		/**
		 * @return string
		 */
		public function getFirstName(): string {
			return $this->first_name;
		}
		
		/**
		 * @return string
		 */
		public function getFullName(): string {
			return $this->full_name ??= sprintf("%s %s", $this->getFirstName(), $this->getLastName());
		}
		
		/**
		 * @return string
		 */
		public function getFullNameLast(): string {
			return $this->full_name_last ??= sprintf("%s, %s", $this->getLastName(), $this->getFirstName());
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
		 * @return null|DateTime
		 */
		public function getLastLogin(): ?DateTime {
			return $this->last_login ??= UserLog::Fetch(Database::Action("SELECT * FROM `user_logs` WHERE `type` = :type AND `author` = :author ORDER BY `timestamp` DESC LIMIT 1", array(
				'type'   => Types\Log::LOGIN->getValue(),
				'author' => $this->getId()
			)))?->getLastTimestamp();
		}
	}