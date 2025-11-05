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
	use Helpers;
	use Items\Enums\Types;
	use Items\Enums\Types\Log;
	use Items\Interfaces;
	use Items\Interfaces\TableEnum;
	use Items\Traits;
	use PDO;
	use PDOStatement;
	
	class UserLog {
		use Traits\Item;
		
		private ?User $user;
		
		private string  $type;
		private ?string $table_name;
		private ?int    $table_id;
		private ?string $table_column;
		private ?string $filename;
		private string  $payload;
		private string  $notes;
		
		/**
		 * @param null|User $user
		 */
		public function __construct(?User $user = NULL) { $this->user = $user; }
		
		/**
		 * @param null|int $id
		 *
		 * @return null|$this
		 */
		public static function Init(?int $id): ?self {
			return Database::Action("SELECT * FROM `user_logs` WHERE `id` = :id", array(
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
		 * @param Log            $type
		 * @param null|TableEnum $table_name
		 * @param null|int       $table_id
		 * @param null|string    $table_column
		 * @param null|string    $filename
		 * @param array          $payload
		 * @param string         $notes
		 *
		 * @return bool
		 */
		public function add(
			Types\Log             $type,
			?Interfaces\TableEnum $table_name = NULL,
			?int                  $table_id = NULL,
			?string               $table_column = NULL,
			?string               $filename = NULL,
			array                 $payload = array(),
			string                $notes = ''
		): bool {
			return Database::Action("INSERT INTO `user_logs` SET `type` = :type, `table_name` = :table_name, `table_id` = :table_id, `table_column` = :table_column, `filename` = :filename, `payload` = :payload, `notes` = :notes, `author` = :author, `user_agent` = :user_agent, `ip_address` = :ip_address", array(
				'type'         => $type->getValue(),
				'table_name'   => $table_name?->getValue(),
				'table_id'     => $table_id,
				'table_column' => $table_column,
				'filename'     => $filename,
				'payload'      => json_encode($payload),
				'notes'        => $notes,
				'author'       => $this->getUser()->getId(),
				'user_agent'   => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
				'ip_address'   => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
			))->rowCount();
		}
		
		/**
		 * @return null|User
		 */
		public function getUser(): ?User {
			return $this->user ?? User::Init($this->getAuthor());
		}
		
		/**
		 * @return null|string
		 */
		public function getType(): ?string {
			return $this->type;
		}
		
		/**
		 * @return null|string
		 */
		public function getTableName(): ?string {
			return $this->table_name;
		}
		
		/**
		 * @return null|int
		 */
		public function getTableId(): ?int {
			return $this->table_id;
		}
		
		/**
		 * @return null|string
		 */
		public function getTableColumn(): ?string {
			return $this->table_column;
		}
		
		/**
		 * @return null|string
		 */
		public function getFilename(): ?string {
			return $this->filename;
		}
		
		/**
		 * @return array
		 */
		public function getPayload(): array {
			return json_decode($this->payload, TRUE);
		}
		
		/**
		 * @param null|int $length
		 *
		 * @return string
		 */
		public function getNotes(?int $length = NULL): string {
			return is_null($length) ? $this->notes : Helpers::Truncate($this->notes, $length);
		}
	}